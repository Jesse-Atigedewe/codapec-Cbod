<?php

namespace App\Livewire\Cooperatives;

use App\Models\Cooperative;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                CreateAction::make()
                ->visible(auth()->user()->hasRole('admin'))
                ->url(fn(): string => route('cooperatives.create')),
                 Action::make('Distribute')
                ->visible(auth()->user()->hasRole(['dco']))
                ->url(fn (): string => route('dco.distribute.cooperatives')),
            ])
            ->recordActions([
                DeleteAction::make()->visible(auth()->user()->hasRole('admin')),
                EditAction::make()
                ->visible(auth()->user()->hasRole('admin'))
                ->url(fn (Cooperative $record): string => route('cooperatives.edit', $record)),
                ViewAction::make('Details')
                ->visible(auth()->user()->hasRole(['admin','dco']))
                ->url(fn (Cooperative $record): string => route('listcooperativemember', $record)),
            
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


