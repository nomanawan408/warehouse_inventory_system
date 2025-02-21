@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Items Stock</h4>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Item</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="col-md-12">
                            <input type="text" class="form-control search-bar me-3" placeholder="Search Item here...">
                        </div>
                        
                    </div>
                    
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>PRODUCT NAME</th>
                                <th>Sale Price</th>
                                <th>Purchase Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sale_price }}</td>
                                    <td>{{ $product->purchase_price }}</td>
                                    @if ($product->quantity > 0)
                                        <td>{{ $product->quantity }}</td>
                                    @else
                                        <td class="text-danger">Out of stock</td>
                                    @endif
                                </tr>
                                
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection