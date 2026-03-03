@extends('layouts.welcome')

@section('content')

@php
    $depositThreshold = 2000;
    $depositPercent = 10;
    $buyerFeeRate = 6;
    $buyerFeeMin = 100;
@endphp

<style>
    .calc-hero {
        background: linear-gradient(135deg, #063466 0%, #1e3a8a 50%, #2563eb 100%);
        padding: 3rem 2rem;
    }
    .calc-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        padding: 2rem;
    }
    .calc-input {
        width: 100%;
        padding: 1rem 1.25rem;
        font-size: 1.5rem;
        font-weight: 600;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .calc-input:focus {
        outline: none;
        border-color: #063466;
        box-shadow: 0 0 0 3px rgba(6, 52, 102, 0.15);
    }
    .result-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .result-row:last-child { border-bottom: none; }
    .result-row.highlight {
        background: #f8fafc;
        margin: 0 -1.5rem;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }
    .result-value { font-size: 1.25rem; font-weight: 700; color: #063466; }
</style>

<!-- Hero -->
<div class="calc-hero text-white text-center">
    <div class="container mx-auto">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Fee Calculator</h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto">
            See how much you need before you commit to an auction
        </p>
    </div>
</div>

<!-- Calculator -->
<div class="container mx-auto px-4 py-10">
    <div class="max-w-xl mx-auto">
        <div class="calc-card" id="fee-calc">
            <p class="text-gray-600 text-sm mb-4">
                Enter the amount you plan to bid (or the vehicle price) to see your security deposit, the total you pay for the item, and CayMark’s fees.
            </p>

            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount ($)</label>
            <input type="number"
                   id="amount"
                   min="0"
                   step="100"
                   placeholder="e.g. 5000"
                   class="calc-input mb-6">

            <div id="results" class="pt-2 border-t border-gray-200" style="display: none;">
                <div class="result-row">
                    <span class="text-gray-700 font-medium">1. Security deposit (on your account)</span>
                    <span class="result-value" id="out-deposit">$0.00</span>
                </div>
                <div class="result-row">
                    <span class="text-gray-700 font-medium">2. What you pay for the vehicle</span>
                    <span class="result-value" id="out-vehicle">$0.00</span>
                </div>
                <div class="result-row">
                    <span class="text-gray-700 font-medium">3. CayMark fees (on top of vehicle price)</span>
                    <span class="result-value" id="out-fees">$0.00</span>
                </div>
                <div class="result-row highlight">
                    <span class="text-gray-800 font-bold">Total due at checkout (vehicle + fees)</span>
                    <span class="text-xl font-bold text-[#063466]" id="out-total">$0.00</span>
                </div>
            </div>

            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-900">
                <p class="font-semibold mb-1">How it works</p>
                <ul class="list-disc list-inside space-y-0.5 text-amber-800">
                    <li>Bids of ${{ number_format($depositThreshold) }}+ require a {{ $depositPercent }}% security deposit in your account.</li>
                    <li>CayMark’s buyer fee is {{ $buyerFeeRate }}% of the sale price (minimum ${{ number_format($buyerFeeMin) }}).</li>
                    <li>Deposit is applied toward your final payment if you win.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var threshold = {{ $depositThreshold }};
    var depositPct = {{ $depositPercent }};
    var feeRate = {{ $buyerFeeRate }};
    var feeMin = {{ $buyerFeeMin }};

    function format(n) {
        return '$' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function run() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var resultsEl = document.getElementById('results');
        if (amount <= 0) {
            resultsEl.style.display = 'none';
            return;
        }
        var deposit = amount >= threshold ? Math.round(amount * (depositPct / 100) * 100) / 100 : 0;
        var fees = Math.round(Math.max(amount * (feeRate / 100), feeMin) * 100) / 100;
        var total = Math.round((amount + fees) * 100) / 100;

        document.getElementById('out-deposit').textContent = format(deposit);
        document.getElementById('out-vehicle').textContent = format(amount);
        document.getElementById('out-fees').textContent = format(fees);
        document.getElementById('out-total').textContent = format(total);
        resultsEl.style.display = 'block';
    }

    document.getElementById('amount').addEventListener('input', run);
    document.getElementById('amount').addEventListener('change', run);
})();
</script>

@endsection
