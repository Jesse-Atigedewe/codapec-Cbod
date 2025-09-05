<div>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-6">
            Save
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>


