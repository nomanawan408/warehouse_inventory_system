@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Stock List</h4>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">Add Item</a>
                    </div>
                  
                    
                    <table id="saleTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Discount</th>
                                {{-- <th>Tax</th> --}}
                                <th>Net Total</th>
                                <th>Amount Paid</th>
                                <th>Pending Amount</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{ $sale->customer->name }}</td>
                                    <td>{{ $sale->total_amount }}</td>
                                    <td>{{ $sale->discount }}</td>
                                    {{-- <td>{{ $sale->tax }}</td> --}}
                                    <td>{{ $sale->net_total }}</td>
                                    <td>{{ $sale->amount_paid }}</td>
                                    <td>{{ $sale->pending_amount }}</td>
                                    {{-- <td>
                                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sale?')">Delete</button>
                                        </form>
                                    </td> --}}
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
        $('#saleTable').DataTable({
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