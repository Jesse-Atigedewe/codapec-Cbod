<?php

namespace App\Livewire\Requests;

use App\Models\Request;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class ViewRequest extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Request $request;

    public function mount($record): void
    {
        $this->request = Request::with(['chemical', 'cooperative', 'farmers'])->findOrFail($record);
    }

    public function render()
    {
        return view('livewire.requests.view-request');
    }

    public function requestInfoList(Schema $schema): Schema
    {
        return $schema
            ->record($this->request)
            ->components([
                Section::make('Request Information')
                    ->schema([
                        // TextEntry::make('id')->label('Request ID'),
                        TextEntry::make('chemical.name')->label('Chemical'),
                        TextEntry::make('chemical.unit')->label('Unit'),
                        TextEntry::make('cooperative.name')->label('Cooperative'),
                        TextEntry::make('status')->label('Status'),
                    ])->columns(2),

                Section::make(function($record){
                     $total = $record->farmers->sum(fn($f) => $f->pivot->allocated_quantity ?? 0);
    $unit = $record->chemical->unit ?? '';
    return 'Farmers & Allocations (Total: ' . number_format($total, 2) . ' ' . $unit . ')';
                })

                    ->schema([
                        RepeatableEntry::make('farmers')
                            ->label('Allocated Farmers')
                            ->schema([
                                TextEntry::make('farmer_name')->label('Farmer Name'),
                                TextEntry::make('hectares')->label('Farm Size (ha)'),
                                TextEntry::make('pivot.allocated_quantity')->label('Allocated Quantity')
                                    ->formatStateUsing(fn($state) => number_format($state, 4)),
                            ])
                            ->columns(3),
                      
                    ]),


            ]);
    }
}
