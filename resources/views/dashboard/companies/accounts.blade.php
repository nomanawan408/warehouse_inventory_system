@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Company Accounts</h4>
                    </div>
                    
                    <table id="companyAccountsTable" class="table table-bordered table-hover table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Total Purchases</th>
                                <th>Total Paid</th>
                                <th>Pending Balance</th>
                                <th>Last Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>{{ $company->company->name }}</td>
                                    <td>{{ $company->company->address }}</td>
                                    <td>{{ $company->total_purchases }}</td>
                                    <td>{{ $company->total_paid }}</td>
                                    <td>{{ $company->pending_balance }}</td>
                                    <td>{{ $company->last_payment_date ? date('d-m-Y', strtotime($company->last_payment_date)) : 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <!-- Add Payment Button -->
                                            <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill me-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" data-bs-toggle="modal" data-bs-target="#addPaymentModal{{ $company->id }}" title="Add Payment">
                                                <i class="ti ti-cash" style="font-size: 1.2rem;"></i>
                                            </button>

                                            <!-- View Transactions Button -->
                                            <a href="{{ route('companies.transactions', $company->id) }}" class="btn btn-success btn-sm shadow-sm rounded-pill" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; padding: 0;" data-bs-toggle="tooltip" data-bs-placement="top" title="View Company Transactions">
                                                <i class="ti ti-eye" style="font-size: 1.2rem;"></i>
                                            </a>
                                        </div>

                                        <!-- Add Payment Modal -->
                                        <div class="modal fade" id="addPaymentModal{{ $company->id }}" tabindex="-1" aria-labelledby="addPaymentModalLabel{{ $company->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addPaymentModalLabel{{ $company->id }}">Add Payment for {{ $company->company->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('companies.record-payment', $company->company_id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="amount" class="form-label">Payment Amount (Rs.)</label>
                                                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required min="0.01" max="{{ $company->pending_balance }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="payment_date" class="form-label">Payment Date</label>
                                                                <input type="date" class="form-control" id="payment_date" name="payment_date" required value="{{ date('Y-m-d') }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="notes" class="form-label">Notes</label>
                                                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Add Payment</button>
                                                        </div>
                                                    </form>
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
        $('#companyAccountsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>

@endsection