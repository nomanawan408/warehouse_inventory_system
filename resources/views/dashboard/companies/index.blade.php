@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-12">
                <div class="container-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Companies List</h4>
                        <a href="{{ route('companies.create') }}" class="btn btn-primary">Add New Company</a>
                    </div>
                            <table id="companiesTable" class="table table-bordered table-hover table-striped mt-3">
                                <thead class="table-dark">
                            <tr>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Phone No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->address }}</td>
                                    <td>{{ $company->phone_no }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                                            <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="d-inline me-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#purchaseStockModal{{ $company->id }}">
                                                Purchase Stock
                                            </button>
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="purchaseStockModal{{ $company->id }}" tabindex="-1" aria-labelledby="purchaseStockModalLabel{{ $company->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="purchaseStockModalLabel{{ $company->id }}">Purchase Stock</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('stocks.purchase', $company->id) }}" method="POST" id="purchaseForm{{ $company->id }}">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="product{{ $company->id }}" class="form-label">Product</label>
                                                                <select class="form-control @error('product_id') is-invalid @enderror" id="product{{ $company->id }}" name="product_id" required onchange="updateProductDetails(this, {{ $company->id }}); updateTotalAmount{{ $company->id }}();">
                                                                    <option value="">Select a product</option>
                                                                    @foreach ($company->products as $product)
                                                                        <option value="{{ $product->id }}" data-purchase-price="{{ $product->purchase_price }}" data-sale-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('product_id')
                                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="purchase_price{{ $company->id }}" class="form-label">Purchase Price</label>
                                                                    <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price{{ $company->id }}" name="purchase_price" required value="{{ old('purchase_price') }}" oninput="updateTotalAmount{{ $company->id }}()">
                                                                    @error('purchase_price')
                                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="sale_price{{ $company->id }}" class="form-label">Sale Price</label>
                                                                    <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price{{ $company->id }}" name="sale_price" required value="{{ old('sale_price') }}">
                                                                    @error('sale_price')
                                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="quantity{{ $company->id }}" class="form-label">Quantity</label>
                                                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity{{ $company->id }}" name="quantity" required value="0" min="1" required oninput="updateTotalAmount{{ $company->id }}()">
                                                                    @error('quantity')
                                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="paid_amount{{ $company->id }}" class="form-label">Paid Amount</label>
                                                                    <input type="number" required class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount{{ $company->id }}" name="paid_amount" value="0" min="0" placeholder="Optional">
                                                                    @error('paid_amount')
                                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-12 mb-3">
                                                                    <label for="total_amount{{ $company->id }}" class="form-label">Total Amount</label>
                                                                    <input type="number" step="0.01" class="form-control" id="total_amount{{ $company->id }}" name="total_amount" readonly placeholder="Calculated automatically">
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-warning">Submit</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            function updateTotalAmount{{ $company->id }}() {
                                                var purchasePriceEl = document.getElementById('purchase_price{{ $company->id }}');
                                                var quantityEl = document.getElementById('quantity{{ $company->id }}');
                                                var totalAmountEl = document.getElementById('total_amount{{ $company->id }}');
                                                var purchasePrice = parseFloat(purchasePriceEl.value) || 0;
                                                var quantity = parseInt(quantityEl.value) || 0;
                                                var total = purchasePrice * quantity;
                                                totalAmountEl.value = total.toFixed(2);
                                            }
                                        </script>
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
        $('#companiesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    function updateProductDetails(select, companyId) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('purchase_price' + companyId).value = selectedOption.dataset.purchasePrice;
            document.getElementById('sale_price' + companyId).value = selectedOption.dataset.salePrice;
        }
    }
</script>

@endsection
<script>
function updateProductDetails(select, companyId) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('purchase_price').value = selectedOption.dataset.purchasePrice;
        document.getElementById('sale_price').value = selectedOption.dataset.salePrice;
    }
}
</script>
