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
        <!-- Sales Reports -->
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-line-chart"></i> Sales Reports
                    </h4>
                </div>
                <div class="panel-body">
                    <p>Daily, monthly sales with profit analysis.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('reports_enhanced/salesSummary?type=daily')?>" class="btn btn-primary">
                            <i class="fa fa-calendar-o"></i> Daily Sales Report
                        </a>
                        <a href="<?=site_url('reports_enhanced/salesSummary?type=monthly')?>" class="btn btn-primary">
                            <i class="fa fa-calendar"></i> Monthly Sales Report
                        </a>
                        <a href="<?=site_url('reports_enhanced/salesSummary?type=itemwise')?>" class="btn btn-primary">
                            <i class="fa fa-list"></i> Item-wise Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khata/Credit Reports -->
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-book"></i> Khata Reports
                    </h4>
                </div>
                <div class="panel-body">
                    <p>Customer credit and outstanding balances.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('reports_enhanced/khataReport')?>" class="btn btn-warning">
                            <i class="fa fa-users"></i> Outstanding Balances
                        </a>
                        <a href="<?=site_url('customers')?>" class="btn btn-info">
                            <i class="fa fa-list"></i> All Customers
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
                    <p>Stock levels and inventory analysis.</p>
                    <div class="btn-group-vertical btn-block">
                        <a href="<?=site_url('reports/lowStock')?>" class="btn btn-success">
                            <i class="fa fa-exclamation-triangle"></i> Low Stock Alert
                        </a>
                        <a href="<?=site_url('reports/stockValue')?>" class="btn btn-success">
                            <i class="fa fa-money"></i> Stock Value
                        </a>
                        <a href="<?=site_url('items')?>" class="btn btn-info">
                            <i class="fa fa-list"></i> All Items
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
