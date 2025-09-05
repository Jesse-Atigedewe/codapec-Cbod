<?php

namespace App\Livewire\WarehouseStocks;

use App\Models\Chemical;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
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

class CreateWarehouseStock extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    protected function getUserWarehouseId(): ?int
    {
        $userId = Auth::id();
        return Warehouse::query()->where('user_id', $userId)->value('id');
    }

    public function mount(): void
    {
        $this->form->fill([
            'warehouse_id' => $this->getUserWarehouseId(),
            'received_date' => now(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('chemical_id')->options(Chemical::query()->pluck('name','id'))->searchable()->required(),
                TextInput::make('quantity_received')->numeric()->required(),
                TextInput::make('batch_number'),
                DatePicker::make('received_date')->required(),
            ])
            ->statePath('data')
            ->model(WarehouseStock::class);
    }

    public function create(): void
    {
        $warehouseId = $this->getUserWarehouseId();
        $authUserId = Auth::id();
        $data = $this->form->getState();

        $existing = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('chemical_id', $data['chemical_id'])
            ->first();

        if ($existing) {
            $existing->quantity_received += $data['quantity_received'];
            $existing->quantity_available += $data['quantity_received'];
            $existing->batch_number = $data['batch_number'] ?? $existing->batch_number;
            $existing->received_date = $data['received_date'] ?? $existing->received_date;
            $existing->save();
            $this->form->model($existing)->saveRelationships();
            Notification::make()->success()->title('Stock incremented')->send();
        } else {
            $record = WarehouseStock::create($data + [
                'warehouse_id' => $warehouseId,
                'user_id' => $authUserId,
                'quantity_available' => $data['quantity_received']
            ]);
            $this->form->model($record)->saveRelationships();
            Notification::make()->success()->title('Stock created')->send();
        }
        $this->redirectRoute('warehouse_stocks.index');
    }

    public function render(): View
    {
        return view('livewire.warehouse-stocks.create-warehouse-stock');
    }
}


