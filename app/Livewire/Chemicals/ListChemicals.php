<?php

namespace App\Livewire\Chemicals;

use App\Models\Chemical;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
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

class ListChemicals extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

  public function table(Table $table): Table
{
    return $table
        ->query(fn (): Builder => Chemical::query())
        ->columns([
            TextColumn::make('name')
                ->label('Input Name')
                ->sortable()
                ->searchable(),

            TextColumn::make('type.name')
                ->label('Input Type')
                ->sortable()
                ->searchable(),

            TextColumn::make('state')
                ->label('Input State')
                ->sortable()
                ->searchable(),

            TextColumn::make('unit')
                ->label('Unit')
                ->sortable()
                ->searchable(),
        ])
        ->headerActions([
            CreateAction::make()
            ->url(fn(): string => route('chemicals.create'))
            ->label('Add New Input'),
        ])
        ->recordActions([
            DeleteAction::make(),
            EditAction::make()->url(fn (Chemical $record): string => route('chemicals.edit', $record)),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}


    public function render(): View
    {
        return view('livewire.chemicals.list-chemicals');
    }
}


