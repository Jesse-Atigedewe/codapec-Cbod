<?php

namespace App\Livewire\Farmers;

use App\Filament\Exports\FarmerExporter;
use App\Filament\Imports\FarmerImporter;
use App\Models\Farmer;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListFarmers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {                      
        return $table
            ->query(function(): Builder { 
                
                $user = Auth::user();
                $farmers = Farmer::query();
               
                return $farmers->when($user->hasRole('dco'), fn($q) =>
                    $q->where('district_id', $user->district_id)
                );
            })
            ->columns([
                TextColumn::make('farmer_name')->sortable()->searchable(),
                TextColumn::make('id_card_number')->sortable()->searchable(),
                TextColumn::make('farm_location')->sortable()->searchable(),
                TextColumn::make('farm_code')->sortable()->searchable(),
                TextColumn::make('contact_number')->label('contact number')->sortable()->searchable(),
                TextColumn::make('hectares')->label('Hectares')->sortable()->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(auth()->user()->hasRole('admin'))
                    ->url(fn(): string => route('farmers.create')),
                Action::make('Distribute')
                    ->visible(auth()->user()->hasRole(['dco']))
                    ->url(fn(): string => route('dco.distribute.farmers')),

                ImportAction::make()
                    ->importer(FarmerImporter::class)
                ->visible(auth()->user()->hasRole('admin'))
                    ,

                // ExportAction::make()
                //     ->exporter(FarmerExporter::class)
                //     ->formats([
                //         ExportFormat::Xlsx,
                //     ]),


            ])
            ->recordActions([
                // DeleteAction::make()->visible(auth()->user()->hasRole('admin')),
                // EditAction::make()
                // ->visible(auth()->user()->hasRole('admin'))
                // ->url(fn (Farmer $record): string => route('farmers.edit', $record)),
                ViewAction::make('distributions')
                    ->visible(auth()->user()->hasRole(['admin', 'dco']))
                    ->url(fn(Farmer $record): string => route('listfarmermember', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ExportBulkAction::make()
                        ->exporter(FarmerExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                            ExportFormat::Csv,
                        ]),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.farmers.list-farmers');
    }
}
