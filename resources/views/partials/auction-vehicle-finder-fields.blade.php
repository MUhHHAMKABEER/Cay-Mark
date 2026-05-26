{{-- Shared Vehicle Finder filter fields (desktop aside + mobile bottom sheet) --}}

{{-- Title --}}
<div class="vf-row">
    <span class="vf-label">Title</span>
    <div class="seg-ctrl">
        <button type="button" :class="{ 'active': titleCondition === '' }" @click="titleCondition = ''; applyFilters()">All</button>
        <button type="button" :class="{ 'active': titleCondition === 'CLEAN' }" @click="titleCondition = 'CLEAN'; applyFilters()">Has Title</button>
        <button type="button" :class="{ 'active': titleCondition === 'SALVAGE' }" @click="titleCondition = 'SALVAGE'; applyFilters()">No Title</button>
    </div>
</div>

{{-- Condition --}}
<div class="vf-row">
    <span class="vf-label">Condition</span>
    <div class="seg-ctrl">
        <button type="button" :class="{ 'active': condition === '' }" @click="condition = ''; applyFilters()">All</button>
        <button type="button" :class="{ 'active': condition === 'used' }" @click="condition = 'used'; applyFilters()">Used</button>
        <button type="button" :class="{ 'active': condition === 'salvaged' }" @click="condition = 'salvaged'; applyFilters()">Salvaged</button>
    </div>
</div>

{{-- Vehicle Type --}}
<div class="vf-row">
    <span class="vf-label">Vehicle Type</span>
    <div class="vf-field-wrap">
        <select class="vf-input" x-model="selectedFilters.vehicle_type" @change="applyFilters()">
            <option value="">All Types</option>
            @foreach ($filterOptions['vehicle_types'] as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
        <button type="button" class="vf-clear-btn" x-show="selectedFilters.vehicle_type"
            @click="selectedFilters.vehicle_type = ''; applyFilters()" title="Clear">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
</div>

{{-- Location --}}
<div class="vf-row">
    <span class="vf-label">Location</span>
    <div class="vf-field-wrap">
        <select class="vf-input" x-model="locationSingle" @change="applyFilters()">
            <option value="">All Islands</option>
            @foreach ($filterOptions['locations'] ?? [] as $location)
                <option value="{{ $location }}">{{ $location }}</option>
            @endforeach
        </select>
        <button type="button" class="vf-clear-btn" x-show="locationSingle"
            @click="locationSingle = ''; applyFilters()" title="Clear">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
</div>

{{-- Make --}}
<div class="vf-row">
    <span class="vf-label">Make</span>
    <div class="vf-field-wrap">
        <input type="text" list="make-list" class="vf-input" x-model="makeSingle"
            @input.debounce.300ms="applyFilters()" placeholder="Type or select make"/>
        <button type="button" class="vf-clear-btn" x-show="makeSingle"
            @click="makeSingle = ''; applyFilters()" title="Clear">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
</div>

{{-- Model --}}
<div class="vf-row">
    <span class="vf-label">Model</span>
    <div class="vf-field-wrap">
        <input type="text" list="model-list" class="vf-input" x-model="modelSingle"
            @input.debounce.300ms="applyFilters()" placeholder="Type or select model"/>
        <button type="button" class="vf-clear-btn" x-show="modelSingle"
            @click="modelSingle = ''; applyFilters()" title="Clear">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
</div>

{{-- Year Range --}}
<div class="vf-row">
    <span class="vf-label">Year Range</span>
    <div class="year-row">
        <select class="vf-input" x-model.number="yearFrom" @change="applyFilters()">
            @for($y = date('Y') + 1; $y >= 1990; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <span class="year-sep">–</span>
        <select class="vf-input" x-model.number="yearTo" @change="applyFilters()">
            @for($y = date('Y') + 1; $y >= 1990; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>
</div>

{{-- Odometer --}}
<div class="vf-row">
    <div class="flex items-center justify-between mb-2">
        <span class="vf-label" style="margin-bottom:0">Odometer (max)</span>
        <span style="font-size:11px;font-weight:600;color:#e5c363" x-text="`${odometerMax ? odometerMax.toLocaleString() : 0} mi`"></span>
    </div>
    <input type="range" class="odo-range" min="0" max="{{ $filterOptions['odometer_max'] ?? 250000 }}"
        x-model.number="odometerMax" @input.debounce.300ms="applyFilters()">
    <div class="flex justify-between" style="font-size:10px;color:#747780;margin-top:4px">
        <span>0</span><span>250k+</span>
    </div>
</div>

{{-- Fuel Type (checkboxes) --}}
@if(!empty($filterOptions['fuel_types']))
<div class="vf-row">
    <span class="vf-label">Fuel Type</span>
    <div class="vf-checkbox-list">
        @foreach ($filterOptions['fuel_types'] as $fuel)
        <label class="vf-check-item">
            <input type="checkbox" value="{{ $fuel }}" x-model="fuelTypeMulti" @change="applyFilters()"
                class="vf-checkbox">
            <span>{{ $fuel }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Color (checkboxes) --}}
@if(!empty($filterOptions['colors']))
<div class="vf-row">
    <span class="vf-label">Color</span>
    <div class="vf-checkbox-list">
        @foreach ($filterOptions['colors'] as $color)
        <label class="vf-check-item">
            <input type="checkbox" value="{{ $color }}" x-model="colorMulti" @change="applyFilters()"
                class="vf-checkbox">
            <span>{{ $color }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Damage Type (checkboxes) --}}
@if(!empty($filterOptions['damage_types']))
<div class="vf-row">
    <span class="vf-label">Damage Type</span>
    <div class="vf-checkbox-list">
        @foreach ($filterOptions['damage_types'] as $damage)
        <label class="vf-check-item">
            <input type="checkbox" value="{{ $damage }}" x-model="damageTypeMulti" @change="applyFilters()"
                class="vf-checkbox">
            <span>{{ $damage }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Search / Reset buttons --}}
<div style="display:flex;gap:.5rem;margin-top:.5rem">
    <button type="button"
        class="vf-btn-search"
        @click="applyFilters(); if (window.CaymarkUI && CaymarkUI.mobile) CaymarkUI.mobile.closeBottomSheet('cm-auction-filter-sheet')">
        <span class="material-symbols-outlined" style="font-size:17px">search</span>
        Search
    </button>
    <button type="button"
        class="vf-btn-reset"
        @click="clearAllFilters(); if (window.CaymarkUI && CaymarkUI.mobile) CaymarkUI.mobile.closeBottomSheet('cm-auction-filter-sheet')">
        Reset
    </button>
</div>

@once
<datalist id="make-list">
    @foreach ($filterOptions['makes'] as $make)<option value="{{ $make }}">@endforeach
</datalist>
<datalist id="model-list">
    @foreach ($filterOptions['models'] as $model)<option value="{{ $model }}">@endforeach
</datalist>
@endonce
