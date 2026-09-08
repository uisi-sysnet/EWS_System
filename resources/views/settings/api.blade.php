@auth
@include('layouts.header')
@include('layouts.topbar')

<div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-300">
    <div class="pt-16 md:pt-20 px-4 sm:px-6 lg:px-8">
        <div class="py-6 max-w-screen-2xl mx-auto">

            <!-- Header with back button -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="returnToController()" title="Back to Homepage"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 transition-colors focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 shrink-0 text-prussian-blue dark:text-lavender-grey">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-prussian-blue dark:text-white">API Connections</h1>
                        <p class="mt-1 text-sm text-slate-grey dark:text-lavender-grey">
                            Manage your external API endpoints and keys.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">

                <!-- Left panel: Saved Connections + Form -->
                <div class="bg-white dark:bg-gray-800 shadow-xl ring-1 ring-slate-grey/20 dark:ring-blue-slate/30 rounded-2xl overflow-hidden flex flex-col h-[calc(100vh-280px)] min-h-[530px]">

                    <!-- Saved Connections header: simplified to match other cards -->
                    <div class="px-5 py-2 border-b border-slate-grey/20 dark:border-blue-slate/30 flex items-center justify-between">
                        <h2 class="text-base font-semibold text-prussian-blue dark:text-white">Saved Connections</h2>
                        <span class="text-xs text-slate-grey dark:text-lavender-grey" id="connectionCount">0</span>
                    </div>

                    <!-- Cards container with consistent padding -->
                    <div class="p-5 md:p-6 border-b border-slate-grey/20 dark:border-blue-slate/30">
                        <div id="cardsContainer" class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scroll">
                            <div class="text-center text-slate-grey dark:text-lavender-grey py-8 text-sm">
                                No saved connections yet. Add one below.
                            </div>
                        </div>
                    </div>

                    <!-- Form area with consistent padding and compact inputs -->
                    <div class="flex-1 p-5 md:p-6 overflow-y-auto custom-scroll">
                        <div class="space-y-4">
                            <!-- Connection Name -->
                            <div class="space-y-1">
                                <label for="connectionName" class="block text-xs text-prussian-blue dark:text-lavender-grey">Connection Name</label>
                                <input type="text" id="connectionName"
                                    class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50"
                                    placeholder="e.g. Siren">
                            </div>

                            <!-- Base URL -->
                            <div class="space-y-1">
                                <label for="baseUrl" class="block text-xs text-prussian-blue dark:text-lavender-grey">Base URL</label>
                                <input type="url" id="baseUrl" value="https://172.0.6.189:44383/"
                                    class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md font-mono focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50"
                                    placeholder="https://example.com/api/">
                            </div>

                            <!-- API Key section -->
                            <div class="space-y-3">
                                <div class="space-y-1">
                                    <label for="apiKeyName" class="block text-xs text-prussian-blue dark:text-lavender-grey">Key</label>
                                    <input type="text" id="apiKeyName" value="X-API-KEY"
                                        class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md font-mono uppercase focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50"
                                        placeholder="e.g. X-API-KEY or api_key">
                                </div>

                                <div class="space-y-1">
                                    <label for="apiKeyValue" class="block text-xs text-prussian-blue dark:text-lavender-grey">Value</label>
                                    <div class="relative">
                                        <input type="password" id="apiKeyValue" value="c0e9fc32-f00f-4c97-b779-57f7ed2b1d83"
                                            class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-700 border border-slate-grey/30 dark:border-blue-slate/40 rounded-md font-mono focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:border-regal-navy dark:focus:border-prussian-blue outline-none transition-all text-prussian-blue dark:text-lavender-grey placeholder-slate-grey dark:placeholder-lavender-grey/50 pr-20"
                                            placeholder="Paste your API key...">
                                        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex gap-2">
                                            <button type="button" class="reveal-btn text-slate-grey hover:text-prussian-blue dark:text-lavender-grey dark:hover:text-white transition p-1" data-target="apiKeyValue" title="Toggle visibility">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button type="button" class="copy-btn text-regal-navy hover:text-blue-slate dark:text-prussian-blue dark:hover:text-blue-slate transition p-1" data-target="apiKeyValue" title="Copy value">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Placement radios -->
                                <div class="space-y-1">
                                    <label class="block text-xs text-prussian-blue dark:text-lavender-grey">Add to</label>
                                    <div class="flex gap-4 pt-1">
                                        <label class="flex items-center gap-1.5 cursor-pointer text-sm text-prussian-blue dark:text-lavender-grey">
                                            <input type="radio" name="apiKeyPlacement" value="header" checked class="w-3.5 h-3.5 text-regal-navy dark:text-prussian-blue focus:ring-regal-navy dark:focus:ring-prussian-blue">
                                            <span>Header</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer text-sm text-prussian-blue dark:text-lavender-grey">
                                            <input type="radio" name="apiKeyPlacement" value="query" class="w-3.5 h-3.5 text-regal-navy dark:text-prussian-blue focus:ring-regal-navy dark:focus:ring-prussian-blue">
                                            <span>Query Params</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Action buttons: consistent sizing -->
                            <div class="flex flex-wrap gap-3 pt-4">
                                <button id="btnSaveConnection"
                                    class="px-6 py-2.5 bg-lime-600 hover:bg-lime-700 text-white text-sm font-medium rounded-md shadow transition focus:ring-2 focus:ring-lime-500 focus:outline-none">
                                    Save Connection
                                </button>
                                <button id="btnFetchNow"
                                    class="flex-1 px-6 py-2.5 bg-regal-navy dark:bg-prussian-blue hover:bg-blue-slate dark:hover:bg-blue-slate text-white text-sm font-medium rounded-md shadow transition focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                                    Fetch Now
                                </button>
                                <button id="btnDownload"
                                    class="hidden flex-1 px-6 py-2.5 bg-twilight-indigo hover:bg-blue-slate text-white text-sm font-medium rounded-md shadow transition focus:ring-2 focus:ring-twilight-indigo focus:outline-none">
                                    Download Response
                                </button>
                                <button id="btnCopyResponse"
                                    class="hidden flex-1 px-6 py-2.5 bg-slate-grey hover:bg-blue-slate text-white text-sm font-medium rounded-md shadow transition focus:ring-2 focus:ring-slate-grey focus:outline-none">
                                    Copy Response
                                </button>
                            </div>

                            <!-- Clear button -->
                            <div class="flex justify-end pt-2">
                                <button id="btnClear" type="button"
                                    class="px-3 py-1.5 text-xs text-slate-grey dark:text-lavender-grey hover:text-munti-red-0 dark:hover:text-munti-red-0 transition flex items-center gap-1.5 rounded-md hover:bg-slate-grey/10 dark:hover:bg-blue-slate/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear all fields
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right panel: Response area -->
                <div class="bg-white dark:bg-gray-800 shadow-xl ring-1 ring-slate-grey/20 dark:ring-blue-slate/30 rounded-2xl overflow-hidden flex flex-col h-[calc(100vh-280px)] min-h-[530px]">
                    <!-- Response header: simplified border-bottom only -->
                    <div class="px-5 py-2 border-b border-slate-grey/20 dark:border-blue-slate/30">
                        <span class="text-sm font-semibold text-prussian-blue dark:text-white">Response</span>
                    </div>

                    <!-- Response content with consistent padding -->
                    <div id="logContainer"
                        class="flex-1 p-5 md:p-6 font-mono text-sm bg-gray-800 dark:bg-gray-800 text-lavender-grey overflow-y-auto whitespace-pre-wrap break-words leading-relaxed custom-scroll">
                        <!-- Response content will appear here -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include('layouts.footer')

<script>
    (function() {
        const els = {
            log: document.getElementById('logContainer'),
            fetchBtn: document.getElementById('btnFetchNow'),
            dlBtn: document.getElementById('btnDownload'),
            copyResponseBtn: document.getElementById('btnCopyResponse'),
            clearBtn: document.getElementById('btnClear'),
            saveBtn: document.getElementById('btnSaveConnection'),
            connectionName: document.getElementById('connectionName'),
            baseUrl: document.getElementById('baseUrl'),
            apiKeyName: document.getElementById('apiKeyName'),
            apiKeyValue: document.getElementById('apiKeyValue'),
            apiKeyPlacementRadios: document.querySelectorAll('input[name="apiKeyPlacement"]'),
            cardsContainer: document.getElementById('cardsContainer'),
            connectionCount: document.getElementById('connectionCount'),
        };

        let lastResponseText = null;
        let currentConnectionId = null;
        let debounceTimer = null;
        let connections = [];

        const routes = {
            save: '{{ route("settings.api.auto-register") }}',
            last: '{{ route("settings.api.last-connection") }}',
            mark: '{{ route("settings.api.mark-used") }}',
            list: '{{ route("settings.api.connections") }}',
            delete: '{{ route("settings.api.delete") }}',
        };

        const csrfToken = '{{ csrf_token() }}';

        function getApiKeyPlacement() {
            return document.querySelector('input[name="apiKeyPlacement"]:checked').value;
        }

        function log(message, className = '') {
            const div = document.createElement('div');
            div.className = `mb-1 ${className}`;
            div.textContent = message;
            els.log.appendChild(div);
            els.log.scrollTop = els.log.scrollHeight;
        }

        function clearLog() {
            els.log.innerHTML = '';
        }

        function showInitialMessage() {
            clearLog();
            log('To access siren endpoints, ensure you are connected to the "Siren" API name.', 'text-munti-red-0 font-bold italic text-sm');
            log('Ready. Set your API key and click Fetch to access the base URL.', 'text-gray-500 italic text-sm');

        }

        function enableButtons(hasData = false) {
            els.dlBtn.classList.toggle('hidden', !hasData);
            els.copyResponseBtn.classList.toggle('hidden', !hasData);
        }

        document.querySelectorAll('.reveal-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = btn.getAttribute('data-target');
                const target = document.getElementById(targetId);
                if (!target) return;
                const type = target.getAttribute('type');
                if (type === 'password') {
                    target.setAttribute('type', 'text');
                    btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l6.59 6.59m7.322 1.322l-3.29-3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';
                } else {
                    target.setAttribute('type', 'password');
                    btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';
                }
            });
        });

        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const targetId = btn.getAttribute('data-target');
                const target = document.getElementById(targetId);
                if (!target) return;
                const text = target.value.trim();
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    log('Copied to clipboard', 'text-green-500 text-sm');
                } catch {
                    log('Failed to copy', 'text-munti-red-0 text-sm');
                }
            });
        });

        els.clearBtn.addEventListener('click', () => {
            els.connectionName.value = '';
            els.baseUrl.value = 'https://172.0.6.189:44383/';
            els.apiKeyName.value = 'X-API-KEY';
            els.apiKeyValue.value = '';
            document.querySelector('input[name="apiKeyPlacement"][value="header"]').checked = true;
            currentConnectionId = null;
            lastResponseText = null;
            enableButtons(false);
            showInitialMessage();
        });

        function debounceFetch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchData, 900);
        }

        [els.apiKeyName, els.apiKeyValue, els.baseUrl].forEach(el => {
            if (el) {
                el.addEventListener('input', debounceFetch);
                el.addEventListener('keypress', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);
                        fetchData();
                    }
                });
            }
        });

        els.apiKeyPlacementRadios.forEach(radio => {
            radio.addEventListener('change', debounceFetch);
        });

        els.fetchBtn.addEventListener('click', () => {
            clearTimeout(debounceTimer);
            fetchData();
        });

        els.saveBtn.addEventListener('click', saveConnection);

        els.dlBtn.addEventListener('click', () => {
            if (!lastResponseText) return;
            const blob = new Blob([lastResponseText], {
                type: 'text/plain'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `response-${new Date().toISOString().slice(0, 10)}.txt`;
            a.click();
            URL.revokeObjectURL(url);
        });

        els.copyResponseBtn.addEventListener('click', async () => {
            if (!lastResponseText) return;
            try {
                await navigator.clipboard.writeText(lastResponseText);
                log('Response copied to clipboard', 'text-green-500 text-sm');
            } catch {
                log('Failed to copy response', 'text-munti-red-0 text-sm');
            }
        });

        async function loadConnectionsList() {
            try {
                const res = await fetch(routes.list, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Failed to load connections');
                connections = await res.json();
                renderCards(connections);
                els.connectionCount.textContent = connections.length;
            } catch (err) {
                console.error('Error loading connections:', err);
                els.cardsContainer.innerHTML = '<div class="text-center text-munti-red-0 py-4">Failed to load connections.</div>';
            }
        }

        function formatLastUsed(dateString) {
            if (!dateString) return 'Never used';

            // Parse the UTC date string from the server
            const utcDate = new Date(dateString + ' UTC'); // ensure it's treated as UTC

            // Convert to Asia/Manila time (UTC+8) by adding 8 hours
            const manilaDate = new Date(utcDate.getTime() + (8 * 60 * 60 * 1000));

            const now = new Date();

            // Also convert current time to Manila for accurate comparison
            const nowManila = new Date(now.getTime() + (8 * 60 * 60 * 1000));

            const diffMs = nowManila - manilaDate;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins === 1 ? '' : 's'} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours === 1 ? '' : 's'} ago`;
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;

            // For older dates, show a short date in Manila time
            return manilaDate.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                timeZone: 'Asia/Manila' // ensures formatting also respects Manila
            });
        }

        function renderCards(connections) {
            if (!connections.length) {
                els.cardsContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-8 text-sm">No saved connections yet. Add one below.</div>';
                return;
            }

            // Find the most recently used connection (the one with the latest last_used_at)
            let mostRecentId = null;
            let latestDate = null;
            connections.forEach(conn => {
                if (conn.last_used_at) {
                    const d = new Date(conn.last_used_at);
                    if (!latestDate || d > latestDate) {
                        latestDate = d;
                        mostRecentId = conn.id;
                    }
                }
            });

            let html = '';
            connections.forEach(conn => {
                const maskedKey = conn.header_value ? '••••••••' + conn.header_value.slice(-4) : 'Not set';
                const displayName = conn.name || conn.endpoint_url;
                const lastUsedFormatted = formatLastUsed(conn.last_used_at);
                const isActive = (conn.id === mostRecentId);
                // Active card gets a blue border and a small badge
                const cardClass = isActive ?
                    'bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border-2 border-blue-500 dark:border-blue-400 hover:shadow-md transition-shadow' :
                    'bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow';

                html += `
            <div class="${cardClass}" data-connection-id="${conn.id}">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate" title="${displayName}">${displayName}</h3>
                            ${isActive ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Active</span>' : ''}
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">${conn.endpoint_url}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span class="font-mono">${conn.header_name}:</span> ${maskedKey}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Last used: <span class="font-mono ${isActive ? 'text-blue-600 dark:text-blue-400 font-semibold' : ''}">${lastUsedFormatted}</span>
                        </p>
                    </div>
                    <div class="flex gap-1 shrink-0">
                        <button class="load-card-btn p-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition" title="Load and fetch" data-id="${conn.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </button>
                        <button class="delete-card-btn p-2 text-munti-red-0 hover:text-munti-red-0/90 dark:text-munti-red-0 dark:hover:text-munti-red-0/90 rounded-full hover:bg-munti-red-0/50 dark:hover:bg-munti-red-0/30 transition" title="Delete" data-id="${conn.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
            });
            els.cardsContainer.innerHTML = html;

            // Re-attach event listeners
            document.querySelectorAll('.load-card-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.getAttribute('data-id');
                    loadConnectionById(id);
                });
            });

            document.querySelectorAll('.delete-card-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.getAttribute('data-id');
                    deleteConnectionById(id);
                });
            });
        }

        async function loadConnectionById(id) {
            try {
                const res = await fetch(routes.list, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Failed to load connection details');
                const conns = await res.json();
                const conn = conns.find(c => c.id == id);
                if (!conn) throw new Error('Connection not found');

                els.connectionName.value = conn.name || '';
                els.baseUrl.value = conn.endpoint_url;
                els.apiKeyName.value = conn.header_name;
                els.apiKeyValue.value = conn.header_value;
                document.querySelector('input[name="apiKeyPlacement"][value="header"]').checked = true;
                currentConnectionId = conn.id;
                log(`Loaded connection: ${conn.name || conn.endpoint_url}`, 'text-blue-500 text-sm');

            } catch (err) {
                log(`Error loading connection: ${err.message}`, 'text-munti-red-0 text-sm');
            }
        }

        async function deleteConnectionById(id) {
            if (!confirm('Are you sure you want to delete this connection?')) return;
            try {
                const res = await fetch(routes.delete, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                });
                if (!res.ok) throw new Error('Delete failed');
                const data = await res.json();
                log(data.message, 'text-green-500');
                if (currentConnectionId == id) {
                    // cleared current
                    els.clearBtn.click();
                }
                await loadConnectionsList();
            } catch (err) {
                log(`Error deleting connection: ${err.message}`, 'text-munti-red-0');
            }
        }

        const deleteGlobalBtn = document.getElementById('btnDeleteConnection');
        if (deleteGlobalBtn) {
            deleteGlobalBtn.addEventListener('click', async () => {
                if (!currentConnectionId) {
                    log('No connection selected to delete', 'text-munti-yellow-0');
                    return;
                }
                await deleteConnectionById(currentConnectionId);
            });
        }

        async function saveConnection() {
            const name = els.connectionName.value.trim();
            const url = els.baseUrl.value.trim();
            const keyName = els.apiKeyName.value.trim();
            const keyValue = els.apiKeyValue.value.trim();

            if (!url || !keyName || !keyValue) {
                log('Base URL, Key name and value are required', 'text-munti-red-0');
                return;
            }

            try {
                const payload = {
                    endpoint_url: url,
                    header_name: keyName,
                    header_value: keyValue,
                    name: name || null,
                };

                const res = await fetch(routes.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();

                if (data.status === 'saved') {
                    log(`Connection saved as "${data.name}"`, 'text-green-500');
                    currentConnectionId = data.id;
                    await loadConnectionsList();
                } else if (data.status === 'already_exists') {
                    log('Connection already exists — updated last used time.', 'text-amber-500');
                    currentConnectionId = data.id;
                    await loadConnectionsList();
                    await fetch(routes.mark, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            connection_id: data.id
                        })
                    });
                }
            } catch (err) {
                log(`Failed to save connection: ${err.message}`, 'text-munti-red-0');
            }
        }

        async function fetchData() {
            const url = els.baseUrl.value.trim();
            const keyName = els.apiKeyName.value.trim();
            const keyValue = els.apiKeyValue.value.trim();

            if (!url || !keyName || !keyValue) {
                return log('Base URL, API Key name and value are required', 'text-munti-red-0');
            }

            let finalUrl = url;
            const headers = {
                'Accept': '*/*'
            };

            const placement = getApiKeyPlacement();
            if (placement === 'header') {
                headers[keyName] = keyValue;
                log(`Header: ${keyName}: …${keyValue.slice(-6)}`, 'text-gray-400 text-xs');
            } else {
                const separator = finalUrl.includes('?') ? '&' : '?';
                finalUrl += `${separator}${encodeURIComponent(keyName)}=${encodeURIComponent(keyValue)}`;
                log(`Query: ?${keyName}=…${keyValue.slice(-6)}`, 'text-gray-400 text-xs');
            }

            clearLog();
            log(`GET ${finalUrl}`, 'text-gray-400 text-xs');

            let response, responseText;
            try {
                response = await fetch(finalUrl, {
                    method: 'GET',
                    headers: headers
                });

                responseText = await response.text();
                lastResponseText = responseText;

                const contentType = response.headers.get('content-type') || '';
                log(`Status: ${response.status} ${response.statusText}`, response.ok ? 'text-green-500' : 'text-munti-yellow-0');
                log(`Content-Type: ${contentType}`, 'text-gray-400 text-xs');
                log(`Size: ${responseText.length} characters`, 'text-gray-400 text-xs');
                log(new Date().toLocaleString(), 'text-xs text-gray-500 mb-3');

                if (contentType.includes('application/json')) {
                    try {
                        const json = JSON.parse(responseText);
                        const pre = document.createElement('pre');
                        pre.className = 'bg-gray-900/80 p-4 rounded-xl overflow-x-auto text-gray-200 text-xs border border-gray-700 mt-3 max-h-[60vh] overflow-y-auto custom-scroll';
                        pre.textContent = JSON.stringify(json, null, 2);
                        els.log.appendChild(pre);
                    } catch {
                        const pre = document.createElement('pre');
                        pre.className = 'bg-gray-900/80 p-4 rounded-xl overflow-x-auto text-gray-200 text-xs border border-gray-700 mt-3 max-h-[60vh] overflow-y-auto custom-scroll';
                        pre.textContent = responseText;
                        els.log.appendChild(pre);
                    }
                } else {
                    const pre = document.createElement('pre');
                    pre.className = 'bg-gray-900/80 p-4 rounded-xl overflow-x-auto text-gray-200 text-xs border border-gray-700 mt-3 max-h-[60vh] overflow-y-auto custom-scroll';
                    pre.textContent = responseText;
                    els.log.appendChild(pre);
                }

                enableButtons(true);
            } catch (err) {
                log(`Error: ${err.message}`, 'text-munti-red-0 font-medium');
                log('Possible causes: wrong key, CORS, SSL cert, firewall, server down', 'text-munti-red-0/90 text-sm mt-2');
                lastResponseText = null;
                enableButtons(false);
            } finally {
                await saveConnectionAfterFetch(url, keyName, keyValue);
            }
        }

        async function saveConnectionAfterFetch(url, keyName, keyValue) {
            const name = els.connectionName.value.trim() || null;

            try {
                const payload = {
                    endpoint_url: url,
                    header_name: keyName,
                    header_value: keyValue,
                    name: name,
                };

                const res = await fetch(routes.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();

                if (data.status === 'saved') {
                    log(`Connection saved as "${data.name}"`, 'text-blue-400 text-sm mt-2');
                    currentConnectionId = data.id;
                    await loadConnectionsList();
                } else if (data.status === 'already_exists') {
                    log('Connection already exists — updating last used time...', 'text-amber-500 text-sm mt-2');
                    currentConnectionId = data.id;
                    await loadConnectionsList();

                    await fetch(routes.mark, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            connection_id: data.id
                        })
                    });

                    log('Timestamp updated', 'text-blue-400 text-xs');
                }
            } catch (err) {
                console.warn('Failed to save connection:', err);
            }
        }

        window.addEventListener('load', async () => {
            showInitialMessage();
            await loadConnectionsList();

            log('Loading last connection...', 'text-gray-500 text-sm italic');

            try {
                const res = await fetch(routes.last, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();

                if (data.success) {
                    els.baseUrl.value = data.endpoint_url;
                    els.apiKeyName.value = data.header_name || 'X-API-KEY';
                    if (data.header_value) els.apiKeyValue.value = data.header_value;
                    els.connectionName.value = data.name || '';

                    log('Loaded last used API connection.', 'text-blue-500 italic text-sm');
                } else {
                    log('No previous API connection found.', 'text-gray-500 italic text-sm');
                }
            } catch (err) {
                log(`Failed to load last connection: ${err.message}`, 'text-munti-red-0 text-sm');
            }
        });

    })();
</script>
</div>

@else
<script>
    window.location = "{{ route('login') }}";
</script>
@endauth
