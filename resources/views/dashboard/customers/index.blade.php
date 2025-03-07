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
                    
                    <table id="customerTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Business Name</th>
                                <th>Phone No</th>
                                <th>CNIC</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->business_name }}</td>
                                    <td>{{ $customer->phone_no }}</td>
                                    <td>{{ $customer->cnic }}</td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-start align-items-center"  >
                                            <a href="{{  route('accounts.transactions', $customer->id) }}" class="btn btn-primary btn-sm shadow-sm rounded-pill" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" data-bs-toggle="tooltip" data-bs-placement="top" title="View Company Transactions">
                                                <i class="ti ti-eye" style="font-size: 1.2rem;"></i>
                                            </a>
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm shadow-sm rounded-pill" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Customer">
                                                <i class="ti ti-edit" style="font-size: 1.2rem;"></i>
                                            </a>
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline-block m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm shadow-sm rounded-pill" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" onclick="return confirm('Are you sure you want to delete this customer?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Customer">
                                                    <i class="ti ti-trash" style="font-size: 1.2rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        $('#customerTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>

@endsection