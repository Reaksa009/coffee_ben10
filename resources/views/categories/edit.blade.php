@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Category</h1>
            <p class="page-subtitle">{{ $category->name }}</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Categories
        </a>
    </div>

    <div class="app-card p-4" style="max-width: 560px;">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @method('PUT')
            @include('categories.form')
        </form>
    </div>
@endsection
