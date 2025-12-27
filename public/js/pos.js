/**
 * Mobile Shop POS - Point of Sale JavaScript
 * Handles IMEI search, cart management, and transaction processing
 */

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
            
            const balance = parseFloat(customer.balance || 0);
            const creditLimit = parseFloat(customer.credit_limit || 0);
            
            $('#customerBalance').text('₨ ' + balance.toFixed(2))
                .removeClass('positive negative')
                .addClass(balance >= 0 ? 'positive' : 'negative');
            
            $('#customerCreditLimit').text('₨ ' + creditLimit.toFixed(2));
            $('#customerInfoSection').removeClass('hidden');
        });

        // When customer is cleared
        $('#customerSelect').on('select2:clear', function() {
            selectedCustomerId = null;
            $('#customerInfoSection').addClass('hidden');
        });
    }

    // ========================================
    // IMEI Search
    // ========================================
    $('#imeiSearch').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const imei = $(this).val().trim();
            
            if (imei.length !== 15) {
                showError('IMEI must be exactly 15 digits');
                return;
            }

            searchByImei(imei);
        }
    });

    function searchByImei(imei) {
        $.ajax({
            url: baseUrl + 'index.php/transactions/searchByImei',
            type: 'GET',
            data: { imei: imei },
            dataType: 'json',
            beforeSend: function() {
                $('#imeiSearch').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 1) {
                    // Add item to cart automatically
                    addItemToCart(response.item.code, imei);
                    $('#imeiSearch').val(''); // Clear search field
                } else {
                    showError(response.msg);
                }
            },
            error: function() {
                showError('Error searching for IMEI');
            },
            complete: function() {
                $('#imeiSearch').prop('disabled', false).focus();
            }
        });
    }

    // ========================================
    // Item Code Search
    // ========================================
    $('#itemCodeSearch').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const itemCode = $(this).val().trim();
            
            if (!itemCode) {
                showError('Please enter an item code');
                return;
            }

            searchByItemCode(itemCode);
        }
    });

    function searchByItemCode(itemCode) {
        $.ajax({
            url: baseUrl + 'index.php/items/getItemInfo',
            type: 'GET',
            data: { code: itemCode },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    const item = response.item;
                    
                    if (item.item_type === 'serialized') {
                        showError('This is a serialized item. Please scan IMEI instead.');
                        $('#itemCodeSearch').val('');
                    } else {
                        // Show quantity input for standard items
                        currentStandardItem = item;
                        $('#standardItemSection').removeClass('hidden');
                        $('#standardItemQty').val(1).focus();
                    }
                } else {
                    showError(response.msg || 'Item not found');
                }
            },
            error: function() {
                showError('Error searching for item');
            }
        });
    }

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
            url: baseUrl + 'transactions/addToCart',
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
            url: baseUrl + 'transactions/getCart',
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
                            <small>@ ₨ ${parseFloat(item.unitPrice).toFixed(2)}</small>
                        </div>
                        <div class="col-sm-2 text-right">
                            <strong>₨ ${parseFloat(item.totalPrice).toFixed(2)}</strong>
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
        $('#subtotalAmount').text('₨ ' + parseFloat(totals.subtotal).toFixed(2));
        $('#discountAmount').text('₨ ' + parseFloat(totals.discount_amount).toFixed(2));
        $('#afterDiscountAmount').text('₨ ' + parseFloat(totals.after_discount).toFixed(2));
        $('#vatAmount').text('₨ ' + parseFloat(totals.vat_amount).toFixed(2));
        $('#grandTotalAmount').text('₨ ' + parseFloat(totals.grand_total).toFixed(2));
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
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('₨ ', '').replace(',', '')) || 0;
        const change = tendered - grandTotal;
        
        $('#changeDue').text('₨ ' + change.toFixed(2));
    });

    // Calculate credit amount for partial payment
    $('#partialAmount').on('input', function() {
        const paid = parseFloat($(this).val()) || 0;
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('₨ ', '').replace(',', '')) || 0;
        const credit = grandTotal - paid;
        
        $('#creditAmount').text('₨ ' + credit.toFixed(2));
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
        const paymentMethod = $('#paymentMethod').val();
        
        if (!paymentMethod) {
            showError('Please select a payment method');
            return;
        }

        const discountPercent = parseFloat($('#discountPercent').val()) || 0;
        const vatPercent = parseFloat($('#vatPercent').val()) || 0;
        const grandTotal = parseFloat($('#grandTotalAmount').text().replace('₨ ', '').replace(',', '')) || 0;

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

        // Confirm transaction
        if (!confirm('Complete this transaction?')) {
            return;
        }

        // Prepare transaction data
        const transactionData = {
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
            url: baseUrl + 'transactions/processTransaction',
            type: 'POST',
            data: transactionData,
            dataType: 'json',
            beforeSend: function() {
                $('#completeTransactionBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },
            success: function(response) {
                if (response.status === 1) {
                    showSuccess('Transaction completed successfully! Ref: ' + response.ref);
                    
                    // Reset form
                    resetPOS();
                    
                    // Reload transaction list
                    loadTransactionList();
                    
                    // TODO: Print receipt
                    alert('Transaction Ref: ' + response.ref + '\nTotal: ₨' + response.grand_total + '\nProfit: ₨' + response.profit);
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
        // Using simple alert for now, can be replaced with better notification system
        alert('Error: ' + message);
    }

    function showSuccess(message) {
        // Using simple alert for now, can be replaced with better notification system
        console.log('Success: ' + message);
    }

    // Auto-refresh cart on page load
    refreshCart();
});
