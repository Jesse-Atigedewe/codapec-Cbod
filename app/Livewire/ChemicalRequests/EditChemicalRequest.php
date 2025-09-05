<?php

namespace App\Livewire\ChemicalRequests;

use App\Models\Chemical;
use App\Models\ChemicalRequest;
use App\Models\District;
use App\Models\HaulageCompany;
use App\Models\Region;
use App\Models\Warehouse;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditChemicalRequest extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ChemicalRequest $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                  Select::make('region_id')
                    ->label('Region')
                    ->options(Region::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('district_id', null)), // reset district on region change

                // District select, filtered by region
                Select::make('district_id')
                    ->label('District')
                    ->options(function (callable $get) {
                        $regionId = $get('region_id');
                        return $regionId ? District::where('region_id', $regionId)->pluck('name', 'id') : [];
                    })
                    ->required()
                    ->searchable(),
                
                Select::make('chemical_id')->options(Chemical::query()->pluck('name','id'))->searchable(),
                Select::make('warehouse_id')->options(Warehouse::query()->pluck('name','id'))->searchable(),
                Select::make('haulage_company_id')->options(HaulageCompany::query()->pluck('name','id'))->searchable(),
                TextInput::make('quantity')->numeric(),
                Select::make('status')->options(array_combine(ChemicalRequest::statuses(), ChemicalRequest::statuses())),
            ])
            ->statePath('data')
            ->model($this->record);
    }

   public function save(): void
{
    $data = $this->form->getState();

    // Resolve the selected warehouse
    $warehouse = Warehouse::find($data['warehouse_id']);

    if (!$warehouse) {
        Notification::make()
            ->danger()
            ->title('Invalid warehouse selected')
            ->send();
        return;
    }

    // Update record, keeping warehouse_rep_id in sync
    $this->record->update($data + [
        'warehouse_rep_id' => $warehouse->user_id,
    ]);

    Notification::make()
        ->success()
        ->title('Chemical request updated successfully')
        ->send();

    $this->redirectRoute('chemical_requests.index');
}


    public function render(): View
    {
        return view('livewire.chemical-requests.edit-chemical-request');
    }
}


