@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Companies List</h4>
                        <a href="{{ route('companies.create') }}" class="btn btn-primary">Add New Company</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="col-md-12">
                            <input type="text" class="form-control search-bar me-3" placeholder="Search Item here...">
                        </div>
                        
                    </div>
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Phone No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @if (count($companies) > 0)
                                @foreach ($companies as $company)
                                    <tr>
                                        <td>{{ $company->name }}</td>
                                        <td>{{ $company->address }}</td>
                                        <td>{{ $company->phone_no }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                                                <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="d-inline">
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
                                    <td colspan="4" class="text-center">No companies found.</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection