@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Invoice</h1>
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="customer">Customer</label>
            <input type="text" class="form-control" id="customer" name="customer" required>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="discount">Discount</label>
            <input type="number" class="form-control" id="discount" name="discount" required>
        </div>
        <div class="form-group">
            <label for="items">Items</label>
            <textarea class="form-control" id="items" name="items" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>
@endsection