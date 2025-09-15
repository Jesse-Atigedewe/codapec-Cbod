<?php

namespace App\Livewire\Dispatches;

use App\Models\DcoReceivedChemicals;
use App\Models\Dispatch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListDispatches extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Dispatch::query()->with(['chemicalRequest', 'user', 'region', 'district']))
            ->columns([
                TextColumn::make('chemicalRequest.quantity')->label('Qty'),
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
                    DeleteBulkAction::make(),
                ]),
            ]);

            
    }


    public function render(): View
    {

        
        return view('livewire.dispatches.list-dispatches');
    }
}
