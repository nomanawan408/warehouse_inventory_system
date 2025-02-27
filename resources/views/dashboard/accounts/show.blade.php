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
                    <table id="customerAccountTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formattedTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction['date'] }}</td>
                                    <td>{{ $transaction['debit'] }}</td>
                                    <td>{{ $transaction['credit'] }}</td>
                                    <td>{{ $transaction['balance'] }}</td>
                                    <td>{{ $transaction['detail'] }}</td>
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
            $('#customerAccountTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
