@props(['type' => 'info'])

@php
$styles = match($type) {
    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'error'   => 'bg-rose-50 text-rose-800 border-rose-200',
    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
    default   => 'bg-sky-50 text-sky-800 border-sky-200',
};

$iconName = match($type) {
    'success' => 'check-circle',
    'error'   => 'exclamation-circle',
    'warning' => 'exclamation-triangle',
    default   => 'information-circle',
};

$iconColor = match($type) {
    'success' => 'text-emerald-500',
    'error'   => 'text-rose-500',
    'warning' => 'text-amber-500',
    default   => 'text-sky-500',
};
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 px-4 py-3 rounded-xl border text-sm $styles"]) }}>
    <x-ui.icon :name="$iconName" class="w-5 h-5 shrink-0 {{ $iconColor }}" />
    <div>{{ $slot }}</div>
</div>
