$(document).ready(function() {
    $('#search').focus();
    if ($('#search').val() == '') {
        $('#search-results').hide();
    }
    
    // Show custom toast message
    function showToast(message, type = 'info') {
        // Remove any existing toasts
        $('.custom-toast').remove();
        
        // Create icon based on type
        let icon = '';
        if (type === 'success') icon = '<i class="fas fa-check-circle text-success"></i>';
        else if (type === 'error') icon = '<i class="fas fa-exclamation-circle text-danger"></i>';
        else icon = '<i class="fas fa-info-circle text-info"></i>';
        
        // Create toast element
        const toast = $(`
            <div class="custom-toast toast-${type}">
                ${icon}
                <span>${message}</span>
                <button type="button" class="btn-close ms-3" aria-label="Close"></button>
            </div>
        `);
        
        // Add to body and remove after delay
        $('body').append(toast);
        
        // Add click handler to close button
        toast.find('.btn-close').on('click', function() {
            toast.css('animation', 'slideOutLeft 0.3s forwards');
            setTimeout(() => toast.remove(), 300);
        });
        
        // Auto-dismiss after delay
        setTimeout(() => {
            toast.css('animation', 'slideOutLeft 0.3s forwards');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Load cart from localStorage on page load
    loadCartFromSession();

    // Live search event with preloaded products
    let allProducts = []; // Will store all products
    
    // Fetch all products on page load
    function fetchAllProducts() {
        $.ajax({
            url: "/products/all",
            type: "GET",
            cache: false, // Disable caching to always get fresh data
            success: function(data) {
                allProducts = data;
                console.log('Preloaded products:', allProducts.length);
            },
            error: function(error) {
                console.error("Error loading products:", error);
                showToast("Failed to load products. Please refresh the page.", "error");
            }
        });
    }
    
    // Load all products when page loads
    fetchAllProducts();
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.product-search-container').length) {
            $('#search-results').hide();
        }
    });
    
    // Show all products when search gets focus
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
            // If products haven't loaded yet, show loading message
            $('#search-results').html('<li class="p-3 text-center text-muted small">Loading products...</li>');
            $('#search-results').show();
            // Attempt to load products
            fetchAllProducts();
        }
    });
    
    // Handle search input
    $('#search').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        if (query.length === 0 && allProducts.length > 0) {
            // Show all products when search is empty
            displayProducts(allProducts);
        } else if (query.length > 0) {
            // Filter products based on query
            filterAndDisplayProducts(query);
        }
        
        $('#search-results').show();
    });
    
    // Filter products based on search query
    function filterAndDisplayProducts(query) {
        if (allProducts.length === 0) {
            // Fall back to API if preloaded products aren't available
            searchProductsAPI(query);
            return;
        }
        
        // Filter locally for faster response
        const filteredProducts = allProducts.filter(product => 
            product.name.toLowerCase().includes(query) || 
            (product.company && product.company.name && 
             product.company.name.toLowerCase().includes(query))
        );
        
        displayProducts(filteredProducts);
    }
    
    // Display products in search results
    function displayProducts(products) {
        let results = "";
        
        if (products.length === 0) {
            results = `<li class="p-3 text-center text-muted">No products found</li>`;
        } else {
            // Sort products by name for easier scanning
            products.sort((a, b) => a.name.localeCompare(b.name));
            
            // Limit to first 20 products to avoid overwhelming the UI
            const displayProducts = products.slice(0, 20);
            
            displayProducts.forEach(product => {
                const inStock = product.quantity > 0;
                const badgeClass = inStock ? 'bg-success' : 'bg-danger';
                const stockText = inStock ? `${product.quantity} in stock` : 'Out of stock';
                const disabledClass = inStock ? '' : 'opacity-50 pe-none'; // pe-none disables pointer events
                const companyName = product.company_name || (product.company ? product.company.name : 'No company');
                
                results += `
                <li class="list-group-item d-flex justify-content-between align-items-center ${disabledClass}" 
                    data-id="${product.id}" 
                    data-name="${product.name}" 
                    data-sale_price="${product.sale_price}"
                    data-quantity="${product.quantity}"
                    data-company_name="${companyName}">
                    
                    <div>
                        <div class="product-name">${product.name}</div>
                        <small class="text-muted">${companyName}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge ${badgeClass} rounded-pill me-3">${stockText}</span>
                        <span class="product-price">Rs. ${parseFloat(product.sale_price).toFixed(2)}</span>
                    </div>
                </li>`;
            });
            
            if (products.length > 20) {
                results += `<li class="p-2 text-center text-muted small">Showing 20 of ${products.length} products. Type to refine search.</li>`;
            }
        }
        
        $('#search-results').html(results);
    }
    
    // Fall back to API search if needed
    function searchProductsAPI(query) {
        $.ajax({
            url: "/search",
            type: "GET",
            data: {
                'query': query
            },
            success: function(data) {
                displayProducts(data);
            },
            error: function(error) {
                console.error("Error searching products:", error);
                showToast("Error searching products. Please try again.", "error");
                $('#search-results').html('<li class="p-3 text-center text-danger">Error searching products</li>');
            }
        });
    }

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
        let productQuantity = parseInt($(this).data('quantity') || 0);
        let companyName = $(this).data('company_name');
        
        // Prevent adding out-of-stock items
        if (productQuantity <= 0) {
            showToast(`Cannot add ${productName} - Out of stock`, "error");
            return;
        }

        addToCart(productId, productName, productPrice, companyName, productQuantity);
        $('#search').val(''); // Clear search input
        $('#search-results').html(''); // Clear search results
        $('#search-results').hide();
    });

    // Function to add product to the cart and store in session
    function addToCart(id, name, price, companyName, quantity) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let existingProduct = cart.find(item => item.id === id);

        if (existingProduct) {
            // Check if adding one more would exceed stock
            if (existingProduct.qty + 1 > quantity) {
                showToast(`Cannot add more ${name} - Only ${quantity} in stock`, "error");
                return;
            }
            
            existingProduct.qty += 1;
            showToast(`Added another ${name} to cart`, "success");
        } else {
            cart.push({
                id,
                name,
                price,
                companyName,
                qty: 1,
                discount: 0,
                stockQuantity: quantity // Store stock quantity for validation
            });
            showToast(`Added ${name} to cart`, "success");
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

        if (cart.length === 0) {
            cartTable.html(`
                <tr>
                    <td colspan="7" class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty</p>
                        <p class="small text-muted">Search for products to add to your cart</p>
                    </td>
                </tr>
            `);
        } else {
            cart.forEach((item, index) => {
                // Calculate item values with proper parsing
                let itemQty = parseInt(item.qty) || 1;
                let itemPrice = parseFloat(item.price) || 0;
                let itemDiscount = parseFloat(item.discount || 0);
                let stockLimit = parseInt(item.stockQuantity) || 0;
                
                let itemSubtotal = itemQty * itemPrice;
                let itemTotalDiscount = itemDiscount * itemQty;
                let itemTotal = itemSubtotal - itemTotalDiscount;
                
                // Ensure positive total
                if (itemTotal < 0) itemTotal = 0;
                
                // For debugging
                console.log('Item:', item.name, 'Price:', itemPrice, 'Discount:', itemDiscount, 'Total:', itemTotal, 'Stock:', stockLimit);
                
                let row = `
                    <tr data-id="${item.id}" class="fade-in" style="animation-delay: ${index * 0.05}s">
                        <td>${item.name}</td>
                        <td>${item.companyName || 'N/A'}</td>
                        <td class="text-center">
                            <input type="number" 
                                class="qty form-control form-control-sm mx-auto" 
                                value="${itemQty}" 
                                min="1" 
                                max="${stockLimit}"
                                data-stock="${stockLimit}" 
                                style="max-width: 70px;">
                        </td>
                        <td class="text-end price">Rs. ${itemPrice.toFixed(2)}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="discount form-control form-control-sm" 
                                    value="${itemDiscount}" min="0">
                            </div>
                        </td>
                        <td class="text-end total">Rs. ${itemTotal.toFixed(2)}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm remove-btn" title="Remove" style="transition: all 0.3s ease; transform: none;">
                                <i class="ti ti-trash" style="filter: drop-shadow(0 0 2px rgba(0, 0, 0, 0.2));"></i>
                            </button>
                        </td>
                    </tr>`;
                cartTable.append(row);
            });
        }

        updateTotals();
    }

    // Update totals
    function updateTotals() {
        let subTotal = 0;
        let totalDiscount = 0;
        let netTotal = 0;

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        cart.forEach(item => {
            let itemSubtotal = item.qty * item.price;
            let itemDiscount = (item.discount || 0) * item.qty;
            
            subTotal += itemSubtotal;
            totalDiscount += itemDiscount;
        });

        netTotal = subTotal - totalDiscount;

        // Animate the changing values
        animateValue('#sub-total', subTotal);
        animateValue('#discount', totalDiscount);
        animateValue('#net-total', netTotal);
    }
    
    // Function to animate changing values
    function animateValue(selector, newValue) {
        const el = $(selector);
        const oldValue = parseFloat(el.text().replace(/[^\d.-]/g, '')) || 0;
        
        // If the values are close, don't animate
        if (Math.abs(newValue - oldValue) < 0.01) {
            el.text(`Rs. ${newValue.toFixed(2)}`);
            return;
        }
        
        // Add highlight class for animation
        el.addClass('text-primary');
        
        // Set new value
        el.text(`Rs. ${newValue.toFixed(2)}`);
        
        // Remove highlight after a delay
        setTimeout(() => {
            el.removeClass('text-primary');
        }, 500);
    }

    // Function to update session
    function updateSession(productId, qty, discount) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let product = cart.find(item => item.id === productId);

        if (product) {
            product.qty = parseInt(qty) || 1;
            product.discount = parseFloat(discount) || 0;
            
            // Show toast for quantity update
            showToast(`Updated ${product.name} quantity to ${qty}`, "info");
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateTotals();
    }

    // Update quantity event listener
    $(document).on('change', '.qty', function() {
        let row = $(this).closest('tr');
        let productId = row.data('id');
        let qty = parseInt($(this).val()) || 1;
        
        // Get the stock limit
        let stockLimit = parseInt($(this).attr('data-stock')) || 0;
        
        // Enforce minimum quantity
        if (qty < 1) {
            qty = 1;
            $(this).val(1);
            showToast("Quantity cannot be less than 1", "error");
        }
        
        // Enforce stock limit
        if (qty > stockLimit) {
            qty = stockLimit;
            $(this).val(stockLimit);
            showToast(`Maximum quantity available is ${stockLimit}`, "error");
        }
        
        // Extract price value by removing the currency symbol
        let priceText = row.find('.price').text().replace('Rs.', '').trim();
        let price = parseFloat(priceText) || 0;
        
        let discount = parseFloat(row.find('.discount').val()) || 0;
        
        let itemSubtotal = qty * price;
        let itemTotalDiscount = discount * qty;
        let itemTotal = itemSubtotal - itemTotalDiscount;
        
        row.find('.total').text(`Rs. ${itemTotal.toFixed(2)}`);
        updateSession(productId, qty, discount);
    });

    // Update discount event listener
    $(document).on('change', '.discount', function() {
        let row = $(this).closest('tr');
        let productId = row.data('id');
        let qty = parseInt(row.find('.qty').val()) || 1;
        
        // Extract price value by removing the currency symbol
        let priceText = row.find('.price').text().replace('Rs.', '').trim();
        let price = parseFloat(priceText) || 0;
        
        let discount = parseFloat($(this).val()) || 0;
        
        let itemSubtotal = qty * price;
        let itemTotalDiscount = discount * qty;
        let itemTotal = itemSubtotal - itemTotalDiscount;
        
        row.find('.total').text(`Rs. ${itemTotal.toFixed(2)}`);
        updateSession(productId, qty, discount);
    });

    // Function to remove an item from cart
    $(document).on('click', '.remove-btn', function() {
        let productId = $(this).closest('tr').data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Find the product name for toast notification
        let productName = $(this).closest('tr').find('td:first-child').text();
        
        // Filter out the product with the matching ID
        cart = cart.filter(item => item.id !== productId);

        // Save updated cart back to storage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Show feedback
        showToast(`Removed ${productName} from cart`, "info");
        
        // Update UI
        renderCart();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json' // Ensure JSON response
        }
    });

    // Checkout process
    $('#checkout-btn').on('click', function() {
        let customerId = $('#selected-customer-id').val();
        if (customerId === "") {
            showToast("Please select a customer first", "error");
            $('#customer-search').focus();
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            showToast("Cart is empty. Please add products before checkout.", "error");
            $('#search').focus();
            return;
        }

        let paidAmount = parseFloat($('#paid-amount').val()) || 0;
        let subTotal = 0;
        let totalDiscount = 0;
        let netTotal = 0;

        cart.forEach(item => {
            let itemSubtotal = item.qty * item.price;
            let itemDiscount = item.qty * (item.discount || 0);
            
            subTotal += itemSubtotal;
            totalDiscount += itemDiscount;
            netTotal += (itemSubtotal - itemDiscount);
        });

        // Show processing animation
        $('#processing').html(`
            <div class="alert alert-info d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>Processing your sale...</div>
            </div>
        `);
        
        // Disable checkout button
        $('#checkout-btn').prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
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
                net_total: netTotal.toFixed(2),
                paid_amount: paidAmount.toFixed(2)
            },
            success: function(response) {
                localStorage.removeItem('cart');
                
                // Show success message with animated icons
                $('#processing').html(`
                    <div class="alert alert-success border-0 shadow">
                        <button type="button" class="btn-close position-absolute" style="top: 10px; right: 10px;" 
                            onclick="$('#processing').html('')"></button>
                        <div class="text-center mb-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>Sale Completed Successfully!</h4>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="${response.print_url}" target="_blank" class="btn btn-primary btn-action w-100">
                                    <i class="fas fa-print me-2"></i> Print Invoice
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="${response.invoice_url}" target="_blank" class="btn btn-secondary btn-action w-100">
                                    <i class="fas fa-download me-2"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                `);
                
                // Show toast
                showToast("Sale completed successfully!", "success");
                
                // Reset form
                loadCartFromSession();
                $('#customer-search').val("");
                $('#paid-amount').val("0.00");
                
                // Re-enable checkout button
                $('#checkout-btn').prop('disabled', false).html(`
                    <i class="fas fa-check-circle me-2"></i>Complete Sale
                `);
                
                // Refresh product data to show updated stock quantities
                refreshProductData();
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error processing sale. Please try again.';
                $('#processing').html(`<div class="alert alert-danger">${errorMsg}</div>`);
                console.error("Error:", xhr.responseText);
                
                // Show toast
                showToast(errorMsg, "error");
                
                // Re-enable checkout button
                $('#checkout-btn').prop('disabled', false).html(`
                    <i class="fas fa-check-circle me-2"></i>Complete Sale
                `);
                
                // Refresh product data
                refreshProductData();
            }
        });
    });

    // Clear products cache and reload when order is completed
    function refreshProductData() {
        // Clear the cached products
        allProducts = [];
        
        // Show loading message
        showToast("Refreshing inventory data...", "info");
        
        // Fetch fresh data
        fetchAllProducts();
    }

    // Customer search functionality
    const customers = @json($customers);
    
    $('#customer-search').on('focus', function() {
        if ($(this).val().length > 0) {
            filterCustomers($(this).val());
        } else {
            populateAllCustomers();
        }
        $('#customer-dropdown').show();
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.customer-select-container').length) {
            $('#customer-dropdown').hide();
        }
    });
    
    // Customer search input handler
    $('#customer-search').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        
        if (query.length === 0) {
            populateAllCustomers();
        } else {
            filterCustomers(query);
        }
        
        $('#customer-dropdown').show();
    });
    
    // Populate all customers
    function populateAllCustomers() {
        let html = '';
        
        if (customers.length === 0) {
            html = '<div class="p-3 text-center text-muted">No customers found</div>';
        } else {
            customers.forEach(customer => {
                html += createCustomerItem(customer);
            });
        }
        
        $('#customer-dropdown').html(html);
    }
    
    // Filter customers based on query
    function filterCustomers(query) {
        query = query.toLowerCase();
        let html = '';
        let filteredCustomers = customers.filter(c => 
            c.name.toLowerCase().includes(query) || 
            (c.phone_no && c.phone_no.includes(query))
        );
        
        if (filteredCustomers.length === 0) {
            html = '<div class="p-3 text-center text-muted">No matching customers found</div>';
        } else {
            filteredCustomers.forEach(customer => {
                html += createCustomerItem(customer);
            });
        }
        
        $('#customer-dropdown').html(html);
    }
    
    // Create customer item HTML
    function createCustomerItem(customer) {
        return `
            <div class="customer-item" data-id="${customer.id}">
                <div class="customer-name">${customer.name}</div>
                <div class="customer-phone">${customer.phone_no || 'No phone'}</div>
            </div>
        `;
    }
    
    // Customer selection
    $(document).on('click', '.customer-item', function() {
        const customerId = $(this).data('id');
        const customerName = $(this).find('.customer-name').text();
        
        $('#selected-customer-id').val(customerId);
        $('#customer-search').val(customerName);
        $('#customer-dropdown').hide();
        
        showToast(`Selected customer: ${customerName}`, "success");
    });
});