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
                    ->label('Chemical Name')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Chemical Type')
                    ->options([
                        'insecticide' => 'Insecticide',
                        'fungicide'   => 'Fungicide',
                        'herbicide'   => 'Herbicide',
                        'fertilizer'  => 'Fertilizer',
                    ])
                    ->required(),

                Select::make('state')
                    ->label('Physical State')
                    ->options([
                        'granular' => 'Granular',
                        'solid'    => 'Solid',
                        'liquid'   => 'Liquid',
                        'powder'   => 'Powder',
                    ])
                    ->required(),

                Select::make('unit')
                    ->label('Unit of Measure')
                    ->options([
                        'liters'  => 'Liters',
                        'kg'      => 'Kilograms',
                        'bottles' => 'Bottles',
                    ])
                    ->default('liters')
                    ->required(),
            ])
            ->statePath('data')
            ->model(Chemical::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = Chemical::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Chemical created successfully');
        $this->redirectRoute('chemicals.index');
    }

    public function render(): View
    {
        return view('livewire.chemicals.create-chemical');
    }
}


