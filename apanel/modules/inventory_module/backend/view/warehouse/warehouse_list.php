<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<!-- <a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Warehouse</a>
						<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete <span></span></button>
				
						<div class="btn btn-group" id="option_buttons">
							<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
								Options <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href = "#" id="export" download="Warehouse.csv" ><span class="glyphicon glyphicon-open"></span> Export</a>
								</li>
								<li>
									<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import</a>
								</li>
							</ul>
						</div> -->
						<?= 
							$ui->CreateNewButton('');
						?>
						<?= 
							$ui->OptionButton('');
						?>
						<?=	$ui->CreateDeleteButton(''); ?>
						<?=	$ui->CreateActButton(''); ?>
					</div>
				</div>
				<div class="col-md-4 pull-right">
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

		<div class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><strong>Error!</strong></h4>
			<div id = "errmsg"></div>
		</div>

		<div class="box-body table-responsive no-padding">
			<table id="tableList" class="table table-hover">
				<thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'text-center col-md-1'
									)
								)
								->addHeader('Warehouse Code',array('class'=>'col-md-3'),'sort','warehousecode','asc')
								->addHeader('Warehouse Name', array('class'=>'col-md-3'),'sort','description','asc')
								->addHeader('Status', array('class'=>'col-md-3'),'sort','stat','asc')								
								->draw();
					?>
				</thead>
				<tbody>

				</tbody>
			</table>
			<div id = "pagination"></div>
		</div>
	</div>
	<div id="pagination"></div>
</section>

<!-- Import Modal -->
<div class="import-modal" id="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Warehouse</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="Warehouse_Template.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<?php
								echo $ui->setElement('file')
										->setId('import_csv')
										->setName('import_csv')
										->setAttribute(array('accept' => '.csv'))
										->setValidation('required')
										->draw();
							?>
							<span class="help-block"></span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var ajax = filterFromURL();
		ajaxToFilter(ajax,{ search: '#table_search', limit: '#items'})
	var ajax_call = '';	

	$('#table_search').on('keyup', function () {
		ajax.page = 1;
		ajax.search = $(this).val();
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		$('.checked').iCheck('uncheck');
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});
	$('#items').on('change', function(){
		ajax.page = 1;
		ajax.limit = $(this).val();
		getList();
	});
	function show_error(msg)
	{
		$(".delete-modal").modal("hide");
		$(".alert-warning").removeClass("hidden");
		$("#errmsg").html(msg);
	}
	function show_success_msg(msg)
	{
		$('#success_modal #message').html(msg);
		$('#success_modal').modal('show');
		setTimeout(function() {												
			window.location = '<?= MODULE_URL ?>';		
		}, 1000)
	}
	function getList() {
		filterToURL();
		if (ajax_call != '') {
			ajax_call.abort();
		}
		ajax_call = $.post('<?=MODULE_URL?>ajax/warehouse_list', ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#pagination').html(data.pagination);
			historyOfMyLife();
			$("#export_id").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			if (ajax.page > data.page_limit && data.page_limit > 0) {
				ajax.page = data.page_limit;
				getList();
			}
		});
	}
	getList();
	function ajaxCallback(id) {
		var ids = getDeleteId(id);
		$.post('<?=BASE_URL?>maintenance/warehouse/ajax/delete', 'id=' + id, function(data) 
		{
			if( data.msg == 'success' )	
			{
				getList();
				$(".alert-warning").addClass("hidden");
			}
			else
			{
				// Call function to display error_get_last
				show_error(data.msg);
			}
		});
	}
	$(function() {
		linkButtonToTable('#item_multiple_delete', '#tableList');
		// linkButtonToTable('#activateMultipleBtn', '#tableList');
		// linkButtonToTable('#deactivateMultipleBtn', '#tableList');
		linkDeleteToModal('#tableList .delete', 'ajaxCallback');
		linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
	});

	// Sorting Script
	tableSort('#tableList', function(value, getlist) {
		ajax.sort = value;
		ajax.page = 1;
		if(getlist)
		{
			getList();
		}
	},ajax);

	$(document).ready(function(){
		/** For Import Modal **/
		$("#import_id").click(function() 
		{
			$(".import-modal > .modal").css("display", "inline");
			$('.import-modal').modal();
		});
		
		$("#importForm #btnImport").click(function() 
		{
			var formData =	new FormData();
			formData.append('file',$('#import_csv')[0].files[0]);
			ajax_call 	=	$.ajax({
								url : '<?=MODULE_URL?>ajax/save_import',
								data:	formData,
								cache: 	false,
								processData: false, 
								contentType: false,
								type: 	'POST',
								success: function(response){
									if(response && response.errmsg == ""){
										$('#import-modal').modal('hide');
										$(".alert-warning").addClass("hidden");
										$("#errmsg").html('');
										show_success_msg('Your data has been successfully imported!');
									}else{
										$('#import-modal').modal('hide');
										show_error(response.errmsg);
									}
								},
							});
		});
		var activate_code = '';
		$('#tableList').on('click', '.activate', function() { 
			activate_code = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&code='+activate_code ,function(data) {
				getList();
			});
		});
		var deactivate_code = '';
		$('#tableList').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			deactivate_code = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&code='+deactivate_code ,function(data) {
					getList();
				});
			});
		});

		$('#importForm').on('change', '#import_csv', function() {
			var filename = $(this).val().split("\\");
			$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
		});

		$('#import-modal').on('show.bs.modal', function() {
			var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
			$('#import_csv').closest('.form-group').html(form_csv);
		});
		
		$('#success_modal .btn-success').on('click', function(){
			$('#success_modal').modal('hide');
			getList();
		});

		$('#export_id').prop('download','warehouse.csv');
	});

$("#deactivateMultipleBtn").click(function() 
	{
	var id = [];

		$('input:checkbox.item_checkbox:checked').each(function()
		{
			id.push($(this).val());
		});
		
		if( id != "" )
		{
			$('#multipleDeactivateModal').modal('show');
			$( "#multipleDeactivateModal #btnDeac" ).click(function() {
			ids 	=	getSelectedIds();
			$.post('<?=MODULE_URL?>ajax/update_multiple_deactivate', "&ids="+ids ,function(data) {
				
				if( data.msg == 'success' )
				{
					$('.checked').iCheck('uncheck');
					getList();
					$('#multipleDeactivateModal').modal('hide');
				} 
			});
		});
		}
	});

	$("#activateMultipleBtn").click(function() 
	{
		var id = [];

		$('input:checkbox.item_checkbox:checked').each(function()
		{
			id.push($(this).val());
		});

		if( id != "" )
		{
			$('#multipleActivateModal').modal('show');
			$( "#multipleActivateModal #btnYes" ).click(function() {
			ids 	=	getSelectedIds();
			$.post('<?=MODULE_URL?>ajax/update_multiple_activate', "&ids="+ids ,function(data) {
				if( data.msg == 'success' )
				{
					$('.checked').iCheck('uncheck');
					getList();
					$('#multipleActivateModal').modal('hide');
				} 
			});
		});
		}
	});

	function getSelectedIds(){
		id 	=	[];
		$('.checkbox:checked').each(function(){
			id.push($(this).val());
		});
		return id;
	}

	$('#tableList').on('ifToggled', 'input[type=checkbox]:not(.checkall)', function() {
			var b = $('input[type=checkbox]:not(.checkall)');
			var row = $('#tableList >tbody >tr').length;
			var c =	b.filter(':checked').length;
			if(c == row){
				$('#tableList thead tr th').find('.checkall').prop('checked', true).iCheck('update');
			}else{
				$('#tableList thead tr th').find('.checkall').prop('checked', false).iCheck('update');
			}
		});

function historyOfMyLife() {
	var arr = [];
	$('#tableList tbody').find('.label').each(function(index, value){
		arr.push($(this).html());
		if(jQuery.inArray('ACTIVE', arr) != -1) {
			$('#deactivateMultipleBtn').attr('disabled', false);
		}else{
			$('#deactivateMultipleBtn').attr('disabled', true);
		}
		if(jQuery.inArray('INACTIVE', arr) != -1) {
			$('#activateMultipleBtn').attr('disabled', false);
		}else{
			$('#activateMultipleBtn').attr('disabled', true);
		}
	});
}
</script>
