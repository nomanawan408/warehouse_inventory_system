@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
@section('content')
    <div class="container-fluid" style="padding: 20px 28px;">
        <div class="pos-wrapper">
            <!-- ═══ LEFT: Products & Cart ═══ -->
            <div class="pos-main">
                <!-- Processing / Response feedback -->
                <div id="processing"></div>
                <div id="response"></div>

                <!-- Search Card -->
                <div class="pos-card" style="margin-bottom: 20px;">
                    <div class="pos-card-body" style="padding: 16px 20px;">
                        <div class="search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="search" placeholder="Search products by name or company..." autofocus>
                            <ul id="search-results"></ul>
                        </div>
                    </div>
                </div>

                <!-- Cart Table -->
                <div class="cart-wrapper">
                    <table class="table" style="width: 100%; margin: 0;">
                        <thead>
                            <tr>
                                <th style="width: 28%;">Product</th>
                                <th style="width: 14%;">Company</th>
                                <th style="width: 10%;" class="text-center">Qty</th>
                                <th style="width: 13%;" class="text-end">Price</th>
                                <th style="width: 15%;" class="text-center">Discount/unit</th>
                                <th style="width: 13%;" class="text-end">Total</th>
                                <th style="width: 7%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody style="max-height: 420px; overflow-y: auto;">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ═══ RIGHT: Sidebar ═══ -->
            <div class="pos-sidebar">
                <div class="pos-card">
                    <div class="pos-card-header">
                        <div class="icon"><i class="ti ti-shopping-cart"></i></div>
                        <h6>Order Summary</h6>
                    </div>
                    <div class="pos-card-body">
                        <!-- Customer -->
                        <div class="sidebar-section">
                            <span class="sidebar-label">Customer</span>
                            <div class="customer-select-container">
                                <input type="text" class="customer-search-input" id="customer-search" placeholder="Search by name or phone...">
                                <input type="hidden" id="selected-customer-id">
                                <div class="customer-dropdown" id="customer-dropdown"></div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="sidebar-section">
                            <div class="summary-panel">
                                <div class="summary-row">
                                    <span class="label">Subtotal</span>
                                    <span id="sub-total" class="value">Rs. 0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="label">Discount %</span>
                                    <div style="width: 110px;">
                                        <div class="summary-input-group">
                                            <input type="number" value="0.00" class="summary-input" id="discount-percent" min="0" max="100" step="0.01">
                                            <span class="suffix">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="summary-row">
                                    <span class="label">Discount</span>
                                    <span id="discount" class="value">Rs. 0.00</span>
                                </div>
                                <div class="summary-row total">
                                    <span class="label">Net Total</span>
                                    <span id="net-total" class="value">Rs. 0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span class="label">Amount Paid</span>
                                    <div style="width: 140px;">
                                        <div class="summary-input-group">
                                            <input type="number" value="0.00" class="summary-input" id="paid-amount" min="0" step="0.01">
                                            <span class="suffix">Rs.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="sidebar-section" style="margin-top: 20px;">
                            <button class="btn-checkout" id="checkout-btn">
                                <i class="fas fa-check-circle"></i>
                                Complete Sale
                            </button>
                            <button class="btn-reset" id="reset-cart" style="margin-top: 10px;">
                                <i class="fas fa-trash-can"></i>
                                Reset Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').focus();
            if ($('#search').val() == '') {
                $('#search-results').hide();
            }
            
            // Show custom toast message
            function showToast(message, type = 'info') {
                $('.custom-toast').remove();
                
                let icon = '';
                if (type === 'success') icon = '<i class="fas fa-check-circle" style="color: #10b981;"></i>';
                else if (type === 'error') icon = '<i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>';
                else icon = '<i class="fas fa-info-circle" style="color: #3b82f6;"></i>';
                
                const toast = $(`
                    <div class="custom-toast toast-${type}">
                        ${icon}
                        <span>${message}</span>
                        <button type="button" class="btn-close ms-3" aria-label="Close"></button>
                    </div>
                `);
                
                $('body').append(toast);
                
                toast.find('.btn-close').on('click', function() {
                    toast.css('animation', 'slideOutLeft 0.3s forwards');
                    setTimeout(() => toast.remove(), 300);
                });
                
                setTimeout(() => {
                    toast.css('animation', 'slideOutLeft 0.3s forwards');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
            
            loadCartFromSession();

            let allProducts = [];
            
            function fetchAllProducts() {
                $.ajax({
                    url: "/products/all",
                    type: "GET",
                    cache: false,
                    success: function(data) {
                        allProducts = data;
                    },
                    error: function(error) {
                        console.error("Error loading products:", error);
                        showToast("Failed to load products. Please refresh the page.", "error");
                    }
                });
            }
            
            fetchAllProducts();
            
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.product-search-container, .search-wrapper').length) {
                    $('#search-results').hide();
                }
            });
            
            $('#search').on('focus', function() {
                if (allProducts.length > 0) {
                    if ($(this).val().length === 0) {
                        displayProducts(allProducts);
                    } else {
                        const query = $(this).val().toLowerCase();
                        filterAndDisplayProducts(query);
                    }
                    $('#search-results').show();
                } else {
                    $('#search-results').html('<li class="p-3 text-center text-muted small">Loading products...</li>');
                    $('#search-results').show();
                    fetchAllProducts();
                }
            });
            
            $('#search').on('input', function() {
                const query = $(this).val().toLowerCase();
                
                if (query.length === 0 && allProducts.length > 0) {
                    displayProducts(allProducts);
                } else if (query.length > 0) {
                    filterAndDisplayProducts(query);
                }
                
                $('#search-results').show();
            });
            
            function filterAndDisplayProducts(query) {
                if (allProducts.length === 0) {
                    searchProductsAPI(query);
                    return;
                }
                
                const filteredProducts = allProducts.filter(product => 
                    product.name.toLowerCase().includes(query) || 
                    (product.company_name && product.company_name.toLowerCase().includes(query))
                );
                
                displayProducts(filteredProducts);
            }
            
            function displayProducts(products) {
                let results = "";
                
                if (products.length === 0) {
                    results = `<li class="p-3 text-center text-muted">No products found</li>`;
                } else {
                    products.sort((a, b) => a.name.localeCompare(b.name));
                    const displayProducts = products.slice(0, 20);
                    
                    displayProducts.forEach(product => {
                        const inStock = product.quantity > 0;
                        const stockClass = inStock ? 'in-stock' : 'out-stock';
                        const stockText = inStock ? `${product.quantity} in stock` : 'Out of stock';
                        const disabledClass = inStock ? '' : 'opacity-50 pe-none';
                        const companyName = product.company_name || 'No company';
                        
                        results += `
                        <li class="${disabledClass}" 
                            data-id="${product.id}" 
                            data-name="${product.name}" 
                            data-sale_price="${product.sale_price}"
                            data-quantity="${product.quantity}"
                            data-company_name="${companyName}">
                            <div style="flex: 1; min-width: 0;">
                                <div class="product-name">${product.name}</div>
                                <small style="color: var(--text-muted); font-size: 0.78rem;">${companyName}</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                                <span class="stock-badge ${stockClass}">${stockText}</span>
                                <span class="product-price">Rs. ${parseFloat(product.sale_price).toFixed(2)}</span>
                            </div>
                        </li>`;
                    });
                    
                    if (products.length > 20) {
                        results += `<li class="p-2 text-center text-muted small" style="border: none; cursor: default;">Showing 20 of ${products.length} products. Type to refine.</li>`;
                    }
                }
                
                $('#search-results').html(results);
            }
            
            function searchProductsAPI(query) {
                $.ajax({
                    url: "/search",
                    type: "GET",
                    data: { query: query },
                    success: function(data) {
                        displayProducts(data);
                    },
                    error: function(error) {
                        console.error("Error searching products:", error);
                        showToast("Error searching products. Please try again.", "error");
                    }
                });
            }

            // Reset cart
            $('#reset-cart').on('click', function() {
                localStorage.removeItem('cart');
                $('#discount-percent').val('0.00');
                $('#selected-customer-id').val('');
                $('#customer-search').val('');
                loadCartFromSession();
            });

            // Add product to cart
            $(document).on('click', '#search-results li', function() {
                if ($(this).hasClass('pe-none')) return;
                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let productPrice = $(this).data('sale_price');
                let productQuantity = parseInt($(this).data('quantity') || 0);
                let companyName = $(this).data('company_name');
                
                if (productQuantity <= 0) {
                    showToast(`Cannot add ${productName} - Out of stock`, "error");
                    return;
                }

                addToCart(productId, productName, productPrice, companyName, productQuantity);
                $('#search').val('');
                $('#search-results').html('');
                $('#search-results').hide();
            });

            function addToCart(id, name, price, companyName, quantity) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let existingProduct = cart.find(item => item.id === id);

                if (existingProduct) {
                    if (existingProduct.qty + 1 > quantity) {
                        showToast(`Cannot add more ${name} - Only ${quantity} in stock`, "error");
                        return;
                    }
                    existingProduct.qty += 1;
                    showToast(`Added another ${name} to cart`, "success");
                } else {
                    cart.push({
                        id, name, price, companyName,
                        qty: 1, discount: 0, stockQuantity: quantity
                    });
                    showToast(`Added ${name} to cart`, "success");
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();

                let customerId = $('#selected-customer-id').val();
                if (customerId && customerId !== '' && !isNaN(customerId)) {
                    fetchItemDiscount(customerId, id);
                }
            }

            function fetchItemDiscount(customerId, productId) {
                $.ajax({
                    url: "/sales/last-discount",
                    type: "GET",
                    data: { customer_id: customerId, product_ids: [productId] },
                    success: function(response) {
                        let discount = parseFloat(response.discounts[productId]) || 0;
                        let cart = JSON.parse(localStorage.getItem('cart')) || [];
                        let item = cart.find(i => parseInt(i.id) === parseInt(productId));
                        if (item) {
                            item.discount = discount;
                            localStorage.setItem('cart', JSON.stringify(cart));
                            renderCart();
                            if (discount > 0) {
                                showToast(`Applied previous discount: Rs. ${discount.toFixed(2)}/unit for ${item.name}`, "info");
                            }
                        }
                    }
                });
            }

            function refreshCartDiscounts(customerId) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                if (cart.length === 0) return;

                cart.forEach(item => { item.discount = 0; });
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();

                let productIds = cart.map(item => item.id);

                $.ajax({
                    url: "/sales/last-discount",
                    type: "GET",
                    data: { customer_id: customerId, product_ids: productIds },
                    success: function(response) {
                        let discounts = response.discounts || {};
                        let cart = JSON.parse(localStorage.getItem('cart')) || [];
                        let changed = false;
                        cart.forEach(item => {
                            let d = parseFloat(discounts[item.id]) || 0;
                            if (d !== item.discount) { item.discount = d; changed = true; }
                        });
                        if (changed) {
                            localStorage.setItem('cart', JSON.stringify(cart));
                            renderCart();
                            showToast("Discounts updated from customer history", "info");
                        }
                    }
                });
            }

            function loadCartFromSession() {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                renderCart(cart);
            }

            function renderCart() {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let cartTable = $('table tbody');
                cartTable.html('');

                if (cart.length === 0) {
                    cartTable.html(`
                        <tr>
                            <td colspan="7" class="empty-cart">
                                <i class="fas fa-cart-shopping"></i>
                                <p style="font-weight: 600; color: var(--text-secondary);">Your cart is empty</p>
                                <p class="small">Search for products to start adding items</p>
                            </td>
                        </tr>
                    `);
                } else {
                    cart.forEach((item, index) => {
                        let itemQty = parseInt(item.qty) || 1;
                        let itemPrice = parseFloat(item.price) || 0;
                        let itemDiscount = parseFloat(item.discount || 0);
                        let stockLimit = parseInt(item.stockQuantity) || 0;
                        
                        let itemSubtotal = itemQty * itemPrice;
                        let itemTotalDiscount = itemDiscount * itemQty;
                        let itemTotal = itemSubtotal - itemTotalDiscount;
                        
                        if (itemTotal < 0) itemTotal = 0;
                        
                        let row = `
                            <tr data-id="${item.id}" class="fade-in" style="animation-delay: ${index * 0.03}s">
                                <td style="font-weight: 600;">${item.name}</td>
                                <td style="color: var(--text-secondary); font-size: 0.85rem;">${item.companyName || 'N/A'}</td>
                                <td class="text-center">
                                    <input type="number" 
                                        class="qty form-control form-control-sm mx-auto" 
                                        value="${itemQty}" 
                                        min="1" 
                                        max="${stockLimit}"
                                        data-stock="${stockLimit}" 
                                        style="max-width: 64px; text-align: center;">
                                </td>
                                <td class="text-end" style="font-weight: 600;">Rs. ${itemPrice.toFixed(2)}</td>
                                <td>
                                    <div class="input-group input-group-sm mx-auto" style="max-width: 120px;">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" class="discount form-control form-control-sm" 
                                            value="${itemDiscount}" min="0" step="0.01">
                                    </div>
                                </td>
                                <td class="text-end" style="font-weight: 700; color: var(--brand-start);">Rs. ${itemTotal.toFixed(2)}</td>
                                <td class="text-center">
                                    <button class="remove-btn" title="Remove">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                        cartTable.append(row);
                    });
                }

                updateTotals();
            }

            function updateTotals() {
                let subTotal = 0;
                let totalDiscount = 0;

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                cart.forEach(item => {
                    let itemSubtotal = item.qty * item.price;
                    let itemDiscount = (item.discount || 0) * item.qty;
                    subTotal += itemSubtotal;
                    totalDiscount += itemDiscount;
                });

                const discountPercent = parseFloat($('#discount-percent').val()) || 0;
                if (discountPercent > 0) {
                    totalDiscount = subTotal * (discountPercent / 100);
                }

                let netTotal = subTotal - totalDiscount;

                animateValue('#sub-total', subTotal);
                animateValue('#discount', totalDiscount);
                animateValue('#net-total', netTotal);
            }
            
            function animateValue(selector, newValue) {
                const el = $(selector);
                const oldValue = parseFloat(el.text().replace(/[^\d.-]/g, '')) || 0;
                
                if (Math.abs(newValue - oldValue) < 0.01) {
                    el.text(`Rs. ${newValue.toFixed(2)}`);
                    return;
                }
                
                el.text(`Rs. ${newValue.toFixed(2)}`);
            }

            function updateSession(productId, qty, discount) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let product = cart.find(item => item.id === productId);

                if (product) {
                    product.qty = parseInt(qty) || 1;
                    product.discount = parseFloat(discount) || 0;
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                updateTotals();
            }

            $(document).on('change', '.qty', function() {
                let row = $(this).closest('tr');
                let productId = row.data('id');
                let qty = parseInt($(this).val()) || 1;
                let stockLimit = parseInt($(this).attr('data-stock')) || 0;
                
                if (qty < 1) { qty = 1; $(this).val(1); showToast("Quantity cannot be less than 1", "error"); }
                if (qty > stockLimit) { qty = stockLimit; $(this).val(stockLimit); showToast(`Max available: ${stockLimit}`, "error"); }
                
                let priceText = row.find('td:eq(3)').text().replace('Rs.', '').trim();
                let price = parseFloat(priceText) || 0;
                let discount = parseFloat(row.find('.discount').val()) || 0;
                
                let itemTotal = (qty * price) - (discount * qty);
                row.find('td:eq(5)').text(`Rs. ${Math.max(0, itemTotal).toFixed(2)}`);
                updateSession(productId, qty, discount);
            });

            $(document).on('change', '.discount', function() {
                let row = $(this).closest('tr');
                let productId = row.data('id');
                let qty = parseInt(row.find('.qty').val()) || 1;
                
                let priceText = row.find('td:eq(3)').text().replace('Rs.', '').trim();
                let price = parseFloat(priceText) || 0;
                let discount = parseFloat($(this).val()) || 0;
                
                let itemTotal = (qty * price) - (discount * qty);
                row.find('td:eq(5)').text(`Rs. ${Math.max(0, itemTotal).toFixed(2)}`);
                updateSession(productId, qty, discount);
            });

            $(document).on('change', '#discount-percent', function() {
                updateTotals();
            });

            $(document).on('click', '.remove-btn', function() {
                let productId = $(this).closest('tr').data('id');
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let productName = $(this).closest('tr').find('td:first-child').text();
                
                cart = cart.filter(item => item.id !== productId);
                localStorage.setItem('cart', JSON.stringify(cart));
                showToast(`Removed ${productName}`, "info");
                renderCart();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            // Checkout
            $('#checkout-btn').on('click', function() {
                let customerId = $('#selected-customer-id').val();
                if (customerId === "") {
                    showToast("Please select a customer first", "error");
                    $('#customer-search').focus();
                    return;
                }

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                if (cart.length === 0) {
                    showToast("Cart is empty. Add products before checkout.", "error");
                    $('#search').focus();
                    return;
                }

                let paidAmount = parseFloat($('#paid-amount').val()) || 0;
                let subTotal = 0, totalDiscount = 0;

                cart.forEach(item => {
                    subTotal += item.qty * item.price;
                    totalDiscount += item.qty * (item.discount || 0);
                });

                const discountPercent = parseFloat($('#discount-percent').val()) || 0;
                if (discountPercent > 0) {
                    totalDiscount = subTotal * (discountPercent / 100);
                }

                let netTotal = subTotal - totalDiscount;

                $('#processing').html(`
                    <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span style="font-weight: 500; color: var(--text-secondary); font-size: 0.9rem;">Processing your sale...</span>
                    </div>
                `);
                
                $('#checkout-btn').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Processing...
                `);

                $.ajax({
                    url: "/sales",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customerId,
                        cart: cart,
                        sub_total: subTotal.toFixed(2),
                        discount: totalDiscount.toFixed(2),
                        discount_percent: discountPercent.toFixed(2),
                        net_total: netTotal.toFixed(2),
                        paid_amount: paidAmount.toFixed(2)
                    },
                    success: function(response) {
                        localStorage.removeItem('cart');
                        $('#processing').html('');
                        
                        // Show success overlay
                        const overlay = $(`
                            <div class="sale-success-overlay">
                                <div class="sale-success-card">
                                    <div class="check-icon"><i class="fas fa-check"></i></div>
                                    <h4>Sale Completed!</h4>
                                    <p>Invoice has been saved successfully.</p>
                                    <div class="sale-success-actions">
                                        <a href="${response.print_url}" target="_blank" class="btn btn-print">
                                            <i class="fas fa-print"></i> Print Invoice
                                        </a>
                                        <button class="btn btn-close-success" onclick="$('.sale-success-overlay').remove();">
                                            <i class="fas fa-xmark"></i> Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('body').append(overlay);
                        
                        showToast("Sale completed successfully!", "success");
                        
                        loadCartFromSession();
                        $('#customer-search').val("");
                        $('#selected-customer-id').val("");
                        $('#discount-percent').val('0.00');
                        $('#paid-amount').val("0.00");
                        
                        $('#checkout-btn').prop('disabled', false).html(`
                            <i class="fas fa-check-circle"></i> Complete Sale
                        `);
                        
                        refreshProductData();
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Error processing sale. Please try again.';
                        $('#processing').html(`<div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 10px; padding: 14px 18px; color: var(--danger); font-weight: 500; font-size: 0.9rem;">${errorMsg}</div>`);
                        
                        showToast(errorMsg, "error");
                        
                        $('#checkout-btn').prop('disabled', false).html(`
                            <i class="fas fa-check-circle"></i> Complete Sale
                        `);
                        
                        refreshProductData();
                    }
                });
            });

            function refreshProductData() {
                allProducts = [];
                fetchAllProducts();
            }

            // Customer search
            const customers = @json($customers);
            
            $('#customer-search').on('focus', function() {
                if ($(this).val().length > 0) {
                    filterCustomers($(this).val());
                } else {
                    populateAllCustomers();
                }
                $('#customer-dropdown').show();
            });
            
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.customer-select-container').length) {
                    $('#customer-dropdown').hide();
                }
            });
            
            $('#customer-search').on('input', function() {
                const query = $(this).val().toLowerCase().trim();
                
                if (query.length === 0) {
                    $('#selected-customer-id').val('');
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];
                    let changed = false;
                    cart.forEach(item => {
                        if (item.discount !== 0) { item.discount = 0; changed = true; }
                    });
                    if (changed) {
                        localStorage.setItem('cart', JSON.stringify(cart));
                        renderCart();
                    }
                    populateAllCustomers();
                } else {
                    filterCustomers(query);
                }
                
                $('#customer-dropdown').show();
            });
            
            function populateAllCustomers() {
                let html = '';
                if (customers.length === 0) {
                    html = '<div class="p-3 text-center text-muted small">No customers found</div>';
                } else {
                    customers.forEach(customer => { html += createCustomerItem(customer); });
                }
                $('#customer-dropdown').html(html);
            }
            
            function filterCustomers(query) {
                query = query.toLowerCase();
                let filteredCustomers = customers.filter(c => 
                    c.name.toLowerCase().includes(query) || 
                    (c.phone_no && c.phone_no.includes(query))
                );
                
                let html = '';
                if (filteredCustomers.length === 0) {
                    html = '<div class="p-3 text-center text-muted small">No matching customers</div>';
                } else {
                    filteredCustomers.forEach(customer => { html += createCustomerItem(customer); });
                }
                $('#customer-dropdown').html(html);
            }
            
            function createCustomerItem(customer) {
                return `
                    <div class="customer-item" data-id="${customer.id}">
                        <div class="customer-name">${customer.name}</div>
                        <div class="customer-phone">${customer.phone_no || 'No phone'}</div>
                    </div>
                `;
            }
            
            $(document).on('click', '.customer-item', function() {
                const customerId = $(this).data('id');
                const customerName = $(this).find('.customer-name').text();

                $('#selected-customer-id').val(customerId);
                $('#customer-search').val(customerName);
                $('#customer-dropdown').hide();

                showToast(`Customer: ${customerName}`, "success");
                fetchCustomerDiscount(customerId);
                refreshCartDiscounts(customerId);
            });

            function fetchCustomerDiscount(customerId) {
                $.ajax({
                    url: "/customers/discount",
                    type: "GET",
                    data: { customer_id: customerId },
                    success: function(response) {
                        const discountPercent = parseFloat(response.discount) || 0;
                        $('#discount-percent').val(discountPercent.toFixed(2));
                        updateTotals();
                    },
                    error: function(xhr) {
                        $('#discount-percent').val('0.00');
                        updateTotals();
                    }
                });
            }
        });
    </script>
@endsection
