<?php

namespace App\Livewire\Distribution;

use App\Models\CooperativeDistributionRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ViewCooperativeMember extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => CooperativeDistributionRecord::query()
            ->with(['farmer','dispatch.chemical', 'distributor'])->orderByDesc('created_at')
            )
            ->columns([
            TextColumn::make('dispatch.chemical.name')
                ->label('Input')
                ->sortable()
                ->searchable(),

            TextColumn::make('farmer.name')
                ->label('Farmer')
                ->sortable()
                ->searchable(),
            TextColumn::make('quantity')
                ->label('Quantity Received')
                ->numeric()
                ->sortable(),
            TextColumn::make('distributor.name')
                ->label('Distributed By')
                ->sortable(),
    
            TextColumn::make('notes')
                ->label('Notes')
                ->wrap(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.distribution.view-cooperative-member');
    }
}
