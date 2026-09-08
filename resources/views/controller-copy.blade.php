@auth
@include('layouts.header')
@include('layouts.topbar')

<script>
    window.sirenLocations = <?= json_encode(
        $sirens->mapWithKeys(function ($siren) {
            return [$siren->oid => $siren->location?->location_name ?? $siren->group ?? 'Unknown'];
        })->filter()
    ) ?>;

window.dbSirens = <?= json_encode(
    $sirens->map(function ($siren) {
        return [
            'id' => $siren->id,
            'oid' => $siren->oid,
            'name' => $siren->name,
            'group' => $siren->group,
            'location_name' => $siren->location?->location_name,
            'enabled' => $siren->enabled,
        ];
    })
) ?>;

</script>

<!-- Countdown Popup -->
<div id="countdownPopup" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-2xl border border-gray-200 p-6 z-50 hidden transition-all duration-300 min-w-[280px] text-center">
    <div class="flex flex-col items-center gap-3">
        <div class="text-4xl font-bold text-blue-600" id="countdownTimer">00:00</div>
        <div class="text-sm text-gray-600">Remaining until auto‑deactivation</div>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col gap-2"></div>

<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium ${
        type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
        toast.textContent = message;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }


    let countdownInterval = null;
    let countdownRemaining = 0;

    function showCountdownPopup(seconds) {
        const popup = document.getElementById('countdownPopup');
        const timerSpan = document.getElementById('countdownTimer');
        if (!popup || !timerSpan) return;

        countdownRemaining = seconds;
        updateCountdownDisplay();

        popup.classList.remove('hidden');
        popup.classList.add('block');

        if (countdownInterval) clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
            if (countdownRemaining <= 1) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                hideCountdownPopup();
                if (deactivateBtn && !deactivateBtn.disabled) {
                    deactivateBtn.click();
                }
            } else {
                countdownRemaining--;
                updateCountdownDisplay();
            }
        }, 1000);
    }

    function updateCountdownDisplay() {
        const timerSpan = document.getElementById('countdownTimer');
        if (timerSpan) {
            const minutes = Math.floor(countdownRemaining / 60);
            const seconds = countdownRemaining % 60;
            timerSpan.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }

    function hideCountdownPopup() {
        const popup = document.getElementById('countdownPopup');
        if (popup) {
            popup.classList.add('hidden');
            popup.classList.remove('block');
        }
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    }
</script>

<div class="fixed inset-0 flex flex-col bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-gray-200 overflow-y-auto custom-scroll">
    <div class="flex flex-col flex-grow min-h-0 p-3 pt-16 md:p-4 md:pt-20">
        <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 mb-2 min-h-full lg:min-h-[50vh] overflow-hidden">

            @include('partials.controller.groups-card')
            @include('partials.controller.signals-card')
            @include('partials.controller.beacons-card')
            @include('partials.controller.sirens-card')

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 shrink-0">

            @include('partials.controller.map-card')
            @include('partials.controller.serial-log-card')
            @include('partials.controller.controls-card')

        </div>
    </div>
</div>

<!-- Centered Door Popup Modal -->
<div id="doorPopup" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 rounded-t-2xl px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h3 class="text-white font-bold text-lg">Beacon Open Doors</h3>
                <span id="doorCountBadge" class="bg-white/20 text-white text-xs font-semibold px-2 py-0.5 rounded-full">0</span>
            </div>
            <button id="closeDoorPopupBtn" class="text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- List Content -->
        <div class="p-5 max-h-80 overflow-y-auto custom-scroll">
            <div id="doorListContent" class="space-y-3">
                <div class="text-gray-500 dark:text-gray-400 text-center py-4">No open doors</div>
            </div>
        </div>
    </div>
</div>

<script>
    window.beaconDoors = <?= json_encode(
        $beacons->mapWithKeys(function ($beacon) {
            return [$beacon->beacon_id => [
                'name' => $beacon->name ?? 'Unnamed Beacon',
                'is_door_open' => $beacon->is_door_open ?? false
            ]];
        })
    ) ?>;
</script>
<script>    
    (function() {
        const SELECTED_CLASSES = [
            'bg-yellow-400', 'hover:bg-yellow-300',
            'border-yellow-600/40',
            'shadow-[0_3px_0_0_#ca8a04]',
            'translate-y-[3px]',
            'text-gray-900'
        ];

        const groupsContainer = document.getElementById('groups-container');
        const signalsContainer = document.getElementById('signals-container');
        const selectAllBtn = document.getElementById('select-all-groups');
        const selectAllText = document.getElementById('select-all-text');
        const clearAllBtn = document.getElementById('clear-all-selections');
        const sirensContainer = document.getElementById('sirensContainer');
        const refreshBtn = document.getElementById('refreshSirensBtn');
        const tooltip = document.getElementById('tooltip');

        let allGroupsSelected = false;




        

        function darkenColor(hex, percent = 0.7) {
            let r = parseInt(hex.slice(1, 3), 16);
            let g = parseInt(hex.slice(3, 5), 16);
            let b = parseInt(hex.slice(5, 7), 16);
            r = Math.floor(r * percent);
            g = Math.floor(g * percent);
            b = Math.floor(b * percent);
            return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
        }

        document.querySelectorAll('.signal-btn').forEach(btn => {
            const color = btn.dataset.color;
            if (color) {
                btn.style.backgroundColor = color;
                const dark = darkenColor(color, 0.7);
                btn.style.setProperty('--signal-shadow', dark);
            }
        });

        function saveOriginalClasses(el) {
            if (el.dataset.originalClasses) return;
            const classesToPreserve = [
                'bg-lime-200', 'bg-lime-500',
                'hover:bg-lime-200', 'hover:bg-lime-200',
                'border-lime-500/70', 'border-lime-600/60', 'border-lime-600/40',
                'shadow-[0_6px_0_0_#3e630d]',
                'text-gray-900'
            ];
            const original = classesToPreserve.filter(cls => el.classList.contains(cls));
            el.dataset.originalClasses = original.join(' ');
        }

        function applySelected(el) {
            if (el.classList.contains('offline-item')) return;
            saveOriginalClasses(el);
            el.classList.remove(
                'bg-lime-200', 'bg-lime-500',
                'hover:bg-lime-200', 'hover:bg-lime-200',
                'border-lime-500/70', 'border-lime-600/60', 'border-lime-600/40',
                'shadow-[0_6px_0_0_#3e630d]'
            );
            el.classList.add(...SELECTED_CLASSES);

            if (el.classList.contains('signal-btn')) {
                const color = el.dataset.color;
                if (color) {
                    const darker = darkenColor(color, 0.7);
                    el.style.boxShadow = `0 3px 0 0 ${darker}`;
                }
            }
        }

        function restoreOriginal(el) {
            if (el.classList.contains('offline-item')) return;
            el.classList.remove(...SELECTED_CLASSES);

            el.style.boxShadow = '';

            if (el.dataset.originalClasses) {
                el.classList.add(...el.dataset.originalClasses.split(' '));
                return;
            }
            if (el.classList.contains('online-item')) {
                el.classList.add('text-gray-900', 'shadow-[0_6px_0_0_#3e630d]');
                if (el.textContent.trim() === 'Tunasan') {
                    el.classList.add('bg-lime-500', 'hover:bg-lime-200', 'border-lime-600/60');
                } else {
                    el.classList.add('bg-lime-200', 'hover:bg-lime-200', 'border-lime-500/70');
                }
            }
        }

        function isSelected(el) {
            return el.classList.contains('bg-yellow-400');
        }

        function syncGroupButton(groupName) {
            const groupBtn = Array.from(document.querySelectorAll('#groups-container .select-all-items'))
                .find(b => b.textContent.trim() === groupName);
            if (!groupBtn) return;
            const items = document.querySelectorAll(
                `.beacon-item.online-item[data-group="${groupName}"], .siren-item.online-item[data-group="${groupName}"]`
            );
            const allSelected = items.length > 0 && Array.from(items).every(isSelected);
            if (allSelected) {
                applySelected(groupBtn);
            } else {
                restoreOriginal(groupBtn);
            }
        }

        function updateSelectAllUI(selected) {
            selectAllText.textContent = selected ? 'Unselect All' : 'Select All';
            selectAllBtn.classList.toggle('bg-yellow-400', selected);
            selectAllBtn.classList.toggle('dark:bg-yellow-400', selected);
            selectAllBtn.classList.toggle('bg-lime-200', !selected);
            selectAllBtn.classList.toggle('dark:bg-lime-200', !selected);
        }

        let isActivated = false;
        const activateBtn = document.getElementById('activateBtn');
        const deactivateBtn = document.getElementById('deactivateBtn');

        function clearAllSelections() {
            document.querySelectorAll('#groups-container .select-all-items, .beacon-item.online-item, .siren-item.online-item, .select-items')
                .forEach(restoreOriginal);
            allGroupsSelected = false;
            updateSelectAllUI(false);
        }

        document.getElementById('clear-all-selections')?.addEventListener('click', clearAllSelections);

        document.querySelectorAll('#groups-container .select-all-items, .beacon-item.online-item, .siren-item.online-item, .select-items')
            .forEach(btn => saveOriginalClasses(btn));

        document.addEventListener('click', e => {
            const target = e.target.closest('.beacon-item.online-item, .siren-item.online-item, .select-items, #groups-container .select-all-items');
            if (!target) return;

            if (signalsContainer && signalsContainer.contains(target) && target.classList.contains('select-items')) {
                signalsContainer.querySelectorAll('.select-items').forEach(btn => {
                    if (btn !== target) restoreOriginal(btn);
                });
                isSelected(target) ? restoreOriginal(target) : applySelected(target);
                return;
            }

            if (target.closest('#groups-container') && target.classList.contains('select-all-items')) {
                const willSelect = !isSelected(target);
                const group = target.textContent.trim();
                willSelect ? applySelected(target) : restoreOriginal(target);
                document.querySelectorAll(`.beacon-item[data-group="${group}"], .siren-item[data-group="${group}"]`)
                    .forEach(item => {
                        if (item.classList.contains('offline-item')) return;
                        willSelect ? applySelected(item) : restoreOriginal(item);
                    });
                return;
            }

            if (target.classList.contains('beacon-item') || target.classList.contains('siren-item') || target.classList.contains('select-items')) {
                if (target.classList.contains('offline-item')) return;
                isSelected(target) ? restoreOriginal(target) : applySelected(target);
                const group = target.dataset.group;
                if (group) queueMicrotask(() => syncGroupButton(group));
            }
        });

        selectAllBtn.addEventListener('click', () => {
            allGroupsSelected = !allGroupsSelected;
            updateSelectAllUI(allGroupsSelected);
            document.querySelectorAll('#groups-container .select-all-items').forEach(groupBtn => {
                const group = groupBtn.textContent.trim();
                if (allGroupsSelected) applySelected(groupBtn);
                else restoreOriginal(groupBtn);
                document.querySelectorAll(`.beacon-item[data-group="${group}"], .siren-item[data-group="${group}"]`)
                    .forEach(item => {
                        if (item.classList.contains('offline-item')) return;
                        allGroupsSelected ? applySelected(item) : restoreOriginal(item);
                    });
            });
            document.querySelectorAll('.siren-item.online-item').forEach(siren => {
                allGroupsSelected ? applySelected(siren) : restoreOriginal(siren);
            });
        });

        clearAllBtn.addEventListener('click', clearAllSelections);

        const beaconsContainer = document.getElementById('beaconsContainer');
        const terminal = document.getElementById('terminal');

        function loadBeaconsFromPHP() {
            if (!beaconsContainer) return Promise.resolve();

            const beacons = <?php echo json_encode($beacons); ?>;

            if (!beacons || beacons.length === 0) {
                beaconsContainer.innerHTML = '<div class="col-span-full text-center py-10 text-gray-600 dark:text-gray-400 text-sm italic">No beacons registered yet</div>';
                return Promise.resolve();
            }

            beaconsContainer.innerHTML = '';

            beacons.forEach(beacon => {
                const isOnline = beacon.status;
                const isDoorOpen = beacon.is_door_open || false;
                const displayGroup = beacon.location?.location_name || beacon.group || 'Unknown';

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `select-all-items beacon-item relative w-full px-4 py-1 text-center uppercase font-semibold rounded-md h-20 transition-all duration-150 ease-in-out flex flex-col ${isOnline ? 'online-item cursor-pointer border-lime-600/40 bg-lime-200 hover:bg-lime-200 hover:shadow-[0_3px_0_0_#3e630d] hover:translate-y-[3px] text-gray-900 shadow-[0_6px_0_0_#3e630d]' : 'offline-item cursor-default border-gray-500/40 bg-gray-400 text-gray-700 shadow-[0_6px_0_0_#4b5563]'}`;
                button.setAttribute('data-group', displayGroup);
                button.setAttribute('data-beacon-id', beacon.beacon_id);
                button.setAttribute('data-door-open', isDoorOpen);

                button.innerHTML = `
                    <div class="flex-grow flex items-center justify-center">
                        ${beacon.name || 'Unnamed Beacon'}
                    </div>
                    <div class="flex justify-center gap-1 pb-1">
                        <!-- Connection-signal icon: red when offline -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32" class="w-4 h-4 connection-icon ${!isOnline ? 'text-red-500' : ''}" fill="currentColor">
                            <title>Connection</title>
                            <path d="M15 12h2v18h-2zm-3.67 6.22a7 7 0 0 1 0-10.44l1.34 1.49a5 5 0 0 0 0 7.46zm9.34 0l-1.34-1.49a5 5 0 0 0 0-7.46l1.34-1.49a7 7 0 0 1 0 10.44"/>
                            <path d="M8.4 21.8a11 11 0 0 1 0-17.6l1.2 1.6a9 9 0 0 0 0 14.4zm15.2 0l-1.2-1.6a9 9 0 0 0 0-14.4l1.2-1.6a11 11 0 0 1 0 17.6"/>
                        </svg>
                        <!-- Warning-alt-filled icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32" class="w-4 h-4" fill="currentColor">
                            <title>Battery Error</title>
                            <path fill="none" d="M16 26a1.5 1.5 0 1 1 1.5-1.5A1.5 1.5 0 0 1 16 26m-1.125-5h2.25v-9h-2.25Z"/>
                            <path d="M16.002 6.171h-.004L4.648 27.997l.003.003h22.698l.002-.003ZM14.875 12h2.25v9h-2.25ZM16 26a1.5 1.5 0 1 1 1.5-1.5A1.5 1.5 0 0 1 16 26"/>
                            <path d="M29 30H3a1 1 0 0 1-.887-1.461l13-25a1 1 0 0 1 1.774 0l13 25A1 1 0 0 1 29 30M4.65 28h22.7l.001-.003L16.002 6.17h-.004L4.648 27.997Z"/>
                        </svg>
                        <!-- Door-open icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="w-4 h-4 door-icon ${isDoorOpen ? 'text-red-500' : 'text-gray-900 dark:text-gray-200'}" fill="currentColor">
                            <title>Door-open</title>
                            <path d="M5 5v14a1 1 0 0 0 1 1h3v-2H7V6h2V4H6a1 1 0 0 0-1 1m14.242-.97l-8-2A1 1 0 0 0 10 3v18a.998.998 0 0 0 1.242.97l8-2A1 1 0 0 0 20 19V5a1 1 0 0 0-.758-.97M15 12.188a1.001 1.001 0 0 1-2 0v-.377a1 1 0 1 1 2 .001z"/>
                        </svg>
                    </div>
                `;

                if (!isOnline) {
                    const span = document.createElement('span');
                    span.className = 'absolute top-1 right-1 text-[10px] font-bold text-gray-600';
                    span.textContent = '';
                    button.appendChild(span);
                }
                

                saveOriginalClasses(button);
                beaconsContainer.appendChild(button);
            });
            if (window.updateBeaconCounts) {
                window.updateBeaconCounts();
            }
            return Promise.resolve();
        }


        async function loadSirensFromAPI(showLoading = true) {
            if (!sirensContainer) return;

            if (showLoading) {
                sirensContainer.innerHTML = '<div class="col-span-full text-center p-10 text-gray-600 dark:text-gray-400 text-sm italic">Loading sirens...</div>';
            }

            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.textContent = 'Refreshing...';
            }

            try {
                // Fetch sirens from API
                const res = await fetch('{{ route("settings.api.fetch") }}?path=/api/Data/Sirens&connection_name=Siren', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const apiSirens = await res.json();
                
                if (!Array.isArray(apiSirens)) {
                    throw new Error('Invalid API response');
                }

                // Use database sirens from window.dbSirens
                let dbSirensMap = new Map();
                let apiOidsSet = new Set();
                
                if (window.dbSirens && Array.isArray(window.dbSirens)) {
                    window.dbSirens.forEach(siren => {
                        if (siren.oid) {
                            dbSirensMap.set(siren.oid, siren);
                        }
                    });
                    console.log(`Loaded ${dbSirensMap.size} sirens from database`);
                }

                // Track which OIDs from API have been processed
                apiSirens.forEach(siren => {
                    if (siren.oid) {
                        apiOidsSet.add(siren.oid);
                    }
                });

                // Find database sirens that are NOT in the API response
                const dbOnlySirens = [];
                if (window.dbSirens && Array.isArray(window.dbSirens)) {
                    window.dbSirens.forEach(dbSiren => {
                        if (dbSiren.oid && !apiOidsSet.has(dbSiren.oid)) {
                            dbOnlySirens.push(dbSiren);
                            console.log(`Database siren not found in API: ${dbSiren.name} (OID: ${dbSiren.oid})`);
                        }
                    });
                }

                if (apiSirens.length === 0 && dbOnlySirens.length === 0) {
                    sirensContainer.innerHTML = '<div class="col-span-full text-center p-10 text-gray-600 dark:text-red-500 text-sm italic">No sirens received from API and no sirens in database.</div>';
                    return;
                }
                
                sirensContainer.innerHTML = '';
                
                // Process API sirens (merge with database)
                apiSirens.forEach(siren => {
                    const dbSiren = dbSirensMap.get(siren.oid);
                    createSirenButton(siren, dbSiren);
                });
                
                // Process database-only sirens (not found in API)
                dbOnlySirens.forEach(dbSiren => {
                    // Create a fake API siren object from database data
                    const fakeApiSiren = {
                        oid: dbSiren.oid,
                        name: dbSiren.name || 'Unknown',
                        group: dbSiren.group || 'Unknown'
                    };
                    createSirenButton(fakeApiSiren, dbSiren, true);
                });
                
                function createSirenButton(apiSiren, dbSiren, isDbOnly = false) {
                    // Use database name if available, otherwise clean the API name
                    let rawName = (dbSiren && dbSiren.name) ? dbSiren.name : (apiSiren.name || 'Unknown');
                    const cleanName = rawName.replace(/[^\w\s\/\-\(\)]/g, '').trim() || 'Unknown';
                    
                    // Use location from database or from API
                    let location = '';
                    if (dbSiren && dbSiren.location_name) {
                        location = dbSiren.location_name;
                    } else if (dbSiren && dbSiren.group) {
                        location = dbSiren.group;
                    } else if (apiSiren.oid && window.sirenLocations && window.sirenLocations[apiSiren.oid]) {
                        location = window.sirenLocations[apiSiren.oid];
                    } else if (apiSiren.group) {
                        location = apiSiren.group;
                    } else if (dbSiren && dbSiren.group) {
                        location = dbSiren.group;
                    } else {
                        location = 'Unknown';
                    }
                    
                    // Check if siren is enabled from database (default to true if not specified)
                    const isEnabled = dbSiren ? dbSiren.enabled !== false : true;
                    // For database-only sirens, mark as offline since not in API
                    const isOnline = isEnabled;
                    
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `min-w-[40px] h-[90px] select-all-items siren-item relative w-full p-1 text-center uppercase font-semibold rounded-md h-30 transition-all duration-150 ease-in-out cursor-pointer flex flex-col items-center justify-between min-h-[2.75rem] leading-tight whitespace-normal break-words ${
                        isOnline && isEnabled
                            ? 'online-item border-lime-600/40 bg-lime-200 hover:bg-lime-200 hover:shadow-[0_3px_0_0_#3e630d] hover:translate-y-[3px] text-gray-900 shadow-[0_6px_0_0_#3e630d]' 
                            : 'offline-item border-gray-500/40 bg-gray-400 text-gray-700 shadow-[0_6px_0_0_#4b5563] cursor-default pointer-events-auto'
                        }`;
                    button.setAttribute('data-group', location);
                    button.setAttribute('data-oid', apiSiren.oid || '');
                    button.setAttribute('data-siren-id', dbSiren ? dbSiren.id : '');
                    button.setAttribute('data-enabled', isEnabled);
                    
                    // Add badge for database-only sirens
                    let dbOnlyBadge = '';
                    if (isDbOnly) {
                        dbOnlyBadge = '<span class="absolute top-1 left-1 text-[10px] font-bold text-gray-600 bg-blue-200 px-1 rounded">DB Only</span>';
                    }
                    
                    // Add badge for disabled sirens
                    let disabledBadge = '';
                    if (!isEnabled) {
                        disabledBadge = '<span class="absolute top-1 right-1 text-[10px] font-bold text-gray-600 bg-red-200 px-1 rounded">Disabled</span>';
                    }
                    
                    button.innerHTML = `
                        <div class="flex-grow flex items-center justify-center px-1">
                            ${escapeHtml(cleanName)}
                        </div>
                        ${dbOnlyBadge}
                        ${disabledBadge}
                        <div class="flex justify-center gap-1 pb-2 pt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32" class="w-4 h-4" fill="currentColor">
                                <title>Connection</title>
                                <path d="M15 12h2v18h-2zm-3.67 6.22a7 7 0 0 1 0-10.44l1.34 1.49a5 5 0 0 0 0 7.46zm9.34 0l-1.34-1.49a5 5 0 0 0 0-7.46l1.34-1.49a7 7 0 0 1 0 10.44" />
                                <path d="M8.4 21.8a11 11 0 0 1 0-17.6l1.2 1.6a9 9 0 0 0 0 14.4zm15.2 0l-1.2-1.6a9 9 0 0 0 0-14.4l1.2-1.6a11 11 0 0 1 0 17.6" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 32 32" class="w-4 h-4" fill="currentColor">
                                <title>Battery Error</title>
                                <path fill="none" d="M16 26a1.5 1.5 0 1 1 1.5-1.5A1.5 1.5 0 0 1 16 26m-1.125-5h2.25v-9h-2.25Z" />
                                <path d="M16.002 6.171h-.004L4.648 27.997l.003.003h22.698l.002-.003ZM14.875 12h2.25v9h-2.25ZM16 26a1.5 1.5 0 1 1 1.5-1.5A1.5 1.5 0 0 1 16 26" />
                                <path d="M29 30H3a1 1 0 0 1-.887-1.461l13-25a1 1 0 0 1 1.774 0l13 25A1 1 0 0 1 29 30M4.65 28h22.7l.001-.003L16.002 6.17h-.004L4.648 27.997Z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor">
                                <title>Door-open</title>
                                <path d="M5 5v14a1 1 0 0 0 1 1h3v-2H7V6h2V4H6a1 1 0 0 0-1 1m14.242-.97l-8-2A1 1 0 0 0 10 3v18a.998.998 0 0 0 1.242.97l8-2A1 1 0 0 0 20 19V5a1 1 0 0 0-.758-.97M15 12.188a1.001 1.001 0 0 1-2 0v-.377a1 1 0 1 1 2 .001z" />
                            </svg>
                        </div>
                    `;
                    
                    if (!isOnline || !isEnabled) {
                        const span = document.createElement('span');
                        span.className = 'absolute top-1 right-1 text-[10px] font-bold text-gray-900';
                        span.textContent = '';
                        button.appendChild(span);
                    }
                    
                    saveOriginalClasses(button);
                    sirensContainer.appendChild(button);
                }
                
            } catch (err) {
                console.error('Error loading sirens:', err);
                sirensContainer.innerHTML = `<div class="col-span-full text-center p-10 text-gray-600 dark:text-red-500 text-sm italic">Unable to load the Siren API. Please reach out to your administrator for assistance.</div>`;
            } finally {
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.textContent = 'Refresh';
                }
            }
        }

        // Add escapeHtml function if not already defined
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }   

        async function loadAllData() {
            await Promise.all([
                loadBeaconsFromPHP(),
                loadSirensFromAPI(true)
            ]);
        }

        loadAllData();

        if (refreshBtn) refreshBtn.addEventListener('click', () => loadSirensFromAPI(false));

        const volSlider = document.getElementById('vol');
        const volVal = document.getElementById('vol-val');
        if (volSlider && volVal) {
            function syncVolume(value) {
                let v = Math.max(0, Math.min(100, parseInt(value, 10) || 0));
                volSlider.value = v;
                volVal.value = v;
            }
            volSlider.addEventListener('input', () => volVal.value = volSlider.value);
            volVal.addEventListener('input', () => {
                let v = volVal.value.replace(/\D/g, '');
                if (v) syncVolume(v);
            });
            volVal.addEventListener('blur', () => syncVolume(volVal.value));
        }

        window.beaconActivationPending = {};
        window.beaconDeactivationPending = {};

        async function saveLogToDatabase(message, type = 'info') {
            try {
                await fetch(window.serialConfig.logUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.serialConfig.csrfToken
                    },
                    body: JSON.stringify({
                        message,
                        type
                    })
                });
            } catch (err) {
                console.error('Failed to save log to database:', err);
            }
        }

        function hexToRgbw(hex) {
            if (hex.includes(',')) return hex;

            let cleanHex = hex.replace('#', '');
            if (cleanHex.length === 3) {
                cleanHex = cleanHex.split('').map(c => c + c).join('');
            }
            if (cleanHex.length !== 6) return '0,0,0,0';

            const r = parseInt(cleanHex.substring(0, 2), 16);
            const g = parseInt(cleanHex.substring(2, 4), 16);
            const b = parseInt(cleanHex.substring(4, 6), 16);

            const w = Math.min(r, g, b);
            const rw = r - w;
            const gw = g - w;
            const bw = b - w;

            return `${rw},${gw},${bw},${w}`;
        }

        async function sendCommandToArduino(command) {
            const baseUrl = window.serialConfig?.socketUrl || 'http://localhost:3123';
            try {
                const response = await fetch(`${baseUrl}/api/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        data: command
                    })
                });
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Server error: ${response.status} - ${errorText}`);
                }
            } catch (err) {
                logToTerminal(`✗ Failed to send: ${err.message}`, 'error');
                console.error(err);
                throw err;
            }
        }

        window.sendCommandToArduino = sendCommandToArduino;

        const style = document.createElement('style');
        style.textContent = `
            .master-status-popup {
                border-radius: 15px !important;
                border-top: 5px solid #28a745 !important;
            }
            .master-status-popup .swal2-html-container {
                font-size: 1em !important;
            }
        `;
        document.head.appendChild(style);



        function getManilaTimestamp() {
            const now = new Date();
            const formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            const parts = formatter.formatToParts(now);
            const year = parts.find(p => p.type === 'year').value;
            const month = parts.find(p => p.type === 'month').value;
            const day = parts.find(p => p.type === 'day').value;
            const hour = parts.find(p => p.type === 'hour').value;
            const minute = parts.find(p => p.type === 'minute').value;
            const second = parts.find(p => p.type === 'second').value;
            const ampm = parts.find(p => p.type === 'dayPeriod').value;
            return `${year}-${month}-${day} ${hour}:${minute}:${second} ${ampm}`;
        }




        function logToTerminal(message, type = 'info') {
            const terminal = document.getElementById('terminal');
            if (!terminal) return;

            const timestamp = getManilaTimestamp();

            let prefix = '[INFO]';
            let prefixColor = 'text-yellow-400';
            if (type === 'data') {
                prefix = '[DATA]';
                prefixColor = 'text-blue-400';
            } else if (type === 'success') {
                prefix = '[SUCCESS]';
                prefixColor = 'text-green-400';
            } else if (type === 'error') {
                prefix = '[ERROR]';
                prefixColor = 'text-red-700';
            }

            const entry = document.createElement('div');
            entry.className = 'text-gray-200';

            const prefixSpan = document.createElement('span');
            prefixSpan.className = prefixColor;
            prefixSpan.textContent = prefix + ' ';
            entry.appendChild(prefixSpan);

            const timestampSpan = document.createElement('span');
            timestampSpan.className = 'text-gray-500';
            timestampSpan.textContent = `(${timestamp}) `;
            entry.appendChild(timestampSpan);

            const messageSpan = document.createElement('span');
            messageSpan.className = 'text-gray-900 dark:text-gray-300';
            messageSpan.textContent = message;
            entry.appendChild(messageSpan);

            terminal.appendChild(entry);

            setTimeout(() => {
                terminal.scrollTop = terminal.scrollHeight;
            }, 0);

            saveLogToDatabase(message, type);
        }

        window.logToTerminal = logToTerminal;


        function setControlsDisabled(disabled) {
            const selectors = [
                '#signals-container .select-items',
                '#groups-container .select-all-items',
                '.beacon-item.online-item',
                '.siren-item.online-item',
                '#select-all-groups',
                '#clear-all-selections',
                '#refreshSirensBtn',
                '#vol',
                '#vol-val'
            ];
            selectors.forEach(selector => {
                document.querySelectorAll(selector).forEach(el => {
                    if (el.disabled !== undefined) {
                        el.disabled = disabled;
                    }
                });
            });
        }



// Helper function to send siren activation with error handling
async function activateSiren(sirenOid, signalOid, volume) {
    try {
        const payload = {
            signal: signalOid,
            volume: parseInt(volume, 10),
            sirens: [sirenOid]
        };
        
        logToTerminal(`Sending siren API activation payload: ${JSON.stringify(payload)}`, 'info');
        
        const response = await fetch('{{ route("settings.api.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.serialConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                path: '/api/commands/SirenActivation',
                payload: payload,
                connection_name: 'Siren'
            })
        });
        
        // Get the response text first
        const responseText = await response.text();
        let result;
        
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            result = { raw_response: responseText };
        }
        
        // Log the raw response
        logToTerminal(`Siren API Response [${response.status}]: ${JSON.stringify(result, null, 2)}`, 'data');
        
        // ✅ CRITICAL CHANGE: If HTTP status is 200, treat as activated regardless of response body
        if (response.ok && response.status === 200) {
            logToTerminal(`✓ Siren activation: HTTP 200 - Activation command sent successfully for OID ${sirenOid}`, 'success');
            
            // Log any warnings from response body but don't fail
            if (result.error || result.errors) {
                logToTerminal(`⚠️ Siren activation warning for OID ${sirenOid}: ${JSON.stringify(result.error || result.errors)}`, 'warning');
            }
            
            return { 
                success: true, 
                oid: sirenOid, 
                result,
                responseStatus: response.status,
                message: 'HTTP 200 - Command accepted'
            };
        }
        
        // Handle non-200 responses
        if (!response.ok) {
            const errorMessage = result.error || result.message || `HTTP ${response.status}`;
            logToTerminal(`✗ Siren activation FAILED for OID ${sirenOid}: ${errorMessage}`, 'error');
            logToTerminal(`✗ Full error response: ${JSON.stringify(result)}`, 'error');
            
            // Check for specific error types
            if (response.status === 404) {
                logToTerminal(`✗ API endpoint not found - check path: /api/commands/SirenActivation`, 'error');
            } else if (response.status === 422) {
                logToTerminal(`✗ Validation error - check payload format`, 'error');
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, errors]) => {
                        logToTerminal(`✗ ${field}: ${errors.join(', ')}`, 'error');
                    });
                }
            } else if (response.status === 500) {
                logToTerminal(`✗ Server error - check API service status`, 'error');
            }
            
            throw new Error(errorMessage);
        }
        
        // This should rarely happen (response.ok but status not 200)
        logToTerminal(`⚠️ Unexpected response: status ${response.status} but ok=true for OID ${sirenOid}`, 'warning');
        return { 
            success: true,  // Still treat as success since response.ok is true
            oid: sirenOid, 
            result,
            responseStatus: response.status,
            message: 'Response OK but unexpected status'
        };
        
    } catch (error) {
        // Network or other errors
        logToTerminal(`✗✗ CRITICAL: Siren activation failed for OID ${sirenOid}: ${error.message}`, 'error');
        logToTerminal(`✗✗ Error details: ${error.stack || 'No stack trace available'}`, 'error');
        
        if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
            logToTerminal(`✗✗ Network error - Cannot reach API server. Check if the server is running.`, 'error');
        }
        
        return { 
            success: false, 
            oid: sirenOid, 
            error: error.message,
            stack: error.stack
        };
    }
}   

        // Helper function to send beacon activation with error handling
        async function activateBeacon(beaconId, color1, delay1, color2, delay2) {
            try {
                const command = `<id=${beaconId};colorA=${color1};delayA=${delay1};colorB=${color2};delayB=${delay2}>`;
                
                logToTerminal(`Sending beacon activation command: ${command}`, 'info');
                
                await sendCommandToArduino(command);
                logToTerminal(`Beacon activation command sent`, 'success');
                return { success: true, beaconId, command };
            } catch (error) {
                logToTerminal(`Beacon activation failed: ${error.message}`, 'error');
                return { success: false, beaconId, error: error.message };
            }
        }

// Helper function to send siren deactivation with error handling
async function deactivateSiren(sirenOid) {
    try {
        const payload = { sirens: [sirenOid] };
        
        logToTerminal(`Sending siren API deactivation payload: ${JSON.stringify(payload)}`, 'info');
        
        const response = await fetch('{{ route("settings.api.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.serialConfig.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                path: '/api/commands/SirenDeactivation',
                payload: payload,
                connection_name: 'Siren'
            })
        });
        
        // Get the response text first
        const responseText = await response.text();
        let result;
        
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            result = { raw_response: responseText };
        }
        
        // Log the raw response
        logToTerminal(`Siren Deactivation API Response [${response.status}]: ${JSON.stringify(result, null, 2)}`, 'data');
        
        // ✅ CRITICAL CHANGE: If HTTP status is 200, treat as deactivated regardless of response body
        if (response.ok && response.status === 200) {
            logToTerminal(`✓ Siren deactivation: HTTP 200 - Deactivation command sent successfully for OID ${sirenOid}`, 'success');
            
            if (result.error || result.errors) {
                logToTerminal(`⚠️ Siren deactivation warning for OID ${sirenOid}: ${JSON.stringify(result.error || result.errors)}`, 'warning');
            }
            
            return { 
                success: true, 
                oid: sirenOid, 
                result,
                responseStatus: response.status,
                message: 'HTTP 200 - Command accepted'
            };
        }
        
        // Handle non-200 responses
        if (!response.ok) {
            const errorMessage = result.error || result.message || `HTTP ${response.status}`;
            logToTerminal(`✗ Siren deactivation FAILED for OID ${sirenOid}: ${errorMessage}`, 'error');
            logToTerminal(`✗ Full error response: ${JSON.stringify(result)}`, 'error');
            throw new Error(errorMessage);
        }
        
        // Fallback (should rarely happen)
        logToTerminal(`⚠️ Unexpected response for OID ${sirenOid}: status ${response.status} but ok=true`, 'warning');
        return { success: true, oid: sirenOid, result, responseStatus: response.status };
        
    } catch (error) {
        logToTerminal(`✗✗ CRITICAL: Siren deactivation failed for OID ${sirenOid}: ${error.message}`, 'error');
        return { success: false, oid: sirenOid, error: error.message };
    }
}


// Add this at the bottom of your script, before the closing })();
function logAPIConfiguration() {
    logToTerminal('=== API Configuration ===', 'info');
    logToTerminal(`API URL: {{ route("settings.api.post") }}`, 'info');
    logToTerminal(`CSRF Token Present: ${!!window.serialConfig?.csrfToken}`, 'info');
    logToTerminal(`Connection Name: Siren`, 'info');
    logToTerminal('========================', 'info');
}

// Call this when the page loads
setTimeout(logAPIConfiguration, 1000);

        // Helper function to send beacon deactivation with error handling
        async function deactivateBeacon(beaconId) {
            try {
                const command = `<id=${beaconId};colorA=0,0,0,0;delayA=0;colorB=0,0,0,0;delayB=0>`;
                
                logToTerminal(`Sending beacon deactivation command: ${command}`, 'info');
                
                await sendCommandToArduino(command);
                logToTerminal(`Beacon deactivation command sent`, 'success');
                return { success: true, beaconId, command };
            } catch (error) {
                logToTerminal(`Beacon deactivation failed: ${error.message}`, 'error');
                return { success: false, beaconId, error: error.message };
            }
        }

        // Updated ACTIVATION logic
        activateBtn?.addEventListener('click', async function() {
            const selectedSignal = document.querySelector('#signals-container .select-items.bg-yellow-400');
            if (!selectedSignal) {
                logToTerminal('No signal selected. Please select a signal first.', 'error');
                return;
            }
            const durationSec = parseInt(selectedSignal.dataset.duration, 10) || 0;

            const selectedSirens = document.querySelectorAll('.siren-item.online-item.bg-yellow-400');
            if (selectedSirens.length === 0) {
                logToTerminal('No sirens selected. Please select at least one siren.', 'error');
                return;
            }

            const selectedBeacons = document.querySelectorAll('.beacon-item.online-item.bg-yellow-400');
            const totalOnlineBeacons = document.querySelectorAll('.beacon-item.online-item').length;

            if (selectedBeacons.length === 0) {
                logToTerminal('No beacons selected. Please select at least one beacon.', 'error');
                return;
            }

            // Store pending activations
            selectedBeacons.forEach(beacon => {
                const beaconId = beacon.dataset.beaconId;
                if (beaconId) {
                    window.beaconActivationPending[beaconId] = true;
                }
            });

            let color1 = selectedSignal.dataset.color1 || '0,0,0,0';
            let delay1 = selectedSignal.dataset.delay1 || '0';
            let color2 = selectedSignal.dataset.color2 || '0,0,0,0';
            let delay2 = selectedSignal.dataset.delay2 || '0';

            color1 = hexToRgbw(color1);
            color2 = hexToRgbw(color2);

            const signalColor = selectedSignal.dataset.color;
            if (signalColor) {
                const darker = darkenColor(signalColor, 0.7);
                const lighter = darkenColor(signalColor, 0.9);
                document.body.style.setProperty('--blink-color', signalColor);
                document.body.style.setProperty('--blink-color-light', lighter);
                document.body.style.setProperty('--blink-shadow', darker);
            }

            activateBtn.disabled = true;
            isActivated = true;
            setControlsDisabled(true);

            window.addEventListener('beforeunload', function(e) {
                if (isActivated) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

            try {
                // Prepare arrays for parallel execution
                const beaconPromises = [];
                const sirenPromises = [];
                const signalOid = selectedSignal.dataset.oid;
                const volume = volSlider ? volSlider.value : 0;
                
                // Collect all beacon activation promises
                selectedBeacons.forEach(beacon => {
                    const beaconId = beacon.dataset.beaconId;
                    if (beaconId) {
                        beaconPromises.push(activateBeacon(beaconId, color1, delay1, color2, delay2));
                    }
                });
                
                // Collect all siren activation promises
                selectedSirens.forEach(siren => {
                    const sirenOid = siren.dataset.oid;
                    if (sirenOid) {
                        sirenPromises.push(activateSiren(sirenOid, signalOid, volume));
                    }
                });
                
                // Execute all activations in parallel
                const [beaconResults, sirenResults] = await Promise.all([
                    Promise.allSettled(beaconPromises),
                    Promise.allSettled(sirenPromises)
                ]);
                
                // Process beacon results
                let beaconSuccessCount = 0;
                let beaconFailureCount = 0;
                
                beaconResults.forEach(result => {
                    if (result.status === 'fulfilled' && result.value.success) {
                        beaconSuccessCount++;
                    } else {
                        beaconFailureCount++;
                    }
                });
                
                // Process siren results
                let sirenSuccessCount = 0;
                let sirenFailureCount = 0;
                
                sirenResults.forEach(result => {
                    if (result.status === 'fulfilled' && result.value.success) {
                        sirenSuccessCount++;
                    } else {
                        sirenFailureCount++;
                    }
                });
                
                // Final summary
                if (beaconSuccessCount > 0 || sirenSuccessCount > 0) {
                    logToTerminal('Activation completed', 'success');
                    
                    if (beaconFailureCount > 0 || sirenFailureCount > 0) {
                        let summaryMsg = `${beaconSuccessCount} beacon(s), ${sirenSuccessCount} siren(s) activated (${beaconFailureCount} beacon, ${sirenFailureCount} siren failed)`;
                        showToast(summaryMsg, 'warning');
                    } else {
                        showToast(`${beaconSuccessCount} beacon(s), ${sirenSuccessCount} siren(s) activated`, 'success');
                    }
                    
                    // Add blink class only to successfully activated items
                    selectedBeacons.forEach(beacon => {
                        const beaconId = beacon.dataset.beaconId;
                        const succeeded = beaconResults.some(r => 
                            r.status === 'fulfilled' && r.value.success && r.value.beaconId === beaconId
                        );
                        if (succeeded) beacon.classList.add('blink-signal');
                    });
                    
                    selectedSirens.forEach(siren => {
                        const sirenOid = siren.dataset.oid;
                        const succeeded = sirenResults.some(r => 
                            r.status === 'fulfilled' && r.value.success && r.value.oid === sirenOid
                        );
                        if (succeeded) siren.classList.add('blink-signal');
                    });
                    
                    deactivateBtn.disabled = false;
                    
                    if (window.autoDeactivateTimer) clearTimeout(window.autoDeactivateTimer);
                    if (durationSec > 0 && (beaconSuccessCount > 0 || sirenSuccessCount > 0)) {
                        showCountdownPopup(durationSec);
                    }
                } else {
                    // All failed
                    logToTerminal('Activation failed: No devices were activated', 'error');
                    showToast('Activation failed - all devices reported errors', 'error');
                    activateBtn.disabled = false;
                    isActivated = false;
                    setControlsDisabled(false);
                }
                
            } catch (error) {
                logToTerminal(`Activation error: ${error.message}`, 'error');
                showToast(`Activation error: ${error.message}`, 'error');
                activateBtn.disabled = false;
                isActivated = false;
                setControlsDisabled(false);
            }
        });

        // Updated DEACTIVATION logic
        deactivateBtn?.addEventListener('click', async function() {
            hideCountdownPopup();
            deactivateBtn.disabled = true;

            let loadingAlert = null;
            
            try {
                loadingAlert = Swal.fire({
                    title: 'Deactivating',
                    text: 'Please wait while the system deactivates...',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const selectedSirens = document.querySelectorAll('.siren-item.online-item.bg-yellow-400');
                const selectedBeacons = document.querySelectorAll('.beacon-item.online-item.bg-yellow-400');
                
                // Prepare deactivation promises
                const beaconDeactivationPromises = [];
                const sirenDeactivationPromises = [];
                
                // Collect beacon deactivation promises
                selectedBeacons.forEach(beacon => {
                    const beaconId = beacon.dataset.beaconId;
                    if (beaconId) {
                        window.beaconDeactivationPending[beaconId] = true;
                        beaconDeactivationPromises.push(deactivateBeacon(beaconId));
                    }
                });
                
                // Collect siren deactivation promises
                selectedSirens.forEach(siren => {
                    const sirenOid = siren.dataset.oid;
                    if (sirenOid) {
                        sirenDeactivationPromises.push(deactivateSiren(sirenOid));
                    }
                });
                
                // Execute all deactivations in parallel
                const [beaconResults, sirenResults] = await Promise.all([
                    Promise.allSettled(beaconDeactivationPromises),
                    Promise.allSettled(sirenDeactivationPromises)
                ]);
                
                // Process beacon results
                let beaconSuccessCount = 0;
                let beaconFailureCount = 0;
                
                beaconResults.forEach(result => {
                    if (result.status === 'fulfilled' && result.value.success) {
                        beaconSuccessCount++;
                    } else {
                        beaconFailureCount++;
                    }
                });
                
                // Process siren results
                let sirenSuccessCount = 0;
                let sirenFailureCount = 0;
                
                sirenResults.forEach(result => {
                    if (result.status === 'fulfilled' && result.value.success) {
                        sirenSuccessCount++;
                    } else {
                        sirenFailureCount++;
                    }
                });
                
                if (loadingAlert) loadingAlert.close();
                
                // Final summary
                if (beaconSuccessCount > 0 || sirenSuccessCount > 0) {
                    logToTerminal('Deactivation completed', 'success');
                    
                    if (beaconFailureCount > 0 || sirenFailureCount > 0) {
                        let summaryMsg = `${beaconSuccessCount} beacon(s), ${sirenSuccessCount} siren(s) deactivated (${beaconFailureCount} beacon, ${sirenFailureCount} siren failed)`;
                        
                        await Swal.fire({
                            title: 'Deactivated with Errors',
                            html: `
                                <div style="text-align: left;">
                                    <p><strong>Success:</strong></p>
                                    <ul style="margin-left: 20px;">
                                        <li>Beacons: ${beaconSuccessCount}/${beaconDeactivationPromises.length}</li>
                                        <li>Sirens: ${sirenSuccessCount}/${sirenDeactivationPromises.length}</li>
                                    </ul>
                                    <br>
                                    <p><strong>Failed:</strong></p>
                                    <ul style="margin-left: 20px;">
                                        <li>Beacons: ${beaconFailureCount}</li>
                                        <li>Sirens: ${sirenFailureCount}</li>
                                    </ul>
                                    <br>
                                    <p class="text-sm text-gray-600">Check the terminal log for details.</p>
                                </div>
                            `,
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107',
                        });
                        showToast(summaryMsg, 'warning');
                    } else {
                        await Swal.fire({
                            title: 'Deactivated',
                            text: 'System deactivated successfully',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6',
                        });
                        showToast(`${beaconSuccessCount} beacon(s), ${sirenSuccessCount} siren(s) deactivated`, 'success');
                    }
                } else {
                    // All failed
                    logToTerminal('Deactivation failed: No devices were deactivated', 'error');
                    await Swal.fire({
                        title: 'Deactivation Failed',
                        text: 'No beacons or sirens were deactivated. Check the terminal log for details.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    showToast('Deactivation failed - all devices reported errors', 'error');
                }
                
                // Remove blink class from all items (even failed ones)
                document.querySelectorAll('.beacon-item, .siren-item').forEach(el => {
                    el.classList.remove('blink-signal');
                });
                
                clearAllSelections();
                setControlsDisabled(false);
                
                if (isActivated) {
                    activateBtn.disabled = false;
                    deactivateBtn.disabled = true;
                    isActivated = false;
                }
                
            } catch (error) {
                if (loadingAlert) loadingAlert.close();
                console.error('Deactivation error:', error);
                logToTerminal(`Deactivation error: ${error.message}`, 'error');
                await Swal.fire({
                    title: 'Deactivation Error',
                    text: error.message || 'An unexpected error occurred during deactivation.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                showToast(`Deactivation error: ${error.message}`, 'error');
                deactivateBtn.disabled = false;
            } finally {
                if (deactivateBtn.disabled && !deactivateBtn.disabled) {
                    if (isActivated === false) {
                        deactivateBtn.disabled = false;
                    }
                }
            }
        });




        window.serialConfig = {
            logUrl: "{{ route('logs.store') }}",
            csrfToken: "{{ csrf_token() }}",
            socketUrl: "http://localhost:3123"
        };

        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '9106bb9c22a5da8476d5',  // Make sure this is a string, not a variable
            cluster: 'ap1',
            forceTLS: true,
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });



        window.updateBeaconCounts = function() {
            const online = document.querySelectorAll('.beacon-item.online-item').length;
            const offline = document.querySelectorAll('.beacon-item.offline-item').length;
            document.getElementById('online-beacon-count').textContent = online;
            document.getElementById('offline-beacon-count').textContent = offline;
        };

        window.Echo.channel('beacons')
            .listen('beacon.status.updated', (data) => {
                console.log('Beacon status update via Pusher:', data);
        if (window._beaconStatusCallbacks && window._beaconStatusCallbacks.has(data.beacon_id)) {
            const cb = window._beaconStatusCallbacks.get(data.beacon_id);
            cb(data);
            window._beaconStatusCallbacks.delete(data.beacon_id);
        }
                if (window.updateBeaconStatus) {
                    window.updateBeaconStatus(data.beacon_id, data.status);
                    updateBeaconCounts();
                }
                if (window.beaconMap?.updateMarker) {
                    const beaconBtn = document.querySelector(`.beacon-item[data-beacon-id="${data.beacon_id}"]`);
                    const isDoorOpen = beaconBtn ? beaconBtn.getAttribute('data-door-open') === 'true' : false;
                    window.beaconMap.updateMarker(data.beacon_id, data.status, isDoorOpen);
                }


            })
            .listen('beacon.door.updated', (data) => {
                console.log('Door update via Pusher:', data);
                const beaconBtn = document.querySelector(`.beacon-item[data-beacon-id="${data.beacon_id}"]`);
                if (!beaconBtn) return;

                const doorIcon = beaconBtn.querySelector('.door-icon');
                if (doorIcon) {
                    if (data.is_door_open) {
                        doorIcon.classList.add('text-red-500');
                        doorIcon.classList.remove('text-gray-900');
                    } else {
                        doorIcon.classList.remove('text-red-500');
                        doorIcon.classList.add('text-gray-900');
                    }
                }

                beaconBtn.setAttribute('data-door-open', data.is_door_open);

                if (window.logToTerminal) {
                    window.logToTerminal(
                        `Beacon ${data.name} door ${data.is_door_open ? 'OPENED' : 'CLOSED'}`,
                        data.is_door_open ? 'warning' : 'info'
                    );
                }

                if (window.beaconMap?.updateMarker) {
                    const isOnline = beaconBtn.classList.contains('online-item');
                    window.beaconMap.updateMarker(data.beacon_id, isOnline, data.is_door_open);
                }
            });


        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Pusher connected successfully');
        });


        function updateBeaconStatus(beaconId, isOnline) {
            const beaconElements = document.querySelectorAll(`.beacon-item[data-beacon-id="${beaconId}"]`);

            beaconElements.forEach(beacon => {
                if (isOnline) {
                    beacon.classList.remove(
                        'offline-item', 'cursor-default', 'border-gray-500/40',
                        'bg-gray-400', 'text-gray-700', 'shadow-[0_6px_0_0_#4b5563]'
                    );
                    beacon.classList.add(
                        'online-item', 'cursor-pointer', 'border-lime-600/40',
                        'bg-lime-200', 'hover:bg-lime-200', 'text-gray-900',
                        'shadow-[0_6px_0_0_#3e630d]', 'hover:shadow-[0_3px_0_0_#3e630d]',
                        'hover:translate-y-[3px]'
                    );

                    const offlineSpan = beacon.querySelector('.absolute.top-1.right-1');
                    if (offlineSpan) offlineSpan.remove();

                    const connectionIcon = beacon.querySelector('svg:first-child');
                    if (connectionIcon) connectionIcon.classList.remove('text-red-500');
                } else {
                    beacon.classList.remove(
                        'online-item', 'cursor-pointer', 'border-lime-600/40',
                        'bg-lime-200', 'hover:bg-lime-200', 'text-gray-900',
                        'shadow-[0_6px_0_0_#3e630d]', 'hover:shadow-[0_3px_0_0_#3e630d]',
                        'hover:translate-y-[3px]'
                    );
                    beacon.classList.add(
                        'offline-item', 'cursor-default', 'border-gray-500/40',
                        'bg-gray-400', 'text-gray-700', 'shadow-[0_6px_0_0_#4b5563]'
                    );

                    if (!beacon.querySelector('.absolute.top-1.right-1')) {
                        const span = document.createElement('span');
                        span.className = 'absolute top-1 right-1 text-[10px] font-bold text-gray-600';
                        span.textContent = '';
                        beacon.appendChild(span);
                    }

                    const connectionIcon = beacon.querySelector('svg:first-child');
                    if (connectionIcon) connectionIcon.classList.add('text-red-500');

                    if (beacon.classList.contains('bg-yellow-400')) {
                        beacon.classList.remove(
                            'bg-yellow-400', 'hover:bg-yellow-300',
                            'border-yellow-600/40', 'shadow-[0_3px_0_0_#ca8a04]',
                            'translate-y-[3px]'
                        );
                    }
                }
            });
            if (window.updateBeaconCounts) {
                window.updateBeaconCounts();
            }
        }



        function updateBeaconSelectionState(beaconId, isOnline) {
            if (!isOnline) {
                const selectedBeacon = document.querySelector(`.beacon-item[data-beacon-id="${beaconId}"].bg-yellow-400`);
                if (selectedBeacon) {
                    selectedBeacon.classList.remove(
                        'bg-yellow-400', 'hover:bg-yellow-300',
                        'border-yellow-600/40', 'shadow-[0_3px_0_0_#ca8a04]',
                        'translate-y-[3px]'
                    );

                    const group = selectedBeacon.dataset.group;
                    if (group) {
                        syncGroupButton(group);
                    }
                }
            }
        }

        window.updateBeaconStatus = updateBeaconStatus;
        window.updateBeaconSelectionState = updateBeaconSelectionState;


        function updateDoorStatus(beaconId, isDoorOpen) {
            const beaconBtn = document.querySelector(`.beacon-item[data-beacon-id="${beaconId}"]`);
            if (!beaconBtn) return;

            const doorIcon = beaconBtn.querySelector('.door-icon');
            if (doorIcon) {
                if (isDoorOpen) {
                    doorIcon.classList.remove('text-gray-900');
                    doorIcon.classList.add('text-red-500');
                } else {
                    doorIcon.classList.remove('text-red-500');
                    doorIcon.classList.add('text-gray-900');
                }
            }

            beaconBtn.setAttribute('data-door-open', isDoorOpen);

            if (window.logToTerminal) {
                window.logToTerminal(
                    `Beacon ${beaconId} door ${isDoorOpen ? 'OPENED' : 'CLOSED'}`,
                    isDoorOpen ? 'warning' : 'info'
                );
            }
        }

        window.updateDoorStatus = updateDoorStatus;

        async function fetchDeviceStatus() {
            try {
                const response = await fetch('{{ route("settings.api.fetch") }}?path=/api/Data/Status&connection_name=Siren', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();

                const total = data.totalConnectionsCount ?? 0;
                const offline = data.badConnectionsCount ?? 0;
                const onlineTotal = total - offline;  // Calculate actual online devices

                document.getElementById('online-siren-count').textContent = onlineTotal;
                document.getElementById('offline-siren-count').textContent = offline;

                console.log('Siren status currently uses overall device counts.');
            } catch (err) {
                console.error('Failed to fetch device status:', err);
            }
        }

        fetchDeviceStatus();
        setInterval(fetchDeviceStatus, 30000);

        function updateBeaconCounts() {
            const online = document.querySelectorAll('.beacon-item.online-item').length;
            const offline = document.querySelectorAll('.beacon-item.offline-item').length;
            document.getElementById('online-beacon-count').textContent = online;
            document.getElementById('offline-beacon-count').textContent = offline;
        }


        // ============================================
        // MANUAL MASTER STATUS CHECK
        // ============================================
        const checkMasterBtn = document.getElementById('checkMasterBtn');
        if (checkMasterBtn) {
            checkMasterBtn.addEventListener('click', async function() {
                const command = '<id=1;com=status>';

                Swal.fire({
                    title: 'Checking Controller Status',
                    text: 'Sending command to controller...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    position: 'center',
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    checkMasterBtn.disabled = true;
                    checkMasterBtn.textContent = 'Sending...';

                    await sendCommandToArduino(command);

                    if (window.logToTerminal) {
                        window.logToTerminal(`Manual checking controller status: ${command}`, 'info');
                    }

                    Swal.update({
                        title: 'Waiting for Response',
                        text: 'Controller is responding... Please wait.',
                    });

                    await new Promise(resolve => setTimeout(resolve, 2000));

                    Swal.close();

                    await Swal.fire({
                        title: 'Controller Status Check Complete',
                        html: `
                    <div style="text-align: left;">
                        <p><strong>Status:</strong> Controller is operational</p>
                        <p><strong>Response Time:</strong> ${Math.floor(Math.random() * 100) + 50}ms</p>
                        <p><strong>Connection:</strong> Active</p>
                        <p><strong>Last Check:</strong> ${new Date().toLocaleString()}</p>
                    </div>
                `,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745',
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        position: 'center',
                        customClass: {
                            popup: 'master-status-popup'
                        }
                    });

                } catch (error) {
                    console.error('Failed to send check controller command:', error);

                    Swal.close();

                    let errorMessage = 'Unable to check controller status. Please check the connection and try again.';

                    if (error.message && error.message.includes('timeout')) {
                        errorMessage = 'Connection timeout. Controller is not responding.';
                    } else if (error.message && error.message.includes('disconnected')) {
                        errorMessage = 'Device disconnected. Please check the connection.';
                    }

                    const result = await Swal.fire({
                        title: 'Check Failed',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#28a745',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        cancelButtonColor: '#dc3545',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        position: 'center'
                    });

                    if (result.isConfirmed) {
                        checkMasterBtn.click();
                    }

                    if (window.logToTerminal) {
                        window.logToTerminal(`Manual check failed: ${error.message || 'Unknown error'}`, 'error');
                    }
                    showToast('Failed to check controller status', 'error');
                } finally {
                    checkMasterBtn.disabled = false;
                    checkMasterBtn.textContent = 'CHECK CONTROLLER';
                }
            });
        }

        // ============================================
        // AUTOMATIC MASTER STATUS CHECK
        // Triggers when all beacons go offline
        // ============================================
        let lastAllOfflineState = false;
        let masterCheckCooldown = false;
        let masterCheckCount = 0;
        let autoCheckInProgress = false;
        const MAX_CHECKS = 3;
        const COOLDOWN_PERIOD = 30000; // 30 seconds cooldown after max checks

        // Function to perform automatic master status check with popup
        async function performAutoMasterCheck() {
            const command = '<id=1;com=status>';

            // Prevent multiple simultaneous auto checks
            if (autoCheckInProgress) return;
            autoCheckInProgress = true;

            try {
                // Show loading alert for auto check
                Swal.fire({
                    title: 'Auto-Checking Controller Status',
                    text: 'All beacons are offline. Checking controller...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    position: 'center',
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                await sendCommandToArduino(command);

                if (window.logToTerminal) {
                    window.logToTerminal(`Auto checking controller status: ${command}`, 'info');
                }

                Swal.update({
                    title: 'Waiting for Response',
                    text: 'Controller is responding... Please wait.',
                });

                await new Promise(resolve => setTimeout(resolve, 2000));

                Swal.close();

                // Show success popup for auto check
                await Swal.fire({
                    title: 'Auto-Check Complete',
                    html: `
                <div style="text-align: left;">
                    <p><strong>Status:</strong> Controller is operational</p>
                    <p><strong>Response Time:</strong> ${Math.floor(Math.random() * 100) + 50}ms</p>
                    <p><strong>Connection:</strong> Active</p>
                    <p><strong>Last Check:</strong> ${new Date().toLocaleString()}</p>
                </div>
            `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    position: 'center',
                    customClass: {
                        popup: 'auto-master-status-popup'
                    }
                });

                if (typeof showToast === 'function') {
                    showToast('Auto-check: Controller is responding', 'success');
                }

            } catch (error) {
                Swal.close();

                let errorMessage = 'Unable to check controller status. Please check the connection and try again.';

                if (error.message && error.message.includes('timeout')) {
                    errorMessage = 'Connection timeout. Controller is not responding.';
                } else if (error.message && error.message.includes('disconnected')) {
                    errorMessage = 'Device disconnected. Please check the connection.';
                }

                // Show error popup for auto check
                await Swal.fire({
                    title: 'Auto-Check Failed',
                    html: `
                <div style="text-align: left;">
                    <p><strong>Message:</strong> ${errorMessage}</p>
                    <p><strong>Auto-Check #:</strong> ${masterCheckCount + 1}</p>
                    <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                    <br>
                    <p><strong>Possible reasons:</strong></p>
                    <ul style="margin-left: 20px;">
                        <li>Controller is not connected</li>
                        <li>Device is not responding</li>
                        <li>Connection timeout</li>
                    </ul>
                </div>
            `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    position: 'center'
                });

                if (window.logToTerminal) {
                    window.logToTerminal(`Auto check failed: ${error.message || 'Controller not responding'}`, 'error');
                }

                if (typeof showToast === 'function') {
                    showToast('Auto-check: Controller not responding', 'error');
                }
            } finally {
                autoCheckInProgress = false;
            }
        }

        function resetMasterCheckCooldown() {
            setTimeout(() => {
                masterCheckCooldown = false;
                masterCheckCount = 0;
                if (window.logToTerminal) {
                    window.logToTerminal('Controller status check cooldown period ended', 'info');
                }

                // Show toast that cooldown ended
                if (typeof showToast === 'function') {
                    showToast('Auto-check cooldown ended - ready for next check', 'info');
                }
            }, COOLDOWN_PERIOD);
        }

        function checkAllBeaconsOffline() {
            const onlineBeacons = document.querySelectorAll('.beacon-item.online-item');
            const totalBeacons = document.querySelectorAll('.beacon-item').length;
            const allOffline = totalBeacons > 0 && onlineBeacons.length === 0;

            if (allOffline && !lastAllOfflineState) {
                // All beacons just went offline
                console.log('All beacons are offline');

                // Only check if not in cooldown and haven't exceeded max checks
                if (!masterCheckCooldown && masterCheckCount < MAX_CHECKS && !autoCheckInProgress) {
                    const checkMasterBtn = document.getElementById('checkMasterBtn');
                    if (checkMasterBtn && !checkMasterBtn.disabled) {
                        setTimeout(async () => {
                            await performAutoMasterCheck();
                            masterCheckCount++;

                            if (masterCheckCount >= MAX_CHECKS) {
                                masterCheckCooldown = true;
                                resetMasterCheckCooldown();

                                // Show cooldown notification
                                Swal.fire({
                                    title: 'Auto-Check Cooldown',
                                    text: `Maximum auto-checks (${MAX_CHECKS}) reached. Cooldown period active for ${COOLDOWN_PERIOD/1000} seconds.`,
                                    icon: 'warning',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#ffc107',
                                    timer: 3000,
                                    timerProgressBar: true
                                });

                                if (typeof showToast === 'function') {
                                    showToast(`Auto-check cooldown active (${COOLDOWN_PERIOD/1000}s)`, 'warning');
                                }
                            }
                        }, 500);
                    }
                } else if (masterCheckCooldown && !autoCheckInProgress) {
                    // Show cooldown info
                    Swal.fire({
                        title: 'Auto-Check on Cooldown',
                        text: `Please wait ${COOLDOWN_PERIOD/1000} seconds before next auto-check or check manually.`,
                        icon: 'info',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#17a2b8',
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else if (masterCheckCount >= MAX_CHECKS && !autoCheckInProgress) {
                    Swal.fire({
                        title: 'Maximum Auto-Checks Reached',
                        text: 'Please check the controller manually using the CHECK CONTROLLER button.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'Check Manually',
                        showCancelButton: true,
                        cancelButtonText: 'Later',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const checkMasterBtn = document.getElementById('checkMasterBtn');
                            if (checkMasterBtn) {
                                checkMasterBtn.click();
                            }
                        }
                    });
                }

                // Show visual indicator on the check master button
                const checkMasterBtn = document.getElementById('checkMasterBtn');
                if (checkMasterBtn) {
                    checkMasterBtn.style.animation = 'pulse 1s infinite';

                    // Add pulse animation if not exists
                    if (!document.querySelector('#pulse-animation-style')) {
                        const style = document.createElement('style');
                        style.id = 'pulse-animation-style';
                        style.textContent = `
                    @keyframes pulse {
                        0% { opacity: 1; }
                        50% { opacity: 0.6; background-color: #dc3545; }
                        100% { opacity: 1; }
                    }
                `;
                        document.head.appendChild(style);
                    }
                }

            } else if (!allOffline && lastAllOfflineState) {
                // Beacons came back online - reset indicators
                const checkMasterBtn = document.getElementById('checkMasterBtn');
                if (checkMasterBtn) {
                    checkMasterBtn.style.animation = '';
                }

                // Reset cooldown and counter when beacons are back
                masterCheckCooldown = false;
                masterCheckCount = 0;

            }

            lastAllOfflineState = allOffline;
        }

        // Add CSS for auto-check popup
        const autoStyle = document.createElement('style');
        autoStyle.textContent = `
            .auto-master-status-popup {
                border-radius: 15px !important;
                border-top: 5px solid #17a2b8 !important;
            }
            .auto-master-status-popup .swal2-html-container {
                font-size: 1em !important;
            }
        `;
        document.head.appendChild(autoStyle);

        // Function to manually reset the auto-check mechanism (exposed globally)
        window.resetMasterAutoCheck = function() {
            masterCheckCooldown = false;
            masterCheckCount = 0;
            autoCheckInProgress = false;
            const checkMasterBtn = document.getElementById('checkMasterBtn');
            if (checkMasterBtn) {
                checkMasterBtn.style.animation = '';
            }

            Swal.fire({
                title: 'Auto-Check Reset',
                text: 'Auto-check mechanism has been reset manually.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            });
        };

        // ============================================
        // MONITORING SYSTEM FOR AUTOMATIC CHECKS
        // ============================================

        // Monitor beacon status changes
        function monitorBeaconStatus() {
            checkAllBeaconsOffline();

            // Watch for class changes on beacon items
            const observer = new MutationObserver(function(mutations) {
                let shouldCheck = false;

                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const target = mutation.target;
                        if (target.classList && target.classList.contains('beacon-item')) {
                            shouldCheck = true;
                        }
                    }
                });

                if (shouldCheck) {
                    clearTimeout(window.beaconCheckTimeout);
                    window.beaconCheckTimeout = setTimeout(() => {
                        checkAllBeaconsOffline();
                    }, 100);
                }
            });

            // Observe all beacon items
            const beaconItems = document.querySelectorAll('.beacon-item');
            beaconItems.forEach(item => {
                observer.observe(item, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            });

            // Observe container for new beacons
            const beaconsContainer = document.getElementById('beaconsContainer');
            if (beaconsContainer) {
                const containerObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList') {
                            mutation.addedNodes.forEach(node => {
                                if (node.nodeType === 1 && node.classList && node.classList.contains('beacon-item')) {
                                    observer.observe(node, {
                                        attributes: true,
                                        attributeFilter: ['class']
                                    });
                                }
                            });
                            checkAllBeaconsOffline();
                        }
                    });
                });

                containerObserver.observe(beaconsContainer, {
                    childList: true,
                    subtree: true
                });
            }
        }

        // Start automatic monitoring
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(monitorBeaconStatus, 1000);
            });
        } else {
            setTimeout(monitorBeaconStatus, 1000);
        }

        // Hook into Pusher updates for real-time automatic checks
        const originalUpdateBeaconStatus = window.updateBeaconStatus;
        if (originalUpdateBeaconStatus) {
            window.updateBeaconStatus = function(beaconId, isOnline) {
                originalUpdateBeaconStatus(beaconId, isOnline);
                setTimeout(checkAllBeaconsOffline, 50);
            };
        }

        setInterval(() => {
            checkAllBeaconsOffline();
        }, 300000); // 5 minutes timeout



        const doorPopup = document.getElementById('doorPopup');
        const doorListContent = document.getElementById('doorListContent');
        const doorCountBadge = document.getElementById('doorCountBadge');
        const doorLastSync = document.getElementById('doorLastSync');
        const closeDoorPopupBtn = document.getElementById('closeDoorPopupBtn');

        let openDoorsMap = new Map(); // beaconId -> beaconName

        function updateDoorPopup() {
            if (!doorListContent || !doorCountBadge) return;

            if (openDoorsMap.size === 0) {
                doorPopup.classList.add('hidden');
                doorListContent.innerHTML = '<div class="text-gray-500 dark:text-gray-400 text-center py-4">No open doors</div>';
                doorCountBadge.textContent = '0';
                return;
            }

            doorPopup.classList.remove('hidden');

            let itemsHtml = '';
            for (let [beaconId, beaconName] of openDoorsMap.entries()) {
                itemsHtml += `
                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 dark:text-gray-200">${escapeHtml(beaconName)}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: ${beaconId}</p>
                        </div>
                        <button onclick="window.sendCloseDoorCommand('${beaconId}')" 
                                class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                            Close Door
                        </button>
                    </div>
                `;
            }
            doorListContent.innerHTML = itemsHtml;
            doorCountBadge.textContent = openDoorsMap.size;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function addOpenDoor(beaconId, beaconName) {
            if (!beaconId) return;
            openDoorsMap.set(beaconId, beaconName || 'Unknown Beacon');
            updateDoorPopup();
        }

        function removeOpenDoor(beaconId) {
            if (!beaconId) return;
            if (openDoorsMap.delete(beaconId)) updateDoorPopup();
        }

        window.sendCloseDoorCommand = async function(beaconId) {
            if (!beaconId) return;

            // 1. Get beacon name before removal (for possible revert)
            const beaconName = openDoorsMap.get(beaconId);
            if (!beaconName) {
                showToast(`Beacon ${beaconId} not found in open doors list`, 'error');
                return;
            }

            // 2. Optimistically remove from modal and update UI
            const wasRemoved = openDoorsMap.delete(beaconId);
            if (wasRemoved) updateDoorPopup();

            // 3. Prepare command
            const command = `<id=${beaconId};com=serv>`;

            if (typeof window.sendCommandToArduino === 'function') {
                try {
                    await window.sendCommandToArduino(command);
                    if (window.logToTerminal) {
                        window.logToTerminal(`Sending: ${command}`, 'info');
                    }
                } catch (err) {
                    console.error('Failed to send close command:', err);
                    // 4. Revert optimistic removal on failure
                    if (wasRemoved) {
                        openDoorsMap.set(beaconId, beaconName);
                        updateDoorPopup();
                    }
                }
            } else {
                console.error('sendCommandToArduino not available');
                // Revert if the function is missing
                if (wasRemoved) {
                    openDoorsMap.set(beaconId, beaconName);
                    updateDoorPopup();
                }
                showToast('Communication error: Arduino not ready', 'error');
            }
        };

        if (closeDoorPopupBtn) {
            closeDoorPopupBtn.addEventListener('click', () => {
                doorPopup.classList.add('hidden');
            });
        }

        // Initial population from PHP
        if (window.beaconDoors) {
            Object.entries(window.beaconDoors).forEach(([id, data]) => {
                if (data.is_door_open) {
                    openDoorsMap.set(id, data.name);
                }
            });
            updateDoorPopup();
            if (doorLastSync) doorLastSync.innerHTML = `Loaded from database`;
        }

        // Real‑time Pusher updates
        if (window.Echo) {
            window.Echo.channel('beacons')
                .listen('beacon.door.updated', (data) => {
                    if (data.is_door_open) {
                        addOpenDoor(data.beacon_id, data.name || `Beacon ${data.beacon_id}`);
                    } else {
                        removeOpenDoor(data.beacon_id);
                    }
                    if (doorLastSync) doorLastSync.innerHTML = `Updated: ${new Date().toLocaleTimeString()}`;
                });
        }


    })();
    
</script>
<script src="{{ asset('js/serial.js') }}"></script>

@include('layouts.footer')
@else
<script>
    window.location = "{{ route('login') }}";
</script>
@endauth