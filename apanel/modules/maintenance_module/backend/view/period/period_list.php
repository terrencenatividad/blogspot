	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Period</a>
									<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete<span></span></button>
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
				<!--<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th class="text-center" style="width: 15px"><input type="checkbox" class="checkall"></th>
							<th class = "col-md-2 text-center">Period</th>
							<th class = "col-md-2 text-center">Fiscal Year</th>
							<th class = "col-md-3 text-center">Start Date</th>
							<th class = "col-md-3 text-center">End date</th>
							<th class = "col-md-3 text-center">Status</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>-->
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
							->addHeader('Period', array('class' => 'col-md-3'), 'sort', 'period')
							->addHeader('Fiscal Year', array('class' => 'col-md-3'), 'sort', 'fiscalyear')
							->addHeader('Start Date', array('class' => 'col-md-3'), 'sort', 'startdate')
							->addHeader('End date', array('class' => 'col-md-3'), 'sort', 'enddate')
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
	</script>