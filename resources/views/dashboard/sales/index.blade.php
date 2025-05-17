@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- Sales List -->
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Sales List</h4>
                    </div>
                    
                    <div class="mb-3">
                        <label for="filter">Filter by:</label>
                        <select id="filter" class="form-control">
                            <option value="">All</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    
                    <table id="saleTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Discount</th>
                                <th>Net Total</th>
                                <th>Amount Paid</th>
                                <th>Pending Amount</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td># {{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $sale->customer ? $sale->customer->name : 'Deleted Customer' }}</td>
                                    <td>{{ $sale->total_amount }}</td>
                                    <td>{{ $sale->discount }}</td>
                                    <td>{{ $sale->net_total }}</td>
                                    <td>{{ $sale->amount_paid }}</td>
                                    <td>
                                        @if ($sale->pending_amount == 0)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-danger">{{ $sale->pending_amount }}</span>
                                        @endif  
                                    </td>
                                    <td>{{ $sale->updated_at }}</td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-start align-items-center">
                                            <button class="btn btn-success btn-sm shadow-sm rounded-pill view-invoice" 
                                                    style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" 
                                                    data-sale-id="{{ $sale->id }}" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="View Invoice">
                                                <i class="ti ti-file-invoice" style="font-size: 1.2rem;"></i>
                                            </button>
                                            <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary btn-sm shadow-sm rounded-pill" 
                                                    style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="Edit Invoice">
                                                <i class="ti ti-edit" style="font-size: 1.2rem;"></i>
                                            </a>
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

    <!-- Required CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

    <!-- Required JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#saleTable').DataTable({
                dom: 'Bfrtip',
                order: [[7, 'desc']],
                buttons: [
                    'excel', 'csv', 'pdf', 'print', 
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape'
                    }
                ]
            });

            // View invoice handler
            $('#saleTable').on('click', '.view-invoice', function() {
                var saleId = $(this).data('sale-id');
                window.open(`/sales/${saleId}/print`, '_blank');
            });

            // Filter functionality
            $('#filter').on('change', function() {
                var filterValue = $(this).val();
                var today = moment();

                if (filterValue === 'daily') {
                    table.column(7).search(today.format('YYYY-MM-DD')).draw();
                } else if (filterValue === 'weekly') {
                    var startOfWeek = today.startOf('week').format('YYYY-MM-DD');
                    var endOfWeek = today.endOf('week').format('YYYY-MM-DD');
                    table.column(7).search(startOfWeek + '|' + endOfWeek, true, false).draw();
                } else if (filterValue === 'monthly') {
                    var startOfMonth = today.startOf('month').format('YYYY-MM-DD');
                    var endOfMonth = today.endOf('month').format('YYYY-MM-DD');
                    table.column(7).search(startOfMonth + '|' + endOfMonth, true, false).draw();
                } else {
                    table.column(7).search('').draw();
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection