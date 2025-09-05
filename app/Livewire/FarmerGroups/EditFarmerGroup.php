<?php

namespace App\Livewire\FarmerGroups;

use App\Models\District;
use App\Models\FarmerGroup;
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

class EditFarmerGroup extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public FarmerGroup $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('region_id')->options(Region::all()->pluck('name','id')),
                 Select::make('district_id')
                ->label('District')
                ->options(function (callable $get) {
                    $regionId = $get('region_id');
                    return $regionId ? District::where('region_id', $regionId)->pluck('name', 'id') : [];
                })
                ->required()
                ->searchable(),
                TextInput::make('name'),
                TextInput::make('leader_name'),
                TextInput::make('leader_contact'),
                TextInput::make('number_of_members')->numeric(),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Farmer group updated successfully');
        $this->redirectRoute('farmer_groups.index');
    }

    public function render(): View
    {
        return view('livewire.farmer-groups.edit-farmer-group');
    }
}


