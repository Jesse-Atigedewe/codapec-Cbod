<?php

namespace App\Livewire\Dispatches;

use App\Models\Dispatch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListDispatches extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $user = Auth::user();

    $query = Dispatch::query()
        ->with(['chemicalRequest', 'user', 'region', 'district'])
        ->orderByDesc('created_at');

    if($user->role==='codapecrep'){
        $query->where('user_id',$user->id);
    }elseif($user->role === 'dco') {
        // DCO: filter to their district (and region for extra safety)
        $query->where('district_id', $user->district_id)
              ->where('region_id', $user->region_id);

    } elseif ( $user->role === 'regional_manager' || $user->role === 'auditor') {
        // Regional Manager: filter to their region
        $query->where('region_id', $user->region_id);
    }else {
        // Any other role → return empty result
        $query->whereRaw('1 = 0'); // Always false condition
    }


        return $table
            ->query(fn (): Builder => $query)
            ->columns([
                TextColumn::make('chemicalRequest.chemical.name')->label('Chemical Name'),
                TextColumn::make('quantity')->label('Qty'),
                TextColumn::make('chemicalRequest.chemical.unit')->label('Unit'),
                TextColumn::make('chemicalRequest.user.name')
                    ->label('Requester')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('waybill')->disk('public'),
                TextColumn::make('status')->badge(),
                IconColumn::make('dco_approved')->boolean()->label('DCO'),
                IconColumn::make('auditor_approved')->boolean()->label('Aud'),
                IconColumn::make('regional_manager_approved')->boolean()->label('RM'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn() => Auth::user()->role === 'codapecrep')
                    ->url(fn(): string => route('dispatches.create')),
            ])
        //     ->filters([
        //         SelectFilter::make('status')
        // ->label('Status')
        // ->options([
        //     'pending' => 'Pending',
        //     'delivered' => 'Delivered',
        // ]),
            
            ->recordActions([
                EditAction::make()
                    ->visible(fn() => Auth::user()->role === 'codapecrep')
                    ->url(fn(Dispatch $record): string => route('dispatches.edit', $record)),
                    DeleteAction::make()->visible(fn() => Auth::user()->role === 'codapecrep'),
                  Action::make('view')
                ->url(fn($record) => route('dispatches.info', $record)) // link to Livewire route
                ->icon('heroicon-o-eye')


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);

            
    }


    public function render(): View
    {

        
        return view('livewire.dispatches.list-dispatches');
    }
}
