<?php

namespace App\Livewire\Requests;

use Livewire\Component;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Request;
use App\Models\Farmer;
use App\Models\Cooperative;
use App\Models\Chemical;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CreateRequests extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    // 🆕 Holds allocation results per farmer
    public $allocations = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Create New Request')
                    ->schema([
                        Select::make('cooperative_id')
                            ->label('Select Cooperative')
                            ->options(Cooperative::query()
                                ->when(Auth::user()->district_id, fn($q) =>
                                    $q->where('district_id', Auth::user()->district_id)
                                )
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('chemical_id')
                            ->label('Select Chemical')
                            ->options(Chemical::pluck('name', 'id'))
                            ->searchable()
                            ->required()->live(),

                        Select::make('farmers')
                            ->label('Select Farmers')
                            ->preload()
                            ->multiple()
                            ->options(
                                Farmer::query()
                                    ->when(Auth::user()->district_id, fn($q) =>
                                        $q->where('district_id', Auth::user()->district_id)
                                    )
                                    ->pluck('farmer_name', 'id')
                            )
                            ->searchable()
                            ->required()->live(),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * 🔁 Automatically recalculate allocations
     * whenever chemical or farmers selection changes.
     */
    public function updatedData($value, $key)
    {
        if (in_array($key, ['farmers', 'chemical_id'])) {
            $this->calculateAllocations();
        }
    }

    /**
     * 💡 Compute allocated quantities
     */
    public function calculateAllocations()
    {
        $this->allocations = [];

        if (empty($this->data['chemical_id']) || empty($this->data['farmers'])) {
            return;
        }

        $chemical = Chemical::find($this->data['chemical_id']);
        if (!$chemical || !$chemical->formula_quantity) {
            return;
        }
        
        foreach ($this->data['farmers'] as $farmerId) {
            $farmer = Farmer::find($farmerId);
          

            if ($farmer && $farmer->hectares) {
                $quantity = $farmer->hectares * $chemical->formula_quantity;
                
                $this->allocations[$farmerId] = [
                    'quantity' => $quantity,
                    'unit' => $chemical->unit,
                ];
            }
        }
    }

    public function createRequest(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();
        $request = Request::create([
            'user_id' => $user->id,
            'district_id' => $user->district_id,
            'region_id' => $user->region_id,
            'chemical_id' => $data['chemical_id'],
            'cooperative_id' => $data['cooperative_id'],
            'status' => 'pending',
        ]);

        $request->farmers()->sync($data['farmers']);

        Notification::make()
            ->title('Request Created')
            ->body('Your request has been successfully created and submitted for approval.')
            ->success()
            ->send();

        $this->form->fill();
        $this->allocations = [];
    }

    public function render()
    {
        return view('livewire.requests.create-requests');
    }
}
