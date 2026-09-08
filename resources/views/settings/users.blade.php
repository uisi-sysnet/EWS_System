@auth
@include('layouts.header')
@include('layouts.topbar')
@include('modals.addUser')
@include('modals.editUser')

<div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-300">
    <div class="pt-16 md:pt-20 px-4 sm:px-6 lg:px-8">
        <div class="py-6 max-w-screen-2xl mx-auto">

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-center gap-4">
                    <a href="javascript:void(0)" onclick="returnToController()" title="Back to Homepage"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-grey/10 hover:bg-slate-grey/20 dark:bg-blue-slate/30 dark:hover:bg-blue-slate/50 transition-colors focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 shrink-0 text-prussian-blue dark:text-lavender-grey">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-prussian-blue dark:text-white">User Management</h1>
                        <p class="mt-1 text-sm text-slate-grey dark:text-lavender-grey">View, add, edit and manage system users, roles & information</p>
                    </div>
                </div>

                <button type="button" data-add-user-modal-open
                    class="group relative w-full sm:w-auto inline-flex items-center justify-center overflow-hidden rounded-lg px-5 py-2.5 font-medium uppercase tracking-wide
                   bg-regal-navy hover:bg-blue-slate dark:bg-prussian-blue dark:hover:bg-blue-slate text-white
                   shadow-[0_6px_0_0_#001845] hover:shadow-[0_3px_0_0_#001845] hover:-translate-y-0.5
                   active:shadow-[0_1px_0_0_#001845] active:translate-y-2 transition-all duration-150
                   focus:outline-none focus:ring-2 focus:ring-regal-navy dark:focus:ring-prussian-blue focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800">
                    <span class="relative z-10 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New User
                    </span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-slate-grey/20 dark:border-blue-slate/30 overflow-hidden flex flex-col h-[calc(100vh-280px)] min-h-[530px]">
                <div class="flex-1 overflow-auto custom-scroll">
                    <table class="min-w-full divide-y divide-slate-grey/20 dark:divide-blue-slate/30">
                        <thead class="bg-slate-grey/5 dark:bg-blue-slate/30 shadow-sm">
                            <tr class="text-xs">
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Name</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Position</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Username</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Email</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Contact</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Role</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Status</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Date</th>
                                <th class="sticky top-0 z-10 px-6 py-4 text-left font-medium text-slate-grey dark:text-lavender-grey uppercase tracking-wider bg-slate-grey/5 dark:bg-blue-slate/30 border-b border-slate-grey/20 dark:border-blue-slate/30">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-slate-grey/10 dark:divide-blue-slate/30">
                            @forelse ($users as $user)
                            @php
                            $fullName = trim("{$user->first_name} {$user->last_name}") ?: '—';
                            $level = $user->user_level ?? 'user';
                            $roleClass = $roleStyles[$level] ?? 'text-slate-grey dark:text-lavender-grey';
                            @endphp
                            <tr class="hover:bg-slate-grey/5 dark:hover:bg-blue-slate/30 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-prussian-blue dark:text-white">{{ $fullName }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $user->position ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $user->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $user->email ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $user->contact_number ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $roleClass }} font-medium">{{ ucfirst($level) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    @if ($user->active)
                                    <span class="text-lime-600 dark:text-lime-400">Active</span>
                                    @else
                                    <span class="text-red-600 dark:text-red-500">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-grey dark:text-lavender-grey">{{ $user->created_at ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium space-x-4">
                                    <button
                                        data-id="{{ $user->id }}"
                                        data-first-name="{{ addslashes($user->first_name) }}"
                                        data-last-name="{{ addslashes($user->last_name) }}"
                                        data-position="{{ addslashes($user->position ?? '') }}"
                                        data-user-level="{{ $user->user_level }}"
                                        data-email="{{ addslashes($user->email ?? '') }}"
                                        data-contact="{{ addslashes($user->contact_number ?? '') }}"
                                        data-active="{{ $user->active ? '1' : '0' }}"
                                        class="text-regal-navy dark:text-smart-blue hover:text-blue-slate dark:hover:text-blue-slate transition-colors edit-user-btn">
                                        Edit
                                    </button>
                                    <button type="button"
                                        data-revoke-url="{{ url('settings/users', $user->id) }}"
                                        data-user-name="{{ $fullName }}"
                                        class="revoke-user-btn text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-24 text-center text-slate-grey dark:text-lavender-grey">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 mb-5 opacity-50 text-slate-grey dark:text-lavender-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4a2 2 0 00-2 2v3m-4-5H4" />
                                        </svg>
                                        <p class="text-lg font-medium text-prussian-blue dark:text-white">No users found</p>
                                        <p class="mt-2 text-slate-grey dark:text-lavender-grey">Add a new user to get started</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="flash-data" class="hidden"
        data-status="{{ session('status') }}"
        data-message="{!! addslashes(session('message') ?? '') !!}"
        data-username="{{ session('username') ?? '' }}"
        data-temp-password="{{ session('temp_password') ?? '' }}">
    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded!');
                return;
            }

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            const Alert = Swal;

            document.querySelector('[data-add-user-modal-open]')?.addEventListener('click', e => {
                e.preventDefault();
                document.getElementById('addUserModal')?.classList.replace('hidden', 'flex');
            });

            document.getElementById('closeAddUserModal')?.addEventListener('click', () => {
                document.getElementById('addUserModal')?.classList.replace('flex', 'hidden');
            });



            const editModal = document.getElementById('editUserModal');
            const editForm = document.getElementById('editUserForm');
            const closeEditBtn = document.getElementById('closeEditUserModal');

            function closeEditModal() {
                editModal.classList.add('hidden');
            }

            editModal?.addEventListener('click', function(e) {
                if (e.target === this) closeEditModal();
            });

            closeEditBtn?.addEventListener('click', closeEditModal);

            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const id = this.dataset.id;
                    const firstName = this.dataset.firstName;
                    const lastName = this.dataset.lastName;
                    const position = this.dataset.position;
                    const userLevel = this.dataset.userLevel;
                    const email = this.dataset.email === '—' ? '' : this.dataset.email;
                    const contact = this.dataset.contact === '—' ? '' : this.dataset.contact;
                    const active = this.dataset.active === '1';

                    document.getElementById('edit_user_id').value = id;
                    document.getElementById('edit_first_name').value = firstName;
                    document.getElementById('edit_last_name').value = lastName;
                    document.getElementById('edit_position').value = position;
                    document.getElementById('edit_user_level').value = userLevel;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_contact_number').value = contact;
                    document.getElementById('edit_active').checked = active;

                    editForm.action = `{{ url('settings/users') }}/${id}`;

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
                        await Toast.fire({
                            icon: 'success',
                            title: 'User updated successfully!'
                        });
                        window.location.reload();
                    } else {
                        let errorMsg = data.message || 'Update failed.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        }
                        await Alert.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                } catch (error) {
                    await Alert.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Network error. Please try again.'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });


            document.querySelectorAll('.revoke-user-btn').forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();

                    const revokeUrl = this.dataset.revokeUrl;
                    const userName = this.dataset.userName;
                    const row = this.closest('tr');

                    const result = await Alert.fire({
                        title: 'Revoke User?',
                        html: `Are you sure you want to revoke <strong>${userName}</strong>?<br>They will no longer be able to access the system.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, revoke',
                        reverseButtons: true
                    });

                    if (!result.isConfirmed) return;

                    try {
                        const response = await fetch(revokeUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Toast.fire({
                                icon: 'success',
                                title: data.message || 'User revoked successfully!'
                            });
                            row.remove();

                            const tbody = document.querySelector('tbody');
                            if (tbody.children.length === 0) {
                                window.location.reload();
                            }
                        } else {
                            Alert.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to revoke user.'
                            });
                        }
                    } catch (error) {
                        console.error('Revoke error:', error);
                        Alert.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Could not connect to server. Please try again.'
                        });
                    }
                });
            });







            // Flash message handling
            document.addEventListener('DOMContentLoaded', () => {
                const flash = document.getElementById('flash-data');
                if (!flash?.dataset.status) return;

                const {
                    status,
                    message,
                    username = '',
                    tempPassword = ''
                } = flash.dataset;

                if (status === 'success') {
                    // Show success toast first
                    Toast.fire({
                        icon: 'success',
                        title: 'Account Created!',
                        text: message || 'New user has been added successfully.'
                    }).then(() => {
                        // After toast closes, show credentials modal if there's a temp password
                        if (tempPassword) {
                            Alert.fire({
                                icon: 'info',
                                title: 'New User Credentials',
                                html: `
                                    <div class="space-y-5 mt-5 text-left">
                                        <div class="flex items-center gap-4 p-4 bg-gray-800/50 dark:bg-gray-800 border border-gray-700 rounded-xl shadow-sm">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-900/40 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-300">Username</p>
                                                <code class="block mt-1 text-base font-semibold text-indigo-400">${username || '—'}</code>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 p-4 bg-gray-800/50 dark:bg-gray-800 border border-gray-700 rounded-xl shadow-sm">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-900/40 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.1-.9-2-2-2s-2 .9-2 2m4 0c0-1.1-.9-2-2-2s-2 .9-2 2m4 0v6m-4-6v6m8-6c0-1.1-.9-2-2-2s-2 .9-2 2m4 0v6m-4-6v6"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-300">Temporary Password</p>
                                                <code class="block mt-1 text-base font-semibold text-indigo-400">${tempPassword}</code>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-amber-950/30 border border-amber-800/50 text-amber-200 rounded-xl">
                                            <strong>Security note:</strong> Share these credentials securely.<br>
                                            User must change password on first login.
                                        </div>
                                    </div>
                                `,
                                showConfirmButton: true,
                                showCancelButton: true,
                                confirmButtonText: 'Copy & Close',
                                cancelButtonText: 'Close',
                                reverseButtons: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                customClass: {
                                    popup: 'max-w-md rounded-2xl bg-gray-900 border border-gray-700'
                                }
                            }).then(result => {
                                if (!result.isConfirmed || !tempPassword) return;

                                const text = `Username: ${username || ''}\nPassword: ${tempPassword}`;
                                navigator.clipboard.writeText(text)
                                    .then(() => Toast.fire({
                                        icon: 'success',
                                        title: 'Copied to clipboard!'
                                    }))
                                    .catch(() => Toast.fire({
                                        icon: 'error',
                                        title: 'Failed to copy'
                                    }));
                            });
                        }
                    });
                } else {
                    // Error or warning
                    Alert.fire({
                        icon: status === 'error' ? 'error' : 'warning',
                        title: status === 'error' ? 'Error' : 'Warning',
                        html: message || 'An issue occurred while creating the user.'
                    });
                }
            });
        })();
    </script>

    @else
    <script>
        window.location = "{{ route('login') }}";
    </script>
    @endauth