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
						->setPlaceholder('Select Supplier')
						->setName('supplier')
						->setId('supplier')
						->setNone('All')
						->setList($supplier_list)
						->draw(true);
				?>
			</div>
			<div class="col-md-1 pull-right">
				<a href="" id="export" download="purchase_report.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
			</div>
		</div>
	</div>
</div>
<div class="nav-tabs-custom">
	<div class="tab-content no-padding">
		<div id="Yearly" class="tab-pane active">
			<table id="tableListYearly" class="table table-hover table-striped table-sidepad report_table text-right">
				<thead>
					<tr class="info">
						<th style="col-xs-4">Supplier</th>
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
						<p><span><strong>Supplier:  </strong></span><span id="current_vendor"></span></p>
						<p><span><strong>Address:  </strong></span><span id="current_address"></span></p>
					</div>
					<div class="col-md-4">
						<a href="" id="export_daily" download="detailed_purchase_report.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
			</div>
			<div class="modal-body no-pad">
				<table id="tableModalList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th>Date</th>
							<th>Purchase Receipt No.</th>
							<th>Invoice No.</th>
							<th>AP No.</th>
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
	var vendor 		= arr[1];
	var vendorname	= arr[2];

	ajax.month 		=	month;
	ajax.year 		= 	year_filter;
	ajax.vendor 	= 	vendor;
	getVendorDetails();
	geModaltList();
});
function getVendorDetails(){
	ajax_call = $.post('<?=MODULE_URL?>ajax/getVendorDetails', ajax, function(data) {
		$('#daily_modal #current_vendor').html(data.name);
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
	ajax_main.page = $(this).attr('data-page');
	getMainList();
})

$('#daily_modal #pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	geModaltList();
})

$('#year_filter').on('change',function(){
	ajax_main.year 	=	$(this).val();
	getMainList();
}).trigger('change');

$('#supplier').on('change',function(){
	ajax_main.supplier 	=	$(this).val();
	ajax_main.page 		= 	1;
	getMainList();
});
</script>