@php
    $user = Auth::user();
    $role = strtolower($user->role ?? '');
    $registrationComplete = $user->isRegistrationComplete() ?? false;
@endphp

@if(!$registrationComplete || empty($role))
    <script>window.location="{{ route('dashboard.default') }}";</script>
@elseif($role === 'admin')
    <script>window.location="{{ route('dashboard.admin') }}";</script>
@elseif($role === 'seller')
    <script>window.location="{{ route('dashboard.seller') }}";</script>
@elseif($role === 'buyer')
    @if($user->hasActiveSubscription())
        <script>window.location="{{ route('welcome') }}";</script>
    @else
        <script>window.location="{{ route('subscription.plans') }}";</script>
    @endif
@else
    <script>window.location="{{ route('dashboard.default') }}";</script>
@endif
