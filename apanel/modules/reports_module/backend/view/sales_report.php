<section class="content">
<div class="box box-primary">
	<div class="box-header pb-none">
		<div class="row">
			<div class="col-md-2 col-sm-4 col-xs-6">
				<?php
					echo $ui->formField('dropdown')
						->setPlaceholder('Filter Year')
						->setName('year_filter')
						->setId('year_filter')
						->setList($year_list)
						->setValue($year)
						->draw();
				?>
			</div>
			<div class="col-md-3">
				<?php 
					echo $ui->formField('dropdown')
						->setPlaceholder('Select Customer')
						->setName('customer')
						->setId('customer')
						->setNone('All')
						->setList($customer_list)
						->draw(true);
				?>
			</div>
			<div class="col-md-1 pull-right">
				<a href="" id="export" download="sales_report.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
			</div>
		</div>
	</div>
</div>
<div class="nav-tabs-custom">
	<div class="tab-content no-padding">
		<div id="Yearly" class="tab-pane active">
			<table id="tableListYearly" class="table table-hover table-striped table-sidepad report_table text-right">
				<thead>
					<?php if (($taxyear == 'fiscal' && $periodstart == 'Jan') || ($taxyear == 'calendar')) { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Feb') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Mar') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Apr') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'May') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Jun') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Jul') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Aug') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Sep') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Oct') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Nov') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Nov</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
						</tr>
					<?php } else if ($taxyear == 'fiscal' && $periodstart == 'Dec') { ?>
						<tr class="info">
							<th style="col-xs-4">Customer</th>
							<th class="text-right">Dec</th>
							<th class="text-right">Jan</th>
							<th class="text-right">Feb</th>
							<th class="text-right">Mar</th>
							<th class="text-right">Apr</th>
							<th class="text-right">May</th>
							<th class="text-right">Jun</th>
							<th class="text-right">Jul</th>
							<th class="text-right">Aug</th>
							<th class="text-right">Sep</th>
							<th class="text-right">Oct</th>
							<th class="text-right">Nov</th>
						</tr>
					<?php } ?>
				</thead>
					<?php //echo $year_view ?>
				<tbody>
				</tbody>
			</table>
			<div id= "main_pagination"></div>
		</div>
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
<div class="modal fade" id="daily_modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-8">
						<p><span><strong>Customer:  </strong></span><span id="current_customer"></span></p>
						<p><span><strong>Address:  </strong></span><span id="current_address"></span></p>
					</div>
					<div class="col-md-4">
						<a href="" id="export_daily" download="detailed_sales_report.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
			</div>
			<div class="modal-body no-pad">
				<table id="tableModalList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th>Date</th>
							<th>Sales Invoice No.</th>
							<th>Reference</th>
							<th>AR No.</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				<div id="pagination"></div>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 right">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var ajax = {};
var ajax_main 	=	{};
var ajax_call = {};

var year_filter = $('#year_filter').val();

function getMainList(){
	ajax_call = $.post('<?=MODULE_URL?>ajax/main_listing', ajax_main, function(data) {
		$('#tableListYearly tbody').html(data.table);
		$("#Yearly #main_pagination").html(data.pagination);
		$("#export").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));;
	});
}
getMainList();

$('#tableListYearly tbody').on('click', 'a', function() {
	var path = $(this).attr('data-id');
	var arr = path.split('/');
	var month 		= arr[0];
	var customer 	= arr[1];
	var customername= arr[2];

	ajax.month 		= 	month;
	ajax.year 		= 	year_filter;
	ajax.customer 	=	customer;
	getCustomerDetails();
	geModaltList();
});
function getCustomerDetails(){
	ajax_call = $.post('<?=MODULE_URL?>ajax/getCustomerDetails', ajax, function(data) {
		$('#daily_modal #current_customer').html(data.name);
		$('#daily_modal #current_address').html(data.address1);
	});
}
function geModaltList(){
	ajax_call = $.post('<?=MODULE_URL?>ajax/daily_listing', ajax, function(data) {
		$('#tableModalList tbody').html(data.table);
		$("#daily_modal #pagination").html(data.pagination);
		$("#export_daily").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		$('#daily_modal').modal('show');
	});
}

$('#Yearly #main_pagination').on('click', 'a', function(e) {
	e.preventDefault();
	var li = $(this).closest('li');
	if (li.not('.active').length && li.not('.disabled').length) {
		ajax_main.page = $(this).attr('data-page');
		getMainList();
	}
})

$('#daily_modal #pagination').on('click', 'a', function(e) {
	e.preventDefault();
	var li = $(this).closest('li');
	if (li.not('.active').length && li.not('.disabled').length) {
		ajax.page = $(this).attr('data-page');
		getModaltList();
	}
})

$('#year_filter').on('change',function(){
	ajax_main.year 	=	$(this).val();
	getMainList();
}).trigger('change');

$('#customer').on('change', function() {
	ajax_main.customer 	= $(this).val();
	ajax_main.page 		= 1;
	getMainList();
});
</script>