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
							->setPlaceholder('Filter Customer')
							->setName('customer')
							->setId('customer')
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
							//->setValidation('required')
							->setAttribute(array("readonly"))
							->setValue("")
							->draw($show_input);
					?>
				</div>

				<div class="col-md-2">
					<?php
						echo $ui->formField('dropdown')
							//->setPlaceholder('Select Status')
							->setName('status')
							->setId('status')
							->setList(array("posted"=>"posted","open"=>"open"))
							//->setNone('Filter: All')
							->draw();
					?>
				</div>

				<div class="col-md-1">
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

<div id="customer_list_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">List of Customers</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input type="text" id="customer_list_search" class="form-control" placeholder="Search Customer">
							<div class="input-group-addon">
								<i class="glyphicon glyphicon-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="customer_tableList" class="table table-hover table-clickable">
					<thead>
						<tr class="info">
							<th class="col-xs-3">Customer Code</th>
							<th class="col-xs-2">Name</th>
							<th class="col-xs-2">Address</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5" class="text-center">Loading List</td>
						</tr>
					</tbody>
				</table>
				<div id="pagination"></div>
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
							<input type="text" id="voucher_list_search" class="form-control" placeholder="Search Voucher">
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
							<th class="col-xs-2 text-center">Voucher No</th>
							<th class="col-xs-2 text-center">Customer</th>
							<th class="col-xs-1 text-center">Reference No</th>
							<th class="col-xs-2 text-center">Transaction Date</th>
							<th class="col-xs-2 text-center">Invoice No</th>
							<th class="col-xs-2 text-center">Invoice Date</th>
							<th class="col-xs-1 text-center">Due Date</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5" class="text-center">Loading List</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!--DETAIL MODAL-->
	<div class="modal fade" id="listModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<?
			/**ITEM OPTIONS**/
			$itemArray	= array("10"=>"10","20"=>"20","50"=>"50","100"=>"100");
			?>
			<div class="modal-content">
				<div class="modal-header">
					List of Transactions
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<?php 
								echo $ui->formField('dropdown')
									->setLabel('Display: ')
									->setSplit('col-md-10 text-right', 'col-md-2 pull-right')
									->setName('items')
									->setId('items')
									//->setAttribute(array("onChange" => "showList();"))
									->setList($itemArray)
									->setValue("10")
									->draw($show_input);
						?>
					</div>
					<br/>
					<br/>
					<table class="table table-condensed table-hover table-bordered">
						<thead>
							<tr class="info">
								<th class="col-md-2 text-center">Reference</th>
								<th class="col-md-2 text-center">Date</th>
								<th class="col-md-2 text-center">Debit</th>
								<th class="col-md-2 text-center">Credit</th>
							</tr>
						</thead>
						<tbody id="list_container">
							<tr>
								<td class="center" style="vertical-align:middle;" colspan="4">- No Records Found -</td>
							</tr>
						</tbody>
						<!--<tfoot>
							<tr class="">
								<td class="center" id="page_info">&nbsp;</td>
								<td class="center" id="page_links" colspan="3"></td>
							</tr>
						</tfoot>-->
					</table>
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
	<script type="text/javascript">
		var ajax = {}
			ajax.limit = 20;
		var ajax_call = {};
			
		function getList() {
			var datefilter	= document.getElementById('daterangefilter').value;
			var customer    = document.getElementById('customer').value;
			var voucherno   = document.getElementById('voucherno').value;
			var status      = document.getElementById('status').value;
			ajax.daterangefilter = datefilter;
			ajax.customer=customer;
			ajax.voucher=voucherno;
			ajax.status = status;
			ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) {
				 	$('#ar_detailed_container').html(data.table);
					$('#pagination').html(data.pagination);
					$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}

		function getCustomerList(){
			var customer = $('#customer').val();
			var status      = document.getElementById('status').value;
			ajax.customer=customer;
			ajax.status = status;
			$('#customer_list_modal').modal('show');
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/load_customer_list', ajax, function(data) 
			{   
				$('#customer_tableList tbody').html(data.table);
				$('#customer_tableList #pagination').html(data.pagination);
			});
		}

		function getVoucherList(){
			var customer = $('#customer').val();
			var voucherno = $('#voucherno').val();
			ajax.customer=customer;
			ajax.voucher=voucherno;
			ajax.status = status;
			$('#voucher_list_modal').modal('show');
			if (ajax_call != '') {
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
	
		$('form').submit(function(e) {
			e.preventDefault();
			getList();
		});

		$('#customer_tableList #pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});

		$('#customer').on('focus', function() 
		{	$("#customer").val();
			getCustomerList();
			getList();	
		});

		$('#customer_tableList').on('click', 'tr[data-id]', function() {
			var customerid = $(this).attr('data-id');
			$('#customer').val(customerid).trigger('blur');
			$('#customer_list_modal').modal('hide');
			getList();	
		});

		$('#voucherno').on('focus', function() 
		{	$('#voucherno').val("");
			getVoucherList();
			getList();
		});

		$('#voucher_tableList').on('click', 'tr[data-id]', function() {
				var voucherno = $(this).attr('data-id');
				$('#voucherno').val(voucherno).trigger('blur');
				$('#voucher_list_modal').modal('hide');
				getList();	
		});

		$("#status").on("change",function(){
			getList();
		});

		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
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