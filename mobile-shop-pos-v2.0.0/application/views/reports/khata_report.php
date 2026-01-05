<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">📒 Khata/Credit Report</h3>
            <p class="text-center text-muted">Outstanding customer balances</p>
            <hr>
            
            <div class="text-right hidden-print">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa fa-print"></i> Print Report
                </button>
                <a href="<?=site_url('reports')?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Reports
                </a>
            </div>
            <br>

            <!-- Summary -->
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-danger">
                        <div class="panel-body text-center">
                            <h4>Total Outstanding</h4>
                            <h2 class="text-danger">Rs. <?=number_format($summary->total_outstanding ?? 0, 2)?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-warning">
                        <div class="panel-body text-center">
                            <h4>Customers with Balance</h4>
                            <h2 class="text-warning"><?=$summary->total_customers ?? 0?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer List -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Customers with Outstanding Balance</h4>
                </div>
                <div class="panel-body">
                    <?php if ($customers && count($customers) > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>CNIC</th>
                                <th>Outstanding Balance</th>
                                <th>Status</th>
                                <th class="hidden-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn = 1; foreach ($customers as $customer): ?>
                            <tr>
                                <td><?=$sn++?></td>
                                <td><strong><?=htmlspecialchars($customer->name)?></strong></td>
                                <td><?=htmlspecialchars($customer->phone)?></td>
                                <td><?=htmlspecialchars($customer->cnic ?? '-')?></td>
                                <td class="text-danger"><strong>Rs. <?=number_format($customer->current_balance, 2)?></strong></td>
                                <td>
                                    <?php if ($customer->status === 'active'): ?>
                                        <span class="label label-success">Active</span>
                                    <?php elseif ($customer->status === 'blocked'): ?>
                                        <span class="label label-danger">Blocked</span>
                                    <?php else: ?>
                                        <span class="label label-default">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-print">
                                    <a href="<?=site_url('customers/viewLedger/' . $customer->id)?>" class="btn btn-xs btn-info">
                                        <i class="fa fa-book"></i> View Ledger
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <th colspan="4" class="text-right">TOTAL:</th>
                                <th class="text-danger">Rs. <?=number_format($summary->total_outstanding ?? 0, 2)?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php else: ?>
                    <div class="alert alert-success text-center">
                        <i class="fa fa-check-circle fa-3x"></i>
                        <h4>No Outstanding Balances!</h4>
                        <p>All customers have cleared their khata.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style media="print">
    .hidden-print { display: none !important; }
    .panel { page-break-inside: avoid; }
</style>
