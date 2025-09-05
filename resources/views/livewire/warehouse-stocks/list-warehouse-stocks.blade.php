<div>
    <div class="mb-4 p-4 bg-blue-50 dark:bg-zinc-800 rounded shadow">
        <span class="font-semibold">Total Chemicals Available:</span>
        <span class="text-blue-700 dark:text-blue-300">{{ $this->totalAvailable }}</span>
    </div>
    {{ $this->table }}
    <x-filament-actions::modals />
</div>


