@extends('layouts.app')

@section('content') 
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>{{ $account->company->name }} - Transactions History</h4>    
                        <a href="{{ route('companies.accounts') }}" class="btn btn-secondary">Back to Company Accounts</a>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card shadow-sm border-primary h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 text-primary"></i>
                                    <h6 class="text-muted">Total Purchases</h6>
                                    <h4 class="text-primary mb-0">Rs. {{ number_format($account->total_purchases, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-2x mb-2 text-success"></i>
                                    <h6 class="text-muted">Total Paid</h6>
                                    <h4 class="text-success mb-0">Rs. {{ number_format($account->total_paid, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-danger h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-balance-scale fa-2x mb-2 text-danger"></i>
                                    <h6 class="text-muted">Pending Balance</h6>
                                    <h4 class="text-danger mb-0">Rs. {{ number_format($account->pending_balance, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-info h-100">
                                <div class="card-body text-center">
                                    <i class="far fa-calendar-alt fa-2x mb-2 text-info"></i>
                                    <h6 class="text-muted">Last Payment Date</h6>
                                    <h4 class="text-info mb-0">
                                        @php
                                        $lastPayment = collect($formattedTransactions)
                                            ->where('credit', '>', 0)
                                            ->last();
                                        $formattedDate = $lastPayment ? date('Y-m-d', strtotime($lastPayment['transaction_date'])) : 'No payments yet';
                                    @endphp
                                    {{ $formattedDate }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="companyAccountTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Purchase</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Detail</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formattedTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction['transaction_date'] }}</td>
                                    <td>{{ $transaction['debit'] ? 'Rs. ' . number_format($transaction['debit'], 2) : '' }}</td>
                                    <td>{{ $transaction['credit'] ? 'Rs. ' . number_format($transaction['credit'], 2) : '' }}</td>
                                    <td>Rs. {{ number_format($transaction['balance'], 2) }}</td>
                                    <td>{{ $transaction['reference'] }}</td>
                                    <td>
                                        <a href="{{ route('companies.transactions.edit', [$account->id, $transaction['id']]) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('companies.transactions.delete', [$account->id, $transaction['id']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
            $('#companyAccountTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'asc']]
            });
        });
    </script>
@endsection
