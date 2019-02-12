<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post" id="ar_detailed_Form">
				<div class="col-md-3">
					<?php
							echo $ui->formField('text')
								->setName('datefilter')
								->setId('datefilter')
								->setClass('datepicker-input')
								->setAttribute(array('readonly'))
								->setAddon('calendar')
								->setValue($datefilter)
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
				<div class="col-md-1 col-md-offset-5">
					<a href="" id="export_csv" download="AR_Detailed_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="">
					<th class="col-md-1 text-left" style="background:#DDD">Customer Code</th>
					<th colspan="6" class="text-left" style="background:#DDD">Customer Name</th>
				</tr>
				<tr class="info">
					<th class="col-md-1 text-left">Voucher Number</th>
					<th class="col-md-1 text-left">Transaction Date</th>
					<th class="col-md-1 text-left">Invoice No</th>
					<th class="col-md-5 text-left">Remarks</th>
					<th class="col-md-1 text-left">Amount</th>
					<th class="col-md-1 text-left">Amount Paid</th>
					<th class="col-md-1 text-left">Balance</th>
				</tr>
			</thead>
			<tbody id="ap_detailed_container">
				
			</tbody>
		</table>
		<div id="pagination"></div>	
	</div>
</section>

<script>
	var ajax = {}
	var ajax_call = {};
	var ajax = filterFromURL();
	ajax.limit 	= 50;
	ajaxToFilter(ajax, { customer : '#customer', datefilter : '#datefilter' });	
		
	function getList() 
	{
		filterToURL();
		ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) 
		{
			$('#ap_detailed_container').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	$(function(){
		getList();
	});
	$("#datefilter").on("change",function(){
		ajax.datefilter = $(this).val();
		ajax.page = 1;
		getList();
	}).trigger('change');
	$("#customer").on("change",function(){
		ajax.customer = $(this).val();
		ajax.page = 1;
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	});
	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});
</script>