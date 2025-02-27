@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Edit Item</h4>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                  
            <form method="POST" action="{{ route('products.update', $product->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" required autofocus value="{{ old('name', $product->name) }}">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Purchase Price -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="purchase_price">Purchase Price</label>
                            <input type="number" step="0.01"
                                class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price"
                                name="purchase_price" required value="{{ old('purchase_price', $product->purchase_price) }}">
                            @error('purchase_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Sale Price -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="sale_price">Sale Price</label>
                            <input type="number" step="0.01"
                                class="form-control @error('sale_price') is-invalid @enderror" id="sale_price"
                                name="sale_price" required value="{{ old('sale_price', $product->sale_price) }}">
                            @error('sale_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                id="quantity" name="quantity" required value="{{ old('quantity', $product->quantity) }}">
                            @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                      
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="company_id">Select Company</label>
                            <select class="form-control @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                <option value="" disabled>Select a company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id', $product->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="created_at">Date</label>
                            <input type="datetime-local" class="form-control @error('created_at') is-invalid @enderror"
                                id="created_at" name="created_at" required 
                                value="{{ old('created_at', $product->created_at->format('Y-m-d\TH:i')) }}">
                            @error('created_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>  
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
