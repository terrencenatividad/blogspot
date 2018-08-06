	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-8">
						<!-- <div class="form-group">
							<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create New Credit Memo</a>
							<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Cancel<span></span></button>
						</div> -->
						<?
							echo $ui->CreateNewButton('');
							// echo $ui->OptionButton('');
						?>
						<button type="button" id="item_multiple_delete" class="btn btn-danger btn-flat">Cancel<span></span></button>
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
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Partner')
										->setName('partner')
										->setId('partner')
										->setList($partner_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="row">
							<div class="col-sm-8 col-xs-6 text-right">
								<label for="" class="padded">Items: </label>
							</div>
							<div class="col-sm-4 col-xs-6">
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
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<!--<thead>
						<tr class="info">
							<th class="text-center" style="width: 15px"><input type="checkbox" class="checkall"></th>
							<th>Document Date</th>
							<th>Voucher Number</th>
							<th>Vendor</th>
							<th>Reference</th>
							<th>Total Amount</th>
						</tr>
					</thead>-->
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
								->addHeader('Transaction Date', array('class' => 'col-md-3'), 'sort', 'transactiondate')
								->addHeader('Voucher Number', array('class' => 'col-md-3'), 'sort', 'voucherno', 'desc')
								->addHeader('Partner', array('class' => 'col-md-3'), 'sort', 'partner', '')
								->addHeader('Reference', array('class' => 'col-md-2'), 'sort', 'referenceno')
								->addHeader('Total Amount', array('class' => 'col-md-1 text-right'), 'sort', 'amount')
								->addHeader('Status', array('class' => 'col-md-1 text-right'), 'sort', 'status')
								->draw();
					?>
					<tbody>

					</tbody>
					<!--<tfoot>
						<tr>
							<td colspan="9">Showing 1 to 25 of 57 entries</td>
						</tr>
					</tfoot>-->
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
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', partner : '#partner', daterangefilter : '#daterangefilter' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		}, ajax);
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
			ajax.page = $(this).attr('data-page');
			getList();
		});
		function getList() {
			filterToURL();
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
			// linkDeleteToModal('#tableList .delete', 'ajaxCallback');
			linkCancelToModal('#tableList .delete', 'ajaxCallback');
			linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
		});
		$('#daterangefilter').on('change', function() {
			ajax.page = 1;
			ajax.daterangefilter = $(this).val();
			ajax_call.abort();
			getList();
		})
		$('#partner').on('change', function() {
			ajax.page = 1;
			ajax.partner = $(this).val();
			ajax_call.abort();
			getList();
		})
	</script>