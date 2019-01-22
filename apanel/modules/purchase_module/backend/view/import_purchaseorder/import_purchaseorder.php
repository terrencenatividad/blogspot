<section class="content">

	<div class="box box-primary">

		<form id = "VendorDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
		</form>

		<form method = "post" class="form-horizontal" id = "purchase_order_form">
			
			<div class="box-body">
				<br>
				<div class="row">
					<div class="col-md-11">
						<?php if($task == 'view') : ?>
							<?php if($stat == 'cancelled') { ?>
								<div class="row">
									<div class="col-lg-2"></div>
									<div class="col-lg-4">
										<?php echo '<font size = "4em"><span class="label label-danger">CANCELLED</span></font>'; ?>
									</div>
									<div class="col-lg-3"></div>
								</div>
								<br>
							<?php } ?>
						<?php endif; ?>
						<div class = "row">
							<div class = "col-md-6">
								<?php
								echo $ui->formField('text')
								->setLabel('PO No:')
								->setSplit('col-md-4', 'col-md-8')
								->setName('voucher_no')
								->setId('voucher_no')
								->setAttribute(array("disabled" => "disabled"))
								->setPlaceholder("- auto generate -")
								->setValue($voucherno)
								->draw($show_input);
								?>
								<input type = "hidden" id = "h_voucher_no" name = "h_voucher_no" value = "<?= $generated_id ?>">
								<input type = "hidden" id = "h_request_no" name = "h_request_no" value = "<?= $request_no ?>">
							</div>

							<div class = "col-md-6">
								<?php
								echo $ui->formField('text')
								->setLabel('Transaction Date')
								->setSplit('col-md-4', 'col-md-8')
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
							<div class = "col-md-6 vendor_div">
								<?php
								if($show_input){
									echo $ui->formField('dropdown')
									->setLabel('Supplier ')
									->setPlaceholder('None')
									->setSplit('col-md-4', 'col-md-8')
									->setName('vendor')
									->setId('vendor')
									->setList($vendor_list)
									->setValue($vendor)
									->setValidation('required')
									->setButtonAddon('plus')
									->draw($show_input);
								}else{
									echo $ui->formField('text')
									->setLabel('Supplier ')
									->setSplit('col-md-4', 'col-md-8')
									->setValue($vendor)
									->draw($show_input);

									echo '<input type="hidden" id="vendor" name="vendor" value="'.$vendor.'">';
								}
								?>
							</div>

							<div class = "col-md-6 referenceno_div">
								<?php
								echo $ui->formField('text')
								->setLabel('Reference Number:')
								->setSplit('col-md-4', 'col-md-8')
								->setName('referenceno')
								->setId('referenceno')
								->setMaxLength(20)
								->setValue($referenceno)
								->draw($show_input);
								?>
							</div>
						</div>

						<div class = "row">
							<div class = "col-md-6 currencydiv">
								<?php
								echo $ui->formField('dropdown')
								->setLabel('Currency:')
								->setSplit('col-md-4', 'col-md-8')
								->setName('currency')
								->setPlaceholder('Select Currency')
								->setList($currency_codes)
								->setId('currency')
								->setValue($currency)
								->draw($show_input);
								?>
							</div>
							<div class = "col-md-6 department_div">
								<?php
								echo $ui->formField('dropdown')
								->setLabel('Department:')
								->setSplit('col-md-4', 'col-md-8')
								->setName('department')
								->setPlaceholder('Select Department')
								->setList($department_list)
								->setId('department')
								->setValue($department)
								->draw($show_input);
								?>
							</div>
						</div>

						<div class = "row">
							<div class = "col-md-6 exchange_ratediv">
								<?php
								echo $ui->formField('text')
								->setLabel('Exchange Rate:')
								->setSplit('col-md-4', 'col-md-8')
								->setName('exchange_rate')
								->setId('exchange_rate')
								->setClass('exchange_rate')
								->setValue($exchange_rate)
								->setValidation('decimal')
								->draw($show_input);
								?>
							</div>
							<div class = "col-md-6">
								<?php
								echo $ui->formField('dropdown')
								->setLabel('Discount Type ')
								->setPlaceholder('Select Discount Type')
								->setSplit('col-md-4', 'col-md-8')
								->setName('discounttype')
								->setId('discounttype')
								->setList($discounttypes)
								->setValue($discounttype)
										// ->setValidation('required')
								->setNone('none')
								->draw($show_input);
								?>
								<input type="hidden" value="" id="newdisctype" name="newdisctype">
							</div>
						</div>

						<div class="row hidden">
							<div class="col-md-6 remove-margin">
								<?php
								echo $ui->formField('text')
								->setLabel('<i>Tin</i>')
								->setSplit('col-md-4', 'col-md-8')
								->setName('vendor_tin')
								->setId('vendor_tin')
								->setAttribute(array("maxlength" => "15", "rows" => "1"))
								->setPlaceholder("000-000-000-000")
								->setClass("input_label")
								->setValue($tinno)
								->draw($show_input);
								?>
							</div>
						</div>

						<div class="row hidden">
							<div class="col-md-6 remove-margin">
								<?php
								echo $ui->formField('text')
								->setLabel('<i>Terms</i>')
								->setSplit('col-md-4', 'col-md-8')
								->setName('vendor_terms')
								->setId('vendor_terms')
								->setAttribute(array("readonly" => "", "maxlength" => "15"))
								->setPlaceholder("0")
								->setClass("input_label")
								->setValue($terms)
								->draw($show_input);
								?>
							</div>
						</div>

						<div class="row hidden">
							<div class="col-md-6 remove-margin">
								<?php
								echo $ui->formField('textarea')
								->setLabel('<i>Address</i>')
								->setSplit('col-md-4', 'col-md-8')
								->setName('vendor_address')
								->setId('vendor_address')
								->setClass("input_label")
								->setAttribute(array("readonly" => "", "rows" => "1"))
								->setValue($address1)
								->draw($show_input);
								?>
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
								->setMaxLength(300)
								->draw($show_input);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if (isset($cancelled_items) && $cancelled_items): ?>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#ordered" aria-controls="ordered" role="tab" data-toggle="tab">Ordered</a></li>
					<li role="presentation"><a href="#received" aria-controls="received" role="tab" data-toggle="tab">Received</a></li>
					<li role="presentation"><a href="#cancelled" aria-controls="cancelled" role="tab" data-toggle="tab">Cancelled</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="ordered">
					<?php endif ?>

					<div class="box-body table-responsive no-padding">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable">
							<thead>
								<tr class="info">
									<th class="col-md-1 text-center">Budget Code</th>
									<th class="col-md-1 text-center">Item Name</th>
									<th class="col-md-2 text-center">Description</th>
									<th class="col-md-1 text-center">Warehouse</th>
									<th class="col-md-1 text-center">On Hand Qty</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-1 text-center">Qty</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-1 text-center">Discount</th>
									<th class="col-md-1 text-center">Foreign Currency Amount</th>
									<th class="col-md-2 text-center">Base Currency Amount</th>
									<th class="taxt-center"></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if($task == 'create' && empty($request_no))
								{
									$accountcode 	   	  = '';
									$detailparticulars 	  = '';
									$remarks 			  = '';
									$warehouse 			  = '';
									$price	   			  = '0.00';
									$rowamount 			  = '0.00';

									$onhandqty 		 	  = 0;
									$quantity 		 	  = 0;
									$uom 				  = '';
									$discount 		 	  = 0;
									$row 			   	  = 1;
									$total_debit 	   	  = 0;
									$total_credit 	   	  = 0;
									$vatable_purchase 	  = 0;
									$vat_exempt_purchase  = 0;
									$t_subtotal 		  = 0;
									$b_subtotal 		  = 0;
									$t_discount  		  = 0;
									$t_total 			  = 0;
									$t_vat 				  = 0;
									$t_wtax 			  = 0;
									$t_wtaxcode 		  = 'NA1';
									$t_wtaxrate 		  = 0;
									$s_atc_code 		  = 0;
									$discount_check_amt   = 0;
									$discount_check_perc  = 0;
									$freight			  = 0;
									$insurance 			  = 0;
									$packaging 			  = 0;
									$converted_freight 	  = 0;
									$converted_insurance  = 0;
									$converted_packaging  = 0;
									$convertedamount  	  = 0;

									$startnumber 	   	= ($row_ctr == 0) ? 1: $row_ctr;

									?>
									<tr class="clone" valign="middle">
										<td class = "remove-margin">
											<?php
											echo $ui->formField('dropdown')
											->setPlaceholder('Select One')
											->setSplit('	', 'col-md-12')
											->setName("budgetcode[".$row."]")
											->setId("budgetcode[".$row."]")
											->setClass('budgetcode')
											->setList($budget_list)
											->setValue("")
											->draw($show_input);
											?>
										</td>
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
											->setName('onhandqty['.$row.']')
											->setId('onhandqty['.$row.']')
											->setClass('onhandqty text-right')
											->setAttribute(array("maxlength" => "20","readonly"=>"readonly"))
											->setValidation('required integer')
											->setValue($onhandqty)
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
											->setValidation('required decimal')
											->setAttribute(array("maxlength" => "20"))
											->setValue(number_format($price,2))
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
											->setValidation('required integer')
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
										<td class = "remove-margin hidden">
											<?php
											echo $ui->formField('dropdown')
											->setSplit('', 'col-md-12')
											->setName('taxcode['.$row.']')
											->setId('taxcode['.$row.']')
											->setClass("taxcode")
											->setAttribute(
												array(
													"maxlength" => "20"
												)
											)
											->setList($tax_codes)
											->setNone('none')
											->draw($show_input);
											?>
											<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00' >
											<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00'>
										</td>
										<td class = "remove-margin">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('discount['.$row.']')
											->setId('discount['.$row.']')
											->setClass("text-right discount")
											->setValidation('required decimal')
											->setAttribute(array("maxlength" => "20"))
											->setValue(number_format($discount,2))
											->draw($show_input);
											?>
										</td>
										<td class = "remove-margin">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('foreignamount['.$row.']')
											->setId('foreignamount['.$row.']')
											->setClass("text-right")
											->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
											->setValidation('decimal')
											->setValue(number_format($rowamount,2))
											->draw($show_input);
											?>

											<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
										</td>
										<td class = "remove-margin">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('baseamount['.$row.']')
											->setId('baseamount['.$row.']')
											->setClass("text-right")
											->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
											->setValidation('decimal')
											->setValue(number_format($rowamount,2))
											->draw($show_input);
											?>

											<input id = '<?php echo 'b_amount['.$row.']'; ?>' name = '<?php echo 'b_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
										</td>			
									</tr>
									<?php
								}
								else if( (!empty($sid) && $task!='create' || ( $task == 'create' && !empty($request_no) ) ))
								{
									$row 				= 1;
									$disable_debit		= '';
									$disable_credit		= '';
									$b_subtotal			= 0;

									for($i = 0; $i < count($details); $i++)
									{
										$budgetcode 	 	= $details[$i]->budgetcode;
										$itemcode 	 		= $details[$i]->itemcode;
										$detailparticular	= stripslashes($details[$i]->detailparticular);
										$onhandqty  		= $details[$i]->onhandqty;
										$quantity 			= number_format($details[$i]->receiptqty,0);
										$itemprice 			= $details[$i]->unitprice;
										$taxcode 			= $details[$i]->taxcode;
										$taxrate 			= $details[$i]->taxrate;
										$amount  			= $details[$i]->amount;
										$convertedamount  	= $details[$i]->convertedamount;
										$uom  				= $details[$i]->receiptuom;
										$discount  			= $details[$i]->discount;
										$warehouse_code		= (empty($request_no)) ? $details[$i]->warehouse 	: 	'';
										$warehouse_name		= (empty($request_no)) ? $details[$i]->description: 	'';
										$b_subtotal 	   += $convertedamount; 

										?>	
										<tr class="clone" valign="middle">
											<td class = "remove-margin">
												<?php
												echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('	', 'col-md-12')
												->setName("budgetcode[".$row."]")
												->setId("budgetcode[".$row."]")
												->setClass('budgetcode')
												->setList($budget_list)
												->setValue($budgetcode)
												->draw($show_input);
												?>
											</td>
											<td class = "remove-margin">
												<?php
												echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('	', 'col-md-12')
												->setName("itemcode[".$row."]")
												->setId("itemcode[".$row."]")
												->setList($itemcodes)
												->setClass('itemcode')
												->setValidation('required')
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
												->setName('onhandqty['.$row.']')
												->setId('onhandqty['.$row.']')
												->setClass('onhandqty text-right')
												->setAttribute(array("maxlength" => "20","readonly"=>"readonly"))
												->setValidation('required integer')
												->setValue($onhandqty)
												->draw($show_input);
												?>
											</td>
											<td class = "remove-margin text-right">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('itemprice['.$row.']')
												->setId('itemprice['.$row.']')
												->setClass("price")
												->setAttribute(array("maxlength" => "20"))
												->setValidation('required decimal')
												->setValue(number_format($itemprice, 2))
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
												->setValidation('required integer')
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
											<td class = "remove-margin hidden">
												<?php

												echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12')
												->setName('taxcode['.$row.']')
												->setId('taxcode['.$row.']')
												->setClass("taxcode")
												->setAttribute(array("maxlength" => "20"))
												->setList($tax_codes)
												->setValue($taxcode)
												->setNone('none')
												->draw($show_input);
												?>
												<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value="<?php echo $taxrate;?>" >
												<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
											</td>
											<td class = "remove-margin">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('discount['.$row.']')
												->setId('discount['.$row.']')
												->setClass("text-right discount")
												->setValidation('required decimal')
												->setAttribute(array("maxlength" => "20"))
												->setValue(number_format($discount,2))
												->draw($show_input);
												?>
											</td>
											<td class = "remove-margin text-right">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('foreignamount['.$row.']')
												->setId('foreignamount['.$row.']')
												->setClass("text-right")
												->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
												->setValidation('decimal')
												->setValue(number_format($amount,2))
												->draw($show_input);
												?>

												<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
											</td>
											<td class = "remove-margin text-right">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('baseamount['.$row.']')
												->setId('baseamount['.$row.']')
												->setClass("text-right")
												->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
												->setValidation('decimal')
												->setValue(number_format($convertedamount,2))
												->draw($show_input);
												?>

												<input id = '<?php echo 'b_amount['.$row.']'; ?>' name = '<?php echo 'b_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
											</td>
											<?php if($task!='view'){ ?>
												<td class="text-center">
													<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
												</td>
											<?php }	?>		
										</tr>
										<?php	
										$row++;	
									}
								}
								?>
							</tbody>
							<tfoot class="summary">
								<tr>
									<td>
										<?php if($task != 'view') { ?>
											<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
										<?php } ?>
									</td>	
								</tr>

								<tr id="total_purchase">
									<td colspan = '8'></td>
									<td class="text-right">
										<label class="control-label">Total Purchase</label>
									</td>
									<td class="text-right" colspan = "1" style="border-top:1px solid #DDDDDD;">
										<?php
										echo $ui->formField('text')
										->setName('t_subtotal')
										->setId('t_subtotal')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly',"style" => "margin-left:-16px;margin-right: 15px;"))
										->setValue(number_format($t_subtotal,2))
										->draw($show_input);
										?>
									</td>
									<td class="text-right" colspan = "1" style="border-top:1px solid #DDDDDD;">
										<?php
										echo $ui->formField('text')
										->setName('b_subtotal')
										->setId('b_subtotal')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly',"style" => "margin-left:4px;margin-right: 14px;"))
										->setValue(number_format($b_subtotal,2))
										->draw($show_input);
										?>
									</td>
								</tr>

								<tr id="total_purchase" class="hidden">
									<td colspan = '5'></td>
									<td class="text-right">
										<label class="control-label">Total Purchases Tax</label>
									</td>
									<td class="text-right" colspan = "2">
										<?php
										echo $ui->formField('text')
										->setName('t_vat')
										->setId('t_vat')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly'))
										->setValue(number_format($t_vat,2))
										->draw($show_input);
										?>
									</td>
								</tr>
								<tr id="total_freight">
									<td colspan = '8'></td>
									<td class="text-right">
										<label class="control-label">Freight</label>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-lg-12')
										->setName('freight')
										->setId('freight')
										->setClass("freight text-right")
										->setPlaceholder('0.00')
										->setAttribute(array("maxlength" => "20"))
										->setValue(number_format($freight,2))
										->setValidation('decimal')
										->draw($show_input);
										?>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setName('b_freight')
										->setId('b_freight')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly',"style" => "margin-left:4px;margin-right: 14px;"))
										->setValue(number_format($converted_freight,2))
										->draw($show_input);
										?>
									</td>
								</tr>

								<tr id="total_insurance">
									<td colspan = '8'></td>
									<td class="text-right">
										<label class="control-label">Insurance</label>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-lg-12')
										->setName('insurance')
										->setId('insurance')
										->setClass("insurance text-right")
										->setPlaceholder('0.00')
										->setAttribute(array("maxlength" => "20"))
										->setValue(number_format($insurance,2))
										->setValidation('decimal')
										->draw($show_input);
										?>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setName('b_insurance')
										->setId('b_insurance')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly',"style" => "margin-left:4px;margin-right: 14px;"))
										->setValue(number_format($converted_insurance,2))
										->draw($show_input);
										?>
									</td>
								</tr>

								<tr id="total_packaging">
									<td colspan = '8'></td>
									<td class="text-right">
										<label class="control-label">Packaging</label>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-lg-12')
										->setName('packaging')
										->setId('packaging')
										->setClass("packaging text-right")
										->setPlaceholder('0.00')
										->setAttribute(array("maxlength" => "20"))
										->setValue(number_format($packaging,2))
										->setValidation('decimal')
										->draw($show_input);
										?>
									</td>
									<td class="text-right" colspan = "1">
										<?php
										echo $ui->formField('text')
										->setName('b_packaging')
										->setId('b_packaging')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40", 'readonly',"style" => "margin-left:4px;margin-right: 14px;"))
										->setValue(number_format($converted_packaging,2))
										->draw($show_input);
										?>
									</td>
								</tr>

								<tr id="total_purchase" class="hidden">
									<td colspan = '5'></td>
									<td class="text-right">
										<label class="control-label">Withholding Tax</label>
									</td>
									<td class="text-right" colspan = "2">
										<div class="row">
											<div class = 'col-md-8'>
												<?php
												echo $ui->formField('dropdown')
												->setSplit('', 'col-lg-12')
												->setName('t_wtaxcode')
												->setId('t_wtaxcode')
												->setClass("taxcode")
												->setAttribute(array("maxlength" => "20"))
												->setNone('none')
												->setList($wtax_codes)
												->setValue($t_wtaxcode)
												->draw($show_input);
												?>
												<div class = "hidden">
													<?php
													echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('wtaxrate')
													->setId('wtaxrate')
													->setClass("taxcode")
													->setAttribute(array("maxlength" => "20"))
													->setList($wtax_codes)
													->setValue($t_wtaxrate)
													->draw($show_input);
													?>
													<?php
													echo $ui->formField('text')
													->setName('wtaxamount')
													->setId('wtaxamount')
													->setClass("taxcode")
													->setAttribute(array("maxlength" => "20"))
													->setList($wtax_codes)
													->draw($show_input);
													?>
													<?php
													echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('s_atc_code')
													->setId('s_atc_code')
													->setClass("taxcode")
													->setAttribute(array("maxlength" => "20"))
													->setList($wtax_codes)
													->setValue($s_atc_code)
													->draw($show_input);
													?>	
												</div>
											</div>
											<div class = 'col-md-4 col-lg-4'>
												<?php
												echo $ui->formField('text')
												->setName('t_wtax')
												->setId('t_wtax')
												->setAttribute(array("readonly"))
												->setClass("input_label text-right")
												->setValue(number_format($t_wtax,2))
												->draw($show_input);
												?>
											</div>
										</div>
									</td>
								</tr>

								<tr id="total_amount_due">
									<td colspan = '8'></td>
									<td class="text-right">
										<label class="control-label">Total Amount Due</label>
									</td>
									<td class="text-right" colspan = "1" style="border-top:1px solid #DDDDDD;">
										<?php
										echo $ui->formField('text')
										->setName('t_total')
										->setId('t_total')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40",'readonly',"style" => "margin-left:-16px;;margin-right: 15px;"))
										->setValue(number_format($t_total,2))
										->draw($show_input);
										?>
									</td>
									<td class="text-right" colspan = "1" style="border-top:1px solid #DDDDDD;">
										<?php
										echo $ui->formField('text')
										->setName('b_total')
										->setId('b_total')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40",'readonly',"style" => "margin-left:4px;;margin-right: 14px;"))
										->setValue(number_format($b_subtotal+$converted_freight+$converted_insurance+$converted_packaging,2))
										->draw($show_input);
										?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<?php if (isset($cancelled_items) && $cancelled_items): ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="received">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable2">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Item Name</th>
									<th class="col-md-3 text-center">Description</th>
									<th class="col-md-2 text-center">Warehouse</th>
									<th class="col-md-1 text-center">Qty</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-2 text-center">Tax</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-2 text-center">Amount</th>
									<th class="taxt-center"></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$received_total_vatable = 0;
								$received_total_tax = 0;
								?>
								<?php foreach ($received_items as $row): ?>
									<tr>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('', 'col-md-12')
											->setList($itemcodes)
											->setClass('itemcode')
											->setValue($row->itemcode)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setValue($row->detailparticular)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('	', 'col-md-12')
											->setList($warehouses)
											->setValue($row->warehouse)
											->draw(false);
											?>
										</td>
										<td class="text-right">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass('quantity text-right')
											->setValue(number_format($row->received_qty, 0))
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setValue($uom)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('', 'col-md-12')
											->setClass("taxcode")
											->setList($tax_codes)
											->setValue($taxcode)
											->draw(false);
											?>
										</td>
										<td class="text-center">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass("price")
											->setValue(number_format($row->unitprice, 2))
											->draw(false);
											?>
										</td>
										<td class="text-center">
											<?php
											$received_total = round(($row->unitprice * $row->received_qty) / (1 + $row->taxrate), 2);
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass("amount")
											->setValue(number_format($received_total, 2))
											->draw(false);

											$received_total_vatable += $received_total;
											$received_total_tax += ($row->unitprice * $row->received_qty) - $received_total;
											$received_wtaxrate = $row->wtaxrate;
											?>
										</td>
										<td></td>
									</tr>
								<?php endforeach ?>
							</tbody>
							<tfoot class="summary">
								<tr>
									<td colspan="4"></td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><label>Total Purchase</label></p>
									</td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><?php echo number_format($received_total_vatable, 2) ?></p>
									</td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><label>Total Purchases Tax</label></p>
									</td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><?php echo number_format($received_total_tax, 2) ?></p>
									</td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><label>Withholding Tax</label></p>
									</td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><?php echo number_format($received_withholding = $received_total_vatable * $received_wtaxrate, 2) ?></p>
									</td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><label>Total Amount Due</label></p>
									</td>
									<td colspan="2" class="text-right">
										<p class="form-control-static"><label><?php 
										$totallings = $received_total_vatable + $received_total_tax - $received_withholding;
										echo number_format($totallings, 2) ?></label></p>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane" id="cancelled">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable3">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Item Name</th>
									<th class="col-md-3 text-center">Description</th>
									<th class="col-md-2 text-center">Warehouse</th>
									<th class="col-md-1 text-center">Quantity</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-2 text-center">Tax</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-2 text-center">Amount</th>
									<th class="taxt-center"></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($cancelled_items as $row): ?>
									<tr>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('', 'col-md-12')
											->setList($itemcodes)
											->setClass('itemcode')
											->setValue($row->itemcode)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setValue($row->detailparticular)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('	', 'col-md-12')
											->setList($warehouses)
											->setValue($row->warehouse)
											->draw(false);
											?>
										</td>
										<td class="text-right">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass('quantity text-right')
											->setValue(number_format($row->balance_qty, 0))
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setValue($uom)
											->draw(false);
											?>
										</td>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setSplit('', 'col-md-12')
											->setClass("taxcode")
											->setList($tax_codes)
											->setValue($taxcode)
											->draw(false);
											?>
										</td>
										<td class="text-center">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass("price")
											->setValue(number_format($row->unitprice, 2))
											->draw(false);
											?>
										</td>
										<td class="text-center">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setClass("amount")
											->setValue(number_format($row->unitprice * $row->balance_qty, 2))
											->draw(false);
											?>
										</td>
										<td></td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif ?>
			<br>
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
					->addEdit(($task == 'view' && ( $stat == 'open' && $restrict_po )))
					->setValue($voucherno)
					->draw_button($show_input);

					?>
					&nbsp;&nbsp;&nbsp;
					<?  
					if( $task != "view" )
					{
						?>
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-id="<?php echo $generated_id?>" data-toggle="back-page" id="btnCancel">Cancel</button>
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

		</form>

	</div>

</section>

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

<div class="modal fade" id="discounttypeModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
			</div>
			<div class="modal-body" id="message">
				Changing the Current Discount Type will clear out the Discounts. Do you wish to proceed?
			</div>
			<div class="modal-footer text-center">
				<button type="button" class="btn btn-info btn-flat" id="disc_yes" data-dismiss='modal'>Yes</button>
				<button type="button" class="btn btn-default btn-flat" id="disc_no" >No</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="warning-modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Warning
			</div>
			<div class="modal-body">
				<div class = "row">
					<div class="col-md-12">
						<div id = "errors">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 col-sm-12 col-xs-12 text-right">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" data-dismiss="modal">Confirm</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="accountchecker-modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Warning
			</div>
			<div class="modal-body">
				<div class = "row">
					<div class="col-md-12">
						<div id = "accounterror">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 col-sm-12 col-xs-12 text-right">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" data-dismiss="modal">Okay</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function addVendorToDropdown() {

		var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
		var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

		$('<option value="'+optionvalue+'">'+optionvalue+" - "+optiondesc+'</option>').insertAfter("#purchase_order_form #vendor option:last-child");
		$('#purchase_order_form #vendor').val(optionvalue);

		getPartnerInfo(optionvalue);

		$('#vendor_modal').modal('hide');
		$('#vendor_modal').find("input[type=text], textarea, select").val("");
	}
	function closeModal(){
		$('#vendor_modal').modal('hide');
	}
</script>
<?php
echo $ui->loadElement('modal')
->setId('vendor_modal')
->setContent('maintenance/supplier/create')
->setHeader('Add New Supplier')
->draw();
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
<!-- End Delete Record Confirmation Modal -->

<!--DELETE RECORD CONFIRMATION MODAL-->
<!-- <div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
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
</div> -->
<!-- End DELETE RECORD CONFIRMATION MODAL-->


<!--ATC CODE MODAL-->
<div class="modal fade" id="atcModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Alphanumeric Tax Code
			</div>
			<div class="modal-body">
				<div class="row row-dense">
					<label for="prefix" class="control-label col-md-3 col-sm-3 col-xs-3">ATC</label>
					<div class="col-md-9 col-sm-9 col-xs-9" id="atcField">
						<?php
						echo $ui->formField('dropdown')
						->setSplit('', 'col-md-12')
						->setName('atccode')
						->setId('atccode')
						->setValue('')
						->setValidation('required') 
						->draw($show_input);
						?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-info" id="btnProceed">Proceed</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button class="btn btn-small btn-default" role="button" data-dismiss="modal" style="outline:none;" onClick="clear_wtax();">
								Skip
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if ($show_input): ?>
	<script>
		var ajax = {};

		/**RETRIEVES VENDOR INFORMATION**/
		function getPartnerInfo(code)
		{
			var cmp = '<?= $cmp ?>';

			if(code == '' || code == 'add')
			{
				$("#vendor_tin").val("");
				$("#vendor_terms").val("");
				$("#vendor_address").val("");

				computeDueDate();
			}
			else
			{
				$.post('<?=BASE_URL?>purchase/import_purchaseorder/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
				{
					var address		= data.address.trim();
					var tinno		= data.tinno.trim();
					var terms		= data.terms.trim();

					$("#vendor_tin").val(tinno);
					$("#vendor_terms").val(terms);
					$("#vendor_address").val(address);

					computeDueDate();
				});
			}
		}

		/**COMPUTES DUE DATE**/
		function computeDueDate()
		{
			var invoice = $("#transaction_date").val();
			var terms 	= $("#vendor_terms").val(); 

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

		/**FOR ADD NEW VENDOR TRANSACTION**/
		function addNewModal(type,val,row)
		{
			row 		= row.replace(/[a-z]/g, '');

			if(val == 'add')
			{
				if(type == 'Vendor')
				{
					$('#vendor_modal').modal();
					$('#Vendor').val('');
				}
			}
		}

		/**RETRIEVAL OF ITEM DETAILS**/
		function getItemDetails(id)
		{
			var itemcode 	=	document.getElementById(id).value;
			var row 		=	id.replace(/[a-z]/g, '');

			$.post('<?=BASE_URL?>purchase/import_purchaseorder/ajax/get_item_details',"itemcode="+itemcode , function(data) 
			{
				if( data != false )
				{

					document.getElementById('detailparticulars'+row).value 	=	data.itemdesc;
					document.getElementById('uom'+row).value 	 			=	data.uomcode;
					document.getElementById('itemprice'+row).value 			= 	"0.00";

					computeAmount();

					$('#purchase_order_form').trigger('change');
				}
				else
				{
					document.getElementById('detailparticulars'+row).value 		=	"";
					document.getElementById('uom'+row).value 					=	"";	
					document.getElementById('itemprice'+row).value 				= 	"0.00";
					document.getElementById('onhandqty'+row).value 				= 	"0.00";

					$('#purchase_order_form').trigger('change');
				}
			});

		}

		/**COMPUTE ROW AMOUNT**/
		function computeAmount()
		{
			var table 	= document.getElementById('itemsTable');
			var count	= table.tBodies[0].rows.length;

			for(row = 1; row <= count; row++) 
			{  
				var vat 		=	document.getElementById('taxrate['+row+']');
				var itemprice 	=	document.getElementById('itemprice['+row+']');
				var quantity 	=	document.getElementById('quantity['+row+']');
				var discount 	=	document.getElementById('discount['+row+']');
				var exchangerate 	=	$('#exchange_rate').val();
				var discounttype 	=	$('#discounttype').val();

				vat 			=	vat.value.replace(/,/g,'');
				itemprice 		=	itemprice.value.replace(/,/g,'');
				quantity 		=	quantity.value.replace(/,/g,'');
				exchangerate	=	exchangerate.replace(/,/g,'');
				discount		=	discount.value.replace(/,/g,'');

				if(discounttype == 'perc'){
					discountamt = ((parseFloat(itemprice) 	* 	parseFloat(quantity)) * (parseFloat(discount)/100));
				}else{
					discountamt = discount;
				}

				var totalprice 	=	(parseFloat(itemprice) 	* 	parseFloat(quantity)) - parseFloat(discountamt);
				var totalbase 	=	parseFloat(totalprice) 	* 	parseFloat(exchangerate);
				//var amount 		=	parseFloat(totalprice) / ( 1 + parseFloat(vat) );

				var vat_amount 	=	parseFloat(totalprice)	*	parseFloat(vat);
				var amount 		=	parseFloat(totalprice) + parseFloat(vat_amount);
				
				amount			= 	Math.round(amount*1000) / 1000;
				vat_amount		= 	Math.round(vat_amount*100) / 100;

				document.getElementById('foreignamount['+row+']').value 	=	addCommas(totalprice.toFixed(2));
				document.getElementById('h_amount['+row+']').value 	=	addCommas(totalprice.toFixed(5));

				document.getElementById('baseamount['+row+']').value 	=	addCommas(totalbase.toFixed(2));
				document.getElementById('b_amount['+row+']').value 	=	addCommas(totalbase.toFixed(5));

				document.getElementById('taxamount['+row+']').value = 	addCommas(vat_amount.toFixed(5));

				computeWTAX();
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

			var total_base 			= 0;
			var charges 			= 0;

			var table				= document.getElementById('itemsTable');
			var count				= table.tBodies[0].rows.length;
			var wtax 	 			= document.getElementById('t_wtax').value;

			for (var i = 1; i <= count; i++) {
				var row = '[' + i + ']';
				var x_unitprice		= document.getElementById('itemprice' + row);
				var x_quantity		= document.getElementById('quantity' + row);
				var x_taxrate		= document.getElementById('taxrate' + row);
				var x_amount		= document.getElementById('foreignamount' + row);
				var x_taxamount		= document.getElementById('taxamount' + row);
				var h_amount		= document.getElementById('h_amount' + row);
				var b_amount		= document.getElementById('b_amount' + row);
				var discount		= document.getElementById('discount' + row);
				var exchangerate	= $('#exchange_rate').val();
				var freight			= $('#freight').val();
				var insurance		= $('#insurance').val();
				var packaging		= $('#packaging').val();
				var discounttype 	=	$('#discounttype').val();

				var unitprice		= x_unitprice.value.replace(/[,]+/g, '');
				var taxrate			= parseFloat(x_taxrate.value);
				var quantity 		= x_quantity.value.replace(/[,]+/g,'');
				var discount 		= discount.value.replace(/[,]+/g,'');
				freight 			= freight.replace(/[,]+/g,'');
				insurance 			= insurance.replace(/[,]+/g,'');
				packaging 			= packaging.replace(/[,]+/g,'');
				exchangerate 		= exchangerate.replace(/[,]+/g,'');
				var tax_amount		= ( quantity * unitprice ) * taxrate;
				//var amount			= ( quantity * unitprice ) / (taxrate + 1);
				
				if(discounttype == 'perc'){
					discountamt = ((parseFloat(unitprice) 	* 	parseFloat(quantity)) * (parseFloat(discount)/100));
				}else{
					discountamt = discount;
				}
				
				var amount			= ( quantity * unitprice ) + tax_amount - discountamt;

				var baseamount		= amount * exchangerate;
				
				var net_of_vat		= 0;
				var vat_ex			= 0;
				var vat				= 0;
				var temp_amount 	= 0;


				x_amount.value		= addCommas(amount.toFixed(2));
				h_amount.value		= amount.toFixed(2);
				x_taxamount.value	= tax_amount.toFixed(2);

				if( taxrate > 0.00 || taxrate > 0 )	
				{
					net_of_vat 		= amount;
				}

				// vat_ex				= amount - net_of_vat;
				// vat					= net_of_vat * taxrate;
				vat_ex				= amount - net_of_vat - tax_amount;
				vat					= tax_amount;

				total_h_vatable		+= net_of_vat;
				total_h_vatex		+= vat_ex;
				total_h_vat			+= vat;

				total_base			+= baseamount;
			}
			charges 				= parseFloat(freight) + parseFloat(insurance) + parseFloat(packaging); 
			subtotal 				= total_h_vatable + total_h_vatex;
			b_subtotal 				= total_base;
			b_total					= (freight*exchangerate) + (insurance*exchangerate) + (packaging*exchangerate) + b_subtotal;
		// }

		document.getElementById('t_subtotal').value 			= addCommas(subtotal.toFixed(2));
		document.getElementById('t_vat').value					= total_h_vat.toFixed(2);
		document.getElementById('t_total').value 				= addCommas((total_h_vatable + charges + total_h_vatex - wtax + total_h_vat).toFixed(2));
		document.getElementById('b_subtotal').value 			= addCommas(b_subtotal.toFixed(2));
		document.getElementById('b_freight').value 				= addCommas((freight*exchangerate).toFixed(2));
		document.getElementById('b_insurance').value 			= addCommas((insurance*exchangerate).toFixed(2));
		document.getElementById('b_packaging').value 			= addCommas((packaging*exchangerate).toFixed(2));
		document.getElementById('b_total').value 				= addCommas(b_total.toFixed(2));
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
			row.cells[0].getElementsByTagName("select")[0].id 	= 'budgetcode['+x+']';
			row.cells[1].getElementsByTagName("select")[0].id 	= 'itemcode['+x+']';
			row.cells[2].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
			row.cells[3].getElementsByTagName("select")[0].id 	= 'warehouse['+x+']';
			row.cells[4].getElementsByTagName("input")[0].id 	= 'onhandqty['+x+']';
			row.cells[5].getElementsByTagName("input")[0].id 	= 'itemprice['+x+']';
			row.cells[6].getElementsByTagName("input")[0].id 	= 'quantity['+x+']';
			row.cells[7].getElementsByTagName("input")[0].id 	= 'uom['+x+']';
			row.cells[8].getElementsByTagName("select")[0].id 	= 'taxcode['+x+']';
			row.cells[8].getElementsByTagName("input")[1].id 	= 'taxamount['+x+']';
			row.cells[8].getElementsByTagName("input")[0].id 	= 'taxrate['+x+']';
			row.cells[9].getElementsByTagName("input")[0].id 	= 'discount['+x+']';
			row.cells[10].getElementsByTagName("input")[0].id 	= 'foreignamount['+x+']';
			row.cells[10].getElementsByTagName("input")[1].id 	= 'h_amount['+x+']';
			row.cells[11].getElementsByTagName("input")[0].id 	= 'baseamount['+x+']';
			row.cells[11].getElementsByTagName("input")[1].id 	= 'b_amount['+x+']';
			
			row.cells[0].getElementsByTagName("select")[0].name 	= 'budgetcode['+x+']';
			row.cells[1].getElementsByTagName("select")[0].name 	= 'itemcode['+x+']';
			row.cells[2].getElementsByTagName("input")[0].name	= 'detailparticulars['+x+']';
			row.cells[3].getElementsByTagName("select")[0].name 	= 'warehouse['+x+']';
			row.cells[4].getElementsByTagName("input")[0].name	= 'onhandqty['+x+']';
			row.cells[5].getElementsByTagName("input")[0].name	= 'itemprice['+x+']';
			row.cells[6].getElementsByTagName("input")[0].name	= 'quantity['+x+']';
			row.cells[7].getElementsByTagName("input")[0].name	= 'uom['+x+']';
			row.cells[8].getElementsByTagName("select")[0].name 	= 'taxcode['+x+']';
			row.cells[8].getElementsByTagName("input")[1].name	= 'taxamount['+x+']';
			row.cells[8].getElementsByTagName("input")[0].name	= 'taxrate['+x+']';
			row.cells[9].getElementsByTagName("input")[0].name	= 'discount['+x+']';
			row.cells[10].getElementsByTagName("input")[0].name	= 'foreignamount['+x+']';
			row.cells[10].getElementsByTagName("input")[1].name	= 'h_amount['+x+']';
			row.cells[11].getElementsByTagName("input")[0].name 	= 'baseamount['+x+']';
			row.cells[11].getElementsByTagName("input")[1].name 	= 'b_amount['+x+']';
			
			row.cells[12].getElementsByTagName("button")[0].setAttribute('id',x);
			row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
			row.cells[12].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

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
		document.getElementById('warehouse['+newid+']').value 			= '';
		document.getElementById('onhandqty['+newid+']').value 			= '0';
		document.getElementById('quantity['+newid+']').value 			= '0';
		document.getElementById('uom['+newid+']').value 				= '';
		document.getElementById('taxcode['+newid+']').value 			= 'none';
		document.getElementById('taxamount['+newid+']').value 			= '0.00';
		document.getElementById('taxrate['+newid+']').value 			= '0.00';
		document.getElementById('itemprice['+newid+']').value 			= '0.00';
		document.getElementById('discount['+newid+']').value 			= '0.00';
		document.getElementById('foreignamount['+newid+']').value 		= '0.00';
		document.getElementById('h_amount['+newid+']').value 			= '0.00';
		document.getElementById('baseamount['+newid+']').value 			= '0.00';
		document.getElementById('b_amount['+newid+']').value 			= '0.00';

	// $('#itemcode\\['+newid+'\\]').trigger('change');
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	var voucher		= document.getElementById('h_voucher_no').value;

	$.post("<?=BASE_URL?>purchase/import_purchaseorder/ajax/cancel",'<?=$ajax_post?>')
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>purchase/import_purchaseorder';
		}
	});
}

/** FINALIZE SAVING **/
function finalizeTransaction(type, error, warning, checkamount, date_checker)
{
	$("#purchase_order_form").find('.form-group').find('input, textarea, select').trigger('blur');

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

	if($("#purchase_order_form").find('.form-group.has-error').length == 0 && no_error)
	{	
		$('#save').val(type);
		computeAmount();

		if($("#purchase_order_form #itemcode\\[1\\]").val() != '' && $("#purchase_order_form #warehouse\\[1\\]").val() != '' && $("#purchase_order_form #transaction_date").val() != '' && $("#purchase_order_form #due_date").val() != '' && $("#purchase_order_form #vendor").val() != '')
		{

			if(checkamount != '') {
				$('#warning-modal').modal('show');
				$('#errors').html(checkamount);
				$('#warning-modal').on('hidden.bs.modal', function() {
					$('#delay_modal').modal('show');
					setTimeout(function() {
						$('#purchase_order_form').submit();
					},1000);
				});
			} else if(error != '') {
				$('#accountchecker-modal').modal('show');
				$('#accounterror').html(error);
			} else if(warning != ''){
				$('#accountchecker-modal').modal('show');
				$('#accounterror').html(warning);
			} else if(date_checker != ''){
				$('#accountchecker-modal').modal('show');
				$('#accounterror').html(date_checker);
			} else {
				$('#delay_modal').modal('show');
				setTimeout(function() {
					$('#purchase_order_form').submit();
				},1000);
			}
		}
	}
	else{
		$('#warning_modal').modal('show').find('#warning_message').html('Please make sure all required fields are filled out.');		
		//$('#warning_modal').modal('show').find('#warning_message').html('Please Input Quantity > 0');
		//$("#purchase_order_form").find('.form-group.has-error').first().find('input, textarea, select').focus();
		next = $('#purchase_order_form').find(".has-error").first();
		$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var btn 	=	$('#save').val();
	
	$("#purchase_order_form").find('.form-group').find('input, textarea, select').trigger('blur');

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
	
	if($('#purchase_order_form').find('.form-group.has-error').length == 0  && no_error)
	{
		if($("#purchase_order_form #itemcode\\[1\\]").val() != '' && $("#purchase_order_form #transaction_date").val() != '' && $("#purchase_order_form #due_date").val() != '' && $("#purchase_order_form #customer").val() != '')
		{
			setTimeout(function() {

				$.post("<?=BASE_URL?>purchase/import_purchaseorder/ajax/<?=$task?>",$("#purchase_order_form").serialize()+'<?=$ajax_post?>',function(data)
				{		
					var parse = JSON.stringify(data.msg);
					var parsed = JSON.parse(parse);
					error = parsed['error'];
					warning = parsed['warning'];
					checkamount = parsed['checkamount'];
					errmsg = parsed['errmsg'];
					date_checker = parsed['date_checker'];

					if(checkamount != '') {
						$('#warning-modal').modal('show');
						$('#errors').html(checkamount);
						$('#warning-modal').on('hidden.bs.modal', function() {
							if(errmsg == '') {
								if( btn == 'final' ) {
									$('#delay_modal').modal('show');
									setTimeout(function() {
										window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder";								
									},1000);
								}
								else if( btn == 'final_preview' ) {
									window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder/view/"+data.voucher;
								}
								else if( btn == 'final_new' ) {
									window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder/create";
								}
							}
						});
					} else if(error != '') {
						$('#accountchecker-modal').modal('show');
						$('#accounterror').html(error);
					} else if(warning != ''){
						$('#accountchecker-modal').modal('show');
						$('#accounterror').html(warning);
					} else if(date_checker != ''){
						$('#accountchecker-modal').modal('show');
						$('#accounterror').html(date_checker);
					} else {
						if(errmsg == '') {
							if( btn == 'final' ) {
								$('#delay_modal').modal('show');
								setTimeout(function() {
									window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder";								
								},1000);
							}
							else if( btn == 'final_preview' ) {
								window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder/view/"+data.voucher;
							}
							else if( btn == 'final_new' ) {
								window.location 	=	"<?=BASE_URL?>purchase/import_purchaseorder/create";
							}

						}
					}
				});	
			},1000);
		}
	}
	else 
	{
		$('#warning_modal').modal('show').find('#warning_message').html('Please make sure all required fields are filled out.');
		// $("#purchase_order_form").find('.form-group.has-error').first().find('input, textarea, select').focus();
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
	var vendor 		= document.getElementById('vendor').value;
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.tBodies[0].rows.length;;
	var valid		= 1;

	var rowindex	= table.rows[row];

	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'import_purchaseorder_details';

		if(rowCount > 1)
		{
			if(task == 'create')
			{
				ajax.table 		=	datatable;
				ajax.linenum 	= 	row;
				ajax.voucherno 	= 	voucher;

				$.post("<?=BASE_URL?>purchase/import_purchaseorder/ajax/delete_row",ajax)
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

/** SKIP ATC CHOICE **/
function clear_wtax()
{
	document.getElementById('t_wtax').value = '0.00'; 
	document.getElementById('t_wtaxcode').value = "NA1";
}

function computeWTAX()
{
	var subtotal 	=	document.getElementById('t_subtotal').value;
	subtotal 		=	subtotal.replace(/,/g,'');

	var wtaxrate 	= 	document.getElementById('wtaxrate').value;

	var computed_wtax 	=	parseFloat(subtotal) * parseFloat(wtaxrate);

	$('#wtaxamount').val(computed_wtax);
	$('#t_wtax').val(computed_wtax.toFixed(2));
}

$(document).ready(function(){

	// -- For Vendor -- 
		// Get getPartnerInfo
		$( "#vendor" ).change(function() 
		{
			$vendor_id = $("#vendor").val();

			if( $vendor_id != "" )
				getPartnerInfo($vendor_id);
		});

		// Open Modal
		$('#vendor_button').click(function()
		{
			$('#vendor_modal').modal('show');
		});

		$('#vendor_terms').on('blur', function(e) 
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


		$('.discount').prop('readonly',true);


	// -- For Vendor -- End

	// -- For Items -- 
		//For Edit
		computeAmount();
		
		$('.itemcode').on('change', function(e) 
		{
			var id = $(this).attr("id");
			var sup = $('#vendor').val();
			var cur = $('#currency').val();
			if(sup != "" && cur != ""){
				getItemDetails(id);			
			}
			else if(sup == ""){
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Supplier');
				setZero();
			}
			else if(cur == ""){
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Currency');
				setZero();
			}
		});

		$('.warehouse').on('change', function(e) 
		{
			var id = $(this).attr('id').replace(/[a-z]/g,'');
			var item 	=	"itemcode"+id;
			var code = $(this).val();
			var itemcode = document.getElementById('itemcode' + id).value;
			$.post('<?=MODULE_URL?>ajax/get_onhandqty', "warehouse=" + code +"&itemcode="+itemcode, function(data) 
			{
				document.getElementById('onhandqty' + id).value = data.onhandqty;
			});
			// getItemDetails(item);
			
		});

		$('.taxcode').on('change', function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			var code 	=	$(this).val();

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

		$('.discount').on('change', function(e) 
		{
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

		$('.freight').on('change', function(e) 
		{
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

		$('.insurance').on('change', function(e) 
		{
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

		$('.packaging').on('change', function(e) 
		{
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

		$('.exchange_rate').on('change', function(e) 
		{
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
			
			if(rowlimit == 0 || rows < rowlimit){
				clone.clone(true).insertAfter(ParentRow);
				setZero();
			}else{
				$('#row_limit').modal('show');
			}
			
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

	$('#discounttype').on('change',function(){
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
				$('#warning_modal #warning_message').html("<b>You cannot enter a value greater than 100 !</b>");
				$('#warning_modal').modal('show');
			}
		}
	});

	// -- For Discount -- End

	// -- Tax -- 

	$('#purchase_order_form').on('change', '#t_wtaxcode',function(){
		
		var wtaxcode 	=	$(this).val();

		ajax.code 		= 	wtaxcode;

		if( wtaxcode != "NA1" )
		{
			$.post('<?=BASE_URL?>purchase/import_purchaseorder/ajax/get_ATC', ajax, function(data) 
			{
				$('#atcModal #atccode').html(data.atc_codes);
				$('#purchase_order_form #wtaxrate').val(data.wtaxrate).trigger('change');	
				
				computeWTAX();
				computeAmount();
				
				$('#atcModal').modal('show');
			});
		}
	});

	$('#atcModal #btnProceed').on('click',function(){
		var selected_atc = $('#atcModal #atccode').val();
		
		$('#purchase_order_form #s_atc_code').val(selected_atc);
		var h_atc 		 = $('#purchase_order_form #s_atc_code').val();

		if( h_atc != "" )
		{
			$('#atcModal').modal('hide');
			$('#purchase_order_form').trigger('change');
		}
	});

	// -- Tax -- End

	// -- For Saving -- 

		// Process New Transaction
		if('<?= $task ?>' == "create")
		{
			var error = '';
			var warning = '';
			var checkamount = '';
			$("#purchase_order_form").on('change blur',function()
			{
				if($("#purchase_order_form #itemcode\\[1\\]").val() != '' && $("#purchase_order_form #transaction_date").val() != '' && $("#purchase_order_form #due_date").val() != '' && $("#purchase_order_form #vendor").val() != '')
				{
					$.post("<?=BASE_URL?>purchase/import_purchaseorder/ajax/save_temp_data",$("#purchase_order_form").serialize())
					.done(function(data)
					{	
						var parse = JSON.stringify(data.msg);
						var parsed = JSON.parse(parse);
						error = parsed['error'];
						warning = parsed['warning'];
						checkamount = parsed['checkamount'];
						date_checker = parsed['date_checker'];
					});
				}
			});

			//Final Saving
			$('#purchase_order_form #btnSave').click(function(){

				finalizeTransaction("final", error, warning, checkamount, date_checker);

			});

			//Save & Preview
			$("#purchase_order_form #save_preview").click(function()
			{
				finalizeTransaction("final_preview", error, warning, checkamount, date_checker);
			});

			//Save & New
			$("#purchase_order_form #save_new").click(function()
			{
				finalizeTransaction("final_new", error, warning, checkamount, date_checker);
			});
		}
		else if('<?= $task ?>' == "edit")
		{
			//Final Saving
			$('#purchase_order_form .save').click(function(){
				$('#itemsTable tbody tr td').find('.warehouse').find('option[disabled]').prop('disabled', false)
				$('#save').val("final");

				finalizeEditTransaction();
			});

			//Save & Preview
			// $("#purchase_order_form #save_preview").click(function()
			// {
			// 	$('#save').val("final_preview");

			// 	finalizeEditTransaction();
			// });

			//Save & New
			// $("#purchase_order_form #save_new").click(function()
			// {
			// 	$('#save').val("final_new");

			// 	finalizeEditTransaction();
			// });
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

$('#currency').on('change', function(){
	ajax.currency 		= 	$(this).val();

	$.post('<?=MODULE_URL?>ajax/get_exchange_rate', ajax, function(data) {
		$('#exchange_rate').val(data.exchangerate);	
		computeAmount();
	});
});

$('#discounttype').on('select2:selecting',function(e){
	var curr_type	=  $(this).val();	
	var new_type 	= e.params.args.data.id;
	if(curr_type == "none" || curr_type == ""){
		$('#discounttype').closest('.form-group').removeClass('has-error');

		if(new_type=="none" || new_type==""){
			$('.discount').prop('readOnly',true);
		} else {
			$('.discount').prop('readOnly',false);
		}
	} else {
		if(curr_type!="" ){
			e.preventDefault();
			$('#discounttypeModal').modal('show');
			$(this).select2('close');
		}
	}
	$('#newdisctype').val(new_type);
});

$('#discounttypeModal').on('click','#disc_yes',function(){
	var newtype = $('#newdisctype').val();
	$('#discounttype').val(newtype).trigger('change');
	$('#discounttypeModal').modal('hide');
	$('.discount').val('0.00');

	if(newtype=="none" || newtype==""){
		$('.discount').prop('readOnly',true);
	} else {
		$('.discount').prop('readOnly',false);
	}
	computeAmount();
});

$('#discounttypeModal').on('click','#disc_no',function(){
	$('#discounttypeModal').modal('hide');
});

</script>
<?php endif; ?>