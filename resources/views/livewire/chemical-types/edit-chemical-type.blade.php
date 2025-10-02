<div class="p-6">
    <h1 class="text-lg font-semibold">Edit Input Type</h1>


     <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-6">
            Save
        </x-filament::button>
    <x-filament-actions::modals />
    </form>
</div>