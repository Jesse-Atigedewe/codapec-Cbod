<div>
    {{-- The Master doesn't talk, he acts. --}}
<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
        <div class="w-full max-w-lg">
            <form wire:submit.prevent="create">
                <div>
                    <h2 class="text-2xl font-bold mb-6 text-center">Create New Region</h2>
                    {{ $this->form }}
                    <div class="mt-6">
                        <x-filament::button type="submit" class="w-full">
                            Create Region
                        </x-filament::button>
                    </div>
                </div>
            </form>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>
