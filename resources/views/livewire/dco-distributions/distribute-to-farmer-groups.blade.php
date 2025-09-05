<div>
    <form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-6">
            Distribute
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
