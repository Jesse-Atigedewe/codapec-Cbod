<?php

namespace App\Livewire\Cooperatives;

use App\Models\Cooperative;
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

class ListCooperatives extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Cooperative::query())
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('leader_name')->label('Leader Name')->sortable()->searchable(),
                TextColumn::make('leader_contact')->label('Leader Contact')->sortable()->searchable(),
                TextColumn::make('number_of_members')->numeric()->label('Members Size')->sortable()->searchable(),
            
            ])
            ->headerActions([
                CreateAction::make()->url(fn(): string => route('cooperatives.create')),
            ])
            ->recordActions([
                DeleteAction::make(),
                EditAction::make()->url(fn (Cooperative $record): string => route('cooperatives.edit', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.cooperatives.list-cooperatives');
    }
}


