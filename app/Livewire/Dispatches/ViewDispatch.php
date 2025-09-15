<?php

namespace App\Livewire\Dispatches;

use Livewire\Component;
use App\Models\Dispatch;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ViewDispatch extends Component
{
    public ?Dispatch $dispatch;

    /**
     * Mount the component with a specific dispatch record.
     */
    public function mount( $record= null): void
    {
        // Load dispatch with related chemicalRequest and drivers
        $this->dispatch = Dispatch::with(['chemicalRequest'])
            ->findOrFail($record);

        // Ensure drivers array exists
        $this->dispatch->drivers = $this->dispatch->drivers ?? [];
    }

    /**
     * Toggle trip_complete for a specific driver.
     */
    public function toggleDriver(int $driverIndex): void
    {
        $drivers = $this->dispatch->drivers;

        if (!array_key_exists($driverIndex, $drivers)) {
            return;
        }

        $drivers[$driverIndex]['trip_complete'] = !($drivers[$driverIndex]['trip_complete'] ?? false);

        $this->dispatch->drivers = $drivers;
        $this->dispatch->save();

        Notification::make()
            ->success()
            ->title("Driver trip status updated")
            ->body("Driver {$drivers[$driverIndex]['driver_name']} trip is now " . ($drivers[$driverIndex]['trip_complete'] ? 'Complete' : 'Pending'))
            ->send();
    }

    /**
     * Toggle DCO approval for the dispatch.
     */
    public function toggleDco(): void
    {
        if (Auth::user()->role !== 'dco') {
            return;
        }

        // Only allow if all trips complete
        if (!collect($this->dispatch->drivers)->every(fn($d) => !empty($d['trip_complete']))) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("All trips must be marked complete first.")
                ->send();
            return;
        }

        $this->dispatch->dco_approved = !$this->dispatch->dco_approved;
        $this->dispatch->dco_approved_by = $this->dispatch->dco_approved ? Auth::id() : null;
        $this->dispatch->dco_approved_at = $this->dispatch->dco_approved ? now() : null;
        $this->dispatch->save();

        Notification::make()
            ->success()
            ->title($this->dispatch->dco_approved ? "DCO approved dispatch" : "DCO approval revoked")
            ->send();
    }

    /**
     * Toggle Auditor approval.
     */
    public function toggleAuditor(): void
    {
        if (Auth::user()->role !== 'auditor') {
            return;
        }

        if (!$this->dispatch->dco_approved) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("DCO must approve first.")
                ->send();
            return;
        }

        $this->dispatch->auditor_approved = !$this->dispatch->auditor_approved;
        $this->dispatch->auditor_approved_by = $this->dispatch->auditor_approved ? Auth::id() : null;
        $this->dispatch->auditor_approved_at = $this->dispatch->auditor_approved ? now() : null;
        $this->dispatch->save();

        Notification::make()
            ->success()
            ->title($this->dispatch->auditor_approved ? "Auditor approved" : "Auditor approval revoked")
            ->send();
    }

    /**
     * Toggle Regional Manager approval.
     */
    public function toggleRm(): void
    {
        if (Auth::user()->role !== 'regional_manager') {
            return;
        }

        if (!$this->dispatch->auditor_approved || collect($this->dispatch->drivers)->contains(fn($d) => empty($d['dco_approved_trip']))) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("Auditor approval and all DCO-approved trips required.")
                ->send();
            return;
        }

        $this->dispatch->regional_manager_approved = !$this->dispatch->regional_manager_approved;
        $this->dispatch->regional_manager_approved_by = $this->dispatch->regional_manager_approved ? Auth::id() : null;
        $this->dispatch->regional_manager_approved_at = $this->dispatch->regional_manager_approved ? now() : null;
        $this->dispatch->save();

        // Update received chemicals if approved
        if ($this->dispatch->regional_manager_approved) {
            $totalQuantity = collect($this->dispatch->drivers)->sum('quantity');
            \App\Models\DcoReceivedChemicals::updateOrCreate(
                ['dispatch_id' => $this->dispatch->id],
                [
                    'user_id' => Auth::id(),
                    'district_id' => $this->dispatch->district_id,
                    'region_id' => $this->dispatch->region_id,
                    'quantity_received' => $totalQuantity,
                    'quantity_distributed' => 0,
                    'received_at' => now(),
                ]
            );
        }

        Notification::make()
            ->success()
            ->title($this->dispatch->regional_manager_approved ? "RM approved dispatch" : "RM approval revoked")
            ->send();
    }

    public function render()
    {
        
        return view('livewire.dispatches.view-dispatch', [
            'dispatch' => $this->dispatch,
        ]);
    }
}
