<?php

namespace App\Livewire\Regions;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Region;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class CreateRegion extends Component implements HasActions, HasSchemas
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
                TextInput::make('name')->required()
            ])
            ->statePath('data')
            ->model(Region::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Region::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Region created successfully');
        $this->redirectRoute('regions.index');

    }

    public function render(): View
    {
        return view('livewire.regions.create-region');
    }
}
