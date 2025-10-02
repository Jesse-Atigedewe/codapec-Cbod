<div class="space-y-6 p-6">

    {{-- ===========================
        SECTION: Trucks & Drivers
    ============================ --}}
    <div class="p-6 bg-gray-50 dark:bg-zinc-900 rounded-lg">
        @if(!empty($dispatches))
            <div class="grid grid-cols-1 gap-6">
                @foreach($dispatches as $d)
                    <div class="p-4 bg-white dark:bg-zinc-800 rounded-lg shadow">
                        
                        {{-- Dispatch Header --}}
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold">Dispatch #{{ $d->id }}</h3>
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    Chemical: {{ $d->chemical->name ?? '—' }}
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                Status: {{ $d->status ?? '—' }}
                            </div>
                        </div>

                        {{-- Dispatch Details --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">

                            {{-- Driver Details --}}
                            <section class="md:col-span-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        class="space-y-2 p-4 border border-gray-200 dark:border-zinc-700
                                               bg-white dark:bg-zinc-800 rounded-xl shadow-sm
                                               hover:shadow-md transition">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Truck:</strong> {{ $d->vehicle_number ?? '—' }}
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Driver:</strong> {{ $d->driver_name ?? '—' }}
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Phone:</strong> {{ $d->driver_phone ?? '—' }}
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <strong>License:</strong> {{ $d->driver_license ?? '—' }}
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <strong>Qty:</strong> {{ $d->quantity ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- Dispatch Summary & Actions --}}
                            <section class="md:col-span-1">
                                <div
                                    class="space-y-2 p-4 border border-gray-200 dark:border-zinc-700
                                           bg-white dark:bg-zinc-800 rounded-xl shadow-sm">
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Quantity:</strong> {{ $d->chemicalRequest->quantity ?? '—' }}
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Unit:</strong> {{ $d->chemical->unit ?? '—' }}
                                    </div>

                                    {{-- Approval Buttons --}}
                                    <div class="mt-3">
                                        @if(auth()->user()->role === 'dco')
                                            <button
                                                wire:click="toggleDco({{ $d->id }})"
                                                class="px-3 py-1 rounded bg-blue-600 text-white">
                                                DCO
                                            </button>
                                        @endif

                                        @if(auth()->user()->role === 'auditor')
                                            <button
                                                wire:click="toggleAuditor({{ $d->id }})"
                                                class="px-3 py-1 rounded bg-indigo-600 text-white">
                                                Auditor
                                            </button>
                                        @endif

                                        @if(auth()->user()->role === 'regional_manager')
                                            <button
                                                wire:click="toggleRm({{ $d->id }})"
                                                class="px-3 py-1 rounded bg-green-600 text-white">
                                                RM
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- When No Dispatches --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                {{-- Drivers List --}}
                <section class="md:col-span-3">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Drivers</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            class="space-y-2 p-4 border border-gray-200 dark:border-zinc-700
                                   bg-white dark:bg-zinc-800 rounded-xl shadow-sm
                                   hover:shadow-md transition">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Truck:</strong> {{ $dispatch->vehicle_number ?? '—' }}
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Driver:</strong> {{ $dispatch->driver_name ?? '—' }}
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Phone:</strong> {{ $dispatch->driver_phone ?? '—' }}
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>License:</strong> {{ $dispatch->driver_license ?? '—' }}
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Qty:</strong> {{ $dispatch->quantity ?? '—' }}
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Dispatch Summary --}}
                <section class="md:col-span-1">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Dispatch Summary</h2>
                    <div
                        class="space-y-2 p-4 border border-gray-200 dark:border-zinc-700
                               bg-white dark:bg-zinc-800 rounded-xl shadow-sm">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Chemical:</strong> {{ $dispatch->chemical->name ?? '—' }}
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Quantity:</strong> {{ $dispatch->quantity ?? '—' }}
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Unit:</strong> {{ $dispatch->chemical->unit ?? '—' }}
                        </div>
                    </div>
                </section>
            </div>
        @endif
    </div>

    {{-- ===========================
        SECTION: Waybill
    ============================ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between p-6 bg-gray-50 dark:bg-zinc-900 rounded-lg">
        <div>
    @if ($dispatch->waybill)
        <div class="space-y-2">
            {{-- Preview --}}
            <img
                src="{{ asset('storage/' . $dispatch->waybill) }}"
                alt="Waybill"
                class="max-w-md rounded shadow h-80"
            >

            {{-- Download link --}}
            <a
                href="{{ asset('storage/' . $dispatch->waybill) }}"
                download
                class="inline-block px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition"
            >
                Download Waybill
            </a>
        </div>
    @endif
    </div>

    {{-- ===========================
        SECTION: Approvals
    ============================ --}}
    <div>
    @if(empty($dispatches))

        {{-- DCO Approval --}}
        @if(auth()->user()->role === 'dco' && !$dispatch->dco_approved)
            <div class="mt-4">
                <button
                    wire:click="toggleDco()"
                    class="px-4 py-2 rounded font-semibold text-white
                           {{ $dispatch->dco_approved ? 'bg-green-600 hover:bg-green-700'
                                                      : 'bg-yellow-500 hover:bg-yellow-600' }}">
                    DCO Approval: {{ $dispatch->dco_approved ? 'Verified' : 'Verify' }}
                </button>
            </div>
        @endif

        {{-- Auditor Approval --}}
        @if(auth()->user()->role === 'auditor')
            <div class="mt-2">
                <button
                    wire:click="toggleAuditor()"
                    class="px-4 py-2 rounded font-semibold text-white
                           {{ $dispatch->auditor_approved ? 'bg-green-600 hover:bg-green-700'
                                                          : 'bg-yellow-500 hover:bg-yellow-600' }}">
                    Auditor Approval: {{ $dispatch->auditor_approved ? 'Verified' : 'Verify' }}
                </button>
            </div>
        @endif

        {{-- Regional Manager Approval --}}
        @if(auth()->user()->role === 'regional_manager')
            <div class="mt-2">
                <button
                    wire:click="toggleRm()"
                    class="px-4 py-2 rounded font-semibold text-white
                           {{ $dispatch->regional_manager_approved ? 'bg-green-600 hover:bg-green-700'
                                                                   : 'bg-yellow-500 hover:bg-yellow-600' }}">
                    RM Approval: {{ $dispatch->regional_manager_approved ? 'Confirmed' : 'Confirm' }}
                </button>
            </div>
        @endif

    @endif
    </div>
    </div>
</div>
