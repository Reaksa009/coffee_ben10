@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">New Product</h1>
            <p class="page-subtitle">Create a product for the POS catalog.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Products
        </a>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @include('products.form')
    </form>
@endsection
