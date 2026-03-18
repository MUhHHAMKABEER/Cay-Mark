@props([
    'name',
    'value' => '1',
    'label' => '',
    'checked' => false,
    'id' => null,
])
@php
    $inputId = $id ?? 'cb_' . preg_replace('/[^a-z0-9]/i', '_', $name);
@endphp
<label class="custom-checkbox-wrap inline-flex items-center gap-3 cursor-pointer select-none" {{ $attributes }}>
    <input type="checkbox"
           name="{{ $name }}"
           value="{{ $value }}"
           id="{{ $inputId }}"
           {{ $checked ? 'checked' : '' }}
           class="custom-checkbox-input">
    <span class="custom-checkbox-box" aria-hidden="true"></span>
    @if($label)
        <span class="custom-checkbox-label text-sm font-medium text-gray-700">{{ $label }}</span>
    @endif
    {{ $slot ?? '' }}
</label>
