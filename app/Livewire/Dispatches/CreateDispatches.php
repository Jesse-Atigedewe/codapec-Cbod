<?php

namespace App\Livewire\Dispatches;

use App\Models\ChemicalRequest;
use App\Models\Dispatch;
use App\Models\District;
use App\Models\Region;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                //waybill
                FileUpload::make('waybill')
                    ->label('Waybill')
                    ->disk('public')
                    ->image()
                    ->directory('waybills')
                    ->downloadable()
                    ->openable(),

                // Chemical request select
               Select::make('chemical_request_id')
    ->label('Chemical Request')
    ->options(
        ChemicalRequest::query()
            ->where('status', 'approved')
            ->with(['chemical', 'dispatches'])
            ->get()
            ->mapWithKeys(fn($request) => [
                $request->id => "Request #{$request->id} | "
                    . $request->chemical->name
                    . " | Requested: {$request->quantity} | "
                    . "Dispatched: {$request->dispatched_quantity} | "
                    . "Remaining: {$request->remaining_quantity}",
            ])
            ->toArray()
    )
    ->required()
    ->searchable()
    ->preload()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $request = ChemicalRequest::find($state);
        if ($request) {
            $set('region_id', $request->region_id);
            $set('district_id', $request->district_id);
            $set('region_display', $request->region_id);
            $set('district_display', $request->district_id);
        }
    }),



                // Hidden fields (these actually get saved)
                Hidden::make('region_id')->dehydrated(),
                Hidden::make('district_id')->dehydrated(),

                // Display-only for user context (not saved)
                Select::make('region_display')
                    ->label('Region')
                    ->options(Region::pluck('name', 'id'))
                    ->disabled()
                    ->dehydrated(false),

                Select::make('district_display')
                    ->label('District')
                    ->options(District::pluck('name', 'id'))
                    ->disabled()
                    ->dehydrated(false),




                // Drivers & Vehicles - stored in JSON
                Repeater::make('drivers')
                    ->label('Drivers & Vehicles')
                    ->schema([
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

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required(),

                        Hidden::make('trip_complete')
                            ->label('Trip Complete')
                            ->dehydrated()
                            ->default(false),
                    ])
                    ->columns(2)
                    ->required(),
            ])
            ->statePath('data')
            ->model(Dispatch::class);
    }


public function create(): void
{
    $data = $this->form->getState();

    $chemicalRequest = ChemicalRequest::findOrFail($data['chemical_request_id']);

    // Total already dispatched from previous records
    $alreadyDispatched = Dispatch::where('chemical_request_id', $chemicalRequest->id)
        ->get()
        ->sum(fn($dispatch) => collect($dispatch->drivers)->sum('quantity'));

    // Sum of new drivers quantities
    $newQuantity = collect($data['drivers'])->sum('quantity');

    // Validate against total allowed
    if (($alreadyDispatched + $newQuantity) > $chemicalRequest->quantity) {
        Notification::make()
            ->title("Total dispatch quantity ({$alreadyDispatched} + {$newQuantity}) exceeds request limit ({$chemicalRequest->quantity})")
            ->danger()
            ->send();
        return;
    }

    // ✅ Extract drivers separately so they don’t get overridden
    $drivers = $data['drivers'] ?? [];
    unset($data['drivers']);

    // ✅ Create the dispatch record
    $record = Dispatch::create([
        ...$data, // chemical_request_id, region_id, district_id
        'chemical_id' => $chemicalRequest->chemical_id,
        'user_id'     => Auth::id(),
        'drivers'     => $drivers, // array -> auto-cast to JSON
    ]);

    $this->form->model($record)->saveRelationships();

    // ✅ Recalculate total dispatched after this new dispatch
    $totalDispatched = Dispatch::where('chemical_request_id', $chemicalRequest->id)
        ->get()
        ->sum(fn($dispatch) => collect($dispatch->drivers)->sum('quantity'));

    // ✅ Flip status to "dispatched" only when fully dispatched
    if ($totalDispatched >= $chemicalRequest->quantity) {
        $chemicalRequest->update(['status' => 'dispatched']);
    }

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
