<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">   
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm">
                        <button class="btn btn-primary btn-sm" id='createItem'>Add New Item</button>
                    </div>

                    <div class="col-sm-2 form-inline form-group-sm">
                        <label for="itemsListPerPage">Show</label>
                        <select id="itemsListPerPage" class="form-control input-sm">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>per page</label>
                    </div>
                    
                    <div class="col-sm-2 form-group-sm form-inline">
                        <label for="categoryFilter">Category</label>
                        <select id="categoryFilter" class="form-control input-sm">
                            <option value="">All</option>
                            <option value="mobile">Mobile</option>
                            <option value="accessory">Accessory</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="col-sm-2 form-group-sm form-inline">
                        <label for="itemTypeFilter">Type</label>
                        <select id="itemTypeFilter" class="form-control input-sm">
                            <option value="">All</option>
                            <option value="standard">Standard</option>
                            <option value="serialized">Serialized</option>
                        </select>
                    </div>
                    
                    <div class="col-sm-2 form-group-sm form-inline">
                        <label for="stockStatusFilter">Stock</label>
                        <select id="stockStatusFilter" class="form-control input-sm">
                            <option value="">All</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="sold_out">Sold Out</option>
                        </select>
                    </div>

                    <div class="col-sm-2 form-group-sm form-inline">
                        <label for="itemsListSortBy">Sort by</label>
                        <select id="itemsListSortBy" class="form-control input-sm">
                            <option value="name-ASC">Name (A-Z)</option>
                            <option value="name-DESC">Name (Z-A)</option>
                            <option value="code-ASC">Code (Asc)</option>
                            <option value="code-DESC">Code (Desc)</option>
                            <option value="unitPrice-DESC">Price (High)</option>
                            <option value="unitPrice-ASC">Price (Low)</option>
                            <option value="quantity-DESC">Qty (High)</option>
                            <option value="quantity-ASC">Qty (Low)</option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control input-sm" placeholder="Search by Name, Code, IMEI, Brand, Model..." style="width: 100%;">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- row of adding new item form and items list table-->
    <div class="row">
        <div class="col-sm-12">
            <!--Form to add/update an item-->
            <div class="col-sm-4 hidden" id='createNewItemDiv'>
                <div class="well">
                    <button class="btn btn-info btn-xs pull-left" id="useBarcodeScanner">Use Scanner</button>
                    <button class="close cancelAddItem">&times;</button><br>
                    <form name="addNewItemForm" id="addNewItemForm" role="form">
                        <div class="text-center errMsg" id='addCustErrMsg'></div>
                        <br>
                        
                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="itemCode">Item Code</label>
                            <input type="text" id="itemCode" name="itemCode" placeholder="Auto-generated" class="form-control" readonly style="background-color:#f5f5f5;">
                            <span class="help-block text-muted"><small><i class="fa fa-info-circle"></i> Auto-generated</small></span>
                        </div></div>
                        
                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label>Product Type <span class="text-danger">*</span></label>
                            <div>
                                <label class="radio-inline"><input type="radio" name="itemType" id="itemTypeStandard" value="standard" checked> <i class="fa fa-cube"></i> Standard</label>
                                <label class="radio-inline"><input type="radio" name="itemType" id="itemTypeSerialized" value="serialized"> <i class="fa fa-mobile"></i> Serialized</label>
                            </div>
                            <span class="help-block errMsg" id="itemTypeErr"></span>
                        </div></div>
                        
                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="itemCategory">Category <span class="text-danger">*</span></label>
                            <select id="itemCategory" name="itemCategory" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="mobile">Mobile</option>
                                <option value="accessory">Accessory</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="help-block errMsg" id="itemCategoryErr"></span>
                        </div></div>
                        
                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="itemName">Item Name <span class="text-danger">*</span></label>
                            <input type="text" id="itemName" name="itemName" placeholder="Item Name" maxlength="80" class="form-control">
                            <span class="help-block errMsg" id="itemNameErr"></span>
                        </div></div>
                        
                        <div class="row">
                            <div class="col-sm-6 form-group-sm">
                                <label for="itemBrand">Brand</label>
                                <input type="text" id="itemBrand" name="itemBrand" placeholder="e.g., Apple" maxlength="50" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group-sm">
                                <label for="itemModel">Model</label>
                                <input type="text" id="itemModel" name="itemModel" placeholder="e.g., iPhone 13" maxlength="50" class="form-control">
                            </div>
                        </div>

                        <div class="row" id="quantityFieldRow"><div class="col-sm-12 form-group-sm">
                            <label for="itemQuantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="itemQuantity" name="itemQuantity" placeholder="Available Quantity" class="form-control" min="0">
                            <span class="help-block errMsg" id="itemQuantityErr"></span>
                        </div></div>
                        
                        <div id="imeiFieldsContainer" style="display:none;">
                            <div class="row"><div class="col-sm-12">
                                <label>Device Details</label>
                            </div></div>
                            
                            <div class="row">
                                <div class="col-sm-6 form-group-sm">
                                    <label for="itemColor">Color</label>
                                    <input type="text" id="itemColor" name="itemColor" placeholder="e.g., Black, White" maxlength="50" class="form-control">
                                </div>
                                <div class="col-sm-6 form-group-sm">
                                    <label for="itemCostPrice">Cost Price (Optional)</label>
                                    <input type="text" id="itemCostPrice" name="itemCostPrice" placeholder="Purchase cost" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row"><div class="col-sm-12">
                                <label>IMEI Numbers <span class="text-danger">*</span></label>
                                <small class="text-muted">(15 digits each)</small>
                            </div></div>
                            <div id="imeiFieldsList"></div>
                            <div class="row"><div class="col-sm-12">
                                <button type="button" class="btn btn-sm btn-success" id="addMoreImeiBtn"><i class="fa fa-plus"></i> Add Another IMEI</button>
                            </div></div>
                            <span class="help-block errMsg" id="imeiFieldsErr"></span>
                        </div>

                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="itemPrice">Rs. Unit Price <span class="text-danger">*</span></label>
                            <input type="text" id="itemPrice" name="itemPrice" placeholder="Rs. Unit Price" class="form-control">
                            <span class="help-block errMsg" id="itemPriceErr"></span>
                        </div></div>
                        
                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="warrantyMonths">Warranty (Months)</label>
                            <input type="number" id="warrantyMonths" name="warrantyMonths" placeholder="e.g., 12" class="form-control" min="0" max="60">
                        </div></div>

                        <div class="row"><div class="col-sm-12 form-group-sm">
                            <label for="itemDescription">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescription" name="itemDescription" rows='3' placeholder="Optional"></textarea>
                        </div></div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Item</button>
                            </div>
                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddItem" class="btn btn-danger btn-sm cancelAddItem">Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
            
            <!--- Item list div-->
            <div class="col-sm-12" id="itemsListDiv">
                <!-- Item list Table-->
                <div class="row">
                    <div class="col-sm-12" id="itemsListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of item list div-->

        </div>
    </div>
    <!-- End of row of adding new item form and items list table-->
</div>

<!--modal to update stock-->
<div id="updateStockModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Update Stock</h4>
                <div id="stockUpdateFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="updateStockForm" id="updateStockForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label>Item Name</label>
                            <input type="text" readonly id="stockUpdateItemName" class="form-control">
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label>Item Code</label>
                            <input type="text" readonly id="stockUpdateItemCode" class="form-control">
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label>Quantity in Stock</label>
                            <input type="text" readonly id="stockUpdateItemQInStock" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="stockUpdateType">Update Type</label>
                            <select id="stockUpdateType" class="form-control checkField">
                                <option value="">---</option>
                                <option value="newStock">New Stock</option>
                                <option value="deficit">Deficit</option>
                            </select>
                            <span class="help-block errMsg" id="stockUpdateTypeErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="stockUpdateQuantity">Quantity</label>
                            <input type="number" id="stockUpdateQuantity" placeholder="Update Quantity"
                                class="form-control checkField" min="0">
                            <span class="help-block errMsg" id="stockUpdateQuantityErr"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="stockUpdateDescription">Description (Optional)</label>
                            <textarea class="form-control" id="stockUpdateDescription" placeholder="Optional: Reason for stock update" rows="2"></textarea>
                            <small class="text-muted">If left empty, a default description will be used</small>
                        </div>
                    </div>
                    
                    <input type="hidden" id="stockUpdateItemId">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="stockUpdateSubmit">Update</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal-->



<!--modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Item</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="itemNameEdit">Item Name <span class="text-danger">*</span></label>
                            <input type="text" id="itemNameEdit" placeholder="Item Name" autofocus class="form-control checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="itemCodeEdit">Item Code</label>
                            <input type="text" id="itemCodeEdit" class="form-control" readonly style="background-color:#f5f5f5;">
                            <small class="text-muted">Cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCategoryEdit">Category</label>
                            <select id="itemCategoryEdit" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="mobile">Mobile</option>
                                <option value="accessory">Accessory</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemBrandEdit">Brand</label>
                            <input type="text" id="itemBrandEdit" placeholder="Brand" class="form-control">
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemModelEdit">Model</label>
                            <input type="text" id="itemModelEdit" placeholder="Model" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemPriceEdit">Selling Price <span class="text-danger">*</span></label>
                            <input type="text" id="itemPriceEdit" name="itemPrice" placeholder="Customer Price" class="form-control checkField">
                            <small class="text-muted">Price for customers</small>
                            <span class="help-block errMsg" id="itemPriceEditErr"></span>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCostPriceEdit">Cost Price (Optional)</label>
                            <input type="text" id="itemCostPriceEdit" placeholder="Purchase Price" class="form-control">
                            <small class="text-muted">Your purchase cost</small>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="warrantyMonthsEdit">Warranty (Months)</label>
                            <input type="number" id="warrantyMonthsEdit" placeholder="Warranty" class="form-control" min="0" max="60">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescriptionEdit">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescriptionEdit" rows="3" placeholder="Optional Item Description"></textarea>
                        </div>
                    </div>
                    
                    <!-- IMEI Management Section (only for serialized items) -->
                    <div class="row" id="editImeiManagementSection" style="display:none;">
                        <div class="col-sm-12">
                            <div class="alert alert-info">
                                <i class="fa fa-mobile"></i> <strong>IMEI Management</strong>
                                <p class="text-muted" style="margin:5px 0 0 0;">This is a serialized item. Click below to view and manage IMEI numbers.</p>
                                <button type="button" class="btn btn-sm btn-info view-imeis-edit" style="margin-top:10px;">
                                    <i class="fa fa-list"></i> View/Manage IMEIs
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="itemIdEdit">
                    <input type="hidden" id="itemTypeEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="editItemSubmit"><i class="fa fa-save"></i> Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal-->

<!--modal to confirm delete-->
<div id="deleteItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-exclamation-triangle"></i> Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p class="text-center" style="font-size: 16px;">
                    Are you sure you want to delete <strong id="deleteItemName"></strong>?
                </p>
                <p class="text-center text-danger">
                    <i class="fa fa-warning"></i> This action cannot be undone.
                </p>
                <input type="hidden" id="deleteItemId">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="confirmDeleteBtn"><i class="fa fa-trash"></i> Yes, Delete</button>
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal-->

<script src="<?=base_url()?>public/js/items.js"></script>

<?php $this->load->view('items/imei_list_modal'); ?>
