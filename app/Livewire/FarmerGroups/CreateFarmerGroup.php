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

class CreateFarmerGroup extends Component implements HasActions, HasSchemas
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
                Select::make('region_id')->options(Region::all()->pluck('name','id')),
                 Select::make('district_id')
                ->label('District')
                ->options(function (callable $get) {
                    $regionId = $get('region_id');
                    return $regionId ? District::where('region_id', $regionId)->pluck('name', 'id') : [];
                })
                ->required()
                ->searchable(),
                TextInput::make('name')->label('Cooperative Name'),
                TextInput::make('leader_name')->label('Leader Name'),
                TextInput::make('leader_contact')->label('Leader Contact'),
                TextInput::make('number_of_members')->numeric(),
            ])
            ->statePath('data')
            ->model(FarmerGroup::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = FarmerGroup::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Farmer group created successfully');
        $this->redirectRoute('farmer_groups.index');
    }

    public function render(): View
    {
        return view('livewire.farmer-groups.create-farmer-group');
    }
}


