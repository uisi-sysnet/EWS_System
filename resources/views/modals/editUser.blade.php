<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit User</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update user information and permissions.</p>
        </div>
        <form id="editUserForm" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" id="edit_first_name" required
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label for="edit_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" id="edit_last_name" required
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <div>
                <label for="edit_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Position <span class="text-red-500">*</span></label>
                <input type="text" name="position" id="edit_position" required
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label for="edit_user_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">User Role <span class="text-red-500">*</span></label>
                <select name="user_level" id="edit_user_level" required
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition appearance-none cursor-pointer">
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>

            <div>
                <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                <input type="email" name="email" id="edit_email"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label for="edit_contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                <input type="text" name="contact_number" id="edit_contact_number"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="active" id="edit_active" value="1"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="edit_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active Account</label>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <button type="button" id="closeEditUserModal"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg transition">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>