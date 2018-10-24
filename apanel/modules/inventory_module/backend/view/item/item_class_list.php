	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<!-- <div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<div class="btn-group">
										<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Item Class</a>
										<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu">
											<li><a href="<?= MODULE_URL ?>get_export" id="export_table" download="item_class.csv"><i class="glyphicon glyphicon-open"></i>Export Item Class/s</a></li>
											<li><a href="#import-modal" data-toggle="modal"><i class="glyphicon glyphicon-save"></i>Import Item Class/s</a></li>
										</ul>
									</div>
									<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete<span></span></button>
								</div>
							</div>
						</div> -->
						<?= 
							$ui->CreateNewButton('');
						?>
						<?= 
							$ui->OptionButton('');
						?>
						<input id = "item_multiple_delete" type = "button" name = "delete" 
						value = "Delete" class="btn btn-danger btn-flat ">
						<input id = "activateMultipleBtn" type = "button" name = "activate" value = "Activate" class="btn btn-success btn-flat ">
						<input id = "deactivateMultipleBtn" type = "button" name = "deactivate" value = "Deactivate" class="btn btn-warning btn-flat ">
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<div class="input-group">
								<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-btn">
									<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
								</div>
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
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'text-center',
										'style' => 'width: 100px'
									)
								)
								->addHeader('Class Name')
								->addHeader('Parent Name')
								->addHeader('Status')
								->draw();
					?>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
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
	<!-- Import Modal -->
	<div class="modal fade" id="import-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" id="importForm">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
						<h4 class="modal-title">Import Item/s</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="item_class_csv_template.csv">here</a></label>
						<hr>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr>
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
						<button type="submit" class="btn btn-info btn-flat">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		var ajax = filterFromURL();
		var ajax_call = '';
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items' });
		$('#table_search').on('input', function () {
			ajax.search = $(this).val();
			var url = '<?= MODULE_URL ?>get_export/';
			$('#export_id').attr('href', url + btoa($(this).val()));
			getList();
		});
		$('#import-modal').on('show.bs.modal', function() {
			var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
			$('#import_csv').closest('.form-group').html(form_csv);
		});
		$('#importForm').on('change', '#import_csv', function() {
			var filename = $(this).val().split("\\");
			$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
		});
		function addError(error, clean) {
			if (clean) {
				$('#warning_modal .modal-body').html(error);
			} else {
				$('#warning_modal .modal-body').append(error);
			}
		}
		$('#importForm').submit(function(e) {
			e.preventDefault();
			var form_element = $(this);
			form_element.find('.form-group').find('input').trigger('blur');
			if (form_element.find('.form-group.has-error').length == 0) {
				form_element.find('[type="submit"]').addClass('disabled');
				var formData =	new FormData();
				formData.append('file',$('#import_csv')[0].files[0]);
				$.ajax({
					url : 			'<?=MODULE_URL?>ajax/ajax_save_import',
					data:			formData,
					cache: 			false,
					processData:	false, 
					contentType:	false,
					type: 			'POST',
					success: 		function(data){
						form_element.find('[type="submit"]').removeClass('disabled');
						form_element.closest('.modal').modal('hide');
						if (data.success) {
							getList();
							$('#import-modal').modal('hide');
							show_success_msg("Your data has been successfully imported!");
						} else {
							addError('<p>' + data.error + '</p>', true);
							try {
								data.duplicate['Item Class'].forEach(function(item_class) {
									addError('<p><b>Duplicate Value</b>: "' + item_class + '"</p>');
								});
							} catch(e) {}
							try {
								data.exist['Item Class'].forEach(function(item_class) {
									addError('<p><b>Exist Value</b>: "' + item_class + '"</p>');
								});
							} catch(e) {}
							try {
								let invalid = data.invalid;
								for (var key in invalid) {
									if (invalid.hasOwnProperty(key)) {
										let invalid2 = invalid[key];
										for (var key2 in invalid2) {
											if (invalid2.hasOwnProperty(key2)) {
												addError('<p><b>Invalid ' + key + '</b>: ' + invalid2[key2] + '</p>');
											}
										}
									}
								}
							} catch(e) {}
							try {
								let validity = data.validity;
								for (var key in validity) {
									if (validity.hasOwnProperty(key)) {
										let validity2 = validity[key];
										for (var key2 in validity2) {
											if (validity2.hasOwnProperty(key2)) {
												addError('<p><b>' + key + ' Field</b>: ' + key2 + '</p>');
											}
										}
									}
								}
							} catch(e) {}
							$('#warning_modal').modal('show');
						}
					}
				});
			}
		});
		function getList() {
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();
		function ajaxCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
				if ( ! data.success) {
					$('#warning_modal #warning_message').html('<p>Unable to delete Item Class: Item Class in Use</p>');
					data.error_id.forEach(function(id) {
						$('#warning_modal #warning_message').append('<p>Item Class ID: ' + id + '</p>');
					});
					$('#warning_modal').modal('show');
				}
				getList();
			});
		}
		$(function() {
			linkButtonToTable('#item_multiple_delete', '#tableList');
			linkButtonToTable('#activateMultipleBtn', '#tableList');
			linkButtonToTable('#deactivateMultipleBtn', '#tableList');
			linkDeleteToModal('#tableList .delete', 'ajaxCallback');
			linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
		});

		$('#items').on('change', function(){
			ajax.page = 1;
			ajax.limit = $(this).val();
			getList();
		});
		var href = '<?= MODULE_URL ?>get_export';

		$('#export_id').prop('download','item_class.csv');
		$('#export_id').prop('href','<?= MODULE_URL ?>get_export');
		$('#import_id').prop('href','#import-modal');
		// $('#import_id').prop('data-toggle','modal');
		$("#import_id").click(function() 
		{
			$("#import-modal > .modal").css("display", "inline");
			$('#import-modal').modal();
		});

		$('#tableList').on('click', '.activate', function() { 
			var id = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+id ,function(data) {
				getList();
			});
		});

		function show_success_msg(msg)
		{
			$('#success_modal #message').html(msg);
			$('#success_modal').modal('show');
			setTimeout(function() {												
				window.location = '<?= MODULE_URL ?>';		
			}, 1000)
		}
		
		$('#tableList').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+id ,function(data) {
					getList();
				});
			});
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


	</script>