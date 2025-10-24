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

class CreateChemical extends Component implements HasActions, HasSchemas
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
                TextInput::make('name')
                    ->label('Input Name')
                    ->required()
                    ->maxLength(255),

                Select::make('type_id')
                    ->label('Input Type')
                    ->options(fn() => \App\Models\ChemicalType::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),

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
            ->model(Chemical::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = Chemical::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Input created successfully');
        $this->redirectRoute('chemicals.index');
    }

    public function render(): View
    {
        return view('livewire.chemicals.create-chemical');
    }
}


