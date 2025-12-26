<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row">
        <div class="col-sm-12">
            <!-- Header -->
            <div class="row">
                <div class="col-sm-4">
                    <h3><i class="fa fa-line-chart"></i> Daily Profit Report</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label>Date:</label>
                            <input type="date" name="date" class="form-control" value="<?=$date?>" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
                <div class="col-sm-4 text-right">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fa fa-print"></i> Print Report
                    </button>
                </div>
            </div>

            <hr>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-sm-3">
                    <div class="panel panel-primary">
                        <div class="panel-body text-center">
                            <h4>₨<?=number_format($report_data['total_sales'], 2)?></h4>
                            <p>Total Sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-success">
                        <div class="panel-body text-center">
                            <h4>₨<?=number_format($report_data['total_profit'], 2)?></h4>
                            <p>Total Profit</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-info">
                        <div class="panel-body text-center">
                            <h4><?=number_format($report_data['profit_margin'], 2)?>%</h4>
                            <p>Profit Margin</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-warning">
                        <div class="panel-body text-center">
                            <h4><?=$report_data['transaction_count']?></h4>
                            <p>Transactions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-list"></i> Transaction Details</h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Ref</th>
                                    <th>Customer</th>
                                    <th>Staff</th>
                                    <th>Payment</th>
                                    <th class="text-right">Sales</th>
                                    <th class="text-right">Profit</th>
                                    <th class="text-right">Margin %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($report_data['transactions']): ?>
                                    <?php foreach ($report_data['transactions'] as $trans): ?>
                                        <tr>
                                            <td><?=date('h:i A', strtotime($trans->transDate))?></td>
                                            <td><?=$trans->ref?></td>
                                            <td><?=$trans->customer_name ?: 'Walk-in'?></td>
                                            <td><?=$trans->staff_name?></td>
                                            <td>
                                                <?php if ($trans->payment_status === 'paid'): ?>
                                                    <span class="label label-success"><?=ucfirst($trans->modeOfPayment)?></span>
                                                <?php elseif ($trans->payment_status === 'credit'): ?>
                                                    <span class="label label-danger">Credit</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Partial</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">₨<?=number_format($trans->totalMoneySpent, 2)?></td>
                                            <td class="text-right">₨<?=number_format($trans->profit_amount, 2)?></td>
                                            <td class="text-right">
                                                <?php $margin = $trans->totalMoneySpent > 0 ? ($trans->profit_amount / $trans->totalMoneySpent) * 100 : 0; ?>
                                                <?=number_format($margin, 1)?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No transactions found for this date</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-pie-chart"></i> Profit by Category</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-right">Profit</th>
                                        <th class="text-right">Transactions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($report_data['category_data']): ?>
                                        <?php foreach ($report_data['category_data'] as $cat): ?>
                                            <tr>
                                                <td><?=ucfirst($cat->category)?></td>
                                                <td class="text-right">₨<?=number_format($cat->category_profit, 2)?></td>
                                                <td class="text-right"><?=$cat->transaction_count?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-users"></i> Staff Performance</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th class="text-right">Sales</th>
                                        <th class="text-right">Profit</th>
                                        <th class="text-right">Trans</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($report_data['staff_data']): ?>
                                        <?php foreach ($report_data['staff_data'] as $staff): ?>
                                            <tr>
                                                <td><?=$staff->staff_name?></td>
                                                <td class="text-right">₨<?=number_format($staff->total_sales, 0)?></td>
                                                <td class="text-right">₨<?=number_format($staff->total_profit, 0)?></td>
                                                <td class="text-right"><?=$staff->transaction_count?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No data</td>
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
</div>
