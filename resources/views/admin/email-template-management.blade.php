@extends('layouts.admin')
@section('title', 'Email Template Management')
@section('content')

<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Email Template Management</h1>
            <p class="text-gray-600">Manage transactional email templates used for notifications, invoices, and account emails.</p>
        </div>
        @php
            $totalTemplates   = count($templates ?? []);
            $disabledCount    = count($disabledTemplates ?? []);
            $enabledCount     = $totalTemplates - $disabledCount;
        @endphp
        <div class="flex items-center gap-3 text-sm flex-shrink-0">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-50 border border-green-200 text-green-700 font-medium">
                <span class="material-icons-round text-base">toggle_on</span>
                {{ $enabledCount }} active
            </div>
            @if($disabledCount > 0)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-red-700 font-medium">
                    <span class="material-icons-round text-base">toggle_off</span>
                    {{ $disabledCount }} disabled
                </div>
            @endif
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 font-medium">
                <span class="material-icons-round text-base">mail_outline</span>
                {{ $totalTemplates }} total
            </div>
        </div>
    </div>

    @if(!empty($disabledTemplates))
        <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
            <span class="material-icons-round text-amber-500 flex-shrink-0 mt-0.5">warning</span>
            <div>
                <p class="text-amber-800 font-medium">{{ count($disabledTemplates) }} template(s) are currently disabled — no emails will be sent for these events.</p>
                <p class="text-amber-700 text-sm mt-0.5">Disabled: <span class="font-mono">{{ implode(', ', $disabledTemplates) }}</span></p>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 flex items-center gap-3">
            <span class="material-icons-round text-green-600">check_circle</span>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 flex items-center gap-3">
            <span class="material-icons-round text-red-600">error</span>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @php
        $templateMap = collect($templates)->keyBy('name');
        $allCategorySlugs = collect($categories)->flatten()->unique()->values();
        $uncategorized = collect($templates)->pluck('name')->filter(fn ($n) => !$allCategorySlugs->contains($n))->values();
    @endphp

    {{-- Categories --}}
    <div class="space-y-8">
        @foreach($categories as $categoryName => $slugs)
            @php
                $items = collect($slugs)->filter(fn ($s) => $templateMap->has($s))->map(fn ($s) => $templateMap->get($s));
            @endphp
            @if($items->isNotEmpty())
                <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-800">{{ $categoryName }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $items->count() }} template(s)</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach($items as $t)
                            @php $isDisabled = in_array($t['name'], $disabledTemplates ?? []); @endphp
                            <li class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 transition-colors {{ $isDisabled ? 'bg-red-50/40' : 'hover:bg-gray-50/50' }}">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $isDisabled ? 'bg-red-100' : 'bg-indigo-100' }} flex items-center justify-center">
                                        <span class="material-icons-round {{ $isDisabled ? 'text-red-400' : 'text-indigo-600' }} text-xl">{{ $isDisabled ? 'mail_off' : 'mail' }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium {{ $isDisabled ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ str_replace(['-', '_'], ' ', $t['name']) }}</p>
                                            @if($isDisabled)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 font-mono truncate">{{ $t['name'] }}.blade.php</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    @if(!empty($t['modified']))
                                        <span>Modified {{ \Carbon\Carbon::createFromTimestamp($t['modified'])->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Toggle Enable/Disable --}}
                                    <form action="{{ route('admin.email-templates.toggle', $t['name']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                title="{{ $isDisabled ? 'Click to enable — emails will be sent again' : 'Click to disable — emails will NOT be sent' }}"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ $isDisabled
                                                            ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100'
                                                            : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100' }}">
                                            <span class="material-icons-round text-lg">{{ $isDisabled ? 'toggle_off' : 'toggle_on' }}</span>
                                            {{ $isDisabled ? 'Disabled' : 'Enabled' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.email-templates.edit', $t['name']) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                                        <span class="material-icons-round text-lg">edit</span>
                                        Edit
                                    </a>
                                    <a href="{{ route('admin.email-templates.preview', $t['name']) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                                        <span class="material-icons-round text-lg">visibility</span>
                                        Preview
                                    </a>
                                    <form action="{{ route('admin.email-templates.restore', $t['name']) }}" method="POST" class="inline" onsubmit="return confirm('Restore this template to default? This cannot be undone if defaults are not stored.');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-amber-300 text-amber-700 text-sm font-medium hover:bg-amber-50 transition-colors">
                                            <span class="material-icons-round text-lg">restore</span>
                                            Restore
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @endforeach

        {{-- Unlisted templates (found on disk but not in categories) --}}
        @if($uncategorized->isNotEmpty())
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-semibold text-gray-800">Other Templates</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Templates not assigned to a category</p>
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach($uncategorized as $name)
                        @php $t = $templateMap->get($name); $isDisabled = in_array($name, $disabledTemplates ?? []); @endphp
                        @if($t)
                            <li class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 transition-colors {{ $isDisabled ? 'bg-red-50/40' : 'hover:bg-gray-50/50' }}">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $isDisabled ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                                        <span class="material-icons-round {{ $isDisabled ? 'text-red-400' : 'text-gray-500' }} text-xl">{{ $isDisabled ? 'mail_off' : 'draft' }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium {{ $isDisabled ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ str_replace(['-', '_'], ' ', $name) }}</p>
                                            @if($isDisabled)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Disabled</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 font-mono truncate">{{ $name }}.blade.php</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    @if(!empty($t['modified']))
                                        <span>Modified {{ \Carbon\Carbon::createFromTimestamp($t['modified'])->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Toggle Enable/Disable --}}
                                    <form action="{{ route('admin.email-templates.toggle', $name) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                title="{{ $isDisabled ? 'Click to enable — emails will be sent again' : 'Click to disable — emails will NOT be sent' }}"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                                       {{ $isDisabled
                                                            ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100'
                                                            : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100' }}">
                                            <span class="material-icons-round text-lg">{{ $isDisabled ? 'toggle_off' : 'toggle_on' }}</span>
                                            {{ $isDisabled ? 'Disabled' : 'Enabled' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.email-templates.edit', $name) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                                        <span class="material-icons-round text-lg">edit</span>
                                        Edit
                                    </a>
                                    <a href="{{ route('admin.email-templates.preview', $name) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                                        <span class="material-icons-round text-lg">visibility</span>
                                        Preview
                                    </a>
                                    <form action="{{ route('admin.email-templates.restore', $name) }}" method="POST" class="inline" onsubmit="return confirm('Restore this template to default?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-amber-300 text-amber-700 text-sm font-medium hover:bg-amber-50 transition-colors">
                                            <span class="material-icons-round text-lg">restore</span>
                                            Restore
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    @if(empty($templates))
        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                <span class="material-icons-round text-gray-400 text-4xl">mail_outline</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No email templates found</h3>
            <p class="text-gray-500 text-sm max-w-sm mx-auto">Transactional templates live in <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">resources/views/emails/caymark/</code> (CayMark branded HTML). Add or edit <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">*.blade.php</code> files there; code references them as <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">emails.caymark.{name}</code>.</p>
        </div>
    @endif
</div>

@endsection
