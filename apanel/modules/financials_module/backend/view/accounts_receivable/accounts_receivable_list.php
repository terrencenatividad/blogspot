<section class="content">
	
	<!-- Success Message for File Import -->
	<?php
		$file_import_msg = ($file_import_result) ? "<strong>Success!</strong> CSV file has been uploaded." : "Selected file was not uploaded successfully.";

		if($file_import_result)
		{
			echo '<div class="alert alert-success alert-dismissable" id="success_alert">
					<button type="button" class="close" data-dismiss="alert" >&times;</button>';
			echo 	'"'.$file_import_msg.'"';
			echo '</div>';
		}
	?>

	<!-- Error Message for File Import -->
	<?php
		$errmsg		= array_filter($import_error_messages);
		$errorcount	= count($errmsg);

		if($errorcount > 0)
		{
			echo '<div class="alert alert-warning alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" >&times;</button>';
			echo 	"<strong>The system encountered the following error(s) in processing the file you've imported:</strong><hr/>";
			echo	"<ul>";
			foreach($errmsg as $errmsgIndex => $errmsgVal)
			{
				echo '<li>'.$errmsgVal.'</li>';
			}		
			echo	"</ul>";
			echo '</div>';
		}
	?>


    <div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Accounts Receivable</a>
						&nbsp;
						<!-- <div class="btn-group" id="option_buttons">
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
								Options <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href = "#" id="export"><span class="glyphicon glyphicon-open"></span> Export Receivables</a>
								</li>
								<li>
									<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Receivables</a>
								</li>
							</ul>
						</div> -->
						<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Cancel<span></span></button>
					</div>
				</div>
				<div class = "col-md-4">
					<div class = "form-group">
						<div class="input-group">
							<input id = "table_search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
							<div class="input-group-btn" style = "height: 34px;">
								<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class = "row">
				<div class = "col-md-3">
					<div class="form-group">
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
										->setPlaceholder('Filter Customer')
										->setName('customer')
										->setId('customer')
										->setList($customer_list)
										->setNone('Filter: All')
										->draw($show_input);
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
			<li><a href="unpaid" data-toggle="tab">Unpaid</a></li>
			<li><a href="partial" data-toggle="tab">With Partial Payment</a></li>
			<li><a href="paid" data-toggle="tab">Paid</a></li>
		</ul>
		<div class="table-responsive">
			<table id="tableList" class="table table-hover">
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader(
								'<input type="checkbox" class="checkall">',
								array(
									'class' => 'col-md-1 text-center'
								)
							)
							->addHeader('Date', array('class' => 'col-md-1 text-center'), 'sort', 'main.transactiondate', 'desc')
							->addHeader('Voucher No', array('class' => 'col-md-1 text-center'), 'sort', 'main.voucherno')
							->addHeader('Customer', array('class' => 'col-md-2 text-center'), 'sort', 'p.partnername')
							->addHeader('Reference', array('class' => 'col-md-2 text-center'), 'sort', 'main.referenceno')
							->addHeader('Amount', array('class' => 'col-md-2 text-center'), 'sort', 'main.convertedamount')
							->addHeader('Balance', array('class' => 'col-md-2 text-center'), 'sort', 'main.balance')
							->addHeader('Status', array('class' => 'col-md-1 text-center'))
							->draw();
				?>
				<tbody>
					<tr>
						<td colspan="8" class="text-center"><b>No Records Found</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id="pagination"></div>
</section>

<!-- Import Modal -->
<div class="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Receivables</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=BASE_URL?>modules/financials_module/backend/view/pdf/import_payable.csv">here</a></label>
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
						<button type="button" class="btn btn-primary btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var ajax = {}
	var ajax_call = {};
	var ajax = filterFromURL();
	ajax.filter = $('#filter_tabs .active a').attr('href');
	ajax.limit 	= $('#items').val();
	ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
	ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter' });

	ajaxToFilterTab(ajax, '#filter_tabs', 'filter');

	tableSort('#tableList', function(value, x) 
	{
		ajax.sort = value;
		ajax.page = 1;
		if (x) 
		{
			getList();
		}
	});
	function getList() {
		filterToURL();
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList tbody').html(data.list);
			$('#pagination').html(data.pagination);
		});
	}
	getList();
	$( "#table_search" ).keyup(function() 
	{
		var search = $( this ).val();
		ajax.search = search;
		getList();
	});
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax_call.abort();
		getList();
	})
	$('#customer').on('change', function() {
		ajax.page 	= 1;
		ajax.customer = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#filter_tabs li').on('click', function() {
		ajax.page = 1;
		ajax.filter = $(this).find('a').attr('href');
		ajax_call.abort();
		getList();
	});
	$("#import").click(function() 
	{
		$(".import-modal > .modal").css("display", "inline");
		$('.import-modal').modal();
	});
	$("#importForm #btnImport").click(function() 
	{
		var valid	= 0;
		
		valid	+= validateField('importForm','import_csv', "import_csv_help");

		if(valid == 0)
		{
			$("#importForm").submit();
		}
	});
	$("#export").click(function() 
	{
		window.location = '<?=BASE_URL?>financials/accounts_receivable/ajax/export?' + $.param(ajax);
	});
	function ajaxCallback(id) {
		var ids = getDeleteId(id);
		$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
			getList();
		});
	}

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	});
	$(function() {
		linkButtonToTable('#item_multiple_delete', '#tableList');
		linkCancelToModal('#tableList .delete', 'ajaxCallback');
		linkDeleteMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
	});
	$('body').on('click','.receive_payment',function(e){
		var voucher = $(this).attr('data-id');
		location.href = '<?=BASE_URL?>financials/accounts_receivable/view/'+voucher+'#payment';
	});
</script>