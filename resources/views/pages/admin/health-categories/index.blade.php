<x-app-layout>
    <x-slot name="title">Health Categories</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Health Categories</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            {{-- ── Add new category ─────────────────────────────────────── --}}
            <x-ui.card title="Add New Category">
                <form method="POST" action="{{ route('admin.health-categories.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input name="name" label="Category Name" placeholder="e.g. Dengue" :value="old('name')" :required="true" />
                        <x-form.textarea name="description" label="Description (optional)" rows="3" placeholder="Brief description..." :value="old('description')" />
                    </div>
                    <div class="flex justify-end">
                        <x-ui.button variant="primary" type="submit">Add Category</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            @include('pages.admin.health-categories.category-list')

        </div>
    </div>
</x-app-layout>
