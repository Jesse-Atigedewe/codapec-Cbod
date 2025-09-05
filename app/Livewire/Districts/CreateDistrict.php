<?php

namespace App\Livewire\Districts;

use App\Models\District;
use App\Models\Region;
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

class CreateDistrict extends Component implements HasActions, HasSchemas
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
                Select::make('region_id')
                ->label('Region')
                ->options(Region::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('district_id', null)), // reset district on region change

                TextInput::make('name')->required()->maxLength(255),
            ])
            ->statePath('data')
            ->model(District::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = District::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('District created successfully');
        $this->redirectRoute('districts.index');
    }

    public function render(): View
    {
        return view('livewire.districts.create-district');
    }
}


