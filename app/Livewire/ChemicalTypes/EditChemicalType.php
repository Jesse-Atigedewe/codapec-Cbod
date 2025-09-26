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

class EditChemicalType extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ChemicalType $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
        ])->statePath('data')->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Chemical type updated successfully');
        $this->redirectRoute('chemical_types.index');
    }

    public function render(): View
    {
        return view('livewire.chemical-types.edit-chemical-type');
    }
}
