@extends('layouts.app')

@section('content')
<style>
    /* Adjustments for product rows */
    .table .form-control, .table .input-group-text {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
        height: auto;
    }
    
    .table .input-group {
        flex-wrap: nowrap;
    }
    
    .input-group-append .input-group-text {
        font-size: 0.7rem;
        padding: 0.25rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 offset-md-0">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Edit Invoice #{{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</h5>
                    <div>
                        <a href="{{ route('sales.print', $sale->id) }}" class="btn btn-light btn-sm" target="_blank">
                            <i class="ti ti-printer"></i> Print
                        </a>
                        <a href="{{ route('sales.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="edit-invoice-form">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <input type="hidden" id="sale-id" value="{{ $sale->id }}">
                        <input type="hidden" id="customer" value="{{ $sale->customer_id }}">
                        <input type="hidden" id="invoice-calculations" value="">

                        <!-- Customer Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Customer Information</h6>
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" value="{{ $sale->customer->name }}" readonly>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label>Contact</label>
                                        <input type="text" class="form-control" value="{{ $sale->customer->phone }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Invoice Details</h6>
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <label>Invoice Date</label>
                                        <input type="text" class="form-control" value="{{ $sale->created_at->format('d M, Y') }}" readonly>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label>Last Updated</label>
                                        <input type="text" class="form-control" value="{{ $sale->updated_at->format('d M, Y h:i A') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="350">Product</th>
                                        <th width="80">Quantity</th>
                                        <th width="150">Price</th>
                                        <th width="180">Discount</th>
                                        <th width="150">Total Discount</th>
                                        <th width="150">Total</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @foreach($sale->items as $item)
                                        <tr class="item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}" data-max-stock="{{ $item->product->quantity }}">
                                            <td>
                                                <input type="text" class="form-control product-name small" value="{{ $item->product->name }}" readonly>
                                                <input type="hidden" class="product-id" value="{{ $item->product_id }}">
                                                <input type="hidden" class="cost-price" value="{{ $item->product->purchase_price }}">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control qty small" value="{{ $item->quantity }}" min="1" />
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="text" class="form-control price small" value="{{ $item->price }}" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="number" class="form-control discount small" value="{{ number_format($item->discount, 2, '.', '') }}" min="0" step="0.01" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text small text-muted">/unit</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="text" class="form-control total-discount small" value="{{ number_format($item->discount * $item->quantity, 2) }}" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="text" class="form-control total small" value="{{ number_format(($item->quantity * $item->price) - $item->discount, 2) }}" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Product Button -->
                        <div class="mb-4">
                            <button type="button" class="btn btn-success" id="add-product-btn">
                                <i class="ti ti-plus"></i> Add Product
                            </button>
                        </div>

                        <!-- Totals Section -->
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="border rounded p-3">
                                    <div class="row mb-2">
                                        <label class="col-6">Sub Total:</label>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="text" id="sub-total" class="form-control" value="{{ number_format($sale->total_amount, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-6">Total Discount:</label>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="text" id="total-discount" class="form-control" value="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-6">Net Total:</label>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="text" id="net-total" class="form-control fw-bold" value="{{ number_format($sale->net_total, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-6">Amount Paid:</label>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="text" id="paid-amount" class="form-control text-success" value="{{ number_format($sale->amount_paid, 2) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-6">Remaining Amount:</label>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="text" id="remaining-amount" class="form-control text-danger" value="{{ number_format($sale->pending_amount, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-6">Payment Status:</label>
                                        <div class="col-6">
                                            <span id="payment-status" class="fw-bold"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-6">Total Items:</label>
                                        <div class="col-6">
                                            <span id="total-items" class="fw-bold"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-6">Total Quantity:</label>
                                        <div class="col-6">
                                            <span id="total-quantity" class="fw-bold"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-6">Estimated Profit:</label>
                                        <div class="col-6">
                                            <input type="text" id="estimated-profit" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" id="submit-edit-invoice" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i> Update Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label>Search Product</label>
                    <input type="text" class="form-control" id="product-search" placeholder="Type to search...">
                </div>
                <div class="product-list mt-3">
                    <!-- Products will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Edit invoice page loaded');
    
    // Initialize variables
    let products = [];
    let selectedProduct = null;
    let timer;

    // Initialize Bootstrap components
    var addProductModal = new bootstrap.Modal(document.getElementById('addProductModal'));
    
    // Initialize toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };

    // Format currency function
    function formatCurrency(number) {
        return new Intl.NumberFormat('en-PK', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }

    // Parse currency string to number
    function parseCurrency(currencyString) {
        if (typeof currencyString === 'string') {
            return parseFloat(currencyString.replace(/[^0-9.-]+/g, '')) || 0;
        }
        return parseFloat(currencyString) || 0;
    }

    // Calculate item total
    function calculateItemTotal(row) {
        console.log('Calculating item total for row:', row);
        const qty = parseInt($(row).find('.qty').val()) || 0;
        const price = parseFloat($(row).find('.price').val()) || 0;
        const discountPerUnit = parseFloat($(row).find('.discount').val()) || 0;
        const totalDiscount = discountPerUnit * qty;
        const total = (qty * price) - totalDiscount;
        
        // Update total discount display
        $(row).find('.total-discount').val(formatCurrency(totalDiscount));
        $(row).data('total-discount', totalDiscount);
        
        $(row).find('.total').val(formatCurrency(total));
        return total;
    }

    // Calculate all totals
    function calculateTotals() {
        console.log('Calculating all totals');
        let subTotal = 0;
        let totalItems = 0;
        let totalQuantity = 0;
        let totalItemsDiscount = 0;

        // Calculate each item's total
        $('.item').each(function() {
            const itemTotal = calculateItemTotal(this);
            subTotal += itemTotal;
            totalItems++;
            totalQuantity += parseInt($(this).find('.qty').val()) || 0;
            
            // Add this item's total discount to the running sum
            totalItemsDiscount += parseFloat($(this).data('total-discount') || 0);
        });

        // Update subtotal
        $('#sub-total').val(formatCurrency(subTotal));
        
        // Update total discount from all items
        $('#total-discount').val(formatCurrency(totalItemsDiscount));
        
        // Calculate net total (already accounts for per-item discounts)
        const netTotal = subTotal;
        $('#net-total').val(formatCurrency(netTotal));
        
        // Calculate paid and remaining amounts
        const paidAmount = parseFloat($('#paid-amount').val().replace(/[^\d.-]/g, '')) || 0;
        const remainingAmount = netTotal - paidAmount;
        $('#remaining-amount').val(formatCurrency(remainingAmount));

        // Update summary information
        $('#total-items').text(totalItems);
        $('#total-quantity').text(totalQuantity);
        
        // Update UI indicators
        if (remainingAmount > 0) {
            $('#remaining-amount').removeClass('text-success').addClass('text-danger');
            $('#payment-status').text('Partial').removeClass('text-success').addClass('text-warning');
        } else if (remainingAmount === 0) {
            $('#remaining-amount').removeClass('text-danger').addClass('text-success');
            $('#payment-status').text('Paid').removeClass('text-warning').addClass('text-success');
        } else {
            $('#remaining-amount').removeClass('text-danger').addClass('text-success');
            $('#payment-status').text('Overpaid').removeClass('text-warning').addClass('text-danger');
        }

        // Update profit calculation
        let totalProfit = 0;
        $('.item').each(function() {
            const qty = parseInt($(this).find('.qty').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const cost = parseFloat($(this).find('.cost-price').val()) || 0;
            const discountPerUnit = parseFloat($(this).find('.discount').val()) || 0;
            
            // Calculate profit per item accounting for discount
            // Profit = (Price - Cost - Discount) × Quantity
            const itemProfit = (price - cost - discountPerUnit) * qty;
            totalProfit += itemProfit;
        });
        
        // Update profit display if element exists
        if ($('#estimated-profit').length) {
            $('#estimated-profit').val(formatCurrency(totalProfit));
        }

        // Store calculations for form submission
        $('#invoice-calculations').val(JSON.stringify({
            subTotal: subTotal,
            discount: totalItemsDiscount,
            netTotal: netTotal,
            paidAmount: paidAmount,
            remainingAmount: remainingAmount,
            totalItems: totalItems,
            totalQuantity: totalQuantity,
            totalProfit: totalProfit
        }));
    }

    // Debounce function to prevent too many calculations
    function debounce(func, wait) {
        clearTimeout(timer);
        timer = setTimeout(func, wait);
    }

    // Event listeners for real-time calculations
    console.log('Setting up event listeners');
    
    // Direct event binding for existing elements
    $('.qty, .price, .discount').on('input', function() {
        console.log('Quantity, price or discount changed');
        debounce(calculateTotals, 300);
    });

    $('#discount, #paid-amount').on('input', function() {
        console.log('Discount or paid amount changed');
        debounce(calculateTotals, 300);
    });

    // Event delegation for dynamically added elements
    $(document).on('input', '.qty, .price, .discount', function() {
        console.log('Quantity, price or discount changed (delegated)');
        debounce(calculateTotals, 300);
    });

    $(document).on('input', '#discount, #paid-amount', function() {
        console.log('Discount or paid amount changed (delegated)');
        debounce(calculateTotals, 300);
    });

    // Stock validation on quantity change
    $(document).on('input', '.qty', function() {
        console.log('Quantity changed - validating stock');
        const row = $(this).closest('.item');
        const maxStock = parseInt(row.data('max-stock')) || 0;
        const qty = parseInt($(this).val()) || 0;
        
        if (qty > maxStock) {
            toastr.warning(`Only ${maxStock} items available in stock`);
            $(this).val(maxStock);
        }
        if (qty < 1) {
            $(this).val(1);
        }
        calculateTotals();
    });

    // Format inputs on blur
    $(document).on('blur', '.price, .discount, #discount, #paid-amount', function() {
        console.log('Formatting currency on blur');
        const value = parseCurrency($(this).val());
        $(this).val(formatCurrency(value));
    });

    // Initialize calculations on page load
    calculateTotals();

    // Remove item
    $(document).on('click', '.remove-item', function() {
        console.log('Removing item');
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Add product button click
    $('#add-product-btn').on('click', function() {
        console.log('Add product button clicked');
        addProductModal.show();
    });

    // Direct access to modal for debugging
    window.showAddProductModal = function() {
        addProductModal.show();
    };

    // Product search
    let searchTimeout;
    $('#product-search').on('input', function() {
        console.log('Searching for products');
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length < 2) {
            $('.product-list').html('');
            return;
        }

        searchTimeout = setTimeout(function() {
            $.get('/products/search', { query: query }, function(response) {
                console.log('Products found:', response);
                let html = '';
                products = response;
                
                response.forEach(function(product) {
                    // Access the correct price field directly
                    const sellingPrice = parseFloat(product.sale_price) || 0;
                    const costPrice = parseFloat(product.purchase_price) || 0;
                    
                    html += `
                        <div class="product-item border-bottom p-2" data-id="${product.id}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${product.name}</strong>
                                    <div class="text-muted small">Stock: ${product.quantity}</div>
                                </div>
                                <div class="text-end">
                                    <div>Rs. ${formatCurrency(sellingPrice)}</div>
                                    <button class="btn btn-sm btn-primary select-product">Select</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('.product-list').html(html);
            }).fail(function(error) {
                console.error("Error searching products:", error);
                toastr.error("Error searching products. Please try again.");
            });
        }, 300);
    });

    // Select product
    $(document).on('click', '.select-product', function() {
        console.log('Product selected');
        const productId = $(this).closest('.product-item').data('id');
        const product = products.find(p => p.id === productId);
        
        if (product) {
            // Log the product data to see what we're working with
            console.log('Selected product:', product);
            
            // Ensure prices are valid numbers or use defaults
            const sellingPrice = parseFloat(product.sale_price) || 0;
            const costPrice = parseFloat(product.purchase_price) || 0;
            
            console.log('Using prices:', { sellingPrice, costPrice });
            
            const row = `
                <tr class="item" data-item-id="new" data-price="${sellingPrice}" data-max-stock="${product.quantity}">
                    <td>
                        <input type="text" class="form-control product-name small" value="${product.name}" readonly>
                        <input type="hidden" class="product-id" value="${product.id}">
                        <input type="hidden" class="cost-price" value="${costPrice}">
                    </td>
                    <td>
                        <input type="number" class="form-control qty small" value="1" min="1" max="${product.quantity}" />
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs.</span>
                            <input type="text" class="form-control price small" value="${sellingPrice}" readonly />
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" class="form-control discount small" value="0.00" min="0" step="0.01" />
                            <div class="input-group-append">
                                <span class="input-group-text small text-muted">/unit</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs.</span>
                            <input type="text" class="form-control total-discount small" value="0" readonly />
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs.</span>
                            <input type="text" class="form-control total small" value="${sellingPrice}" readonly />
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#cart-items').append(row);
            calculateTotals();
            addProductModal.hide();
            $('#product-search').val('');
            $('.product-list').html('');
        }
    });

    // Form submission
    $('#edit-invoice-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');

        // Collect form data
        const items = [];
        $('.item').each(function() {
            items.push({
                id: $(this).find('.product-id').val(),
                qty: parseInt($(this).find('.qty').val()) || 1,
                price: parseFloat($(this).find('.price').val()) || 0,
                discount: parseFloat($(this).find('.discount').val()) || 0
            });
        });

        const calculations = JSON.parse($('#invoice-calculations').val() || '{}');
        
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');
        formData.append('customer_id', $('#customer').val());
        formData.append('cart', JSON.stringify(items));
        formData.append('sub_total', calculations.subTotal || 0);
        formData.append('discount', calculations.discount || 0);
        formData.append('net_total', calculations.netTotal || 0);
        formData.append('paid_amount', calculations.paidAmount || 0);

        // Show loading state
        $('#submit-edit-invoice').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

        // Submit form
        $.ajax({
            url: `/sales/${$('#sale-id').val()}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Invoice updated successfully:', response);
                // Redirect to sales index with success message
                window.location.href = '/sales?success=Invoice updated successfully';
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                console.error('Error updating invoice:', xhr);
                toastr.error(error?.message || 'Failed to update invoice. Please check your data.');
                
                // Reset button state
                $('#submit-edit-invoice').prop('disabled', false).html('<i class="ti ti-device-floppy"></i> Update Invoice');
            }
        });
    });

    // Log initial state for debugging
    console.log('Initial cart items count:', $('.item').length);
    console.log('Total calculations on load:', {
        subTotal: parseCurrency($('#sub-total').val()),
        netTotal: parseCurrency($('#net-total').val()),
        paidAmount: parseCurrency($('#paid-amount').val())
    });
});
</script>
@endpush
