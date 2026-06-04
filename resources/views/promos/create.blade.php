@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create Promo Code</h1>
            <p class="page-subtitle">Set up a discount for checkout.</p>
        </div>
        <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @include('promos.form', [
        'promo' => null,
        'action' => route('promos.store'),
        'method' => 'POST',
        'submitLabel' => 'Create Promo',
    ])
@endsection
