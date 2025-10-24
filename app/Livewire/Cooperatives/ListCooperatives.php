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
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\ViewAction;
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

class ListCooperatives extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder{
                $user = Auth::user();
                $cooperative = Cooperative::query();
                //return cooperatives based on user role
                return $cooperative->when($user->hasRole('dco'), fn($q) =>
                    $q->where('district_id', $user->district_id)
                );
            })
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('registration_number')->label('Registration Number')->sortable()->searchable(),
                TextColumn::make('leader_name')->label('Leader Name')->sortable()->searchable(),
                TextColumn::make('leader_contact')->numeric()->label('Leader Contact')->sortable()->searchable(),
                TextColumn::make('district.name')->label('District')->sortable()->searchable(),
            
            ])
            ->headerActions([
                CreateAction::make()
                ->visible(auth()->user()->hasRole('admin'))
                ->url(fn(): string => route('cooperatives.create')),
                //  Action::make('Distribute')
                // ->visible(auth()->user()->hasRole(['dco']))
                // ->url(fn (): string => route('dco.distribute.cooperatives')),
                ImportAction::make()->importer(\App\Filament\Imports\CooperativeImporter::class)
                ->visible(auth()->user()->hasRole('admin'))

            ])
            ->recordActions([
                DeleteAction::make()->visible(auth()->user()->hasRole('admin')),
                // EditAction::make()
                // ->visible(auth()->user()->hasRole('admin'))
                // ->url(fn (Cooperative $record): string => route('cooperatives.edit', $record)),
                
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


