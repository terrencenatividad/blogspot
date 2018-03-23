<style>
	.remove-margin .form-group {
		margin-bottom: 0;
	}

	.pay .form-group {
		margin: 0;
	}
</style>

<section class="content">
    <div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">
					<div class = "col-md-8">
						<a class="btn btn-primary btn-flat" data-toggle = "modal" role="button" href = "<?=BASE_URL?>financials/payment_voucher/create" style="outline:none;">Create New Payment Voucher</a>
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
					<input type = "hidden" value = "all" name = "addCond" id = "addCond" />


					<div class = "col-md-3">
						<div class = "form-group">
							<div class="input-group monthlyfilter">
								<input type="text" readOnly name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month"/>
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
											->setNone('All')
											->draw($show_input);
								?>
							</div>
						</div>
					</div>
					<div class="col-md-1 pull-right">
						<a href="" id="export_csv" download="Payment Voucher.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span>Export</a>
					</div>
				</div>
			</div>
			<div class="nav-tabs-custom">
				<ul id="filter_tabs" class="nav nav-tabs">
					<li class="active"><a href="all" data-toggle="tab">All</a></li>
					<li><a href="unposted" data-toggle="tab">Unposted</a></li>
					<li><a href="posted" data-toggle="tab">Posted</a></li>
				</ul>
				<table id="tableList" class="table table-hover table-bordered table-striped">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'',
									array(
										'class' => 'col-md-1 text-center'
									)
								)
								->addHeader('Date', array('class' => 'col-md-1'), 'sort', 'main.transactiondate')
								->addHeader('Voucher', array('class' => 'col-md-1'), 'sort', 'main.voucherno', 'desc')
								->addHeader('Vendor', array('class' => 'col-md-3'), 'sort', 'p.partnername')
								->addHeader('Reference', array('class' => 'col-md-3'), 'sort', 'main.referenceno')
								->addHeader('Payment Mode', array('class' => 'col-md-1'), 'sort', 'main.paymentmode')
								->addHeader('Amount', array('class' => 'col-md-1'), 'sort', 'main.convertedamount')
								->addHeader('Status', array('class' => 'col-md-1'))
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
		</form>
    </div>
    
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
						<h4 class="modal-title">Import Payment Vouchers</h4>
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
	ajax.filter 	= $('#filter_tabs .active a').attr('href');
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

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function showList() 
{
	ajax_call = $.post('<?=BASE_URL?>financials/payment_voucher/ajax/load_list', ajax, function(data)
	{
		$('#list_container').html(data.list);
		$('#page_links').html(data.pagination);
		$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
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

$('#vendor').on('change', function() {
	ajax.page 	= 1;
	ajax.vendor = $(this).val();
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
})

/**JSON : RETRIEVE PAYABLES**/
function showPayableList() 
{
	var vendfilter = $("#paymentForm #vendor").val();

	var data 	   = "vendor=" + vendfilter;
	
	$.post("<?= BASE_URL ?>financials/payment_voucher/ajax/load_payables",data)
	.done(function( data ) 
	{
		$('#payable_list_container').html(data.table);
	});
}

/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
	var field	= $("#"+form+" #"+id).val();

	if(id.indexOf('_chosen') != -1)
	{
		var id2	= id.replace("_chosen","");
		field	= $("#"+form+" #"+id2).val();
	}

	if((field == '' || parseFloat(field) == 0))
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');
		
		$("#"+form+" #"+help_block)
			.removeClass('hidden');

		// if($("#"+form+" .row-dense").next(".help-block")[0])
		// {
		// 	$("#"+form+" #"+help_block)
		// 	// .parent()
		// 	// .next(".help-block")
		// 	.removeClass('hidden');
		// }

		return 1;
	}
	else
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+help_block)
			.addClass('hidden');
			
		// if($("#"+form+" .row-dense").next(".help-block")[0])
		// {
		// 	$("#"+form+" #"+help_block)
		// 	// .parent()
		// 	// .next(".help-block")
		// 	.removeClass('hidden');
		// }

		return 0;
	}
}

function toggleCheckInfo(val)
{	
	if(val == 'cheque')
	{
		$("#paymentForm #cash_payment_details").addClass('hidden');
		$("#paymentForm #check_details").removeClass('hidden');
	}
	else
	{
		$("#paymentForm #cash_payment_details").removeClass('hidden');
		$("#paymentForm #check_details").addClass('hidden');
	}
}

function SelectAll(id)
{
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

function addAmounts() 
{
	var sum 		= 0;
	var subtotal 	= 0;
	
	var subData 	= 0;
	
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	for(i = 1; i <= count; i++) 
	{  
		var inputamt	= document.getElementById('chequeamount['+i+']');
		
		if(document.getElementById('chequeamount['+i+']') != null)
		{          
			if(inputamt.value && inputamt != '0' && inputamt.value != '0.00')
			{                            
				subData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				subData = 0;
			}
			subtotal = parseFloat(subtotal) + parseFloat(subData);
		}	
	}

	subtotal	= Math.round(1000*subtotal) / 1000;
	
	document.getElementById('total').value = addCommas(subtotal.toFixed(2));
}

function resetIds()
{
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	x = 1;
	
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'chequeamount['+x+']';
		
		row.cells[0].getElementsByTagName("select")[0].name = 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'chequeamount['+x+']';
		
		row.cells[4].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); addAmounts();');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');
		x++;
	}
	
}

function setZero()
{
	resetIds();
	
	var table 		= document.getElementById('chequeTable');
	var newid 		= table.rows.length - 2;
	var account		= document.getElementById('chequeaccount['+newid+']');
	
	if(document.getElementById('chequeaccount['+newid+']')!=null)
	{
		document.getElementById('chequeaccount['+newid+']').value 	= '';
		document.getElementById('chequenumber['+newid+']').value 	= '';
		document.getElementById('chequeamount['+newid+']').value 	= '0.00';
	}
}

function clearInput(id)
{
	document.getElementById(id).value = '';
}

function confirmDelete(row)
{
	var table 		= document.getElementById('chequeTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;
	var rowindex	= table.rows[row];
	
	if($('#chequeaccount\\['+row+'\\]').val() != '') //$('#chequeaccount\\['+row+'\\]').chosen().val()
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetIds();
			addAmounts();
		}
		else
		{	
			document.getElementById('chequeaccount['+row+']').value 	= '';

			// $('#chequeaccount\\['+row+'\\]').chosen().val('');
			$('#chequeaccount\\['+row+'\\]').trigger("change");
			
			document.getElementById('chequenumber['+row+']').value 		= '';
			document.getElementById('chequedate['+row+']').value 		= '<?= $date ?>';//today();
			document.getElementById('chequeamount['+row+']').value 		= '0.00';
			
			addAmounts();
		}
	}
	else
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetIds();
			addAmounts();
		}
		else
		{
			document.getElementById('chequeaccount['+row+']').value 	= '';
			
			// $('#chequeaccount\\['+row+'\\]').chosen().val('');
			$('#chequeaccount\\['+row+'\\]').trigger("change");

			document.getElementById('chequenumber['+row+']').value 		= '';
			document.getElementById('chequedate['+row+']').value 		= '<?= $date ?>';//today();
			document.getElementById('chequeamount['+row+']').value 		= '0.00';
			addAmounts();
		}
	}
}

function validateCheques()
{
	var table 	= document.getElementById('chequeTable');
	count		= table.rows.length - 2;
	var valid	= 0;
	
	var selected	= 0;
	if(count > 0 && document.getElementById('chequeaccount[1]') != null)
	{
		for(var i=1;i<=count;i++)
		{
			var chequeaccount = $('#chequeaccount\\['+i+'\\]').val();
			
			if(chequeaccount != '')
			{
				selected++;
			}
		}
	}

	if(selected == 0 && (count > 0))
	{
		$("#paymentForm #chequeCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#paymentForm #chequeCountError").addClass('hidden');
	}
	
	if(valid == 0 && count > 0)
	{
		for(var i = 1;i <= count; i++)
		{
			var chequeaccount 	= $('#chequeaccount\\['+i+'\\]').val();
			var chequenumber 	= $('#chequenumber\\['+i+'\\]').val();
			var chequedate 		= $('#chequedate\\['+i+'\\]').val();
			var chequeamount 	= $('#chequeamount\\['+i+'\\]').val();
			
			if(chequeaccount == '' || chequenumber == '' || chequedate == '' || parseFloat(chequeamount) <= 0 || chequeamount == '')
			{
				$('#chequeaccount\\['+i+'\\]').closest('tr').addClass('danger');
				valid++;
			}
			else
			{
				$('#chequeaccount\\['+i+'\\]').closest('tr').removeClass('danger');
			}
		}
	}
		
	if(valid > 0)
	{
		$("#paymentForm #chequeAmountError").removeClass('hidden');
	}
	else
	{
		$("#paymentForm #chequeAmountError").addClass('hidden');
	}
	
	if(valid > 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

/**COMPARE TOTAL CHEQUE AMOUNT WITH PAYMENT**/
function totalPaymentGreaterThanChequeAmount()
{
	var total_payment	= document.getElementById('total_payment').value;
	var total_cheque	= document.getElementById('total').value;
	
	$('#paymentForm #disp_tot_payment').html(total_payment);
	$('#paymentForm #disp_tot_cheque').html(total_cheque);
	
	total_payment    	= total_payment.replace(/\,/g,'');
	total_cheque    	= total_cheque.replace(/\,/g,'');

	if(parseFloat(total_payment) == parseFloat(total_cheque))
	{
		$("#paymentForm #paymentAmountError").addClass('hidden');
		return 0;
	}
	else
	{
		$("#paymentForm #paymentAmountError").removeClass('hidden');
		return 1;
	}
}

/**VALIDATE AMOUNTS IN SELECTED INVOICES**/
function validateInvoices()
{
	var table 	= document.getElementById('app_payableList');
	count		= table.rows.length;
	var valid	= 0;
	
	var selected	= 0;
	if(count > 0 && document.getElementById('paymentamount[0]') != null)
	{
		for(var i=0;i<=count;i++)
		{
			var row   = table.rows[i];

			if(row)
			{
				if(row.cells[0].getElementsByTagName("input")[0].checked)
				{
					selected++;
				}
			}
		}
	}

	if(selected == 0 && (count > 1))
	{
		$("#paymentForm #appCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#paymentForm #appCountError").addClass('hidden');
	}
	
	
	if(document.getElementById('paymentamount[0]') != null)
	{	
		if(valid == 0 && count > 0)
		{
			for(var i=0;i<=count;i++)
			{
				var row   = table.rows[i];
				
				if(row)
				{
					if(row.cells[0].getElementsByTagName("input")[0].checked)
					{
						if(document.getElementById('paymentamount['+i+']') != null)
						{
							var qty = document.getElementById('paymentamount['+i+']').value;

							if(parseFloat(qty) <= 0 || qty == '')
							{
								$("#paymentForm #paymentamount\\["+i+"\\]").closest('td').addClass('has-error');
								valid++;
							}
							else
							{
								$("#paymentForm #paymentamount\\["+i+"\\]").closest('td').removeClass('has-error');
							}
						}
					}
				}
			}
	
			if(valid > 0)
			{
				$("#paymentForm #appAmountError").removeClass('hidden');
			}
			else
			{
				$("#paymentForm #appAmountError").addClass('hidden');
			}
		}
	}

	if(valid > 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function applySelected(e)
{
	e.preventDefault();
	
	var paymentvendor		= $("#paymentForm #vendor").val();
	var paymentdate			= document.getElementById('document_date').value;
	var paymentaccount		= document.getElementById('paymentaccount').value;
	var paymentmode			= document.getElementById('paymentmode').value;
	var paymentreference	= document.getElementById('paymentreference').value;
	var paymentnotes		= document.getElementById('paymentnotes').value;
	var valid				= 0;

	// console.log("test: " + paymentnotes);
	
	valid	+= validateField('paymentForm','vendor', "vendor_help");
	valid	+= validateField('paymentForm','document_date', "document_date_help");
	valid	+= validateField('paymentForm','paymentmode', "paymentmode_help");
	
	if(paymentmode == 'cash')
	{
		valid	+= validateField('paymentForm','paymentaccount', "paymentaccount_help");
	}
	else
	{
		valid	+= validateCheques();
		valid	+= totalPaymentGreaterThanChequeAmount();
	}
	
	valid	+= validateInvoices();
	
	if(valid == 0)
	{
		var table 		= document.getElementById('app_payableList');
		var count 		= table.rows.length;
		
		var selected 			= [];
		var selectedamount 		= [];
		
		var selecteddate 		= [];
		var selectedaccount		= [];
		var selectedmode		= [];
		var selectedreference	= [];
		var selectednotes		= [];
		var selectedvendor		= [];
		
		var selectedcheque		= [];
		var selectedchequenumber= [];
		var selectedchequedate	= [];
		var selectedchequeamount= [];
		
		for(var i=0;i<count;i++)
		{
			var row   = table.rows[i];
			
			if(row.cells[0].getElementsByTagName("input")[0].checked)
			{
				var invoiceno 		= document.getElementById('invoice['+i+']').value;
				var paymentamount	= document.getElementById('paymentamount['+i+']').value;
			
				selected.push(invoiceno);
				selectedamount.push(paymentamount);
				
				selecteddate.push(paymentdate);
				selectedaccount.push(paymentaccount);
				selectedmode.push(paymentmode);
				selectedreference.push(paymentreference);
				selectednotes.push(paymentnotes);
				//selectednotes.push(paymentvendor);
			}
		}
		
		/**Multiple Cheque payments**/
		var chequeTable		= document.getElementById('chequeTable');
		var chequeCount		= chequeTable.rows.length - 2;
		
		for(var j=1;j<=chequeCount;j++)
		{
			var chequeRow   = chequeTable.rows[j];
			
			if(document.getElementById('chequeaccount['+j+']').value != '')
			{
				var chequeaccount 	= document.getElementById('chequeaccount['+j+']').value;
				var chequenumber 	= document.getElementById('chequenumber['+j+']').value;
				var chequedate 		= document.getElementById('chequedate['+j+']').value;
				var chequeamount 	= document.getElementById('chequeamount['+j+']').value;
				
				selectedcheque.push(chequeaccount);
				selectedchequenumber.push(chequenumber);
				selectedchequedate.push(chequedate);
				selectedchequeamount.push(chequeamount);
			}
		}
		
		$.post("<?= BASE_URL ?>financials/payment_voucher/ajax/apply_payments",
		{ 
			"invoiceno[]": selected, 
			"paymentdate[]": selecteddate, 
			"paymentnumber[]": '', 
			"paymentaccount[]": selectedaccount,
			"paymentmode[]": selectedmode,
			"paymentreference[]": selectedreference,
			"paymentamount[]": selectedamount,
			"paymentnotes[]": selectednotes,
			"vendor[]": paymentvendor,
			"chequeaccount[]": selectedcheque,
			"chequenumber[]": selectedchequenumber,
			"chequedate[]": selectedchequedate,
			"chequeamount[]": selectedchequeamount
		}).done(function(data)
		{
			showList();
			$('#paymentModal').modal('hide');
		});
	}
}

function clearPayment()
{
	var today	= moment().format("MMM D, YYYY");
	clearInput('vendor');
	showPayableList();
	clearInput('paymentreference');
	clearInput('paymentaccount');
	
	$("#paymentForm #paymentdate").val(today);
	$("#paymentForm #paymentmode").val('cash');
	toggleCheckInfo('cash');
	$("#paymentForm #paymentcheckdate").val('');
}

/**COMPUTE TOTAL PAYMENTS APPLIED**/
function addPaymentAmount() 
{
	var sum 		= 0;
	var subtotal 	= 0;
	
	var subData 	= 0;
	
	var table 	= document.getElementById('app_payableList');
	var count	= table.rows.length;
	
	for(i = 0; i < count; i++) 
	{  
		var inputamt	= document.getElementById('paymentamount['+i+']');
		
		if(document.getElementById('paymentamount['+i+']') != null)
		{          
			if(inputamt.value && inputamt != '0' && inputamt.value != '0.00')
			{                            
				subData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				subData = 0;
			}
			subtotal = parseFloat(subtotal) + parseFloat(subData);
		}	
	}

	subtotal	= Math.round(1000*subtotal)/1000;
	
	document.getElementById('total_payment').value 		= addCommas(subtotal.toFixed(2));	
}

function selectPayable(id,toggle)
{
	var table 		= document.getElementById('app_payableList');
	
	var row   		= table.rows[id];
	var dueamount	= row.cells[4].innerHTML;
	dueamount		= dueamount.replace(/\,/g,'');
	dueamount		= parseFloat(dueamount);
	dueamount		= addCommas(dueamount.toFixed(2));
	
	if(row.cells[0].getElementsByTagName("input")[0].checked)
	{
		if(toggle == 1)
		{
			row.cells[0].getElementsByTagName("input")[0].checked	= false;
			row.cells[5].getElementsByTagName("input")[0].disabled	= true;
			document.getElementById('paymentamount['+id+']').value	= '';
		}
		else
		{
			row.cells[0].getElementsByTagName("input")[0].checked	= true;
			row.cells[5].getElementsByTagName("input")[0].disabled	= false;
			document.getElementById('paymentamount['+id+']').value	= dueamount;
			
			var paymentaccount	= document.getElementById('paymentaccount').value;
		}
	}
	else
	{
		if(toggle == 1)
		{
			row.cells[0].getElementsByTagName("input")[0].checked	= true;
			row.cells[5].getElementsByTagName("input")[0].disabled	= false;
			document.getElementById('paymentamount['+id+']').value	= dueamount;
			
			var paymentaccount	= document.getElementById('paymentaccount').value;
		}
		else
		{
			row.cells[0].getElementsByTagName("input")[0].checked	= false;
			row.cells[5].getElementsByTagName("input")[0].disabled	= true;
			document.getElementById('paymentamount['+id+']').value	= '';
		}
	}
	
	addPaymentAmount();
}

/**CHECK BALANCE**/
function checkBalance(val,id)
{
	var table 		= document.getElementById('app_payableList');
	
	var row   		= table.rows[id];
	var dueamount	= row.cells[4].innerHTML;
	dueamount		= dueamount.replace(/\,/g,'');
	
	var paymentaccount	= document.getElementById('paymentaccount').value;
	
	val	= val.replace(/,/g,'');
	
	if(parseFloat(val) > parseFloat(dueamount))
	{
		bootbox.alert("Payment amount is greater the due amount of this Bill.", function() 
		{
			document.getElementById('paymentamount['+id+']').value = '';
		});
	}
	
	addPaymentAmount();	
}

$(document).ready(function() 
{
	// Load data
	showList();

	$('.monthlyfilter').on('apply.daterangepicker', function() 
	{
		showList();
	});

	$('#vendor').on('change', function(e) 
	{
		showList();
	});

	// Deletion of PV
	$('#deleteModal #btnYes').click(function() 
	{
		// handle deletion here
		var id 		   = $('#deleteModal #recordId').val();

		ajax.voucher 	=	id;

		$.post('<?=BASE_URL?>financials/payment_voucher/ajax/delete_payments', ajax , function(data) 
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

	// Deletion of AP (no payment issued)
	$('#deleteModalAP #btnYes').click(function() 
	{
		// handle deletion here
		var id 		   = $('#deleteModalAP #recordId').val();

		var column	   = {};
		column['stat'] = 'cancelled';

		// Update Details
		$.post("<?= BASE_URL ?>financials/accounts_payable/ajax/update",{ table: "ap_details", condition: "voucherno = '"+id+"'", fields : column });

		// Update Header
		$.post("<?= BASE_URL ?>financials/accounts_payable/ajax/update",{ table: "accountspayable", condition: "voucherno = '"+id+"'", fields : column })
		.done(function( data ) 
		{
			if(data.msg == "success")
			{
				$('#deleteModalAP').modal('hide');
				showList();
			}
		});
	});


	/*
	* For Payment Form Validations
	*/
	$('#paymentForm #vendor').on('change', function(e) 
	{
		showPayableList(); 
		validateField('paymentForm',e.target.id, e.target.id + "_help");
	});

	$('.payment_mode').on('change', function(e) 
	{
		toggleCheckInfo(e.target.value);
		validateField('paymentForm', e.target.id, e.target.id + "_help");
	});

	$('.pay_account').on('change', function(e) 
	{
		validateField('paymentForm',e.target.id, e.target.id + "_help"); 
		// validateCheck();
	});

	$('#paymentreference').on('blur', function(e) 
	{
		// validateCheck();

		if($("#paymentmode").val() == "cheque")
			validateField('paymentForm', e.target.id, e.target.id + "_help");
	});

	$('.chequeamount').on('blur click', function(e) 
	{
		if(e.type == "blur")
		{
			formatNumber(e.target.id); 
			addAmounts();
		}
		if(e.type == "click")
		{
			SelectAll(e.target.id);
		}
	});


	/**ADD NEW BANK ROW**/
	$('body').on('click', '.add-data', function() 
	{
		$('#chequeTable tbody tr.clone select').select2('destroy');

		var clone = $("#chequeTable tbody tr.clone:first").clone(true);

		var ParentRow = $("#chequeTable tbody tr.clone").last();
		
		clone.clone(true).insertAfter(ParentRow);
		
		setZero();
		
		$('#chequeTable tbody tr.clone select').select2({width: "100%"});
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({autoclose: true});
	});
	
	$('#app_payableList').on('ifToggled', '.icheckbox', function(event)
	{
		event.type = "checked";
		var selectid = $(this).attr('row');
		var selecttoggleid = $(this).attr('toggleid');
		
		selectPayable(selectid,selecttoggleid);
	});

	/*
	* For Import Modal
	*/
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

	// For Pagination
	$('#page_links').on('click', 'a', function(e) 
	{
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		showList();
	});


});

</script>