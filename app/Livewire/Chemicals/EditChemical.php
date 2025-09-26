<?php

namespace App\Livewire\Chemicals;

use App\Models\Chemical;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditChemical extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Chemical $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('name')
                ->label('Item Name')
                ->required()
                ->maxLength(255),

            Select::make('type_id')
                ->label('Item Type')
                ->options(fn() => \App\Models\ChemicalType::pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),

            Select::make('state')
                ->label('Physical State')
                ->options([
                    'granular' => 'Granular',
                    'solid'    => 'Solid',
                    'liquid'   => 'Liquid',
                    'powder'   => 'Powder',
                ])
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if (in_array($state, ['granular', 'solid', 'powder'])) {
                        $set('unit', 'kg');
                    } elseif ($state === 'liquid') {
                        $set('unit', 'liters');
                    }
                }),

            Select::make('unit')
                ->label('Unit of Measure')
                ->options([
                    'liters'  => 'Liters',
                    'kg'      => 'Kilograms',
                    'bottles' => 'Bottles',
                ])
                ->default('liters')
                ->required()
                ->reactive(),
        ])
        ->statePath('data')
        ->model($this->record);
}


    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Chemical updated successfully');
        $this->redirectRoute('chemicals.index');
    }

    public function render(): View
    {
        return view('livewire.chemicals.edit-chemical');
    }
}


