@extends('layouts.admin')
@section('title', 'Edit Email Template - ' . $templateName)
@section('content')

<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit email template</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $templateName }}</p>
        </div>
        <a href="{{ route('admin.email-templates') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">
            <span class="material-icons-round text-lg">arrow_back</span>
            Back to list
        </a>
    </div>

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

    <form action="{{ route('admin.email-templates.update', $templateName) }}" method="POST" id="email-template-form" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="editor_mode" id="editor_mode" value="simple">

        {{-- Simple editor (default) --}}
        <div id="simple-editor" class="p-6 space-y-6">
            <p class="text-sm text-gray-600">Change the text and link below. The email layout and styling stay the same.</p>

            <div>
                <label for="email_title" class="block text-sm font-medium text-gray-700 mb-1">Email title (browser tab)</label>
                <input type="text" name="email_title" id="email_title" value="{{ old('email_title', $simple['email_title'] ?? '') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="e.g. Complete Your Registration - CayMark">
            </div>

            <div>
                <label for="heading" class="block text-sm font-medium text-gray-700 mb-1">Heading (main title in email)</label>
                <input type="text" name="heading" id="heading" value="{{ old('heading', $simple['heading'] ?? '') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="e.g. Complete Your CayMark Registration">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message (main body text)</label>
                <textarea name="message" id="message" rows="8"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Write the email body here. You can use multiple paragraphs.">{{ old('message', $simple['message'] ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Tip: For the recipient’s name you can type: &#123;&#123; $user->name &#125;&#125;</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="button_text" class="block text-sm font-medium text-gray-700 mb-1">Button text</label>
                    <input type="text" name="button_text" id="button_text" value="{{ old('button_text', $simple['button_text'] ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="e.g. Complete Your Registration">
                </div>
                <div>
                    <label for="button_url" class="block text-sm font-medium text-gray-700 mb-1">Button link (URL)</label>
                    <input type="text" name="button_url" id="button_url" value="{{ old('button_url', $simple['button_url'] ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                           placeholder="e.g. {{ url('/finish-registration') }}">
                </div>
            </div>
        </div>

        {{-- Advanced editor (hidden by default) --}}
        <div id="advanced-editor" class="hidden border-t border-gray-200">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <p class="text-sm text-gray-600">Edit raw HTML. Only use this if you know what you’re doing.</p>
            </div>
            <div class="p-6">
                <textarea name="content" id="content" rows="22" class="w-full font-mono text-sm rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="HTML content...">{!! str_replace('</textarea>', '</' . 'textarea>', $content ?? '') !!}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition-colors">
                <span class="material-icons-round text-lg">save</span>
                Save changes
            </button>
            <a href="{{ route('admin.email-templates.preview', $templateName) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                <span class="material-icons-round text-lg">visibility</span>
                Preview
            </a>
            <button type="button" id="toggle-advanced" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-amber-300 text-amber-700 font-medium hover:bg-amber-50 transition-colors text-sm">
                <span class="material-icons-round text-lg">code</span>
                <span id="toggle-advanced-label">Edit HTML instead</span>
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('toggle-advanced').addEventListener('click', function () {
    var simple = document.getElementById('simple-editor');
    var advanced = document.getElementById('advanced-editor');
    var modeInput = document.getElementById('editor_mode');
    var label = document.getElementById('toggle-advanced-label');

    if (advanced.classList.contains('hidden')) {
        simple.classList.add('hidden');
        advanced.classList.remove('hidden');
        modeInput.value = 'advanced';
        label.textContent = 'Back to simple editor';
    } else {
        simple.classList.remove('hidden');
        advanced.classList.add('hidden');
        modeInput.value = 'simple';
        label.textContent = 'Edit HTML instead';
    }
});
</script>
@endsection
