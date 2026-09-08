@auth
@include('layouts.header')
@include('layouts.topbar')

<div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-300 overflow-y-auto custom-scroll" style="max-height: calc(100vh - 100px);">
    <div class="pt-16 md:pt-20 px-4 sm:px-6 lg:px-8">
        <div class="py-6 max-w-screen-2xl mx-auto">

            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="returnToController()"
                        title="Back to Homepage"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 text-prussian-blue dark:text-lavender-grey transition-colors focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 dark:focus:ring-offset-prussian-blue-3 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>

                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-prussian-blue dark:text-white">Beacon Status Statistics</h1>
                        <p class="mt-1 text-sm text-slate-grey dark:text-lavender-grey">
                            View Statistics
                        </p>
                    </div>
                </div>
            </div>

            <!-- Offline Beacons Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-munti-red-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Currently Offline Beacons
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ count($offlineBeacons) }} beacon{{ count($offlineBeacons) != 1 ? 's' : '' }})
                            </span>
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Beacons that are currently offline and how long they've been disconnected
                        </p>
                    </div>
                    <button onclick="refreshOfflineList()" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>

                @if(count($offlineBeacons) > 0)
                    <!-- Scrollable Table Container -->
                    <div class="overflow-x-auto custom-scroll" style="max-height: 500px; overflow-y: auto;">
                        <div class="min-w-full">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">
                                            Beacon Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">
                                            Location
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">
                                            Offline Since
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">
                                            Duration
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($offlineBeacons as $beacon)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $beacon['name'] }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    ID: {{ $beacon['beacon_id'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $beacon['location'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $beacon['offline_since']->format('M d, Y H:i:s') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $durationSeconds = $beacon['duration_raw'];
                                                        $severityClass = '';
                                                        if ($durationSeconds > 86400) { // > 24 hours
                                                            $severityClass = 'text-munti-red-0 dark:text-munti-red-0 font-semibold';
                                                        } elseif ($durationSeconds > 3600) { // > 1 hour
                                                            $severityClass = 'text-munti-yellow-0 dark:text-munti-yellow-0';
                                                        } elseif ($durationSeconds > 300) { // > 5 minutes
                                                            $severityClass = 'text-munti-yellow-0 dark:text-munti-yellow-0';
                                                        } else {
                                                            $severityClass = 'text-gray-600 dark:text-gray-400';
                                                        }
                                                    @endphp
                                                    <span class="text-sm {{ $severityClass }}">
                                                        {{ $beacon['duration'] }}
                                                    </span>
                                                    @if($durationSeconds > 86400)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-munti-red-0/30 text-munti-red-0 dark:bg-munti-red-0 dark:text-munti-red-0">
                                                            Critical
                                                        </span>
                                                    @elseif($durationSeconds > 3600)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-munti-yellow-0/30 text-munti-yellow-0 dark:bg-munti-yellow-0 dark:text-munti-yellow-0">
                                                            Warning
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-munti-red-0/30 text-munti-red-0 dark:bg-munti-red-0 dark:text-munti-red-0">
                                                    <span class="w-1.5 h-1.5 bg-munti-red-0 dark:bg-munti-red-0 rounded-full mr-1.5"></span>
                                                    Offline
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Offline</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($offlineBeacons) }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Critical (>24h)</div>
                            <div class="text-2xl font-bold text-munti-red-0 dark:text-munti-red-0">
                                {{ collect($offlineBeacons)->filter(fn($b) => $b['duration_raw'] > 86400)->count() }}
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Warning (1-24h)</div>
                            <div class="text-2xl font-bold text-munti-yellow-0 dark:text-munti-yellow-0">
                                {{ collect($offlineBeacons)->filter(fn($b) => $b['duration_raw'] > 3600 && $b['duration_raw'] <= 86400)->count() }}
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Recent (<1h)</div>
                            <div class="text-2xl font-bold text-munti-yellow-0 dark:text-munti-yellow-0">
                                {{ collect($offlineBeacons)->filter(fn($b) => $b['duration_raw'] <= 3600)->count() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-munti-green-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">All Beacons Online</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Great! All beacons are currently online and functioning properly.</p>
                    </div>
                @endif
            </div>

            <!-- Time Range Selector with Dropdown -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-2 p-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="interval-info">Loading...</p>
                    </div>
                    <div class="relative">
                        <select id="range-select" class="appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-48 p-2.5 cursor-pointer">
                            <option value="last_3_hours">Last 3 Hours</option>
                            <option value="last_6_hours">Last 6 Hours</option>
                            <option value="last_12_hours">Last 12 Hours</option>
                            <option value="last_24_hours">Last 24 Hours</option>
                            <option value="last_week">Last Week</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="last_year">Last Year</option>
                            <option value="last_10_years">Last 10 Years</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
                <div id="status-chart" class="w-full h-[400px]"></div>
            </div>

        </div>
    </div>
</div>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    let chart = null;
    let currentRange = 'last_3_hours';

    // Function to load chart data
    async function loadChartData(range) {
        try {
            const response = await fetch(`{{ route('settings.statistics.chart-data') }}?range=${range}`);
            const data = await response.json();
            
            // Update interval info text
            const intervalInfo = document.getElementById('interval-info');
            if (data.interval_minutes) {
                if (data.interval_minutes >= 60) {
                    if (data.interval_minutes >= 1440) {
                        if (data.interval_minutes >= 525600) {
                            const years = data.interval_minutes / 525600;
                            intervalInfo.textContent = `Showing ${data.categories.length} intervals (every ${years} ${years === 1 ? 'year' : 'years'})`;
                        } else if (data.interval_minutes >= 43200) {
                            const months = data.interval_minutes / 43200;
                            intervalInfo.textContent = `Showing ${data.categories.length} intervals (every ${months} ${months === 1 ? 'month' : 'months'})`;
                        } else {
                            const days = data.interval_minutes / 1440;
                            intervalInfo.textContent = `Showing ${data.categories.length} intervals (every ${days} ${days === 1 ? 'day' : 'days'})`;
                        }
                    } else {
                        const hours = data.interval_minutes / 60;
                        intervalInfo.textContent = `Showing ${data.categories.length} intervals (every ${hours} ${hours === 1 ? 'hour' : 'hours'})`;
                    }
                } else {
                    intervalInfo.textContent = `Showing ${data.categories.length} intervals (every ${data.interval_minutes} minutes)`;
                }
            }
            
            const options = {
                series: [
                    {
                        name: 'Online Beacons',
                        data: data.online,
                        color: '#84cc16' // Green
                    },
                    {
                        name: 'Offline Beacons',
                        data: data.offline,
                        color: '#e2261b' // Red
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: false,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    },
                    background: 'transparent'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%',
                        borderRadius: 4,
                        borderRadiusApplication: 'end',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        fontSize: '11px',
                        colors: [document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0']
                    },
                    formatter: function(val) {
                        return val > 0 ? val : '';
                    }
                },
                title: {
                    text: `Beacon Status Overview (${range.replace(/_/g, ' ').toUpperCase()})`,
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold',
                        color: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0'
                    }
                },
                xaxis: {
                    categories: data.categories,
                    title: {
                        text: `Time Period`,
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0'
                        }
                    },
                    labels: {
                        rotate: -45,
                        rotateAlways: false,
                        style: {
                            fontSize: '10px',
                            colors: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0'
                        },
                        formatter: function(value) {
                            return value;
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Beacons',
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0'
                        }
                    },
                    min: 0,
                    tickAmount: 10,
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0'
                        },
                        formatter: function(val) {
                            return Math.floor(val);
                        }
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(value, { seriesIndex }) {
                            const seriesName = seriesIndex === 0 ? 'Online' : 'Offline';
                            return `${seriesName}: ${value} beacon${value !== 1 ? 's' : ''}`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    labels: {
                        colors: document.documentElement.classList.contains('dark') ? '#5c677dff' : '#e0e0e0',
                        useSeriesColors: false
                    },
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 2
                    }
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#e0e0e0' : '#5c677dff',
                    strokeDashArray: 5,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['transparent']
                },
                fill: {
                    opacity: 1,
                    colors: ['#84cc16', '#e2261b']
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        plotOptions: {
                            bar: {
                                columnWidth: '80%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        }
                    }
                }]
            };
            
            if (chart) {
                chart.updateOptions({
                    xaxis: {
                        categories: data.categories
                    },
                    series: options.series,
                    title: {
                        text: `Beacon Status Overview (${range.replace(/_/g, ' ').toUpperCase()})`
                    }
                });
            } else {
                chart = new ApexCharts(document.querySelector("#status-chart"), options);
                chart.render();
            }
        } catch (error) {
            console.error('Error loading chart data:', error);
        }
    }

    // Handle range select change
    const rangeSelect = document.getElementById('range-select');
    rangeSelect.addEventListener('change', async function() {
        const range = this.value;
        currentRange = range;
        await loadChartData(range);
    });
    
    // Initial load
    loadChartData('last_3_hours');
    
    // Auto-refresh every 30 seconds (optional)
    setInterval(() => {
        loadChartData(currentRange);
    }, 30000);
    
    // Handle dark mode changes
    const observer = new MutationObserver(() => {
        if (chart) {
            loadChartData(currentRange);
        }
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
</script>

@include('layouts.footer')
@else
<script>window.location = "{{ route('login') }}";</script>
@endauth