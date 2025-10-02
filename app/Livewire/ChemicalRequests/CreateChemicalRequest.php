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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateChemicalRequest extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn() => Auth::id())
                    ->dehydrated(),
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
                Select::make('chemical_id')
                    ->label('Select Input')
                    ->options(Chemical::query()->pluck('name', 'id'))->searchable(),
                Select::make('warehouse_id')
                    ->label('Select Warehouse')
                    ->options(Warehouse::query()
                        ->pluck('name', 'id'))->searchable(),
                Select::make('haulage_company_id')
                    ->label('Select Haulage Company')
                    ->options(HaulageCompany::query()->where('status', 'active')->pluck('name', 'id'))->searchable(),
                TextInput::make('quantity')->numeric(),
            ])
            ->statePath('data')
            ->model(ChemicalRequest::class);
    }


   public function create(): void
{
    $data = $this->form->getState();

    // Resolve the warehouse
    $warehouse = Warehouse::find($data['warehouse_id']);

    if (!$warehouse) {
        Notification::make()
            ->danger()
            ->title('Invalid Warehouse Selected')
            ->send();
        return;
    }

    // Create the request, also saving warehouse rep id
    $record = ChemicalRequest::create($data + [
        'status' => 'pending',
        'warehouse_rep_id' => $warehouse->user_id, // <-- new field
    ]);

    $this->form->model($record)->saveRelationships();

    Notification::make()
        ->success()
        ->title('Chemical request created successfully')
        ->send();

    $this->redirect(route('chemical_requests.index'));
}


    public function render(): View
    {
        return view('livewire.chemical-requests.create-chemical-request');
    }
}
