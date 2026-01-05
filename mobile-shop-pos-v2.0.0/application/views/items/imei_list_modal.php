<?php defined('BASEPATH') OR exit(''); ?>
<div id="imeiListModal" class="modal fade" role="dialog" data-backdrop="static">
<div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header"><button class="close" data-dismiss="modal">&times;</button>
<h4 class="text-center"><i class="fa fa-mobile"></i> IMEI Management</h4>
<div class="text-center"><strong id="imeiModalItemName"></strong> <span class="text-muted" id="imeiModalItemCode"></span></div></div>
<div class="modal-body">
<div class="row" style="margin-bottom:15px;">
<div class="col-sm-4"><label for="imeiStatusFilter">Filter:</label>
<select id="imeiStatusFilter" class="form-control input-sm">
<option value="">All</option><option value="available">Available</option><option value="sold">Sold</option>
<option value="reserved">Reserved</option><option value="defective">Defective</option></select></div>
<div class="col-sm-8 text-right"><div id="imeiCountSummary"></div></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped table-hover" id="imeiListTable">
<thead><tr><th>#</th><th>IMEI</th><th>Color</th><th>Cost</th><th>Status</th><th>Sold Date</th><th>Actions</th></tr></thead>
<tbody id="imeiListTableBody"></tbody></table></div>
<div id="imeiListLoading" class="text-center" style="display:none;"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>
<div id="imeiListEmpty" class="text-center text-muted" style="display:none;"><i class="fa fa-inbox fa-3x"></i><p>No IMEIs found</p></div>
</div><div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button></div>
</div></div></div>
<script>
$(document).ready(function(){let a=null;$(document).on('click','.view-imeis',function(b){b.preventDefault(),a=$(this).data('item-id');const c=$(this).data('item-name'),d=$(this).closest('tr').find('[id^="itemCode-"]').text();$('#imeiModalItemName').text(c),$('#imeiModalItemCode').text('('+d+')'),$('#imeiStatusFilter').val(''),e(a),$('#imeiListModal').modal('show')});$('#imeiStatusFilter').on('change',function(){a&&e(a,$(this).val())});function e(b,c=''){$('#imeiListLoading').show(),$('#imeiListTableBody').empty(),$('#imeiListEmpty').hide(),$.ajax({url:appRoot+'items/getItemSerials',type:'GET',data:{itemId:b,status:c},dataType:'json',success:function(d){$('#imeiListLoading').hide(),d.status===1&&d.serials&&d.serials.length>0?(f(d.serials),g(d.serials)):($('#imeiListEmpty').show(),$('#imeiCountSummary').html('<span class="text-muted">No IMEIs</span>'))},error:function(){$('#imeiListLoading').hide(),alert('Error loading IMEIs')}})}function f(b){let c='',d=1;b.forEach(function(e){const f=h(e.status),g=e.sold_date?new Date(e.sold_date).toLocaleDateString():'-';c+=`<tr><td>${d}</td><td><strong>${e.imei_number}</strong></td><td>${e.color||'-'}</td><td>Rs. ${parseFloat(e.cost_price).toLocaleString()}</td><td>${f}</td><td>${g}</td><td>${e.status==='available'?`<button class="btn btn-xs btn-warning"><i class="fa fa-exclamation-triangle"></i> Defective</button>`:'-'}</td></tr>`,d++}),$('#imeiListTableBody').html(c)}function h(b){const c={available:'<span class="label label-success">Available</span>',sold:'<span class="label label-primary">Sold</span>',reserved:'<span class="label label-warning">Reserved</span>',defective:'<span class="label label-danger">Defective</span>'};return c[b]||b}function g(b){const c={available:0,sold:0,defective:0};b.forEach(function(d){c.hasOwnProperty(d.status)&&c[d.status]++});const d=`<span class="label label-success">Available: ${c.available}</span> <span class="label label-primary">Sold: ${c.sold}</span> <span class="label label-danger">Defective: ${c.defective}</span> <span class="label label-default">Total: ${b.length}</span>`;$('#imeiCountSummary').html(d)}});
</script>
