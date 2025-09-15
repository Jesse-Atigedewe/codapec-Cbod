<?php

namespace App\Livewire\Dispatches;

use App\Models\ChemicalRequest;
use App\Models\Dispatch;
use App\Models\DcoReceivedChemicals;
use App\Models\District;
use App\Models\Region;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
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
use Livewire\Component;

class EditDispatch extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Dispatch $record;

    public ?array $data = [];

 public function mount(): void
{
    $state = $this->record->toArray();

    $this->form->fill($state);
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
    ->options(function () {
        return ChemicalRequest::query()
            ->with('chemical')
            ->get()
            ->mapWithKeys(fn($request) => [
                $request->id => "Request #{$request->id} | "
                    . $request->chemical->name
                    . " | Requested: {$request->quantity} | "
                    . "Dispatched: {$request->fresh()->dispatched_quantity} | "
                    . "Remaining: {$request->fresh()->remaining_quantity}",
            ])
            ->toArray();
    }) // 👈 wrap in closure so it re-runs dynamically
    ->default(fn () => $this->record->chemical_request_id)
    ->disabled()
    ->dehydrated(false)
    ->reactive(),




                // Hidden fields (these actually get saved)
                Hidden::make('region_id')->dehydrated(),
                Hidden::make('district_id')->dehydrated(),

                // Display-only for user context (not saved)
                Select::make('region_id')
                    ->label('Region')
                    ->options(Region::pluck('name', 'id'))
                    ->disabled()
                    ->dehydrated(false),

                Select::make('district_id')
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


    public function save(): void
{
    $wasRmApproved = (bool) $this->record->regional_manager_approved;

    $data = $this->form->getState();

    // ✅ Quantity validation (like in Create)
    $chemicalRequest = ChemicalRequest::findOrFail($this->record->chemical_request_id);

    $alreadyDispatched = Dispatch::where('chemical_request_id', $chemicalRequest->id)
        ->where('id', '!=', $this->record->id) // exclude current dispatch
        ->get()
        ->sum(fn($dispatch) => collect($dispatch->drivers)->sum('quantity'));

    $newQuantity = collect($data['drivers'])->sum('quantity');

    if (($alreadyDispatched + $newQuantity) > $chemicalRequest->quantity) {
        Notification::make()
            ->title("Total dispatch quantity ({$alreadyDispatched} + {$newQuantity}) exceeds request limit ({$chemicalRequest->quantity})")
            ->danger()
            ->send();
        return;
    }

    // ✅ Update current dispatch
    $this->record->update($data);

    // ✅ Recalculate all dispatch quantities for this request
    $totalDispatched = Dispatch::where('chemical_request_id', $chemicalRequest->id)
        ->get()
        ->sum(fn($dispatch) => collect($dispatch->drivers)->sum('quantity'));

    $remaining = max(0, $chemicalRequest->quantity - $totalDispatched);

    // ✅ Update chemical request with synced quantities
    $chemicalRequest->update([
        'dispatched_quantity' => $totalDispatched,
        'remaining_quantity'  => $remaining,
        'status'              => $totalDispatched >= $chemicalRequest->quantity
                                    ? 'dispatched'
                                    : 'approved',
    ]);

    // ✅ Create receiving record if RM just approved
    $totalQuantity = collect($this->record->drivers)->sum('quantity');

    if (!$wasRmApproved && $this->record->regional_manager_approved) {
        DcoReceivedChemicals::updateOrCreate(
            [
                'dispatch_id' => $this->record->id,
            ],
            [
                'user_id' => $this->record->user_id,
                'district_id' => $this->record->district_id,
                'region_id' => $this->record->region_id,
                'quantity_received' => $totalQuantity,
                'quantity_distributed' => 0,
                'received_at' => now(),
            ]
        );
    }

    Notification::make()
        ->success()
        ->title('Dispatch updated successfully')
        ->send();

    $this->redirectRoute('dispatches.index');
}

    public function render(): View
    {
        return view('livewire.dispatches.edit-dispatch');
    }
}
