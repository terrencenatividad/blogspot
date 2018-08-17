<section class="content">
	<!-- Error Message for File Import -->
	<div class="alert alert-danger hidden" id="import_error">
		<button type="button" class="link btn-sm close" >&times;</button>
		<p>Ok, just a few more things we need to adjust for us to proceed :) </p><hr/>
		<ul>

		</ul>
	</div>
    <div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-8">
					<!-- <div class="form-group">
						<a href="<?//= MODULE_URL ?>create" class="btn btn-primary">Create Accounts Receivable</a>
						&nbsp;
						<button type="button" id="import_ar" class="btn btn-info delete_button">Import<span></span></button>
						<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Cancel<span></span></button>
					</div> -->
					<?
						echo $ui->CreateNewButton('');
						echo $ui->OptionButton('');
					?>
					<input type="button" id="item_multiple_delete" class="btn btn-danger btn-flat " value="Cancel">
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
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Warning!</strong></h4>
		<div id = "errmsg"></div>
		<div id = "warningmsg"></div>
	</div>
	<div class="nav-tabs-custom">
		<ul id="filter_tabs" class="nav nav-tabs">
			<li class="active"><a href="all" data-toggle="tab">All</a></li>
			<li><a href="unpaid" data-toggle="tab">Unpaid</a></li>
			<li><a href="partial" data-toggle="tab">With Partial Payment</a></li>
			<li><a href="paid" data-toggle="tab">Paid</a></li>
			<li><a href="cancelled" data-toggle="tab">Cancelled</a></li>
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
							->addHeader('Date', array('class' => 'col-md-1'), 'sort', 'main.transactiondate')
							->addHeader('Imported', array('class' => 'col-md-1'),'sort','main.lockkey')
							->addHeader('Voucher No', array('class' => 'col-md-1'), 'sort', 'main.voucherno', 'desc')
							->addHeader('Customer', array('class' => 'col-md-2'), 'sort', 'p.partnername')
							->addHeader('Reference', array('class' => 'col-md-2'), 'sort', 'main.referenceno')
							->addHeader('Amount', array('class' => 'col-md-2'), 'sort', 'main.convertedamount')
							->addHeader('Balance', array('class' => 'col-md-2'), 'sort', 'main.balance')
							->addHeader('Status', array('class' => 'col-md-1'))
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
<div class="modal fade" id="import-modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
					<h4 class="modal-title">Import Accounts Receivable</h4>
				</div>
				<div class="modal-body">
					<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" id="download-link" download="Accounts Receivable Template.csv" >here</a></label>
					<hr/>
					<label>Step 2. Fill up the information needed for each columns of the template.</label>
					<hr/>
					<div class="form-group">
						<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
						<?php
							echo $ui->setElement('file')
									->setId('import_csv')
									->setName('import_csv')
									->setAttribute(array('accept' => '.csv'))
									->setValidation('required')
									->draw();
						?>
						<span class="help-block"></span>
					</div>
					<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" id="btnClose">Close</button>
					</div>
				</div>
				
			</form>
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
	$("#export").click(function() 
	{
		window.location = '<?=BASE_URL?>financials/accounts_receivable/ajax/export?' + $.param(ajax);
	});
	$(".close").click(function() 
	{
		location.reload();
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
	// NO Export Yet
	$('#export_id').addClass('hidden')
	$('#import_id').prop('href','#import-modal');
	$("#import_id").click(function() {
		$("#import-modal > .modal").css("display", "inline");
		$('#import-modal').modal();
	});
	$('#import-modal #btnClose').click(function(){
		$('#import-modal #import-skip #loading').addClass('hidden');
		$('#import-modal #import-proceed #loading').addClass('hidden');
		$('#import-modal #import-step1').show();
		$('#import-modal #import-step2').hide();
		$('#import-modal').modal('hide');	
	});
	$('#importForm').on('change', '#import_csv', function() {
		var filename = $(this).val().split("\\");
		$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
	});
	$('#import-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
		$('#import_csv').closest('.form-group').html(form_csv);
	});
	$('#btnImport').on('click',function(e){
		var formData =	new FormData();
		formData.append('file',$('#import_csv')[0].files[0]);
		ajax_call 	=	$.ajax({
			url : '<?=MODULE_URL?>ajax/save_import',
			data:	formData,
			cache: 	false,
			processData: false, 
			contentType: false,
			type: 	'POST',
			success: function(response){
				if(response && response.errmsg == ""){
					$('#import-modal').modal('hide');
					// $(".alert-warning").addClass("hidden");
					// $("#errmsg").html('');
					$("#import_error").addClass('hidden');
					show_success_msg('Your data has been imported successfully.');
				}else{
					$('#import-modal').modal('hide');
					show_error(response.errmsg, response.warning);
				}
			},
		});
	});
	function show_error(msg, warning){
		// $(".delete-modal").modal("hide");
		// $(".alert-warning").removeClass("hidden");
		// $("#errmsg").html(msg);
		// $("#warningmsg").html(warning);
		if(msg != ''){
			var newmsg 	= msg.split("<br/>");
			var errcnt	= newmsg.length;
			var list 	= '';
			for (let index = 0; index < errcnt; index++) {
				if(newmsg[index] != ''){
					list += '<li>'+newmsg[index]+'</li>';
				}
			}
		}
		$("#import_error").removeClass('hidden');
		$("#import_error ul").html(list);

		$('html,body').animate({ scrollTop: (0) }, 'slow');
	}
	function show_success_msg(msg){
		$('#success_modal #message').html(msg);
		$('#success_modal').modal('show');
		getList();
	}
	$('body').on('click','#success_modal .btn-success', function(){
		$('#success_modal').modal('hide');
		getList();
	});
</script>