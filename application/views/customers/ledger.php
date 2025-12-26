<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row">
        <div class="col-sm-12">
            <!-- Customer Info Card -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-user"></i> <?=htmlspecialchars($customer->name)?>
                        <button class="btn btn-sm btn-default pull-right" onclick="window.location.href='<?=base_url('customers')?>'">
                            <i class="fa fa-arrow-left"></i> Back to Customers
                        </button>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <p><strong>Phone:</strong> <?=htmlspecialchars($customer->phone)?></p>
                            <?php if ($customer->email): ?>
                                <p><strong>Email:</strong> <?=htmlspecialchars($customer->email)?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-3">
                            <?php if ($customer->address): ?>
                                <p><strong>Address:</strong> <?=htmlspecialchars($customer->address)?></p>
                            <?php endif; ?>
                            <?php if ($customer->cnic): ?>
                                <p><strong>CNIC:</strong> <?=htmlspecialchars($customer->cnic)?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-3">
                            <p><strong>Current Balance:</strong> 
                                <span class="<?=$customer->current_balance > 0 ? 'text-danger' : 'text-success'?>" style="font-size: 18px;">
                                    <strong>₨<?=number_format($customer->current_balance, 2)?></strong>
                                </span>
                            </p>
                            <p><strong>Credit Limit:</strong> ₨<?=number_format($customer->credit_limit, 2)?></p>
                        </div>
                        <div class="col-sm-3">
                            <?php $available = $customer->credit_limit - $customer->current_balance; ?>
                            <p><strong>Available Credit:</strong> 
                                <span class="<?=$available > 0 ? 'text-success' : 'text-danger'?>">
                                    ₨<?=number_format($available, 2)?>
                                </span>
                            </p>
                            <?php if ($customer->current_balance > 0): ?>
                                <button class="btn btn-success btn-sm" onclick="openPaymentModal(<?=$customer->id?>, '<?=htmlspecialchars($customer->name)?>', <?=$customer->current_balance?>)">
                                    <i class="fa fa-money"></i> Record Payment
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <?php if (isset($stats)): ?>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h4>₨<?=number_format($stats['total_credit_sales'], 2)?></h4>
                                <p>Total Credit Sales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h4>₨<?=number_format($stats['total_payments'], 2)?></h4>
                                <p>Total Payments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h4><?=$stats['transaction_count']?></h4>
                                <p>Total Transactions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4>₨<?=number_format($stats['available_credit'], 2)?></h4>
                                <p>Available Credit</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Ledger Table -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-list"></i> Transaction Ledger</h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Transaction Ref</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($ledger): ?>
                                    <?php foreach ($ledger as $entry): ?>
                                        <tr>
                                            <td><?=date('d M Y, h:i A', strtotime($entry->created_at))?></td>
                                            <td>
                                                <?php if ($entry->transaction_type === 'sale'): ?>
                                                    <span class="label label-danger">Credit Sale</span>
                                                <?php elseif ($entry->transaction_type === 'payment'): ?>
                                                    <span class="label label-success">Payment</span>
                                                <?php else: ?>
                                                    <span class="label label-default"><?=ucfirst($entry->transaction_type)?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($entry->transaction_ref): ?>
                                                    <a href="javascript:void(0)" onclick="viewTransaction('<?=$entry->transaction_ref?>')">
                                                        <?=$entry->transaction_ref?>
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="<?=$entry->transaction_type === 'payment' ? 'text-success' : 'text-danger'?>">
                                                    <?=$entry->transaction_type === 'payment' ? '-' : '+'?>₨<?=number_format($entry->amount, 2)?>
                                                </span>
                                            </td>
                                            <td>₨<?=number_format($entry->balance_after, 2)?></td>
                                            <td><?=htmlspecialchars($entry->notes)?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No ledger entries found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
                    <input type="hidden" id="paymentCustomerId" name="customerId" value="<?=$customer->id?>">
                    
                    <div class="alert alert-info">
                        <strong>Customer:</strong> <?=htmlspecialchars($customer->name)?><br>
                        <strong>Current Balance:</strong> ₨<span id="paymentCustomerBalance"><?=number_format($customer->current_balance, 2)?></span>
                    </div>

                    <div class="form-group">
                        <label for="paymentAmount">Payment Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="paymentAmount" name="amount" min="0" step="100" max="<?=$customer->current_balance?>" required>
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

<script src="<?=base_url()?>public/js/customers.js"></script>
