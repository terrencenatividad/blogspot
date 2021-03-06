	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<!-- <a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create User Group</a> -->
									<?= 
									$ui->CreateNewButton('');
									?>
									<!-- <button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete<span></span></button> -->
									<?=	$ui->CreateDeleteButton(''); ?>
									<?=	$ui->CreateActButton(''); ?>
								</div>
							</div>
						</div>
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
				<table id="tableList" class="table table-hover table-sidepad">
					<?php
					echo $ui->loadElement('table')
					->setHeaderClass('info')
					->addHeader(
						'<input type="checkbox" class="checkall">',
						array(
							'class' => 'text-center',
							'style' => 'width: 40px'
						)
					)
					->addHeader('Group Name', array('class' => 'col-md-4'), 'sort', 'groupname', 'asc')
					->addHeader('Description', array('class' => 'col-md-8'), 'sort', 'description')
					->addHeader('Status', array('class' => 'col-md-8'), 'sort', 'stat')
					->draw();
					?>
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
			var ajax = filterFromURL();
			var ajax_call = '';
			ajaxToFilter(ajax, { search : '#table_search', limit : '#items' });
			tableSort('#tableList', function(value, getlist) {
				ajax.sort = value;
				ajax.page = 1;
				if (getlist) {
					getList();
				}
			});
			$('#table_search').on('input', function () {
				ajax.page = 1;
				ajax.search = $(this).val();
				getList();
			});
			$('#items').on('change', function() {
				ajax.limit = $(this).val();
				ajax.page = 1;
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
			function getList() {
				filterToURL();
				if (ajax_call != '') {
					ajax_call.abort();
				}
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
					$('#tableList tbody').html(data.table);
					$('#pagination').html(data.pagination);
					historyOfMyLife();
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
						$('#warning_modal #warning_message').html('<p>Unable to delete User Group: User Group in Use</p>');
						data.error_id.forEach(function(id) {
							$('#warning_modal #warning_message').append('<p>User Group Name: ' + id + '</p>');
						});
						$('#warning_modal').modal('show');
					}
					getList();
				});
			}
			$(function() {
				linkButtonToTable('#item_multiple_delete, #item_multiple_view', '#tableList');
			// linkButtonToTable('#activateMultipleBtn', '#tableList');
			// linkButtonToTable('#deactivateMultipleBtn', '#tableList');
			linkDeleteToModal('#tableList .delete', 'ajaxCallback');
			linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
		});


			var activate_id = '';
			$('#tableList').on('click', '.activate', function() { 
				activate_id = $(this).attr('data-id');
				var decode_activate = atob(activate_id);
				$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+decode_activate ,function(data) {
					getList();
				});
			});
			var deactivate_id = '';
			$('#tableList').on('click', '.deactivate', function() { 
				$('#deactivate_modal').modal('show');
				deactivate_id = $(this).attr('data-id');			
				var decode_deactivate = atob(deactivate_id);

				$('#deactivate_modal').on('click', '#deactyes', function() {
					$('#deactivate_modal').modal('hide');

					$.post('<?=MODULE_URL?>ajax/check_stat', '&id='+decode_deactivate ,function(data) {
						if(data.success == true){
							bootbox.dialog({
								message: "Can't deactivate. User Group in use.",
								title: "Warning",
								buttons: {
									success: {
										label: "Ok",
										className: "btn-info btn-flat",
										callback: function() {

										}
									}
								}
							});
							$('.btn').on('click', function(){
								window.location = data.redirect;
							});
						}else{
							$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+decode ,function(data) {
								window.location = data.redirect;
							});
						}

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
		// console.log(arr);
		$('.checkbox:checked').each(function(){
			id.push(atob($(this).val()));
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