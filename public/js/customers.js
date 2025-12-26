/**
 * Customers Management JavaScript
 * Handles customer CRUD operations and payment recording
 */

$(document).ready(function() {
    // Load customers on page load
    if ($('#customerListTable').length) {
        loadCustomers();
    }

    // ========================================
    // Load Customers
    // ========================================
    function loadCustomers(url) {
        url = url || baseUrl + 'customers/loadCustomers';

        const limit = $('#customerListPerPage').val() || 10;
        const sortBy = $('#customerListSortBy').val() || 'name-ASC';
        const [orderBy, orderFormat] = sortBy.split('-');

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                limit: limit,
                orderBy: orderBy,
                orderFormat: orderFormat
            },
            dataType: 'json',
            success: function(response) {
                if (response.customerTable) {
                    $('#customerListTable').html(response.customerTable);
                }
            },
            error: function() {
                $('#customerListTable').html('<p class="text-center text-danger">Error loading customers</p>');
            }
        });

        return false;
    }

    // Make loadCustomers globally accessible
    window.loadCustomers = loadCustomers;

    $('#customerListPerPage, #customerListSortBy').on('change', function() {
        loadCustomers();
    });

    // ========================================
    // Add Customer
    // ========================================
    $('#saveCustomerBtn').on('click', function() {
        const formData = $('#addCustomerForm').serialize();

        $.ajax({
            url: baseUrl + 'customers/add',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#saveCustomerBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                $('.help-block').text('');
            },
            success: function(response) {
                if (response.status === 1) {
                    alert('Success: ' + response.msg);
                    $('#addCustomerModal').modal('hide');
                    $('#addCustomerForm')[0].reset();
                    loadCustomers();
                } else {
                    alert('Error: ' + response.msg);
                    // Display field errors
                    if (response.customerName) $('#customerNameErr').text(response.customerName);
                    if (response.customerPhone) $('#customerPhoneErr').text(response.customerPhone);
                    if (response.customerEmail) $('#customerEmailErr').text(response.customerEmail);
                    if (response.creditLimit) $('#creditLimitErr').text(response.creditLimit);
                }
            },
            error: function() {
                alert('Error: Failed to add customer');
            },
            complete: function() {
                $('#saveCustomerBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Customer');
            }
        });
    });

    // ========================================
    // Edit Customer
    // ========================================
    window.openEditModal = function(customerId) {
        $.ajax({
            url: baseUrl + 'customers/getCustomerInfo',
            type: 'GET',
            data: { id: customerId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    const customer = response.customer;
                    $('#editCustomerId').val(customer.id);
                    $('#editCustomerName').val(customer.name);
                    $('#editCustomerPhone').val(customer.phone);
                    $('#editCustomerEmail').val(customer.email);
                    $('#editCustomerAddress').val(customer.address);
                    $('#editCustomerCnic').val(customer.cnic);
                    $('#editCreditLimit').val(customer.credit_limit);
                    $('#editCustomerStatus').val(customer.status);
                    $('#editCustomerModal').modal('show');
                } else {
                    alert('Error: ' + response.msg);
                }
            },
            error: function() {
                alert('Error: Failed to load customer details');
            }
        });
    };

    $('#updateCustomerBtn').on('click', function() {
        const formData = $('#editCustomerForm').serialize();

        $.ajax({
            url: baseUrl + 'customers/edit',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#updateCustomerBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
            },
            success: function(response) {
                if (response.status === 1) {
                    alert('Success: ' + response.msg);
                    $('#editCustomerModal').modal('hide');
                    loadCustomers();
                } else {
                    alert('Error: ' + response.msg);
                }
            },
            error: function() {
                alert('Error: Failed to update customer');
            },
            complete: function() {
                $('#updateCustomerBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Update Customer');
            }
        });
    });

    // ========================================
    // Delete Customer
    // ========================================
    window.deleteCustomer = function(customerId, customerName) {
        if (!confirm('Are you sure you want to delete customer "' + customerName + '"?')) {
            return;
        }

        $.ajax({
            url: baseUrl + 'customers/delete',
            type: 'POST',
            data: { customerId: customerId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    alert('Success: ' + response.msg);
                    loadCustomers();
                } else {
                    alert('Error: ' + response.msg);
                }
            },
            error: function() {
                alert('Error: Failed to delete customer');
            }
        });
    };

    // ========================================
    // Record Payment
    // ========================================
    window.openPaymentModal = function(customerId, customerName, balance) {
        $('#paymentCustomerId').val(customerId);
        $('#paymentCustomerName').text(customerName);
        $('#paymentCustomerBalance').text(balance.toFixed(2));
        $('#paymentAmount').attr('max', balance).val('');
        $('#paymentNotes').val('');
        $('#recordPaymentModal').modal('show');
    };

    $('#savePaymentBtn').on('click', function() {
        const formData = $('#recordPaymentForm').serialize();

        $.ajax({
            url: baseUrl + 'customers/recordPayment',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#savePaymentBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                $('#paymentAmountErr').text('');
            },
            success: function(response) {
                if (response.status === 1) {
                    alert('Success: ' + response.msg);
                    $('#recordPaymentModal').modal('hide');
                    
                    // Reload page if on ledger view, otherwise reload customer list
                    if (window.location.href.indexOf('viewLedger') > -1) {
                        location.reload();
                    } else {
                        loadCustomers();
                    }
                } else {
                    alert('Error: ' + response.msg);
                    if (response.amount) $('#paymentAmountErr').text(response.amount);
                }
            },
            error: function() {
                alert('Error: Failed to record payment');
            },
            complete: function() {
                $('#savePaymentBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Record Payment');
            }
        });
    });

    // ========================================
    // View Ledger
    // ========================================
    window.viewLedger = function(customerId) {
        window.location.href = baseUrl + 'customers/viewLedger/' + customerId;
    };

    // ========================================
    // View Transaction
    // ========================================
    window.viewTransaction = function(ref) {
        // TODO: Implement transaction view modal
        alert('Transaction Ref: ' + ref);
    };

    // ========================================
    // Customer Search
    // ========================================
    $('#customerSearch').on('keyup', function() {
        const query = $(this).val().trim();

        if (query.length < 2) {
            loadCustomers();
            return;
        }

        $.ajax({
            url: baseUrl + 'customers/search',
            type: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1 && response.results) {
                    displaySearchResults(response.results);
                }
            }
        });
    });

    function displaySearchResults(results) {
        if (results.length === 0) {
            $('#customerListTable').html('<p class="text-center">No customers found</p>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-striped table-hover"><thead><tr>';
        html += '<th>Name</th><th>Phone</th><th>Balance</th><th>Credit Limit</th><th>Status</th><th>Actions</th>';
        html += '</tr></thead><tbody>';

        results.forEach(function(customer) {
            const available = customer.credit_limit - customer.current_balance;
            html += '<tr>';
            html += '<td><strong>' + customer.name + '</strong></td>';
            html += '<td>' + customer.phone + '</td>';
            html += '<td><span class="' + (customer.current_balance > 0 ? 'text-danger' : 'text-success') + '">₨' + parseFloat(customer.current_balance).toFixed(2) + '</span></td>';
            html += '<td>₨' + parseFloat(customer.credit_limit).toFixed(2) + '</td>';
            html += '<td><span class="label label-' + (customer.status === 'active' ? 'success' : 'default') + '">' + customer.status + '</span></td>';
            html += '<td><div class="btn-group">';
            html += '<button class="btn btn-xs btn-info" onclick="viewLedger(' + customer.id + ')"><i class="fa fa-book"></i></button>';
            html += '<button class="btn btn-xs btn-success" onclick="openPaymentModal(' + customer.id + ', \'' + customer.name + '\', ' + customer.current_balance + ')" ' + (customer.current_balance <= 0 ? 'disabled' : '') + '><i class="fa fa-money"></i></button>';
            html += '<button class="btn btn-xs btn-warning" onclick="openEditModal(' + customer.id + ')"><i class="fa fa-edit"></i></button>';
            html += '</div></td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#customerListTable').html(html);
    }
});
