// Items Management JavaScript

$(document).ready(function() {
    // Load items on page load
    if ($('#itemsListTable').length) {
        lilt();
    }
    
    // Item Type Toggle
    let imeiCounter = 0;
    
    $('input[name="itemType"]').on('change', function() {
        if ($(this).val() === 'serialized') {
            $('#quantityFieldRow').hide();
            $('#imeiFieldsContainer').show();
            if ($('#imeiFieldsList .imei-field-row').length === 0) {
                addImeiField();
            }
        } else {
            $('#quantityFieldRow').show();
            $('#imeiFieldsContainer').hide();
            $('#imeiFieldsList').empty();
            imeiCounter = 0;
        }
    });
    
    // Add IMEI Field
    $('#addMoreImeiBtn').on('click', addImeiField);
    
    function addImeiField() {
        imeiCounter++;
        const fieldHtml = `
            <div class="row imei-field-row" data-imei-id="${imeiCounter}" style="margin-bottom:10px;">
                <div class="col-sm-10">
                    <input type="text" name="imeiNumbers[]" class="form-control imei-input" 
                           placeholder="IMEI (15 digits)" maxlength="15" pattern="\\d{15}" data-imei-id="${imeiCounter}">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm btn-block remove-imei-btn" data-imei-id="${imeiCounter}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#imeiFieldsList').append(fieldHtml);
        $(`input[data-imei-id="${imeiCounter}"].imei-input`).focus();
    }
    
    // Remove IMEI Field
    $(document).on('click', '.remove-imei-btn', function() {
        const imeiId = $(this).data('imei-id');
        $(`.imei-field-row[data-imei-id="${imeiId}"]`).remove();
        
        if ($('input[name="itemType"]:checked').val() === 'serialized' && 
            $('#imeiFieldsList .imei-field-row').length === 0) {
            addImeiField();
        }
    });
    
    // Auto-focus next IMEI field and validate
    $(document).on('input', '.imei-input', function() {
        const $input = $(this);
        const value = $input.val();
        const $row = $input.closest('.imei-field-row');
        
        // Remove any existing validation messages
        $row.find('.imei-validation-msg').remove();
        $input.removeClass('border-danger border-success');
        
        if (value.length === 15) {
            if (/^\d{15}$/.test(value)) {
                // Valid format - check for duplicates
                $input.prop('disabled', true);
                
                $.ajax({
                    url: appRoot + 'items/checkImeiExists',
                    type: 'POST',
                    data: { imei: value },
                    dataType: 'json',
                    success: function(response) {
                        $input.prop('disabled', false);
                        
                        if (response.exists) {
                            // IMEI already exists
                            $input.addClass('border-danger');
                            $row.append('<div class="imei-validation-msg"><small class="text-danger"><i class="fa fa-times-circle"></i> This IMEI already exists in the system!</small></div>');
                        } else {
                            // IMEI is unique
                            $input.addClass('border-success');
                            $row.append('<div class="imei-validation-msg"><small class="text-success"><i class="fa fa-check-circle"></i> Valid & unique</small></div>');
                            
                            // Auto-focus next field
                            const currentId = $input.data('imei-id');
                            const nextField = $(`.imei-field-row[data-imei-id="${currentId}"]`).next('.imei-field-row');
                            
                            if (nextField.length > 0) {
                                nextField.find('.imei-input').focus();
                            } else {
                                addImeiField();
                            }
                        }
                    },
                    error: function() {
                        $input.prop('disabled', false);
                        $input.addClass('border-danger');
                        $row.append('<div class="imei-validation-msg"><small class="text-danger">Error checking IMEI</small></div>');
                    }
                });
            } else {
                // Invalid format
                $input.addClass('border-danger');
                $row.append('<div class="imei-validation-msg"><small class="text-danger"><i class="fa fa-times-circle"></i> Must be 15 digits only</small></div>');
            }
        }
    });
    
    // Category change auto-select type
    $('#itemCategory').on('change', function() {
        const category = $(this).val();
        if (category === 'mobile') {
            $('#itemTypeSerialized').prop('checked', true).trigger('change');
        } else if (category === 'accessory' || category === 'other') {
            $('#itemTypeStandard').prop('checked', true).trigger('change');
        }
    });
    
    // Add New Item
    $('#addNewItem').on('click', function(e) {
        e.preventDefault();
        $('.errMsg').text('');
        
        const itemType = $('input[name="itemType"]:checked').val();
        const itemCategory = $('#itemCategory').val();
        const itemName = $('#itemName').val();
        const itemPrice = $('#itemPrice').val();
        const itemBrand = $('#itemBrand').val();
        const itemModel = $('#itemModel').val();
        const warrantyMonths = $('#warrantyMonths').val();
        const itemDescription = $('#itemDescription').val();
        
        // Validation
        if (!itemType) {
            $('#itemTypeErr').text('Please select product type');
            return;
        }
        if (!itemCategory) {
            $('#itemCategoryErr').text('Please select category');
            return;
        }
        if (!itemName) {
            $('#itemNameErr').text('Item name is required');
            return;
        }
        if (!itemPrice) {
            $('#itemPriceErr').text('Price is required');
            return;
        }
        
        const formData = {
            itemType: itemType,
            itemCategory: itemCategory,
            itemName: itemName,
            itemPrice: itemPrice,
            itemBrand: itemBrand,
            itemModel: itemModel,
            warrantyMonths: warrantyMonths,
            itemDescription: itemDescription
        };
        
        if (itemType === 'standard') {
            const quantity = $('#itemQuantity').val();
            if (!quantity || quantity < 0) {
                $('#itemQuantityErr').text('Quantity is required');
                return;
            }
            formData.itemQuantity = quantity;
        } else if (itemType === 'serialized') {
            const imeiNumbers = [];
            let hasError = false;
            const color = $('#itemColor').val();
            const costPrice = $('#itemCostPrice').val();
            
            // Check for duplicate IMEIs in form
            const imeiValues = [];
            $('.imei-field-row').each(function() {
                const $input = $(this).find('input[name="imeiNumbers[]"]');
                const imei = $input.val();
                
                if (imei) {
                    // Check format
                    if (!/^\d{15}$/.test(imei)) {
                        $('#imeiFieldsErr').text('All IMEI numbers must be exactly 15 digits');
                        hasError = true;
                        return false;
                    }
                    
                    // Check if IMEI has error indicator (red border)
                    if ($input.hasClass('border-danger')) {
                        $('#imeiFieldsErr').text('Please fix invalid/duplicate IMEI numbers before submitting');
                        hasError = true;
                        return false;
                    }
                    
                    // Check for duplicates within form
                    if (imeiValues.includes(imei)) {
                        $('#imeiFieldsErr').text('Duplicate IMEI numbers in form: ' + imei);
                        hasError = true;
                        return false;
                    }
                    
                    imeiValues.push(imei);
                    imeiNumbers.push({
                        imei: imei,
                        color: color || '',
                        cost_price: costPrice || itemPrice
                    });
                }
            });
            
            if (hasError) return;
            
            if (imeiNumbers.length === 0) {
                $('#imeiFieldsErr').text('At least one IMEI number is required for serialized items');
                return;
            }
            
            formData.imeiNumbers = imeiNumbers;
        }
        
        $(this).prop('disabled', true).text('Adding...');
        
        // Debug: Log the data being sent
        console.log('Sending item data:', formData);
        
        $.ajax({
            url: appRoot + 'items/add',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showNotification('success', response.msg);
                    $('#addNewItemForm')[0].reset();
                    $('#createNewItemDiv').addClass('hidden');
                    $('#itemsListDiv').removeClass('col-sm-8').addClass('col-sm-12');
                    $('#imeiFieldsList').empty();
                    imeiCounter = 0;
                    lilt();
                } else {
                    showNotification('error', response.msg);
                    if (response.itemName) $('#itemNameErr').text(response.itemName);
                    if (response.itemPrice) $('#itemPriceErr').text(response.itemPrice);
                    if (response.itemQuantity) $('#itemQuantityErr').text(response.itemQuantity);
                    if (response.itemCategory) $('#itemCategoryErr').text(response.itemCategory);
                }
            },
            error: function(xhr, status, error) {
                console.error('Add item error:', xhr.responseText);
                showNotification('error', 'Failed to add item. Please try again.');
            },
            complete: function() {
                $('#addNewItem').prop('disabled', false).text('Add Item');
            }
        });
    });
    
    // Show Add Item Form
    $('#createItem').on('click', function() {
        $('#createNewItemDiv').removeClass('hidden');
        $('#itemsListDiv').removeClass('col-sm-12').addClass('col-sm-8');
        $('#itemName').focus();
    });
    
    // Cancel Add Item
    $('.cancelAddItem').on('click', function() {
        $('#createNewItemDiv').addClass('hidden');
        $('#itemsListDiv').removeClass('col-sm-8').addClass('col-sm-12');
        $('#addNewItemForm')[0].reset();
        $('.errMsg').text('');
        $('#imeiFieldsList').empty();
        imeiCounter = 0;
    });
    
    // Category Filter Change
    $('#categoryFilter').on('change', function() {
        lilt();
    });
    
    // Item Type Filter Change
    $('#itemTypeFilter').on('change', function() {
        lilt();
    });
    
    // Stock Status Filter Change
    $('#stockStatusFilter').on('change', function() {
        lilt();
    });
    
    // Reload items when filters change
    $('#itemsListPerPage, #itemsListSortBy').on('change', function() {
        lilt();
    });
    
    // Search functionality
    let searchTimeout;
    $('#itemSearch').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                lilt();
            }
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Search on Enter key
    $('#itemSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            lilt();
        }
    });
    
    // Edit Item Button
    $(document).on('click', '.editItem', function() {
        const itemId = $(this).attr('id').split('-')[1];
        
        console.log('Fetching item details for ID:', itemId);
        
        // Fetch full item details via AJAX
        $.ajax({
            url: appRoot + 'items/getItemDetails',
            type: 'POST',
            data: { itemId: itemId },
            dataType: 'json',
            success: function(response) {
                console.log('Item details response:', response);
                if (response.status === 1 && response.item) {
                    const item = response.item;
                    
                    $('#itemIdEdit').val(item.id);
                    $('#itemNameEdit').val(item.name);
                    $('#itemCodeEdit').val(item.code);
                    $('#itemPriceEdit').val(item.unitPrice);
                    $('#itemDescriptionEdit').val(item.description || '');
                    $('#itemCategoryEdit').val(item.category || '');
                    $('#itemBrandEdit').val(item.brand || '');
                    $('#itemModelEdit').val(item.model || '');
                    $('#itemCostPriceEdit').val(item.cost_price || '');
                    $('#warrantyMonthsEdit').val(item.warranty_months || 0);
                    $('#itemTypeEdit').val(item.item_type || 'standard');
                    
                    // Show/hide IMEI management section based on item type
                    if (item.item_type === 'serialized') {
                        $('#editImeiManagementSection').show();
                        // Store item details for IMEI button
                        $('#editImeiManagementSection').data('item-id', item.id);
                        $('#editImeiManagementSection').data('item-name', item.name);
                    } else {
                        $('#editImeiManagementSection').hide();
                    }
                    
                    $('#editItemModal').modal('show');
                } else {
                    showNotification('error', 'Error loading item details: ' + (response.msg || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load item details:', xhr.responseText);
                showNotification('error', 'Failed to load item details. Please try again.');
            }
        });
    });
    
    // View IMEIs from Edit Modal
    $(document).on('click', '.view-imeis-edit', function() {
        const itemId = $('#editImeiManagementSection').data('item-id');
        const itemName = $('#editImeiManagementSection').data('item-name');
        const itemCode = $('#itemCodeEdit').val();
        
        // Close edit modal
        $('#editItemModal').modal('hide');
        
        // Open IMEI modal
        $('#imeiModalItemName').text(itemName);
        $('#imeiModalItemCode').text('(' + itemCode + ')');
        $('#imeiStatusFilter').val('');
        
        // Trigger the IMEI view (the modal script will handle loading)
        $('.view-imeis[data-item-id="' + itemId + '"]').trigger('click');
    });
    
    // Save Edit Item
    $('#editItemSubmit').off('click').on('click', function() {
        $('.errMsg').text('');
        
        const itemId = $('#itemIdEdit').val();
        const itemName = $('#itemNameEdit').val();
        const itemCode = $('#itemCodeEdit').val();
        const itemPrice = $('#itemPriceEdit').val();
        const itemDescription = $('#itemDescriptionEdit').val();
        const itemCategory = $('#itemCategoryEdit').val();
        const itemBrand = $('#itemBrandEdit').val();
        const itemModel = $('#itemModelEdit').val();
        const itemCostPrice = $('#itemCostPriceEdit').val();
        const warrantyMonths = $('#warrantyMonthsEdit').val();
        
        if (!itemName) {
            $('#itemNameEditErr').text('Item name is required');
            return;
        }
        if (!itemPrice) {
            $('#itemPriceEditErr').text('Price is required');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        console.log('Submitting edit with data:', {itemId, itemName, itemPrice});
        
        $.ajax({
            url: appRoot + 'items/edit',
            type: 'POST',
            data: {
                itemId: itemId,
                itemName: itemName,
                itemCode: itemCode,
                itemPrice: itemPrice,
                itemDescription: itemDescription,
                itemCategory: itemCategory,
                itemBrand: itemBrand,
                itemModel: itemModel,
                itemCostPrice: itemCostPrice,
                warrantyMonths: warrantyMonths
            },
            dataType: 'json',
            success: function(response) {
                console.log('Edit response:', response);
                if (response.status === 1) {
                    showNotification('success', response.msg || 'Item updated successfully');
                    $('#editItemModal').modal('hide');
                    lilt();
                } else {
                    $('#editItemFMsg').html('<div class="alert alert-danger">' + (response.msg || 'Update failed') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit error:', xhr.responseText);
                showNotification('error', 'An error occurred. Please try again.');
            },
            complete: function() {
                $('#editItemSubmit').prop('disabled', false).html('<i class="fa fa-save"></i> Save');
            }
        });
    });
    
    // Delete Item Button
    $(document).on('click', '.delItem', function() {
        const itemId = $(this).closest('tr').find('.curItemId').val();
        const itemName = $('#itemName-' + itemId).text();
        
        // Show delete confirmation modal
        $('#deleteItemId').val(itemId);
        $('#deleteItemName').text(itemName);
        $('#deleteItemModal').modal('show');
    });
    
    // Confirm Delete Button
    $('#confirmDeleteBtn').on('click', function() {
        const itemId = $('#deleteItemId').val();
        $('#deleteItemModal').modal('hide');
        deleteItem(itemId);
    });
    
    function deleteItem(itemId) {
        $.ajax({
            url: appRoot + 'items/delete',
            type: 'POST',
            data: { itemId: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    // Show success message
                    showNotification('success', response.msg || 'Item deleted successfully');
                    lilt();
                } else {
                    showNotification('error', response.msg || 'Failed to delete item');
                }
            },
            error: function(xhr) {
                showNotification('error', 'An error occurred while deleting the item');
            }
        });
    }
    
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade in" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<i class="fa ' + icon + '"></i> ' + message +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Update Stock Button
    $(document).on('click', '.updateStock', function() {
        const itemId = $(this).attr('id').split('-')[1];
        const itemName = $('#itemName-' + itemId).text();
        const itemCode = $('#itemCode-' + itemId).text();
        const itemQty = $('#itemQuantity-' + itemId).text();
        
        $('#stockUpdateItemId').val(itemId);
        $('#stockUpdateItemName').val(itemName);
        $('#stockUpdateItemCode').val(itemCode);
        $('#stockUpdateItemQInStock').val(itemQty);
        
        $('#updateStockModal').modal('show');
    });
    
    // Submit Stock Update
    $('#stockUpdateSubmit').on('click', function() {
        $('.errMsg').text('');
        $('#stockUpdateFMsg').html('');
        
        const itemId = $('#stockUpdateItemId').val();
        const updateType = $('#stockUpdateType').val();
        const quantity = $('#stockUpdateQuantity').val();
        const description = $('#stockUpdateDescription').val();
        
        if (!updateType) {
            $('#stockUpdateTypeErr').text('Please select update type');
            return;
        }
        if (!quantity || quantity <= 0) {
            $('#stockUpdateQuantityErr').text('Please enter valid quantity');
            return;
        }
        // Description is optional, use default if empty
        const desc = description || 'Stock update - ' + updateType;
        
        $(this).prop('disabled', true).text('Updating...');
        
        $.ajax({
            url: appRoot + 'items/updatestock',
            type: 'POST',
            data: {
                _iId: itemId,
                _upType: updateType,
                qty: quantity,
                desc: desc
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    showNotification('success', response.msg || 'Stock updated successfully');
                    $('#updateStockModal').modal('hide');
                    $('#updateStockForm')[0].reset();
                    lilt();
                } else {
                    $('#stockUpdateFMsg').html('<div class="alert alert-danger">' + (response.msg || 'Update failed') + '</div>');
                }
            },
            error: function(xhr) {
                console.error('Stock update error:', xhr.responseText);
                showNotification('error', 'An error occurred. Please try again.');
            },
            complete: function() {
                $('#stockUpdateSubmit').prop('disabled', false).text('Update');
            }
        });
    });
});

// Load Items List Table function
function lilt(url) {
    url = url || appRoot + 'index.php/items/lilt';
    
    const limit = $('#itemsListPerPage').val() || 10;
    const sortBy = $('#itemsListSortBy').val() || 'name-ASC';
    const category = $('#categoryFilter').val() || '';
    const itemType = $('#itemTypeFilter').val() || '';
    const stockStatus = $('#stockStatusFilter').val() || '';
    const searchTerm = $('#itemSearch').val().trim() || '';
    const sortParts = sortBy.split('-');
    const orderBy = sortParts[0];
    const orderFormat = sortParts[1];
    
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            limit: limit,
            orderBy: orderBy,
            orderFormat: orderFormat,
            category: category,
            itemType: itemType,
            stockStatus: stockStatus,
            search: searchTerm
        },
        dataType: 'json',
        beforeSend: function() {
            $('#itemsListTable').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading items...</p>');
        },
        success: function(response) {
            if (response.itemsListTable) {
                $('#itemsListTable').html(response.itemsListTable);
            } else {
                $('#itemsListTable').html('<p class="text-center text-danger">No items found</p>');
            }
        },
        error: function(xhr) {
            console.error('Items load error:', xhr);
            $('#itemsListTable').html('<p class="text-center text-danger">Error loading items. Status: ' + xhr.status + '</p>');
        }
    });
    
    return false;
}

// Make lilt globally accessible
window.lilt = lilt;
