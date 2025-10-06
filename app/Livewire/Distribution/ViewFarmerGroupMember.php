<?php

namespace App\Livewire\Distribution;

use App\Models\FarmerGroupDistributionRecord;
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

class ViewFarmerGroupMember extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
{
    return $table
        ->query(fn (): Builder => FarmerGroupDistributionRecord::query()
            ->with(['farmerGroup','dispatch.chemical', 'distributor'])->orderByDesc('created_at')
        )
        ->columns([
             TextColumn::make('dispatch.chemical.name')
                ->label('Input')
                ->sortable()
                ->searchable(),
                
            TextColumn::make('farmerGroup.name')
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

            TextColumn::make('distributed_at')
                ->label('Date')
                ->dateTime('d M Y H:i')
                ->sortable(),

            TextColumn::make('notes')
                ->label('Notes')
                ->wrap(),
        ])
        ->recordActions([])  // no row actions
        ->headerActions([])  // no create actions
        ->filters([])
        ->bulkActions([]);   // no bulk actions
}


    public function render(): View
    {
        return view('livewire.distribution.view-farmer-group-member');
    }
}
