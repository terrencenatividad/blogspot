	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Delivery Receipt</a>
							<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Cancel<span></span></button>
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
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" readonly data-daterangefilter="month" data-class="testingclass">
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
		</div>
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li class="active"><a href="all" data-toggle="tab">All</a></li>
				<li><a href="Prepared" data-toggle="tab">Prepared</a></li>
				<li><a href="Delivered" data-toggle="tab">Delivered</a></li>
				<li><a href="With Invoice" data-toggle="tab">With Invoice</a></li>
				<li><a href="Cancelled" data-toggle="tab">Cancelled</a></li>
			</ul>
			<div class="table-responsive">
				<table id="tableList" class="table table-hover table-sidepad">
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
								->addHeader('Traction Date', array('class' => 'col-md-2'), 'sort', 'transactiondate')
								->addHeader('Delivery Receipt No.', array('class' => 'col-md-3'), 'sort', 'voucherno', 'desc')
								->addHeader('Customer', array('class' => 'col-md-3'), 'sort', 'customer')
								->addHeader('Packing No.', array('class' => 'col-md-2'), 'sort', 'packing_no')
								->addHeader('Delivery Date', array('class' => 'col-md-2'), 'sort', 'deliverydate')
								->addHeader('Status', array('style' => 'width: 15px'), 'sort', 'dr.stat')
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
		var ajax_call = '';
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');

		ajax.filter = $('#filter_tabs .active a').attr('href');
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
		$('#customer').on('change', function() {
			ajax.page = 1;
			ajax.customer = $(this).val();
			getList();
		});
		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
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
		function ajaxCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
				getList();
			});
		}
		function tagDeliveredCallback(id) {
			var ids = getIds(id);
			$.post('<?=MODULE_URL?>ajax/ajax_update_tagdelivered', ids, function(data) {
				getList();
			});
		}
		function untagDeliveredCallback(id) {
			var ids = getIds(id);
			$.post('<?=MODULE_URL?>ajax/ajax_update_untagdelivered', ids, function(data) {
				getList();
			});
		}
		function getIds(ids) {
			var x = ids.split(",");
			return "id[]=" + x.join("&id[]=");
		}
		$(function() {
			linkButtonToTable('#item_multiple_delete', '#tableList');
			linkCancelToModal('#tableList .delete', 'ajaxCallback');
			linkCancelMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
			createConfimationLink('#tableList .tag_as_delivered', 'tagDeliveredCallback');
			createConfimationLink('#tableList .remove_delivered_tag', 'untagDeliveredCallback');
		});
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			getList();
		})
	</script>