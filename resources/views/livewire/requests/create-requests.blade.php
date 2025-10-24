<div class="max-w-6xl mx-auto mt-10">
    <form wire:submit.prevent="createRequest" class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Left: Request Form -->
        <div class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary" class="w-full">
                Create Request
            </x-filament::button>

            <x-filament-actions::modals />
        </div>

        <!-- Right: Selected Farmers and Allocations -->
        <div class="bg-gray-50 p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold text-black mb-4">Selected Farmers & Allocations</h2>

            @if (!empty($this->data['farmers']))
                <ul class="divide-y divide-gray-200">
                    @foreach ($this->data['farmers'] as $farmerId)
                        @php $farmer = \App\Models\Farmer::find($farmerId); @endphp
                        @if ($farmer)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <span class="font-medium text-black">{{ $farmer->farmer_name }}</span>
                                    <span class="block text-xs text-gray-500">
                                        {{ $farmer->hectares ?? 0 }} ha
                                    </span>
                                </div>

                                @php $alloc = $this->allocations[$farmerId] ?? null; @endphp
                                <span class="text-sm text-gray-700">
                                    @if ($alloc)
                                        {{ number_format($alloc['quantity'], 2) }} {{ $alloc['unit'] }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm">No farmers selected yet.</p>
            @endif
        </div>
    </form>
</div>
