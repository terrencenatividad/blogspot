	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Add Content</a>
							<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete<span></span></button>
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
							'style' => 'width: 15px'
						)
					)
					->addHeader('Title', array('class' => 'col-md-5'), 'sort', 'title', 'asc')
					->addHeader('Content', array('class' => 'col-md-6'), 'sort', 'content', 'asc')
					->addHeader('Status', array('style' => 'col-md-2'), 'sort', 'status')
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

		<div class="modal modal-warning" id = "unpublish_modal">
			<div class="modal-dialog" style = "width: 300px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Confirmation</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to unpublish this record?</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-outline btn-flat" id = "unpublish-yes">Yes</button>
						<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			var ajax = filterFromURL();
			var ajax_call = '';
			ajaxToFilter(ajax, { search : '#table_search', limit : '#items' });
			function changeExportLink() {
				var url = '<?= MODULE_URL ?>get_export/';
				$('#export_table').attr('href', url + btoa(ajax.search || '') + '/' + btoa(ajax.sort || ''));
			}
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
				changeExportLink();
				if (ajax_call != '') {
					ajax_call.abort();
				}
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

			function show_success_msg(msg)
			{
				$('#success_modal #message').html(msg);
				$('#success_modal').modal('show');
				setTimeout(function() {												
					window.location = '<?= MODULE_URL ?>';		
				}, 1000)
			}

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
			var unpublish_id = '';
			var publish_id = '';
			$('#tableList tbody').on('click', '.unpublish', function() {
				unpublish_id = $(this).attr('data-id');
				$('#unpublish_modal').modal('show');
			});

			$('#unpublish-yes').on('click', function() {
				$.post('<?=MODULE_URL?>ajax/ajax_update_stat', '&id=' + unpublish_id + '&status=unpublished', function(data) {
					if(data) {
						$('#unpublish_modal').modal('hide');
						getList();
					}
				});
			});

			$('#tableList tbody').on('click', '.publish', function() {
				publish_id = $(this).attr('data-id');
				$.post('<?=MODULE_URL?>ajax/ajax_update_stat', '&id=' + publish_id + '&status=published', function(data) {
					if(data) {
						getList();
					}
				});
			});
		</script>