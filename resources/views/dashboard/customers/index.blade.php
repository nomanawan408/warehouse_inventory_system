@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Customer List</h4>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary">Create Customer</a>
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
                                <th>Business Name</th>
                                <th>Phone No</th>
                                <th>CNIC</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @if (count($customers) > 0)
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->business_name }}</td>
                                        <td>{{ $customer->phone_no }}</td>
                                        <td>{{ $customer->cnic }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No customers found.</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection