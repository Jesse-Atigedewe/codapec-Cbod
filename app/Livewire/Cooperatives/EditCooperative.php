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

class EditCooperative extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Cooperative $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
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
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Cooperative updated successfully');
        $this->redirectRoute('cooperatives.index');
    }

    public function render(): View
    {
        return view('livewire.cooperatives.edit-cooperative');
    }
}


