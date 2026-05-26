<footer class="bg-primary border-t border-white/10 mt-auto">
    <div class="w-full max-w-[1280px] mx-auto px-4 md:px-16 py-14">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            {{-- Brand --}}
            <div class="lg:col-span-1 space-y-5">
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset(config('logos.footer', 'Logos/Caymark Logo.png')) }}"
                         alt="CayMark Island Exchange"
                         class="h-[56px] w-auto object-contain"/>
                </a>
                <p class="text-sm text-white/60 leading-relaxed">
                    The Bahamas' premier digital trading center for vehicles and marine vessels. Transforming how the islands buy, sell, and trade.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="space-y-4">
                <h4 class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em]">Quick Links</h4>
                <nav class="flex flex-col gap-2.5">
                    <a href="{{ route('welcome') }}" class="text-white/60 hover:text-white transition-colors text-sm">Home</a>
                    <a href="{{ route('Auction.index') }}" class="text-white/60 hover:text-white transition-colors text-sm">Auctions</a>
                    <a href="{{ route('Auction.index') }}" class="text-white/60 hover:text-white transition-colors text-sm">Marketplace</a>
                    <a href="{{ Route::has('contact') ? route('contact') : '#' }}" class="text-white/60 hover:text-white transition-colors text-sm">Contact Us</a>
                </nav>
            </div>

            {{-- Support --}}
            <div class="space-y-4">
                <h4 class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em]">Support</h4>
                <nav class="flex flex-col gap-2.5">
                    <a href="{{ Route::has('help-center') ? route('help-center') : '#' }}" class="text-white/60 hover:text-white transition-colors text-sm">Help Center</a>
                    <a href="{{ route('fee-calculator') }}" class="text-white/60 hover:text-white transition-colors text-sm">Fee Calculator</a>
                    <a href="#" class="text-white/60 hover:text-white transition-colors text-sm">Rules &amp; Policies</a>
                    <a href="#" class="text-white/60 hover:text-white transition-colors text-sm">FAQ</a>
                </nav>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <h4 class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em]">Contact Information</h4>
                <div class="flex flex-col gap-3">
                    <div class="flex items-start gap-2.5 text-white/60 text-sm">
                        <span class="material-symbols-outlined text-[16px] flex-shrink-0 mt-0.5">location_on</span>
                        Nassau, The Bahamas
                    </div>
                    <div class="flex items-start gap-2.5 text-white/60 text-sm">
                        <span class="material-symbols-outlined text-[16px] flex-shrink-0 mt-0.5">call</span>
                        +1 (242) 555-CARS
                    </div>
                    <div class="flex items-start gap-2.5 text-white/60 text-sm">
                        <span class="material-symbols-outlined text-[16px] flex-shrink-0 mt-0.5">mail</span>
                        <a href="mailto:info@caymark.com" class="hover:text-white transition-colors">info@caymark.com</a>
                    </div>
                    <div class="flex items-start gap-2.5 text-white/60 text-sm">
                        <span class="material-symbols-outlined text-[16px] flex-shrink-0 mt-0.5">schedule</span>
                        Mon–Fri: 9AM–6PM EST
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-white/40">
                © 2026 CayMark Island Exchange &amp; Auction House. All rights reserved.
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-white/40">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                <span class="text-white/20">•</span>
                <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
                <span class="text-white/20">•</span>
                <a href="#" class="hover:text-white/70 transition-colors">Cookie Policy</a>
            </div>
        </div>

    </div>
</footer>
