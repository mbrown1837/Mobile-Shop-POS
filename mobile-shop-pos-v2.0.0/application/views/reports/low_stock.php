<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row hidden-print">
        <div class="col-sm-12">
            <h2><i class="fa fa-exclamation-triangle text-warning"></i> Low Stock Report</h2>
            <p class="text-muted">Items that are running low on stock and need reordering</p>
            <hr>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row hidden-print">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="get" action="<?= site_url('reports/lowStock') ?>" class="form-inline">
                        <div class="form-group">
                            <label for="threshold">Stock Threshold:</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="threshold" 
                                   name="threshold" 
                                   value="<?= $threshold ?>" 
                                   min="0" 
                                   max="100"
                                   style="width: 100px;">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                        <a href="<?= site_url('reports/lowStock') ?>" class="btn btn-default">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        <button type="button" class="btn btn-info pull-right" onclick="window.print()">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-info-circle"></i> Summary
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4 text-center">
                            <h4><i class="fa fa-exclamation-triangle text-warning"></i></h4>
                            <h3 class="text-warning"><?= $total_items ?></h3>
                            <p class="text-muted">Items Below Threshold</p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <h4><i class="fa fa-sort-numeric-asc text-info"></i></h4>
                            <h3 class="text-info"><?= $threshold ?></h3>
                            <p class="text-muted">Current Threshold</p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <h4><i class="fa fa-money text-success"></i></h4>
                            <h3 class="text-success">Rs. <?= number_format($total_value, 2) ?></h3>
                            <p class="text-muted">Total Stock Value</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> Low Stock Items
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if (empty($items)): ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> 
                            <strong>Great!</strong> No items are below the stock threshold of <?= $threshold ?>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th class="text-center">Available Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Stock Value</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr class="<?= $item->available_qty == 0 ? 'danger' : ($item->available_qty <= 5 ? 'warning' : '') ?>">
                                            <td><?= $counter++ ?></td>
                                            <td><strong><?= htmlspecialchars($item->code) ?></strong></td>
                                            <td><?= htmlspecialchars($item->name) ?></td>
                                            <td><?= htmlspecialchars($item->brand ?? '-') ?></td>
                                            <td><?= htmlspecialchars($item->model ?? '-') ?></td>
                                            <td>
                                                <span class="label label-<?= $item->category == 'mobile' ? 'primary' : ($item->category == 'accessory' ? 'info' : 'default') ?>">
                                                    <?= ucfirst($item->category) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="label label-<?= $item->item_type == 'serialized' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($item->item_type) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong class="<?= $item->available_qty == 0 ? 'text-danger' : ($item->available_qty <= 5 ? 'text-warning' : 'text-success') ?>">
                                                    <?= $item->available_qty ?>
                                                </strong>
                                            </td>
                                            <td class="text-right">Rs. <?= number_format($item->unitPrice, 2) ?></td>
                                            <td class="text-right">Rs. <?= number_format($item->total_value, 2) ?></td>
                                            <td class="text-center">
                                                <?php if ($item->available_qty == 0): ?>
                                                    <span class="label label-danger">
                                                        <i class="fa fa-times-circle"></i> Out of Stock
                                                    </span>
                                                <?php elseif ($item->available_qty <= 5): ?>
                                                    <span class="label label-warning">
                                                        <i class="fa fa-exclamation-triangle"></i> Critical
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-info">
                                                        <i class="fa fa-info-circle"></i> Low
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="info">
                                        <th colspan="9" class="text-right">Total Stock Value:</th>
                                        <th class="text-right">Rs. <?= number_format($total_value, 2) ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Legend -->
                        <div class="alert alert-info hidden-print">
                            <strong><i class="fa fa-info-circle"></i> Legend:</strong>
                            <ul class="list-inline" style="margin-bottom: 0; margin-top: 10px;">
                                <li><span class="label label-danger">Out of Stock</span> = 0 items</li>
                                <li><span class="label label-warning">Critical</span> = 1-5 items</li>
                                <li><span class="label label-info">Low</span> = 6-<?= $threshold ?> items</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row hidden-print">
        <div class="col-sm-12">
            <a href="<?= site_url('reports') ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Back to Reports
            </a>
            <a href="<?= site_url('items') ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Items
            </a>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    .hidden-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .panel {
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }
    
    .table {
        font-size: 11px;
    }
    
    .table th, .table td {
        padding: 5px !important;
    }
</style>
