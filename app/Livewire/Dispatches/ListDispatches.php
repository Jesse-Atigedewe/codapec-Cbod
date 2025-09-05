<?php

namespace App\Livewire\Dispatches;

use App\Models\DcoReceivedChemicals;
use App\Models\Dispatch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListDispatches extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Dispatch::query()->with(['chemicalRequest', 'user', 'region', 'district']))
            ->columns([
                TextColumn::make('chemicalRequest.quantity')->label('Qty'),
                TextColumn::make('chemicalRequest.user.name')
                    ->label('Requester')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('driver_name')->label('Driver Name'),
                TextColumn::make('driver_phone')->label('Driver Phone'),
                TextColumn::make('driver_license')->label('Driver License'),
                TextColumn::make('vehicle_number')->label('Vehicle No.'),
                TextColumn::make('status')->badge(),
                IconColumn::make('dco_approved')->boolean()->label('DCO'),
                IconColumn::make('auditor_approved')->boolean()->label('Aud'),
                IconColumn::make('regional_manager_approved')->boolean()->label('RM'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn() => Auth::user()->role === 'codapecrep')
                    ->url(fn(): string => route('dispatches.create')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn() => Auth::user()->role === 'codapecrep')
                    ->url(fn(Dispatch $record): string => route('dispatches.edit', $record)),
                DeleteAction::make()->visible(fn() => Auth::user()->role === 'codapecrep'),
                // DCO Approval
                Action::make('DCO Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn() => Auth::user()->role === 'dco') // only DCOs
                    ->label(fn(Dispatch $record) => $record->dco_approved ? 'Undo DCO' : 'Approve DCO')
                    ->action(function (Dispatch $record) {
                        $record->status = 'delivered';
                        $record->dco_approved = !$record->dco_approved;
                        $record->dco_approved_by = $record->dco_approved ? Auth::id() : null;
                        $record->dco_approved_at = $record->dco_approved ? Carbon::now() : null;
                        $record->save();
                    }),

                // Auditor Approval
                Action::make('Auditor Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn() => Auth::user()->role === 'auditor')
                    ->label(fn(Dispatch $record) => $record->auditor_approved ? 'Undo Auditor' : 'Approve Auditor')
                    ->action(function (Dispatch $record) {
                        $record->auditor_approved = !$record->auditor_approved;
                        $record->auditor_approved_by = $record->auditor_approved ? Auth::id() : null;
                        $record->auditor_approved_at = $record->auditor_approved ? Carbon::now() : null;
                        $record->save();
                    }),

                // Regional Manager Approval
                Action::make('RM Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn() => Auth::user()->role === 'regional_manager')
                    ->label(fn(Dispatch $record) => $record->regional_manager_approved ? 'Undo RM' : 'Approve RM')
                    ->action(function (Dispatch $record) {
                        $record->regional_manager_approved = !$record->regional_manager_approved;
                        $record->regional_manager_approved_by = $record->regional_manager_approved ? Auth::id() : null;
                        $record->regional_manager_approved_at = $record->regional_manager_approved ? Carbon::now() : null;
                        $record->save();
                        // quantity that got dispatched now being saved to dcoreceivedchemical. 
                       $quantity = $record->chemicalRequest->quantity;
                       DcoReceivedChemicals::create([
                            'dispatch_id' => $record->id,
                            'user_id' => Auth::id(), //regional manager id
                            'district_id' => $record->district_id,
                            'quantity_received' => $quantity,
                            'quantity_distributed' => 0,
                            'received_at' => now()
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.dispatches.list-dispatches');
    }
}
