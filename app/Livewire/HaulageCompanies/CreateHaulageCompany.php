<?php

namespace App\Livewire\HaulageCompanies;

use App\Models\HaulageCompany;
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

class CreateHaulageCompany extends Component implements HasActions, HasSchemas
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
                TextInput::make('name'),
                TextInput::make('email'),
                TextInput::make('phone'),
                TextInput::make('address'),
                TextInput::make('contact_person'),
                Select::make('status')->options([
                    'active'=>'active',
                    'inactive'=>'inactive'
                ])
            ])
            ->statePath('data')
            ->model(HaulageCompany::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = HaulageCompany::create($data);
        $this->form->model($record)->saveRelationships();
        Notification::make()->success()->title('Haulage company created successfully');
        $this->redirectRoute('haulage_companies.index');
    }

    public function render(): View
    {
        return view('livewire.haulage-companies.create-haulage-company');
    }
}


