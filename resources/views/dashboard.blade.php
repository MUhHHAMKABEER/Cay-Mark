@php
    $role = strtolower(Auth::user()->role ?? '');
@endphp

@if($role === 'admin')
    <script>window.location="{{ route('dashboard.admin') }}";</script>
@elseif($role === 'seller')
    <script>window.location="{{ route('dashboard.seller') }}";</script>
@elseif($role === 'buyer')
    @if(Auth::user()->hasActiveSubscription())
        <script>window.location="{{ route('welcome') }}";</script>
    @else
        <script>window.location="{{ route('subscription.plans') }}";</script>
    @endif
@endif
