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
                                <th>Customer Name</th>
                                <th>Total Purchases</th>
                                <th>Total Paid</th>
                                <th>Pending Balance</th>
                                <th>Last Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($accounts) > 0)
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>{{ $account->customer->name }}</td>
                                        <td>Rs. {{ number_format($account->total_purchases, 2) }}</td>
                                        <td>Rs. {{ number_format($account->total_paid, 2) }}</td>
                                        <td>Rs. {{ number_format($account->pending_balance, 2) }}</td>
                                        <td>
                                            {{ $account->last_payment_date ? \Carbon\Carbon::parse($account->last_payment_date)->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal{{ $account->id }}">
                                                    Add Payment
                                                </button> &nbsp;
                                                {{-- <a href="{{ route('accounts.show', $account->id) }}" class="btn btn-primary btn-sm">View</a>  --}}
                                                <a href="{{ route('accounts.transactions', $account->id) }}" class="btn btn-secondary btn-sm">View</a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="addPaymentModal{{ $account->id }}" tabindex="-1" aria-labelledby="addPaymentModalLabel{{ $account->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="addPaymentModalLabel{{ $account->id }}">Add Payment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('accounts.payments.store', $account->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <label for="paymentAmount" class="form-label">Payment Amount</label>
                                                                        <input type="number" class="form-control" id="paymentAmount" name="payment_amount" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="paymentDate" class="form-label">Payment Date</label>
                                                                        <input type="date" class="form-control" id="paymentDate" name="payment_date" value="{{ now()->format('Y-m-d') }}" required>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No accounts found.</td>
                                </tr>
                            @endif
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