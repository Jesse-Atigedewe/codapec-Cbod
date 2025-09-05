<?php

namespace App\Livewire\DcoDistributions;

use App\Models\DcoReceivedChemicals;
use App\Models\FarmerGroup;
use App\Models\FarmerGroupDistributionRecord;
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

class DistributeToFarmerGroups extends Component implements HasActions, HasSchemas
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
                // Farmer group select
                Select::make('farmer_group_id')
                    ->label('Farmer Group')
                    ->options(fn () => FarmerGroup::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                // Quantity
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                
                  // Pick which dispatch's stock to distribute from (by dispatch_id)
                Select::make('dispatch_id')
                    ->label('Dispatch / Received Stock')
                    ->options(function () {
                        // Only show this DCO’s stocks that still have remaining balance
                        return DcoReceivedChemicals::query()
                            // ->where('user_id', Auth::id())
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
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('notes')->label('Notes')->nullable(),

                Hidden::make('distributed_at')->default(now())->dehydrated(),
                Hidden::make('distributed_by')->default(fn () => Auth::id())->dehydrated(),
            ])
            ->statePath('data')
            ->model(FarmerGroupDistributionRecord::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $userId = Auth::id();

        // resolve the received stock
        $received = DcoReceivedChemicals::query()
            // ->where('user_id', $userId)
            ->where('dispatch_id', $data['dispatch_id'])
            ->latest('received_at')
            ->first();

        if (! $received) {
            Notification::make()
                ->danger()
                ->title('Invalid or unavailable stock selection.')
                ->send();
            return;
        }

        // validate balance
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

        // create record
        $record = FarmerGroupDistributionRecord::create([
            'farmer_group_id'         => $data['farmer_group_id'],
            'quantity'                => $qty,
            'distributed_by'          => $userId,
            'distributed_at'          => $data['distributed_at'] ?? now(),
            'notes'                   => $data['notes'] ?? null,
            'dco_received_chemical_id'=> $received->id,
        ]);

        // update stock balance
        $received->increment('quantity_distributed', $qty);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->success()
            ->title('Distributed to farmer group successfully.')
            ->send();

        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.dco-distributions.distribute-to-farmer-groups');
    }
}
