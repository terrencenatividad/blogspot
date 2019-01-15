<section class="content">

	<div class="box box-primary">

		<form id = "requestorDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			<input class = "form_iput" value = "<?=$h_disctype?>" name = "h_disctype" id = "h_disctype" type="hidden">
		</form>

		<form method = "post" class="form-horizontal" id = "purchase_request_form">
			
			<div class = "row">
                <div class = "col-md-12">&nbsp;</div>
			</div>

			<div class = "row">
				<div class = "col-md-6">
					<?php
						echo $ui->formField('text')
								->setLabel('Request No:')
								->setSplit('col-md-3', 'col-md-8')
								->setName('voucher_no')
								->setId('voucher_no')
								->setAttribute(array("disabled" => "disabled"))
								->setPlaceholder("- auto generate -")
								->setValue($voucherno)
								->draw($show_input);
					?>
					<input type = "hidden" id = "h_voucher_no" name = "h_voucher_no" value = "<?= $generated_id ?>">
				</div>

				<div class = "col-md-6">
					<?php
						echo $ui->formField('text')
							->setLabel('Transaction Date')
							->setSplit('col-md-3', 'col-md-8')
							->setName('transaction_date')
							->setId('transaction_date')
							->setClass('datepicker-input')
							->setAttribute(array('readonly' => '', 'data-date-start-date' => $close_date))
							->setAddon('calendar')
							->setValue($transactiondate)
							->setValidation('required')
							->draw($show_input);
					?>
				</div>
				
			</div>

			<div class = "row">
				<div class = "col-md-6 requestor_div">
					<?php
						echo $ui->formField('dropdown')
							->setLabel('Requestor')
							->setPlaceholder('None')
							->setSplit('col-md-3', 'col-md-8')
							->setName('requestor')
							->setId('requestor')
							->setList($requestor_list)
							->setValue($requestor)
							->setValidation('required')
							// ->setButtonAddon('plus')
							->draw($show_input);
					?>
				</div>

				<div class = "col-md-6">
					<?php
						echo $ui->formField('text')
							->setLabel('Expiration Date')
							->setSplit('col-md-3', 'col-md-8')
							->setName('due_date')
							->setId('due_date')
							->setClass('datepicker-input')
							->setAttribute(array('readonly' => ''))
							->setAddon('calendar')
							->setValue($due_date)
							->setValidation('required')
							->draw($show_input);
					?>
				</div>
			</div>

			<div class = "row">
				<div class = "col-md-6">
					<?php
						echo $ui->formField('text')
							->setLabel('Department:')
							->setSplit('col-md-3', 'col-md-8')
							->setName('department')
							->setId('department')
							->setValue($department)
							// ->setValidation('required') 
							->draw($show_input);
					?>
				</div>
				<div class = "col-md-6">
					<?php
						echo $ui->formField('textarea')
							->setLabel('Notes:')
							->setSplit('col-md-3', 'col-md-8')
							->setName('remarks')
							->setId('remarks')
							->setValue($remarks)
							->draw($show_input);
					?>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="table-responsive">
					<table class="table table-hover table-condensed " id="itemsTable">
						<thead>
							<tr class="info">
								<th class="col-md-3 text-center">Item</th>
								<th class="col-md-4 text-center">Description</th>
								<th class="col-md-2 text-center">Qty</th>
								<th class="col-md-2 text-center">UOM</th>
								<th class="col-md-1 text-center"></th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($task == 'create')
								{
									$accountcode 	   	= '';
									$detailparticulars 	= '';
									$price	   			= '0.00';
									$rowamount 			= '0.00';
									$quantity 		 	= 1;
									$uom 		 		= '';
									$row 			   	= 1;
									$total_debit 	   	= 0;
									$total_credit 	   	= 0;
									$vatable_sales 	   	= 0;
									$vat_exempt_sales 	= 0;
									$t_subtotal 		= 0;
									$t_discount  		= 0;
									$t_total 			= 0;
									$t_vat 				= 0;
									$t_vatsales 		= 0;
									$t_vatexempt 		= 0;
									$discount_check_amt = 0;
									$discount_check_perc= 0;
									$startnumber 	   	= ($row_ctr == 0) ? 1: $row_ctr;

							?>
									<tr class="clone" valign="middle">
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setName("itemcode[".$row."]")
													->setId("itemcode[".$row."]")
													->setClass('itemcode')
													->setList($itemcodes)
													->setValidation('required')
													->setValue("")
													->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('detailparticulars['.$row.']')
														->setId('detailparticulars['.$row.']')
														->setAttribute(array("maxlength" => "100"))
														->setValue("")
														->setValidation('required')
														->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('quantity['.$row.']')
														->setId('quantity['.$row.']')
														->setClass("text-right quantity")
														->setAttribute(array("maxlength" => "20"))
														->setValue($quantity)
														->setValidation('required integer')
														->draw($show_input);
											?>
										</td>	
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('uom['.$row.']')
														->setId('uom['.$row.']')
														->setClass('uom text-right')
														->setAttribute(array("maxlength" => "20", "readonly" => "readonly"))
														->setValue($uom)
														->draw($show_input);
											?>
										</td>
										
										<td class="text-center">
											<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
										</td>			
									</tr>
							<?
									$row++;
							?>
									<!--<tr class="clone" valign="middle">
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setName("itemcode[".$row."]")
													->setId("itemcode[".$row."]")
													->setClass('itemcode')
													->setList($itemcodes)
													->setValue("")
													->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('detailparticulars['.$row.']')
														->setId('detailparticulars['.$row.']')
														->setAttribute(array("maxlength" => "100"))
														->setValue("")
														->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('quantity['.$row.']')
														->setId('quantity['.$row.']')
														->setClass("text-right price")
														->setAttribute(array("maxlength" => "20"))
														->setValue($quantity)
														->setValidation('integer')
														->draw($show_input);
											?>
											<?php
												echo $ui->setElement('hidden')
														->setSplit('', 'col-md-12')
														->setName('uom['.$row.']')
														->setId('uom['.$row.']')
														->setClass('uom text-right')
														->setAttribute(array("maxlength" => "20"))
														->setValue($uom)
														->draw($show_input);
											?>
										</td>	
										<td class="text-center">
											<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
										</td>			
									</tr>-->
							<?
								}
								else if(!empty($sid) && $task!='create')
								{
									$row 			= 1;
									$disable_debit	= '';
									$disable_credit	= '';
									
									for($i = 0; $i < count($details); $i++)
									{
										$itemcode 	 		= $details[$i]->itemcode;
										$detailparticular	= $details[$i]->detailparticular;
										$quantity 			= $details[$i]->receiptqty;
										$uom 				= $details[$i]->receiptuom;
										$itemprice 			= $details[$i]->unitprice;
										$taxcode 			= $details[$i]->taxcode;
										$taxrate 			= $details[$i]->taxrate;
										$amount  			= $details[$i]->amount;
											
							?>	
										<tr class="clone" valign="middle">
											<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setName("itemcode[".$row."]")
													->setId("itemcode[".$row."]")
													->setClass('itemcode')
													->setList($itemcodes)
													->setValue($itemcode)
													->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('detailparticulars['.$row.']')
														->setId('detailparticulars['.$row.']')
														->setAttribute(array("maxlength" => "100"))
														->setValue($detailparticular)
														->draw($show_input);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('quantity['.$row.']')
														->setId('quantity['.$row.']')
														->setClass("text-right quantity")
														->setAttribute(array("maxlength" => "20"))
														->setValue($quantity)
														->setValidation('integer')														
														->draw($show_input);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('uom['.$row.']')
														->setId('uom['.$row.']')
														->setClass('uom text-right')
														->setAttribute(array("maxlength" => "20", "readonly" => "readonly"))
														->setValue($uom)
														->draw($show_input);
											?>
										</td>
										<?php if($task!='view'){ ?>
										<td class="text-center">
											<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
										</td>	
										<?php }	?>	
										
										</tr>
								<?	
										$row++;	
									}
								}
							?>
						</tbody>
						<tfoot class="summary">
						<tr>
								<td>
									<? if($task != 'view') { ?>
										<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
									<? } ?>
								</td>	
							</tr>	
						</tfoot>
					</table>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 col-sm-12 text-center">
					<?php

					if( $show_input )
					{
						$save		= ($task == 'create') ? 'name="save"' : '';
						$save_new	= ($task == 'create') ? 'name="save_new"' : '';
					?>
						<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
						
						<div class="btn-group" id="save_group">
							<button  type="button" id="btnSave" class="btn btn-primary btn-sm">Save</button>
							<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm " data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu left" role="menu">
								<li style="cursor:pointer;" id="save_new">
									&nbsp;&nbsp;Save & New
									<input type = "hidden" value = "" name = "h_save_new" id = "h_save_new"/>
								</li>
								<li class="divider"></li>
								<li style="cursor:pointer;" id="save_preview">
									&nbsp;&nbsp;Save & Preview
									<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_new"/>
								</li>
							</ul>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<!--<button type="button" class="btn btn-default" data-id="<?=$generated_id?>"  id="btnCancel">Cancel</button>-->
							<a href="<?=MODULE_URL?>" class="btn btn-default back" data-toggle="back_page">Cancel</a>
						</div>
					<? 	
					}
					else
					{ 	
						if( $restrict_req && $stat != 'cancelled'){
					?>
						<div class="btn-group">
							<a class="btn btn-primary" role="button" href="<?=BASE_URL?>purchase/purchase_request/edit/<?=$sid?>" style="outline:none;">Edit</a>
						</div>
						&nbsp;&nbsp;&nbsp;
					<?	}	?>
						<div class="btn-group">
							<!--<a class="btn btn-default" role="button" href="<?=MODULE_URL?>" style="outline:none;">Cancel</a>-->
							<a href="<?=MODULE_URL?>" class="btn btn-default back" data-toggle="back_page">Cancel</a>
						</div>
					<?
					}
					?>
				</div>
			</div>
			
			<div class="row">
				<div class = "col-md-12">&nbsp;</div>
			</div>

		</form>

	</div>

</section>

<?php 
	if (isset($modal_script)) {
		echo $modal_script;
	}
?>

<script>
	function addVendorToDropdown() {

		var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
		var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

		$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#purchase_request_form #requestor option:last-child");
		$('#purchase_request_form #requestor').val(optionvalue);
		
		getPartnerInfo(optionvalue);

		$('#vendor_modal').modal('hide');
		$('#vendor_modal').find("input[type=text], textarea, select").val("");
	}
	function closeModal(){
		$('#vendor_modal').modal('hide');
	}

	$(document).ready(function(){
		$('.back').click(function(){
			parent.history.back();
			return false;
	});

});

</script>
<?php
	// echo $ui->loadElement('modal')
	// 	->setId('vendor_modal')
	// 	->setContent('maintenance/supplier/create')
	// 	->setHeader('Add a Vendor')
	// 	->draw();
?>

<!-- requestor Modal -->
<div class="modal fade" id="requestorModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Add a Requestor
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="newrequestor" autocomplete="off">
					<input class = "form_iput" value = "newrequestor" name = "h_form" id = "h_form" type="hidden">
					<div class="alert alert-warning alert-dismissable hidden" id="requestorAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class = "well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Requestor Code: <span class="asterisk"> * </span>')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('partnercode')
										->setId('partnercode')
										->setAttribute(array("maxlength" => "20"))
										->setValue("")
										->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "partnercode_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
									->setLabel('First Name')
									->setSplit('col-md-3', 'col-md-8 field_col')
									->setName('first_name')
									->setId('first_name')
									->setValue("")
									->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "first_name_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
							<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
									->setLabel('Last Name')
									->setSplit('col-md-3', 'col-md-8 field_col')
									->setName('last_name')
									->setId('last_name')
									->setValue("")
									->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "last_name_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('Email:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('email')
										->setId('email')
										->setAttribute(array("maxlength" => "150"))
										->setPlaceHolder("email@oojeema.com")
										->setValue("")
										->draw($show_input);
							?>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('textarea')
										->setLabel('Address: ')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('address1')
										->setId('address1')
										->setAttribute(array("rows" => "1"))
										->setValue("")
										->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "address_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Business Type: <span class="asterisk"> * </span>')
									->setPlaceholder('Filter Business Type')
									->setSplit('col-md-3', 'col-md-8 field_col')
									->setName('businesstype')
									->setId('businesstype')
									->setList($business_type_list)
									->setValue("")
									->draw($show_input);

							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "businesstype_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('TIN:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('tinno')
										->setId('tinno')
										->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
										->setPlaceholder('000-000-000-000')
										->setValue("")
										->draw($show_input);
							?>
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('Terms:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('terms')
										->setId('terms')
										->setAttribute(array("maxlength" => "5"))
										->setValue("30")
										->draw($show_input);
							?>
						</div>
					</div>
					<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-info btn-flat" id="requestorBtnSave">Save</button>
									</div>
										&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<!--<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>-->
										<a href="<?=MODULE_URL?>" class="btn btn-default btn-flat back" data-toggle="back_page">Cancel</a>
									</div>
								</div>
							</div>
						</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Vendor Modal -->

<!-- Delete Record Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
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
					<div class="col-md-12 center">
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
<!-- End Delete Record Confirmation Modal -->

<!--DELETE RECORD CONFIRMATION MODAL-->
<div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to cancel this transaction?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
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
<!-- End DELETE RECORD CONFIRMATION MODAL-->

<script>
var ajax = {};

/**RETRIEVES requestor INFORMATION**/
function getPartnerInfo(code)
{
	var cmp = '<?= $cmp ?>';

	if(code == '' || code == 'add')
	{
		$("#requestor_tin").val("");
		$("#requestor_terms").val("");
		$("#requestor_address").val("");

		computeDueDate();
	}
	else
	{
		$.post('<?=BASE_URL?>purchase/purchase_request/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
		{
			// var address		= data.address.trim();
			// var tinno		= data.tinno.trim();
			// var terms		= data.terms.trim();
			
			// $("#requestor_tin").val(tinno);
			// $("#requestor_terms").val(terms);
			// $("#requestor_address").val(address);

			computeDueDate();
		});
	}
}

/**COMPUTES DUE DATE**/
function computeDueDate()
{
	var invoice = $("#transaction_date").val();
	var terms 	= $("#requestor_terms").val(); 

	if(invoice != '')
	{
		var newDate	= moment(invoice).add(7, 'days').format("MMM DD, YYYY");
		
		$("#due_date").val(newDate);
	}
}

/**FORMATS PRICES INPUT**/
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

/**FOR ADD NEW requestor TRANSACTION**/
function addNewModal(type,val,row)
{
	row 		= row.replace(/[a-z]/g, '');
	
	if(val == 'add')
	{
		if(type == 'requestor')
		{
			$('#requestorModal').modal();
			$('#requestor').val('');
		}
	}
}

/**RETRIEVAL OF ITEM DETAILS**/
function getItemDetails(id)
{

	var itemcode 	=	document.getElementById(id).value;
	var row 		=	id.replace(/[a-z]/g, '');

	$.post('<?=BASE_URL?>purchase/purchase_request/ajax/get_item_details',"itemcode="+itemcode , function(data) 
	{
		if (data != false){
			document.getElementById('detailparticulars'+row).value 	=	data.itemdesc;
			document.getElementById('uom'+row).value 	 			=	data.uomcode;
			$('#purchase_request_form').trigger('change'); 
		} else {
			document.getElementById('detailparticulars'+row).value 		=	"";			
			document.getElementById('uom'+row).value 	 				=	"";	
			document.getElementById('quantity'+row).value 				= 	"1";

			$('#purchase_order_form').trigger('change');
		}

		// computeAmount();
	});

}



/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

/**VALIDATION FOR NUMERIC FIELDS**/
function isNumberKey(evt,exemptChar) 
{
	if(evt.which != 0)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode 
		if(charCode == exemptChar) return true; 
		if (charCode > 31 && (charCode < 48 || charCode > 57)) 
		return false; 
		return true;
	}
}

/**LIMIT INPUT TO NUMBERS ONLY**/
function isNumberKey2(evt) 
{

	if(evt.which != 0){
		var charCode = (evt.which) ? evt.which : evt.keyCode 
		if(charCode == 46) return true; 
		if (charCode > 31 && (charCode < 48 || charCode > 57)) 
		return false; 
		return true;
	}
}

/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
	var field	= $("#"+form+" #"+id).val();

	if(id.indexOf('_chosen') != -1){
		var id2	= id.replace("_chosen","");
		field	= $("#"+form+" #"+id2).val();

	}

	if(field == '' || parseFloat(field) == 0)
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');

		$("#"+form+" #"+help_block)
			// .next(".help-block")
			.removeClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0])
		{
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.removeClass('hidden');
		}
		return 1;
	}
	else
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+help_block) //$("#"+form+" #"+id)
			// .next(".help-block")
			.addClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0])
		{
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.addClass('hidden');
		}
		return 0;
	}
}

/**RESET IDS OF ROWS**/
function resetIds()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.tBodies[0].rows.length;

	x = 1;
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];

		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'uom['+x+']';
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'uom['+x+']';
		
		row.cells[4].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

		x++;
	}
	
}

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero()
{
	resetIds();
	
	var table 		= document.getElementById('itemsTable');
	var newid 		= table.tBodies[0].rows.length;
	//alert(newid);

	document.getElementById('itemcode['+newid+']').value 			= '';
	document.getElementById('detailparticulars['+newid+']').value 	= '';
	document.getElementById('quantity['+newid+']').value 			= '1';
	document.getElementById('uom['+newid+']').value 				= '';

	// $('#itemcode\\['+newid+'\\]').trigger('change');
	if (table ==  null){
		document.getElementById('quantity['+newid+']').value 			= '1';
	} else {
		document.getElementById('quantity['+newid+']').value 			= '1';

	}
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	var voucher		= document.getElementById('h_voucher_no').value;

	$.post("<?=BASE_URL?>purchase/purchase_request/ajax/cancel",'<?=$ajax_post?>')
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>purchase/purchase_request';
		}
	});
}

/** FINALIZE SAVING **/
function finalizeTransaction(type)
{
	$("#purchase_request_form").find('.form-group').find('input, textarea, select').trigger('blur');

	var no_error = true;
	$('.quantity').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			$(this).closest('div').addClass('has-error');
		}
	});

	if($("#purchase_request_form").find('.form-group.has-error').length == 0 && no_error)
	{	
		// computeAmount();
		$('#save').val(type);
		var btn 	=	$('#save').val();
		if($("#purchase_request_form #itemcode\\[1\\]").val() != '' && $("#purchase_request_form #detailparticular\\[1\\]").val() != '' && $("#purchase_request_form #transaction_date").val() != '' && $("#purchase_request_form #customer").val() != '')
		{
			setTimeout(function() {
				// $('#purchase_request_form').submit();
				$.post("<?=BASE_URL?>purchase/purchase_request/ajax/<?=$task?>",$("#purchase_request_form").serialize()+'<?=$ajax_post?>',function(data)
				{		
					if( data.msg == 'success' )
					{
						if( btn == 'final' )
						{
							// window.location 	=	"<?=BASE_URL?>purchase/purchase_request";
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = 	"<?=BASE_URL?>purchase/purchase_request";
							}, 1000);
						}
						else if( btn == 'final_preview' )
						{
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = 	"<?=BASE_URL?>purchase/purchase_request/view/"+data.voucher;
								}, 1000);
							}
						else if( btn == 'final_new' )
						{
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = 	"<?=BASE_URL?>purchase/purchase_request/create";
								}, 1000);
						}
						
					}
					else
					{
						//insert error message / MOdal heree
						
					}
				});
			},1000);
		}
		
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var btn 	=	$('#save').val();
				
	$("#purchase_request_form").find('.form-group').find('input, textarea, select').trigger('blur');

	var no_error = true;
	$('.quantity').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			$(this).closest('div').addClass('has-error');
		}
	});

	if($('#purchase_request_form').find('.form-group.has-error').length == 0)
	{
		if($("#purchase_request_form #itemcode\\[1\\]").val() != '' && $("#purchase_request_form #transaction_date").val() != '' && $("#purchase_request_form #due_date").val() != '' && $("#purchase_request_form #customer").val() != '')
		{
			setTimeout(function() {

				$.post("<?=BASE_URL?>purchase/purchase_request/ajax/<?=$task?>",$("#purchase_request_form").serialize()+'<?=$ajax_post?>',function(data)
				{		
					if( data.msg == 'success' )
					{
						if( btn == 'final' )
						{
							// window.location 	=	"<?=BASE_URL?>purchase/purchase_request";
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = 	"<?=BASE_URL?>purchase/purchase_request";
							}, 1000);
						}
						else if( btn == 'final_preview' )
						{
							window.location 	=	"<?=BASE_URL?>purchase/purchase_request/view/"+data.voucher;
						}
						else if( btn == 'final_new' )
						{
							window.location 	=	"<?=BASE_URL?>purchase/purchase_request/create";
						}
						
					}
					else
					{
						//insert error message / MOdal heree
						
					}
				});
			},1000);
		}
	}
	else 
	{
		$("#purchase_request_form").find('.form-group.has-error').first().find('input, textarea, select').focus();
	}
}

/** CONFIRMATION OF DELETION OF ROW DURING TRANSACTION**/
function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}

/** DELETION OF ROW DURING TRANSACTION **/
function deleteItem(row)
{
	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= '<?= COMPANYCODE ?>';
	var requestor 	= document.getElementById('requestor').value;
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.tBodies[0].rows.length;;
	var valid		= 1;

	var rowindex	= table.rows[row];
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'purchaserequest_details';

		if(rowCount > 1)
		{
			if(task == 'create')
			{
				ajax.table 		=	datatable;
				ajax.linenum 	= 	row;
				ajax.voucherno 	= 	voucher;

				$.post("<?=BASE_URL?>purchase/purchase_request/ajax/delete_row",ajax)
				.done(function( data ) 
				{
					if( data.msg == 'success' )
					{
						table.deleteRow(row);	
						resetIds();
						// addAmounts();
					}
				});
			}
			else
			{
				table.deleteRow(row);	
				resetIds();
				// addAmounts();
			}
		}
		else
		{	
			setZero();
			// addAmounts();
		}
	}
	else
	{
		// console.log("else 2");
		if(rowCount > 2)
		{
			table.deleteRow(row);	
			resetIds();
			// addAmounts();
		}
	}
}

$(document).ready(function(){

	// -- For Date -- 

		//Date picker
		$('.datepicker, .datepicker_').datepicker({
			autoclose: true
		});

		// Set default date to date of the day
		$(".datepicker").datepicker("setDate", new Date());

	// -- For Date -- End

	// -- For requestor -- 
		// Open Modal
		$('#vendor_button').click(function()
		{
			$('#vendor_modal').modal('show');
		});

		// Add new requestor
		$("#newrequestor #requestorBtnSave").click(function()
		{
			var valid	= 0;

			valid		+= validateField('newrequestor','partnercode', "partnercode_help");
			valid		+= validateField('newrequestor','first_name', "first_name_help");
			valid		+= validateField('newrequestor','last_name', "last_name_help");
			valid		+= validateField('newrequestor','address1', "address_help");
			valid		+= validateField('newrequestor','businesstype', "businesstype_help");

			if(valid == 0)
			{
				$.post('<?=BASE_URL?>purchase/purchase_request/ajax/save_requestor', $("#newrequestor").serialize(), function(data) 
				{
					var optionvalue = $("#newrequestor #partnercode").val();
					var optiondesc 	= $("#newrequestor #first_name").val() + " " + $("#newrequestor #last_name").val();
					console.log(optiondesc);
					if(data.msg == "success")
					{
						$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#purchase_request_form #requestor option:nth-child(4)");
						$('#purchase_request_form #requestor').val(optionvalue);
						
						getPartnerInfo(optionvalue);

						$('#requestorModal').modal('hide');
						$('#requestorModal').find("input[type=text], textarea, select").val("");
						return true;
					}
					else
					{
						$("#requestorAlert p").html(data.msg);
						$("#requestorAlert").removeClass('hidden');
					}
				});
			}
		});

		// Get getPartnerInfo
		$( "#requestor" ).change(function() 
		{
			$requestor_id = $("#requestor").val();

			if( $requestor_id != "" )
				getPartnerInfo($requestor_id);
		});

		// Validation for Tinno, Terms
		$('#requestor_tin, #requestor_terms').on('keypress blur click', function(e) 
		{
			if(e.type == "keypress")
				return isNumberKey(e,45);
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
		});

		$('#requestor_address').on('blur click', function(e) 
		{
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
		});

		$('#transaction_date').on('change', function(e) 
		{
			computeDueDate();
		});

		$('#tinno').on('keypress blur', function(e) 
		{
			if(e.type == "keypress")
				return isNumberKey(e,45);
		});

		$('#terms').on('keypress', function(e) 
		{
			if(e.type == "keypress")
				return isNumberKey(e,45);
		});

		// Validation for requestor Modal
		$('#partnercode, #first_name, #last_name, #address1, #businesstype').on('keyup', function(e) 
		{
			validateField('newrequestor',e.target.id, e.target.id + "_help");
		});

		$('#businesstype').on('change', function(e) 
		{
			validateField('newrequestor',e.target.id, e.target.id + "_help");
		});

		computeDueDate();

	// -- For requestor -- End

	// -- For Items -- 
		//For Edit
		// computeAmount();

		$('.itemcode').on('change', function(e) 
		{
			var id = $(this).attr("id");
			getItemDetails(id);
		});


		$('.price').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');

			formatNumber(id);
		});

		// For adding new roll
		$('body').on('click', '.add-data', function() 
		{	
			
			$('#itemsTable tbody tr.clone select').select2('destroy');
			
			var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

			var ParentRow = $("#itemsTable tbody tr.clone").last();
		
			clone.clone(true).insertAfter(ParentRow);
			
			setZero();
			
			$('#itemsTable tbody tr.clone select').select2({width: "100%"});
		});
		
	// -- For Items -- End

	// -- For Discount -- 

		$('.d_opt').on('change',function(){
			var disc_id =	$('input[type=radio][name=discounttype]:checked').attr('id');

			var type 	=	$(this).val();
			$('#h_disctype').val(type);
			
			//computeTotalAmount();
		});

	// -- For Discount -- End

	// -- For Saving -- 

		// Process New Transaction
		if('<?= $task ?>' == "create")
		{
			$("#purchase_request_form").change(function()
			{
				if($("#purchase_request_form #itemcode\\[1\\]").val() != '' && $("#purchase_request_form #transaction_date").val() != '' && $("#purchase_request_form #due_date").val() != '' && $("#purchase_request_form #requestor").val() != '')
				{
					$.post("<?=BASE_URL?>purchase/purchase_request/ajax/save_temp_data",$("#purchase_request_form").serialize())
					.done(function(data)
					{	
						
					});
				}
			});

			//Final Saving
			$('#purchase_request_form #btnSave').click(function(){

				// $('#save').val("final");
				finalizeTransaction("final");

			});

			//Save & Preview
			$("#purchase_request_form #save_preview").click(function()
			{
				// $('#save').val("final_preview");
				finalizeTransaction("final_preview");
			});

			//Save & New
			$("#purchase_request_form #save_new").click(function()
			{
				// $('#save').val("final_new");
				finalizeTransaction("final_new");
			});
		}
		else if('<?= $task ?>' == "edit") 
		{
			//Final Saving
			$('#purchase_request_form #btnSave').click(function(){
				
				$('#save').val("final");

				finalizeEditTransaction();
			});

			//Save & Preview
			$("#purchase_request_form #save_preview").click(function()
			{
				$('#save').val("final_preview");

				finalizeEditTransaction();
			});

			//Save & New
			$("#purchase_request_form #save_new").click(function()
			{
				$('#save').val("final_new");

				finalizeEditTransaction();
			});
		}

		else if('<?= $task ?>' == "create_so") 
		{
			//Final Saving
			$('#purchase_request_form #btnSave').click(function(){
				
				$('#save').val("final");

				finalizeEditTransaction();
			});

			//Save & Preview
			$("#purchase_request_form #save_preview").click(function()
			{
				$('#save').val("final_preview");

				finalizeEditTransaction();
			});

			//Save & New
			$("#purchase_request_form #save_new").click(function()
			{
				$('#save').val("final_new");

				finalizeEditTransaction();
			});
		}
	// -- For Saving -- End

	// -- For Cancel -- 

		/**SCRIPT FOR HANDLING DELETE RECORD CONFIRMATION**/
		$('#btnCancel').click(function() 
		{
			$('#cancelModal').modal('show');
		});

		$('#cancelModal #btnYes').click(function() 
		{
			var task = '<?= $task ?>';
			
			if(task != 'view')
			{
				var record = document.getElementById('h_voucher_no').value;
				cancelTransaction(record);
			}
		});

	// -- For Cancel -- End

	// -- For Deletion of Item Per Row -- 
	
		$('#deleteItemModal #btnYes').click(function() 
		{
			var id = $('#deleteItemModal').data('id');

			var table 		= document.getElementById('itemsTable');
			var rowCount 	= table.tBodies[0].rows.length;;

			deleteItem(id);
			
			$('#deleteItemModal').modal('hide');
		});
		
	// -- For Deletion of Item Per Row -- End
});

$('#requestor_button').click(function()
{
	$('#requestorModal').modal('show');
});
		


</script>