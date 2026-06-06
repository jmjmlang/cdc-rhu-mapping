{{-- ── Existing categories ───────────────────────────────────── --}}
<x-ui.card title="Existing Categories">
    @if($categories->isEmpty())
        <p class="text-sm text-gray-400 text-center py-8">No categories yet.</p>
    @else
        <div class="space-y-3">
            @foreach($categories as $category)
                <div x-data="{ editing: false, guideOpen: false }" class="border border-gray-200 bg-white rounded-xl">
                    {{-- Display mode --}}
                    <div x-show="!editing" class="flex items-start justify-between gap-4 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-900 text-sm">{{ $category->name }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-primary-50 text-primary-700 rounded-md">
                                    {{ $category->case_reports_count }} {{ Str::plural('report', $category->case_reports_count) }}
                                </span>
                                @if(empty($category->prevention_tips) && empty($category->action_steps))
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-800 rounded-md">
                                        <x-ui.icon name="exclamation-triangle" class="w-3 h-3" />
                                        Needs guide
                                    </span>
                                @endif
                            </div>
                            @if($category->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $category->description }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button
                                type="button"
                                x-on:click="guideOpen = !guideOpen"
                                class="px-2.5 py-1 text-xs font-medium text-primary-600 bg-white rounded-lg border border-primary-200 hover:bg-primary-50 transition-colors"
                            >
                                <span x-text="guideOpen ? 'Close Guide' : 'Edit Guide'"></span>
                            </button>
                            <button
                                type="button"
                                x-on:click="editing = true"
                                class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                            >Edit</button>
                            <form method="POST" action="{{ route('admin.health-categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete \'{{ $category->name }}\'? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-2.5 py-1 text-xs font-medium text-red-600 bg-white rounded-lg border border-red-100 hover:bg-red-50 transition-colors"
                                >Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit mode (name/description) --}}
                    <div x-show="editing" x-cloak class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.health-categories.update', $category) }}" class="space-y-3">
                            @csrf @method('PATCH')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <x-form.input
                                    name="name"
                                    label="Category Name"
                                    :value="$category->name"
                                    :required="true" />
                                <x-form.textarea
                                    name="description"
                                    label="Description"
                                    rows="3"
                                    :value="$category->description" />
                            </div>
                            <div class="flex justify-end gap-2">
                                <x-ui.button variant="secondary" type="button" x-on:click="editing = false">Cancel</x-ui.button>
                                <x-ui.button variant="primary" type="submit">Save</x-ui.button>
                            </div>
                        </form>
                    </div>

                    {{-- Guide editing panel --}}
                    <div x-show="guideOpen" x-cloak x-collapse class="border-t border-gray-200 px-4 py-4 bg-white">
                        <form method="POST" action="{{ route('admin.health-categories.update-guide', $category) }}" class="space-y-5">
                            @csrf @method('PATCH')

                            <p class="text-sm font-semibold text-gray-700">Health Guide Content</p>
                            <p class="text-xs text-gray-400 -mt-3">One item per line. Leave blank to clear.</p>

                            {{-- Prevention tips --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Prevention Tips</label>
                                <textarea
                                    name="prevention_tips"
                                    rows="5"
                                    class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400"
                                    placeholder="One prevention tip per line..."
                                >{{ implode("\n", $category->prevention_tips ?? []) }}</textarea>
                            </div>

                            {{-- Action steps --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Action Steps (if infected)</label>
                                <textarea
                                    name="action_steps"
                                    rows="5"
                                    class="block w-full border-gray-300 text-sm shadow-sm focus:border-primary-400 focus:ring-primary-400"
                                    placeholder="One action step per line..."
                                >{{ implode("\n", $category->action_steps ?? []) }}</textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                                <x-ui.button variant="secondary" type="button" x-on:click="guideOpen = false">Cancel</x-ui.button>
                                <x-ui.button variant="primary" type="submit">Save Guide</x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-ui.card>
