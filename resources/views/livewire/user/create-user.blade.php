<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
        <div class="w-full max-w-lg">
            <div>
                <h2 class="text-2xl font-bold mb-6 text-center">Create New User</h2>

                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button wire:click="create" type="submit" class="w-full">
                        Create User
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</div>
