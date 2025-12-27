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
    
    // Auto-focus next IMEI field
    $(document).on('input', '.imei-input', function() {
        const value = $(this).val();
        if (value.length === 15 && /^\d{15}$/.test(value)) {
            const currentId = $(this).data('imei-id');
            const nextField = $(`.imei-field-row[data-imei-id="${currentId}"]`).next('.imei-field-row');
            
            if (nextField.length > 0) {
                nextField.find('.imei-input').focus();
            } else {
                addImeiField();
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
            
            $('.imei-field-row').each(function() {
                const imei = $(this).find('input[name="imeiNumbers[]"]').val();
                
                if (imei) {
                    if (!/^\d{15}$/.test(imei)) {
                        $('#imeiFieldsErr').text('All IMEI numbers must be exactly 15 digits');
                        hasError = true;
                        return false;
                    }
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
        
        $.ajax({
            url: baseUrl + 'index.php/items/add',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    alert(response.msg);
                    $('#addNewItemForm')[0].reset();
                    $('#createNewItemDiv').addClass('hidden');
                    $('#itemsListDiv').removeClass('col-sm-8').addClass('col-sm-12');
                    lilt();
                } else {
                    $('#addCustErrMsg').html('<div class="alert alert-danger">' + response.msg + '</div>');
                    if (response.itemName) $('#itemNameErr').text(response.itemName);
                    if (response.itemPrice) $('#itemPriceErr').text(response.itemPrice);
                    if (response.itemQuantity) $('#itemQuantityErr').text(response.itemQuantity);
                    if (response.itemCategory) $('#itemCategoryErr').text(response.itemCategory);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
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
        const itemName = $('#itemName-' + itemId).text();
        const itemCode = $('#itemCode-' + itemId).text();
        const itemPrice = $('#itemPrice-' + itemId).text().replace(/,/g, '');
        const itemDescription = $('#itemDescription-' + itemId).text();
        
        $('#itemIdEdit').val(itemId);
        $('#itemNameEdit').val(itemName);
        $('#itemCodeEdit').val(itemCode);
        $('#itemPriceEdit').val(itemPrice);
        $('#itemDescriptionEdit').val(itemDescription);
        
        $('#editItemModal').modal('show');
    });
    
    // Save Edit Item
    $('#editItemSubmit').on('click', function() {
        $('.errMsg').text('');
        
        const itemId = $('#itemIdEdit').val();
        const itemName = $('#itemNameEdit').val();
        const itemCode = $('#itemCodeEdit').val();
        const itemPrice = $('#itemPriceEdit').val();
        const itemDescription = $('#itemDescriptionEdit').val();
        
        if (!itemName) {
            $('#itemNameEditErr').text('Item name is required');
            return;
        }
        if (!itemPrice) {
            $('#itemPriceEditErr').text('Price is required');
            return;
        }
        
        $(this).prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: baseUrl + 'index.php/items/edit',
            type: 'POST',
            data: {
                itemId: itemId,
                itemName: itemName,
                itemCode: itemCode,
                itemPrice: itemPrice,
                itemDescription: itemDescription
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    alert(response.msg);
                    $('#editItemModal').modal('hide');
                    lilt();
                } else {
                    $('#editItemFMsg').html('<div class="alert alert-danger">' + response.msg + '</div>');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#editItemSubmit').prop('disabled', false).text('Save');
            }
        });
    });
    
    // Delete Item Button
    $(document).on('click', '.delItem', function() {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }
        
        const itemId = $(this).closest('tr').find('.curItemId').val();
        
        $.ajax({
            url: baseUrl + 'index.php/items/delete',
            type: 'POST',
            data: { itemId: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    alert(response.msg);
                    lilt();
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
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
        if (!description) {
            $('#stockUpdateDescriptionErr').text('Please enter description');
            return;
        }
        
        $(this).prop('disabled', true).text('Updating...');
        
        $.ajax({
            url: baseUrl + 'index.php/items/updatestock',
            type: 'POST',
            data: {
                itemId: itemId,
                updateType: updateType,
                quantity: quantity,
                description: description
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 1) {
                    alert(response.msg);
                    $('#updateStockModal').modal('hide');
                    $('#updateStockForm')[0].reset();
                    lilt();
                } else {
                    $('#stockUpdateFMsg').html('<div class="alert alert-danger">' + response.msg + '</div>');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
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
