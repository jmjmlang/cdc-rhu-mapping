@props(['class' => ''])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 {{ $class }}">
    {{ $slot }}
</div>
