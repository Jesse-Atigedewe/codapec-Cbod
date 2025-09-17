<?php

namespace App\Livewire\Warehouse;

use App\Models\Warehouse;
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

class ListWarehouse extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Warehouse::query()->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('user.name')->label('Codapec Rep')->sortable()->searchable(),
                TextColumn::make('name')->label('Warehouse Name')->sortable()->searchable(),
                TextColumn::make('location')->limit(15)->sortable()->searchable(),
                TextColumn::make('description')->limit(15)->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => route(name: 'warehouse.create'))
                    
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Warehouse $warehouse): string => route('warehouses.edit', $warehouse)),

                DeleteAction::make(), // delete warehouse
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // bulk delete

                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.warehouse.list-warehouse');
    }
}
