<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">Reports & Analytics</h3>
            <hr>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Profit Reports -->
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-line-chart"></i> Profit Reports
                    </h4>
                </div>
                <div class="panel-body">
                    <p>View daily, monthly, and custom date range profit reports.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('reports/profitDaily')?>" class="btn btn-default">
                            <i class="fa fa-calendar-o"></i> Daily Profit
                        </a>
                        <a href="<?=site_url('reports/profitMonthly')?>" class="btn btn-default">
                            <i class="fa fa-calendar"></i> Monthly Profit
                        </a>
                        <a href="<?=site_url('reports/profitRange')?>" class="btn btn-default">
                            <i class="fa fa-calendar-check-o"></i> Date Range Profit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-cubes"></i> Inventory Reports
                    </h4>
                </div>
                <div class="panel-body">
                    <p>View stock levels, values, and IMEI status reports.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('reports/lowStock')?>" class="btn btn-default">
                            <i class="fa fa-exclamation-triangle"></i> Low Stock Alert
                        </a>
                        <a href="<?=site_url('reports/stockValue')?>" class="btn btn-default">
                            <i class="fa fa-money"></i> Stock Value
                        </a>
                        <a href="<?=site_url('reports/imeiStatus')?>" class="btn btn-default">
                            <i class="fa fa-mobile"></i> IMEI Status
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Reports -->
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-users"></i> Customer Reports
                    </h4>
                </div>
                <div class="panel-body">
                    <p>View customer balances and payment history.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('customers')?>" class="btn btn-default">
                            <i class="fa fa-list"></i> All Customers
                        </a>
                        <a href="<?=site_url('customers')?>" class="btn btn-default">
                            <i class="fa fa-exclamation-circle"></i> Outstanding Balances
                        </a>
                        <a href="<?=site_url('customers')?>" class="btn btn-default">
                            <i class="fa fa-history"></i> Payment History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-info-circle"></i> Quick Stats
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3 text-center">
                            <h4>Total Items</h4>
                            <h2><?=$this->db->count_all('items')?></h2>
                        </div>
                        <div class="col-sm-3 text-center">
                            <h4>Total Customers</h4>
                            <h2><?=$this->db->count_all('customers')?></h2>
                        </div>
                        <div class="col-sm-3 text-center">
                            <h4>Total Transactions</h4>
                            <h2><?=$this->db->count_all('transactions')?></h2>
                        </div>
                        <div class="col-sm-3 text-center">
                            <h4>Available IMEIs</h4>
                            <h2><?=$this->db->where('status', 'available')->count_all_results('item_serials')?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
