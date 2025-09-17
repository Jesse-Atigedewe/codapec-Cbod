<?php

namespace App\Livewire\Warehouse;

use App\Models\User;
use App\Models\Warehouse;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditWarehouse extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Warehouse $warehouse =null;

    public ?array $data = [];

    public function mount(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
        $this->form->fill($this->warehouse->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->label('codapec rep')
                     ->options(User::where('role', 'codapecrep')->pluck('name', 'id'))

                    ->required(),

                TextInput::make('name')
                    ->label('Warehouse Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->label('Location')
                    ->placeholder('Enter location')
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter description')
                    ->rows(4)
                    ->nullable(),
            ])
            ->statePath('data')
            ->model($this->warehouse);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->warehouse->update($data);

        $this->form->model($this->warehouse)->saveRelationships();

        Notification::make()->success()->title('Warehouse updated successfully!')->send();
        redirect()->route('warehouses.list');
    }

    public function render(): View
    {
        return view('livewire.warehouse.edit-warehouse');
    }
}
