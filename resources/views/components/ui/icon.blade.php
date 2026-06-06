{{--
  Thin wrapper around blade-heroicons.
  Swap icon libraries here in one place if ever needed.

  Usage:
  <x-ui.icon name="x-mark" />                     outline, default w-4 h-4
    <x-ui.icon name="x-mark" class="w-4 h-4" />     custom size
    <x-ui.icon name="ellipsis-horizontal" type="m" /> mini (20 px solid)
    <x-ui.icon name="eye" x-show="!visible" />        Alpine attributes pass through
--}}
@props(['name', 'type' => 'o'])

<x-dynamic-component
    :component="'heroicon-' . $type . '-' . $name"
  {{ $attributes->merge(['class' => 'w-4 h-4']) }}
/>
