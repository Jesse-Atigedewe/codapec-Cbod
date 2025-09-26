<div class="p-6">
    <h1 class="text-lg font-semibold">Create Chemical Type</h1>

     <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-6">
            Submit
        </x-filament::button>
    </form>
    <x-filament-actions::modals />
</div>