@extends('layouts.welcome')

@section('title', 'Contact Us — CayMark Island Exchange')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ --}}
<section class="bg-primary text-white py-20 md:py-28 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 40px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 40px)"></div>
    <div class="relative max-w-[1280px] mx-auto px-4 md:px-16 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 border border-white/20 bg-white/10 text-[11px] font-bold tracking-[0.2em] uppercase mb-8">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            We're here to help
        </div>
        <h1 class="text-4xl md:text-6xl font-bold font-headline-lg uppercase tracking-tight mb-6">
            Contact <span class="text-secondary-fixed-dim">Us</span>
        </h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto font-body-lg">
            CayMark is ready to provide the right solution according to your needs. Reach out and our team will respond within 24 hours.
        </p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     CONTACT INFO + FORM
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">
        <div class="grid lg:grid-cols-5 gap-12 xl:gap-16">

            {{-- ── Left: Contact info ─────────────────────────── --}}
            <div class="lg:col-span-2 space-y-10">

                <div>
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Get in touch</p>
                    <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-md mb-4">Contact Information</h2>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        We're here to help with any questions, support, or inquiries about our vehicle auction platform.
                    </p>
                </div>

                {{-- Info items --}}
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-11 h-11 bg-primary flex items-center justify-center" style="border-radius:0">
                            <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">location_on</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Address</p>
                            <p class="text-sm text-gray-700 font-medium">Nassau, The Bahamas</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-11 h-11 bg-primary flex items-center justify-center" style="border-radius:0">
                            <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">call</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Phone</p>
                            <a href="tel:+12425551234" class="text-sm text-gray-700 font-medium hover:text-primary transition-colors">+1 (242) 123-4567</a>
                            <p class="text-xs text-gray-400 mt-0.5">Fax: +1 (242) 123-4568</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-11 h-11 bg-primary flex items-center justify-center" style="border-radius:0">
                            <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">mail</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Email</p>
                            <a href="mailto:info@caymark.co" class="text-sm text-gray-700 font-medium hover:text-primary transition-colors">info@caymark.co</a>
                            <br>
                            <a href="mailto:support@caymark.co" class="text-sm text-gray-400 hover:text-primary transition-colors">support@caymark.co</a>
                        </div>
                    </div>
                </div>

                {{-- Business hours --}}
                <div class="border border-gray-100 overflow-hidden" style="border-radius:0">
                    <div class="bg-primary px-5 py-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[18px]">schedule</span>
                        <span class="text-[11px] font-bold text-white uppercase tracking-widest">Business Hours</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between items-center px-5 py-3">
                            <span class="text-sm text-gray-600">Monday – Friday</span>
                            <span class="text-sm font-bold text-primary">9:00 AM – 6:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center px-5 py-3">
                            <span class="text-sm text-gray-600">Saturday</span>
                            <span class="text-sm font-bold text-primary">10:00 AM – 4:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center px-5 py-3">
                            <span class="text-sm text-gray-600">Sunday</span>
                            <span class="text-sm font-medium text-gray-400">Closed</span>
                        </div>
                    </div>
                </div>

                {{-- Social --}}
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Follow Us</p>
                    <div class="flex gap-3">
                        <a href="#" title="Facebook"
                           class="w-10 h-10 bg-primary flex items-center justify-center text-white hover:bg-secondary-fixed-dim hover:text-primary transition-colors"
                           style="border-radius:0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" title="Instagram"
                           class="w-10 h-10 bg-primary flex items-center justify-center text-white hover:bg-secondary-fixed-dim hover:text-primary transition-colors"
                           style="border-radius:0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" title="TikTok"
                           class="w-10 h-10 bg-primary flex items-center justify-center text-white hover:bg-secondary-fixed-dim hover:text-primary transition-colors"
                           style="border-radius:0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- ── Right: Form ─────────────────────────────────── --}}
            <div class="lg:col-span-3">

                <div class="mb-8">
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Send a message</p>
                    <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-md mb-2">We'd Love to Hear From You</h2>
                    <p class="text-gray-500 text-sm">Fill out the form below and we'll get back to you within 24 hours.</p>
                </div>

                @if(session('success'))
                <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-green-50 border border-green-200" style="border-radius:0">
                    <span class="material-symbols-outlined text-green-500 text-[20px]">check_circle</span>
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="flex items-start gap-3 px-5 py-4 mb-6 bg-red-50 border border-red-200" style="border-radius:0">
                    <span class="material-symbols-outlined text-red-500 text-[20px] flex-shrink-0 mt-0.5">error</span>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="#" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Name <span class="text-red-400">*</span></label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name') }}"
                                   placeholder="Your full name"
                                   class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300"
                                   style="border-radius:0"/>
                        </div>
                        <div>
                            <label for="company" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Company</label>
                            <input type="text" id="company" name="company"
                                   value="{{ old('company') }}"
                                   placeholder="Your company (optional)"
                                   class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300"
                                   style="border-radius:0"/>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone <span class="text-red-400">*</span></label>
                            <input type="text" id="phone" name="phone" required
                                   value="{{ old('phone') }}"
                                   placeholder="e.g. (242) 555-1234"
                                   inputmode="numeric"
                                   class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300 js-digits-only js-phone-format"
                                   data-cm-validate="phone"
                                   style="border-radius:0"/>
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email <span class="text-red-400">*</span></label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email') }}"
                                   placeholder="you@example.com"
                                   class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300"
                                   style="border-radius:0"/>
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Subject <span class="text-red-400">*</span></label>
                        <input type="text" id="subject" name="subject" required
                               value="{{ old('subject') }}"
                               placeholder="How can we help?"
                               class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300"
                               style="border-radius:0"/>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Message <span class="text-red-400">*</span></label>
                        <textarea id="message" name="message" rows="6" required maxlength="500"
                                  placeholder="Tell us more about your inquiry…"
                                  class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-sm text-on-surface focus:outline-none focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary transition-all placeholder:text-gray-300 resize-none"
                                  style="border-radius:0">{{ old('message') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1.5">Maximum 500 characters.</p>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-primary text-white font-bold uppercase tracking-widest text-sm py-4 px-8 hover:bg-[#003377] transition-colors"
                            style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Send Message
                    </button>
                </form>

            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     MAP
══════════════════════════════════════════════════════════════ --}}
<section>
    <div class="bg-primary px-4 md:px-16 py-10 max-w-full">
        <div class="max-w-[1280px] mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-1">Our Location</p>
                <h2 class="text-2xl font-bold text-white uppercase tracking-tight font-headline-md">Find Us</h2>
            </div>
            <div class="flex items-center gap-2 text-white/60 text-sm">
                <span class="material-symbols-outlined text-secondary-fixed-dim text-[18px]">location_on</span>
                Nassau, The Bahamas
            </div>
        </div>
    </div>
    <div style="height:480px;width:100%;overflow:hidden">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.178509579!2d-77.343055684951!3d25.077196983899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x892f7c99b981dbc9%3A0x2d0c2b5c8b8b8b8b!2sBay%20Street%2C%20Nassau%2C%20The%20Bahamas!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
            width="100%"
            height="100%"
            style="border:0;display:block"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

@endsection
