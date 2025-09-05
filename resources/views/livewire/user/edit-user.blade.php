<div>
    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class='mt-6'>
            Submit
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
