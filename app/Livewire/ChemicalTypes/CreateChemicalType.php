<?php

namespace App\Livewire\ChemicalTypes;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\ChemicalType;

class CreateChemicalType extends Component implements HasActions, HasSchemas
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
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
        ])->statePath('data')->model(ChemicalType::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = ChemicalType::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Input type created successfully');
        $this->redirectRoute('chemical_types.index');
    }

    public function render(): View
    {
        return view('livewire.chemical-types.create-chemical-type');
    }
}
