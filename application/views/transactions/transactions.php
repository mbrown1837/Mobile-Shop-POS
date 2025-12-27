<?php
defined('BASEPATH') OR exit('');
?>

<style>
.pos-container { max-width: 1400px; margin: 0 auto; }
.cart-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
.cart-item:last-child { border-bottom: none; }
.cart-summary { background: #f8f9fa; padding: 15px; border-radius: 5px; }
.imei-badge { background: #17a2b8; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
.customer-balance { font-size: 18px; font-weight: bold; }
.customer-balance.positive { color: #28a745; }
.customer-balance.negative { color: #dc3545; }
</style>

<div class="pos-container hidden-print">
    <div class="row">
        <div class="col-md-8">
            <!-- Search Section -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-search"></i> Search Items</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label><i class="fa fa-search"></i> Search by Name, Code, IMEI, Brand, or Model</label>
                        <input type="text" id="unifiedSearch" class="form-control input-lg" 
                               placeholder="Search by Name, Code, IMEI, Brand, Model..." 
                               autofocus>
                        <span class="help-block text-muted">
                            <i class="fa fa-info-circle"></i> Type to search across all fields. Results will appear below.
                        </span>
                    </div>
                    
                    <!-- Search Results -->
                    <div id="searchResults" class="hidden">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr class="active">
                                        <th>Item</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Available</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="searchResultsBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Standard Item Quantity (shown when item is selected) -->
                    <div id="standardItemSection" class="hidden">
                        <div class="alert alert-success">
                            <strong id="foundItemName"></strong> - <span id="foundItemPrice" class="currency"></span>
                            <br><small>Available: <span id="foundItemQty"></span> | Code: <span id="foundItemCode"></span></small>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" id="standardItemQty" class="form-control" value="1" min="1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="button" id="addStandardItemBtn" class="btn btn-success btn-block">
                                        <i class="fa fa-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-shopping-cart"></i> Cart 
                        <span class="badge" id="cartItemCount">0</span>
                        <button type="button" class="btn btn-danger btn-xs pull-right" id="clearCartBtn">
                            <i class="fa fa-trash"></i> Clear Cart
                        </button>
                    </h4>
                </div>
                <div class="panel-body">
                    <div id="cartItemsContainer">
                        <p class="text-center text-muted">Cart is empty. Scan an IMEI or item code to add items.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Customer Section -->
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-user"></i> Customer
                        <button type="button" class="btn btn-xs btn-primary pull-right" id="quickAddCustomerBtn" style="margin-top: -3px;">
                            <i class="fa fa-plus"></i> New
                        </button>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>Search Customer</label>
                        <select id="customerSelect" class="form-control">
                            <option value="">Walk-in Customer</option>
                        </select>
                    </div>
                    
                    <div id="customerInfoSection" class="hidden">
                        <div class="well well-sm">
                            <p><strong>Name:</strong> <span id="customerName"></span></p>
                            <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
                            <p>
                                <strong>Balance:</strong> 
                                <span id="customerBalance" class="customer-balance">Rs. 0</span>
                            </p>
                            <p><strong>Credit Limit:</strong> <span id="customerCreditLimit">Rs. 0</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-money"></i> Payment</h4>
                </div>
                <div class="panel-body">
                    <div class="cart-summary">
                        <table class="table table-condensed">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-right"><strong class="currency" id="subtotalAmount">Rs. 0.00</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    Discount:
                                    <input type="number" id="discountPercent" class="form-control input-sm" 
                                           value="0" min="0" max="100" step="0.1" style="width: 80px; display: inline;">%
                                </td>
                                <td class="text-right"><strong class="currency" id="discountAmount">Rs. 0.00</strong></td>
                            </tr>
                            <tr>
                                <td>After Discount:</td>
                                <td class="text-right"><strong class="currency" id="afterDiscountAmount">Rs. 0.00</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    VAT:
                                    <input type="number" id="vatPercent" class="form-control input-sm" 
                                           value="0" min="0" max="100" step="0.1" style="width: 80px; display: inline;">%
                                </td>
                                <td class="text-right"><strong class="currency" id="vatAmount">Rs. 0.00</strong></td>
                            </tr>
                            <tr class="success">
                                <td><h4>Grand Total:</h4></td>
                                <td class="text-right"><h4 class="currency-lg" id="grandTotalAmount">Rs. 0.00</h4></td>
                            </tr>
                        </table>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select id="paymentMethod" class="form-control">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="pos">Card/POS</option>
                            <option value="credit">Credit (Khata)</option>
                            <option value="partial">Partial Payment</option>
                        </select>
                    </div>

                    <!-- Cash/POS Payment Fields -->
                    <div id="fullPaymentSection" class="hidden">
                        <div class="form-group">
                            <label>Amount Tendered</label>
                            <input type="number" id="amountTendered" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label>Change Due</label>
                            <div class="form-control" id="changeDue">₨ 0.00</div>
                        </div>
                    </div>

                    <!-- Partial Payment Fields -->
                    <div id="partialPaymentSection" class="hidden">
                        <div class="form-group">
                            <label>Amount Paid Now</label>
                            <input type="number" id="partialAmount" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                            <label>Credit Amount</label>
                            <div class="form-control" id="creditAmount">₨ 0.00</div>
                        </div>
                    </div>

                    <!-- Trade-In Section -->
                    <div class="form-group">
                        <button type="button" class="btn btn-info btn-block" id="addTradeInBtn">
                            <i class="fa fa-exchange"></i> Add Trade-In
                        </button>
                    </div>
                    <div id="tradeInInfo" class="hidden alert alert-info">
                        <strong>Trade-In Value:</strong> <span id="tradeInValue">₨ 0</span>
                        <button type="button" class="btn btn-xs btn-danger pull-right" id="removeTradeInBtn">Remove</button>
                    </div>

                    <hr>

                    <button type="button" class="btn btn-success btn-lg btn-block" id="completeTransactionBtn">
                        <i class="fa fa-check"></i> Complete Transaction
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-history"></i> Recent Transactions
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#reportModal">
                            <i class="fa fa-file-text"></i> Generate Report
                        </button>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3 form-inline form-group-sm">
                            <label for="transListPerPage">Per Page</label>
                            <select id="transListPerPage" class="form-control">
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="col-sm-5 form-group-sm form-inline">
                            <label for="transListSortBy">Sort by</label>
                            <select id="transListSortBy" class="form-control">
                                <option value="transDate-DESC">Date (Latest First)</option>
                                <option value="transDate-ASC">Date (Oldest First)</option>
                                <option value="totalMoneySpent-DESC">Amount (Highest First)</option>
                                <option value="totalMoneySpent-ASC">Amount (Lowest First)</option>
                            </select>
                        </div>
                        <div class="col-sm-4 form-inline form-group-sm">
                            <label><i class="fa fa-search"></i></label>
                            <input type="search" id="transSearch" class="form-control" placeholder="Search Transactions">
                        </div>
                    </div>
                    <hr>
                    <div id="transListTable"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Generate Report</h4>
            </div>
            <div class="modal-body">
                <div class="row" id="datePair">
                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id="transFrom" class="form-control date start" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id="transFromErr"></span>
                    </div>
                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id="transTo" class="form-control date end" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id="transToErr"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="clickToGen">Generate</button>
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('transactions/trade_in_modal'); ?>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Quick Add Customer</h4>
            </div>
            <div class="modal-body">
                <form id="quickAddCustomerForm">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" id="quickCustName" class="form-control" required>
                        <span class="help-block errMsg" id="quickCustNameErr"></span>
                    </div>
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" id="quickCustPhone" class="form-control" required>
                        <span class="help-block errMsg" id="quickCustPhoneErr"></span>
                    </div>
                    <div class="form-group">
                        <label>Credit Limit (Rs.)</label>
                        <input type="number" id="quickCustCreditLimit" class="form-control" value="50000" min="0">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="quickCustAddress" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveQuickCustomerBtn">
                    <i class="fa fa-save"></i> Save & Select
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>public/js/pos.js"></script>
<script src="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.js')?>"></script>
