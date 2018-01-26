<style>
	.nopadding{
		padding:0;
	}
</style>
<section class="content">
    <div class="box box-primary">
		<form method = "post">	
			<div class="box-header">
				<div class="row">
					<div class = "col-md-8">
						<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>financials/payment/create" style="outline:none;">Create New Disbursement Voucher</a>
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

					<input type = "hidden" value = "paid" name = "addCond" id = "addCond" />

					<div class = "col-md-3">
						<div class="form-group">
							<div class="input-group monthlyfilter">
								<input type="text" readOnly name="daterangefilter" id="daterangefilter" class="form-control" value = "" onFocusOut="showList();" data-daterangefilter="month"/>

								<span class="input-group-addon glyphicon glyphicon-calendar"></span>
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
											->setAttribute(array("onChange" => "showList();"))
											->setNone('All')
											->draw($show_input);
								?>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="panel panel-default">
				<!--<div class="panel-heading" id="option_filter">
					<div class="row">
						<div class="control-label col-md-9 col-sm-9 col-xs-9">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#" data-toggle="tab" style="outline:none;" onClick="filterList('paid');">Paid</a></li>
							<li><a href="#" data-toggle="tab" style="outline:none;" onClick="filterList('partial');">With Partial Payment</a></li>
							<li><a href="#" data-toggle="tab" style="outline:none;" onClick="filterList('all');">All</a></li>
						</ul>
						</div>
						<label class="control-label col-md-2 col-sm-2 col-xs-2 right" for="items" style="padding-top:5px;">Display</label>
						<div class="col-md-1 col-sm-1 col-xs-1">
							<select name="items" id="items" class="form-control" false false  onChange="showList();" style="padding-left:5px; padding-right:5px;">
								<option value="10" >10</option>
								<option value="15" >15</option>
								<option value="20" selected>20</option>
								<option value="50" >50</option>
								<option value="100" >100</option>
							</select>						
						</div>
					</div>
				</div>	-->
				<div class="box-body table table-responsive"  style = "overflow-x: inherit;">
					<table id = "payment_table" class="table table-hover">
						<!--<thead>
							<tr class = "info">
								<th class = "col-md-1" style="text-align:center;"></th>
								<th class = "col-md-2 text-center">Transaction Date</th>
								<th class = "col-md-2 text-center">Voucher No.</th>
								<th class = "col-md-2 text-center">Vendor</th>
								<th class = "col-md-2 text-center">Amount</th>-->
								<!--<th class = "col-md-2 text-center">Status</th>-->
							<!--</tr>
						</thead>-->

						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader(
										'',
										array(
											'class' => 'col-md-1 text-center'
										)
									)
									->addHeader('Transaction Date', array('class' => 'col-md-3 text-center'), 'sort', 'pv.transactiondate', 'asc')
									->addHeader('Voucher No.', array('class' => 'col-md-3 text-center'), 'sort', 'pv.voucherno')
									->addHeader('Vendor', array('class' => 'col-md-3 text-center'), 'sort', 'p.partnername')
									->addHeader('Amount', array('class' => 'col-md-3 text-center'), 'sort', 'pv.netamount')
									->draw();
						?>
					
						<tbody id = "list_container">
						</tbody>

						<tfoot>
							<tr>
								<td colspan="8" class="text-center" id="page_links"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</form>
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

tableSort('#payment_table', function(value, x) 
{
	ajax.sort = value;
	ajax.page = 1;
	if (x) 
	{
		showList();
	}
});


function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function showList(pg)
{
	ajax.daterangefilter = $("#daterangefilter").val();
	ajax.vendfilter      = ($("#vendor").val() != "none") ? $("#vendor").val() : "";
	ajax.addCond 		 = $("#addCond").val();

	$.post('<?=BASE_URL?>financials/payment/ajax/payment_listing',ajax, function(data) 
	{
		$('#payment_table #list_container').html(data.table);
		$('#payment_table #page_links').html(data.pagination);
	});
}

/**FILTER BY TYPE**/
function filterList(tab)
{
	var addCondition	= document.getElementById('addCond');

	addCondition.value = tab;
	
	showList();
}

$( "#search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$(document).ready(function() 
{	
	// Load data
	showList();

	$('.monthlyfilter').on('apply.daterangepicker', function() 
	{
		showList();
	});


	$('#paymentForm #paymentmode').on('change',function()
	{
		var mode = $(this).val();
		
		if( mode.trim() == "cheque" )
		{
			$('#paymentForm #cash_details').addClass('hidden');
			$('#paymentForm #check_details').removeClass('hidden');
		}
		else
		{
			$('#paymentForm #cash_details').removeClass('hidden');
			$('#paymentForm #check_details').addClass('hidden');
		}
	});

	/** -- FOR DELETING DATA -- **/
	$('#deleteModal #btnYes').click(function() 
	{
		var id 		   = $('#deleteModal #recordId').val();
	
		ajax.voucherno 	=	id;
		
		$.post('<?=BASE_URL?>financials/payment/ajax/delete_dv', ajax , function(data) 
		{
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
/** -- FOR DELETING DATA -- end **/
});

</script>