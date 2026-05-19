@extends('layouts.app')

@section('page-title', 'Profile')

@section('content')

<div class="profile-container">
    <!-- Profile Sidebar Card -->
    <div class="profile-card-col">
        <div class="card profile-card">
            <div class="profile-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
            <h3 class="profile-name">{{ Auth::user()->name }}</h3>
            <p class="profile-role">{{ auth()->user()->isAdmin() ? 'Admin' : 'Employee' }}</p>
        </div>
    </div>

    <!-- Profile Form Cards -->
    <div class="profile-forms">
        <div class="card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card danger-zone">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

@endsection
