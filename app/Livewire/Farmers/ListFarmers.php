<?php

namespace App\Livewire\Farmers;

use App\Models\Farmer;
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

class ListFarmers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Farmer::query())
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('contact_number')->label('contact number')->sortable()->searchable(),
                TextColumn::make('farm_size')->label('Farm Size')->numeric()->sortable()->searchable(),
            ])
            ->headerActions([
                CreateAction::make()->url(fn(): string => route('farmers.create')),
            ])
            ->recordActions([
                DeleteAction::make(),
                EditAction::make()->url(fn (Farmer $record): string => route('farmers.edit', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.farmers.list-farmers');
    }
}


