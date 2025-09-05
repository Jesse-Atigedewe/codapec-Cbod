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

class EditHaulageCompany extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public HaulageCompany $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
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
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->update($data);
        Notification::make()->success()->title('Haulage company updated successfully');
        $this->redirectRoute('haulage_companies.index');
    }

    public function render(): View
    {
        return view('livewire.haulage-companies.edit-haulage-company');
    }
}


