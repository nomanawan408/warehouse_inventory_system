@extends('layouts.app')

@section('content') 
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Account Transaction History</h4>
                        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Back to Accounts</a>
                    </div>
                    
                    <!-- Transaction Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Debits/Purchases</h5>
                                    <h3 class="card-text">Rs. {{ number_format(array_sum(array_column($formattedTransactions, 'debit')), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Credits/Paid</h5>
                                    <h3 class="card-text">Rs. {{ number_format(array_sum(array_column($formattedTransactions, 'credit')), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Balance</h5>
                                    <h3 class="card-text">Rs. {{ number_format(end($formattedTransactions)['balance'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
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
                                    <td>{{ $transaction['transaction_date'] }}</td>
                                    <td>{{ $transaction['debit'] ? number_format((float)$transaction['debit'], 2) : '' }}</td>
                                    <td>{{ $transaction['credit'] ? number_format((float)$transaction['credit'], 2) : '' }}</td>
                                    <td>{{ number_format((float)$transaction['balance'], 2) }}</td>
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
