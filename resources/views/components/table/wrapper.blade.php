<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl border border-gray-200']) }}>
    <table class="min-w-full divide-y divide-gray-200">
        {{ $slot }}
    </table>
</div>
