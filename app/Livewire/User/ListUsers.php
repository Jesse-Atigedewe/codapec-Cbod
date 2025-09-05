<?php

namespace App\Livewire\User;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Str;

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => User::query())
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('role')->sortable()->badge(),
                TextColumn::make('region.name')->label('Region')->sortable()->searchable(),
                TextColumn::make('district.name')->label('District')->sortable()->searchable(),])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'superadmin' => 'Superadmin',
                        'admin' => 'Admin',
                        'codapecrep' => 'CODAPEC Rep',
                        'dco' => 'DCO',
                        'regional_manager' => 'Regional Manager',
                        'auditor' => 'Auditor',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => route(name: 'create-users.create'))
                    ->visible(fn () => Auth::user()->hasRole('superadmin')),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (User $record): string => route('users.edit', $record))
                    ->visible(fn () => Auth::user()->hasRole(['superadmin', 'admin'])),

                DeleteAction::make()
                    ->visible(fn () => Auth::user()->hasRole('superadmin')),
              Action::make('Reset Password')
        // ->icon('heroicon-o-refresh')
        ->requiresConfirmation() // optional confirmation dialog
        ->visible(fn () => Auth::user()->hasRole(['superadmin', 'admin']))
        ->action(function (User $record) {
            // Reset password to a default value
            $randomPassword = \Illuminate\Support\Str::random(8); // or generate dynamically
            $record->password = Hash::make($randomPassword);
            $record->save();

            // Send success notification
            Notification::make()
                ->title('Password Reset')
                ->body("Password for {$record->name} has been reset to '{$randomPassword}'")
                ->success()
                ->send();
        }),
                    ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->hasRole('superadmin')),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.user.list-users');
    }
}
