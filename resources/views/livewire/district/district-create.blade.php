<div>
    {{-- Success is as dangerous as failure. --}}
<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
        <div class="w-full max-w-lg">
            <form wire:submit.prevent="createDistrict">
                <div>
                    <h2 class="text-2xl font-bold mb-6 text-center">Create New District</h2>
                    {{ $this->form }}
                    <div class="mt-6">
                        <x-filament::button type="submit" class="w-full">
                            Create District
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
