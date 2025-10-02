<div class="space-y-6">
    {{-- Your Warehouses summary --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded p-4">
        <h2 class="text-lg font-semibold">Your warehouses</h2>

        @if(isset($warehouses) && $warehouses->isNotEmpty())
            <div class="mt-4 space-y-4">
                @foreach($warehouses as $warehouse)
                    <div class="border rounded p-3">
                        <div class="flex items-center justify-between">
                            <div class="font-medium">{{ $warehouse->name }}</div>
                            <div class="text-sm text-gray-500">Managed by you</div>
                        </div>

                        {{-- find the matching group for this warehouse --}}
                        @php
                            $group = collect($groups ?? [])->firstWhere('warehouse_id', $warehouse->id);
                        @endphp

                        @if($group)
                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs text-gray-600">
                                            <th class="px-2 py-1">Type</th>
                                            <th class="px-2 py-1">Chemical</th>
                                            <th class="px-2 py-1">State</th>
                                            <th class="px-2 py-1 text-right">Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group['items'] as $item)
                                            <tr class="border-t">
                                                <td class="px-2 py-2">{{ $item['chemical_type'] }}</td>
                                                <td class="px-2 py-2">{{ $item['chemical_name'] }}</td>
                                                <td class="px-2 py-2">{{ $item['chemical_state'] }}</td>
                                                <td class="px-2 py-2 text-right">{{ $item['remaining'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">Total remaining: <strong>{{ $group['total_remaining'] }}</strong></div>
                        @else
                            <div class="mt-2 text-sm text-gray-500">No items in this warehouse yet.</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-2 text-sm text-gray-500">You don't manage any warehouses yet.</div>
        @endif
    </div>

    {{-- Existing Filament table --}}
    <div>
        {{ $this->table }}
    </div>
</div>
