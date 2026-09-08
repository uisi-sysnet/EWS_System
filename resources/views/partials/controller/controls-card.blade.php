<div class="card bg-white dark:bg-gray-900 backdrop-blur-md border border-slate-grey/20 dark:border-munti-blue-1/30 rounded-md shadow-sm dark:shadow-none p-2 flex flex-col gap-2">
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 py-2 px-1">
        <button type="button" id="activateBtn"
            class="group relative w-full max-w-sm overflow-hidden rounded-lg px-8 py-4 text-lg font-bold tracking-wide transition-all duration-150 ease-in-out cursor-pointer shadow-[0_6px_0_0_#3e630d] hover:shadow-[0_3px_0_0_#3e630d] hover:translate-y-[3px] active:shadow-[0_1px_0_0_#3e630d] active:translate-y-[5px] border border-lime-500/40 bg-lime-300 hover:bg-lime-200 munti-black-0 border-lime-500/70 disabled:text-gray-800 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:shadow-[0_6px_0_0_#4b5563] disabled:border-gray-500/40">
            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
            <span class="relative z-10">ACTIVATE</span>
            <span class="absolute inset-0 rounded-lg border border-lime-400/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
        </button>
        <button type="button" id="deactivateBtn" disabled
            class="group relative w-full max-w-sm overflow-hidden rounded-lg px-8 py-4 text-lg font-bold tracking-wide transition-all duration-150 ease-in-out cursor-pointer shadow-[0_6px_0_0_#7f1d1d] hover:shadow-[0_3px_0_0_#7f1d1d] hover:translate-y-[3px] active:shadow-[0_1px_0_0_#7f1d1d] active:translate-y-[5px] border border-munti-red-0/40 bg-munti-red-0 hover:bg-munti-red-0/90 munti-black-0 border-munti-red-0/60 disabled:text-gray-800 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:shadow-[0_6px_0_0_#4b5563] disabled:border-gray-500/40">
            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
            <span class="relative z-10">DEACTIVATE</span>
            <span class="absolute inset-0 rounded-lg border border-munti-red-0/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
        </button>
    </div>
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-1.5">
            <span class="text-munti-blue-0 dark:text-munti-blue-1 font-medium whitespace-nowrap">Volume</span>
            <input type="range" min="0" max="100" value="0" class="flex-1 accent-munti-blue-0 dark:accent-munti-blue-0 h-1 min-w-[80px]" id="vol">
            <input type="text" value="0" inputmode="numeric" pattern="[0-9]*" class="text-munti-blue-0 dark:text-munti-blue-1 font-medium text-right w-8 bg-transparent p-0 border-none focus:outline-none focus:ring-0" id="vol-val">
        </div>
        <div class="flex gap-2">
            <button id="clear-all-selections" class="flex-1 flex items-center justify-center px-5 py-2 font-medium uppercase text-white rounded-md bg-gradient-to-b from-munti-blue-0 to-munti-blue-0 shadow-[0_6px_0_0_#001845] hover:shadow-[0_3px_0_0_#001845] active:shadow-[0_1px_0_0_#001845] transition-all">
                Clear All
            </button>
            <button id="checkMasterBtn" class="flex-1 flex items-center justify-cen ter px-3 py-2 font-medium uppercase text-white rounded-md bg-gradient-to-b from-munti-blue-0 to-munti-blue-0 shadow-[0_6px_0_0_#001845] hover:shadow-[0_3px_0_0_#001845] active:shadow-[0_1px_0_0_#001845] transition-all">
                Check Controller
            </button>
            <button id="checkBeaconModalBtn" class="flex-1 flex items-center justify-center gap-2 px-5 py-2 font-medium uppercase text-white rounded-md bg-gradient-to-b from-munti-blue-0 to-munti-blue-0 shadow-[0_6px_0_0_#001845] hover:shadow-[0_3px_0_0_#001845] active:shadow-[0_1px_0_0_#001845] transition-all">
                Check Beacons
            </button>
        </div>
    </div>
    <div class="mt-2 p-3 bg-slate-grey/5 dark:bg-blue-slate/10 rounded-md border border-slate-grey/20 dark:border-blue-slate/30">
        <h3 class="font-bold text-munti-blue-0 dark:text-munti-blue-1 mb-2 tracking-wide">DEVICES STATUS</h3>
        <div class="grid grid-cols-2 gap-2">
            <div class="flex items-center gap-2">
                <span class="text-lime-600 dark:text-lime-400 font-semibold">Online Beacon:</span>
                <span class="text-lime-600 dark:text-lime-400" id="online-beacon-count"><?= $beaconCounts['online'] ?? 0 ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-munti-red-0 dark:text-munti-red-0 font-semibold">Offline Beacon:</span>
                <span class="text-munti-red-0 dark:text-munti-red-0" id="offline-beacon-count"><?= $beaconCounts['offline'] ?? 0 ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-lime-600 dark:text-lime-400 font-semibold">Online Siren:</span>
                <span class="text-lime-600 dark:text-lime-400" id="online-siren-count">0</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-munti-red-0 dark:text-munti-red-0 font-semibold">Offline Siren:</span>
                <span class="text-munti-red-0 dark:text-munti-red-0" id="offline-siren-count">0</span>
            </div>
        </div>
    </div>
</div>  






    <script>
        // Utility: Manila timestamp (kept as original, no random)
        window.getManilaTimestamp = function() {
            try {
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('en-US', { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                const parts = formatter.formatToParts(now);
                const year = parts.find(p => p.type === 'year')?.value || '2025';
                const month = parts.find(p => p.type === 'month')?.value || '01';
                const day = parts.find(p => p.type === 'day')?.value || '01';
                const hour = parts.find(p => p.type === 'hour')?.value || '00';
                const minute = parts.find(p => p.type === 'minute')?.value || '00';
                const second = parts.find(p => p.type === 'second')?.value || '00';
                const ampm = parts.find(p => p.type === 'dayPeriod')?.value || 'AM';
                return `${year}-${month}-${day} ${hour}:${minute}:${second} ${ampm}`;
            } catch(e) { return new Date().toLocaleString(); }
        };
        
        function escapeHtml(unsafe) { 
            if(!unsafe) return ''; 
            return unsafe.replace(/[&<>]/g, function(m) { 
                if(m === '&') return '&amp;'; 
                if(m === '<') return '&lt;'; 
                if(m === '>') return '&gt;'; 
                return m;
            });
        }
        
        // ---------- Mock sendCommandToArduino (replace with your actual implementation) ----------
        // This is the bridge to your hardware. For demo purposes it logs to console.
        // In production, replace with WebSocket / serial / API call.
        window.sendCommandToArduino = async function(command) {
            console.log("[CMD]", command);
            // Simulate network delay (but no random data)
            await new Promise(resolve => setTimeout(resolve, 300));
            // You would typically wait for real ACK. Here we just resolve.
            return { success: true };
        };
        
        // ========== 1. REMOVED RANDOM DATA: checkBeaconStatus now only sends command and shows confirmation ==========
        async function checkBeaconStatus(beaconId, beaconName) {
            if(!beaconId) { 
                Swal.fire('Error', 'Invalid beacon ID', 'error'); 
                return; 
            }
            const command = `<id=${beaconId};com=status>`;
            Swal.fire({ 
                title: `Checking: ${beaconName || beaconId}`, 
                text: 'Sending status request to device...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading() 
            });
            try {
                await sendCommandToArduino(command);
                Swal.close();
                // No random simulation — just inform that command was sent.
                // The actual status will appear in the device list when the device responds.
                await Swal.fire({
                    title: `Command Sent`,
                    html: `<div style="text-align:left">
                        <strong>Beacon:</strong> ${escapeHtml(beaconName || beaconId)}<br>
                        <strong>ID:</strong> ${escapeHtml(beaconId)}<br>
                        <strong>Command:</strong> <code>${escapeHtml(command)}</code><br>
                        <strong>Status request transmitted.</strong><br>
                        <span class="text-sm text-gray-500">Device list will update upon response.</span>
                    </div>`,
                    icon: 'info',
                    confirmButtonColor: '#3085d6'
                });
            } catch(e) { 
                Swal.close(); 
                Swal.fire('Error', `Failed to send command: ${e.message}`, 'error'); 
            }
        }
        
        // Helper: wait for a beacon's UI to reflect online status (polling, no random)
        function waitForBeaconOnlineStatus(beaconId, timeoutMs = 1000) {
            return new Promise((resolve) => {
                const startTime = Date.now();
                const interval = setInterval(() => {
                    const beaconEl = document.querySelector(`.beacon-item[data-beacon-id="${beaconId}"]`);
                    if (beaconEl) {
                        const isOnline = beaconEl.classList.contains('online-item');
                        if (isOnline) {
                            clearInterval(interval);
                            resolve(true);
                            return;
                        }
                    }
                    if (Date.now() - startTime >= timeoutMs) {
                        clearInterval(interval);
                        resolve(false);
                    }
                }, 100);
                setTimeout(() => {
                    clearInterval(interval);
                    resolve(false);
                }, timeoutMs);
            });
        }
        
        // Enhanced multi‑check using UI polling (no random, no fake logs)
        async function checkMultipleBeacons(beaconsArray) {
            if (!beaconsArray.length) {
                Swal.fire('No beacons', 'Please select at least one beacon', 'info');
                return;
            }
            const overallStartTime = performance.now();
            let online = 0;
            let offline = 0;
            
            Swal.fire({
                title: 'Checking Beacons',
                html: `<div class="text-lg">Processing ${beaconsArray.length} beacon(s)...</div>
                    <div class="mt-2">Online: 0</div>
                    <div>Offline: 0</div>
                    <div class="mt-2 text-sm text-gray-500">Please wait...</div>`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            for (let i = 0; i < beaconsArray.length; i++) {
                const b = beaconsArray[i];
                const command = `<id=${b.id};com=status>`;
                try {
                    await sendCommandToArduino(command);
                    if (window.logToTerminal) {
                        window.logToTerminal(`Sending: ${command}`, 'info');
                    }
                } catch (err) {
                    offline++;
                    if (window.logToTerminal) {
                        window.logToTerminal(`Failed to send to beacon ${b.id}: ${err.message}`, 'error');
                    }
                    Swal.update({
                        html: `<div class="text-lg">Processing ${beaconsArray.length} beacon(s)...</div>
                            <div class="mt-2">Online: ${online}</div>
                            <div>Offline: ${offline}</div>
                            <div class="mt-2 text-sm text-gray-500">${i+1}/${beaconsArray.length} completed</div>`
                    });
                    continue;
                }
                
                const isOnline = await waitForBeaconOnlineStatus(b.id, 1000 * 10);
                if (isOnline) {
                    online++;
                } else {
                    offline++;
                }
                
                Swal.update({
                    html: `<div class="text-lg">Processing ${beaconsArray.length} beacon(s)...</div>
                        <div class="mt-2">Online: ${online}</div>
                        <div>Offline: ${offline}</div>
                        <div class="mt-2 text-sm text-gray-500">${i+1}/${beaconsArray.length} completed</div>`
                });
            }
            const overallDuration = ((performance.now() - overallStartTime) / 1000).toFixed(2);
            Swal.fire({
                title: 'Beacon Check Complete',
                html: `<div class="text-lg">${beaconsArray.length} beacon(s) processed</div>
                    <div>Online: ${online}<br>
                    Offline: ${offline}</div>
                    <div class="mt-3 text-sm text-gray-500">Duration: ${overallDuration} seconds</div>`,
                icon: offline === 0 ? 'success' : 'warning',
                confirmButtonText: 'OK'
            });
        }

        // ========== MODAL SYSTEM – NOW AUTO‑SELECTS OFFLINE BEACONS ==========
        let currentSelectedBeaconIds = new Set();
        let allBeaconData = [];

        function buildModalUI() {
            const container = document.getElementById('modalBeaconListContainer');
            if (!container) return;
            if (!allBeaconData.length) {
                container.innerHTML = `<div class="text-center py-10 text-gray-500 dark:text-gray-400">No beacons found. Add beacons to dashboard.</div>`;
                return;
            }
            let html = `<div class="text-xs text-gray-500 dark:text-gray-400 mb-2 px-1 flex justify-between items-center">
                            <span>Select beacons to check</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">${currentSelectedBeaconIds.size} selected</span>
                        </div>`;
            allBeaconData.forEach(beacon => {
                const isChecked = currentSelectedBeaconIds.has(beacon.id);
                const onlineBadge = beacon.online ?
                    '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-300">● Online</span>' :
                    '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-munti-red-0 dark:bg-munti-red-0/60 dark:text-munti-red-0">● Offline</span>';
                html += `
                    <div class="beacon-modal-row flex items-center justify-between p-3 rounded-xl bg-white dark:bg-gray-800/70 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer group" data-id="${escapeHtml(beacon.id)}" data-name="${escapeHtml(beacon.name)}">
                        <div class="flex items-center gap-3 flex-1">
                            <input type="checkbox" class="beacon-checkbox w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-1 dark:bg-gray-700 dark:border-gray-500" data-id="${beacon.id}" ${isChecked ? 'checked' : ''}>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800 dark:text-gray-100">${escapeHtml(beacon.name)}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">ID: ${escapeHtml(beacon.id)}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${onlineBadge}
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;

            document.querySelectorAll('.beacon-checkbox').forEach(cb => {
                cb.addEventListener('change', (e) => {
                    e.stopPropagation();
                    const bid = cb.getAttribute('data-id');
                    if (cb.checked) currentSelectedBeaconIds.add(bid);
                    else currentSelectedBeaconIds.delete(bid);
                    updateSelectionCounter();
                });
            });
            document.querySelectorAll('.beacon-modal-row').forEach(row => {
                const chk = row.querySelector('.beacon-checkbox');
                row.addEventListener('click', (e) => {
                    if (e.target.tagName === 'INPUT' && e.target.type === 'checkbox') return;
                    if (chk) { chk.checked = !chk.checked;
                        const evt = new Event('change', { bubbles: true });
                        chk.dispatchEvent(evt);
                    }
                });
            });
        }

        function updateSelectionCounter() {
            const counterSpan = document.querySelector('#modalSelectionCounter');
            if (counterSpan) counterSpan.innerText = currentSelectedBeaconIds.size;
            const selectAllCheckbox = document.getElementById('selectAllBeaconsCheckbox');
            if (selectAllCheckbox && allBeaconData.length) {
                selectAllCheckbox.checked = (currentSelectedBeaconIds.size === allBeaconData.length);
                selectAllCheckbox.indeterminate = (currentSelectedBeaconIds.size > 0 && currentSelectedBeaconIds.size < allBeaconData.length);
            }
        }

        function renderBeaconsToModal() {
            // gather real beacon data from DOM (.beacon-item) – NO FALLBACK DEMO DATA
            const beaconElements = document.querySelectorAll('.beacon-item');
            const newBeaconList = [];
            beaconElements.forEach(el => {
                let id = el.getAttribute('data-beacon-id');
                if (!id) {
                    const idSpan = el.querySelector('[data-id]') || el.querySelector('.beacon-id-text');
                    id = idSpan ? idSpan.getAttribute('data-id') : null;
                }
                if (!id) return;
                let nameRaw = '';
                const nameSpan = el.querySelector('.font-medium');
                if (nameSpan) nameRaw = nameSpan.innerText.trim();
                else nameRaw = el.innerText.split('\n')[0].replace(/● Online|● Offline/g, '').trim();
                const isOnline = el.classList.contains('online-item');
                newBeaconList.push({ id: id, name: nameRaw || `Beacon ${id}`, online: isOnline });
            });
            allBeaconData = newBeaconList;

            // 🆕 AUTO‑SELECT OFFLINE BEACONS
            const offlineIds = allBeaconData.filter(b => !b.online).map(b => b.id);
            currentSelectedBeaconIds = new Set(offlineIds);

            // preserve any previously selected that still exist (if we ever call this again without reset)
            // but we want offline to be selected by default, so we override.

            buildModalUI();
            updateSelectionCounter();
        }

        function openTailwindModal() {
            const modal = document.getElementById('beaconTailwindModal');
            if (!modal) createTailwindModal();
            renderBeaconsToModal();
            const modalDiv = document.getElementById('beaconTailwindModal');
            if (modalDiv) {
                modalDiv.classList.remove('hidden');
                modalDiv.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeTailwindModal() {
            const modalDiv = document.getElementById('beaconTailwindModal');
            if (modalDiv) {
                modalDiv.classList.add('hidden');
                modalDiv.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function createTailwindModal() {
            if (document.getElementById('beaconTailwindModal')) return;
            const modalDiv = document.createElement('div');
            modalDiv.id = 'beaconTailwindModal';
            modalDiv.className = 'fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all';
            modalDiv.setAttribute('aria-modal', 'true');
            modalDiv.innerHTML = `
            <div class="absolute inset-0 bg-black/60 dark:bg-black/70 backdrop-blur-sm" id="modalBackdrop"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-lg w-full max-h-[85vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between px-5 pt-4 pb-2 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">Check Beacons Status</h3>
                    <button id="closeModalBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full p-1 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="px-4 py-2 bg-slate-100 dark:bg-slate-800/60 flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-200">
                            <input type="checkbox" id="selectAllBeaconsCheckbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                            Select All
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <button id="modalSelectOfflineBtn" class="px-3 py-1.5 text-sm font-medium bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow transition">Select Offline</button>
                    </div>
                </div>
                <div id="modalBeaconListContainer" class="flex-1 overflow-y-auto custom-scroll px-3 py-3 space-y-2 bg-white dark:bg-slate-900"></div>
                <div class="px-5 py-3 border-t border-slate-50 dark:border-slate-700 flex justify-between bg-slate-100 dark:bg-slate-900/60">
                    <button id="modalClearSelectionBtn" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">Clear Selection</button>
                    <div class="flex gap-2">
                        <button id="modalCheckAllBtn" class="px-3 py-1.5 text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow transition">Check All</button>
                        <button id="modalCheckSelectedBtn" class="px-3 py-1.5 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">Check Selected</button>
                    </div>
                </div>
            </div>  
            `;
            document.body.appendChild(modalDiv);

            const closeModal = () => closeTailwindModal();
            document.getElementById('closeModalBtn')?.addEventListener('click', closeModal);
            document.getElementById('modalCloseFooterBtn')?.addEventListener('click', closeModal);
            document.getElementById('modalBackdrop')?.addEventListener('click', closeModal);

            const selectAllChk = document.getElementById('selectAllBeaconsCheckbox');
            if (selectAllChk) {
                selectAllChk.addEventListener('change', (e) => {
                    if (e.target.checked) {
                        allBeaconData.forEach(b => currentSelectedBeaconIds.add(b.id));
                    } else {
                        currentSelectedBeaconIds.clear();
                    }
                    buildModalUI();
                    updateSelectionCounter();
                });
            }

            // 🆕 SELECT OFFLINE button
            document.getElementById('modalSelectOfflineBtn')?.addEventListener('click', () => {
                const offlineIds = allBeaconData.filter(b => !b.online).map(b => b.id);
                currentSelectedBeaconIds = new Set(offlineIds);
                buildModalUI();
                updateSelectionCounter();
            });

            document.getElementById('modalCheckSelectedBtn')?.addEventListener('click', async () => {
                const selectedBeacons = allBeaconData.filter(b => currentSelectedBeaconIds.has(b.id));
                if (selectedBeacons.length === 0) { Swal.fire('No selection', 'Please select at least one beacon', 'info'); return; }
                closeTailwindModal();
                await checkMultipleBeacons(selectedBeacons.map(b => ({ id: b.id, name: b.name })));
            });

            document.getElementById('modalCheckAllBtn')?.addEventListener('click', async () => {
                if (allBeaconData.length === 0) { Swal.fire('No beacons', 'No beacons available', 'warning'); return; }
                closeTailwindModal();
                await checkMultipleBeacons(allBeaconData.map(b => ({ id: b.id, name: b.name })));
            });

            document.getElementById('modalClearSelectionBtn')?.addEventListener('click', () => {
                currentSelectedBeaconIds.clear();
                buildModalUI();
                updateSelectionCounter();
            });
        }

        function initModalSystem() {
            createTailwindModal();
            const beaconModalBtn = document.getElementById('checkBeaconModalBtn');
            if (beaconModalBtn) {
                const newBtn = beaconModalBtn.cloneNode(true);
                beaconModalBtn.parentNode.replaceChild(newBtn, beaconModalBtn);
                newBtn.addEventListener('click', (e) => { e.preventDefault(); openTailwindModal(); });
            }
        }
        
        // Volume slider handler
        const volSlider = document.getElementById('vol');
        const volVal = document.getElementById('vol-val');
        if(volSlider && volVal) {
            volSlider.addEventListener('input', (e) => { volVal.value = e.target.value; });
            volVal.addEventListener('input', (e) => { let v = parseInt(e.target.value)||0; v=Math.min(100,Math.max(0,v)); volSlider.value=v; volVal.value=v; });
        }
        

        
        initModalSystem();
        window.checkBeaconStatus = checkBeaconStatus;
    </script>
