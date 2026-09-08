<div id="addUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden transform transition-all scale-95">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New User</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill in the details to create a new system user.</p>
        </div>

        <form id="addUserForm" action="{{ route('settings.users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- First Name & Last Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" id="first_name" required class="w-full text-gray-300 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" id="last_name" required class="w-full text-gray-300 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Position -->
            <div>
                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Position <span class="text-red-500">*</span></label>
                <input type="text" name="position" id="position" required class="w-full text-gray-300 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>

            <!-- User Level -->
            <div>
                <label for="user_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">User Level <span class="text-red-500">*</span></label>

                <div class="relative">
                    <select name="user_level" id="user_level" required class="w-full px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500">
                        <option value="" disabled selected>Select role</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>

                    <!-- Custom dropdown arrow -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

            <!-- Optional fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Email (optional)
                    </label>
                    <input type="email" name="email" id="email"
                        class="w-full text-gray-300 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Contact Number (optional)
                    </label>
                    <input type="text" name="contact_number" id="contact_number"
                        class="w-full text-gray-300 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Auto-generated preview -->
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg text-sm">
                <p class="font-medium text-gray-700 dark:text-gray-300">Auto-generated:</p>
                <div class="mt-2 space-y-1">
                    <p class="text-gray-300">
                        Username:
                        <span id="preview_username" class="font-semibold text-indigo-600 dark:text-indigo-400">—</span>
                    </p>
                    <p class="text-gray-300">
                        Password:
                        <span id="preview_password" class="font-semibold text-indigo-600 dark:text-indigo-400">—</span>
                    </p>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 italic">
                    The password will be set automatically and can be changed later.
                </p>
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button type="button" id="closeAddUserModal"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg transition">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Close on backdrop click
    document.getElementById('addUserModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });

    // Auto-generate username & password preview
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const usernamePreview = document.getElementById('preview_username');
    const passwordPreview = document.getElementById('preview_password');

    function updateCredentialsPreview() {
        const first = firstNameInput.value.trim().toLowerCase();
        const last = lastNameInput.value.trim().toLowerCase();

        if (first && last) {
            let username = `${first.charAt(0)}.${last}`;
            const year = new Date().getFullYear();
            const password = `${username}${year}!!`;

            usernamePreview.textContent = username;
            passwordPreview.textContent = password;
        } else {
            usernamePreview.textContent = '—';
            passwordPreview.textContent = '—';
        }
    }

    firstNameInput?.addEventListener('input', updateCredentialsPreview);
    lastNameInput?.addEventListener('input', updateCredentialsPreview);
</script>