<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center"><?=$title?></h3>
            <hr>
            
            <!-- Filter Options -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label>Report Type:</label>
                            <select name="type" class="form-control" onchange="this.form.submit()">
                                <option value="daily" <?=$type === 'daily' ? 'selected' : ''?>>Daily</option>
                                <option value="monthly" <?=$type === 'monthly' ? 'selected' : ''?>>Monthly</option>
                                <option value="itemwise" <?=$type === 'itemwise' ? 'selected' : ''?>>Item-wise</option>
                            </select>
                        </div>
                        
                        <?php if ($type === 'daily'): ?>
                        <div class="form-group">
                            <label>Date:</label>
                            <input type="date" name="date" value="<?=$date?>" class="form-control" onchange="this.form.submit()">
                        </div>
                        <?php else: ?>
                        <div class="form-group">
                            <label>Month:</label>
                            <input type="month" name="month" value="<?=$month?>" class="form-control" onchange="this.form.submit()">
                        </div>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <?php if (isset($report['summary'])): ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-primary">
                        <div class="panel-body text-center">
                            <h4>Total Sales</h4>
                            <h2 class="text-primary">Rs. <?=number_format($report['summary']->total_sales ?? 0, 2)?></h2>
                            <p><?=$report['summary']->total_transactions ?? 0?> Transactions</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-success">
                        <div class="panel-body text-center">
                            <h4>Total Profit</h4>
                            <h2 class="text-success">Rs. <?=number_format($report['summary']->total_profit ?? 0, 2)?></h2>
                            <?php 
                            $margin = $report['summary']->total_sales > 0 ? 
                                      ($report['summary']->total_profit / $report['summary']->total_sales * 100) : 0;
                            ?>
                            <p><?=number_format($margin, 1)?>% Margin</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-body text-center">
                            <h4>Average Sale</h4>
                            <?php 
                            $avg = $report['summary']->total_transactions > 0 ? 
                                   ($report['summary']->total_sales / $report['summary']->total_transactions) : 0;
                            ?>
                            <h2 class="text-info">Rs. <?=number_format($avg, 2)?></h2>
                            <p>Per Transaction</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Payment Methods Breakdown -->
            <?php if (isset($report['payment_methods']) && !empty($report['payment_methods'])): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Payment Methods</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Transactions</th>
                                <th>Amount</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = $report['summary']->total_sales;
                            foreach ($report['payment_methods'] as $pm): 
                            ?>
                            <tr>
                                <td>
                                    <?php if ($pm->modeOfPayment === 'cash'): ?>
                                        <span class="label label-success">💵 Cash</span>
                                    <?php else: ?>
                                        <span class="label label-warning">📒 Credit</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$pm->count?></td>
                                <td>Rs. <?=number_format($pm->amount, 2)?></td>
                                <td><?=number_format(($pm->amount / $total * 100), 1)?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Top Selling Items -->
            <?php if (isset($report['top_items']) && !empty($report['top_items'])): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Top Selling Items</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Times Sold</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($report['top_items'] as $item): ?>
                            <tr>
                                <td><?=$rank++?></td>
                                <td><?=htmlspecialchars($item->name)?></td>
                                <td><?=$item->code?></td>
                                <td><?=$item->times_sold?></td>
                                <td><?=$item->total_qty?></td>
                                <td>Rs. <?=number_format($item->total_amount, 2)?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Item-wise Sales -->
            <?php if (isset($report['items']) && !empty($report['items'])): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Item-wise Sales Analysis</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Times Sold</th>
                                <th>Quantity</th>
                                <th>Total Sales</th>
                                <th>Total Profit</th>
                                <th>Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['items'] as $item): ?>
                            <tr>
                                <td>
                                    <strong><?=htmlspecialchars($item->name)?></strong>
                                    <br><small class="text-muted"><?=$item->code?></small>
                                </td>
                                <td>
                                    <?php if ($item->category === 'mobile'): ?>
                                        <span class="label label-primary">Mobile</span>
                                    <?php else: ?>
                                        <span class="label label-success">Accessory</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$item->times_sold?></td>
                                <td><?=$item->total_quantity?></td>
                                <td>Rs. <?=number_format($item->total_sales, 2)?></td>
                                <td class="text-success">Rs. <?=number_format($item->total_profit, 2)?></td>
                                <td>
                                    <?php 
                                    $margin = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales * 100) : 0;
                                    ?>
                                    <?=number_format($margin, 1)?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Daily Breakdown for Monthly Report -->
            <?php if (isset($report['daily_breakdown']) && !empty($report['daily_breakdown'])): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Daily Breakdown</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transactions</th>
                                <th>Sales</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['daily_breakdown'] as $day): ?>
                            <tr>
                                <td><?=date('d M Y', strtotime($day->date))?></td>
                                <td><?=$day->transactions?></td>
                                <td>Rs. <?=number_format($day->sales, 2)?></td>
                                <td class="text-success">Rs. <?=number_format($day->profit, 2)?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Category Breakdown -->
            <?php if (isset($report['category_breakdown']) && !empty($report['category_breakdown'])): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Category Breakdown</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Transactions</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['category_breakdown'] as $cat): ?>
                            <tr>
                                <td>
                                    <?php if ($cat->category === 'mobile'): ?>
                                        <span class="label label-primary">📱 Mobiles</span>
                                    <?php else: ?>
                                        <span class="label label-success">🔌 Accessories</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$cat->transactions?></td>
                                <td>Rs. <?=number_format($cat->sales, 2)?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
