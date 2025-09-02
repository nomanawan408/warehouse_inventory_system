@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Edit Transaction - {{ $account->customer->name }}</h4>
                        <a href="{{ route('accounts.transactions', $account->id) }}" class="btn btn-secondary">Back to Transactions</a>
                    </div>
                    
                    <form action="{{ route('accounts.transactions.update', [$account->id, $transaction->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_type" class="form-label">Transaction Type</label>
                                    <select class="form-control" id="transaction_type" name="transaction_type" disabled>
                                        <option value="credit" {{ $transaction->transaction_type == 'credit' ? 'selected' : '' }}>
                                            Payment (Credit)
                                        </option>
                                        <option value="debit" {{ $transaction->transaction_type == 'debit' ? 'selected' : '' }}>
                                            Purchase (Debit)
                                        </option>
                                    </select>
                                    <input type="hidden" name="transaction_type" value="{{ $transaction->transaction_type }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (Rs.)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="{{ old('amount', $transaction->amount) }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label">Transaction Date</label>
                                    <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <input type="text" class="form-control" id="payment_method" name="payment_method" value="{{ old('payment_method', $transaction->payment_method) }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="detail" class="form-label">Detail</label>
                            <input type="text" class="form-control" id="detail" name="detail" value="{{ old('detail', $transaction->detail) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $transaction->reference) }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Transaction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
