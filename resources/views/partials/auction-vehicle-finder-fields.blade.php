{{-- Shared Vehicle Finder filter fields (desktop aside + mobile bottom sheet) --}}
<!-- Title -->
<div class="vehicle-finder-row">
    <label>Title</label>
    <div class="input-wrap">
        <div class="segmented-control">
            <button type="button" :class="{ 'active': titleCondition === '' }" @click="titleCondition = ''; applyFilters()">All</button>
            <button type="button" :class="{ 'active': titleCondition === 'CLEAN' }" @click="titleCondition = 'CLEAN'; applyFilters()">Has Title</button>
            <button type="button" :class="{ 'active': titleCondition === 'SALVAGE' }" @click="titleCondition = 'SALVAGE'; applyFilters()">No Title</button>
        </div>
    </div>
</div>

<!-- Condition -->
<div class="vehicle-finder-row">
    <label>Condition</label>
    <div class="input-wrap">
        <div class="segmented-control">
            <button type="button" :class="{ 'active': condition === '' }" @click="condition = ''; applyFilters()">All</button>
            <button type="button" :class="{ 'active': condition === 'used' }" @click="condition = 'used'; applyFilters()">Used</button>
            <button type="button" :class="{ 'active': condition === 'salvaged' }" @click="condition = 'salvaged'; applyFilters()">Salvaged</button>
        </div>
    </div>
</div>

<!-- Types -->
<div class="vehicle-finder-row">
    <label>Vehicle Type</label>
    <div class="input-wrap">
        <select x-model="selectedFilters.vehicle_type" @change="applyFilters()">
            <option value="">All Types</option>
            @foreach ($filterOptions['vehicle_types'] as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Odometer -->
<div class="vehicle-finder-row">
    <label>Odometer Range</label>
    <div class="input-wrap">
        <div class="flex justify-between text-xs text-gray-500 mb-2 font-medium">
            <span>0 mi</span>
            <span>250,000+ mi</span>
        </div>
        <input type="range" class="odometer-range w-full" min="0" max="{{ $filterOptions['odometer_max'] ?? 250000 }}"
            x-model.number="odometerMax" @input.debounce.300ms="applyFilters()">
        <p class="text-xs text-blue-600 mt-2 font-semibold" x-text="`Up to ${odometerMax ? odometerMax.toLocaleString() : 0} miles`"></p>
    </div>
</div>

<!-- Year -->
<div class="vehicle-finder-row">
    <label>Year Range</label>
    <div class="input-wrap">
        <div class="year-selects">
            <select x-model.number="yearFrom" @change="applyFilters()">
                @for($y = date('Y') + 1; $y >= 1990; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            <span class="separator">to</span>
            <select x-model.number="yearTo" @change="applyFilters()">
                @for($y = date('Y') + 1; $y >= 1990; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>

<!-- Location -->
<div class="vehicle-finder-row">
    <label>Location</label>
    <div class="input-wrap">
        <select x-model="locationSingle" @change="applyFilters()">
            <option value="">All islands</option>
            @foreach ($filterOptions['locations'] ?? [] as $location)
                <option value="{{ $location }}">{{ $location }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Make -->
<div class="vehicle-finder-row">
    <label>Make</label>
    <div class="input-wrap">
        <input type="text" list="make-list" x-model="makeSingle" @input.debounce.300ms="applyFilters()"
            placeholder="Type or select make" class="w-full">
    </div>
</div>

<!-- Model -->
<div class="vehicle-finder-row">
    <label>Model</label>
    <div class="input-wrap">
        <input type="text" list="model-list" x-model="modelSingle" @input.debounce.300ms="applyFilters()"
            placeholder="Type or select model" class="w-full">
    </div>
</div>

<!-- Fuel Type -->
<div class="vehicle-finder-row">
    <label>Fuel Type</label>
    <div class="input-wrap">
        <select multiple x-model="fuelTypeMulti" @change="applyFilters()" class="h-24">
            @foreach ($filterOptions['fuel_types'] ?? [] as $fuel)
                <option value="{{ $fuel }}">{{ $fuel }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
    </div>
</div>

<!-- Color -->
<div class="vehicle-finder-row">
    <label>Color</label>
    <div class="input-wrap">
        <select multiple x-model="colorMulti" @change="applyFilters()" class="h-24">
            @foreach ($filterOptions['colors'] ?? [] as $color)
                <option value="{{ $color }}">{{ $color }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
    </div>
</div>

<!-- Damage Type -->
<div class="vehicle-finder-row">
    <label>Damage Type</label>
    <div class="input-wrap">
        <select multiple x-model="damageTypeMulti" @change="applyFilters()" class="h-24">
            @foreach ($filterOptions['damage_types'] ?? [] as $damage)
                <option value="{{ $damage }}">{{ $damage }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
    </div>
</div>

<button type="button" class="btn-search-vehicle mt-3" @click="applyFilters(); if (window.CaymarkUI && CaymarkUI.mobile) CaymarkUI.mobile.closeBottomSheet('cm-auction-filter-sheet')">
    <span class="flex items-center justify-center gap-2">
        <span class="material-icons text-lg">search</span>
        <span>Search Vehicles</span>
    </span>
</button>

@once
    <datalist id="make-list">
        @foreach ($filterOptions['makes'] as $make)
            <option value="{{ $make }}">
        @endforeach
    </datalist>
    <datalist id="model-list">
        @foreach ($filterOptions['models'] as $model)
            <option value="{{ $model }}">
        @endforeach
    </datalist>
@endonce
