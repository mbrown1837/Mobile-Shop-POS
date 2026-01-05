/**
 * Mobile Shop POS - Point of Sale JavaScript
 * Handles IMEI search, cart management, and transaction processing
 */

// Define base URL
var baseUrl = (typeof appRoot !== 'undefined') ? appRoot : (window.location.protocol + '//' + window.location.host + '/mobile-shop-pos/');

$(document).ready(function() {
    let selectedCustomerId = null;
    let currentStandardItem = null;

    // Initialize
    loadTransactionList();
    initializeDatePicker();
    initializeCustomerSelect();

    // ========================================
    // Customer Select Initialization
    // ========================================
    function initializeCustomerSelect() {
        $('#customerSelect').select2({
            placeholder: 'Search customer by name or phone',
            allowClear: true,
            ajax: {
                url: baseUrl + 'index.php/customers/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.customers.map(function(customer) {
                            return {
                                id: customer.id,
                                text: customer.name + ' - ' + customer.phone,
                                customer: customer
                            };
                        })
                    };
                },
                cache: true
            }
        });

        // When customer is selected
        $('#customerSelect').on('select2:select', function(e) {
            const customer = e.params.data.customer;
            selectedCustomerId = customer.id;
            
            // Show customer info
            $('#customerName').text(customer.name);
            $('#customerPhone').text(customer.phone);
            
            const balance = parseFloat(customer.balance || customer.current_balance || 0);
            const creditLimit = parseFloat(customer.credit_limit || 0);
            
            $('#customerBalance').text('Rs. ' + balance.toFixed(2))
                .removeClass('positive negative')
                .addClass(balance >= 0 ? 'positive' : 'negative');
            
            $('#customerCreditLimit').text('Rs. ' + creditLimit.toFixed(2));
            $('#customerInfoSection').removeClass('hidden');
        });

        // When customer is cleared
        $('#customerSelect').on('select2:clear', function() {
            selectedCustomerId = null;
            $('#customerInfoSection').addClass('hidden');
        });
    }

    // Quick Add Customer
    $('#quickAddCustomerBtn').on('click', function() {
        $('#quickAddCustomerForm')[0].reset();
        $('.errMsg').text('');
        $('#quickAddCustomerModal').modal('show');
    });

    $('#saveQuickCustomerBtn').on('click', function() {
        $('.errMsg').text('');
        
        const name = $('#quickCustName').val().trim();
        const phone = $('#quickCustPhone').val().trim();
        const creditLimit = $('#quickCustCreditLimit').val() || 50000;
        const address = $('#quickCustAddress').val().trim();

        if (!name) {
            $('#quickCustNameErr').text('Name is required');
            return;
        }
        if (!phone) {
            $('#quickCustPhoneErr').text('Phone is required');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: baseUrl + 'index.php/customers/quickAdd',
            type: 'POST',
            data: {
                name: name,
                phone: phone,
                credit_limit: creditLimit,
                address: address
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showSuccess('Customer added successfully!');
                    $('#quickAddCustomerModal').modal('hide');
                    
                    // Auto-select the new customer
                    const newOption = new Option(name + ' - ' + phone, response.customer_id, true, true);
                    $('#customerSelect').append(newOption).trigger('change');
                    
                    // Manually trigger select event with customer data
                    selectedCustomerId = response.customer_id;
                    $('#customerName').text(name);
                    $('#customerPhone').text(phone);
                    $('#customerBalance').text('Rs. 0.00').removeClass('negative').addClass('positive');
                    $('#customerCreditLimit').text('Rs. ' + parseFloat(creditLimit).toFixed(2));
                    $('#customerInfoSection').removeClass('hidden');
                } else {
                    if (response.name) $('#quickCustNameErr').text(response.name);
                    if (response.phone) $('#quickCustPhoneErr').text(response.phone);
                    showError(response.msg || 'Failed to add customer');
                }
            },
            error: function() {
                showError('Error adding customer');
            },
            complete: function() {
                $('#saveQuickCustomerBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Save & Select');
            }
        });
    });

    // ========================================
    // Unified Search (Name, Code, IMEI, Brand, Model)
    // ========================================
    let searchTimeout;
    
    $('#unifiedSearch').on('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = $(this).val().trim();
        
        if (searchValue.length < 2) {
            $('#searchResults').addClass('hidden');
            $('#standardItemSection').addClass('hidden');
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performUnifiedSearch(searchValue);
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Also search on Enter key
    $('#unifiedSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            const searchValue = $(this).val().trim();
            if (searchValue.length >= 2) {
                performUnifiedSearch(searchValue);
            }
        }
    });
    
    function performUnifiedSearch(searchTerm) {
        $.ajax({
            url: baseUrl + 'index.php/items/searchForPos',
            type: 'GET',
            data: { 
                search: searchTerm,
                _t: new Date().getTime() // Cache buster
            },
            dataType: 'json',
            cache: false, // Disable caching
            beforeSend: function() {
                // Clear previous results
                $('#searchResults table').html('<thead><tr><th colspan="9" class="text-center"><i class="fa fa-spinner fa-spin"></i> Searching...</th></tr></thead>');
                $('#searchResults').removeClass('hidden');
            },
            success: function(response) {
                if (response.status === 1 && response.items && response.items.length > 0) {
                    displaySearchResults(response.items);
                } else {
                    $('#searchResults table').html('<thead><tr><th>No Results</th></tr></thead><tbody><tr><td class="text-center text-muted">No items found for "' + searchTerm + '"</td></tr></tbody>');
                }
            },
            error: function() {
                $('#searchResults table').html('<thead><tr><th>Error</th></tr></thead><tbody><tr><td class="text-center text-danger">Error searching items</td></tr></tbody>');
            }
        });
    }
    
    function displaySearchResults(items) {
        // Separate items by type
        const serializedItems = items.filter(item => (item.item_type || 'standard') === 'serialized');
        const standardItems = items.filter(item => (item.item_type || 'standard') === 'standard');
        
        const hasSerializedItems = serializedItems.length > 0;
        
        // Build dynamic table header
        let headerHtml = '<thead><tr class="active">' +
            '<th>Item</th>' +
            '<th>Code</th>' +
            '<th>Type</th>' +
            '<th>Brand</th>';
        
        if (hasSerializedItems) {
            headerHtml += '<th>Color</th><th>SIM</th><th>IMEI(s)</th>';
        }
        
        headerHtml += '<th>Price</th><th>Action</th></tr></thead>';
        
        // Build table body
        let bodyHtml = '<tbody>';
        
        // Display standard items
        standardItems.forEach(function(item) {
            const modelInfo = item.model ? '<br><small class="text-muted">' + item.model + '</small>' : '';
            
            bodyHtml += '<tr>' +
                '<td><strong>' + item.name + '</strong>' + modelInfo + '</td>' +
                '<td><code>' + item.code + '</code></td>' +
                '<td><span class="label label-default"><i class="fa fa-cube"></i> Std</span></td>' +
                '<td>' + (item.brand || '-') + '</td>';
            
            if (hasSerializedItems) {
                bodyHtml += '<td colspan="3" class="text-center text-muted">-</td>';
            }
            
            bodyHtml += '<td>Rs. ' + parseFloat(item.unitPrice).toFixed(2) + '</td>' +
                '<td><button class="btn btn-xs btn-success add-standard-btn" ' +
                'data-item-code="' + item.code + '" ' +
                'data-item-name="' + item.name + '" ' +
                'data-item-price="' + item.unitPrice + '" ' +
                'data-item-qty="' + item.quantity + '">' +
                '<i class="fa fa-plus"></i> Add</button></td>' +
                '</tr>';
        });
        
        // Display serialized items with full details
        if (hasSerializedItems) {
            serializedItems.forEach(function(item) {
                $.ajax({
                    url: baseUrl + 'index.php/items/getAvailableMobileUnits',
                    type: 'GET',
                    data: { item_id: item.id },
                    dataType: 'json',
                    async: false,
                    success: function(response) {
                        if (response.status === 1 && response.units && response.units.length > 0) {
                            response.units.forEach(function(unit) {
                                const imeiCount = unit.imeis.length;
                                const imeiDisplay = unit.imeis.map(function(imei) {
                                    return '<code style="font-size: 11px;">' + imei + '</code>';
                                }).join('<br>');
                                
                                const simBadge = imeiCount > 1 ? 
                                    '<span class="label label-success">Dual</span>' : 
                                    '<span class="label label-default">Single</span>';
                                
                                const modelInfo = item.model ? '<br><small class="text-muted">' + item.model + '</small>' : '';
                                
                                bodyHtml += '<tr>' +
                                    '<td><strong>' + item.name + '</strong>' + modelInfo + '</td>' +
                                    '<td><code>' + item.code + '</code></td>' +
                                    '<td><span class="label label-info"><i class="fa fa-mobile"></i> Serial</span></td>' +
                                    '<td>' + (item.brand || '-') + '</td>' +
                                    '<td>' + (unit.color || '-') + '</td>' +
                                    '<td>' + simBadge + '</td>' +
                                    '<td style="max-width: 150px;">' + imeiDisplay + '</td>' +
                                    '<td>Rs. ' + parseFloat(item.unitPrice).toFixed(2) + '</td>' +
                                    '<td><button class="btn btn-xs btn-success add-mobile-direct-btn" ' +
                                    'data-item-code="' + item.code + '" ' +
                                    'data-imeis=\'' + JSON.stringify(unit.imeis) + '\'>' +
                                    '<i class="fa fa-shopping-cart"></i> Add</button></td>' +
                                    '</tr>';
                            });
                        }
                    }
                });
            });
        }
        
        bodyHtml += '</tbody>';
        
        // Replace entire table content
        $('#searchResults table').html(headerHtml + bodyHtml);
    }
    
    // Handle direct mobile add to cart
    $(document).on('click', '.add-mobile-direct-btn', function(e) {
        e.preventDefault();
        const itemCode = $(this).data('item-code');
        const imeis = JSON.parse($(this).attr('data-imeis'));
        
        // Add to cart with all IMEIs
        addItemToCart(itemCode, imeis.join(','));
        
        $('#unifiedSearch').val('').focus();
        $('#searchResults').addClass('hidden');
    });
    
    // Handle Add Standard Item from search results
    $(document).on('click', '.add-standard-btn', function() {
        const itemCode = $(this).data('item-code');
        const itemName = $(this).data('item-name');
        const itemPrice = $(this).data('item-price');
        const itemQty = $(this).data('item-qty');
        
        // If only 1 quantity available, add directly to cart
        if (itemQty == 1) {
            addItemToCart(itemCode, null, 1);
            $('#unifiedSearch').val('').focus();
            $('#searchResults').addClass('hidden');
        } else {
            // Show quantity selection section
            currentStandardItem = {
                code: itemCode,
                name: itemName,
                unitPrice: itemPrice,
                quantity: itemQty
            };
            
            $('#foundItemName').text(itemName);
            $('#foundItemCode').text(itemCode);
            $('#foundItemPrice').text('Rs. ' + parseFloat(itemPrice).toFixed(2));
            $('#foundItemQty').text(itemQty);
            $('#standardItemSection').removeClass('hidden');
            $('#standardItemQty').val(1).attr('max', itemQty).focus();
            $('#searchResults').addClass('hidden');
            $('#unifiedSearch').val('');
        }
    });
    
    // Handle Add Mobile (for serialized items) - Show mobile details inline
    $(document).on('click', '.add-mobile-btn', function() {
        const itemId = $(this).data('item-id');
        const itemCode = $(this).data('item-code');
        const itemName = $(this).data('item-name');
        const itemPrice = $(this).data('item-price');
        const itemBrand = $(this).data('item-brand');
        
        // Load available mobiles and show inline
        $.ajax({
            url: baseUrl + 'index.php/items/getAvailableMobileUnits',
            type: 'GET',
            data: { item_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1 && response.units && response.units.length > 0) {
                    // Show mobile selection section with simple list
                    let html = '<div class="panel panel-success">' +
                        '<div class="panel-heading">' +
                        '<strong>' + itemName + '</strong> - ' + itemBrand + ' - Rs. ' + parseFloat(itemPrice).toFixed(2) +
                        '</div>' +
                        '<div class="panel-body"><div class="row">';
                    
                    response.units.forEach(function(unit) {
                        const imeiCount = unit.imeis.length;
                        const imeiList = unit.imeis.map(function(imei) {
                            return '<div><code>' + imei + '</code></div>';
                        }).join('');
                        
                        const simBadge = imeiCount > 1 ? 
                            '<span class="label label-success">Dual SIM</span>' : 
                            '<span class="label label-default">Single SIM</span>';
                        
                        const color = unit.color || 'N/A';
                        const cost = unit.cost_price ? '<small class="text-muted">Cost: Rs. ' + parseFloat(unit.cost_price).toFixed(2) + '</small>' : '';
                        
                        html += '<div class="col-sm-6" style="margin-bottom: 10px;">' +
                            '<div class="well well-sm">' +
                            '<div><strong>Color:</strong> ' + color + ' ' + simBadge + '</div>' +
                            '<div style="margin: 8px 0;"><strong>IMEI(s):</strong><br>' + imeiList + '</div>' +
                            (cost ? '<div>' + cost + '</div>' : '') +
                            '<button class="btn btn-success btn-sm btn-block select-mobile-unit" style="margin-top: 8px;" ' +
                            'data-item-code="' + itemCode + '" ' +
                            'data-imeis=\'' + JSON.stringify(unit.imeis) + '\'>' +
                            '<i class="fa fa-shopping-cart"></i> Add to Cart</button>' +
                            '</div></div>';
                    });
                    
                    html += '</div></div></div>';
                    
                    $('#mobileSelectionSection').html(html).removeClass('hidden');
                    $('#searchResults').addClass('hidden');
                } else {
                    showError('No available mobiles');
                }
            },
            error: function() {
                showError('Error loading mobiles');
            }
        });
    });
    
    // Handle mobile selection - add to cart
    $(document).on('click', '.select-mobile-unit', function(e) {
        e.preventDefault();
        const itemCode = $(this).data('item-code');
        const imeis = JSON.parse($(this).attr('data-imeis'));
        
        // Add to cart with all IMEIs
        addItemToCart(itemCode, imeis.join(','));
        
        $('#mobileSelectionSection').addClass('hidden').html('');
        $('#unifiedSearch').val('').focus();
    });

    // Add standard item to cart
    $('#addStandardItemBtn').on('click', function() {
        if (!currentStandardItem) return;
        
        const qty = parseInt($('#standardItemQty').val());
        if (qty < 1) {
            showError('Quantity must be at least 1');
            return;
        }

        addItemToCart(currentStandardItem.code, null, qty);
        
        // Reset
        $('#itemCodeSearch').val('');
        $('#standardItemSection').addClass('hidden');
        currentStandardItem = null;
    });

    // ========================================
    // Cart Management
    // ========================================
    function addItemToCart(itemCode, imei = null, qty = 1) {
        $.ajax({
            url: baseUrl + 'index.php/transactions/addToCart',
            type: 'POST',
            data: { 
                itemCode: itemCode,
                imei: imei,
                qty: qty
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showSuccess(response.msg);
                    refreshCart();
                } else {
                    showError(response.msg);
                }
            },
            error: function() {
                showError('Error adding item to cart');
            }
        });
    }

    function refreshCart() {
        const discountAmount = parseFloat($('#discountAmount').val()) || 0;

        $.ajax({
            url: baseUrl + 'index.php/transactions/getCart',
            type: 'POST',
            data: { 
                discount_amount: discountAmount
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    displayCart(response.cartItems, response.totals);
                }
            },
            error: function() {
                console.error('Error refreshing cart');
            }
        });
    }

    function getCartItems() {
        // Get cart items from server
        let cartItems = [];
        
        $.ajax({
            url: baseUrl + 'index.php/transactions/getCart',
            type: 'GET',
            dataType: 'json',
            async: false, // Synchronous call to get cart items
            success: function(response) {
                if (response.status === 1 && response.cartItems) {
                    // Convert cart object to array
                    cartItems = Object.values(response.cartItems);
                }
            }
        });
        
        return cartItems;
    }

    function displayCart(cartItems, totals) {
        const container = $('#cartItemsContainer');
        
        if (Object.keys(cartItems).length === 0) {
            container.html('<p class="text-center text-muted">Cart is empty. Scan an IMEI or item code to add items.</p>');
            $('#cartItemCount').text('0');
            updateTotals({ subtotal: 0, discount_amount: 0, grand_total: 0 });
            return;
        }

        let html = '';
        let itemCount = 0;

        $.each(cartItems, function(cartItemId, item) {
            itemCount++;
            html += `
                <div class="cart-item" data-cart-id="${cartItemId}">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>${item.name}</strong><br>
                            <small class="text-muted">Code: ${item.code}</small>
                            ${item.imei ? '<br><span class="imei-badge">IMEI: ' + item.imei + '</span>' : ''}
                            ${item.brand ? '<br><small>Brand: ' + item.brand + '</small>' : ''}
                            ${item.color ? '<small> | Color: ' + item.color + '</small>' : ''}
                        </div>
                        <div class="col-sm-3 text-right">
                            ${item.item_type === 'standard' ? 
                                '<input type="number" class="form-control input-sm cart-qty-input" data-cart-id="' + cartItemId + '" value="' + item.qty + '" min="1" style="width: 70px; display: inline;">' :
                                '<span>Qty: 1</span>'
                            }
                            <br>
                            <small>@ Rs. ${parseFloat(item.unitPrice).toFixed(2)}</small>
                        </div>
                        <div class="col-sm-2 text-right">
                            <strong class="currency">Rs. ${parseFloat(item.totalPrice).toFixed(2)}</strong>
                        </div>
                        <div class="col-sm-1 text-right">
                            <button class="btn btn-danger btn-xs remove-cart-item" data-cart-id="${cartItemId}">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
        $('#cartItemCount').text(itemCount);
        updateTotals(totals);
    }

    function updateTotals(totals) {
        $('#subtotalAmount').text('Rs. ' + parseFloat(totals.subtotal).toFixed(2));
        $('#discountDisplay').text('Rs. ' + parseFloat(totals.discount_amount).toFixed(2));
        $('#grandTotalAmount').text('Rs. ' + parseFloat(totals.grand_total).toFixed(2));
        
        // Auto-update Amount Tendered if Cash payment is selected
        const paymentMethod = $('#paymentMethod').val();
        if (paymentMethod === 'cash') {
            $('#amountTendered').val(parseFloat(totals.grand_total).toFixed(2));
            // Update change due
            const tendered = parseFloat($('#amountTendered').val()) || 0;
            const change = tendered - parseFloat(totals.grand_total);
            $('#changeDue').text('Rs. ' + change.toFixed(2));
        }
    }

    // Remove item from cart
    $(document).on('click', '.remove-cart-item', function() {
        const cartItemId = $(this).data('cart-id');
        
        $.ajax({
            url: baseUrl + 'transactions/removeFromCart',
            type: 'POST',
            data: { cartItemId: cartItemId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showSuccess(response.msg);
                    refreshCart();
                } else {
                    showError(response.msg);
                }
            },
            error: function() {
                showError('Error removing item from cart');
            }
        });
    });

    // Update cart quantity
    $(document).on('change', '.cart-qty-input', function() {
        const cartItemId = $(this).data('cart-id');
        const qty = parseInt($(this).val());

        if (qty < 1) {
            $(this).val(1);
            return;
        }

        $.ajax({
            url: baseUrl + 'transactions/updateCartQty',
            type: 'POST',
            data: { 
                cartItemId: cartItemId,
                qty: qty
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    refreshCart();
                } else {
                    showError(response.msg);
                    refreshCart(); // Refresh to reset quantity
                }
            },
            error: function() {
                showError('Error updating quantity');
            }
        });
    });

    // Clear cart
    $('#clearCartBtn').on('click', function() {
        if (!confirm('Are you sure you want to clear the cart?')) return;

        $.ajax({
            url: baseUrl + 'transactions/clearCart',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showSuccess(response.msg);
                    refreshCart();
                } else {
                    showError(response.msg);
                }
            },
            error: function() {
                showError('Error clearing cart');
            }
        });
    });

    // ========================================
    // Discount input
    // ========================================
    $('#discountAmount').on('input', function() {
        // Remove leading zeros
        let value = $(this).val();
        if (value && value.length > 1 && value.startsWith('0') && value[1] !== '.') {
            value = value.replace(/^0+/, '');
            $(this).val(value);
        }
        refreshCart();
    });

    // ========================================
    // Payment Method - Simplified (Cash or Credit only)
    // ========================================
    // Payment Method - Simplified (Cash or Credit only)
    // ========================================
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        
        // Hide all sections first
        $('#cashPaymentSection, #creditPaymentSection, #customerPanel').hide();

        if (method === 'cash') {
            // Show cash payment section
            $('#cashPaymentSection').removeClass('hidden').show();
            const grandTotal = parseFloat($('#grandTotalAmount').text().replace('Rs. ', '').replace(/,/g, '')) || 0;
            $('#amountTendered').val(grandTotal.toFixed(2)); // Auto-fill with exact amount (removed .focus())
            
            // Hide customer panel for cash
            $('#customerPanel').hide();
            selectedCustomerId = null;
            $('#customerSelect').val(null).trigger('change');
            $('#customerInfoSection').addClass('hidden');
        } else if (method === 'credit') {
            // Show credit payment section and customer panel
            $('#creditPaymentSection').removeClass('hidden').show();
            $('#customerPanel').show();
            
            // Focus on customer select
            setTimeout(function() {
                $('#customerSelect').select2('open');
            }, 100);
        }
    });

    // Trigger change on page load to show cash by default
    $(document).ready(function() {
        $('#paymentMethod').trigger('change');
    });

    // Calculate change due for cash payment
    $('#amountTendered').on('input', function() {
        const tendered = parseFloat($(this).val()) || 0;
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('Rs. ', '').replace(/,/g, '')) || 0;
        const change = tendered - grandTotal;
        
        $('#changeDue').text('Rs. ' + change.toFixed(2));
    });

    // Calculate credit amount for partial payment
    $('#partialAmount').on('input', function() {
        const paid = parseFloat($(this).val()) || 0;
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('Rs. ', '').replace(/,/g, '')) || 0;
        const credit = grandTotal - paid;
        
        $('#creditAmount').text('Rs. ' + credit.toFixed(2));
    });

    // ========================================
    // Customer Selection
    // ========================================
    // TODO: Implement customer search/select with Select2 or similar
    // For now, placeholder functionality

    // ========================================
    // ========================================
    // Complete Transaction - Simplified (Cash or Credit only)
    // ========================================
    $('#completeTransactionBtn').on('click', function() {
        // Check if cart has items
        const cartItems = getCartItems();
        if (!cartItems || cartItems.length === 0) {
            showNotification('error', 'Cart is empty. Please add items first.');
            return;
        }

        const paymentMethod = $('#paymentMethod').val();
        
        if (!paymentMethod) {
            showNotification('error', 'Please select payment method (Cash or Credit)');
            return;
        }

        const discountAmount = parseFloat($('#discountAmount').val()) || 0;
        const grandTotalText = $('#grandTotalAmount').text().replace('Rs. ', '').replace(/,/g, '').trim();
        const grandTotal = parseFloat(grandTotalText) || 0;

        // Debug logging
        console.log('Grand Total Text:', grandTotalText);
        console.log('Grand Total Parsed:', grandTotal);

        let amountTendered = 0;

        // Validate based on payment method
        if (paymentMethod === 'cash') {
            const tenderedInput = $('#amountTendered').val().trim();
            amountTendered = parseFloat(tenderedInput) || 0;
            
            // Debug logging
            console.log('Amount Tendered Input:', tenderedInput);
            console.log('Amount Tendered Parsed:', amountTendered);
            
            // Round to 2 decimal places for comparison
            const roundedTendered = Math.round(amountTendered * 100) / 100;
            const roundedTotal = Math.round(grandTotal * 100) / 100;
            
            console.log('Rounded Tendered:', roundedTendered);
            console.log('Rounded Total:', roundedTotal);
            console.log('Comparison:', roundedTendered, '<', roundedTotal, '=', roundedTendered < roundedTotal);
            
            if (roundedTendered < roundedTotal) {
                showNotification('error', 'Amount tendered (Rs. ' + roundedTendered.toFixed(2) + ') is less than total (Rs. ' + roundedTotal.toFixed(2) + ')');
                $('#amountTendered').focus();
                return;
            }
        }

        if (paymentMethod === 'credit') {
            if (!selectedCustomerId) {
                showNotification('error', 'Please select a customer for credit/khata sale');
                return;
            }
            amountTendered = grandTotal; // For credit, amount tendered = total
        }

        // Prepare transaction data
        const transactionData = {
            cart_items: JSON.stringify(cartItems),
            payment_method: paymentMethod,
            discount_amount: discountAmount,
            amount_tendered: amountTendered,
            customer_id: selectedCustomerId || null
        };

        // Process transaction
        $.ajax({
            url: baseUrl + 'index.php/transactions/processTransaction',
            type: 'POST',
            data: transactionData,
            dataType: 'json',
            beforeSend: function() {
                $('#completeTransactionBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },
            success: function(response) {
                if (response.status === 1) {
                    // Reset form first
                    resetPOS();
                    
                    // Show success message
                    showNotification('success', 'Sale completed successfully!');
                    
                    // Reload transaction list
                    loadTransactionList();
                    
                    // Show success modal with receipt option
                    showTransactionSuccess(response.ref, response.grand_total);
                } else {
                    showNotification('error', response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.log('XHR Response:', xhr.responseText);
                console.log('Status:', status);
                console.log('Error:', error);
                
                // Try to parse JSON response
                try {
                    const response = JSON.parse(xhr.responseText);
                    showNotification('error', response.msg || 'Transaction failed');
                    console.log('Error details:', response);
                } catch(e) {
                    showNotification('error', 'Transaction failed: ' + error);
                    console.log('Raw error:', xhr.responseText);
                }
            },
            complete: function() {
                $('#completeTransactionBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Complete Transaction');
            }
        });
    });

    function resetPOS() {
        // Clear search fields
        $('#unifiedSearch').val('');
        $('#searchResults').addClass('hidden');
        $('#standardItemSection').addClass('hidden');
        currentStandardItem = null;
        
        // Reset payment fields
        $('#paymentMethod').val('cash'); // Set to cash by default
        $('#discountAmount').val(''); // Empty, not 0
        $('#amountTendered').val('');
        $('#changeDue').text('Rs. 0.00');
        $('#cashPaymentSection, #creditPaymentSection').addClass('hidden');
        
        // Reset customer
        selectedCustomerId = null;
        $('#customerSelect').val(null).trigger('change');
        $('#customerInfoSection').addClass('hidden');
        $('#customerPanel').hide();
        
        // Trigger payment method change to show cash section
        $('#paymentMethod').trigger('change');
        
        // Refresh cart (should be empty now)
        refreshCart();
    }

    // ========================================
    // Transaction List
    // ========================================
    function loadTransactionList() {
        const limit = $('#transListPerPage').val() || 10;
        const sortBy = $('#transListSortBy').val() || 'transDate-DESC';
        const [orderBy, orderFormat] = sortBy.split('-');

        $.ajax({
            url: baseUrl + 'transactions/latr_',
            type: 'GET',
            data: {
                limit: limit,
                orderBy: orderBy,
                orderFormat: orderFormat
            },
            dataType: 'json',
            success: function(response) {
                if (response.transTable) {
                    $('#transListTable').html(response.transTable);
                }
            },
            error: function() {
                console.error('Error loading transactions');
            }
        });
    }

    $('#transListPerPage, #transListSortBy').on('change', function() {
        loadTransactionList();
    });

    // ========================================
    // Date Picker for Reports
    // ========================================
    function initializeDatePicker() {
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        }
    }

    $('#clickToGen').on('click', function() {
        const fromDate = $('#transFrom').val();
        const toDate = $('#transTo').val();

        if (!fromDate) {
            showError('Please select from date');
            return;
        }

        const url = baseUrl + 'transactions/report/' + fromDate + (toDate ? '/' + toDate : '');
        window.open(url, '_blank');
    });

    // ========================================
    // Helper Functions
    // ========================================
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const title = type === 'success' ? 'Success!' : 'Error!';
        const duration = type === 'success' ? 3000 : 5000;
        
        // Create toast notification
        const toast = $('<div class="alert ' + alertClass + ' alert-dismissible" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<strong><i class="fa ' + icon + '"></i> ' + title + '</strong> ' + message +
            '</div>');
        
        $('body').append(toast);
        
        // Auto-remove after duration
        setTimeout(function() {
            toast.fadeOut(function() {
                $(this).remove();
            });
        }, duration);
    }

    function showError(message) {
        showNotification('error', message);
    }

    function showSuccess(message) {
        showNotification('success', message);
    }

    function showTransactionSuccess(ref, total) {
        // Show success toast
        showSuccess('Transaction completed! Ref: ' + ref);
        
        // Show modal with receipt option
        const modalHtml = '<div class="modal fade" id="transactionSuccessModal" tabindex="-1">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header bg-success text-white">' +
            '<h4 class="modal-title"><i class="fa fa-check-circle"></i> Transaction Completed!</h4>' +
            '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>' +
            '</div>' +
            '<div class="modal-body text-center">' +
            '<p class="lead">Transaction processed successfully</p>' +
            '<div class="well" style="background: #f5f5f5; padding: 20px; margin: 20px 0;">' +
            '<h3 style="margin: 0; color: #333;">Ref: ' + ref + '</h3>' +
            '<h4 style="margin: 10px 0; color: #5cb85c;">Total: Rs. ' + total + '</h4>' +
            '</div>' +
            '<p>Would you like to view the receipt?</p>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" class="btn btn-primary" onclick="viewReceipt(\'' + ref + '\')"><i class="fa fa-file-text"></i> View Receipt</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        // Remove existing modal if any
        $('#transactionSuccessModal').remove();
        
        // Add and show modal
        $('body').append(modalHtml);
        $('#transactionSuccessModal').modal('show');
        
        // Remove modal from DOM after it's hidden
        $('#transactionSuccessModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // View receipt function
    window.viewReceipt = function(ref) {
        $('#transactionSuccessModal').modal('hide');
        // Open receipt in modal instead of new window
        $('#transReceiptModal').modal('show');
        $('#transReceipt').html('<i class="fa fa-spinner fa-spin"></i> Loading receipt...');
        
        $.ajax({
            url: baseUrl + 'transactions/vtr_',
            type: 'POST',
            data: {ref: ref},
            success: function(response) {
                if (response.status === 1) {
                    $('#transReceipt').html(response.transReceipt);
                } else {
                    $('#transReceipt').html('<p class="text-danger">Receipt not found</p>');
                }
            },
            error: function() {
                $('#transReceipt').html('<p class="text-danger">Error loading receipt</p>');
            }
        });
    };

    // Auto-refresh cart on page load
    refreshCart();
});

