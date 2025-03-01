@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Stock List</h4>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Item</a>
                    </div>
                  
                    
                    <table id="productTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Product Name</th>
                                <th>Company Name</th>
                                <th>Sale Price</th>
                                <th>Purchase Price</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->company->name }}</td>
                                    <td>{{ $product->sale_price }}</td>
                                    <td>{{ $product->purchase_price }}</td>
                                    @if ($product->quantity > 0)
                                        <td>{{ $product->quantity }}</td>
                                    @else
                                        <td class="text-danger">Out of stock</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $product->id }}">
                                            Update Stock
                                        </button>

                                        <!-- Update Stock Modal -->
                                        <div class="modal fade" id="updateStockModal{{ $product->id }}" tabindex="-1" aria-labelledby="updateStockModalLabel{{ $product->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateStockModalLabel{{ $product->id }}">Update Stock for {{ $product->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('products.updateStock', $product->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label for="oldQuantity{{ $product->id }}" class="form-label">Old Quantity</label>
                                                                <input type="number" class="form-control" id="oldQuantity{{ $product->id }}" name="old_quantity" value="{{ $product->quantity }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="newQuantity{{ $product->id }}" class="form-label">New Quantity</label>
                                                                <input type="number" class="form-control" id="newQuantity{{ $product->id }}" name="new_quantity" required>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Update Stock</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                        </form>
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
        $('#productTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'pdf','print', 
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape'
                }
            ]
        });
    });
</script>

@endsection