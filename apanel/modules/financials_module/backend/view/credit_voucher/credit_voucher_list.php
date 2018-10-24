<section class="content">
	<!-- Error Message for File Import -->
	<div class="alert alert-danger hidden" id="import_error">
		<button type="button" class="link btn-sm close" >&times;</button>
		<p>Ok, just a few more things we need to adjust for us to proceed :) </p><hr/>
		<ul>

		</ul>
	</div>
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<?
							//echo $ui->CreateNewButton('');
						?>
						<input type="button" id="item_multiple_delete" class="btn btn-danger btn-flat " value="Cancel">
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
					<div class = "col-md-5">
						
					</div>
					<div class="col-md-4">
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
				<h4><strong>Warning!</strong></h4>
				<div id = "errmsg"></div>
				<div id = "warningmsg"></div>
			</div>
			<div class="nav-tabs-custom">
				<ul id="filter_tabs" class="nav nav-tabs">
					<li class="active"><a href="all" data-toggle="tab">All</a></li>
					<li><a href="inactive" data-toggle="tab">Cancelled</a></li>
				</ul>
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
								->addHeader('Date', array('class' => 'col-md-2'), 'sort', 'transactiondate')
								->addHeader('Credit Voucher No', array('class' => 'col-md-2'), 'sort', 'voucherno')
								->addHeader('Customer', array('class' => 'col-md-2'), 'sort', 'partnername')
								->addHeader('Invoice No', array('class' => 'col-md-2'), 'sort', 'invoiceno')
								->addHeader('Reference No', array('class' => 'col-md-2'), 'sort', 'referenceno')
								->addHeader('Balance', array('class' => 'col-md-1'), 'sort', 'balance')
								->addHeader('Amount', array('class' => 'col-md-1'), 'sort', 'amount')
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
		var ajax_call = '';
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', daterangefilter : '#daterangefilter' });
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
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});
		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			ajax_call.abort();
			getList();
		});
		function getList() {
			filterToURL();
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
		function cancelCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
				getList();
			});
		}
		$(function() {
			linkButtonToTable('#item_multiple_delete', '#tableList');
			linkCancelToModal('#tableList .delete', 'cancelCallback');
			linkCancelMultipleToModal('#item_multiple_delete', '#tableList', 'cancelCallback');
		});
		$('#daterangefilter').on('change', function() {
			ajax.page = 1;
			ajax.daterangefilter = $(this).val();
			getList();
		});
		$(".close").click(function() 
		{
			location.reload();
		});
		$('body').on('click','#success_modal .btn-success', function(){
			$('#success_modal').modal('hide');
		});
	</script>