<?php

namespace App\Livewire\Requests;

use App\Models\HaulageCompany;
use App\Models\Request;
use App\Models\Warehouse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Livewire\Component;

class ListRequests extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $user = Auth::user();

                if ($user->role === 'admin') {
                    // Admins see everything
                    return Request::query()
                        ->where('status', 'approved')
                        ->where('regional_manager_approved', true)
                        ->orderByDesc('created_at');
                }

                if ($user->role === 'dco') {
                    // DCOs only see requests linked to their district (and region for extra safety)
                    return Request::query()
                        ->whereHas('user', function (Builder $query) use ($user) {
                            $query->where('district_id', $user->district_id)
                                ->where('region_id', $user->region_id);
                        })->orderByDesc('created_at');
                }

                if ($user->role === 'regional_manager') {
                    // Regional Manager: filter to their region
                    return Request::query()
                        ->whereHas('user', function (Builder $query) use ($user) {
                            $query->where('region_id', $user->region_id);
                        })->orderByDesc('created_at');
                }
                // Default fallback (no access or limited)
                return Request::query()->whereRaw('1 = 0'); // returns empty

            })
            ->columns([

                TextColumn::make('user.name')->label('Requested By'),
                TextColumn::make('cooperative.name')->label('Cooperative'),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable(),
                ToggleColumn::make('regional_manager_approved')
                    ->visible(fn() => Auth::user()->role === 'regional_manager')
                    ->disabled( function ( $record ) {
                        // Disable if status is rejected
                        return $record->status === 'rejected';
                    } )
                    ->label('RM Approved')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        // Load relationships for use in allocation/deallocation
                        $record->load('farmers', 'chemical');

                        // Update approval + status
                        $record->regional_manager_approved = $state;
                        $record->status = $state ? 'approved' : 'pending';
                        $record->save();

                        if ($state) {
                            // ✅ APPROVED: allocate chemicals only if not already allocated
                            $record->allocateChemicalToFarmers();
                        } else {
                            // ❌ REVOKED: delete allocations if they exist
                            $record->removeChemicalAllocations();
                        }

                        Notification::make()
                            ->title('Approval Updated')
                            ->body(
                                $state
                                    ? 'Regional Manager approval granted and allocations calculated.'
                                    : 'Regional Manager approval revoked and allocations removed.'
                            )
                            ->success()
                            ->send();
                    }),

                ToggleColumn::make('admin_approved')
                    ->label('Admin Approved')
                    ->sortable()
                    ->visible(fn() => Auth::user()?->role === 'admin') // ✅ null-safe check
                    ->afterStateUpdated(function ($record, $state) {
                        // ✅ Optional safeguard: Only allow approval if RM has approved first
                        if ($state && ! $record->regional_manager_approved) {
                            Notification::make()
                                ->title('Approval Blocked')
                                ->body('Cannot approve until Regional Manager has approved this request.')
                                ->danger()
                                ->send();

                            // revert the toggle visually
                            $record->admin_approved = false;
                            $record->save();

                            return;
                        }

                        $record->admin_approved = $state;
                        $record->save();

                        Notification::make()
                            ->title('Approval Updated')
                            ->body('Admin approval has been ' . ($state ? 'granted' : 'revoked') . '.')
                            ->success()
                            ->send();
                    }),


            ])
            ->filters([
                //filter by status
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'pending',
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                    ])
                    ->label('Status')
                    // ->default('pending')
            ])
            ->headerActions([
                // Add any header actions here
                Action::make('Create Request')
                    ->button()
                    ->color('primary')
                    ->url(route('requests.create'))
                    ->visible(fn() => Auth::user()->role === 'dco'),
            ])
            ->recordActions([

                //delete if status is pending
                Action::make('Delete')
                    ->visible(
                        fn(Request $record) =>
                        $record->status === 'pending' &&
                            (Auth::user()->role === 'dco' || Auth::user()->id === $record->user_id)
                    )

                    ->action(fn(Request $record) => $record->delete())
                    ->color('danger')
                    ->requiresConfirmation(),

                //rm reject action for pending requests
                Action::make('rm=reject')
                    ->label('reject')
                    ->visible(
                        fn(Request $record) =>
                        Auth::user()->role === 'regional_manager' && $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Rejection')
                    ->modalDescription('Are you sure you want to reject this request? This action cannot be undone.')   
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->action(fn(Request $record) => $record->rejectedByRegionalManager()),
                    //admin apporve
                    Action::make('admin_approve')
    ->label('Evacuate')
    ->icon('heroicon-o-check-circle')
    ->visible(fn(Request $record) =>
                    
        Auth::user()->role === 'admin' && $record->status === 'approved' && $record->admin_approved == true
    )
    ->schema([
        Select::make('warehouse_id')
            ->label('Select Warehouse')
            ->options(Warehouse::pluck('name', 'id'))
            ->required()
            ->searchable(),

        Select::make('haulage_company_id')
            ->label('Select Haulage Company')
            ->options(HaulageCompany::where('status', 'active')->pluck('name', 'id'))
            ->required()
            ->searchable(),
    ])
    ->requiresConfirmation()
    ->action(function (Request $record, array $data) {
        // Update approval

       
        $record->admin_approved = true;
        $record->status = 'approved';
        $record->save();

        // Create ChemicalRequest
        $record->createChemicalRequest(
            $data['warehouse_id'],
            $data['haulage_company_id']
        );
        

        Notification::make()
            ->title('Request Approved')
            ->body('The request has been approved and a Chemical Request has been created.')
            ->success()
            ->send();
    }),
                // admin reject
                Action::make('admin-reject')
                    ->label('reject')
                    ->visible(
                        fn(Request $record) =>
                        Auth::user()->role === 'admin' && $record->status === 'pending'
                    )
                    ->action(fn(Request $record) => $record->rejectedByAdmin()),

                //view action to see details
                Action::make('view')
                    ->label('View')
                    ->visible(fn(Request $record) => $record->status !== 'rejected')
                    ->url(fn(Request $record) => route('requests.view', $record->id)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
 

    public function render(): View
    {
        return view('livewire.requests.list-requests');
    }
}
