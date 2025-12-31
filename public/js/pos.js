/**
 * Mobile Shop POS - Point of Sale JavaScript
 * Handles IMEI search, cart management, and transaction processing
 */

// Define base URL
var baseUrl = (typeof appRoot !== 'undefined') ? appRoot : (window.location.protocol + '//' + window.location.host + '/mobile-shop-pos/');

$(document).ready(function() {
    let selectedCustomerId = null;
    let tradeInData = null;
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
            data: { search: searchTerm },
            dataType: 'json',
            beforeSend: function() {
                $('#searchResultsBody').html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Searching...</td></tr>');
                $('#searchResults').removeClass('hidden');
            },
            success: function(response) {
                if (response.status === 1 && response.items && response.items.length > 0) {
                    displaySearchResults(response.items);
                } else {
                    $('#searchResultsBody').html('<tr><td colspan="6" class="text-center text-muted">No items found</td></tr>');
                }
            },
            error: function() {
                $('#searchResultsBody').html('<tr><td colspan="6" class="text-center text-danger">Error searching items</td></tr>');
            }
        });
    }
    
    function displaySearchResults(items) {
        let html = '';
        
        items.forEach(function(item) {
            const itemType = item.item_type || 'standard';
            const available = item.available_qty || item.quantity || 0;
            const typeLabel = itemType === 'serialized' 
                ? '<span class="label label-info"><i class="fa fa-mobile"></i> Serial</span>' 
                : '<span class="label label-default"><i class="fa fa-cube"></i> Std</span>';
            
            const brandInfo = item.brand ? '<br><small class="text-muted">' + item.brand + (item.model ? ' ' + item.model : '') + '</small>' : '';
            const badgeClass = available <= 10 ? 'badge-danger' : 'badge-success';
            
            let actionButton = '';
            if (itemType === 'serialized') {
                actionButton = '<button class="btn btn-xs btn-primary select-mobile-btn" data-item-id="' + item.id + '" data-item-code="' + item.code + '" data-item-name="' + item.name + '" data-item-price="' + item.unitPrice + '">' +
                    '<i class="fa fa-mobile"></i> Select Unit</button>';
            } else {
                actionButton = '<button class="btn btn-xs btn-success add-standard-btn" ' +
                    'data-item-code="' + item.code + '" ' +
                    'data-item-name="' + item.name + '" ' +
                    'data-item-price="' + item.unitPrice + '" ' +
                    'data-item-qty="' + available + '">' +
                    '<i class="fa fa-plus"></i> Add</button>';
            }
            
            html += '<tr>' +
                '<td><strong>' + item.name + '</strong>' + brandInfo + '</td>' +
                '<td><code>' + item.code + '</code></td>' +
                '<td>' + typeLabel + '</td>' +
                '<td><span class="currency">Rs. ' + parseFloat(item.unitPrice).toFixed(2) + '</span></td>' +
                '<td><span class="badge ' + badgeClass + '">' + available + '</span></td>' +
                '<td>' + actionButton + '</td>' +
                '</tr>';
        });
        
        $('#searchResultsBody').html(html);
    }
    
    // Handle Add Standard Item from search results
    $(document).on('click', '.add-standard-btn', function() {
        const itemCode = $(this).data('item-code');
        const itemName = $(this).data('item-name');
        const itemPrice = $(this).data('item-price');
        const itemQty = $(this).data('item-qty');
        
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
        $('#standardItemQty').val(1).focus();
        $('#searchResults').addClass('hidden');
        $('#unifiedSearch').val('');
    });
    
    // Handle Select Mobile Unit (for serialized items)
    $(document).on('click', '.select-mobile-btn', function() {
        const itemId = $(this).data('item-id');
        const itemCode = $(this).data('item-code');
        const itemName = $(this).data('item-name');
        const itemPrice = $(this).data('item-price');
        
        showMobileUnitSelectionModal(itemId, itemCode, itemName, itemPrice);
    });
    
    function showMobileUnitSelectionModal(itemId, itemCode, itemName, itemPrice) {
        // Load available mobile units
        $.ajax({
            url: baseUrl + 'index.php/items/getAvailableMobileUnits',
            type: 'GET',
            data: { item_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1 && response.units && response.units.length > 0) {
                    let html = '<div class="modal fade" id="mobileUnitModal" tabindex="-1">' +
                        '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                        '<h4 class="modal-title"><i class="fa fa-mobile"></i> Select Mobile - ' + itemName + '</h4>' +
                        '<p class="text-muted">Price: <span class="currency">Rs. ' + parseFloat(itemPrice).toFixed(2) + '</span> | Available: ' + response.units.length + ' units</p>' +
                        '</div>' +
                        '<div class="modal-body" style="max-height: 500px; overflow-y: auto;">' +
                        '<div class="row">';
                    
                    response.units.forEach(function(unit, index) {
                        const imeiCount = unit.imeis.length;
                        const imeiDisplay = unit.imeis.map(function(imei) {
                            return '<code>' + imei + '</code>';
                        }).join('<br>');
                        
                        const simType = imeiCount > 1 ? '<span class="label label-success">Dual SIM</span>' : '<span class="label label-default">Single SIM</span>';
                        const colorBadge = unit.color ? '<span class="label label-primary">' + unit.color + '</span>' : '<span class="label label-default">No Color</span>';
                        const costInfo = unit.cost_price ? '<br><small><strong>Cost:</strong> Rs. ' + parseFloat(unit.cost_price).toFixed(2) + '</small>' : '';
                        
                        html += '<div class="col-sm-6" style="margin-bottom: 15px;">' +
                            '<div class="panel panel-default" style="cursor: pointer; transition: all 0.2s;" ' +
                            'onmouseover="this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.2)\'" ' +
                            'onmouseout="this.style.boxShadow=\'none\'">' +
                            '<div class="panel-body select-unit-item" ' +
                            'data-unit-index="' + index + '" ' +
                            'data-item-code="' + itemCode + '" ' +
                            'data-imeis=\'' + JSON.stringify(unit.imeis) + '\'>' +
                            '<h4><i class="fa fa-mobile"></i> Unit #' + (index + 1) + ' ' + colorBadge + ' ' + simType + '</h4>' +
                            '<p><strong>IMEI' + (imeiCount > 1 ? 's' : '') + ':</strong><br>' + imeiDisplay + costInfo + '</p>' +
                            '<button class="btn btn-success btn-sm btn-block">' +
                            '<i class="fa fa-shopping-cart"></i> Add to Cart' +
                            '</button>' +
                            '</div></div></div>';
                    });
                    
                    html += '</div></div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
                        '</div>' +
                        '</div></div></div>';
                    
                    // Remove existing modal if any
                    $('#mobileUnitModal').remove();
                    
                    // Add and show modal
                    $('body').append(html);
                    $('#mobileUnitModal').modal('show');
                } else {
                    showError('No available units for this item');
                }
            },
            error: function() {
                showError('Error loading mobile units');
            }
        });
    }
    
    // Handle Mobile Unit selection
    $(document).on('click', '.select-unit-item', function(e) {
        e.preventDefault();
        const itemCode = $(this).data('item-code');
        const imeis = JSON.parse($(this).attr('data-imeis'));
        
        $('#mobileUnitModal').modal('hide');
        
        // Add to cart with all IMEIs
        addItemToCart(itemCode, imeis.join(','));
        
        $('#unifiedSearch').val('').focus();
        $('#searchResults').addClass('hidden');
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
        const discount = parseFloat($('#discountPercent').val()) || 0;
        const vat = parseFloat($('#vatPercent').val()) || 0;

        $.ajax({
            url: baseUrl + 'index.php/transactions/getCart',
            type: 'POST',
            data: { 
                discount: discount,
                vat: vat
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
            updateTotals({ subtotal: 0, discount_amount: 0, after_discount: 0, vat_amount: 0, grand_total: 0 });
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
        $('#discountAmount').text('Rs. ' + parseFloat(totals.discount_amount).toFixed(2));
        $('#afterDiscountAmount').text('Rs. ' + parseFloat(totals.after_discount).toFixed(2));
        $('#vatAmount').text('Rs. ' + parseFloat(totals.vat_amount).toFixed(2));
        $('#grandTotalAmount').text('Rs. ' + parseFloat(totals.grand_total).toFixed(2));
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
    // Discount and VAT
    // ========================================
    $('#discountPercent, #vatPercent').on('input', function() {
        refreshCart();
    });

    // ========================================
    // Payment Method
    // ========================================
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        
        // Hide all payment sections
        $('#fullPaymentSection, #partialPaymentSection').addClass('hidden');

        if (method === 'cash' || method === 'pos') {
            $('#fullPaymentSection').removeClass('hidden');
            $('#amountTendered').focus();
        } else if (method === 'partial') {
            if (!selectedCustomerId) {
                showError('Please select a customer for partial payment');
                $(this).val('');
                return;
            }
            $('#partialPaymentSection').removeClass('hidden');
            $('#partialAmount').focus();
        } else if (method === 'credit') {
            if (!selectedCustomerId) {
                showError('Please select a customer for credit sale');
                $(this).val('');
                return;
            }
        }
    });

    // Calculate change due
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
    // Trade-In
    // ========================================
    $('#addTradeInBtn').on('click', function() {
        $('#tradeInModal').modal('show');
    });

    $('#saveTradeInBtn').on('click', function() {
        const brand = $('#tradeInBrand').val().trim();
        const model = $('#tradeInModel').val().trim();
        const imei = $('#tradeInImei').val().trim();
        const condition = $('#tradeInCondition').val();
        const value = parseFloat($('#tradeInValue').val()) || 0;
        const storage = $('#tradeInStorage').val().trim();
        const color = $('#tradeInColor').val().trim();
        const notes = $('#tradeInNotes').val().trim();

        // Validation
        if (!brand) {
            showError('Please enter brand');
            return;
        }
        if (!model) {
            showError('Please enter model');
            return;
        }
        if (!condition) {
            showError('Please select condition');
            return;
        }
        if (value <= 0) {
            showError('Please enter a valid trade-in value');
            return;
        }

        // Validate IMEI if provided
        if (imei && !(/^\d{15}$/.test(imei))) {
            showError('IMEI must be exactly 15 digits');
            return;
        }

        // Check if trade-in value exceeds grand total
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('₨ ', '').replace(',', '')) || 0;
        if (value > grandTotal) {
            showError('Trade-in value cannot exceed transaction total');
            return;
        }

        // Store trade-in data
        tradeInData = {
            brand: brand,
            model: model,
            imei: imei,
            condition: condition,
            value: value,
            storage: storage,
            color: color,
            notes: notes
        };

        // Display trade-in info
        $('#tradeInValue').text('₨ ' + value.toFixed(2));
        $('#tradeInInfo').removeClass('hidden');

        // Close modal and reset form
        $('#tradeInModal').modal('hide');
        $('#tradeInForm')[0].reset();

        showSuccess('Trade-in added successfully');
    });

    $('#removeTradeInBtn').on('click', function() {
        if (confirm('Are you sure you want to remove the trade-in?')) {
            tradeInData = null;
            $('#tradeInInfo').addClass('hidden');
            showSuccess('Trade-in removed');
        }
    });

    // ========================================
    // Complete Transaction
    // ========================================
    $('#completeTransactionBtn').on('click', function() {
        // Check if cart has items
        const cartItems = getCartItems();
        if (!cartItems || cartItems.length === 0) {
            showError('Cart is empty. Please add items first.');
            return;
        }

        const paymentMethod = $('#paymentMethod').val();
        
        if (!paymentMethod) {
            showError('Please select a payment method');
            return;
        }

        const discountPercent = parseFloat($('#discountPercent').val()) || 0;
        const vatPercent = parseFloat($('#vatPercent').val()) || 0;
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('Rs. ', '').replace(/,/g, '')) || 0;

        let amountTendered = 0;
        let partialAmount = 0;

        // Validate based on payment method
        if (paymentMethod === 'cash' || paymentMethod === 'pos') {
            amountTendered = parseFloat($('#amountTendered').val()) || 0;
            
            if (amountTendered < grandTotal) {
                showError('Amount tendered is less than grand total');
                return;
            }
        }

        if (paymentMethod === 'partial') {
            if (!selectedCustomerId) {
                showError('Please select a customer for partial payment');
                return;
            }
            
            partialAmount = parseFloat($('#partialAmount').val()) || 0;
            
            if (partialAmount <= 0) {
                showError('Please enter amount paid');
                return;
            }

            if (partialAmount >= grandTotal) {
                showError('Partial amount must be less than total. Use full payment instead.');
                return;
            }
        }

        if (paymentMethod === 'credit') {
            if (!selectedCustomerId) {
                showError('Please select a customer for credit sale');
                return;
            }
        }

        // Prepare transaction data
        const transactionData = {
            cart_items: JSON.stringify(cartItems),
            payment_method: paymentMethod,
            discount_percent: discountPercent,
            vat_percent: vatPercent,
            amount_tendered: amountTendered,
            partial_amount: partialAmount,
            customer_id: selectedCustomerId,
            trade_in_data: tradeInData ? JSON.stringify(tradeInData) : null
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
                    
                    // Reload transaction list
                    loadTransactionList();
                    
                    // Show success modal with receipt option
                    showTransactionSuccess(response.ref, response.grand_total);
                } else {
                    showError(response.msg);
                }
            },
            error: function(xhr, status, error) {
                showError('Error processing transaction: ' + error);
            },
            complete: function() {
                $('#completeTransactionBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Complete Transaction');
            }
        });
    });

    function resetPOS() {
        // Clear search fields
        $('#imeiSearch, #itemCodeSearch').val('');
        $('#standardItemSection').addClass('hidden');
        currentStandardItem = null;
        
        // Reset payment fields
        $('#paymentMethod').val('');
        $('#discountPercent, #vatPercent').val(0);
        $('#amountTendered, #partialAmount').val('');
        $('#changeDue, #creditAmount').text('₨ 0.00');
        $('#fullPaymentSection, #partialPaymentSection').addClass('hidden');
        
        // Reset customer
        selectedCustomerId = null;
        $('#customerSelect').val('');
        $('#customerInfoSection').addClass('hidden');
        
        // Reset trade-in
        tradeInData = null;
        $('#tradeInInfo').addClass('hidden');
        
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
    function showError(message) {
        // Create toast notification
        const toast = $('<div class="alert alert-danger alert-dismissible" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<strong><i class="fa fa-exclamation-circle"></i> Error!</strong> ' + message +
            '</div>');
        
        $('body').append(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            toast.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    function showSuccess(message) {
        // Create toast notification
        const toast = $('<div class="alert alert-success alert-dismissible" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<strong><i class="fa fa-check-circle"></i> Success!</strong> ' + message +
            '</div>');
        
        $('body').append(toast);
        
        // Auto-remove after 3 seconds
        setTimeout(function() {
            toast.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
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

