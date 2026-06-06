@props([
    'variant' => 'primary',
    'type'    => 'button',
    'size'    => 'md',
])

@php
$base = 'inline-flex items-center justify-center font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';

$variants = [
    'primary'   => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
    'secondary' => 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-primary-500',
    'danger'    => 'bg-red-700 text-white hover:bg-red-800 focus:ring-red-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs rounded-lg',
    'md' => 'px-4 py-2 text-sm rounded-lg',
    'lg' => 'px-5 py-2.5 text-base rounded-lg',
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$base {$variants[$variant]} {$sizes[$size]}"]) }}
>
    {{ $slot }}
</button>
