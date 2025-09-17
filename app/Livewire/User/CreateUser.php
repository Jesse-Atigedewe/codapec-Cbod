<?php

namespace App\Livewire\User;

use App\Models\District;
use App\Models\Region;
use App\Models\User;
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


class CreateUser extends Component implements HasActions, HasSchemas
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
        
            ->schema([
            TextInput::make('name')->required()->label('Full Name')->placeholder('Enter full name'),
                TextInput::make('email')->required()->label('Email')->placeholder('Enter email'),
                TextInput::make('password')->required()->label('Password')->placeholder('Enter password')->type('password'),
                 // Region select
            Select::make('region_id')
                ->label('Region')
                ->options(Region::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('district_id', null)), // reset district on region change

            // District select, filtered by region
            Select::make('district_id')
                ->label('District')
                ->options(function (callable $get) {
                    $regionId = $get('region_id');
                    return $regionId ? District::where('region_id', $regionId)->pluck('name', 'id') : [];
                })
                ->required()
                ->preload()
                ->searchable(),

                Select::make('role')->required()->label('Role')->options([
                     'superadmin' => 'Superadmin',
                     'admin' => 'Admin',
                     'codapecrep' => 'CODAPEC Rep',
                     'dco' => 'DCO',
                     'auditor' => 'Auditor',
                     'regional_manager' => 'Regional Manager',
                 ]),
                
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();
        Notification::make()
            ->title('User created successfully')
            ->success()
            ->send();
        $this->redirect(route('users.index'));
    }

    public function render(): View
    {
        return view('livewire.user.create-user');
    }
}
