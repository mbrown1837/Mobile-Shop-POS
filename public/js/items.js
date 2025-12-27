$(document).ready(function(){
    // Load items on page load
    if ($('#itemsListTable').length) {
        lilt(appRoot + 'items/lilt');
    }
    
    let c=0;$('input[name="itemType"]').on('change',function(){$(this).val()==='serialized'?($('#quantityFieldRow').hide(),$('#imeiFieldsContainer').show(),$('#imeiFieldsList .imei-field-row').length===0&&a()):($('#quantityFieldRow').show(),$('#imeiFieldsContainer').hide(),$('#imeiFieldsList').empty(),c=0)});$('#addMoreImeiBtn').on('click',a);function a(){c++;const d=`<div class="row imei-field-row" data-imei-id="${c}" style="margin-bottom:10px;"><div class="col-sm-5"><input type="text" name="imeiNumbers[${c}][imei]" class="form-control imei-input" placeholder="IMEI (15 digits)" maxlength="15" pattern="\\d{15}" data-imei-id="${c}"></div><div class="col-sm-3"><input type="text" name="imeiNumbers[${c}][color]" class="form-control" placeholder="Color"></div><div class="col-sm-3"><input type="text" name="imeiNumbers[${c}][cost_price]" class="form-control" placeholder="Cost"></div><div class="col-sm-1"><button type="button" class="btn btn-danger btn-sm remove-imei-btn" data-imei-id="${c}"><i class="fa fa-times"></i></button></div></div>`;$('#imeiFieldsList').append(d),$(`input[data-imei-id="${c}"].imei-input`).focus()}$(document).on('click','.remove-imei-btn',function(){const d=$(this).data('imei-id');$(`.imei-field-row[data-imei-id="${d}"]`).remove(),$('input[name="itemType"]:checked').val()==='serialized'&&$('#imeiFieldsList .imei-field-row').length===0&&a()});$(document).on('input','.imei-input',function(){const d=$(this).val();if(d.length===15&&/^\d{15}$/.test(d)){const e=$(this).data('imei-id'),f=$(`.imei-field-row[data-imei-id="${e}"]`).next('.imei-field-row');f.length>0?f.find('.imei-input').focus():a()}});$('#itemCategory').on('change',function(){const d=$(this).val();d==='mobile'?$('#itemTypeSerialized').prop('checked',!0).trigger('change'):(d==='accessory'||d==='other')&&$('#itemTypeStandard').prop('checked',!0).trigger('change')});$('#addNewItem').on('click',function(d){d.preventDefault(),$('.errMsg').text('');const e=$('input[name="itemType"]:checked').val(),f=$('#itemCategory').val(),g=$('#itemName').val(),h=$('#itemPrice').val(),i=$('#itemBrand').val(),j=$('#itemModel').val(),k=$('#warrantyMonths').val(),l=$('#itemDescription').val();if(!e)return void $('#itemTypeErr').text('Please select product type');if(!f)return void $('#itemCategoryErr').text('Please select category');if(!g)return void $('#itemNameErr').text('Item name is required');if(!h)return void $('#itemPriceErr').text('Price is required');const m={itemType:e,itemCategory:f,itemName:g,itemPrice:h,itemBrand:i,itemModel:j,warrantyMonths:k,itemDescription:l};if(e==='standard'){const n=$('#itemQuantity').val();if(!n||n<0)return void $('#itemQuantityErr').text('Quantity is required');m.itemQuantity=n}else if(e==='serialized'){const o=[];let p=!1;if($('.imei-field-row').each(function(){const q=$(this).find('input[name*="[imei]"]').val(),r=$(this).find('input[name*="[color]"]').val(),s=$(this).find('input[name*="[cost_price]"]').val();if(q){if(!/^\d{15}$/.test(q))return $('#imeiFieldsErr').text('All IMEI numbers must be exactly 15 digits'),p=!0,!1;o.push({imei:q,color:r||'',cost_price:s||h})}}),p)return;if(o.length===0)return void $('#imeiFieldsErr').text('At least one IMEI number is required for serialized items');m.imeiNumbers=o}$(this).prop('disabled',!0).text('Adding...'),$.ajax({url:baseUrl+'items/add',type:'POST',data:m,dataType:'json',success:function(n){n.status===1?(alert(n.msg),$('#addNewItemForm')[0].reset(),$('#createNewItemDiv').addClass('hidden'),$('#itemsListDiv').removeClass('col-sm-8').addClass('col-sm-12'),lilt(baseUrl+'items/lilt')):($('#addCustErrMsg').html('<div class="alert alert-danger">'+n.msg+'</div>'),n.itemName&&$('#itemNameErr').text(n.itemName),n.itemPrice&&$('#itemPriceErr').text(n.itemPrice),n.itemQuantity&&$('#itemQuantityErr').text(n.itemQuantity),n.itemCategory&&$('#itemCategoryErr').text(n.itemCategory))},error:function(){alert('An error occurred. Please try again.')},complete:function(){$('#addNewItem').prop('disabled',!1).text('Add Item')}})});$('#createItem').on('click',function(){$('#createNewItemDiv').removeClass('hidden'),$('#itemsListDiv').removeClass('col-sm-12').addClass('col-sm-8'),$('#itemName').focus()});$('.cancelAddItem').on('click',function(){$('#createNewItemDiv').addClass('hidden'),$('#itemsListDiv').removeClass('col-sm-8').addClass('col-sm-12'),$('#addNewItemForm')[0].reset(),$('.errMsg').text(''),$('#imeiFieldsList').empty(),c=0});$('#categoryFilter').on('change',function(){lilt(baseUrl+'items/lilt')})});


// Load Items List Table function
function lilt(url) {
    url = url || appRoot + 'items/lilt';
    
    const limit = $('#itemsListPerPage').val() || 10;
    const sortBy = $('#itemsListSortBy').val() || 'name-ASC';
    const category = $('#categoryFilter').val() || '';
    const [orderBy, orderFormat] = sortBy.split('-');
    
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            limit: limit,
            orderBy: orderBy,
            orderFormat: orderFormat,
            category: category
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
            $('#itemsListTable').html('<p class="text-center text-danger">Error loading items: ' + xhr.status + '</p>');
        }
    });
    
    return false;
}

// Make lilt globally accessible
window.lilt = lilt;

// Reload items when filters change
$('#itemsListPerPage, #itemsListSortBy, #categoryFilter').on('change', function() {
    lilt();
});
