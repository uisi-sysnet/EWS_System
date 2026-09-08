@auth
@include('layouts.header')
@include('layouts.topbar')
@include('modals.addLocation')

<div id="editLocationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Location</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the name of the location.</p>
        </div>
        <form id="editLocationForm" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="location_id" id="edit_location_id">
            <div>
                <label for="edit_location_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Location Name <span class="text-munti-red-0">*</span></label>
                <input type="text" name="location_name" id="edit_location_name" required autocomplete="off" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button type="button" id="closeEditLocationModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition">Update Location</button>
            </div>
        </form>
    </div>
</div>

<div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-300">
    <div class="pt-16 md:pt-20 px-4 sm:px-6 lg:px-8">
        <div class="py-6 max-w-screen-2xl mx-auto">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="returnToController()" title="Back to Homepage"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 transition-colors focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 shrink-0 text-prussian-blue dark:text-lavender-grey">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-prussian-blue dark:text-white">Locations & Devices Management</h1>
                        <p class="mt-1 text-sm text-slate-grey dark:text-lavender-grey">View, add, edit and manage beacons & sirens</p>
                    </div>
                </div>
                <button type="button" data-add-location-modal-open
                    class="group relative px-5 py-2.5 font-medium uppercase tracking-wide text-white rounded-md transition-all duration-150 ease-in-out cursor-pointer shadow-[0_6px_0_0_#001845] hover:shadow-[0_3px_0_0_#001845] hover:translate-y-[3px] active:shadow-[0_1px_0_0_#001845] active:translate-y-[5px] bg-regal-navy hover:bg-blue-slate dark:bg-prussian-blue dark:hover:bg-blue-slate overflow-hidden focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800">
                    <span class="relative z-10">Add Location</span>
                </button>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,_2.5fr)_minmax(0,_8.5fr)] gap-6 lg:gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-slate-grey/20 dark:border-blue-slate/30 overflow-hidden flex flex-col h-[calc(100vh-280px)] min-h-[530px]">
                    <div class="px-6 py-5 border-b border-slate-grey/20 dark:border-blue-slate/30 flex items-center justify-between">
                        <h2 class="text-base font-medium text-prussian-blue dark:text-white">Registered Locations</h2>
                        @if ($locations->isNotEmpty())
                        <span class="text-xs text-slate-grey dark:text-lavender-grey">{{ $locations->count() }} location{{ $locations->count() !== 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                    <div class="flex-1 overflow-auto custom-scroll">
                        <table class="min-w-full divide-y divide-slate-grey/20 dark:divide-blue-slate/30">
                            <thead class="bg-slate-grey/5 dark:bg-blue-slate/30 shadow-sm sticky top-0 z-10">
                                <tr class="text-xs">
                                    <th class="px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30 w-28">ID</th>
                                    <th class="px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">LOCATION NAME</th>
                                    <th class="px-6 py-4 text-right font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30 w-32">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-slate-grey/10 dark:divide-blue-slate/30">
                                @forelse ($locations as $location)
                                <tr data-location-id="{{ $location->id }}" class="hover:bg-slate-grey/5 dark:hover:bg-blue-slate/30 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-grey dark:text-lavender-grey">{{ $location->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-prussian-blue dark:text-white">{{ $location->location_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-5">
                                        <button data-id="{{ $location->id }}" data-name="{{ addslashes($location->location_name) }}" class="text-regal-navy dark:text-smart-blue hover:text-blue-slate dark:hover:text-blue-slate transition-colors edit-location-btn">Edit</button>
                                        <!-- <button class="text-munti-red-0 hover:text-munti-red-0 transition-colors">Delete</button> -->
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-20 text-center text-slate-grey dark:text-lavender-grey">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg class="w-16 h-16 mb-5 opacity-50 text-slate-grey dark:text-lavender-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4a2 2 0 00-2 2v3m-4-5H4" />
                                            </svg>
                                            <p class="text-lg font-medium text-prussian-blue dark:text-white">No locations found</p>
                                            <p class="mt-2 text-slate-grey dark:text-lavender-grey">Add a new location to get started</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($locations->isNotEmpty())
                    <div class="px-6 py-3 border-t border-slate-grey/20 dark:border-blue-slate/30 text-xs text-slate-grey dark:text-lavender-grey bg-slate-grey/5 dark:bg-blue-slate/30 text-right">
                        Showing {{ $locations->count() }} location{{ $locations->count() !== 1 ? 's' : '' }}
                    </div>
                    @endif
                </div>
                <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-grey/20 dark:border-blue-slate/30 overflow-hidden flex flex-col h-[calc(100vh-280px)] min-h-[530px]">
                    <div class="flex items-center justify-end gap-4 px-6 pt-2 pb-4 border-b border-slate-grey/20 dark:border-blue-slate/30">
                        <button id="toggleBeaconBtn"
                            class="px-6 py-2 text-center uppercase font-semibold rounded-md transition-all duration-150 ease-in-out cursor-pointer border border-munti-green-1/40 bg-munti-green-1 text-gray-900 border-munti-green-1/70">
                            Beacons
                        </button>
                        <button id="toggleSirenBtn"
                            class="px-6 py-2 text-center uppercase font-semibold rounded-md transition-all duration-150 ease-in-out cursor-pointer border border-gray-600/50 bg-gray-400 text-gray-900 dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500/60">
                            Sirens
                        </button>
                    </div>

                    <!-- Beacon Section -->
                    <div id="beacon-section" class="flex-1 p-5 lg:p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_3fr] gap-5 lg:gap-7 h-full">
                            <!-- Register New Beacon Form -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-grey/20 dark:border-blue-slate/30 flex flex-col h-[410px] overflow-hidden">
                                <div class="px-6 pt-5 pb-3 border-b border-slate-grey/20 dark:border-blue-slate/30 bg-white dark:bg-gray-800 shrink-0">
                                    <h3 class="text-base font-semibold text-prussian-blue dark:text-white tracking-tight">Register New Beacon</h3>
                                    <p class="text-xs text-slate-grey dark:text-lavender-grey mt-0.5">Fill in the details below</p>
                                </div>
                                <div class="flex-1 p-5 overflow-y-auto custom-scroll">
                                    <form id="addBeaconForm" action="{{ route('settings.beacons.store') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label for="beacon_name" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Name <span class="text-munti-red-0">*</span></label>
                                            <input type="text" name="beacon_name" id="beacon_name" required
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="location_id" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1.5">Location <span class="text-munti-red-0">*</span></label>
                                            <div class="relative">
                                                <select name="location_id" id="location_id" required
                                                    class="w-full px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 text-prussian-blue dark:text-lavender-grey border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all duration-200 appearance-none cursor-pointer hover:border-regal-navy dark:hover:border-prussian-blue">
                                                    <option value="" disabled selected class="text-slate-grey dark:text-lavender-grey">Select location</option>
                                                    @foreach ($locations as $loc)
                                                    <option value="{{ $loc->id }}" class="text-prussian-blue dark:text-lavender-grey">{{ $loc->location_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-grey dark:text-lavender-grey">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="oid" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">OID</label>
                                            <input type="text" name="oid" id="oid"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="beacon_id" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Device Serial / Beacon ID</label>
                                            <input type="text" name="beacon_id" id="beacon_id"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="group" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Group / Zone</label>
                                            <input type="text" name="group" id="group"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="latitude" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Latitude <span class="text-slate-grey dark:text-lavender-grey/70">(optional)</span></label>
                                            <input type="number" name="latitude" id="latitude" step="any" min="-90" max="90"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition"
                                                placeholder="e.g. 14.599512" />
                                        </div>
                                        <div>
                                            <label for="longitude" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Longitude <span class="text-slate-grey dark:text-lavender-grey/70">(optional)</span></label>
                                            <input type="number" name="longitude" id="longitude" step="any" min="-180" max="180"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition"
                                                placeholder="e.g. 120.984222" />
                                        </div>
                                        <div class="pt-4">
                                            <button type="submit"
                                                class="w-full py-2.5 px-4 bg-gradient-to-r from-regal-navy to-prussian-blue hover:from-blue-slate hover:to-regal-navy text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg active:scale-[0.98] transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-regal-navy/40 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 disabled:opacity-60">
                                                Save Beacon
                                            </button>
                                        </div>
                                        <button type="button" id="clearBeaconFormBtn"
                                            class="w-full mt-2 py-2 px-4 bg-slate-grey hover:bg-blue-slate text-white text-sm font-medium rounded-lg shadow-md transition">
                                            Clear Form
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Beacons List -->
                            <div class="bg-gradient-to-br from-slate-grey/5 to-white dark:from-blue-slate/20 dark:to-prussian-blue-2 rounded-xl shadow-sm border border-slate-grey/20 dark:border-blue-slate/30 flex flex-col h-[410px] overflow-hidden">
                                <div class="flex-1 overflow-auto custom-scroll">
                                    @if ($beacons->isEmpty())
                                    <div class="flex flex-col items-center justify-center h-full p-8 text-center text-slate-grey dark:text-lavender-grey">
                                        <svg class="w-16 h-16 mb-4 opacity-40 text-slate-grey dark:text-lavender-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-medium text-prussian-blue dark:text-white">No beacons yet</p>
                                        <p class="mt-2 text-slate-grey dark:text-lavender-grey">Create your first beacon using the form.</p>
                                    </div>
                                    @else
                                    <table class="min-w-full divide-y divide-slate-grey/20 dark:divide-blue-slate/30">
                                        <thead class="bg-slate-grey/5 dark:bg-gray-800 sticky top-0">
                                            <tr>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Location</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">OID</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Beacon ID</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Location</th>
                                                <th class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-slate-grey/10 dark:divide-blue-slate/30">
                                            @foreach ($beacons as $beacon)
                                            <tr data-beacon-id="{{ $beacon->id }}" data-location-id="{{ $beacon->location_id }}"
                                                class="hover:bg-slate-grey/5 dark:hover:bg-blue-slate/30 transition-colors cursor-pointer">
                                                <td class="px-6 py-4 whitespace-nowrap font-medium text-prussian-blue dark:text-white">{{ $beacon->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $beacon->location_name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey font-mono">{{ $beacon->oid ?? '—' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey font-mono">{{ $beacon->beacon_id ?? '—' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $beacon->group ?? '—' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right font-medium"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Siren Section (Hidden by default) -->
                    <div id="siren-section" class="flex-1 p-5 lg:p-6 hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_3fr] gap-5 lg:gap-7 h-full">
                            <!-- Register New Siren Form -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-grey/20 dark:border-blue-slate/30 flex flex-col h-[410px] overflow-hidden">
                                <div class="px-6 pt-5 pb-3 border-b border-slate-grey/20 dark:border-blue-slate/30 bg-white dark:bg-gray-800 shrink-0">
                                    <h3 class="text-base font-semibold text-prussian-blue dark:text-white tracking-tight">Register New Siren</h3>
                                    <p class="text-xs text-slate-grey dark:text-lavender-grey mt-0.5">Fill in the details below</p>
                                </div>
                                <div class="flex-1 p-5 overflow-y-auto custom-scroll">
                                    <form id="addSirenForm" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="_method" value="PUT" id="sirenFormMethod">
                                        <div>
                                            <label for="siren_name" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Name <span class="text-munti-red-0">*</span></label>
                                            <input type="text" name="name" id="siren_name" required
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="siren_location_id" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1.5">Location</label>
                                            <div class="relative">
                                                <select name="location_id" id="siren_location_id"
                                                    class="w-full px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 text-prussian-blue dark:text-lavender-grey border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all duration-200 appearance-none cursor-pointer hover:border-regal-navy dark:hover:border-prussian-blue">
                                                    <option value="" selected class="text-slate-grey dark:text-lavender-grey">-- No location --</option>
                                                    @foreach ($locations as $loc)
                                                    <option value="{{ $loc->id }}" class="text-prussian-blue dark:text-lavender-grey">{{ $loc->location_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-grey dark:text-lavender-grey">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="siren_oid" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">OID</label>
                                            <input type="text" name="oid" id="siren_oid"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="siren_siren_id" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Siren ID / Device Serial</label>
                                            <input type="text" name="siren_id" id="siren_siren_id"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="siren_group" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Group / Zone</label>
                                            <input type="text" name="group" id="siren_group"
                                                class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="siren_latitude" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Latitude</label>
                                            <input type="number" name="latitude" id="siren_latitude" step="any" min="-90" max="90"
                                            class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div>
                                            <label for="siren_longitude" class="block text-xs font-medium text-prussian-blue dark:text-lavender-grey mb-1">Longitude</label>
                                            <input type="number" name="longitude" id="siren_longitude" step="any" min="-180" max="180"
                                            class="block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md text-sm text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 focus:border-regal-navy dark:focus:border-prussian-blue focus:ring-1 focus:ring-regal-navy/40 dark:focus:ring-prussian-blue/40 outline-none transition" />
                                        </div>
                                        <div class="pt-4">
                                            <button type="submit"
                                                class="w-full py-2.5 px-4 bg-gradient-to-r from-regal-navy to-prussian-blue hover:from-blue-slate hover:to-regal-navy text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg active:scale-[0.98] transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-regal-navy/40 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 disabled:opacity-60">
                                                Save Siren
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" id="clearSirenFormBtn"
                                                class="w-full py-2 px-4 bg-slate-grey hover:bg-blue-slate text-white text-sm font-medium rounded-lg shadow-md transition">
                                                Clear Form
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Sirens List -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-grey/20 dark:border-blue-slate/30 flex flex-col h-[410px] overflow-hidden">
                                <div class="flex-1 overflow-auto custom-scroll">
                                    <table class="min-w-full divide-y divide-slate-grey/20 dark:divide-blue-slate/30">
                                        <thead class="bg-white dark:bg-gray-800 sticky top-0">
                                            <tr>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Location</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">OID</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Siren ID</th>
                                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Group</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sirens-table-body" class="bg-white dark:bg-gray-800 divide-y divide-slate-grey/10 dark:divide-blue-slate/30">
                                            <tr id="sirens-loading-row">
                                                <td colspan="6" class="px-6 py-8 text-center text-slate-grey dark:text-lavender-grey">
                                                    Loading sirens...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    (function() {
        const beaconSection = document.getElementById('beacon-section');
        const sirenSection = document.getElementById('siren-section');
        const beaconBtn = document.getElementById('toggleBeaconBtn');
        const sirenBtn = document.getElementById('toggleSirenBtn');

        const activeClasses = [
            'bg-munti-green-1', 'text-gray-900',
            'shadow-[0_3px_0_0_#3e630d]',
            'translate-y-[3px]',
            'border-munti-green-1/40',
            'border-munti-green-1/70'
        ];

        const inactiveClasses = [
            'bg-gray-400', 'text-gray-900',
            'shadow-[0_6px_0_0_#4b5563]',
            'translate-y-0',
            'border-gray-600/50',
            'dark:bg-gray-600', 'dark:text-gray-100',
            'dark:border-gray-500/60',
            'dark:shadow-[0_6px_0_0_#374151]'
        ];

        const inactiveHoverClasses = [
            'hover:shadow-[0_3px_0_0_#4b5563]',
            'hover:translate-y-[3px]',
            'hover:bg-gray-300',
            'dark:hover:bg-gray-500',
            'dark:hover:shadow-[0_3px_0_0_#374151]'
        ];

        function removeAllClasses(btn) {
            btn.classList.remove(
                'bg-munti-green-1', 'bg-gray-400',
                'hover:bg-munti-green-1', 'hover:bg-gray-300',
                'dark:bg-gray-600', 'dark:hover:bg-gray-500',

                'text-gray-900', 'dark:text-gray-100',

                'shadow-[0_6px_0_0_#3e630d]', 'shadow-[0_3px_0_0_#3e630d]',
                'shadow-[0_6px_0_0_#4b5563]', 'shadow-[0_3px_0_0_#4b5563]',
                'hover:shadow-[0_3px_0_0_#3e630d]', 'hover:shadow-[0_3px_0_0_#4b5563]',
                'dark:shadow-[0_6px_0_0_#374151]', 'dark:shadow-[0_3px_0_0_#374151]',
                'dark:hover:shadow-[0_3px_0_0_#374151]',

                'translate-y-0', 'translate-y-[3px]',
                'hover:translate-y-[3px]',

                'border-munti-green-1/40', 'border-gray-600/50',
                'border-munti-green-1/70', 'dark:border-gray-500/60'
            );
        }

        function setActiveMode(mode) {
            if (mode === 'beacon') {
                beaconSection.classList.remove('hidden');
                sirenSection.classList.add('hidden');

                removeAllClasses(beaconBtn);
                beaconBtn.classList.add(...activeClasses);

                removeAllClasses(sirenBtn);
                sirenBtn.classList.add(...inactiveClasses, ...inactiveHoverClasses);

            } else {
                beaconSection.classList.add('hidden');
                sirenSection.classList.remove('hidden');

                removeAllClasses(sirenBtn);
                sirenBtn.classList.add(...activeClasses);

                removeAllClasses(beaconBtn);
                beaconBtn.classList.add(...inactiveClasses, ...inactiveHoverClasses);
            }
        }

        beaconBtn.addEventListener('click', () => setActiveMode('beacon'));
        sirenBtn.addEventListener('click', () => setActiveMode('siren'));

        setActiveMode('beacon');






        const addModal = document.getElementById('addLocationModal');
        const addForm = document.getElementById('addLocationForm');

        const openAddModal = () => addModal?.classList.replace('hidden', 'flex');
        const closeAddModal = () => {
            addModal?.classList.replace('flex', 'hidden');
            addForm?.reset();
        };

        document.querySelector('[data-add-location-modal-open]')?.addEventListener('click', openAddModal);
        document.getElementById('closeAddLocationModal')?.addEventListener('click', closeAddModal);
        addModal?.addEventListener('click', e => {
            if (e.target === addModal) closeAddModal();
        });


        if (addForm) {
            addForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';

                try {
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    });

                    const data = await response.json();


                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Location created successfully!'
                        }).then(() => {
                            window.location.reload();
                        });
                        addForm.reset();
                        closeAddModal();
                    } else {
                        let errorMsg = data.message || 'Creation failed.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Network error. Please try again.'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }






        const editModal = document.getElementById('editLocationModal');
        const editForm = document.getElementById('editLocationForm');
        const editIdInput = document.getElementById('edit_location_id');
        const editNameInput = document.getElementById('edit_location_name');
        const closeBtn = document.getElementById('closeEditLocationModal');
        const cancelBtn = closeBtn;

        function closeEditModal() {
            editModal.classList.add('hidden');
        }

        editModal?.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        closeBtn?.addEventListener('click', closeEditModal);

        document.querySelectorAll('.edit-location-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const name = this.dataset.name;

                editIdInput.value = id;
                editNameInput.value = name;

                editForm.action = `/settings/locations/${id}`;

                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            });
        });

        editForm?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Updating...';

            try {
                const formData = new FormData(this);

                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();

                 if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Location updated successfully!'
                    }).then(() => {
                        window.location.reload();
                    });

                    closeEditModal();
                    editForm.reset();
                } else {
                    let errorMsg = data.message || 'Update failed.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error. Please try again.'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    })();

    const beaconForm = document.getElementById('addBeaconForm');

    beaconForm.addEventListener('submit', function(e) {
        let methodField = beaconForm.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            beaconForm.appendChild(methodField);
        }

        if (beaconForm.dataset.editingId) {
            methodField.value = 'PUT';
        } else {
            methodField.value = '';
        }

        console.log('Submitting with _method =', methodField.value);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('sirens-table-body');
        const refreshBtn = document.getElementById('refreshSirensBtn');
        const clearBtn = document.getElementById('clearSirenFormBtn');

        window.sirensData = {};

        async function loadSirens(showLoading = true) {
            if (!tbody) return;
            if (showLoading) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Loading sirens...</td></tr>`;
            }
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.textContent = 'Refreshing...';
            }
            try {
                const response = await fetch('{{ route("settings.api.fetch") }}?path=/api/Data/Sirens&connection_name=Siren', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                tbody.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No sirens found</td></tr>`;
                    return;
                }
                window.sirensData = {};
                data.forEach((siren, index) => {
                    const clean = (value) => {
                        if (value === null || value === undefined) return '—';
                        let str = String(value);
                        str = str.replace(/[^\w\s\/\-\(\)\.]/g, '').trim();
                        return str || '—';
                    };
                    
                    const name = clean(siren.name);
                    const location = clean(siren.location);
                    const oid = clean(siren.oid);
                    const sirenId = clean(siren.sirenId || siren.id);
                    const group = clean(siren.group);
                    
                    // ✅ Get latitude and longitude from API data
                    const latitude = siren.latitude ? clean(siren.latitude.toString()) : '—';
                    const longitude = siren.longitude ? clean(siren.longitude.toString()) : '—';
                    
                    const key = oid !== '—' ? oid : `siren-${index}`;

                    // Get local database values (these override API values if they exist in your DB)
                    const localData = window.sirensLocalData?.[key] || {};
                    const locationId = localData.location_id;
                    const dbLatitude = localData.latitude;     // Get from your database
                    const dbLongitude = localData.longitude;   // Get from your database
                    
                    let locationName = location;
                    if (locationId && window.locationsData && window.locationsData[locationId]) {
                        locationName = window.locationsData[locationId];
                    }

                    // Store in window.sirensData for the form population
                    window.sirensData[key] = {
                        name: name !== '—' ? name : '',
                        location: locationName,
                        oid: oid !== '—' ? oid : '',
                        siren_id: sirenId !== '—' ? sirenId : '',
                        group: group !== '—' ? group : '',
                        latitude: dbLatitude || siren.latitude || '',     // Prefer DB value over API
                        longitude: dbLongitude || siren.longitude || '',  // Prefer DB value over API
                        location_id: locationId,
                    };

                    // ✅ Display latitude and longitude (prefer DB values)
                    const displayLatitude = dbLatitude ? clean(dbLatitude.toString()) : latitude;
                    const displayLongitude = dbLongitude ? clean(dbLongitude.toString()) : longitude;

                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer';
                    row.setAttribute('data-siren-key', key);
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-200">${escapeHtml(name)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">${escapeHtml(locationName)}</td>
                        <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400 max-w-[120px] truncate" title="${escapeHtml(oid)}">
                            ${oid ? escapeHtml(oid).substring(0,8) + '…' : '—'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">${escapeHtml(sirenId)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">${escapeHtml(group)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">${escapeHtml(displayLatitude)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">${escapeHtml(displayLongitude)}</td>
                    `;
                    tbody.appendChild(row);
                });
                
                // ✅ Update table headers to include Latitude and Longitude
                const sirenTable = document.querySelector('#siren-section .min-w-full');
                if (sirenTable) {
                    const thead = sirenTable.querySelector('thead');
                    if (thead) {
                        thead.innerHTML = `
                            <tr>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">OID</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Siren ID</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Group</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Latitude</th>
                                <th class="px-6 py-3.5 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider">Longitude</th>
                            </tr>
                        `;
                    }
                }
                
                attachSirenRowClick();
            } catch (error) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-munti-red-0 dark:text-munti-red-0">Unable to load the Siren API. Please reach out to your administrator for assistance.</td></tr>`;
            } finally {
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.textContent = 'Refresh';
                }
            }
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '—';
            if (typeof unsafe !== 'string') unsafe = String(unsafe);
            if (unsafe === '—') return unsafe;
            return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }



        function populateSirenForm(sirenData, key) {
            const form = document.getElementById('addSirenForm');
            const methodInput = document.getElementById('sirenFormMethod');
            const nameField = document.getElementById('siren_name');
            const oidField = document.getElementById('siren_oid');
            const sirenIdField = document.getElementById('siren_siren_id');
            const groupField = document.getElementById('siren_group');
            const latField = document.getElementById('siren_latitude');
            const lngField = document.getElementById('siren_longitude');
            const locationSelect = document.getElementById('siren_location_id');
            if (sirenData.location_id) {
                locationSelect.value = sirenData.location_id;
            } else {
                locationSelect.value = '';
            }

            nameField.value = sirenData.name || '';
            oidField.value = sirenData.oid || '';
            sirenIdField.value = sirenData.siren_id || '';
            groupField.value = sirenData.group || '';
            latField.value = sirenData.latitude || '';
            lngField.value = sirenData.longitude || '';

            if (sirenData.location_id) {
                locationSelect.value = sirenData.location_id;
            } else {
                const option = Array.from(locationSelect.options).find(opt => opt.text.trim() === sirenData.location);
                if (option) locationSelect.value = option.value;
                else locationSelect.value = '';
            }

            nameField.readOnly = false;
            oidField.readOnly = false;
            sirenIdField.readOnly = false;
            groupField.readOnly = false;
            latField.readOnly = false;
            lngField.readOnly = false;

            form.action = `{{ url('settings/sirens') }}/${encodeURIComponent(sirenData.oid)}`;
            methodInput.value = 'PUT';

            form.dataset.editingOid = sirenData.oid;

            document.querySelectorAll('#sirens-table-body tr.selected-row').forEach(r => {
                r.classList.remove('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
            });
            const row = document.querySelector(`#sirens-table-body tr[data-siren-key="${key}"]`);
            if (row) row.classList.add('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        document.getElementById('clearSirenFormBtn').addEventListener('click', clearSirenForm);

        document.getElementById('addSirenForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" ...></svg> Saving...';

            try {
                const formData = new FormData(form);
                if (form.querySelector('input[name="_method"]')?.value === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Siren saved successfully!'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Save failed');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        });

        function attachSirenRowClick() {
            if (!tbody) return;
            tbody.removeEventListener('click', handleSirenRowClick);
            tbody.addEventListener('click', handleSirenRowClick);
        }

        function handleSirenRowClick(e) {
            const row = e.target.closest('tr[data-siren-key]');
            if (!row) return;
            const key = row.dataset.sirenKey;
            const sirenData = window.sirensData[key];
            if (!sirenData) return;
            populateSirenForm(sirenData);
            document.querySelectorAll('#sirens-table-body tr.selected-row').forEach(r => {
                r.classList.remove('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
            });
            row.classList.add('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                document.getElementById('addSirenForm').reset();
                document.getElementById('siren_name').readOnly = false;
                document.getElementById('siren_oid').readOnly = false;
                document.getElementById('siren_siren_id').readOnly = false;
                document.getElementById('siren_group').readOnly = false;
                document.querySelectorAll('#sirens-table-body tr.selected-row').forEach(r => {
                    r.classList.remove('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
                });
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => loadSirens(false));
        }

        loadSirens(true);

        function clearSirenForm() {
            const form = document.getElementById('addSirenForm');
            form.reset();
            form.action = '';
            document.getElementById('sirenFormMethod').value = 'POST';
            delete form.dataset.editingOid;

            document.querySelectorAll('#addSirenForm input, #addSirenForm select').forEach(field => {
                field.readOnly = false;
            });

            document.querySelectorAll('#sirens-table-body tr.selected-row').forEach(r => {
                r.classList.remove('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
            });
        }

        async function refreshBeaconTable() {
            try {
                const response = await fetch('/settings/beacons/list');
                const beacons = await response.json();
                window.beaconsData = beacons;
                renderBeaconTable(beacons);
            } catch (error) {
                console.error('Failed to refresh beacons', error);
            }
        }

        function renderBeaconTable(beacons) {
            const tbody = document.querySelector('#beacon-section tbody');
            if (!tbody) return;
            let html = '';
            beacons.forEach(beacon => {
                html += `<tr data-beacon-id="${beacon.id}" data-location-id="${beacon.location_id}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer">
            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">${escapeHtml(beacon.name)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">${escapeHtml(beacon.location_name)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300 font-mono">${escapeHtml(beacon.oid || '—')}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300 font-mono">${escapeHtml(beacon.beacon_id || '—')}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">${escapeHtml(beacon.group || '—')}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right font-medium"></td>
        </tr>`;
            });
            tbody.innerHTML = html;
            attachBeaconRowClick();
        }

        const beaconForm = document.getElementById('addBeaconForm');

        beaconForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" ...></svg> Saving...';

            try {
                const formData = new FormData(form);
                if (form.querySelector('input[name="_method"]')?.value === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Beacon created successfully!'
                    }).then(() => {
                        window.location.reload();
                    });

                } else {
                    throw new Error(data.message || 'Save failed');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    const Alert = Swal.mixin({});

    window.beaconsData = <?= json_encode($beacons->map(function ($beacon) {
        return [
            'id'          => $beacon->id,
            'name'        => $beacon->name,
            'location_id' => $beacon->location_id,
            'oid'         => $beacon->oid,
            'beacon_id'   => $beacon->beacon_id,
            'group'       => $beacon->group,
            'latitude'    => $beacon->latitude,
            'longitude'   => $beacon->longitude,
        ];
    })) ?>;

    window.locationsData = <?= json_encode($locations->mapWithKeys(function ($loc) {
        return [$loc->id => $loc->location_name];
    })) ?>;

    window.sirensLocalData = <?= json_encode(
        $sirens->mapWithKeys(function ($siren) {
            return [$siren->oid => [
                'location_id' => $siren->location_id,
                'latitude' => $siren->latitude,  
                'longitude' => $siren->longitude   
            ]];
        })
    ) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const beaconRows = document.querySelectorAll('tbody tr[data-beacon-id]');
        const nameInput = document.getElementById('beacon_name');
        const locationSelect = document.getElementById('location_id');
        const oidInput = document.getElementById('oid');
        const beaconIdInput = document.getElementById('beacon_id');
        const groupInput = document.getElementById('group');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        function setSelectValue(select, value) {
            const option = Array.from(select.options).find(opt => opt.value == value);
            if (option) select.value = value;
            else select.value = '';
        }

        const beaconForm = document.getElementById('addBeaconForm');
        const beaconSubmitBtn = beaconForm.querySelector('button[type="submit"]');
        const originalFormAction = beaconForm.action;
        const originalSubmitText = beaconSubmitBtn.innerText;

        let methodField = document.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            beaconForm.appendChild(methodField);
        }

        window.resetBeaconForm = function() {
            beaconForm.reset();
            beaconForm.action = originalFormAction;
            methodField.value = '';
            beaconSubmitBtn.innerText = originalSubmitText;

            delete beaconForm.dataset.editingId;
        };

        document.getElementById('clearBeaconFormBtn')?.addEventListener('click', window.resetBeaconForm);

        beaconRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('button')) return;

                const beaconId = this.dataset.beaconId;
                const beacon = window.beaconsData.find(b => b.id == beaconId);
                if (!beacon) return;

                document.getElementById('beacon_name').value = beacon.name || '';
                setSelectValue(document.getElementById('location_id'), beacon.location_id);
                document.getElementById('oid').value = beacon.oid || '';
                document.getElementById('beacon_id').value = beacon.beacon_id || '';
                document.getElementById('group').value = beacon.group || '';
                document.getElementById('latitude').value = beacon.latitude || '';
                document.getElementById('longitude').value = beacon.longitude || '';

                beaconForm.action = `/settings/beacons/${beaconId}`;
                methodField.value = 'PUT';
                beaconSubmitBtn.innerText = 'Update Beacon';
                beaconForm.dataset.editingId = beaconId;

                this.classList.add('selected-row', 'bg-blue-50', 'dark:bg-blue-900/20');
            });
        });
    });
</script>
@else
<script>
    window.location = "{{ route('login') }}";
</script>
@endauth








