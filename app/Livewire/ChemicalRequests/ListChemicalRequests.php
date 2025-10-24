<?php

namespace App\Livewire\ChemicalRequests;

use App\Models\ChemicalRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use App\Models\Warehouse;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class ListChemicalRequests extends Component implements HasActions, HasSchemas, HasTable
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
                    return ChemicalRequest::query()->orderByDesc('created_at');
                }

                if ($user->role === 'codapecrep') {
                    // CODAPEC reps only see requests linked to their warehouse
                    return ChemicalRequest::query()
                        ->where('warehouse_rep_id', $user->id)->orderByDesc('created_at');
                }
                // Default fallback (no access or limited)
                return ChemicalRequest::query()->whereRaw('1 = 0'); // returns empty
            })
            ->columns([
                TextColumn::make('chemical.name')->label('label'),
                TextColumn::make('warehouse.name')->label('Warehouse'),
                TextColumn::make('quantity'),
                TextColumn::make('status')->badge(),
            ])
            ->headerActions([
                // CreateAction::make()
                //     ->label('Evacuate Item')
                //     ->visible(fn() => Auth::user()->role === 'admin')
                //     ->url(fn(): string => route('chemical_requests.create')),
            ])
            ->recordActions([
                DeleteAction::make()->visible(fn() => Auth::user()->role === 'admin'),
                // EditAction::make()->visible(fn() => Auth::user()->role === 'admin'),


                Action::make('Verify')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->label('Verify')
                    ->visible(
                        fn(ChemicalRequest $record) =>
                        Auth::user()->role === 'codapecrep' && $record->status === 'pending'
                    )
                    ->action(function (ChemicalRequest $record) {
                        $userId = Auth::id();

                        // Find all warehouses managed by this user
                        $warehouses = Warehouse::where('user_id', $userId)->pluck('id');

                        if ($warehouses->isEmpty()) {
                            Notification::make()
                                ->danger()
                                ->title('No warehouse found')
                                ->body('You are not managing any warehouse.')
                                ->send();
                            return;
                        }

                        // Search for stock lots of this chemical across ALL warehouses managed by this user
                        $stocks = WarehouseStock::whereIn('warehouse_id', $warehouses)
                            ->where('chemical_id', $record->chemical_id)
                            ->orderBy('created_at')
                            ->get();
                        

                        if ($stocks->isEmpty()) {
                            Notification::make()
                                ->danger()
                                ->title('Stock not found')
                                ->body('No stock available in your warehouses for this chemical.')
                                ->send();
                            return;
                        }

                        $totalAvailable = $stocks->sum('quantity_available');

                        if ($totalAvailable < $record->quantity) {
                            Notification::make()
                                ->danger()
                                ->title('Insufficient Stock')
                                ->body('Not enough stock across your warehouses to approve this request.')
                                ->send();
                            return;
                        }

                        // Deduct requested quantity across warehouses (oldest stock first)
                        try {
                            DB::transaction(function () use ($stocks, $record) {
                                $quantityToDeduct = $record->quantity;
                                if( $quantityToDeduct <= 0) {
                                    throw new \Exception('Requested quantity must be greater than zero');
                                }
                                // Deduct requested quantity across warehouses (oldest stock first)
                                foreach ($stocks as $lot) {
                                    if ($quantityToDeduct <= 0) {
                                        break;
                                    }

                                    $available = (float) $lot->quantity_available;

                                    if ($available <= 0) {
                                        continue;
                                    }

                                    $deducted = min($quantityToDeduct, $available);
                                    $fullydeducted = $available - $deducted;
                                    $lot->update(['quantity_available' => $fullydeducted]);
                                    $record->update([
                                        'status'       => 'approved',
                                    ]);
                                    Notification::make()
                                        ->success()
                                        ->title('Request Approved')
                                        ->body('The request has been fully approved and stock updated.')
                                        ->send();
                                }

                              
                            });
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn() => Auth::user()->role === 'admin'),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.chemical-requests.list-chemical-requests');
    }
}
