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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
// Repeater removed: using single driver fields instead
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
                    ->required()
                    ->openable(),

                // Item request select
               Select::make('chemical_request_id')
    ->label('Item Request')
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



                // Single driver fields (each create creates one Dispatch)
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
                Hidden::make('trip_complete')->default(false),
            ])
            ->statePath('data')
            ->model(Dispatch::class);
    }


public function create(): void
{
    $data = $this->form->getState();

    $chemicalRequest = ChemicalRequest::findOrFail($data['chemical_request_id']);

    $alreadyDispatched = $chemicalRequest->dispatched_quantity;
    $newQuantity = (int) ($data['quantity'] ?? 0);

    // Validate
    if (($alreadyDispatched + $newQuantity) > $chemicalRequest->quantity) {
        Notification::make()
            ->title("{$newQuantity} exceeds request limit of ({$chemicalRequest->quantity})")
            ->danger()
            ->send();
        return;
    }

   
    // Create dispatch
    $record = Dispatch::create([
        ...$data,
        'chemical_id'    => $chemicalRequest->chemical_id,
        'user_id'        => Auth::id(),
    ]);

    $this->form->model($record)->saveRelationships();

    // Refresh totals
   $totalDispatched = Dispatch::where('chemical_request_id', $chemicalRequest->id)->sum('quantity');
 
$remaining = max(0, $chemicalRequest->quantity - $totalDispatched);

if ($remaining === 0 && $chemicalRequest->status !== 'dispatched') {
    $chemicalRequest->update(['status' => 'dispatched']);
    Notification::make()
        ->success()
        ->title('Items fully dispatched')
        ->send();
}else{
    Notification::make()
        ->success()
        ->title('Dispatch created successfully')
        ->body("Total dispatched: {$totalDispatched}. Remaining: {$remaining}.")
        ->send();
}



    $this->redirectRoute('dispatches.index');
}


    public function render(): View
    {
        return view('livewire.dispatches.create-dispatches');
    }
}
