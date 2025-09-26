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
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                TextColumn::make('chemical.name')->label('Chemical'),
                TextColumn::make('warehouse.name')->label('Warehouse'),
                TextColumn::make('quantity'),
                TextColumn::make('status')->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn() => Auth::user()->role === 'admin')
                    ->url(fn(): string => route('chemical_requests.create')),
            ])
            ->recordActions([
                DeleteAction::make()->visible(fn() => Auth::user()->role === 'admin'),
                EditAction::make()->visible(fn() => Auth::user()->role === 'admin'),


Action::make('Toggle Approval')
    ->color(fn(ChemicalRequest $record) => $record->status === 'approved' ? 'danger' : 'success')
    ->icon(fn(ChemicalRequest $record) => $record->status === 'approved' ? 'heroicon-o-x-circle' : 'heroicon-o-check')
    ->requiresConfirmation()
    ->visible(fn(ChemicalRequest $record) => Auth::user()->role === 'codapecrep' && $record->status === 'pending')
    ->label(fn(ChemicalRequest $record) => $record->status === 'approved' ? 'Set Pending' : 'Confirm')
    ->action(function (ChemicalRequest $record) {
        if ($record->status === 'pending') {
            // Find warehouse stock for the chemical belonging to the rep
            $stock = WarehouseStock::with('warehouse')
    ->where('chemical_id', $record->chemical_id)
    ->where('warehouse_id', $record->warehouse_id)
    ->whereHas('warehouse', function ($q) {
        $q->whereColumn('warehouses.user_id', 'warehouse_stocks.user_id');
    })
    ->first();



            if (! $stock) {
                Notification::make()
                    ->danger()
                    ->title('Stock not found')
                    ->body('No stock available in your warehouse for this chemical.')
                    ->send();
                return;
            }

            if ($stock->quantity_available < $record->quantity) {
                Notification::make()
                    ->danger()
                    ->title('Insufficient Stock')
                    ->body('You do not have enough stock to approve this request.')
                    ->send();
                return;
            }

            // Deduct stock
            $stock->decrement('quantity_available', $record->quantity);

            // Approve request
            $record->status = 'approved';
            $record->save();

            Notification::make()
                ->success()
                ->title('Request Approved')
                ->body('The request has been approved and stock updated.')
                ->send();
        } else {
            // Toggle back to pending (no stock rollback here, unless you want to add it)
            $record->status = 'pending';
            $record->save();

            Notification::make()
                ->success()
                ->title('Request Set Back to Pending')
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
