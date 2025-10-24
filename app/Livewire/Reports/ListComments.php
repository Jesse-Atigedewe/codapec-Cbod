<?php

namespace App\Livewire\Reports;

use App\Models\Comment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListComments extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Comment::query()->with(['user','dispatch']))
            ->columns([
                 TextColumn::make('description')
                ->label('Comment')
                ->searchable()
                ->wrap()
                ->limit(50)
                ->tooltip(fn ($record) => $record->description),

            
            TextColumn::make('dispatch.chemical.name')
                ->label('Dispatch / Chemical')
                ->formatStateUsing(fn ($state, $record) =>
                    $state ?? 'Unknown Chemical'
                )
                ->tooltip(fn ($record) =>
                    'Dispatch #' . ($record->dispatch->id ?? 'N/A')
                )
                ->sortable(),

            TextColumn::make('user.name')
                ->label('Commented By')
                ->sortable()
                ->searchable(),

ToggleColumn::make('marked_as_read')
    ->label('Read')
    ->disabled(fn ($record) => $record->marked_as_read)
            
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function render(): View
    {
        return view('livewire.reports.list-comments');
    }
}
