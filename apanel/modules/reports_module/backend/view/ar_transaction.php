<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post" id="ar_detailed_Form">
				
				<div class="col-md-3">
					<?php
						echo $ui->formField('text')
								->setName('daterangefilter')
								->setId('daterangefilter')
								->setAttribute(array('data-daterangefilter' => 'month'))
								->setAddon('calendar')
								->setValue($datefilter)
								->setValidation('required')
								->draw(true);
					?>
				</div>

				<div class="col-md-3">
					<?php 
						echo $ui->formField('dropdown')
							->setPlaceholder('Filter Customer')
							->setName('customer')
							->setId('customer')
							->setNone('All')
							->setList($customer_list)
							->draw(true);
					?>
				</div>
				<div class="col-md-1 col-md-offset-5">
					<a href="" id="export_csv" download="AR_Transactions.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
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
					<th colspan="9" class="text-left" style="background:#DDD">Customer Name</th>
				</tr>
				<tr class="info">
					<th class="col-md-1 text-left">Voucher Number</th>
					<th class="col-md-1 text-left">Receipt Voucher</th>
					<th class="col-md-1 text-left">Transaction Date</th>
					<th class="col-md-1 text-left">Invoice No</th>
					<th class="col-md-1 text-left">Amount</th>
					<th class="col-md-1 text-left">Amount Applied</th>
					<th class="col-md-1 text-left">Balance</th>
					<th class="col-md-1 text-left">Remarks</th>
					<th class="col-md-1 text-left">Terms</th>
					<th class="col-md-1 text-left">Status</th>
				</tr>
			</thead>
			<tbody id="ar_detailed_container">
				
			</tbody>
		</table>
		<div id="pagination"></div>	
	</div>
</section>
	
<script type="text/javascript">
	var ajax = {}
	var ajax_call = {};
	var ajax = filterFromURL();
	ajax.limit 	= 50;
	ajax.page 	= 1;
	ajaxToFilter(ajax, { customer : '#customer', daterangefilter : '#daterangefilter' });

	function getList() {
		filterToURL();
		ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) {
			$('#ar_detailed_container').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}

	$(function(){
		getList();
	});
	$("#daterangefilter").on("change",function(){
		ajax.daterangefilter = $(this).val();
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
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});
	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});
</script>