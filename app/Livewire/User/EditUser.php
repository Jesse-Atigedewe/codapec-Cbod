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

class EditUser extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?User $user = null;
    public ?array $data = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        // Pre-fill form with user’s data
        $this->form->fill($this->user->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
        ->model($this->user)
            ->components([
            
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),

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
                ->searchable(),

                Select::make('role')
                    ->label('Role')
                    ->options([
                        'superadmin' => 'Superadmin',
                        'admin' => 'Admin',
                        'codapecrep' => 'CODAPEC Rep',
                        'dco' => 'DCO',
                        'auditor' => 'Auditor',
                        'regional_manager' => 'Regional Manager',
                    ])
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $this->user->update($data);
        Notification::make()
            ->title('User updated')
            ->success()
            ->send();
        // redirect user to user index
        $this->redirectRoute('users.index');
    }

    public function render(): View
    {
        return view('livewire.user.edit-user');
    }
}
