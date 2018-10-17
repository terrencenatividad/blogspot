<section class="content">

	<div class="box box-primary">

		<form id = "CustomerDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			<input class = "form_iput" value = "<?=$h_disctype?>" name = "h_disctype" id = "h_disctype" type="hidden">
		</form>

		<form method = "post" class="form-horizontal" id = "sales_quotation_form">
			
			<div class = "row">
                <div class = "col-md-12">&nbsp;</div>
			</div>

			<div class = "row">
				<div class = "col-md-6">
					<?php
						echo $ui->formField('text')
								->setLabel('Quotation No:')
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
				<div class = "col-md-6 customer_div">
					<?php
							echo $ui->formField('dropdown')
								->setLabel('Customer ')
								->setPlaceholder('None')
								->setSplit('col-md-3', 'col-md-8')
								->setName('customer')
								->setId('customer')
								->setList($customer_list)
								->setValue($customer)
								->setValidation('required')
								->setButtonAddon('plus')
								->draw($show_input);
					?>
				</div>

				<div class = "col-md-6 judith">
					<?php
						echo $ui->formField('text')
							->setLabel('Expiration Date ')
							->setSplit('col-md-3', 'col-md-8')
							->setName('due_date')
							->setId('due_date')
							->setClass('datepicker-input')
							->setAttribute(array('readonly' => '', 'data-date-start-date' => $close_date))
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
								<th class="col-md-2 text-center">UOM</th>
								<th class="col-md-2 text-center">Price</th>
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
									$issueuom 			= '';
									$rowamount 			= '0.00';
									$quantity 		 	= 1;
									$row 			   	= 1;
									$total_debit 	   	= 0;
									$total_credit 	   	= 0;
									$vatable_sales 	   	= 0;
									$vat_exempt_sales 	= 0;
									$t_subtotal 		= 0;
									$t_discount  		= 0;
									$t_total 			= 0;
									$amount 			= 0;
									$t_vat 				= 0;
									$t_vatsales 		= 0;
									$t_vatexempt 		= 0;
									$discount_check_amt = 0;
									$discount_check_perc = 0;

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
													->setValue("")
													->setValidation('required')
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
														->setName('issueuom['.$row.']')
														->setId('issueuom['.$row.']')
														->setClass("text-right issueuom")
														->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
														->setValue($issueuom)
														->draw($show_input);
											?>
										</td>	
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 pricey')
														->setName('itemprice['.$row.']')
														->setId('itemprice['.$row.']')
														->setClass("text-right price")
														->setAttribute(array("maxlength" => "20"))
														->setValue($price)
														->setValidation('required')
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
													->setList($itemcodes)
													->setClass('itemcode')
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
														->setName('itemprice['.$row.']')
														->setId('itemprice['.$row.']')
														->setClass("price text-right")
														->setAttribute(array("maxlength" => "20"))
														->setValue($price)
														->setValidation('required')
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
										$quantity 			= $details[$i]->issueqty;
										$itemprice 			= $details[$i]->unitprice;
										$taxcode 			= $details[$i]->taxcode;
										$taxrate 			= $details[$i]->taxrate;
										$issueuom 			= $details[$i]->issueuom;
										// $amount  			= $details[$i]->amount;
											
								?>	
										<tr class="clone" valign="middle">
											<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setName("itemcode[".$row."]")
													->setId("itemcode[".$row."]")
													->setList($itemcodes)
													->setClass('itemcode')
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
														->setName('issueuom['.$row.']')
														->setId('issueuom['.$row.']')
														->setClass("issueuom")
														->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
														->setValue($issueuom)
														->draw($show_input);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('itemprice['.$row.']')
														->setId('itemprice['.$row.']')
														->setClass("price text-right")
														->setAttribute(array("maxlength" => "20"))
														->setValue($itemprice)
														->draw($show_input);
											?>
										</td>
										<?if($task!='view'){ ?>
											<td class="text-center">
												<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
											</td>
										<?}?>		
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
						<tr id="total_amount_due">
							<td colspan = '2'></td>
							<td class="right">
								<label class="control-label col-md-12">Total Price</label>
							</td>
							<td class="text-right" style="border-top:1px solid #DDDDDD;">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('amount')
											->setId('amount')
											->setClass("input_label text-right")
											->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
											->setValue(number_format($amount,2))
											->draw($show_input);
								?>
							</td>
						</tr>	
						
						</tfoot>
					</table>
				</div>
			</div>

			<!-- <div class="row">
				<div class="col-md-12 col-sm-12 text-center">
					<?php

					if( $show_input )
					{
						$save		= ($task == 'create') ? 'name="save"' : '';
						$save_new	= ($task == 'create') ? 'name="save_new"' : '';
					?>
						<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
						
						<div class="btn-group" id="save_group">
							<button   type="button" id="btnSave" class="btn btn-primary btn-sm">&nbsp;Save&nbsp;</button>
							<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown">
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
							<a href="<?=MODULE_URL?>"class="btn btn-default back" data-toggle="back_page" >Cancel</a>
						</div>
					<? 	
					}
					else
					{ 	
					?>
						<div class="btn-group">
							<a class="btn btn-primary" role="button" href="<?=BASE_URL?>sales/sales_quotation/edit/<?=$sid?>" style="outline:none;">Edit</a>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<a href="<?=MODULE_URL?>" class="btn btn-default back" data-toggle="back_page">Cancel</a>
						</div>
					<?
					}
					?>
					
				</div>
			</div> -->

			<div class="box-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 text-center">
						<?php
							$save		= ($task == 'create') ? 'name="save"' : '';
							$save_new	= ($task == 'create') ? 'name="save_new"' : '';
						?>
							<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
						<?php 	
							echo $ui->loadElement('check_task')
									->addSave(($task == 'create'))
									->addOtherTask('Save','',($task == 'edit'),'primary')
									->addEdit(($task == 'view' && ( $stat != 'expired' && $restrict_sq ) ))
									->setValue($voucherno)
									->draw_button($show_input);

						?>
						&nbsp;&nbsp;&nbsp;
						<?  
						if( $task != "view" )
						{
						?>
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-flat" data-id="<?php echo $generated_id?>" id="btnCancel" data-toggle="back_page">Cancel</button>
							</div>
						<?
						}
						else
						{
						?>
							<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
						<?
						}
						?>
					</div>
				</div>
				
				<div class="row">
					<div class = "col-md-12">&nbsp;</div>
				</div>
			</div>
			
			<div class="row">
				<div class = "col-md-12">&nbsp;</div>
			</div>

		</form>

	</div>
</section>

<script>
function addCustomerToDropdown() {
	var optionvalue = $("#customer_modal #customerForm #partnercode").val();
	var optiondesc 	= $("#customer_modal #customerForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optionvalue+" - "+optiondesc+'</option>').insertAfter("#sales_quotation_form #customer option:last-child");
	$('#sales_quotation_form #customer').val(optionvalue);

	$('#customer_modal').modal('hide');
	$('#customer_modal').find("input[type=text], textarea, select").val("");
}
function closeModal(){
	$('#customer_modal').modal('hide');
}

$(document).ready(function(){
	$('.back').click(function(){
		parent.history.back();
		return false;
	});

	// computeDueDate();
});

</script>
<?php
	echo $ui->loadElement('modal')
		->setId('customer_modal')
		->setContent('maintenance/customer/create')
		->setHeader('Add a Customer')
		->draw();
?>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Add a Customer
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="newCustomer" autocomplete="off">
					<input class = "form_iput" value = "newCustomer" name = "h_form" id = "h_form" type="hidden">
					<div class="alert alert-warning alert-dismissable hidden" id="customerAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class = "well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Customer Code: <span class="asterisk"> * </span>')
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
										->setLabel('Address:')
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
										<button type="button" class="btn btn-info btn-flat" id="customerBtnSave">Save</button>
									</div>
										&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
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
<!-- End DELETE RECORD CONFIRMATION MODAL-->

<?php 
	if (isset($modal_script)) {
		echo $modal_script;
	}
?>
<script>
var ajax = {};

/**RETRIEVES CUSTOMER INFORMATION**/
function getPartnerInfo(code)
{
	var cmp = '<?= $cmp ?>';

	if(code == '' || code == 'add')
	{
		$("#customer_tin").val("");
		$("#customer_terms").val("");
		$("#customer_address").val("");

		computeDueDate();
	}
	else
	{
		$.post('<?=BASE_URL?>sales/sales_quotation/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
		{
			var address		= data.address.trim();
			var tinno		= data.tinno.trim();
			var terms		= data.terms.trim();
			
			$("#customer_tin").val(tinno);
			$("#customer_terms").val(terms);
			$("#customer_address").val(address);

			computeDueDate();
		});
	}
}

/**COMPUTES DUE DATE**/
function computeDueDate()
{
	var invoice = $("#transaction_date").val();
	var terms 	= $("#customer_terms").val(); 
	// alert(terms);
	
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

/**FOR ADD NEW CUSTOMER TRANSACTION**/
function addNewModal(type,val,row)
{
	row 		= row.replace(/[a-z]/g, '');
	
	if(val == 'add')
	{
		if(type == 'Customer')
		{
			$('#customerModal').modal();
			$('#Customer').val('');
		}
	}
}

/**RETRIEVAL OF ITEM DETAILS**/
function getItemDetails(id)
{
	var itemcode 	=	document.getElementById(id).value;
	var customer 	= 	document.getElementById('customer').value;
	var row 		=	id.replace(/[a-z]/g, '');
	
	$.post('<?=BASE_URL?>sales/sales_quotation/ajax/get_item_details',"itemcode="+itemcode+"&customer="+customer, function(data) 
	{
		if( data != false ){
		
		document.getElementById('detailparticulars'+row).value 		=	data.itemdesc;
		document.getElementById('issueuom'+row).value 					=	data.uomcode;
				
			if( data.c_price != null )
			{
				document.getElementById('itemprice'+row).value 			= 	addCommas(data.c_price);
			}
			else if(data.price != null )
			{	
				document.getElementById('itemprice'+row).value 			= 	addCommas(data.price);
			}
			else
			{
				document.getElementById('itemprice'+row).value 			= 	addCommas('0.00');
			}
				
			$('#sales_quotation_form').trigger('change');
		} else {
			document.getElementById('detailparticulars'+row).value 		=	"";
			document.getElementById('issueuom'+row).value 				= 	"";
			document.getElementById('itemprice'+row).value 				= 	"0.00";

			$('#sales_quotation_form').trigger('change');
		}
		computeAmount();
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

		row.cells[0].getElementsByTagName("select")[0].id 	= 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'issueuom['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'itemprice['+x+']';
		
		
		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'issueuom['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'itemprice['+x+']';

		
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

	document.getElementById('itemcode['+newid+']').value 			= '';
	document.getElementById('detailparticulars['+newid+']').value 	= '';
	document.getElementById('issueuom['+newid+']').value 			= '';
	document.getElementById('itemprice['+newid+']').value 			= '0.00';

	// $('#itemcode\\['+newid+'\\]').trigger('change');
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	var voucher		= document.getElementById('h_voucher_no').value;

	$.post("<?=BASE_URL?>sales/sales_quotation/ajax/cancel",'<?=$ajax_post?>')
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>sales/sales_quotation';
		}
	});
}


/** FINALIZE SAVING **/
function finalizeTransaction(type)
{
	$("#sales_quotation_form").find('.form-group').find('input, textarea, select').trigger('blur');

	var no_error = true;
	$('.quantity').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			$(this).closest('div').addClass('has-error');
		}
	});

	$('.price').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			$(this).closest('div').addClass('has-error'); 
		}
	});

	if($("#sales_quotation_form").find('.form-group.has-error').length == 0 && no_error)
	{	
		$('#save').val(type);
		computeAmount();

		// if($("#sales_quotation_form #itemcode\\[1\\]").val() != '' && $("#sales_quotation_form #detailparticular\\[1\\]").val() != '' && $("#sales_quotation_form #transaction_date").val() != '' && $("#sales_quotation_form #customer").val() != '')
		// {
		// 	$('#sales_quotation_form').submit();
		// 	// setTimeout(function() {
				
		// 	// },1000);
		// }
		
		var btn 	=	$('#save').val();
		if($("#sales_quotation_form #itemcode\\[1\\]").val() != '' && $("#sales_quotation_form #transaction_date").val() != '' && $("#sales_quotation_form #due_date").val() != '' && $("#sales_quotation_form #customer").val() != '')
		{
			setTimeout(function() {

				$.post("<?=BASE_URL?>sales/sales_quotation/ajax/<?=$task?>",$("#sales_quotation_form").serialize()+'<?=$ajax_post?>',function(data)
				{	
					if( data.msg == 'success' )
					{
						if( btn == 'final' )
						{
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = "<?=BASE_URL?>sales/sales_quotation";
							}, 1000)								
						}
						else if( btn == 'final_preview' )
						{
							// window.location 	=	"<?=BASE_URL?>sales/sales_quotation/view/"+data.voucher;
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = "<?=BASE_URL?>sales/sales_quotation/view/"+data.voucher;
							}, 1000)
						}
						else if( btn == 'final_new' )
						{
							// window.location 	=	"<?=BASE_URL?>sales/sales_quotation/create";
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = "<?=BASE_URL?>sales/sales_quotation/create";
							}, 1000)
						}
						
					}
					else
					{
						//insert error message / MOdal heree
						
					}
				});
			},1000);
		}
		
	}else{
		$('#warning_modal').modal('show').find('#warning_message').html('Please make sure all required fields are filled out.');		
		//$('#warning_modal').modal('show').find('#warning_message').html('Please Input Quantity > 0');
		next = $('#sales_order_form').find(".has-error").first();
		$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var btn 	=	$('#save').val();
				
	$("#sales_quotation_form").find('.form-group').find('input, textarea, select').trigger('blur');

	var no_error = true;
	$('.price').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			 $(this).closest('div').addClass('has-error');
		}
	});

	if($('#sales_quotation_form').find('.form-group.has-error').length == 0)
	{
		if($("#sales_quotation_form #itemcode\\[1\\]").val() != '' && $("#sales_quotation_form #transaction_date").val() != '' && $("#sales_quotation_form #due_date").val() != '' && $("#sales_quotation_form #customer").val() != '')
		{
			setTimeout(function() {

				$.post("<?=BASE_URL?>sales/sales_quotation/ajax/<?=$task?>",$("#sales_quotation_form").serialize()+'<?=$ajax_post?>',function(data)
				{		
					if( data.msg == 'success' )
					{
						if( btn == 'final' )
						{
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = "<?=BASE_URL?>sales/sales_quotation";
							}, 1000)
						}
						else if( btn == 'final_preview' )
						{
							window.location 	=	"<?=BASE_URL?>sales/sales_quotation/view/"+data.voucher;
						}
						else if( btn == 'final_new' )
						{
							window.location 	=	"<?=BASE_URL?>sales/sales_quotation/create";
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
		$("#sales_quotation_form").find('.form-group.has-error').first().find('input, textarea, select').focus();
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
	var customer 	= document.getElementById('customer').value;
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.tBodies[0].rows.length;;
	var valid		= 1;

	var rowindex	= table.rows[row];
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'salesquotation_details';

		if(rowCount > 1)
		{
			if(task == 'create')
			{
				ajax.table 		=	datatable;
				ajax.linenum 	= 	row;
				ajax.voucherno 	= 	voucher;

				$.post("<?=BASE_URL?>sales/sales_quotation/ajax/delete_row",ajax)
				.done(function( data ) 
				{
					if( data.msg == 'success' )
					{
						table.deleteRow(row);	
						resetIds();
						computeAmount();
					}
				});
			}
			else
			{
				table.deleteRow(row);	
				resetIds();
				computeAmount();
			}
		}
		else
		{	
			setZero();
			computeAmount();
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

	// -- For Customer -- 

		// Add new Customer
		$("#newCustomer #customerBtnSave").click(function()
		{
			var valid	= 0;

			valid		+= validateField('newCustomer','partnercode', "partnercode_help");
			valid		+= validateField('newCustomer','first_name', "first_name_help");
			valid		+= validateField('newCustomer','last_name', "last_name_help");
			valid		+= validateField('newCustomer','address1', "address_help");
			valid		+= validateField('newCustomer','businesstype', "businesstype_help");

			if(valid == 0)
			{
				$.post('<?=BASE_URL?>sales/sales_quotation/ajax/save_customer', $("#newCustomer").serialize(), function(data) 
				{
					var optionvalue = $("#newCustomer #partnercode").val();
					var optiondesc 	= $("#newCustomer #first_name").val() + " " + $("#newCustomer #last_name").val();
					console.log(optiondesc);
					if(data.msg == "success")
					{
						$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#sales_quotation_form #customer option:nth-child(4)");
						$('#sales_quotation_form #customer').val(optionvalue);
						
						getPartnerInfo(optionvalue);

						$('#customerModal').modal('hide');
						$('#customerModal').find("input[type=text], textarea, select").val("");
						return true;
					}
					else
					{
						$("#customerAlert p").html(data.msg);
						$("#customerAlert").removeClass('hidden');
					}
				});
			}
		});

		// Get getPartnerInfo
		$( "#customer" ).change(function() 
		{
			$customer_id = $("#customer").val();

			if( $customer_id != "" )
				getPartnerInfo($customer_id);
		});

		// Validation for Tinno, Terms
		$('#customer_tin, #customer_terms').on('keypress blur click', function(e) 
		{
			if(e.type == "keypress")
				return isNumberKey(e,45);
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
		});

		$('#customer_address').on('blur click', function(e) 
		{
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
		});

		$('#transaction_date').on('change', function(e) 
		{
			computeDueDate()
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

		// Validation for Customer Modal
		$('#partnercode, #first_name, #last_name, #address1, #businesstype').on('keyup', function(e) 
		{
			validateField('newCustomer',e.target.id, e.target.id + "_help");
		});

		$('#businesstype').on('change', function(e) 
		{
			validateField('newCustomer',e.target.id, e.target.id + "_help");
		});

	// -- For Customer -- End

	// -- For Items -- 
		//For Edit
		computeAmount();
		$('#customer_button').click(function()
		{
			$('#customer_modal').modal('show');
		});

		$('.itemcode').on('change', function(e) 
		{
			var customer 	=	$('#customer').val();
			
			if( customer != "" )
			{
				var id = $(this).attr("id");
				getItemDetails(id);
			}
			else
			{
			bootbox.dialog({
			message: "Please select customer first.",
			title: "Oops!",
			buttons: {
				yes: {
					label: "OK",
					className: "btn-primary btn-flat",
					callback: function(result) {

					}
				}
			}
		});
				$(this).val('');
				$('#customer').focus();
			}
		});


		$('.price').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			
			if($('#'+id) != 0){
				$(this).closest('.form-group').find('.pricey').removeClass('has-error');
			}

			formatNumber(id);
		});

		// For adding new roll
		$('body').on('click', '.add-data', function() 
		{	
			$('#itemsTable tbody tr.clone select').select2('destroy');
			
			var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

			var ParentRow = $("#itemsTable tbody tr.clone").last();
			
			var table 		= document.getElementById('itemsTable');
			var rows 		= table.tBodies[0].rows.length;
		
			// if(rowlimit == 0 || rows < rowlimit){
			// 	clone.clone(true).insertAfter(ParentRow);
			// 	setZero();
			// }else{
			// 	$('#row_limit').modal('show');
			// }

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
		if('<?= $task ?>' == "create" )
		{
			$("#sales_quotation_form").change(function()
			{
				computeAmount();
				if($("#sales_quotation_form #itemcode\\[1\\]").val() != '' && $("#sales_quotation_form #transaction_date").val() != '' && $("#sales_quotation_form #due_date").val() != '' && $("#sales_quotation_form #customer").val() != '')
				{
					$.post("<?=BASE_URL?>sales/sales_quotation/ajax/save_temp_data",$("#sales_quotation_form").serialize())
					.done(function(data)
					{	
						
					});
				}
			});

			//Final Saving
			$('#sales_quotation_form #btnSave').click(function(){

				finalizeTransaction("final");

			});

			//Save & Preview
			$("#sales_quotation_form #save_preview").click(function()
			{
				finalizeTransaction("final_preview");
			});

			//Save & New
			$("#sales_quotation_form #save_new").click(function()
			{
				finalizeTransaction("final_new");
			});
		}
		else if('<?= $task ?>' == "edit")
		{
			//Final Saving
			computeAmount();
			$('#sales_quotation_form .save').click(function(){
				
				$('#save').val("final");

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

$('.price').on('change', function(e){
			
	var id 		= 	$(this).attr("id");
	var row 	=	id.replace(/[a-z]/g, '');

	formatNumber(id);
	computeAmount();
});

$('#customer').on('change', function(){
	$('#sales_quotation_form .row').find('.form-group').removeClass('has-error');
	$('.m-none').addClass('hidden');
	computeAmount();
});

//**COMPUTE ROW AMOUNT**/
 function computeAmount(){
	var table 	= document.getElementById('itemsTable');
 	var count	= table.tBodies[0].rows.length;
 	total_amount = 0
 	for(row = 1; row <= count; row++) 
 	{  
 		var itemprice 	=	document.getElementById('itemprice['+row+']');
 		itemprice 		=	itemprice.value.replace(/,/g,'');
	
 		var amount 	 	=	parseFloat(itemprice);
 		total_amount 	 		+= amount;
 	}
	
 	document.getElementById('amount').value 				= addCommas(total_amount.toFixed(2));

 }



</script>