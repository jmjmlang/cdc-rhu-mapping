@props(['label', 'value', 'description' => null, 'accent' => 'primary'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 p-5']) }}>
    <p class="text-sm font-medium text-gray-500 mb-2">{{ $label }}</p>
    <p class="text-3xl font-extrabold text-gray-900">{{ $value }}</p>
</div>
