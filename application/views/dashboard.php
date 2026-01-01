<?php
defined('BASEPATH') OR exit('');
?>

<div class="row latestStuffs">
    <div class="col-sm-3 col-xs-6">
        <div class="panel panel-success">
            <div class="panel-body latestStuffsBody" style="background-color: #5cb85c; min-height: 100px; padding: 15px;">
                <div class="clearfix">
                    <div class="pull-left"><i class="fa fa-money fa-3x"></i></div>
                    <div class="pull-right text-right" style="max-width: 60%;">
                        <div style="font-size: 18px; font-weight: bold; word-wrap: break-word;">Rs. <?=number_format($totalEarnedToday ?? 0, 0)?></div>
                        <div style="font-size: 12px;">Today's Sales</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#5cb85c; font-size: 11px;">
                <i class="fa fa-shopping-cart"></i> <?=$totalSalesToday ?? 0?> items sold
            </div>
        </div>
    </div>
    
    <div class="col-sm-3 col-xs-6">
        <div class="panel panel-primary">
            <div class="panel-body latestStuffsBody" style="background-color: #337ab7; min-height: 100px; padding: 15px;">
                <div class="clearfix">
                    <div class="pull-left"><i class="fa fa-line-chart fa-3x"></i></div>
                    <div class="pull-right text-right" style="max-width: 60%;">
                        <div style="font-size: 18px; font-weight: bold; word-wrap: break-word;">Rs. <?=number_format($totalProfitToday ?? 0, 0)?></div>
                        <div style="font-size: 12px;">Today's Profit</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#337ab7; font-size: 11px;">
                <i class="fa fa-percent"></i> Profit earned today
            </div>
        </div>
    </div>
    
    <div class="col-sm-3 col-xs-6">
        <div class="panel panel-warning">
            <div class="panel-body latestStuffsBody" style="background-color: #f0ad4e; min-height: 100px; padding: 15px;">
                <div class="clearfix">
                    <div class="pull-left"><i class="fa fa-book fa-3x"></i></div>
                    <div class="pull-right text-right" style="max-width: 60%;">
                        <div style="font-size: 18px; font-weight: bold; word-wrap: break-word;">Rs. <?=number_format($totalKhataOutstanding ?? 0, 0)?></div>
                        <div style="font-size: 12px;">Outstanding</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#f0ad4e; font-size: 11px;">
                <i class="fa fa-users"></i> <?=$customersWithBalance ?? 0?> customers
            </div>
        </div>
    </div>
    
    <div class="col-sm-3 col-xs-6">
        <div class="panel panel-info">
            <div class="panel-body latestStuffsBody" style="background-color: #5bc0de; min-height: 100px; padding: 15px;">
                <div class="clearfix">
                    <div class="pull-left"><i class="fa fa-cubes fa-3x"></i></div>
                    <div class="pull-right text-right" style="max-width: 60%;">
                        <div style="font-size: 18px; font-weight: bold; word-wrap: break-word;"><?=$totalItems ?? 0?></div>
                        <div style="font-size: 12px;">Items in Stock</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-center" style="color:#5bc0de; font-size: 11px;">
                <i class="fa fa-exclamation-triangle"></i> <?=$lowStockItems ?? 0?> low stock
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-bolt"></i> Quick Actions</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <a href="<?=site_url('transactions')?>" class="btn btn-success btn-lg btn-block">
                            <i class="fa fa-shopping-cart"></i><br>New Sale
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <a href="<?=site_url('reports_enhanced/salesSummary?type=daily')?>" class="btn btn-primary btn-lg btn-block">
                            <i class="fa fa-bar-chart"></i><br>Daily Report
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <a href="<?=site_url('reports_enhanced/khataReport')?>" class="btn btn-warning btn-lg btn-block">
                            <i class="fa fa-book"></i><br>Khata Report
                        </a>
                    </div>
                    <div class="col-sm-3">
                        <a href="<?=site_url('items')?>" class="btn btn-info btn-lg btn-block">
                            <i class="fa fa-cube"></i><br>Manage Items
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ROW OF GRAPH/CHART OF EARNINGS PER MONTH/YEAR-->
<div class="row margin-top-5">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header" style="background-color:/*#33c9dd*/#333;">
              <h3 class="box-title" id="earningsTitle"></h3>
              <div class="pull-right">
                <label class="control-label" style="color: #fff; margin-right: 10px;">Select Year:</label>
                <select class="form-control" id="earningAndExpenseYear" style="display: inline-block; width: auto;">
                    <?php $years = range("2016", date('Y')); ?>
                    <?php foreach($years as $y):?>
                    <option value="<?=$y?>" <?=$y == date('Y') ? 'selected' : ''?>><?=$y?></option>
                    <?php endforeach; ?>
                </select>
                <span id="yearAccountLoading"></span>
              </div>
            </div>

            <div class="box-body" style="background-color:/*#33c9dd*/#333;">
              <canvas style="padding-right:25px" id="earningsGraph" width="200" height="80"/></canvas>
            </div>
        </div>
    </div>
</div>
<!-- END OF ROW OF GRAPH/CHART OF EARNINGS PER MONTH/YEAR-->

<!-- ROW OF SUMMARY -->
<div class="row margin-top-5">
    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-cart-plus"></i> HIGH IN DEMAND</div>
            <?php if($topDemanded): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty Sold</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($topDemanded as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td><?=$get->totSold?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-cart-arrow-down"></i> LOW IN DEMAND</div>
            <?php if($leastDemanded): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty Sold</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($leastDemanded as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td><?=$get->totSold?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-money"></i> HIGHEST EARNING</div>
            <?php if($highestEarners): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Total Earned</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($highestEarners as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td>Rs. <?=number_format($get->totEarned, 2)?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?> 
        </div>
    </div>
    
    <div class="col-sm-3">
        <div class="panel panel-hash">
            <div class="panel-heading"><i class="fa fa-money"></i> LOWEST EARNING</div>
            <?php if($lowestEarners): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Total Earned</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($lowestEarners as $get):?>
                    <tr>
                        <td><?=$get->name?></td>
                        <td>Rs. <?=number_format($get->totEarned, 2)?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            No Transactions
            <?php endif; ?> 
        </div>
    </div>
</div>
<!-- END OF ROW OF SUMMARY -->

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Daily Transactions</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($dailyTransactions) && $dailyTransactions): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($dailyTransactions as $get): ?>
                        <tr>
                            <td><?=
                                    date('l jS M, Y', strtotime($get->transactionDate)) === date('l jS M, Y', time())
                                    ? 
                                    "Today" 
                                    : 
                                    date('l jS M, Y', strtotime($get->transactionDate));
                                ?>
                            </td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>Rs. <?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Days</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByDays) && $transByDays): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByDays as $get): ?>
                        <tr>
                            <td><?=$get->day?>s</td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>Rs. <?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Months</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByMonths) && $transByMonths): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByMonths as $get): ?>
                        <tr>
                            <td><?=$get->month?></td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>Rs. <?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    
    <div class="col-sm-6">
        <div class="panel panel-hash">
            <div class="panel-heading">Transactions by Years</div>
            <div class="panel-body scroll panel-height">
                <?php if(isset($transByYears) && $transByYears): ?>
                <table class="table table-responsive table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Qty Sold</th>
                            <th>Tot. Trans</th>
                            <th>Tot. Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($transByYears as $get): ?>
                        <tr>
                            <td><?=$get->year?></td>
                            <td><?=$get->qty_sold?></td>
                            <td><?=$get->tot_trans?></td>
                            <td>Rs. <?=number_format($get->tot_earned, 2)?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                <li>No Transactions</li>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url('public/js/chart.js?v='.time()); ?>"></script>
<script src="<?=base_url('public/js/dashboard.js?v='.time())?>"></script>