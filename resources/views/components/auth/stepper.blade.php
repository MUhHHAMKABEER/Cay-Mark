@props([
    'current' => 1,
])

{{-- CayMark registration step indicator. current = 1 | 2 | 3. --}}
@php
    $cmSteps = [1 => 'Create Account', 2 => 'Select Membership', 3 => 'Verify & Pay'];
@endphp

@once
@push('styles')
<style>
.cm-step-row     { display:flex; align-items:flex-start; font-family:'Inter',sans-serif; }
.cm-step         { display:flex; flex-direction:column; align-items:center; gap:6px; flex-shrink:0; width:90px; text-align:center; }
.cm-step-dot     { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; transition:all .2s ease; }
.cm-step-dot.is-upcoming { background:#fff; border:2px solid #E2E5E9; color:#9CA3AF; }
.cm-step-dot.is-active   { background:#1B3A6B; border:2px solid #1B3A6B; color:#fff; }
.cm-step-dot.is-done     { background:#C8A84B; border:2px solid #C8A84B; color:#fff; }
.cm-step-label   { font-size:11.5px; font-weight:600; line-height:1.2; }
.cm-step-label.is-upcoming { color:#9CA3AF; }
.cm-step-label.is-active   { color:#1B3A6B; }
.cm-step-label.is-done     { color:#C8A84B; }
.cm-step-line    { flex:1; height:2px; background:#E2E5E9; margin-top:17px; }
.cm-step-line.is-done { background:#C8A84B; }
</style>
@endpush
@endonce

<div class="cm-step-row">
    @foreach ($cmSteps as $n => $label)
        @php
            $state = $n < (int) $current ? 'done' : ($n === (int) $current ? 'active' : 'upcoming');
        @endphp
        <div class="cm-step">
            <div class="cm-step-dot is-{{ $state }}">
                @if ($state === 'done')
                    <span class="material-symbols-outlined" style="font-size:18px">check</span>
                @else
                    {{ $n }}
                @endif
            </div>
            <span class="cm-step-label is-{{ $state }}">{{ $label }}</span>
        </div>
        @if (! $loop->last)
            <div class="cm-step-line {{ $n < (int) $current ? 'is-done' : '' }}"></div>
        @endif
    @endforeach
</div>
