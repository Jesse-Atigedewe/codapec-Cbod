<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
           @livewire(\App\Livewire\Chemicalstat::class)
           @livewire(\App\Livewire\MembersWidget::class)

    </div>
</x-layouts.app>
    