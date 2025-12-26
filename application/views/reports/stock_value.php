<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row hidden-print">
        <div class="col-sm-12">
            <h2><i class="fa fa-money text-success"></i> Stock Value Report</h2>
            <p class="text-muted">Complete inventory valuation by category and item type</p>
            <hr>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row hidden-print">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form method="get" action="<?= site_url('reports/stockValue') ?>" class="form-inline">
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select class="form-control" id="category" name="category">
                                <option value="all" <?= $selected_category == 'all' ? 'selected' : '' ?>>All Categories</option>
                                <option value="mobile" <?= $selected_category == 'mobile' ? 'selected' : '' ?>>Mobile</option>
                                <option value="accessory" <?= $selected_category == 'accessory' ? 'selected' : '' ?>>Accessory</option>
                                <option value="other" <?= $selected_category == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_type">Item Type:</label>
                            <select class="form-control" id="item_type" name="item_type">
                                <option value="all" <?= $selected_item_type == 'all' ? 'selected' : '' ?>>All Types</option>
                                <option value="standard" <?= $selected_item_type == 'standard' ? 'selected' : '' ?>>Standard</option>
                                <option value="serialized" <?= $selected_item_type == 'serialized' ? 'selected' : '' ?>>Serialized</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                        <a href="<?= site_url('reports/stockValue') ?>" class="btn btn-default">
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
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-info-circle"></i> Overall Summary
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4 text-center">
                            <h4><i class="fa fa-cubes text-info"></i></h4>
                            <h3 class="text-info"><?= $total_items ?></h3>
                            <p class="text-muted">Total Items</p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <h4><i class="fa fa-sort-numeric-asc text-primary"></i></h4>
                            <h3 class="text-primary"><?= number_format($total_quantity) ?></h3>
                            <p class="text-muted">Total Quantity</p>
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

    <!-- Category Breakdown -->
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-pie-chart"></i> Breakdown by Category
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Items</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Value</th>
                                <th class="text-right">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($category_breakdown as $cat => $data): ?>
                                <?php if ($data['count'] > 0): ?>
                                    <tr>
                                        <td>
                                            <span class="label label-<?= $cat == 'mobile' ? 'primary' : ($cat == 'accessory' ? 'info' : 'default') ?>">
                                                <?= ucfirst($cat) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= $data['count'] ?></td>
                                        <td class="text-center"><?= number_format($data['qty']) ?></td>
                                        <td class="text-right">Rs. <?= number_format($data['value'], 2) ?></td>
                                        <td class="text-right">
                                            <?= $total_value > 0 ? number_format(($data['value'] / $total_value) * 100, 1) : 0 ?>%
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-bar-chart"></i> Breakdown by Item Type
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th class="text-center">Items</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Value</th>
                                <th class="text-right">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($type_breakdown as $type => $data): ?>
                                <?php if ($data['count'] > 0): ?>
                                    <tr>
                                        <td>
                                            <span class="label label-<?= $type == 'serialized' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($type) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= $data['count'] ?></td>
                                        <td class="text-center"><?= number_format($data['qty']) ?></td>
                                        <td class="text-right">Rs. <?= number_format($data['value'], 2) ?></td>
                                        <td class="text-right">
                                            <?= $total_value > 0 ? number_format(($data['value'] / $total_value) * 100, 1) : 0 ?>%
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Items Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> Detailed Stock Valuation
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if (empty($items)): ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            No items found matching the selected filters.
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
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Total Value</th>
                                        <th class="text-right">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
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
                                                <strong><?= $item->available_qty ?></strong>
                                            </td>
                                            <td class="text-right">Rs. <?= number_format($item->unitPrice, 2) ?></td>
                                            <td class="text-right">
                                                <strong class="text-success">Rs. <?= number_format($item->total_value, 2) ?></strong>
                                            </td>
                                            <td class="text-right">
                                                <?= $total_value > 0 ? number_format(($item->total_value / $total_value) * 100, 2) : 0 ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="success">
                                        <th colspan="7" class="text-right">Totals:</th>
                                        <th class="text-center"><?= number_format($total_quantity) ?></th>
                                        <th></th>
                                        <th class="text-right">Rs. <?= number_format($total_value, 2) ?></th>
                                        <th class="text-right">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Statistics -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-4">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-calculator"></i> Average Item Value:</strong><br>
                                    Rs. <?= $total_items > 0 ? number_format($total_value / $total_items, 2) : 0 ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="alert alert-success">
                                    <strong><i class="fa fa-line-chart"></i> Highest Value Item:</strong><br>
                                    <?php if (!empty($items)): ?>
                                        <?= htmlspecialchars($items[0]->name) ?> (Rs. <?= number_format($items[0]->total_value, 2) ?>)
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-archive"></i> Average Unit Price:</strong><br>
                                    Rs. <?= $total_quantity > 0 ? number_format($total_value / $total_quantity, 2) : 0 ?>
                                </div>
                            </div>
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
                <i class="fa fa-cubes"></i> View Inventory
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
