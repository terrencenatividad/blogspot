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
						echo $ui->formField('text')
							->setPlaceholder('Filter Vendor')
							->setName('supplier')
							->setId('supplier')
							->setAddon('search')
							//->setValidation('required')
							->setAttribute(array("readonly"))
							->setValue("")
							->draw($show_input);
					?>
				</div>

				<div class="col-md-3">
					<?php
						echo $ui->formField('text')
							->setPlaceholder('Filter Voucher No')
							->setName('voucherno')
							->setId('voucherno')
							->setAddon('search')
							->setAttribute(array("readonly"))
							->setValue("")
							->draw($show_input);
					?>
				</div>

				<div class="col-md-2">
					<?php
						echo $ui->formField('dropdown')
								->setName('status')
								->setId('status')
								->setList(array("posted"=>"posted","open"=>"open"))
								->setAttribute(array("onChange" => "getList();"))
								->draw();
					?>
				</div>

				<div class="col-md-1">
					<a href="" id="export_csv" download="AP_Detailed_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
			
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="">
					<th class="col-md-1 text-left" style="background:#DDD">Vendor Code</th>
					<th colspan="9" class="text-left" style="background:#DDD">Vendor Name</th>
				</tr>
				<tr class="info">
					<th class="col-md-1 text-left">Voucher Number</th>
					<th class="col-md-1 text-left">Payment Voucher</th>
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
			<tbody id="ap_detailed_container">
				
			</tbody>
		</table>
		<div id="pagination"></div>	
	</div>
</section>

<div id="supplier_list_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">List of Vendors</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input type="text" id="supplier_list_search" class="form-control" placeholder="Search Vendor" name = "supplier_list_search" onKeyUp = "getsupplierList();">
							<div class="input-group-addon">
								<i class="glyphicon glyphicon-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="supplier_tableList" class="table table-hover table-clickable">
					<thead>
						<tr class="info">
							<th class="col-xs-3">Vendor Code</th>
							<th class="col-xs-2">Name</th>
							<th class="col-xs-2">Address</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5" class="text-center">Loading List</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" class="text-center" id="pagination"></td>
						</tr>
					</tfoot>
				</table>
				<!--<div id="pagination"></div>-->
			</div>
		</div>
	</div>
</div>

<div id="voucher_list_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">List of Invoices</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input type="text" id="voucher_list_search" class="form-control" placeholder="Search Voucher" onKeyUp = "getVoucherList();">
							<div class="input-group-addon">
								<i class="glyphicon glyphicon-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="voucher_tableList" class="table table-hover table-clickable">
					<thead>
						<tr class="info">
							<th class="col-xs-1 text-center">Voucher No</th>
							<th class="col-xs-2 text-center">Vendor</th>
							<th class="col-xs-2 text-center">Reference No</th>
							<th class="col-xs-1 text-center">Transaction Date</th>
							<th class="col-xs-1 text-center">Invoice No</th>
							<th class="col-xs-1 text-center">Invoice Date</th>
							<th class="col-xs-1 text-center">Due Date</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="7" class="text-center">Loading List</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" class="text-center" id="pagination"></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var ajax = {}
		ajax.limit = 20;
	var ajax_call = {};
		
	function getList() 
	{
		var supplier    = document.getElementById('supplier').value;
		var voucherno   = document.getElementById('voucherno').value;
		var status      = document.getElementById('status').value;
		
		ajax.supplier = supplier;
		ajax.voucher = voucherno;
		ajax.status = status;
		
		ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) 
		{
			$('#ap_detailed_container').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}

	function getsupplierList()
	{
		var supplier = $('#supplier').val();
		var status      = document.getElementById('status').value;
		ajax.supplier = supplier;
		ajax.status = status;
		ajax.search = $("#supplier_list_search").val();

		$('#supplier_list_modal').modal('show');
		
		if (ajax_call != '') 
		{
			ajax_call.abort();
		}
		
		ajax_call = $.post('<?=MODULE_URL?>ajax/load_supplier_list', ajax, function(data) 
		{   
			$('#supplier_tableList tbody').html(data.table);
			$('#supplier_tableList #pagination').html(data.pagination);
		});
	}

	function getVoucherList()
	{
		var supplier = $('#supplier').val();
		var voucherno = $('#voucherno').val();
		
		ajax.supplier = supplier;
		ajax.voucher  = voucherno;
		ajax.status   = status;
		ajax.search   = $("#voucher_list_search").val();
		
		$('#voucher_list_modal').modal('show');

		if (ajax_call != '') 
		{
			ajax_call.abort();
		}
		
		ajax_call = $.post('<?=MODULE_URL?>ajax/load_voucher_list', ajax, function(data) 
		{   
			$('#voucher_tableList tbody').html(data.table);
			$('#voucher_tableList #pagination').html(data.pagination);
		});
	}

	$(function(){
		getList();
	});

	$('form').submit(function(e) 
	{
		e.preventDefault();
		getList();
	});

	$('#supplier_tableList #pagination').on('click', 'a', function(e) 
	{
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	});

	$('#supplier_tableList').on('click', 'tr[data-id]', function() 
	{
		var supplierid = $(this).attr('data-id');
		$('#supplier').val(supplierid).trigger('blur');
		$('#supplier_list_modal').modal('hide');
		getList();	
	});

	$('#supplier').on('focus', function() 
	{	$('#supplier').val("");
		getsupplierList(); 
		getList();
	});

	$('#voucherno').on('focus', function() 
	{	$('#voucherno').val("");
		getVoucherList();
		getList();
	});

	$("#daterangefilter").on("change",function(){
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		getList();
	}).trigger('change');

	$('#voucher_tableList').on('click', 'tr[data-id]', function() 
	{
		var voucherno = $(this).attr('data-id');
		$('#voucherno').val(voucherno).trigger('blur');
		$('#voucher_list_modal').modal('hide');
		getList();	
	});

	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		try {
			ajax_call.abort();
		} catch (e) {}
		getList();
	}).trigger('change');

	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});

</script>