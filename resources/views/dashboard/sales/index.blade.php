@extends('layouts.app')

@section('content')
    <div class="container my-4">
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
                                    <td>{{ $sale->customer->name }}</td>
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
                                        <button class="btn btn-success fo btn-sm view-invoice" data-sale-id="{{ $sale->id }}">Invoice</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @include('dashboard.sales.invoice-modal')

<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
        var table = $('#saleTable').DataTable({
            dom: 'Bfrtip',
            order: [[0, 'desc']], // Order by the Invoice # column (index 0) in descending order
            buttons: [
                'excel', 'csv', 'pdf', 'print', 
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape'
                }
            ]
        });

        // Invoice view handler using event delegation
        $('#saleTable').on('click', '.view-invoice', function() {
            var saleId = $(this).data('sale-id');
            window.open(`/sales/${saleId}/print`, '_blank');
        });

        // Custom filter for daily, weekly, and monthly records
        $('#filter').on('change', function() {
            var filterValue = $(this).val();
            var startDate, endDate;

            if (filterValue === 'daily') {
                startDate = moment().startOf('day');
                endDate = moment().endOf('day');
            } else if (filterValue === 'weekly') {
                startDate = moment().startOf('week');
                endDate = moment().endOf('week');
            } else if (filterValue === 'monthly') {
                startDate = moment().startOf('month');
                endDate = moment().endOf('month');
            } else {
                startDate = null;
                endDate = null;
            }

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var date = moment(data[6]); // Assuming the "Last Update" column is the 7th column (index 6)
                if (!startDate || !endDate) {
                    return true;
                }
                return date.isBetween(startDate, endDate, null, '[]');
            });

            table.draw();
            $.fn.dataTable.ext.search.pop();
        });
    });
</script>

@endsection