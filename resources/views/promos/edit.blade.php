@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Promo Code</h1>
            <p class="page-subtitle">{{ $promo->code }}</p>
        </div>
        <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @include('promos.form', [
        'promo' => $promo,
        'action' => route('promos.update', $promo),
        'method' => 'PUT',
        'submitLabel' => 'Update Promo',
    ])
@endsection
