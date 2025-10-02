<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Warehouses by Chemical</h2>
        <div class="flex items-center gap-2">
            <div class="text-sm text-gray-600">Expand a warehouse to see its chemicals by type & state</div>
        </div>
    </div>

    @if($groups->isEmpty())
        <div class="text-sm text-gray-500">No data available.</div>
    @else
        @foreach($groups as $group)
            <div x-data="{ open: {{ in_array($group['warehouse_id'], $open) ? 'true' : 'false' }} }" class="mb-4 border rounded-md overflow-hidden">
                <button @click="open = !open; $wire.toggle({{ $group['warehouse_id'] }})" class="w-full text-left p-3 bg-gray-100 dark:bg-gray-800 flex justify-between items-center">
                    <div class="font-medium">{{ $group['warehouse_name'] }} <span class="text-sm text-gray-600">({{ $group['total_remaining'] }})</span></div>
                    <div class="text-sm text-indigo-600">
                        <span x-text="open ? 'Collapse' : 'Expand'"></span>
                    </div>
                </button>

                <div x-show="open" x-transition class="p-3">
                    <div class="mb-2 text-sm text-gray-600">Chemical types and quantities</div>
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="text-left text-sm text-gray-600">
                                <th class="px-2 py-1">Type</th>
                                <th class="px-2 py-1">Chemical</th>
                                <th class="px-2 py-1">State</th>
                                <th class="px-2 py-1">Remaining Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['items'] as $item)
                                <tr class="border-t">
                                    <td class="px-2 py-2">{{ $item['chemical_type'] }}</td>
                                    <td class="px-2 py-2">{{ $item['chemical_name'] }}</td>
                                    <td class="px-2 py-2">{{ $item['chemical_state'] }}</td>
                                    <td class="px-2 py-2">{{ $item['remaining'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
