@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 text-[0.8125rem] font-semibold text-primary-700 bg-primary-50 rounded-lg'
            : 'flex items-center gap-3 px-3 py-2.5 text-[0.8125rem] font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
