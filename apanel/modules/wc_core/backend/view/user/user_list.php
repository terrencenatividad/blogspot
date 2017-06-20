	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create User</a>
									<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete<span></span></button>
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
										'style' => 'width: 15px'
									)
								)
								->addHeader('Username', array('class' => 'col-md-4'), 'sort', 'username', 'asc')
								->addHeader('Name', array('class' => 'col-md-2'), 'sort', 'firstname, lastname')
								->addHeader('E-mail Address', array('class' => 'col-md-2'), 'sort', 'email')
								->addHeader('Group Name', array('class' => 'col-md-2'), 'sort', 'wu.groupname')
								->addHeader('Status', array('style' => 'width: 15px'), 'sort', 'stat')
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
		var ajax = {}
		var ajax_call = {};
		$('#tableList').on('click', 'a[data-sort]', function() {
			var field = $(this).attr('data-field');
			var sort = $(this).attr('data-sort');
			var fields = field.split(',');
			$(this).closest('tr').find('a[data-sort]').attr('data-sort', '');
			var new_sort = '';
			if (sort != 'asc') {
				new_sort = 'asc';
			} else {
				new_sort = 'desc';
			}
			$(this).attr('data-sort', new_sort);
			ajax.sort = fields.join(' ' + new_sort + ', ') + ' ' + new_sort;
			ajax.page = 1;
			getList();
		});
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
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
					$('#warning_modal #warning_message').html('<p>Unable to delete User: User in Use</p>');
					data.error_id.forEach(function(id) {
						$('#warning_modal #warning_message').append('<p>Username: ' + id + '</p>');
					});
					$('#warning_modal').modal('show');
				}
				getList();
			});
		}
		$(function() {
			linkButtonToTable('#item_multiple_delete', '#tableList');
			linkDeleteToModal('#tableList .delete', 'ajaxCallback');
			linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
		});
	</script>