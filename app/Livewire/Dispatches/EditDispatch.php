<?php

namespace App\Livewire\Dispatches;

use App\Models\Dispatch;
use App\Models\DcoReceivedChemicals;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
    $this->form->fill($this->record);
}

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('waybill')->image()->directory('waybills')->downloadable()->openable(),
                Toggle::make('dco_approved'),
                Toggle::make('auditor_approved'),
                Toggle::make('regional_manager_approved'),
                DateTimePicker::make('dispatched_at'),
                DateTimePicker::make('delivered_at'),
            ])
            ->statePath('data')
            ->model($this->record);
    }

   public function save(): void
{
    $wasRmApproved = (bool) $this->record->regional_manager_approved;

    $data = $this->form->getState();
    $this->record->update($data);

    if (! $wasRmApproved && $this->record->regional_manager_approved) {
        DcoReceivedChemicals::updateOrCreate(
            [
                'dispatch_id' => $this->record->id,
            ],
            [
                'user_id' => $this->record->user_id, // or Auth::id() if it's the receiver
                'district_id' => $this->record->district_id,
                'region_id' => $this->record->region_id,
                'quantity_received' => $this->record->quantity, // should exist on dispatch
                'quantity_distributed' => 0,
                'received_at' => now(),
            ]
        );
    }

    Notification::make()
        ->success()
        ->title('Dispatch updated')
        ->send();

    $this->redirectRoute('dispatches.index');
}

    public function render(): View
    {
        return view('livewire.dispatches.edit-dispatch');
    }
}


