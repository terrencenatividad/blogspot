<section class="content">
    <div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class = "col-md-8">
					<!-- <div class="btn-group"> -->
						<!-- <a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Disbursement Voucher</a>
						<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="caret"></span>
							<span class="sr-only">Toggle Dropdown</span>
						</button> -->
						<!-- <ul class="dropdown-menu"> -->
							<!-- <li><a href="<?= MODULE_URL ?>get_export" id="export_table" download="Disbursement Voucher.csv"><i class="glyphicon glyphicon-open"></i>Export Voucher/s</a></li> -->
							<!--<li><a href="#import-modal" data-toggle="modal"><i class="glyphicon glyphicon-save"></i>Import Voucher/s</a></li>-->
						<!-- </ul> -->
					<!-- </div> -->
					<?
						echo $ui->CreateNewButton('');
						// echo $ui->OptionButton('');
					?>
					<button type="button" id="item_multiple_delete" class="btn btn-danger btn-flat">Delete<span></span></button>
					<button type="button" id="item_multiple_cancel" class="btn btn-warning btn-flat">Cancel<span></span></button>
				</div>

				<div class = "col-md-4">
					<div class = "form-group">
						<div class="input-group">
							<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
							<div class="input-group-btn" style = "height: 34px;">
								<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class = "row">
				<div class = "col-md-3">
					<div class = "form-group">
						<div class="input-group monthlyfilter">
							<input type="text" readOnly name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class = "col-md-5">
					<div class = "row">
						<div class = "col-md-6">
							<?php
								echo $ui->formField('dropdown')
										->setPlaceholder('Filter Vendor')
										->setName('vendor')
										->setId('vendor')
										->setList($vendor_list)
										->setNone('All')
										->draw($show_input);
							?>
						</div>
					</div>
				</div>
				<div class="col-md-4 pull-right">
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
			<li><a href="unposted" data-toggle="tab">Unposted</a></li>
			<li><a href="posted" data-toggle="tab">Posted</a></li>
			<li><a href="cancelled" data-toggle="tab">Cancelled</a></li>
		</ul>
		<table id="tableList" class="table table-hover table-bordered table-striped">
			<?php
				echo $ui->loadElement('table')
						->setHeaderClass('info')
						->addHeader(
							'<input type="checkbox" class="checkall">',
							array(
								'class' => 'col-md-1 text-center'
							)
						)
						->addHeader('Date', array('class' => 'col-md-1'), 'sort', 'main.transactiondate')
						->addHeader('Voucher', array('class' => 'col-md-1'), 'sort', 'main.voucherno', 'desc')
						->addHeader('Vendor', array('class' => 'col-md-3'), 'sort', 'p.partnername')
						->addHeader('Reference', array('class' => 'col-md-3'), 'sort', 'main.referenceno')
						->addHeader('Payment Mode', array('class' => 'col-md-1'), 'sort', 'main.paymenttype')
						->addHeader('Amount', array('class' => 'col-md-1'), 'sort', 'main.convertedamount')
						->addHeader('Status', array('class' => 'col-md-1'))
						->draw();
			?>
			<tbody id = "list_container">
			</tbody>
		</table>
		
	</div>
	<div id="pagination"></div>
</section>

<!-- Delete Modal for Paid, Partial PV -->
<div class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
						</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Delete Modal for Unpaid AP -->
<div class="modal fade" id="deleteModalAP" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
						</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Import Modal -->
<div class="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Disbursement Vouchers</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=BASE_URL?>modules/financials_module/backend/view/pdf/import_payment_voucher.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group field_col">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<input class = "form_iput" value = "" name = "import_csv" id = "import_csv" type = "file">
							<span class="help-block hidden small" id = "import_csv_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var ajax_call 	= {};
	var ajax 		= filterFromURL();
	//ajax.filter 	= $('#filter_tabs .active a').attr('href');
	ajax.limit 		= $('#items').val();
	ajax.filter 	= ajax.filter || $('#filter_tabs .active a').attr('href');
	ajaxToFilter(ajax, { search : '#table_search', limit : '#items', vendor : '#vendor', daterangefilter : '#daterangefilter' });

	ajaxToFilterTab(ajax, '#filter_tabs', 'filter');

	tableSort('#tableList', function(value, x) 
	{
		ajax.sort = value;
		ajax.page = 1;
		if (x) 
		{
			showList();
		}
	});
	$( "#search" ).keyup(function() 
	{
		var search = $( this ).val();
		ajax.search = search;
		showList();
	});
	$('#vendor').on('change', function() {
		ajax.page 	= 1;
		ajax.vendor = $(this).val();
		ajax_call.abort();
		showList();
	});
	$('#items').on('change', function() {
		ajax.page 	= 1;
		ajax.limit = $(this).val();
		ajax_call.abort();
		showList();
	});
	$('#filter_tabs li').on('click', function() {
		ajax.page = 1;
		ajax.filter = $(this).find('a').attr('href');
		ajax_call.abort();
		showList();
	});
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax_call.abort();
		showList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		showList();
	});
	$(function() {
		linkButtonToTable('#item_multiple_delete', '#tableList');
		linkButtonToTable('#item_multiple_cancel', '#tableList');
		linkDeleteToModal('#tableList .delete', 'deleteCallback');
		linkCancelToModal('#tableList .cancel', 'cancelCallback');

		createConfimationLink('#tableList .post', 'postCallback', 'Are you sure you want to post this disbursement voucher?');
		createConfimationLink('#tableList .unpost', 'unpostCallback', 'Are you sure you want to unpost this disbursement voucher?');

		linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'deleteCallback');
		linkCancelMultipleToModal('#item_multiple_cancel', '#tableList', 'cancelCallback');
	});
	function showList(){
		filterToURL();
		ajax_call = $.post('<?=BASE_URL?>financials/disbursement/ajax/load_list', ajax, function(data){
			$('#list_container').html(data.list);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	showList();
	function deleteCallback(id) {
		var ids = getDeleteId(id);
		
		$.post('<?=MODULE_URL?>ajax/ajax_delete', ids+'&type=delete', function(data) {
			showList();
		});
	}
	function cancelCallback(id) {
		var ids = getDeleteId(id);
		$.post('<?=MODULE_URL?>ajax/ajax_delete', ids+'&type=cancel', function(data) {
			showList();
		});
	}
	function postCallback(id) {
		$.post('<?=MODULE_URL?>ajax/ajax_update', {id : id, type: 'yes'}, function(data) {
			showList();
		});
	}
	function unpostCallback(id) {
		$.post('<?=MODULE_URL?>ajax/ajax_update', {id : id, type: 'no'}, function(data) {
			showList();
		});
	}
</script>