<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<!-- <a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Unit of Measure</a> -->
								<?= 
									$ui->CreateNewButton('');
								?>
								<!-- <button type="button" id="item_multiple_delete" class="btn btn-danger">Delete<span></span></button> -->
								<input id = "item_multiple_delete" type = "button" name = "delete" 
								value = "Delete" class="btn btn-danger btn-flat ">
			
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="input-group">
						<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
			<div class="col-md-4 col-md-offset-8">
				<div class="row">
					<div class="col-sm-8 col-xs-6 text-right">
						<label for="" class="padded">Items: </label>
					</div>
					<div class="col-sm-4 col-xs-6">
						<div class="form-group">
							<select id="items">
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="100">100</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		</div>
		<div class="box-body table-responsive no-padding">
			<table id="tableList" class="table table-hover">
				<thead>
					<tr class="info">
						<th class="col-md-1 text-center"><input type="checkbox" class="checkall"></th>
						<th class="col-md-2">Unit Code</th>
						<th class="col-md-8">Unit Description</th>
						<th class="col-md-1">Unit Type</th>
						<th class="col-md-1">Status</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
	<div id="pagination"></div>
</section>
<div class="delete-modal">
	<div class="modal modal-danger">
		<div class="modal-dialog" style = "width: 300px;">
			<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
				<p>Are you sure you want to delete this record?</p>
				</div>
				<div class="modal-footer text-center">
					<button type="button" class="btn btn-outline btn-flat" id = "delete-yes">Yes</button>
					<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var ajax = {}
	var ajax_call = {};
	$('#table_search').on('input', function () {
		ajax.page = 1;
		ajax.search = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#classid').on('change', function() {
		ajax.page = 1;
		ajax.classid = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#typeid').on('change', function() {
		ajax.page = 1;
		ajax.typeid = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	})
	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#pagination').html(data.pagination);
		});
	}
	getList();
	function ajaxCallback(id) {
		var ids = getDeleteId(id);
		$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
			getList();
		});
	}
	$(function() {
		linkButtonToTable('#item_multiple_delete', '#tableList');
		linkDeleteToModal('#tableList .delete', 'ajaxCallback');
		linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
	});

	$('#items').on('change', function(){
		ajax.page = 1;
		ajax.limit = $(this).val();
		getList();
	});

	$('#tableList').on('click', '.activate', function() { 
			var code = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&uomcode='+code ,function(data) {
				getList();
			});
		});

		$('#tableList').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&uomcode='+id ,function(data) {
					getList();
				});
			});
		});
</script>