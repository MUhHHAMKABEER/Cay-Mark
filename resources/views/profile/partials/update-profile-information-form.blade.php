<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Name is not editable. To change email or password, use Dashboard > Account settings.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700 shadow-sm">
                {{ $user->name }}
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ __('Name cannot be changed.') }}</p>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-700 shadow-sm">
                {{ $user->email }}
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ __('To change your email, go to Dashboard > Account settings. A verification code will be sent to your current email first.') }}</p>
        </div>
    </div>
</section>
