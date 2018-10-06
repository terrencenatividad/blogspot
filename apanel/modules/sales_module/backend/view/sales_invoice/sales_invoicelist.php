	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-8">
						<?
							echo $ui->CreateNewButton('');
						?>
						<button type="button" id="item_multiple_cancel" class="btn btn-danger btn-flat">Cancel<span></span></button>
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
										->setPlaceholder('Filter Customer')
										->setName('customer')
										->setId('customer')
										->setList($customer_list)
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
		</div>
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li class="active"><a href="all" data-toggle="tab">All</a></li>
				<!-- <li><a href="approval" data-toggle="tab">For Approval</a></li> -->
				<li><a href="unpaid" data-toggle="tab">Unpaid</a></li>
				<li><a href="partial" data-toggle="tab">Partial</a></li>
				<li><a href="paid" data-toggle="tab">Paid</a></li>
				<li><a href="cancelled" data-toggle="tab">Cancelled</a></li>
			</ul>
			<div class="table-responsive">
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
								->addHeader('Invoice Date', array('class' => 'col-md-2'), 'sort', 'inv.transactiondate')
								->addHeader('Invoice No.', array('class' => 'col-md-2'), 'sort', 'inv.voucherno', 'desc')
								->addHeader('Customer', array('class' => 'col-md-4'), 'sort', 'cust.partnername')
								->addHeader('Amount', array('class' => 'col-md-1'), 'sort', 'inv.amount')
								->addHeader('Balance', array('class' => 'col-md-1'), 'sort', 'app.balance')
								->addHeader('Status', array('class' => 'col-md-1'), 'sort', 'inv.stat')
								->draw();
					?>
					<tbody>
						<tr>
							<td colspan="7" class="text-center"><b>No Records Found</b></td>
						</tr>
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
					<p>Are you sure you want to cancel this record?</p>
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
		ajax.filter = $('#filter_tabs .active a').attr('href');
		ajax.limit 	= $('#items').val();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter' });

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
			ajax_call.abort();
			getList();
		});
		$('#items').on('change', function() {
			ajax.page = 1;
			ajax.limit = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#customer').on('change', function() {
			ajax.page = 1;
			ajax.customer = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			ajax_call.abort();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
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
		function processInvoice(id, type) {
			//var ids = getIds(id);
			$.post('<?=MODULE_URL?>ajax/apply_approve/'+type, {voucherno : id}, function(data) {
				getList();
			});
		}
		function approveInvoice(id) {
			processInvoice(id, 'yes');
		}
		function disapproveInvoice(id) {
			processInvoice(id, 'no');
		}
		function getIds(ids) {
			var x = ids.split(",");
			return "id[]=" + x.join("&id[]=");
		}
		$(function() {
			linkButtonToTable('#item_multiple_cancel', '#tableList');
			linkCancelToModal('#tableList .delete', 'ajaxCallback');
			linkCancelMultipleToModal('#item_multiple_cancel', '#tableList', 'ajaxCallback');

			createConfimationLink('#tableList .approve', 'approveInvoice', 'Are you sure you want to approve this invoice?');
			createConfimationLink('#tableList .disapprove', 'disapproveInvoice', 'Are you sure you want to disapprove this invoice?');
		});
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax_call.abort();
			getList();
		})
		$('#tableList').on('click', '.print_invoice',function(){
			var voucher = $(this).attr('data-id');
			//window.location = '<?=MODULE_URL?>print_invoice/' + voucher;
			url = '<?=MODULE_URL?>print_invoice/' + voucher;
			var win = window.open(url, '_blank');
  			win.focus();
		});
	</script>