<div class="space-y-6 p-6">
    {{-- Trucks & Drivers --}}
    <section class="grid grid-cols-4 gap-4">
        @foreach($dispatch->drivers ?? [] as $index => $driver)
            <div class="space-y-1 p-2 border rounded">
                <div><strong>Truck:</strong> {{ $driver['vehicle_number'] ?? '—' }}</div>
                <div><strong>Driver:</strong> {{ $driver['driver_name'] ?? '—' }}</div>
                <div><strong>Phone:</strong> {{ $driver['driver_phone'] ?? '—' }}</div>
                <div><strong>License:</strong> {{ $driver['driver_license'] ?? '—' }}</div>
                <div><strong>Qty:</strong> {{ $driver['quantity'] ?? '—' }}</div>

                {{-- Trip Complete Toggle --}}
                  @if(auth()->user()->role === 'dco')
                <button
                    wire:click="toggleDriver({{ $index }})"
                    class="px-3 py-1 rounded-full font-semibold text-white mt-2"
                    style="background-color: {{ $driver['trip_complete'] ? '#16a34a' : '#facc15' }}">
                    {{ $driver['trip_complete'] ? 'Complete' : 'Pending' }}
                </button>
                  @endif
            </div>
        @endforeach
    </section>
    @if ($dispatch->waybill)
    <div class="space-y-2">
        {{-- Preview --}}
        <img 
            src="{{ Storage::disk('public')->url($dispatch->waybill) }}" 
            alt="Waybill" 
            class="max-w-md rounded shadow h-80"
        >

        {{-- Download link --}}
        <a 
            href="{{ Storage::disk('public')->url($dispatch->waybill) }}" 
            download
            class="inline-block px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition"
        >
            Download Waybill
        </a>
    </div>
@endif



    {{-- DCO Approval --}}
    @if(auth()->user()->role === 'dco')
        <div class="mt-4">
            <button
                wire:click="toggleDco"
                class="px-4 py-2 rounded font-semibold text-white"
                style="background-color: {{ $dispatch->dco_approved ? '#16a34a' : '#facc15' }}">
                DCO Approval: {{ $dispatch->dco_approved ? 'Approved' : 'Pending' }}
            </button>
        </div>
    @endif

    {{-- Auditor Approval --}}
    @if(auth()->user()->role === 'auditor')
        <div class="mt-2">
            <button
                wire:click="toggleAuditor"
                class="px-4 py-2 rounded font-semibold text-white"
                style="background-color: {{ $dispatch->auditor_approved ? '#16a34a' : '#facc15' }}">
                Auditor Approval: {{ $dispatch->auditor_approved ? 'Approved' : 'Pending' }}
            </button>
        </div>
    @endif

    {{-- Regional Manager Approval --}}
    @if(auth()->user()->role === 'regional_manager')
        <div class="mt-2">
            <button
                wire:click="toggleRm"
                class="px-4 py-2 rounded font-semibold text-white"
                style="background-color: {{ $dispatch->regional_manager_approved ? '#16a34a' : '#facc15' }}">
                RM Approval: {{ $dispatch->regional_manager_approved ? 'Approved' : 'Pending' }}
            </button>
        </div>
    @endif
</div>
