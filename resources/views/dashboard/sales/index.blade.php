@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-8">
                <div class="container-box">
                    <input type="text" class="search-bar" placeholder="Search Item here....">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>${{ number_format($sale->price, 2) }}</td>
                                <td>{{ $sale->user->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No item here</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="container-box">
                    <h5><strong>STOCK</strong></h5>
                    <a href="{{ route('products.create') }}" class="btn btn-green w-100 mb-2">ADD PRODUCTS</a>
                    <button class="btn btn-green w-100 mb-2">REPORTS</button>
                    <h6>Select Customer</h6>
                    <!-- <div class="border p-2 mb-3">TOTAL: 3,000</div>
                    <div class="border p-2 mb-3">Discounts: 50</div>
                    <div class="border p-2 total-box">Net Total: 5,550</div>
                    <button class="btn btn-green w-100 mb-2">RESET</button> -->
                    <button class="btn btn-green w-100">PAY NOW</button>
                </div>
            </div>
        </div>
    </div>

@endsection