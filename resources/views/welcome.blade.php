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
                                <th>PRODUCT NAME</th>
                                <th>QTY</th>
                                <th>PRICE</th>
                                <th>Discount</th>
                                <th>Total</ th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Lays Sneaks 100gm</td>
                                <td>100</td>
                                <td>05</td>
                                <td>05</td>
                                <td>550</td>
                            </tr>
                            <tr>
                                <td>Lays Sneaks 100gm</td>
                                <td>100</td>
                                <td>05</td>
                                <td>05</td>
                                <td>550</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="container-box">
                    <h5><strong>STOCK</strong></h5>
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