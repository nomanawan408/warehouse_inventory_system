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
                            <table id="companiesTable" class="table table-bordered table-hover table-striped mt-3">
                                <thead class="table-dark">
                            <tr>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Phone No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->address }}</td>
                                    <td>{{ $company->phone_no }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                                            <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="d-inline me-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#purchaseStockModal{{ $company->id }}">
                                                Purchase Stock
                                            </button>
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="purchaseStockModal{{ $company->id }}" tabindex="-1" aria-labelledby="purchaseStockModalLabel{{ $company->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="purchaseStockModalLabel{{ $company->id }}">Purchase Stock</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('stocks.purchase', $company->id) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="product" class="form-label">Product</label>
                                                                <select class="form-control" id="product" name="product_id" required>
                                                                    @foreach ($company->products as $product)
                                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="stockAmount" class="form-label">Stock Amount</label>
                                                                    <input type="number" class="form-control" id="stockAmount" name="stock_amount" required>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="purchaseDate" class="form-label">Purchase Date</label>
                                                                    <input type="date" class="form-control" id="purchaseDate" name="purchase_date" value="{{ now()->format('Y-m-d') }}" required>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="mb-3">
                                                                <label for="purchaseDate" class="form-label">Purchase Date</label>
                                                                <input type="date" class="form-control" id="purchaseDate" name="purchase_date" value="{{ now()->format('Y-m-d') }}" required>
                                                            </div> --}}
                                                            <button type="submit" class="btn btn-warning">Submit</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
        $('#companiesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>

@endsection
