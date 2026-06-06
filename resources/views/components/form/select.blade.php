@props([
    'label'    => null,
    'name',
    'options'  => [],
    'required' => false,
    'selected' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}{{ $required ? ' *' : '' }}
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 px-3 py-2']) }}
        @if($required) required @endif
    >
        <option value="">Select...</option>
        @foreach($options as $option)
            <option
                value="{{ $option->id }}"
                @selected(old($name, $selected) == $option->id)
            >
                {{ $option->name }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
