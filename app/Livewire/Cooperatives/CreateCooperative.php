<?php

namespace App\Livewire\Cooperatives;

use App\Models\Cooperative;
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

class CreateCooperative extends Component implements HasActions, HasSchemas
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
                ->label('Select region')
                ->options(Region::all()->pluck('name','id')),
                Select::make('district_id')
                ->label('Select district')
                
                ->options(District::all()->pluck('name','id')),
                TextInput::make('name')->label('Cooperative Name'),
                TextInput::make('leader_name')->label('Leader Name'),
                TextInput::make('leader_contact')->label('Leader Contact'),
                TextInput::make('number_of_members')->numeric(),
            ])
            ->statePath('data')
            ->model(Cooperative::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = Cooperative::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Cooperative created successfully');
        $this->redirectRoute('cooperatives.index');
    }

    public function render(): View
    {
        return view('livewire.cooperatives.create-cooperative');
    }
}


