@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        #search-results {
            position: absolute;
            background: #ffffff;
            width: 700px;
            list-style-type: none;
            padding: 16px;
            margin: 8px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #search-results li {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        #search-results li:hover {
            background: #eeeeee;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-9">
                <div class="container-box ">
                    <div id="processing" class="my-3">
                        <!-- API results will be rendered here -->
                    </div>
                    <div id="response" class="my-3"></div>
                    <input type="text" id="search" placeholder="Search for products..." class="search-bar form-control">
                    <div style="width: 100%;">
                        <ul id="search-results"></ul>
                    </div>


                    <table class="table table-bordered table-hover table-striped mt-3" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">PRODUCT NAME</th>
                                <th style="width: 30%">COMPANY NAME</th>
                                <th style="width: 10%" class="text-center">QTY</th>
                                <th style="width: 10%" class="text-center">PRICE</th>
                                <th style="width: 15%" class="text-center">Discount</th>
                                <th style="width: 15%" class="text-center">Total</th>
                                <th style="width: 10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody style="overflow-y: auto; max-height: 400px;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="container-box">
                    <h5><strong>INVENTORY</strong></h5>

                    <button class="btn btn-primary w-100 mb-2">VIEW REPORTS</button>
                    <select class="form-select mb-3" aria-label="Choose Customer" id="customer">
                        <option selected>Choose Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <div class="border p-2 mb-3 sub-total">Subtotal:
                        <span id="sub-total">
                            @if (isset($subTotal))
                                {{ $subTotal }}
                            @else
                                Rs. 0.00
                            @endif
                        </span>
                    </div>
                    <div class="border p-2 mb-3">Discount:
                        <span id="discount">
                            @if (isset($discount))
                                {{ $discount }}
                            @else
                                Rs. 0.00
                            @endif
                        </span>
                    </div>
                    <div class="border p-2 total-box">Net Total:
                        <span id="net-total">
                            @if (isset($netTotal))
                                <b>{{ $netTotal }}</b>
                            @else
                                <b>Rs. 0.00</b>
                            @endif
                        </span>
                    </div>
                    <div class="border p-2 mb-3">Amount Paid:
                        <input type="number" class="form-control" id="paid-amount" placeholder="Enter amount paid">
                    </div>
                    <button class="btn btn-secondary w-100 mb-2" id="reset-cart">RESET</button>
                    <button class="btn btn-success w-100" id="checkout-btn">PAY NOW</button>
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
            // Load cart from localStorage on page load
            loadCartFromSession();

            // Live search event
            $('#search').on('keyup', function() {
                let query = $(this).val();

                if (query.length > 1) {
                    $('#search-results').show();
                    $.ajax({
                        url: "/search",
                        type: "GET",
                        data: {
                            'query': query
                        },
                        success: function(data) {
                            let results = "";
                            data.forEach(product => {
                                results += `
                        <li class="list-group-item d-flex justify-content-between align-items-center" 
                            data-id="${product.id}" 
                            data-name="${product.name}" 
                            data-sale_price="${product.sale_price}"
                            data-company_name="${product.company_name}">
                            
                            <span class="product-name">${product.name}</span>
                            <span class="badge bg-primary rounded-pill">${product.quantity} in stock</span>
                            <span class="product-price">Rs. ${product.sale_price}</span>
                        </li>`;
                            });
                            $('#search-results').html(results);
                        }
                    });
                } else {
                    $('#search-results').hide();
                }
            });

            // Reset cart
            $('#reset-cart').on('click', function() {
                localStorage.removeItem('cart');
                loadCartFromSession();
            });

            // Click event to add product to cart
            $(document).on('click', '#search-results li', function() {
                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let productPrice = $(this).data('sale_price');
                let companyName = $(this).data('company_name'); // Get the company name

                addToCart(productId, productName, productPrice, companyName);
                $('#search').val(''); // Clear search input
                $('#search-results').html(''); // Clear search results
                $('#search-results').hide();
            });

            // Function to add product to the cart and store in session
            function addToCart(id, name, price, companyName) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let existingProduct = cart.find(item => item.id === id);

                if (existingProduct) {
                    existingProduct.qty += 1;
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        companyName,
                        qty: 1,
                        discount: 0
                    });
                }

                localStorage.setItem('cart', JSON.stringify(cart)); // Save cart to session
                renderCart(); // Update UI
            }

            // Function to load cart from session and display it
            function loadCartFromSession() {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                renderCart(cart);
            }

            // Function to render cart table from session data
            function renderCart() {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let cartTable = $('table tbody');
                cartTable.html('');

                cart.forEach(item => {
                    let row = `
            <tr data-id="${item.id}">
                <td>${item.name}</td>
                <td>${item.companyName}</td>
                <td><input type="number" class="qty form-control" value="${item.qty}" min="1" style="width: 60px;"></td>
                <td class="price">${item.price}</td>
                <td><input type="number" class="discount form-control" value="${item.discount}" min="0" style="width: 100%;"></td>
                <td class="total">${(item.qty * item.price - item.discount).toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm remove">X</button></td>
            </tr>`;
                    cartTable.append(row);
                });

                updateDiscount();
                updateGrandTotal();
            }

            // Function to update row totals when quantity or discount changes
            $(document).on('input', '.qty, .discount', function() {
                let row = $(this).closest('tr');
                let productId = row.data('id');
                let qty = parseInt(row.find('.qty').val());
                let discount = parseFloat(row.find('.discount').val());

                updateSession(productId, qty, discount);
                setTimeout(() => {
                    renderCart();
                }, 900);
            });

            // Function to update session storage when quantity/discount changes
            function updateSession(productId, qty, discount) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                let product = cart.find(item => item.id === productId);

                if (product) {
                    product.qty = qty;
                    product.discount = discount;
                }

                localStorage.setItem('cart', JSON.stringify(cart));
            }

            // Function to remove an item from cart
            $(document).on('click', '.remove', function() {
                let productId = $(this).closest('tr').data('id');
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart = cart.filter(item => item.id !== productId);

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            });

            // Function to calculate grand total
            function updateGrandTotal() {
                let subTotal = 0;
                let grandTotal = 0;
                $('table tbody tr').each(function() {
                    subTotal += parseFloat($(this).find('.price').text()) * parseInt($(this).find('.qty')
                        .val());
                    grandTotal += parseFloat($(this).find('.total').text());
                });

                $('.sub-total').text(`Sub Total: ${subTotal.toFixed(2)}`);
                $('.total-box').text(`Net Total: ${grandTotal.toFixed(2)}`);
            }

            // Function to calculate discount
            function updateDiscount() {
                let discount = 0;
                $('table tbody tr').each(function() {
                    discount += parseFloat($(this).find('.discount').val());
                });

                $('#discount').text(discount.toFixed(2));
            }
            // ////////////////////////////////////////////////////////////////////////////////////////
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json' // ✅ Ensure JSON response
                }
            });

            $('#checkout-btn').on('click', function() {
                let customerId = $('#customer').val();
                if (customerId === "Choose Customer") {
                    alert("Please select a customer first");
                    return;
                }

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                if (cart.length === 0) {
                    alert("Cart is empty");
                    return;
                }

                let paidAmount = parseFloat($('#paid-amount').val());
                if (isNaN(paidAmount) || paidAmount <= 0) {
                    alert("Please enter a valid paid amount");
                    return;
                }

                let subTotal = 0;
                let totalDiscount = 0;
                cart.forEach(item => {
                    subTotal += item.qty * item.price;
                    totalDiscount += parseFloat(item.discount || 0);
                });
                let netTotal = subTotal - totalDiscount;
                $('#processing').html('<div class="alert alert-info">Processing payment...</div>');

                $.ajax({
                    url: "/sales",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                        'content'), // ✅ Add CSRF token here
                        customer_id: customerId,
                        cart: cart,
                        sub_total: subTotal,
                        discount: totalDiscount,
                        net_total: netTotal,
                        paid_amount: paidAmount
                    },
                    success: function(response) {
                        // $('#response').html(response.data);
                        alert(response.data);
                        localStorage.removeItem('cart');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            });

        });
    </script>
@endsection
