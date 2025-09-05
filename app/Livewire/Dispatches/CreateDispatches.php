<?php

namespace App\Livewire\Dispatches;

use App\Models\ChemicalRequest;
use App\Models\Dispatch;
use App\Models\District;
use App\Models\Region;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateDispatches extends Component implements HasActions, HasSchemas
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
                  // Automatically set the user creating the dispatch
            Hidden::make('user_id')
                ->default(fn () => Auth::id())
                ->dehydrated(),

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

            // Chemical request select
        Select::make('chemical_request_id')
    ->label('Chemical Request')
    ->options(
        ChemicalRequest::query()
            ->where('status', 'approved')
            ->with(['chemical', 'dispatches']) // eager load
            ->get()
            ->mapWithKeys(fn ($request) => [
                $request->id => "Request #{$request->id} | "
                    . $request->chemical->name
                    . " | Requested: {$request->quantity} | "
                    . "Dispatched: {$request->dispatches->sum('quantity')} | "
                    . "Remaining: " . ($request->quantity - $request->dispatches->sum('quantity')),
            ])
            ->toArray()
    )
    ->required()
    ->searchable(),



            // Driver details
            TextInput::make('driver_name')
                ->label('Driver Name')
                ->required()
                ->maxLength(255),

            TextInput::make('driver_phone')
                ->label('Driver Phone')
                ->required()
                ->tel()
                ->maxLength(20),

            TextInput::make('driver_license')
                ->label('Driver License')
                ->nullable()
                ->maxLength(50),

            TextInput::make('vehicle_number')
                ->label('Vehicle Number')
                ->required()
                ->maxLength(50),
            ])
            ->statePath('data')
            ->model(Dispatch::class);
    }

   public function create(): void
{
    $data = $this->form->getState();

    // Get chemical_id from the selected request
    $chemicalRequest = ChemicalRequest::findOrFail($data['chemical_request_id']);

    $record = Dispatch::create($data + [
        'chemical_id' => $chemicalRequest->chemical_id,
    ]);

    $this->form->model($record)->saveRelationships();

    Notification::make()
        ->title('Dispatch created successfully')
        ->success()
        ->send();

    $this->redirectRoute('dispatches.index');
}


    public function render(): View
    {
        return view('livewire.dispatches.create-dispatches');
    }
}
