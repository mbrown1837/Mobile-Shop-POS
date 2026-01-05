<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row">
        <div class="col-sm-12">
            <!-- Header -->
            <div class="row">
                <div class="col-sm-4">
                    <h3><i class="fa fa-calendar"></i> Monthly Profit Report</h3>
                </div>
                <div class="col-sm-4 text-center">
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label>Month:</label>
                            <input type="month" name="month" class="form-control" value="<?=$month?>" onchange="this.form.submit()">
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
                            <h4>₨<?=number_format($report_data['average_daily_profit'], 2)?></h4>
                            <p>Avg Daily Profit</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-warning">
                        <div class="panel-body text-center">
                            <h4><?=$report_data['total_transactions']?></h4>
                            <p>Total Transactions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Breakdown -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-bar-chart"></i> Daily Breakdown</h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-right">Sales</th>
                                    <th class="text-right">Profit</th>
                                    <th class="text-right">Margin %</th>
                                    <th class="text-right">Transactions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($report_data['daily_data']): ?>
                                    <?php foreach ($report_data['daily_data'] as $day): ?>
                                        <tr>
                                            <td><?=date('d M Y (D)', strtotime($day->date))?></td>
                                            <td class="text-right">₨<?=number_format($day->daily_sales, 2)?></td>
                                            <td class="text-right">₨<?=number_format($day->daily_profit, 2)?></td>
                                            <td class="text-right">
                                                <?php $margin = $day->daily_sales > 0 ? ($day->daily_profit / $day->daily_sales) * 100 : 0; ?>
                                                <?=number_format($margin, 1)?>%
                                            </td>
                                            <td class="text-right"><?=$day->transaction_count?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No data for this month</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="info">
                                    <th>TOTAL</th>
                                    <th class="text-right">₨<?=number_format($report_data['total_sales'], 2)?></th>
                                    <th class="text-right">₨<?=number_format($report_data['total_profit'], 2)?></th>
                                    <th class="text-right"><?=number_format($report_data['profit_margin'], 1)?>%</th>
                                    <th class="text-right"><?=$report_data['total_transactions']?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category & Staff Performance -->
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
                                        <th class="text-right">Sales</th>
                                        <th class="text-right">Profit</th>
                                        <th class="text-right">Trans</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($report_data['category_data']): ?>
                                        <?php foreach ($report_data['category_data'] as $cat): ?>
                                            <tr>
                                                <td><?=ucfirst($cat->category)?></td>
                                                <td class="text-right">₨<?=number_format($cat->category_sales, 0)?></td>
                                                <td class="text-right">₨<?=number_format($cat->category_profit, 0)?></td>
                                                <td class="text-right"><?=$cat->transaction_count?></td>
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

            <!-- Top Selling Items -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-trophy"></i> Top 10 Profitable Items</h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th class="text-right">Qty Sold</th>
                                    <th class="text-right">Sales</th>
                                    <th class="text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($report_data['top_items']): ?>
                                    <?php $rank = 1; ?>
                                    <?php foreach ($report_data['top_items'] as $item): ?>
                                        <tr>
                                            <td><?=$rank++?></td>
                                            <td><?=$item->itemName?></td>
                                            <td><?=$item->itemCode?></td>
                                            <td class="text-right"><?=$item->total_quantity?></td>
                                            <td class="text-right">₨<?=number_format($item->total_sales, 0)?></td>
                                            <td class="text-right">₨<?=number_format($item->total_profit, 0)?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No data</td>
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
