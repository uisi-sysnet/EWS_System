@auth
@include('layouts.header')
@include('layouts.topbar')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-300">
    <div class="pt-16 md:pt-20 px-4 sm:px-6 lg:px-8">
        <div class="py-6 max-w-screen-2xl mx-auto">

            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="returnToController()" title="Back to Homepage"
                        class="inline-flex size-10 items-center justify-center rounded-full bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 transition focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 shrink-0 text-prussian-blue dark:text-lavender-grey">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-prussian-blue dark:text-white">IOT logs</h1>
                        <p class="mt-1 text-sm text-slate-grey dark:text-lavender-grey">View all recorded events and activities from master controller</p>
                    </div>
                </div>
            </div>

            <div class="mb-4 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm ring-1 ring-slate-grey/20 dark:ring-blue-slate/30">
                <form method="GET" action="{{ route('settings.logs') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[150px]">
                        <label for="date_from" class="block font-medium uppercase tracking-wider text-slate-grey dark:text-lavender-grey mb-1">From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            min="{{ $minDate ? $minDate->format('Y-m-d') : '' }}"
                            max="{{ $maxDate ? $maxDate->format('Y-m-d') : now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue text-prussian-blue dark:text-lavender-grey">
                        @if($minDate)
                        <p class="hidden text-slate-grey mt-1">Earliest: {{ $minDate->format('Y-m-d') }}</p>
                        @endif
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="date_to" class="block font-medium uppercase tracking-wider text-slate-grey dark:text-lavender-grey mb-1">To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            min="{{ $minDate ? $minDate->format('Y-m-d') : '' }}"
                            max="{{ $maxDate ? $maxDate->format('Y-m-d') : now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue text-prussian-blue dark:text-lavender-grey">
                        @if($maxDate)
                        <p class="hidden text-slate-grey mt-1">Latest: {{ $maxDate->format('Y-m-d') }}</p>
                        @endif
                    </div>

                    <div class="flex-1 min-w-[130px]">
                        <label for="type" class="block font-medium uppercase tracking-wider text-slate-grey dark:text-lavender-grey mb-1">Type</label>
                        <select name="type" id="type"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue text-prussian-blue dark:text-lavender-grey">
                            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }} class="text-prussian-blue dark:text-lavender-grey">All types</option>
                            <option value="info" {{ request('type') == 'info' ? 'selected' : '' }} class="text-prussian-blue dark:text-lavender-grey">Info</option>
                            <option value="error" {{ request('type') == 'error' ? 'selected' : '' }} class="text-prussian-blue dark:text-lavender-grey">Error</option>
                            <option value="success" {{ request('type') == 'success' ? 'selected' : '' }} class="text-prussian-blue dark:text-lavender-grey">Success</option>
                            <option value="data" {{ request('type') == 'data' ? 'selected' : '' }} class="text-prussian-blue dark:text-lavender-grey">Data</option>
                        </select>
                    </div>

                    <div class="flex-[2] min-w-[200px]">
                        <label for="search" class="block font-medium uppercase tracking-wider text-slate-grey dark:text-lavender-grey mb-1">Search message</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Keywords..."
                            class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-lg focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-regal-navy dark:bg-prussian-blue hover:bg-blue-slate dark:hover:bg-blue-slate text-white font-medium rounded-lg transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('settings.logs') }}"
                            class="px-4 py-2 bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 text-prussian-blue dark:text-lavender-grey font-medium rounded-lg transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl ring-1 ring-slate-grey/20 dark:ring-blue-slate/30 rounded-2xl overflow-hidden h-[calc(100vh-320px)] flex flex-col min-h-[430px]">
                <div class="overflow-hidden relative h-full flex flex-col">
                    <table class="w-full text-left text-slate-grey dark:text-lavender-grey table-fixed">
                        <thead class="uppercase bg-slate-grey/5 dark:bg-blue-slate/30 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium text-slate-grey dark:text-lavender-grey w-[80px]">#</th>
                                <th scope="col" class="px-6 py-3 font-medium text-slate-grey dark:text-lavender-grey w-[200px]">User</th>
                                <th scope="col" class="px-6 py-3 font-medium text-slate-grey dark:text-lavender-grey w-[200px]">Date & Time</th>
                                <th scope="col" class="px-6 py-3 font-medium text-slate-grey dark:text-lavender-grey w-[200px]">Type</th>
                                <th scope="col" class="px-6 py-3 font-medium text-slate-grey dark:text-lavender-grey">Message</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="overflow-y-auto flex-1 custom-scroll">
                        <table class="w-full text-left text-slate-grey dark:text-lavender-grey table-fixed">
                            <tbody class="divide-y divide-slate-grey/20 dark:divide-blue-slate/30">
                                @forelse($logs as $log)
                                <tr class="hover:bg-slate-grey/5 dark:hover:bg-blue-slate/30 transition">
                                    <td class="px-6 py-2 font-mono text-prussian-blue dark:text-lavender-grey truncate w-[80px]">{{ $log->id }}</td>
                                    <td class="px-6 py-2 w-[200px]">
                                        @if($log->user)
                                        <span class="font-medium text-prussian-blue dark:text-white truncate block">{{ $log->user->first_name }} {{ $log->user->last_name }}</span>
                                        <span class="text-xs text-slate-grey dark:text-lavender-grey block truncate">{{ ucfirst($log->user->user_level) }}</span>
                                        @else
                                        <span class="text-slate-grey dark:text-lavender-grey/70 italic block truncate">System / Deleted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap w-[200px]">
                                        <span class="font-mono text-prussian-blue dark:text-lavender-grey block truncate">{{ $log->datetime->format('Y-m-d H:i:s') }}</span>
                                        <span class="text-slate-grey dark:text-lavender-grey/70 block truncate">{{ $log->datetime->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-2 w-[200px]">
                                        @php
                                        $typeClasses = [
                                        'info' => 'bg-yellow-100/10 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'error' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'data' => 'bg-blue-100/10 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        ];
                                        $class = $typeClasses[$log->type] ?? 'bg-slate-grey/10 text-slate-grey dark:bg-blue-slate/30 dark:text-lavender-grey';
                                        @endphp
                                        <span class="px-2 py-1 rounded-full font-medium {{ $class }} inline-block truncate max-w-full">
                                            {{ ucfirst($log->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-2 break-words text-prussian-blue dark:text-lavender-grey">
                                        <span class="block break-words">{{ $log->message }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-grey dark:text-lavender-grey">
                                        <svg class="w-12 h-12 mx-auto mb-3 opacity-20 text-slate-grey dark:text-lavender-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium text-prussian-blue dark:text-white">No logs found</p>
                                        <p class="text-slate-grey dark:text-lavender-grey/70 mt-1">Try adjusting your filters or check back later.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4 py-3 bg-white dark:bg-gray-800 border-t border-slate-grey/20 dark:border-blue-slate/30 rounded-b-2xl">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
    @else
    <script>
        window.location = "{{ route('login') }}";
    </script>
    @endauth