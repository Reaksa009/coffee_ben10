@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">New Category</h1>
            <p class="page-subtitle">Create a product category for menu filtering and checkout.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Categories
        </a>
    </div>

    <div class="app-card p-4" style="max-width: 560px;">
        <form method="POST" action="{{ route('categories.store') }}">
            @include('categories.form', ['category' => null])
        </form>
    </div>
@endsection
