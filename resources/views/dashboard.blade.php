<x-layouts.app :title="__('Dashboard')">
    <div class="grid grid-cols-1 gap-4">
        <div class="col-span-1">
            @livewire(\App\Livewire\CountsWidget::class)
        </div>

        @if(auth()->user()->hasRole('codapecrep'))
        <div class="col-span-1">
            @livewire(\App\Livewire\ItemsByTypeWidget::class)
        </div>

        @endif

        @if(auth()->user()->hasRole('regional_manager'))
        <div class="col-span-1">
            @livewire(\App\Livewire\ChemicalByTypeInRegion::class)
        </div>
        @endif

        @if(auth()->user()->hasRole('admin'))
        <div class="col-span-1">
            @livewire(\App\Livewire\WarehousesGrouped::class)
        </div>
        @endif
    </div>
</x-layouts.app>