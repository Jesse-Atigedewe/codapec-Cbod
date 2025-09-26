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
                        // map state -> default unit
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
                    ->reactive()
                    ->afterStateHydrated(function ($state, $set, $get) {
                        // noop here; unit will be adjusted when state changes via the state select
                    }),
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


