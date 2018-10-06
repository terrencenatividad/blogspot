<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!</strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
		<form method = "post">
			
			<div class="box-header">
				<div class="row">
					<div class = "col-md-8">
						<?
							echo $ui->CreateNewButton('');
						?>
						<button type="button" id="item_multiple_cancel" class="btn btn-danger btn-flat">Cancel<span></span></button>
					</div>
					<div class = "col-md-4">
						<div class="form-group">
							<div class="input-group" >
								<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
								<div class="input-group-btn" style = "height: 34px;">
									<button type="button" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3 col-xs-6">
						<div class="form-group">
							<div class="input-group monthlyfilter">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-sm-3 col-xs-6">
							<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Requestor')
								->setName('requestor')
								->setId('requestor')
								->setList($requestor_list)
								->setNone('All')
								->draw($show_input);
							?>
					</div>
					<div class="col-sm-4 col-xs-6"></div>
					
					<div class="col-sm-1 col-xs-6 text-right">
						<label for="" class="padded">Items: </label>
					</div>

					<div class="col-sm-1 col-xs-6">
						<select id="items">
							<option value="10">10</option>
							<option value="20">20</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>
					</div>
				</div>
				<input type = "hidden" id = "addCond" value = "all">
			</div>

		</form>
    </div>

	<div class="nav-tabs-custom">
		<ul id="filter_tabs" class="nav nav-tabs">
			<li class="active"><a href="all" data-toggle="tab">All</a></li>	
			<li><a href="expired" data-toggle="tab">Expired</a></li>	
			<li><a href="open" data-toggle="tab">Draft</a></li>
			<li><a href="locked" data-toggle="tab">Converted</a></li>
			<li><a href="cancelled" data-toggle="tab">Cancelled</a></li>
			<!--<li><a href="posted" data-toggle="tab">Completed</a></li>					-->
		</ul>
		<table id = "so_table" class="table table-hover">
			<!--<thead>
				<tr class = "info">
					<th class = "col-md-1 text-center"></th>
						<th class = "col-md-4 text-center">Transaction Date</th>
						<th class = "col-md-3 text-center">Request No.</th>
						<th class = "col-md-3 text-center">Requestor</th>
						<th class = "col-md-4 text-center">Status</th>
				</tr>
			</thead>-->
			<thead>
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('',array('class' => 'text-center col-md-1'))
							->addHeader('Transaction Date',array('class'=>'col-md-3'),'sort','s.transactiondate')
							->addHeader('Request No.', array('class'=>'col-md-3'),'sort','s.voucherno','desc')
							->addHeader('Requestor',array('class'=>'col-md-3'),'sort','s.requestor')
							->addHeader('Status',array('class'=>'col-md-2'),'sort','s.stat')
							->draw();
				?>
			</thead>
		<form method = "post">
			<tbody id = "list_container">
			</tbody>
		</form>	
		</table>
		<div id="pagination"></div>
	</div>	

</section>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to cancel this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnYes">Yes</button>
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
						<h4 class="modal-title">Import Taxes</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=BASE_URL?>modules/maintenance_module/backend/view/pdf/import_taxes.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group field_col">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<input class = "form_iput" value = "" name = "import_csv" id = "import_csv" type = "file">
							<span class="help-block hidden small"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="submit" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to cancel this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnYes">Yes</button>
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

<!--Tagging Declined-->
<div class="modal fade" id="decline_modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Decline Quotation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to tag this request as closed?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnYes">Yes</button>
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
						<h4 class="modal-title">Import Taxes</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=BASE_URL?>modules/maintenance_module/backend/view/pdf/import_taxes.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group field_col">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<input class = "form_iput" value = "" name = "import_csv" id = "import_csv" type = "file">
							<span class="help-block hidden small"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="submit" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var ajax = {};
var ajax_call = '';
var ajax = filterFromURL();

ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
ajaxToFilter(ajax, { search : '#search', limit : '#items', requestor : '#requestor', daterangefilter : '#daterangefilter' });
ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
 
function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

/** -- FOR DELETING DATA -- **/

		function ajaxCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/delete_so', ids, function(data) {
				if(data.msg == "success")
				{
					showList();
				}
			});
		}

		function getIds(ids) {
			var x = ids.split(",");
			return "id[]=" + x.join("&id[]=");
		}
	
		$(function() {
			linkCancelToModal('#so_table .delete', 'ajaxCallback');
			linkButtonToTable('#item_multiple_cancel', '#so_table');
			linkCancelMultipleToModal('#item_multiple_cancel', '#so_table', 'ajaxCallback');
		});
	/** -- FOR DELETING DATA -- end **/

function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	// ajax.daterangefilter = $("#daterangefilter").val();
	// ajax.custfilter      = $("#requestor").val();
	if (!ajax.filter) {
		ajax.filter = $('#filter_tabs .active a').attr('href');
	}

	ajax_call = $.post('<?=BASE_URL?>purchase/purchase_request/ajax/pr_listing',ajax, function(data) {
		$('#so_table #list_container').html(data.table);
		$('#pagination').html(data.pagination);
		$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
	});
};

$( "#search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$('#filter_tabs li').on('click', function() {
	ajax.filter = $(this).find('a').attr('href');
	showList();
});

/** -- FOR ITEM DISPLAY -- **/
		$('#items').on('change', function() {
			ajax.page = 1;
			ajax.limit = $(this).val();
			// ajax_call.abort();
			showList();
		});
/** -- FOR ITEM DISPLAY -- end **/

/** -- FOR SORTING -- **/
		tableSort('#so_table', function(value, getList) {
			ajax.sort = value;
			ajax.page = 1;
			
			if (getList) {
				showList();
			}
		}, ajax );
/** -- FOR SORTING -- end **/



//Date picker
$('.datepicker, .datepicker_').datepicker({
	autoclose: true
});

// Set default date to date of the day
$(".datepicker").datepicker("setDate", new Date());

$('#so_table').on('click', '.convert_to_po', function() {
	var voucherno = $(this).attr('data-id');
	window.location = '<?php echo BASE_URL ?>purchase/purchase_order/create/' + voucherno;
});

$(function() {
	$('#so_table').on('click', '.tag_complete', function() {
		var voucherno = $(this).attr("data-id");
		showConfirmationLink("tagClose('" + voucherno + "')", '', 'Are you sure you want to tag this request as complete?');
	});
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	var li = $(this).closest('li');
	if (li.not('.active').length && li.not('.disabled').length) {
		ajax.page = $(this).attr('data-page');
		showList();
	}
});

function tagClose(voucherno) {
	$.post('<?=MODULE_URL?>ajax/update_statusClosed', 'voucherno=' + voucherno, function(data) {
		showList();
	});
}

// function ajaxCallback(voucherno) {
// 	// var ids = delete_yes(id);
// 	$.post('<?=MODULE_URL?>ajax/ajax_delete', 'voucherno=' + voucherno, function(data) {
// 		showList();
// 	});
// }

$(document).ready(function() 
{
	// linkDeleteToModal('#so_table .delete', 'ajaxCallback');
	linkCancelToModal('#so_table .delete', 'ajaxCallback');
		
	/** -- FOR DATE -- end **/

	/** -- FOR requestor **/
		$('#requestor').on('change', function(e) 
		{
			ajax.requestor  = $("#requestor").val();
			showList();
			ajax.page = 1;
		});
	/** -- FOR requestor -- end **/

	/** -- FOR LOADING DATA -- **/
		showList();
	/** -- FOR LOADING DATA -- end **/
	
	/** -- FOR DELETING DATA -- **/
		$('#deleteModal #btnYes').click(function() 
		{
			var id 		   = $('#deleteModal #recordId').val();
		
			ajax.voucherno 	=	id;
			
			$.post('<?=BASE_URL?>purchase/purchase_request/ajax/delete_pr', ajax , function(data) {
				if(data.msg == "success")
				{
					$('#deleteModal').modal('hide');
					showList();
				}
				else
				{
					console.log(data.msg);
				}
			});


		});
	

	/** -- FOR DECLINING QUOTATION -- **/
		$('#decline_modal #btnYes').click(function() 
		{
			var id 		   = $('#decline_modal #recordId').val();
			// console.log(id);
		
			ajax.voucherno 	=	id;
			
			$.post('<?=BASE_URL?>purchase/purchase_request/ajax/update_req_decline', ajax , function(data) {
				console.log(id);
				if(data.msg == "success")
				{
					$('#decline_modal').modal('hide');
					showList();
				}
				else
				{
					console.log(data.msg);
				}
			});
		});
	/** -- FOR DELETING DATA -- end **/

	
	
	/** -- FOR IMPORTING DATA -- **/
		$("#import").click(function() 
		{
			$(".import-modal > .modal").css("display", "inline");
			$('.import-modal').modal();
		});
	/** -- FOR IMPORTING DATA -- end **/

	/** -- FOR EXPORTING DATA -- **/
		$("#export").click(function() 
		{
			window.location = '<?=BASE_URL?>maintenance/taxcodes/ajax/export';
		});
	/** -- FOR EXPORTING DATA -- end **/
	
	$('#daterangefilter').on('change', function(){
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		showList();
	})
});

</script>