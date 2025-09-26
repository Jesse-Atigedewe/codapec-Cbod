<?php

namespace App\Livewire\Dispatches;

use Livewire\Component;
use App\Models\Dispatch;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ViewDispatch extends Component
{
    public ?Dispatch $dispatch;
    // when viewing all dispatches for a chemical request
    public ?EloquentCollection $dispatches = null;

    /**
     * Mount the component with a specific dispatch record.
     */
    public function mount($record = null, $byChemicalRequest = false): void
    {
        if ($byChemicalRequest) {
            // $record is expected to be chemical_request_id
            $this->dispatches = Dispatch::with(['chemical', 'chemicalRequest'])
                ->where('chemical_request_id', $record)
                ->get();

            // no legacy drivers JSON: use concrete dispatch columns

            // keep $dispatch null when listing multiple
            $this->dispatch = null;
            return;
        }

        // single dispatch view
        $this->dispatch = Dispatch::with(['chemicalRequest', 'chemical'])
            ->findOrFail($record);

    // no legacy drivers JSON: single-driver dispatch columns are used
    }

    /**
     * Toggle trip_complete for a specific driver.
     */
    public function toggleDriver(?int $dispatchId = null): void
    {
        $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }

        // toggle concrete trip_complete column (single-driver per dispatch)
        $dispatch->trip_complete = !($dispatch->trip_complete ?? false);
        $dispatch->save();

    $driverName = $dispatch->driver_name ?? 'Driver';

        Notification::make()
            ->success()
            ->title("Driver trip status updated")
            ->body("{$driverName} trip is now " . ($dispatch->trip_complete ? 'Complete' : 'Pending'))
            ->send();
    }

    /**
     * Toggle DCO approval for the dispatch.
     */
    public function toggleDco(?int $dispatchId = null): void
    {
        if (Auth::user()->role !== 'dco') {
            return;
        }

        $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }

        // Only allow if the trip for this dispatch is complete
        if (empty($dispatch->trip_complete)) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("All trips must be marked complete first.")
                ->send();
            return;
        }

        $dispatch->dco_approved = !$dispatch->dco_approved;
        $dispatch->dco_approved_by = $dispatch->dco_approved ? Auth::id() : null;
        $dispatch->dco_approved_at = $dispatch->dco_approved ? now() : null;
        $dispatch->save();

        Notification::make()
            ->success()
            ->title($dispatch->dco_approved ? "DCO approved dispatch" : "DCO approval revoked")
            ->send();
    }

    /**
     * Toggle Auditor approval.
     */
    public function toggleAuditor(?int $dispatchId = null): void
    {
        if (Auth::user()->role !== 'auditor') {
            return;
        }

        $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }

        if (!$dispatch->dco_approved) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("DCO must approve first.")
                ->send();
            return;
        }

        $dispatch->auditor_approved = !$dispatch->auditor_approved;
        $dispatch->auditor_approved_by = $dispatch->auditor_approved ? Auth::id() : null;
        $dispatch->auditor_approved_at = $dispatch->auditor_approved ? now() : null;
        $dispatch->save();

        Notification::make()
            ->success()
            ->title($dispatch->auditor_approved ? "Auditor approved" : "Auditor approval revoked")
            ->send();
    }

    /**
     * Toggle Regional Manager approval.
     */

        // Update received chemicals if approved
    public function toggleRm(?int $dispatchId = null): void
    {
        if (Auth::user()->role !== 'regional_manager') {
            return;
        }

        $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }

        if (!$dispatch->auditor_approved || empty($dispatch->trip_complete)) {
            Notification::make()
                ->warning()
                ->title("Cannot approve")
                ->body("Auditor approval and all trips must be completed first.")
                ->send();
            return;
        }

        $dispatch->regional_manager_approved = !$dispatch->regional_manager_approved;
        $dispatch->regional_manager_approved_by = $dispatch->regional_manager_approved ? Auth::id() : null;
        $dispatch->regional_manager_approved_at = $dispatch->regional_manager_approved ? now() : null;

        // ✅ If approved, update stocks & mark dispatch as delivered
        if ($dispatch->regional_manager_approved) {
            // For single-driver dispatches, the dispatch->quantity represents the dispatched amount
            $totalQuantity = $dispatch->quantity ?? 0;

            \App\Models\DcoReceivedChemicals::updateOrCreate(
                ['dispatch_id' => $dispatch->id],
                [
                    'user_id' => Auth::id(),
                    'district_id' => $dispatch->district_id,
                    // 'region_id' => $dispatch->region_id,
                    'quantity_received' => $totalQuantity,
                    'quantity_distributed' => 0,
                    'received_at' => now(),
                ]
            );

            // ✅ Mark dispatch as delivered
            $dispatch->status = 'delivered';
            $dispatch->delivered_at = now();
        }

        $dispatch->save();

        Notification::make()
            ->success()
            ->title($dispatch->regional_manager_approved ? "RM approved dispatch & marked delivered" : "RM approval revoked")
            ->send();
    }



    public function render()
    {
        return view('livewire.dispatches.view-dispatch', [
            'dispatch' => $this->dispatch,
            'dispatches' => $this->dispatches,
        ]);
    }
}
