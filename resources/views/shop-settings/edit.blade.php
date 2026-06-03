@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Shop Settings</h1>
            <p class="page-subtitle">Configure receipt details, currency, and optional charges.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the highlighted fields.</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('shop-settings.update') }}">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <section class="app-card p-4">
                    <div class="mb-3">
                        <label class="form-label">Shop Name</label>
                        <input type="text" name="shop_name" value="{{ old('shop_name', $settings->shop_name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $settings->address) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Footer</label>
                        <textarea name="receipt_footer" class="form-control" rows="3" placeholder="Thank you for your order.">{{ old('receipt_footer', $settings->receipt_footer) }}</textarea>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="app-card p-4">
                    <div class="mb-3">
                        <label class="form-label">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $settings->currency) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Printer Width</label>
                        <select name="receipt_width_mm" class="form-select" required>
                            <option value="80" @selected((int) old('receipt_width_mm', $settings->receipt_width_mm) === 80)>80mm thermal</option>
                            <option value="58" @selected((int) old('receipt_width_mm', $settings->receipt_width_mm) === 58)>58mm thermal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tax Rate %</label>
                        <input type="number" step="0.001" min="0" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate) }}" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Service Charge %</label>
                        <input type="number" step="0.001" min="0" name="service_charge_rate" value="{{ old('service_charge_rate', $settings->service_charge_rate) }}" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Save Settings
                    </button>
                </section>
            </div>
        </div>
    </form>
@endsection
