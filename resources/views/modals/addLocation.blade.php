<!-- Add Location Modal -->
<div id="addLocationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all scale-95">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Location</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Enter the name of the new location (e.g. Uplink Office, Calamba City)
            </p>
        </div>

        <form id="addLocationForm" action="{{ route('settings.locations.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="location_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Location Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="location_name" 
                    id="location_name" 
                    required
                    autocomplete="off"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                @error('location_name')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button type="button" id="closeAddLocationModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition">
                    Cancel
                </button>
                
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg transition flex items-center gap-2">
                    Add Location
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('addLocationModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    document.getElementById('closeAddLocationModal')?.addEventListener('click', function() {
        const modal = document.getElementById('addLocationModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    });
</script>