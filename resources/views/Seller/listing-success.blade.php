@extends('layouts.dashboard')

@section('title', 'Listing Submitted Successfully - CayMark')

@section('content')
<style>
    @keyframes checkmark {
        0% { stroke-dashoffset: 100; }
        100% { stroke-dashoffset: 0; }
    }
    
    .success-container {
        padding: 2rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        min-height: auto;
    }
    
    .success-card {
        background: white;
        border-radius: 12px;
        padding: 2.5rem;
        max-width: 700px;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        text-align: center;
        position: relative;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: #10b981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    
    .success-icon svg {
        width: 45px;
        height: 45px;
        stroke: white;
        stroke-width: 3;
        fill: none;
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: checkmark 0.6s ease-out 0.2s forwards;
    }
    
    .success-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }
    
    .success-message {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    
    .listing-details {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1.5rem;
        margin: 2rem 0;
        text-align: left;
        border: 1px solid #e5e7eb;
    }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #4b5563;
        font-size: 0.9375rem;
    }
    
    .detail-value {
        color: #111827;
        font-weight: 500;
        font-size: 0.9375rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .btn-primary-action {
        background: #2563eb;
        color: white;
        padding: 0.75rem 1.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-primary-action:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .btn-secondary-action {
        background: white;
        color: #2563eb;
        padding: 0.75rem 1.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: 2px solid #2563eb;
    }
    
    .btn-secondary-action:hover {
        background: #eff6ff;
        transform: translateY(-1px);
    }
    
    .next-steps {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: 8px;
        padding: 1.25rem;
        margin-top: 2rem;
        text-align: left;
    }
    
    .next-steps h3 {
        color: #92400e;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .next-steps ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .next-steps li {
        color: #78350f;
        padding: 0.5rem 0;
        display: flex;
        align-items: start;
        gap: 0.75rem;
        font-size: 0.9375rem;
        line-height: 1.5;
    }
    
    .next-steps li::before {
        content: "✓";
        color: #10b981;
        font-weight: bold;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .status-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-block;
    }
</style>

<div class="success-container">
    <div class="success-card">
        <!-- Success Icon -->
        <div class="success-icon">
            <svg viewBox="0 0 52 52">
                <circle cx="26" cy="26" r="25" fill="none" stroke="white" stroke-width="2"/>
                <path d="M14 26 L22 34 L38 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
        </div>
        
        <!-- Success Title -->
        <h1 class="success-title">Listing Submitted Successfully!</h1>
        
        <!-- Success Message -->
        <p class="success-message">
            Your listing has been submitted and is now pending admin review. 
            You'll receive a notification once it's approved and goes live.
        </p>
        
        <!-- Listing Details -->
        <div class="listing-details">
            <div class="detail-item">
                <span class="detail-label">Vehicle:</span>
                <span class="detail-value">
                    {{ $listing->year ?? 'N/A' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '' }}
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Listing ID:</span>
                <span class="detail-value">#{{ $listing->id }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge">PENDING REVIEW</span>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Submitted:</span>
                <span class="detail-value">{{ $listing->created_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>
        
        <!-- Next Steps -->
        <div class="next-steps">
            <h3>
                <span class="material-icons-round" style="font-size: 1.25rem;">info</span>
                What Happens Next?
            </h3>
            <ul>
                <li>Our team will review your listing within 24-48 hours</li>
                <li>You'll receive an email notification once approved</li>
                <li>Your listing will go live and be visible to buyers</li>
                <li>You can track the status in "My Listings" section</li>
            </ul>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('seller.listings.index') }}" class="btn-primary-action">
                <span class="material-icons-round" style="font-size: 1.125rem;">list_alt</span>
                View My Listings
            </a>
            <a href="{{ route('seller.listings.create') }}" class="btn-secondary-action">
                <span class="material-icons-round" style="font-size: 1.125rem;">add_circle</span>
                Submit Another Listing
            </a>
        </div>
    </div>
</div>
@endsection
