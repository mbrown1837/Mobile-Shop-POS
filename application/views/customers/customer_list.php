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
                <th>Balance</th>
                <th>Credit Limit</th>
                <th>Available Credit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?=$sn++?></td>
                        <td><strong><?=htmlspecialchars($customer->name)?></strong></td>
                        <td><?=htmlspecialchars($customer->phone)?></td>
                        <td>
                            <span class="currency <?=$customer->current_balance > 0 ? 'balance-negative' : 'balance-positive'?>">
                                Rs. <?=number_format($customer->current_balance, 2)?>
                            </span>
                        </td>
                        <td><span class="currency">Rs. <?=number_format($customer->credit_limit, 2)?></span></td>
                        <td>
                            <?php $available = $customer->credit_limit - $customer->current_balance; ?>
                            <span class="currency <?=$available > 0 ? 'balance-positive' : 'balance-negative'?>">
                                Rs. <?=number_format($available, 2)?>
                            </span>
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
                                    <i class="fa fa-book"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-success" onclick="openPaymentModal(<?=$customer->id?>, '<?=htmlspecialchars($customer->name)?>', <?=$customer->current_balance?>)" title="Record Payment" <?=$customer->current_balance <= 0 ? 'disabled' : ''?>>
                                    <i class="fa fa-money"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" onclick="openEditModal(<?=$customer->id?>)" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="deleteCustomer(<?=$customer->id?>, '<?=htmlspecialchars($customer->name)?>')" title="Delete" <?=$customer->current_balance > 0 ? 'disabled' : ''?>>
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No customers found</td>
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
