<?php

namespace App\Livewire\DcoDistributions;

use App\Models\DcoReceivedChemicals;
use App\Models\Farmer;
use App\Models\FarmerDistributionRecord;
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

class DistributeToFarmers extends Component implements HasActions, HasSchemas
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
            Hidden::make('request_id')->dehydrated(),

            // 🔹 Dispatch must come *before* Farmer so that request_id gets set first
            Select::make('dispatch_id')
                ->label('Dispatch / Received Stock')
                ->options(function () {
                    return DcoReceivedChemicals::query()
                        ->whereColumn('quantity_received', '>', 'quantity_distributed')
                        ->with(['dispatch.chemicalRequest.chemical'])
                        ->get()
                        ->mapWithKeys(function ($r) {
                            $chem = optional(optional($r->dispatch->chemicalRequest)->chemical)->name ?? 'Unknown chemical';
                            $remaining = (float)$r->quantity_received - (float)$r->quantity_distributed;

                            return [
                                $r->dispatch_id => sprintf(
                                    'Dispatch #%d | %s | Received: %s | Distributed: %s | Remaining: %s',
                                    $r->dispatch_id,
                                    $chem,
                                    $r->quantity_received,
                                    $r->quantity_distributed,
                                    $remaining
                                ),
                            ];
                        });
                })
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $received = DcoReceivedChemicals::where('dispatch_id', $state)->first();
                    $set('request_id', $received?->request_id);
                })
                ->searchable()
                ->preload()
                ->required(),

            // 🔹 Farmer select depends on request_id set above
            // Select::make('farmer_id')
            //     ->label('Farmer')
            //     ->options(function (callable $get) {
            //         $requestId = $get('request_id');

            //         if (! $requestId) {
            //             return [];
            //         }

            //         return Farmer::whereHas('requests', function ($query) use ($requestId) {
            //                 $query->where('requests.id', $requestId);
            //             })
            //             ->pluck('farmer_name', 'id')
            //             ->toArray();
            //     })
            //     ->reactive() // updates when request_id changes
            //     ->searchable()
            //     ->preload()
            //     ->hint(fn (callable $get) => 'Request ID: ' . ($get('request_id') ?: 'none'))
            //     ->required(),

            Select::make('farmer_id')
    ->label('Farmer')
    ->options(function (callable $get) {
        $requestId = $get('request_id');

        if (! $requestId) {
            return [];
        }

        return \DB::table('request_farmers')
            ->join('farmers', 'request_farmers.farmer_id', '=', 'farmers.id')
            ->join('requests', 'request_farmers.request_id', '=', 'requests.id')
            ->join('chemicals', 'requests.chemical_id', '=', 'chemicals.id')
            ->where('request_farmers.request_id', $requestId)
            ->select(
                'farmers.id',
                'farmers.farmer_name',
                'request_farmers.allocated_quantity',
                'chemicals.unit'
            )
            ->get()
            ->mapWithKeys(function ($row) {
                $quantity = number_format($row->allocated_quantity, 2);
                return [
                    $row->id => "{$row->farmer_name} — {$quantity} {$row->unit} allocated",
                ];
            })
            ->toArray();
    })
    ->reactive()
    ->searchable()
    ->preload()
    ->hint(fn (callable $get) => 'Request ID: ' . ($get('request_id') ?: 'none'))
    ->required(),

    

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->minValue(0.01)
                ->required(),

            TextInput::make('notes')
                ->label('Notes')
                ->nullable(),

            Hidden::make('distributed_at')->default(now())->dehydrated(),
            Hidden::make('distributed_by')->default(fn () => Auth::id())->dehydrated(),
        ])
        ->statePath('data')
        ->model(FarmerDistributionRecord::class);
}


    public function create(): void
    {
        $data = $this->form->getState();
        $userId = Auth::id();

        // Resolve the exact received-chemicals record for this DCO + selected dispatch
        $received = DcoReceivedChemicals::query()
            // ->where('user_id', $userId)
            ->where('dispatch_id', $data['dispatch_id'])
            ->latest('received_at') // in case there are multiple receipts for the same dispatch
            ->first();

        if (! $received) {
            Notification::make()
                ->danger()
                ->title('Invalid or unavailable stock selection.')
                ->send();
            return;
        }

        // Balance check
        $remaining = (float)$received->quantity_received - (float)$received->quantity_distributed;
        $qty       = (float)$data['quantity'];

        if ($qty <= 0) {
            Notification::make()->danger()->title('Quantity must be greater than zero.')->send();
            return;
        }

        if ($qty > $remaining) {
            Notification::make()
                ->danger()
                ->title("Not enough stock. Remaining: {$remaining}")
                ->send();
            return;
        }

        // Create distribution record (store link to the exact stock row via FK)
        $payload = [
            'dispatch_id'              => $data['dispatch_id'],
            'farmer_id'                => $data['farmer_id'],
            'quantity'                 => $qty,
            'distributed_by'           => $data['distributed_by'] ?? $userId,
            'distributed_at'           => $data['distributed_at'] ?? now(),
            'notes'                    => $data['notes'] ?? null,
            'dco_received_chemical_id' => $received->id, // keep traceability to the stock row
        ];

        $record = FarmerDistributionRecord::create($payload);

        // Update distributed quantity on the stock record
        $received->increment('quantity_distributed', $qty);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->success()
            ->title('Distributed to farmer successfully.')
            ->send();

        // Reset the form
        $this->form->fill();
        $this->redirectRoute('dco.distribute.farmers');
    }

    public function render(): View
    {
        return view('livewire.dco-distributions.distribute-to-farmers');
    }
}
