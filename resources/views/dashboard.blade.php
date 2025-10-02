<x-layouts.app :title="__('Dashboard')">
    <div class="grid grid-cols-1 gap-4">
        <div class="col-span-1">
            @livewire(\App\Livewire\CountsWidget::class)
        </div>

        <div class="col-span-1">
            @livewire(\App\Livewire\ItemsByTypeWidget::class)
        </div>

    

        <div class="col-span-1">
            @livewire(\App\Livewire\WarehousesGrouped::class)
        </div>
    </div>
</x-layouts.app>
    