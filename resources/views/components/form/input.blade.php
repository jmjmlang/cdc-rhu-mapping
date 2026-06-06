@props([
    'label'       => null,
    'name',
    'id'          => null,
    'type'        => 'text',
    'placeholder' => '',
    'required'    => false,
    'value'       => null,
    'bag'         => null,
    'togglePassword' => false,
])

@php $fieldId = $id ?? $name; @endphp

<div>
    @if($label)
        <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}{{ $required ? ' *' : '' }}
        </label>
    @endif

    @if($togglePassword)
    <div x-data="{ visible: false }" class="relative">
        <input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            :type="visible ? 'text' : 'password'"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 pr-10']) }}
            @if($required) required @endif
        />
        <button type="button" @click="visible = !visible" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
            <x-ui.icon name="eye" x-show="!visible" class="w-3.5 h-3.5" />
            <x-ui.icon name="eye-slash" x-show="visible" x-cloak class="w-3.5 h-3.5" />
        </button>
    </div>
    @else
    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500']) }}
        @if($required) required @endif
    />
    @endif

    @if($bag)
        @error($name, $bag)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @else
        @error($name)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>
