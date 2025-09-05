<?php

namespace App\Livewire\DcoDistribution;

use App\Models\DcoReceivedChemicals;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListDcoDistributions extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

   public function table(Table $table): Table
{
    return $table
        ->query(fn(): Builder => DcoReceivedChemicals::query()->with(['dispatch', 'user', 'dispatch.chemicalRequest']))
        ->columns([
            TextColumn::make('id')->label('ID')->sortable(),

            TextColumn::make('dispatch.chemicalRequest.chemical.name')
                ->label('Chemical')
                ->sortable()
                ->searchable(),

            TextColumn::make('user.name')
                ->label('DCO')
                ->sortable()
                ->searchable(),

            TextColumn::make('district.name')->label('District')->sortable(),

            TextColumn::make('quantity_received')
                ->label('Received')
                ->getStateUsing(fn ($record) => $record->quantity_received),

            TextColumn::make('quantity_distributed')
                ->label('Distributed')
                ->getStateUsing(fn ($record) => $record->quantity_distributed)  ,

            TextColumn::make('remaining')
                ->label('Remaining')
                ->getStateUsing(fn ($record) => $record->quantity_received - $record->quantity_distributed),

            TextColumn::make('received_at')
                ->label('Received At')
                ->dateTime('d M Y H:i'),
        ])
        ->filters([
            // You can add filters by district, date, or DCO
        ])
        ->headerActions([
            // Example: add new received chemical
            // CreateAction::make(),
        ])
        ->recordActions([
            // EditAction::make(),
            // DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                // DeleteBulkAction::make(),
            ]),
        ]);
}

    public function render(): View
    {
        return view('livewire.dco-distribution.list-dco-distributions');
    }
}
