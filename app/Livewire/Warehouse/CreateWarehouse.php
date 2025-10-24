<?php

namespace App\Livewire\Warehouse;

use App\Models\District;
use App\Models\Region;
use App\Models\User;
use App\Models\Warehouse;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateWarehouse extends Component implements HasActions, HasSchemas
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
            ->schema([
                Select::make('user_id')
                    ->label('Representative')
                    ->options(User::where('role', 'codapecrep')->pluck('name', 'id'))
                    ->required(),

                  Select::make('region_id')
                    ->label('Region')
                    ->options(Region::query()->pluck('name', 'id'))
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
                    ->searchable(),


                TextInput::make('name')
                    ->label('Warehouse Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location_name')
                    ->label('Location')
                    ->placeholder('Enter location')
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter description')
                    ->rows(4)
                    ->nullable(),
            ])
            ->statePath('data')
            ->model(Warehouse::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Warehouse::create($data);

        $this->form->model($record)->saveRelationships();

        // reset form after creation
        $this->form->fill();
        Notification::make()->success()->title('Warehouse created successfully!')->send();
        redirect()->route('warehouses.list');
    }

    public function render(): View
    {
        return view('livewire.warehouse.create-warehouse');
    }
}
