<div>
    @if($singleWarehouseName)
        <div class="mb-4 p-3 bg-gray-100 rounded">Adding stock to: <strong>{{ $singleWarehouseName }}</strong></div>
    @endif

    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-6">
            Add Stock
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>


