<section class="content">

	<div class="box box-primary">

		<form id = "CustomerDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			<input class = "form_iput" value = "<?=$close_date?>" name = "h_close_date" id = "h_close_date" type="hidden">
			<input class = "form_iput" value = "<?=$h_disctype?>" name = "h_disctype" id = "h_disctype" type="hidden">
		</form>

		<form method = "post" class="form-horizontal" id = "sales_order_form">

			<input class = "form_iput" value = "" name = "h_curr_limit" id = "h_curr_limit" type="text">
			<input class = "form_iput" value = "" name = "h_outstanding" id = "h_outstanding" type="text">
			<input class = "form_iput" value = "" name = "h_incurred" id = "h_incurred" type="text">
			<input class = "form_iput" value = "" name = "h_balance" id = "h_balance" type="text">
			
			<div class="box-body">
				<br>
				<div class="row">
					<div class="col-md-11">
						<div class = "row">
							<div class = "col-md-6">
								<?php
									echo $ui->formField('text')
											->setLabel('SO No:')
											->setSplit('col-md-4', 'col-md-8')
											->setName('voucher_no')
											->setId('voucher_no')
											->setAttribute(array("disabled" => "disabled"))
											->setPlaceholder("- auto generate -")
											->setValue($voucherno)
											->draw($show_input);
								?>
								<input type = "hidden" id = "h_voucher_no" name = "h_voucher_no" value = "<?= $generated_id ?>">
								<input class = "form_iput" value = "<?=$quotation_no?>" name = "h_quotation_no" id = "h_quotation_no" type="hidden">
							</div>

							<div class = "col-md-6">
								<?php
									echo $ui->formField('text')
										->setLabel('Transaction Date')
										->setSplit('col-md-4', 'col-md-8')
										->setName('transaction_date')
										->setId('transaction_date')
										->setClass('datepicker datepicker-input')
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
									if($show_input){
										echo $ui->formField('dropdown')
											->setLabel('Customer ')
											->setPlaceholder('None')
											->setSplit('col-md-4', 'col-md-8')
											->setName('customer')
											->setId('customer')
											->setList($customer_list)
											->setValue($customer)
											->setValidation('required')
											->setButtonAddon('plus')
											->draw($show_input);
									}else{
										echo $ui->formField('text')
											->setLabel('Customer')
											->setSplit('col-md-4', 'col-md-8')
											->setValue($customer)
											->draw($show_input);

										echo '<input type="hidden" id="customer" name="customer" value="'.$customer.'">';
									}
								?>
							</div>

							<div class = "col-md-6 hidden">
								<label class="control-label col-md-4" for="daterangefilter">Due Date:</label>
								<div class = "col-md-8" style = "padding-left: 8px; padding-right: 3px;">
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control pull-right datepicker" id="due_date" name = "due_date" type="text" value="<?=$due_date?>">
										<span class="help-block hidden small req-color" id = "duedate_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
									</div>
								</div>
							</div>
						</div>

						<div class = 'hidden'>
							<div class="row">
								<div class="col-md-6 remove-margin">
									<?php
										echo $ui->formField('text')
												->setLabel('<i>Tin</i>')
												->setSplit('col-md-4', 'col-md-8')
												->setName('customer_tin')
												->setId('customer_tin')
												->setAttribute(array("maxlength" => "15", "rows" => "1"))
												->setPlaceholder("000-000-000-000")
												->setClass("input_label")
												->setValue($tinno)
												->draw($show_input);
									?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 remove-margin">
									<?php
										echo $ui->formField('text')
												->setLabel('<i>Terms</i>')
												->setSplit('col-md-4', 'col-md-8')
												->setName('customer_terms')
												->setId('customer_terms')
												->setAttribute(array("readonly" => "", "maxlength" => "15"))
												->setPlaceholder("0")
												->setClass("input_label")
												->setValue($terms)
												->draw($show_input);
									?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 remove-margin">
									<?php
										echo $ui->formField('textarea')
												->setLabel('<i>Address</i>')
												->setSplit('col-md-4', 'col-md-8')
												->setName('customer_address')
												->setId('customer_address')
												->setClass("input_label")
												->setAttribute(array("readonly" => "", "rows" => "1"))
												->setValue($address1)
												->draw($show_input);
									?>
								</div>
							</div>
						</div>

						<div class = "row">
							<div class = "col-md-12">
								<?php
									echo $ui->formField('textarea')
											->setLabel('Notes:')
											->setSplit('col-md-2', 'col-md-10')
											->setName('remarks')
											->setId('remarks')
											->setValue($remarks)
											->draw($show_input);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="box-body table-responsive no-padding">
				<table class="table table-hover table-condensed table-sidepad" id="itemsTable">
					<thead>
						<tr class="info">
							<th class="col-md-2 text-center">Item</th>
							<th class="col-md-3 text-center">Description</th>
							<th class="col-md-2 text-center">Warehouse</th>
							<th class="col-md-1 text-center">Quantity</th>
							<th class="col-md-1 text-center">UOM</th>
							<th class="col-md-1 text-center">Price</th>
							<th class="col-md-2 text-center">Amount</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($task == 'create' && empty($quotation_no))
							{
								$accountcode 	   	= '';
								$detailparticulars 	= '';
								$warehouse 			= '';
								$price	   			= '0.00';
								$rowamount 			= '0.00';

								$quantity 		 	= 0;
								$uom 				= '';
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
													->draw($show_input);
										?>
									</td>
									<td class = "remove-margin">
										<?php
											echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('	', 'col-md-12')
												->setName("warehouse[".$row."]")
												->setId("warehouse[".$row."]")
												->setClass('warehouse')
												->setList($warehouses)
												->setValidation('required')
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
													->setClass('quantity text-right')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('integer')
													->setValue($quantity)
													->draw($show_input);
										?>
									</td>
									<td class = "remove-margin">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('uom['.$row.']')
													->setId('uom['.$row.']')
													->setClass("text-right")
													->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
													->setValue($uom)
													->draw($show_input);
										?>
									</td>
									<td class = "remove-margin">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('itemprice['.$row.']')
													->setId('itemprice['.$row.']')
													->setClass("text-right price")
													->setAttribute(array("maxlength" => "20"))
													->setValue($price)
													->setValidation('decimal')
													//->addHidden(true)
													->draw($show_input);
										?>
									</td>
									<!--<td class = "remove-margin">-->
										<?php
											// echo $ui->formField('dropdown')
											// 		->setSplit('', 'col-md-12')
											// 		->setName('taxcode['.$row.']')
											// 		->setId('taxcode['.$row.']')
											// 		->setClass("taxcode")
											// 		->setAttribute(
											// 			array(
											// 				"maxlength" => "20"
											// 			)
											// 		)
											// 		->setList($tax_codes)
											// 		->draw($show_input);
										?>
										<!--<input id = '<?php //echo 'taxrate['.$row.']'; ?>' name = '<?php //echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00' >
										<input id = '<?php //echo 'taxamount['.$row.']'; ?>' name = '<?php //echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00'>-->
									<!--</td>-->
									<td class = "remove-margin">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('amount['.$row.']')
													->setId('amount['.$row.']')
													->setClass("text-right")
													->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
													->setValidation('decimal')
													->setValue($rowamount)
													->draw($show_input);
										?>

										<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
									</td>			
								</tr>
						<?
							}
							else if( (!empty($sid) && $task!='create') || ( $task == 'create' && !empty($quotation_no) ) )
							{
								$row 			= 1;
								$disable_debit	= '';
								$disable_credit	= '';
								for($i = 0; $i < count($details); $i++)
								{
									$itemcode 	 		= $details[$i]->itemcode;
									$detailparticular	= $details[$i]->detailparticular;
									$quantity 			= isset($details[$i]->issueqty) ?	number_format($details[$i]->issueqty,0) 	: 	"1";
									$itemprice 			= $details[$i]->unitprice;
									$uom 				= $details[$i]->issueuom;
									// $taxcode 			= $details[$i]->taxcode;
									// $taxrate 			= $details[$i]->taxrate;
									$amount  			= $details[$i]->amount;
									// $uom  				= (empty($quotation_no)) ? $details[$i]->issueuom 	: 	$details[$i]->issueuom;
									$warehouse_code		= (empty($quotation_no)) ? $details[$i]->warehouse 	: 	'';
									$warehouse_name		= (empty($quotation_no)) ? $details[$i]->description: 	'';
									
									//itemcode, detailparticular, unitprice, issueqty, taxcode, taxrate, amount

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
													->setValue($detailparticular)
													->draw($show_input);
										?>
									</td>
									<td class = "remove-margin">
										<?php
											$value 	=	( $task == 'view' ) 	? 	$warehouse_name 	: 	$warehouse_code;

											echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('	', 'col-md-12')
												->setName("warehouse[".$row."]")
												->setId("warehouse[".$row."]")
												->setClass('warehouse')
												->setList($warehouses)
												->setValidation('required')
												->setValue($value)
												->draw($show_input);
										?>
									</td>
									<td class = "remove-margin text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('quantity['.$row.']')
													->setId('quantity['.$row.']')
													->setClass('quantity text-right')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('integer')
													->setValue($quantity)
													->draw($show_input);
										?>
									</td>
									<td class = "remove-margin">	
										<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('uom['.$row.']')
											->setId('uom['.$row.']')
											->setClass("text-right")
											->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
											->setValue($uom)
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
													->setValidation('decimal')
													->setValue(number_format($itemprice,'2','.',','))
													->draw($show_input);
										?>
									</td>
									<!--<td class = "remove-margin">-->
										<?php

											// echo $ui->formField('dropdown')
											// 		->setSplit('', 'col-md-12')
											// 		->setName('taxcode['.$row.']')
											// 		->setId('taxcode['.$row.']')
											// 		->setClass("taxcode")
											// 		->setAttribute(array("maxlength" => "20"))
											// 		->setList($tax_codes)
											// 		->setValue($taxcode)
											// 		->draw($show_input);
										?>
										<!--<input id = '<?php //echo 'taxrate['.$row.']'; ?>' name = '<?php //echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value="<?php echo $taxrate;?>" >
										<input id = '<?php //echo 'taxamount['.$row.']'; ?>' name = '<?php //echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >-->
									<!--</td>-->
									<td class = "remove-margin text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('amount['.$row.']')
													->setId('amount['.$row.']')
													->setClass("text-right")
													->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
													->setValidation('decimal')
													->setValue(number_format($amount,'2','.',','))
													->draw($show_input);
										?>
										
										<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
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

						<tr id="vatable_sales" class='hidden'>
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">VATable Sales</label>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('t_vatsales')
											->setId('t_vatsales')
											->setClass("input_label text-right remove-margin")
											->setValue(number_format($t_vatsales,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						
						<tr id="vat_exempt_sales" class='hidden'>
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">VAT-Exempt Sales</label>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('t_vatexempt')
											->setId('t_vatexempt')
											->setClass("input_label text-right remove-margin")
											->setValue(number_format($t_vatexempt,2))
											->draw($show_input);
								?>
							</td>
						</tr>

						<tr id="total_sales" class='hidden'>
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">Total Sales</label>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('t_subtotal')
											->setId('t_subtotal')
											->setClass("input_label text-right")
											->setAttribute(array("maxlength" => "40"))
											->setValue(number_format($t_subtotal,2))
											->draw($show_input);
								?>
							</td>
						</tr>

						<tr id="discount" class='hidden'>
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">Discount</label>
							</td>
							<td class="text-right">
								<div class = 'col-md-7'>
								<?php if($show_input) {?>
									<div class="btn-group btn-group-xs" data-toggle="buttons">
										<label class="btn btn-default" onChange="computeAmount();">
											<input type="radio" class='d_opt' name="discounttype" id="discounttype1" autocomplete="off" value="amt">amt
										</label>
										<label class="btn btn-default active" onChange="computeAmount();">
											<input type="radio" class='d_opt' name="discounttype" id="discounttype2" autocomplete="off" value="perc"  checked="checked">%
										</label>
									</div>
									<?php } ?>
								</div>
								<div class = 'col-md-5'>
									<?php
										echo $ui->formField('text')
												->setSplit('', '')
												->setName('t_discount')
												->setId('t_discount')
												->setClass("text-right")
												->setValue(number_format($t_discount,2) . " " . $percentage )
												->draw($show_input);
									?>
								</div>

							</td>
						</tr>

						<tr id="total_sales" class='hidden'>
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">Add 12% VAT</label>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('t_vat')
											->setId('t_vat')
											->setClass("input_label text-right")
											->setAttribute(array("maxlength" => "40"))
											->setValue(number_format($t_vat,2))
											->draw($show_input);
								?>
							</td>
						</tr>

						<tr id="total_amount_due">
							<td colspan = '5'></td>
							<td class="right">
								<label class="control-label col-md-12">Total Amount</label>
							</td>
							<td class="text-right" style="border-top:1px solid #DDDDDD;">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('t_total')
											->setId('t_total')
											->setClass("input_label text-right")
											->setAttribute(array("maxlength" => "40"))
											->setValue(number_format($t_total,'2','.',','))
											->draw($show_input);
								?>
							</td>
							<td></td>
						</tr>

					</tfoot>
				</table>
			</div>

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
									->addEdit(($task == 'view' && ( $stat == 'open' && $restrict_so )))
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
		</form>

	</div>

</section>

<div class="modal fade" id="creditLimitModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Oops!
			</div>
			<div class="modal-body" id="message">
				This customer is about to exceed their Credit Limit. Do you wish to Proceed?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 ">
						<div class="text-center">
							<button type="button" class="btn btn-info btn-flat" id="btnOk" data-dismiss='modal'>OK</button>
						</div>
							<!-- &nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" id="btnNo" >No</button>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="orderQtymodal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Oops!
			</div>
			<div class="modal-body" id="message">
				Ordered quantity cannot be greater than on hand quantity. Please check the item in Inventory Inquiry List.
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 ">
						<div class="text-center">
							<button type="button" class="btn btn-default btn-flat" id="btnOk" data-dismiss='modal'>Cancel</button>
						</div>
							<!-- &nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" id="btnNo" >No</button>
						</div> -->
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function addCustomerToDropdown() {
		var optionvalue = $("#customer_modal #customerForm #partnercode").val();
		var optiondesc 	= $("#customer_modal #customerForm #partnername").val();

		var credit_limit = $('#customer_modal #customerForm #credit_limit').val();
		$('#h_curr_limit').val(credit_limit);
		$('#h_balance').val(credit_limit);

		$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#sales_order_form #customer option:last-child");
		$('#sales_order_form #customer').val(optionvalue);
		
		getPartnerInfo(optionvalue);

		$('#customer_modal').modal('hide');
		$('#customer_modal').find("input[type=text], textarea, select").val("");
	}

	function retrieveCurrentOutstandingReceivables(customercode){
		$.post('<?php echo BASE_URL?>sales/sales_order/ajax/retrieve_outstanding_receivables', "customercode=" + customercode, function(data) {
			$('#h_outstanding').val(data.outstanding_receivables);
			computeforremainingcredit();
		});
	}

	function retrieveCurrentIncurredReceivables(customercode){
		$.post('<?php echo BASE_URL?>sales/sales_order/ajax/retrieve_incurred_receivables', "customercode=" + customercode, function(data) {
			$('#h_incurred').val(data.incurred_receivables);
			// computeforremainingcredit();
		});
	}

	function retrieveCreditLimit(customercode){
		$.post('<?php echo BASE_URL?>sales/sales_order/ajax/retrieve_credit_limit', "customercode=" + customercode, function(data) {
			$('#h_curr_limit').val(data.credit_limit);
		});
	}

	function computeforremainingcredit(){
		var credit_limit 			=	$('#h_curr_limit').val();
		var outstanding_receivables = 	$('#h_outstanding').val();
		// var incurred_receivables 	=	$('#h_incurred').val();

		var balance 				=	parseFloat(credit_limit) 	-	parseFloat(outstanding_receivables);

		$('#h_balance').val(balance);
	}

	function checkIfExceededCreditLimit(){
		var current_total 		=	$('#t_total').val();
		var current_balance 	=	$('#h_balance').val();
		var current_outstanding	=	$('#h_outstanding').val();
		var current_limit		=	$('#h_curr_limit').val();

		var flag 	=	0; 
		if(removeComma(current_total) > removeComma(current_balance)){
			$('#creditLimitModal #message').html("Total sales order of "+addComma(current_total)+" exceeds your credit limit of "+addComma(current_limit)+". <br><br>Current Credit Balance: "+addComma(current_balance));	
			flag 	=	1;
		}

		return flag;
	}

	
	function closeModal(module){
		var id = module + '_modal';
		$('#'+id).modal('hide');
	}
</script>
<?php
	echo $ui->loadElement('modal')
		->setId('customer_modal')
		->setContent('maintenance/customer/create')
		->setHeader('Add a Customer')
		->draw();

		// echo $ui->loadElement('modal')
		// ->setId('item_modal')
		// ->setContent('maintenance/item/create')
		// ->setHeader('Item')
		// ->draw();
?>
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

<div id="row_limit" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Item Limit</h4>
			</div>
			<div class="modal-body">
				<p>Sorry, but the printout for this record is only limited to <strong><?=$item_limit?></strong> items.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
var ajax = {};

var close_date 	=	$('#h_close_date').val();

/**RETRIEVES CUSTOMER INFORMATION**/
function getPartnerInfo(code) {
	var cmp = '<?= $cmp ?>';

	if(code == '' || code == 'add') {
		$("#customer_tin").val("");
		$("#customer_terms").val("");
		$("#customer_address").val("");

		computeDueDate();
	} else {
		$.post('<?=BASE_URL?>sales/sales_order/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
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
	
	if(invoice != '')
	{
		var newDate	= moment(invoice).add(terms, 'days').format("MMM DD, YYYY");
		
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
			$('#customer_modal').modal();
			$('#Customer').val('');
		}
	}
}

/**RETRIEVAL OF ITEM DETAILS**/
function getItemDetails(id)
{
	//console.log('id = '+id);
	if( id != undefined )
	{
		var itemcode 	=	document.getElementById(id).value;
		var customer 	= 	document.getElementById('customer').value;
		var row 		=	id.replace(/[a-z]/g, '');

		$.post('<?=BASE_URL?>sales/sales_order/ajax/get_item_details',"itemcode="+itemcode+"&customer="+customer, function(data) 
		{

			if( data != false )
			{
				document.getElementById('detailparticulars'+row).value 		=	data.itemdesc;
				document.getElementById('uom'+row).value 					=	data.uomcode;
				
				if( data.c_price != null && data.stat == 'active' )
				{
				
					document.getElementById('itemprice'+row).value 			= 	addComma(data.c_price);
				}
				else
				{	
					if( data.price != null )
					{
						document.getElementById('itemprice'+row).value 			= 	addComma(data.price);
					}
					else
					{
						document.getElementById('itemprice'+row).value 			= 	"0.00";
					}
				}
					
				computeAmount();

				$('#sales_order_form').trigger('change');
			}
			else
			{
				document.getElementById('detailparticulars'+row).value 		=	"";
				document.getElementById('uom'+row).value 					=	"";	
				document.getElementById('itemprice'+row).value 				= 	"0.00";

				$('#sales_order_form').trigger('change');
			}
			
		});

	}
}

/**COMPUTE ROW AMOUNT**/
function computeAmount()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.tBodies[0].rows.length;

	for(row = 1; row <= count; row++) 
	{  
		//var vat 		=	document.getElementById('taxrate['+row+']');
		var itemprice 	=	document.getElementById('itemprice['+row+']');
		var quantity 	=	document.getElementById('quantity['+row+']');

		//vat 			=	vat.value.replace(/,/g,'');
		// vat 			= 	(vat == "" || vat == undefined) 	?	0 	:	vat;
		
		itemprice 		=	itemprice.value.replace(/,/g,'');
		quantity 		=	quantity.value.replace(/,/g,'');
		
		//var totalprice 	=	parseFloat(itemprice) 	* 	parseFloat(quantity);
		//var amount 		=	parseFloat(totalprice) / ( 1 + parseFloat(vat) );
		//var vat_amount 	=	parseFloat(amount)	*	parseFloat(vat);

		var amount 	 	=	parseFloat(itemprice) 	* 	parseFloat(quantity);

		// amount			= 	(amount>0) 		?	Math.round(amount*1000) / 1000 	:	0;
		// vat_amount		= 	(vat_amount>0) 	?	Math.round(vat_amount*100) / 100:	0;

		amount			=	Math.round(amount*1000) / 1000; 	
		//vat_amount		= 	Math.round(vat_amount*100) / 100;
		
		document.getElementById('amount['+row+']').value 	=	addCommas(amount.toFixed(2));
		document.getElementById('h_amount['+row+']').value 	=	addCommas(amount.toFixed(5));
		//document.getElementById('taxamount['+row+']').value = 	addCommas(vat_amount.toFixed(5));

		addAmounts(); 
	}
}

/**COMPUTE TOTAL AMOUNTS**/
function addAmounts() {
	var total_h_vatable		= 0;
	var total_h_vatex		= 0;
	var total_h_vat			= 0;
	var total_discount		= 0;
	var total_gross_disc	= 0;
	var total_amount 		= 0;
	var subtotal 			= 0;

	var table				= document.getElementById('itemsTable');
	var count				= table.tBodies[0].rows.length;


	for (var i = 1; i <= count; i++) {
		var row = '[' + i + ']';
		var x_unitprice		= document.getElementById('itemprice' + row);
		var x_quantity		= document.getElementById('quantity' + row);
		var x_taxrate		= document.getElementById('taxrate' + row);
		var x_amount		= document.getElementById('amount' + row);
		var x_taxamount		= document.getElementById('taxamount' + row);
		var h_amount		= document.getElementById('h_amount' + row);

		x_unitprice 		= 	(x_unitprice == "" || x_unitprice == undefined) ?	0 	:	x_unitprice;
		x_taxrate 			= 	(x_taxrate == "" || x_taxrate == undefined) ?	0 	:	x_taxrate;
		
		var unitprice		= x_unitprice.value.replace(/[,]+/g, '');
		var taxrate			= parseFloat(x_taxrate.value);
		var quantity 		= x_quantity.value.replace(/[,]+/g,'');

		var amount			= ( quantity * unitprice );
		
		x_amount.value		= addCommas(amount.toFixed(2));
		h_amount.value		= amount.toFixed(2);

		total_amount 	 	+= amount;
	}

	document.getElementById('t_total').value 				= addCommas(total_amount.toFixed(2));

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
		row.cells[2].getElementsByTagName("select")[0].id 	= 'warehouse['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'quantity['+x+']';
		row.cells[4].getElementsByTagName("input")[0].id 	= 'uom['+x+']';
		row.cells[5].getElementsByTagName("input")[0].id 	= 'itemprice['+x+']';
		// row.cells[5].getElementsByTagName("select")[0].id 	= 'taxcode['+x+']';
		// row.cells[5].getElementsByTagName("input")[0].id 	= 'taxrate['+x+']';
		// row.cells[5].getElementsByTagName("input")[1].id 	= 'taxamount['+x+']';
		// row.cells[6].getElementsByTagName("input")[0].id 	= 'amount['+x+']';
		// row.cells[6].getElementsByTagName("input")[1].id 	= 'h_amount['+x+']';
		row.cells[6].getElementsByTagName("input")[0].id 	= 'amount['+x+']';
		row.cells[6].getElementsByTagName("input")[1].id 	= 'h_amount['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("select")[0].name = 'warehouse['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'quantity['+x+']';
		row.cells[4].getElementsByTagName("input")[0].name 	= 'uom['+x+']';
		row.cells[5].getElementsByTagName("input")[0].name 	= 'itemprice['+x+']';
		// row.cells[5].getElementsByTagName("select")[0].name = 'taxcode['+x+']';
		// row.cells[5].getElementsByTagName("input")[0].name 	= 'taxrate['+x+']';
		// row.cells[5].getElementsByTagName("input")[1].name 	= 'taxamount['+x+']';
		// row.cells[6].getElementsByTagName("input")[0].name 	= 'amount['+x+']';
		// row.cells[6].getElementsByTagName("input")[1].name 	= 'h_amount['+x+']';
		row.cells[6].getElementsByTagName("input")[0].name 	= 'amount['+x+']';
		row.cells[6].getElementsByTagName("input")[1].name 	= 'h_amount['+x+']';

		row.cells[7].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[7].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

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
	document.getElementById('warehouse['+newid+']').value 			= '';
	document.getElementById('quantity['+newid+']').value 			= '0';
	document.getElementById('uom['+newid+']').value 				= '';
	document.getElementById('itemprice['+newid+']').value 			= '0.00';
	// document.getElementById('taxcode['+newid+']').value 			= 'NA';
	// document.getElementById('taxrate['+newid+']').value 			= '0.00';
	// document.getElementById('taxamount['+newid+']').value 			= '0.00';
	document.getElementById('amount['+newid+']').value 				= '0.00';
	document.getElementById('h_amount['+newid+']').value 			= '0.00';

	// $('#itemcode\\['+newid+'\\]').trigger('change');
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	var voucher		= document.getElementById('h_voucher_no').value;

	$.post("<?=BASE_URL?>sales/sales_order/ajax/cancel",'<?=$ajax_post?>')
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>sales/sales_order';
		}
	});
}

/** FINALIZE SAVING **/
function finalizeTransaction(type)
{
	$("#sales_order_form").find('.form-group').find('input, textarea, select').trigger('blur');

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

	var credit_limit_exceed =	checkIfExceededCreditLimit();
	if(credit_limit_exceed == 1){
		// $('#creditLimitModal').modal('show');
		no_error = false;
	}

	if($("#sales_order_form").find('.form-group.has-error').length == 0 && no_error)
	{	
		$('#save').val(type);
		computeAmount();
		if($("#sales_order_form #itemcode\\[1\\]").val() != '' && $("#sales_order_form #warehouse\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #customer").val() != '')
		{
			$('#delay_modal').modal('show');
			setTimeout(function() {									
				$('#sales_order_form').submit();
			}, 1000)
		}
	}
	else{
		if(credit_limit_exceed != 1){
			$('#warning_modal').modal('show').find('#warning_message').html('Please make sure all required fields are filled out.');		
			next = $('#sales_order_form').find(".has-error").first();
			$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
		} else {
			$('#creditLimitModal').modal('show');
		}
		 
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var btn 	=	$('#save').val();
				
	$("#sales_order_form").find('.form-group').find('input, textarea, select').trigger('blur');

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

	if($('#sales_order_form').find('.form-group.has-error').length == 0 && no_error)
	{
		if($("#sales_order_form #itemcode\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #due_date").val() != '' && $("#sales_order_form #customer").val() != '')
		{
			setTimeout(function() {

				$.post("<?=BASE_URL?>sales/sales_order/ajax/<?=$task?>",$("#sales_order_form").serialize()+'<?=$ajax_post?>',function(data)
				{		
					if( data.msg == 'success' )
					{
						if( btn == 'final' )
						{
							$('#delay_modal').modal('show');
							setTimeout(function() {									
								window.location 	=	"<?=BASE_URL?>sales/sales_order";
							}, 1000)
						}
						else if( btn == 'final_preview' )
						{
							window.location 	=	"<?=BASE_URL?>sales/sales_order/view/"+data.voucher;															
						}
						else if( btn == 'final_new' )
						{
							window.location 	=	"<?=BASE_URL?>sales/sales_order/create";
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
		$('#warning_modal').modal('show').find('#warning_message').html('Please make sure all required fields are filled out.');		
		// $("#sales_order_form").find('.form-group.has-error').first().find('input, textarea, select').focus();
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
		var datatable	= 'salesorder_details';

		if(rowCount > 1)
		{
			if(task == 'create')
			{
				ajax.table 		=	datatable;
				ajax.linenum 	= 	row;
				ajax.voucherno 	= 	voucher;

				$.post("<?=BASE_URL?>sales/sales_order/ajax/delete_row",ajax)
				.done(function( data ) 
				{
					if( data.msg == 'success' )
					{
						table.deleteRow(row);	
						resetIds();
						addAmounts();
					}
				});
			}
			else
			{
				table.deleteRow(row);	
				resetIds();
				addAmounts();
			}
		}
		else
		{	
			setZero();
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
		else{	
			setZero();
			addAmounts();
		}
	}
}

$(document).ready(function(){

	// -- For Customer --
		// Get getPartnerInfo
		$( "#customer" ).change(function() 
		{
			customer_id = $("#customer").val();

			if( customer_id != "" )
			{
				retrieveCreditLimit(customer_id);
				retrieveCurrentIncurredReceivables(customer_id);
				retrieveCurrentOutstandingReceivables(customer_id);

				getPartnerInfo(customer_id);
				if( $('#itemcode\\[1\\]').val() != "" ){
					$('.itemcode').trigger('change');
				}
			}
		});

		// Open Modal
		$('#customer_button').click(function()
		{
			$('#customer_modal').modal('show');
		});

		$('#customer_terms').on('blur', function(e) 
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

	// -- For Customer -- End

	// -- For Items -- 
		//For Edit
		computeAmount();

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
				$(this).val('');
				$('#customer').focus();
			}
		});

		$('.taxcode').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			var code 	= 	$(this).val();

			$.post('<?=BASE_URL?>sales/sales_invoice/ajax/get_value', "taxcode=" + code + "&event=getTaxRate", function(data) 
			{
				document.getElementById('taxrate' + row).value = data.taxrate;

				computeAmount();
			});
		});

		$('.quantity').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');

			if( $(this).val() > 0)
			{
				$(this).closest('div').removeClass('has-error');
			}
			else{
				$(this).closest('div').addClass('has-error');
			}

			//formatNumber(id);
			computeAmount();
		});

		$('.price').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');

			if( $(this).val() > 0)
			{
				$(this).closest('div').removeClass('has-error');
			}
			else{
				$(this).closest('div').addClass('has-error');
			}

			formatNumber(id);
			computeAmount();
		});

		// For adding new roll
		$('body').on('click', '.add-data', function() 
		{	
			$('#itemsTable tbody tr.clone select').select2('destroy');
			
			var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

			var ParentRow = $("#itemsTable tbody tr.clone").last();
			
			var table 		= document.getElementById('itemsTable');
			var rows 		= table.tBodies[0].rows.length;
			var rowlimit 	= '<?echo $item_limit?>';
		
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

		$('#h_disctype').on('change',function(){
			computeAmount();
		});

		$('#t_discount').on('change',function(){
			var disc_id =	$('input[type=radio][name=discounttype]:checked').attr('id');
			if( disc_id != "" || disc_id != undefined )
			{
				var type 	=	$('#'+disc_id).val();
				var value 	=	$(this).val();

				$('#h_disctype').val(type);	

				if( value <= 100 )
				{
					computeAmount();
				}
				else
				{
					//Add Modal here
					alert( " You cannot enter a value greater than 100 ! " );
				}
			}
		});

	// -- For Discount -- End

	// -- For Saving -- 

		// Process New Transaction
		if('<?= $task ?>' == "create")
		{
			$("#sales_order_form").change(function()
			{
				if($("#sales_order_form #itemcode\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #due_date").val() != '' && $("#sales_order_form #customer").val() != '')
				{
					$.post("<?=BASE_URL?>sales/sales_order/ajax/save_temp_data",$("#sales_order_form").serialize())
					.done(function(data)
					{});
				}
			});

			//Final Saving
			$('#sales_order_form #btnSave').click(function(){
			
				finalizeTransaction("final");
				
			});

			//Save & Preview
			$("#sales_order_form #save_preview").click(function()
			{
				finalizeTransaction("final_preview");
			});

			//Save & New
			$("#sales_order_form #save_new").click(function()
			{
				finalizeTransaction("final_new");
			});
		}
		else if('<?= $task ?>' == "edit")
		{
			//Final Saving
			$('#sales_order_form .save').click(function(){
				$('#itemsTable tbody tr td').find('.warehouse').find('option[disabled]').prop('disabled', false)
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
			else
			{
				window.location =	"<?= BASE_URL ?>sales/sales_order/";
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

	// -- Back Button 
		$('#btnBack').on('click',function(){
			window.history.back();
		});
	// -- Back Button -- End

	$('#creditLimitModal').on('click','#btnOk',function(){
		$('#creditLimitModal').modal('hide');
	});
	
	// $('#creditLimitModal').on('click','#btnNo',function(){
	// 	window.location.href = '<?=BASE_URL?>sales/sales_order/create';
	// });
	
});


$('.quantity').on('change',function() {
	var element = $(this);
	var items = [];
	var itemcode = $(this).closest('tr').find('.itemcode').val();
	$('.quantity').each(function() {
		if( $(this).val() > 0 )
		{
			var qty = removeComma($(this).val());
			if (typeof items[itemcode] == 'undefined') {
				items[itemcode] = 0;
			}
			items[itemcode] += qty;
		}
		$.post('<?php echo BASE_URL?>sales/sales_order/ajax/retrieve_item_quantity', "itemcode="+itemcode , function(data) {
			var x = data.qty.replace(/\.00$/,'');
			if (items[itemcode] > x){
				$('#orderQtymodal').modal('show');
				element.val('0');
			}
		});
	});
	
})


</script>