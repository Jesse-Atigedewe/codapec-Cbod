<?php

namespace App\Livewire\HaulageCompanies;

use App\Models\HaulageCompany;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListHaulageCompanies extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => HaulageCompany::query())
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('phone')->sortable()->searchable(),
                TextColumn::make('status')->badge()->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions(actions: [
                CreateAction::make()->url(fn(): string => route('haulage_companies.create')),
            ])
            ->recordActions([
                DeleteAction::make(),
                EditAction::make()->url(fn(HaulageCompany $record): string => route('haulage_companies.edit', $record)),
                Action::make('view')
                    ->label('View')
                    ->Schema([
                        Section::make('Company Data')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('phone'),
                                TextEntry::make('contact_person'),
                                TextEntry::make('status')->badge(),
                            ])
                            ->columns(2),
                    ])
                    ->modalHeading('Company Details')
                    ->modalSubmitAction(false) // removes footer save button

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.haulage-companies.list-haulage-companies');
    }
}
