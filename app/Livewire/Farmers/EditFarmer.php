<?php

namespace App\Livewire\Farmers;

use App\Models\District;
use App\Models\Farmer;
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

class EditFarmer extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Farmer $record;

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
                ->label('region')
                ->options(Region::all()->pluck('name','id')),
                Select::make('district_id')
                ->label('district')
                ->options(District::all()->pluck('name','id')),
                TextInput::make('name'),
                TextInput::make('contact_number')->tel(),
                TextInput::make('farm_size')->numeric(),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Farmer updated successfully');
        $this->redirectRoute('farmers.index');
    }

    public function render(): View
    {
        return view('livewire.farmers.edit-farmer');
    }
}


