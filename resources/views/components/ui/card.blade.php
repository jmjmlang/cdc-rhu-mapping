@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200']) }}>
    @if($title)
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">{{ $title }}</h3>
        </div>
    @endif
    <div class="p-5">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50/60 rounded-b-xl">
            {{ $footer }}
        </div>
    @endisset
</div>
