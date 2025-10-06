@extends('layouts.Seller')

@section('title', 'Buyer Dashboard')
<link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined&display=swap" rel="stylesheet">


@section('content')
    <div class="dashboard-container">
        <!-- Header Section -->
        <header class="dashboard-header">
            <div class="header-content">
                <h1 class="dashboard-title">SELLER Dashboard</h1>
                <p class="dashboard-subtitle">Welcome back, {{ Auth::user()->name }}! Here's what's happening today.</p>
            </div>

            <div class="header-actions">
                <div class="notification-badge">
                    <span class="material-icons-outlined">notifications</span>
                    <span class="badge-count">3</span>
                </div>

                <div class="user-profile-dropdown">
                    <img class="user-avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsKVAK8aIjaUQuRumMm4PebDgiI8DzQRkGn40Q2xsad_NjUATKCbNUmIUPWUxqVKb71jMgdGesyKWfwLZVSdMoI8zyEhTmAlUTthIoi8Vx-iQ4p_GUMTHJo2qCZwfp16xYuIA-jrZLbLw_eXEdGiezW1CAVIKnXemlKUvNmT8cm1P-uJ4tbnFsQcLRl8B1JncWpokSQxbKMdTd7lV11sdqN61KDbp0crLEdR4N0hWSSzr_7plPjwFYZavL5TfJulmifymzv1acMig" alt="User Avatar" />
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">Premium Buyer</span>
                    </div>
                    <span class="material-icons-outlined dropdown-icon">expand_more</span>
                </div>
            </div>
        </header>





@endsection
