<?php

namespace App\Livewire\Dispatches;

use App\Models\Comment;
use App\Models\DcoReceivedChemicals;
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

    //comment
    
    public string $commentText = '';

    /**
     * Mount the component with a specific dispatch record.
     */
    public function mount($record = null, $byChemicalRequest = false): void
    {
        if ($byChemicalRequest) {
            // $record is expected to be chemical_request_id
            $this->dispatches = Dispatch::with(['chemical', 'chemicalRequest','comments'])
                ->where('chemical_request_id', $record)
                ->get();

            // no legacy drivers JSON: use concrete dispatch columns

            // keep $dispatch null when listing multiple
            $this->dispatch = null;
            return;
        }

        // single dispatch view
        $this->dispatch = Dispatch::with(['chemicalRequest', 'chemical','comments'])
            ->findOrFail($record);

    // no legacy drivers JSON: single-driver dispatch columns are used
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
        if ($dispatch->dco_approved) {
            Notification::make()
                ->warning()
                ->title("Cannot perform request")
                ->body("Request already received.")
                ->send();
            return;
        }
    

        $dispatch->dco_approved = !$dispatch->dco_approved;
        $dispatch->dco_approved_by = $dispatch->dco_approved ? Auth::id() : null;
        $dispatch->dco_approved_at = $dispatch->dco_approved ? now() : null;

          if ($dispatch->dco_approved) {
            // For single-driver dispatches, the dispatch->quantity represents the dispatched amount
            $totalQuantity = $dispatch->quantity ?? 0;

            DcoReceivedChemicals::updateOrCreate(
                ['dispatch_id' => $dispatch->id],
                [
                    'user_id' => Auth::id(),
                    'district_id' => $dispatch->district_id,
                    'request_id'=>$dispatch->request_id,
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
            ->title("Request Received")
            ->send();
    }

    /**
     * Toggle Auditor approval.
     */
    public function toggleAuditorRegionalRM(?int $dispatchId = null): void
    {
        if (Auth::user()->role === 'auditor') {
             $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }

        if ($dispatch->auditor_approved) {
            Notification::make()
                ->warning()
                ->title("Cannot perform request")
                ->body("Request already verified.")
                ->send();
            return;
        }

        $dispatch->auditor_approved = !$dispatch->auditor_approved;
        $dispatch->auditor_approved_by = $dispatch->auditor_approved ? Auth::id() : null;
        $dispatch->auditor_approved_at = $dispatch->auditor_approved ? now() : null;
        $dispatch->save();

        Notification::make()
            ->success()
            ->title("Request Verified")
            ->send();
        }
        elseif(Auth::user()->role === 'regional_manager'){
            $dispatch = $dispatchId ? Dispatch::find($dispatchId) : $this->dispatch;
        if (!$dispatch) {
            return;
        }
        if ($dispatch->regional_manager_approved) {
            Notification::make()
                ->warning()
                ->title("Cannot perform action")
                ->body("You have already confirmed.")
                ->send();
            return;
        }

        $dispatch->regional_manager_approved = !$dispatch->regional_manager_approved;
        $dispatch->regional_manager_approved_by = $dispatch->regional_manager_approved ? Auth::id() : null;
        $dispatch->regional_manager_approved_at = $dispatch->regional_manager_approved ? now() : null;
         $dispatch->save();

        Notification::make()
            ->success()
            ->title("Request Confirmed")
            ->send();
        }else{
            Notification::make()
            ->success()
            ->title("Request Confirmed")
            // ->color('danger')
            ->send();
        }

       
    }



public function saveComment($dispatchId)
{
    $this->validate([
        'commentText' => 'required|string|max:500',
    ]);

    Comment::create([
        'user_id' => Auth::id(),
        'dispatch_id' => $dispatchId,
        'description' => $this->commentText,
    ]);

    $this->commentText = '';

    Notification::make()
        ->success()
        ->title('Comment Added')
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
