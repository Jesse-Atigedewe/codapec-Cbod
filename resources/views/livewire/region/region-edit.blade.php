<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
    <div class="w-full max-w-lg">
        <form wire:submit="submit">
            <div>
                <h2 class="text-2xl font-bold mb-6 text-center">Edit Region</h2>
                {{ $this->form }}
                <div class="mt-6">
                    <x-filament::button type="submit" class="w-full">
                        Update Region
                    </x-filament::button>
                </div>
            </div>
        </form>
    </div>
</div>
