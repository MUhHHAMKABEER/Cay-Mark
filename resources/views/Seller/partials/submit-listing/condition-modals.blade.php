{{-- Condition tooltips (Title, Salvage, Runs & Drives, Starts) --}}
<div id="modal-title-yes" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Has Title — Yes</h3>
        <p class="text-gray-700 text-sm mb-2">This means the vehicle has a valid ownership title that will be provided to the buyer.</p>
        <p class="text-gray-700 text-sm font-semibold mb-2">Select this only when:</p>
        <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
            <li>You have the title in your possession or can legally transfer it</li>
            <li>The title is valid and transferable</li>
            <li>The buyer will receive the title after purchase</li>
            <li>The vehicle can be registered (if applicable)</li>
        </ul>
        <button type="button" class="condition-modal-close w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
    </div>
</div>
<div id="modal-title-no" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Has Title — No</h3>
        <p class="text-gray-700 text-sm mb-2">This means the vehicle does not have an ownership title.</p>
        <p class="text-gray-700 text-sm font-semibold mb-2">Select this only when:</p>
        <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
            <li>No title is available for the vehicle</li>
            <li>The vehicle cannot be registered for road use</li>
            <li>The vehicle is being sold for parts, export, or salvage</li>
        </ul>
        <button type="button" class="condition-modal-close w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
    </div>
</div>
<div id="modal-salvage" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Salvaged — Yes</h3>
        <p class="text-gray-700 text-sm mb-2">This means the vehicle has been classified as a total loss.</p>
        <p class="text-gray-700 text-sm font-semibold mb-2">Select this only when:</p>
        <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
            <li>70% or more of the vehicle has been considered a total loss</li>
            <li>The cost to repair the vehicle is higher than the vehicle's value</li>
        </ul>
        <button type="button" class="condition-modal-close w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
    </div>
</div>
<div id="modal-runs-drives" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Runs &amp; Drives — Yes</h3>
        <p class="text-gray-700 text-sm mb-2">This means the vehicle starts and moves under its own power.</p>
        <p class="text-gray-700 text-sm font-semibold mb-2">Select this only when:</p>
        <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
            <li>The engine starts</li>
            <li>The vehicle can shift into gear</li>
            <li>The vehicle can move forward and backward without assistance</li>
        </ul>
        <button type="button" class="condition-modal-close w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
    </div>
</div>
<div id="modal-starts" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Starts — Yes</h3>
        <p class="text-gray-700 text-sm mb-2">This means the engine turns on and is able to run.</p>
        <p class="text-gray-700 text-sm font-semibold mb-2">Select this only when:</p>
        <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
            <li>The engine turns on when started</li>
            <li>The engine can run on its own, even if only briefly</li>
        </ul>
        <button type="button" class="condition-modal-close w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
    </div>
</div>
