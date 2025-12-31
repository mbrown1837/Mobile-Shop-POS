<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <!-- Header -->
            <div class="row">
                <div class="col-sm-4">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCustomerModal">
                        <i class="fa fa-plus"></i> Add Customer
                    </button>
                </div>
                <div class="col-sm-4 text-center">
                    <h4>Customer Management</h4>
                </div>
                <div class="col-sm-4 text-right">
                    <button class="btn btn-info btn-sm" onclick="window.location.href='<?=base_url('customers/viewLedger')?>'">
                        <i class="fa fa-book"></i> View All Ledgers
                    </button>
                </div>
            </div>

            <br>

            <!-- Filters and Search -->
            <div class="row">
                <div class="col-sm-3 form-inline form-group-sm">
                    <label for="customerListPerPage">Per Page</label>
                    <select id="customerListPerPage" class="form-control">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="col-sm-5 form-group-sm form-inline">
                    <label for="customerListSortBy">Sort by</label>
                    <select id="customerListSortBy" class="form-control">
                        <option value="name-ASC">Name (A-Z)</option>
                        <option value="name-DESC">Name (Z-A)</option>
                        <option value="balance-DESC">Balance (Highest First)</option>
                        <option value="balance-ASC">Balance (Lowest First)</option>
                        <option value="created_at-DESC">Date Added (Latest First)</option>
                        <option value="created_at-ASC">Date Added (Oldest First)</option>
                    </select>
                </div>

                <div class="col-sm-4 form-inline form-group-sm">
                    <label><i class="fa fa-search"></i></label>
                    <input type="search" id="customerSearch" class="form-control" placeholder="Search customers">
                </div>
            </div>

            <hr>

            <!-- Customer List Table -->
            <div class="row">
                <div class="col-sm-12" id="customerListTable">
                    <p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading customers...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-user-plus"></i> Add New Customer</h4>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="form-group">
                        <label for="customerName">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customerName" name="customerName" required>
                        <span class="help-block text-danger" id="customerNameErr"></span>
                    </div>

                    <div class="form-group">
                        <label for="customerPhone">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="customerPhone" name="customerPhone" required placeholder="03XX-XXXXXXX">
                        <span class="help-block text-danger" id="customerPhoneErr"></span>
                    </div>

                    <div class="form-group">
                        <label for="customerAddress">Address</label>
                        <textarea class="form-control" id="customerAddress" name="customerAddress" rows="2" placeholder="Shop address or area"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="customerCnic">CNIC</label>
                        <input type="text" class="form-control" id="customerCnic" name="customerCnic" placeholder="xxxxx-xxxxxxx-x">
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="creditEnabled" name="creditEnabled" value="1">
                                <strong>Enable Credit/Khata for this customer</strong>
                            </label>
                        </div>
                        <small class="text-muted">Only enable for trusted customers</small>
                    </div>

                    <div class="form-group" id="creditLimitGroup" style="display:none;">
                        <label for="creditLimit">Credit Limit (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="creditLimit" name="creditLimit" value="0" min="0" step="1000">
                        <span class="help-block text-muted">Maximum amount customer can owe</span>
                        <span class="help-block text-danger" id="creditLimitErr"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveCustomerBtn">
                    <i class="fa fa-save"></i> Save Customer
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-edit"></i> Edit Customer</h4>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm">
                    <input type="hidden" id="editCustomerId" name="customerId">
                    
                    <div class="form-group">
                        <label for="editCustomerName">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCustomerName" name="customerName" required>
                        <span class="help-block text-danger" id="editCustomerNameErr"></span>
                    </div>

                    <div class="form-group">
                        <label for="editCustomerPhone">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="editCustomerPhone" name="customerPhone" required>
                        <span class="help-block text-danger" id="editCustomerPhoneErr"></span>
                    </div>

                    <div class="form-group">
                        <label for="editCustomerEmail">Email</label>
                        <input type="email" class="form-control" id="editCustomerEmail" name="customerEmail">
                    </div>

                    <div class="form-group">
                        <label for="editCustomerAddress">Address</label>
                        <textarea class="form-control" id="editCustomerAddress" name="customerAddress" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="editCustomerCnic">CNIC</label>
                        <input type="text" class="form-control" id="editCustomerCnic" name="customerCnic">
                    </div>

                    <div class="form-group">
                        <label for="editCreditLimit">Credit Limit (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editCreditLimit" name="creditLimit" min="0" step="1000" required>
                    </div>

                    <div class="form-group">
                        <label for="editCustomerStatus">Status</label>
                        <select class="form-control" id="editCustomerStatus" name="customerStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="updateCustomerBtn">
                    <i class="fa fa-save"></i> Update Customer
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-money"></i> Record Payment</h4>
            </div>
            <div class="modal-body">
                <form id="recordPaymentForm">
                    <input type="hidden" id="paymentCustomerId" name="customerId">
                    
                    <div class="alert alert-info">
                        <strong>Customer:</strong> <span id="paymentCustomerName"></span><br>
                        <strong>Current Balance:</strong> ₨<span id="paymentCustomerBalance">0</span>
                    </div>

                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="paymentAmount" name="amount" min="0" step="100" required>
                        <span class="help-block text-danger" id="paymentAmountErr"></span>
                    </div>

                    <div class="form-group">
                        <label for="paymentNotes">Notes</label>
                        <textarea class="form-control" id="paymentNotes" name="notes" rows="3" placeholder="Optional payment notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="savePaymentBtn">
                    <i class="fa fa-check"></i> Record Payment
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>public/js/main.js"></script>
<script src="<?=base_url()?>public/js/customers.js"></script>
