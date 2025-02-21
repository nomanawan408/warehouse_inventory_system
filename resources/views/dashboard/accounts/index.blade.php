@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Accounts List</h4>
                        {{-- <a href="{{ route('customers.create') }}" class="btn btn-primary">Create Customer</a> --}}
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="col-md-12">
                            <input type="text" class="form-control search-bar me-3" placeholder="Search Item here...">
                        </div>
                        
                    </div>
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Customer Name</th>
                                <th>Paid/Advance Amount</th>
                                <th>Pending Amount</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($accounts) > 0)
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>{{ $account->customer->name }}</td>
                                        <td>Rs. {{ number_format($account->paid_amount, 2) }}</td>
                                        <td>Rs. {{ number_format($account->pedding_amount, 2) }}</td>
                                        <td>{{ $account->updated_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('accounts.show', $account->id) }}" class="btn btn-primary  btn-sm">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No accounts found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection