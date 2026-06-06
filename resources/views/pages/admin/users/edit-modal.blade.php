{{-- ── Edit User Modals ─────────────────────────────────────── --}}
@foreach($allUsers as $user)
    <x-ui.modal name="edit-user-{{ $user->id }}" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Edit User</h3>
                <button type="button" x-on:click="$dispatch('close-modal', 'edit-user-{{ $user->id }}')" class="text-gray-400 hover:text-gray-600">
                    <x-ui.icon name="x-mark" class="w-4 h-4" />
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                        class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                        class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400" />
                </div>

                <div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <div class="px-3 py-2 border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-700">
                                {{ strtoupper($user->role) }}{{ $user->id === auth()->id() ? ' (YOU)' : '' }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Role changes are disabled to keep accounts single-role.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                            <select name="barangay_id" class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400">
                                <option value="">None</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy->id }}" {{ $user->barangay_id == $brgy->id ? 'selected' : '' }}>{{ $brgy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation <span class="font-normal text-gray-400 text-xs">(Admin/RHU only)</span></label>
                        <input type="text" name="org_role" value="{{ $user->org_role }}" {{ $user->role === 'citizen' ? 'disabled' : '' }}
                            class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400 disabled:bg-gray-100 disabled:text-gray-400" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reset Password</label>
                    <div x-data="{ visible: false }" class="relative">
                        <input :type="visible ? 'text' : 'password'" name="new_password" placeholder="Leave blank to keep current password"
                            class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400 pr-10" />
                        <button type="button" @click="visible = !visible" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <x-ui.icon name="eye" x-show="!visible" class="w-3.5 h-3.5" />
                            <x-ui.icon name="eye-slash" x-show="visible" x-cloak class="w-3.5 h-3.5" />
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters. Only fill if you want to change the password.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close-modal', 'edit-user-{{ $user->id }}')">Cancel</x-ui.button>
                    <x-ui.button variant="primary" type="submit">Save Changes</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
@endforeach
