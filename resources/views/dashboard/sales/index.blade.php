@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- Sales List -->
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold" style="color: var(--primary-start);">Sales List</h4>
                    </div>
                    
                    <div class="mb-3">
                        <label for="filter">Filter by:</label>
                        <select id="filter" class="form-control">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="daily" {{ $filter === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $filter === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $filter === 'monthly' ? 'selected' : '' }}>Monthly</option>
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
                            {{-- Rows are loaded page-by-page from the server (see script below). --}}
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
            var dataUrl = @json(route('sales.data'));
            var exportBaseUrl = @json(url('/sales/export'));

            function exportUrl(format) {
                var params = new URLSearchParams({
                    filter: $('#filter').val(),
                    search: table.search()
                });
                return exportBaseUrl + '/' + format + '?' + params.toString();
            }

            // Server-side table: only the visible page is ever transferred.
            var table = $('#saleTable').DataTable({
                dom: 'Bfrtip',
                processing: true,
                serverSide: true,
                order: [[7, 'desc']],
                pageLength: 25,
                ajax: {
                    url: dataUrl,
                    data: function(d) {
                        d.filter = $('#filter').val();
                    }
                },
                columns: [
                    { data: 'invoice', name: 'id' },
                    { data: 'customer_name', name: 'customer' },
                    { data: 'total_amount', name: 'total' },
                    { data: 'discount', name: 'discount' },
                    { data: 'net_total', name: 'net_total' },
                    { data: 'amount_paid', name: 'paid' },
                    {
                        data: 'pending_amount',
                        name: 'pending',
                        render: function(data) {
                            if (parseFloat(data) == 0) {
                                return '<span class="badge bg-success">Paid</span>';
                            }
                            return '<span class="badge bg-danger">' + $('<div>').text(data).html() + '</span>';
                        }
                    },
                    { data: 'updated_at', name: 'updated' },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            var id = parseInt(data, 10);
                            return '<div class="d-flex gap-2 justify-content-start align-items-center">' +
                                '<button class="btn btn-sm shadow-sm rounded-pill view-invoice"' +
                                ' style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0; background: linear-gradient(135deg, #198754, #20c997); border: none; color: #fff;"' +
                                ' data-sale-id="' + id + '"' +
                                ' data-bs-toggle="tooltip" data-bs-placement="top" title="View Invoice">' +
                                '<i class="ti ti-file-invoice" style="font-size: 1.2rem;"></i></button>' +
                                '<a href="/sales/' + id + '/edit" class="btn btn-sm shadow-sm rounded-pill"' +
                                ' style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0; background: linear-gradient(135deg, #6a11cb, #2575fc); border: none; color: #fff;"' +
                                ' data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Invoice">' +
                                '<i class="ti ti-edit" style="font-size: 1.2rem;"></i></a></div>';
                        }
                    }
                ],
                buttons: [
                    {
                        text: 'CSV (all filtered)',
                        action: function() { window.location = exportUrl('csv'); }
                    },
                    {
                        text: 'PDF (all filtered)',
                        action: function() { window.location = exportUrl('pdf'); }
                    },
                    'print'
                ],
                drawCallback: function() {
                    // Tooltips only exist for the freshly drawn page.
                    $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
                }
            });

            // View invoice handler (delegated: rows are replaced on every draw)
            $('#saleTable').on('click', '.view-invoice', function() {
                var saleId = $(this).data('sale-id');
                window.open(`/sales/${saleId}/print`, '_blank');
            });

            // Date-range filter is applied server-side, then the page reloads.
            $('#filter').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection