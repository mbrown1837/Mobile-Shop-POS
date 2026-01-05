<?php
defined('BASEPATH') OR exit('');
?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Current Balance (Khata)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?=$sn++?></td>
                        <td>
                            <strong><?=htmlspecialchars($customer->name)?></strong>
                            <?php if (!empty($customer->cnic)): ?>
                                <br><small class="text-muted">CNIC: <?=htmlspecialchars($customer->cnic)?></small>
                            <?php endif; ?>
                        </td>
                        <td><?=htmlspecialchars($customer->phone)?></td>
                        <td>
                            <?php if ($customer->current_balance > 0): ?>
                                <span class="text-danger"><strong>Rs. <?=number_format($customer->current_balance, 2)?></strong></span>
                                <br><small class="text-muted">Udhar</small>
                            <?php elseif ($customer->current_balance < 0): ?>
                                <span class="text-success"><strong>Rs. <?=number_format(abs($customer->current_balance), 2)?></strong></span>
                                <br><small class="text-muted">Advance</small>
                            <?php else: ?>
                                <span class="text-muted">Rs. 0.00</span>
                                <br><small class="text-muted">Clear</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($customer->status === 'active'): ?>
                                <span class="label label-success">Active</span>
                            <?php elseif ($customer->status === 'blocked'): ?>
                                <span class="label label-danger">Blocked</span>
                            <?php else: ?>
                                <span class="label label-default">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-info" onclick="viewLedger(<?=$customer->id?>)" title="View Ledger">
                                    <i class="fa fa-book"></i> Ledger
                                </button>
                                <?php if ($customer->current_balance > 0): ?>
                                <button type="button" class="btn btn-xs btn-success" onclick="openPaymentModal(<?=$customer->id?>, '<?=htmlspecialchars($customer->name)?>', <?=$customer->current_balance?>)" title="Record Payment">
                                    <i class="fa fa-money"></i> Payment
                                </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-xs btn-warning" onclick="openEditModal(<?=$customer->id?>)" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="deleteCustomer(<?=$customer->id?>, '<?=htmlspecialchars($customer->name)?>')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No customers found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="row">
    <div class="col-sm-6">
        <p><?=$range?></p>
    </div>
    <div class="col-sm-6 text-right">
        <?=$links?>
    </div>
</div>
