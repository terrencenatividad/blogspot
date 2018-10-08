<section class="content">
<div class="box box-primary">
	<div class="box-header pb-none">
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
						<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" readonly data-daterangefilter="month">
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
								->setPlaceholder('Filter Supplier')
								->setName('vendor')
								->setId('vendor')
								->setList($vendor_list)
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
		<li><a href="Returned" data-toggle="tab">Returned</a></li>
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
						->addHeader('Transaction Date', array('class' => 'col-md-3'), 'sort', 'transactiondate')
						->addHeader('Purchase Return No.', array('class' => 'col-md-3'), 'sort', 'voucherno', 'desc')
						->addHeader('Supplier', array('class' => 'col-md-3'), 'sort', 'vendor')
						->addHeader('Purchase Receipt No.', array('class' => 'col-md-3'), 'sort', 'source_no')
						->addHeader('Status', array('style' => 'width: 15px'), 'sort', 'pr.stat')
						->draw();
			?>
			<tbody>

			</tbody>
		</table>
	</div>
</div>
<div id="pagination"></div>
</section>
<script>
var ajax = filterFromURL();
var ajax_call = '';
ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
ajaxToFilter(ajax, { search : '#table_search', limit : '#items', vendor : '#vendor', daterangefilter : '#daterangefilter' });
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
$('#vendor').on('change', function() {
	ajax.page = 1;
	ajax.vendor = $(this).val();
	getList();
});
$('#filter_tabs li').on('click', function() {
	ajax.page = 1;
	ajax.filter = $(this).find('a').attr('href');
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
function getIds(ids) {
	var x = ids.split(",");
	return "id[]=" + x.join("&id[]=");
}
$(function() {
	linkButtonToTable('#item_multiple_cancel', '#tableList');
	linkCancelToModal('#tableList .delete', 'ajaxCallback');
	linkCancelMultipleToModal('#item_multiple_cancel', '#tableList', 'ajaxCallback');
});
$('#daterangefilter').on('change', function() {
	ajax.daterangefilter = $(this).val();
	getList();
})
</script>