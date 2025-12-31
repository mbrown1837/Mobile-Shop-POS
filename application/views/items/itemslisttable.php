<?php defined('BASEPATH') OR exit('') ?>

<div class='col-sm-6'>
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>

<div class='col-sm-6 text-right'><b>Items Total Worth/Price:</b> Rs. <?=$cum_total ? number_format($cum_total, 2) : '0.00'?></div>

<div class='col-xs-12'>
    <div class="panel panel-primary">
        <!-- Default panel contents -->
        <div class="panel-heading">Items</div>
        <?php if($allItems): ?>
        <div class="table table-responsive">
            <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>CODE</th>
                        <th>NAME</th>
                        <th>TYPE</th>
                        <th>CATEGORY</th>
                        <th>BRAND</th>
                        <th>QTY/IMEI</th>
                        <th>COST</th>
                        <th>SELLING</th>
                        <th>PROFIT</th>
                        <th>WARRANTY</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allItems as $get): ?>
                    <tr>
                        <input type="hidden" value="<?=$get->id?>" class="curItemId">
                        <th class="itemSN"><?=$sn?>.</th>
                        <td><span id="itemCode-<?=$get->id?>"><?=$get->code?></span></td>
                        <td>
                            <span id="itemName-<?=$get->id?>"><?=$get->name?></span>
                            <?php if(isset($get->model) && $get->model): ?><br><small class="text-muted"><?=$get->model?></small><?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($get->item_type) && $get->item_type === 'serialized'): ?>
                                <span class="label label-info"><i class="fa fa-mobile"></i> Serial</span>
                            <?php else: ?>
                                <span class="label label-default"><i class="fa fa-cube"></i> Std</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($get->category)): ?>
                                <?php if($get->category === 'mobile'): ?><span class="label label-primary">Mobile</span>
                                <?php elseif($get->category === 'accessory'): ?><span class="label label-success">Accessory</span>
                                <?php else: ?><span class="label label-default">Other</span><?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?=isset($get->brand) ? $get->brand : '-'?></td>
                        <td class="<?=(isset($get->available_qty) ? $get->available_qty : $get->quantity) <= 10 ? 'bg-danger' : ((isset($get->available_qty) ? $get->available_qty : $get->quantity) <= 25 ? 'bg-warning' : '')?>">
                            <?php 
                            $qty = isset($get->available_qty) ? $get->available_qty : $get->quantity;
                            ?>
                            <span id="itemQuantity-<?=$get->id?>"><?=$qty?></span>
                            <?php if($qty == 0): ?>
                                <br><span class="label label-danger">SOLD OUT</span>
                            <?php endif; ?>
                            <?php if(isset($get->item_type) && $get->item_type === 'serialized'): ?>
                                <br><a href="#" class="btn btn-xs btn-info view-imeis" data-item-id="<?=$get->id?>" data-item-name="<?=$get->name?>"><i class="fa fa-list"></i> IMEIs</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            // Debug output
                            $cost = isset($get->cost_price) ? floatval($get->cost_price) : 0;
                            // Temporary debug: echo "<!-- Cost: " . $cost . " Raw: " . (isset($get->cost_price) ? $get->cost_price : 'NOT SET') . " -->";
                            if($cost > 0): ?>
                                <small class="text-muted">Rs.</small> <?=number_format($cost, 2)?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><strong>Rs. <span id="itemPrice-<?=$get->id?>"><?=number_format($get->unitPrice, 2)?></span></strong></td>
                        <td>
                            <?php 
                            $cost = isset($get->cost_price) ? floatval($get->cost_price) : 0;
                            $selling = floatval($get->unitPrice);
                            $profit = $selling - $cost;
                            
                            if($cost > 0) {
                                $profitClass = $profit > 0 ? 'text-success' : ($profit < 0 ? 'text-danger' : 'text-muted');
                                echo '<span class="' . $profitClass . '"><strong>Rs. ' . number_format($profit, 2) . '</strong></span>';
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <td><?php if(isset($get->warranty_months) && $get->warranty_months > 0): ?><span class="label label-success"><?=$get->warranty_months?>m</span><?php else: ?>-<?php endif; ?></td>
                        <td>
                            <div class="btn-group-vertical btn-group-xs">
                                <?php if(!isset($get->item_type) || $get->item_type === 'standard'): ?>
                                <a class="btn btn-default updateStock" id="stock-<?=$get->id?>"><i class="fa fa-refresh"></i> Qty</a>
                                <?php endif; ?>
                                <a class="btn btn-primary editItem" id="edit-<?=$get->id?>"><i class="fa fa-pencil"></i> Edit</a>
                                <a class="btn btn-danger delItem"><i class="fa fa-trash"></i> Del</a>
                            </div>
                        </td>
                    </tr>
                    <?php $sn++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- table div end-->
        <?php else: ?>
        <ul><li>No items</li></ul>
        <?php endif; ?>
    </div>
    <!--- panel end-->
</div>

<!---Pagination div-->
<div class="col-sm-12 text-center">
    <ul class="pagination">
        <?= isset($links) ? $links : "" ?>
    </ul>
</div>
