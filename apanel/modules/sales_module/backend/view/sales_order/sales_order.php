<section class="content">
 
	<div class="box box-primary">

		<form id = "CustomerDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			<input class = "form_iput" value = "<?=$close_date?>" name = "h_close_date" id = "h_close_date" type="hidden">
			<!-- <input class = "form_iput" value = "<?//=$h_disctype?>" name = "h_disctype" id = "h_disctype" type="hidden"> -->
		</form>

		<form method = "post" class="form-horizontal" id = "sales_order_form">

			<input class = "form_iput" value = "<?=$h_curr_limit?>" name = "h_curr_limit" id = "h_curr_limit" type="hidden">
			<input class = "form_iput" value = "<?=$h_outstanding?>" name = "h_outstanding" id = "h_outstanding" type="hidden">
			<input class = "form_iput" value = "<?=$h_incurred?>" name = "h_incurred" id = "h_incurred" type="hidden">
			<input class = "form_iput" value = "<?=$h_balance?>" name = "h_balance" id = "h_balance" type="hidden">
			<input class = "form_iput" value = "<?=$vat_ex?>" name = "vat_ex" id = "vat_ex" type="hidden">
			<input class = "form_iput" value = "<?=$task?>" name = "task" id = "task" type="hidden">
			<input class = "form_iput" value = "" name = "h_curr_customer" id = "h_curr_customer" type="hidden">
			
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
										->setDefault('none')
										->draw($show_input);
								?>
								<input type="hidden" value="" id="newdisctype" name="newdisctype">
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

						<div class="row">
							<div class = "col-md-12">
								<?php
									echo $ui->formField('textarea')
											->setLabel('Shipping Address:')
											->setSplit('col-md-2', 'col-md-10')
											->setName('s_address')
											->setId('s_address')
											->setAttribute(array("maxlength" => "105"))
											->setValidation('required')
											->setValue($s_address)
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
											->draw($show_input);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="has-error">
				<span id="discountError" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					You cannot input a Discount Amount greater than your Price Amount.	
				</span>
				<span id="discount100Error" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					You cannot input a Percentage Discount greater than 100.
				</span>
				<span id="negaDiscountError" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					You cannot input a Negative Percentage Discount. 
				</span>
			</div>	

			<?php if (isset($cancelled_items) && $cancelled_items): ?>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#ordered" aria-controls="ordered" role="tab" data-toggle="tab">Ordered</a></li>
					<li role="presentation"><a href="#delivered" aria-controls="delivered" role="tab" data-toggle="tab">Delivered</a></li>
					<li role="presentation"><a href="#cancelled" aria-controls="cancelled" role="tab" data-toggle="tab">Cancelled</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="ordered">
					<?php endif ?>

					<div class="box-body table-responsive no-padding">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Item</th>
									<th class="col-md-2 text-center">Description</th>
									<th class="col-md-1 text-center">Warehouse</th>
									<th class="col-md-1 text-center">Quantity</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-1 text-center">Discount</th>
									<th class="col-md-1 text-center">Tax</th>
									<th class="col-md-2 text-center">Amount</th>
									<?php if($task !="view"):?><th class="text-center"></th><?php endif;?>
								</tr>
							</thead>
							<tbody>
								<?php
									if($task == 'create' && empty($quotation_no))
									{
										$accountcode 	   	= '';
										$detailparticulars 	= '';
										$warehouse 			= '';
										$discounttype 		= '';
										$price	   			= '0.00';
										$discount 			= '0.00';
										$rowamount 			= '0.00';

										$quantity 		 	= 0;
										$uom 				= '';
										$row 			   	= 1;
										$total_debit 	   	= 0;
										$total_credit 	   	= 0;
										$vatable_sales 	   	= 0;
										$vat_exempt_sales 	= 0;
										$t_subtotal 		= 0;
										$t_discount 		= 0;
										$t_discount  		= 0;
										$t_total 			= 0;
										$t_vat 				= 0;
										$t_vatsales 		= 0;
										$t_vatexempt 		= 0;
										$t_vatzerorated 	= 0;
										$discount_check_amt = 0;
										$discount_check_perc= 0;
										$itemdiscount  		= 0;
										$discountedamount 	= 0;

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
												<input type = "hidden" id = <?php echo 'h_itemcode['.$row.']'; ?> name = <?php echo 'h_itemcode['.$row.']'; ?> class = "h_itemcode" value = "">
												<input type = "hidden" id = <?php echo 'h_parentcode['.$row.']'; ?> name = <?php echo 'h_parentcode['.$row.']'; ?> class = "h_parentcode" value = "">
												<input type = "hidden" id = <?php echo 'h_isbundle['.$row.']'; ?> name = <?php echo 'h_isbundle['.$row.']'; ?> class = "h_isbundle" value = "">
												<input type = "hidden" id = <?php echo 'h_parentline['.$row.']'; ?> name = <?php echo 'h_parentline['.$row.']'; ?> class = "h_parentline" value = "">
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('detailparticulars['.$row.']')
															->setId('detailparticulars['.$row.']')
															->setClass('itemdescription')
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
												<input type = "hidden" id = <?php echo 'h_warehouse['.$row.']'; ?> name = <?php echo 'h_warehouse['.$row.']'; ?> class = "h_warehouse" value = "">
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
												<input type = "hidden" id = <?php echo 'h_quantity['.$row.']'; ?> name = <?php echo 'h_quantity['.$row.']'; ?> class = "h_quantity" value = "">
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('uom['.$row.']')
															->setId('uom['.$row.']')
															->setClass("text-right itemuom")
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
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('discount['.$row.']')
															->setId('discount['.$row.']')
															->setClass("text-right discount")
															->setAttribute(array("maxlength" => "20","readOnly"=>true))
															->setValue($discount)
															->setValidation('decimal')
															//->addHidden(true)
															->draw($show_input);
												?>
											</td>
											<td class = "remove-margin">
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
												<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12 taxrate' type = 'hidden' value='0.00' >
												<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12 taxamount' type = 'hidden' value='0.00'>
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('amount['.$row.']')
															->setId('amount['.$row.']')
															->setClass("text-right itemamount")
															->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
															->setValidation('decimal')
															->setValue($rowamount)
															->draw($show_input);
												?>

												<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
												<input id = '<?php echo 'itemdiscount['.$row.']'; ?>' name = '<?php echo 'itemdiscount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $itemdiscount;?>'>
												<input id = '<?php echo 'discountedamount['.$row.']'; ?>' name = '<?php echo 'discountedamount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $discountedamount;?>'>
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

										$readOnly 		= true;
										if($discounttype !="" || $discounttype != "none"){
											$readOnly 	= false;
										}
										
										for($i = 0; $i < count($details); $i++)
										{
											$itemcode 	 		= $details[$i]->itemcode;
											$parentcode 	 	= $details[$i]->parentcode;
											$isbundle 	 		= $details[$i]->isbundle;
											$parentline 	 	= $details[$i]->parentline;
											$detailparticular	= htmlspecialchars($details[$i]->detailparticular);
											$quantity 			= isset($details[$i]->issueqty) ?	number_format($details[$i]->issueqty,0) 	: 	"1";
											$bundle_itemqty 	= isset($details[$i]->bundle_itemqty) ?	number_format($details[$i]->bundle_itemqty,0) 	: 	"0";
											$itemprice 			= $details[$i]->unitprice;
											$discounttype 		= isset($details[$i]->discounttype) ? $details[$i]->discounttype : '';
											$discount 			= isset($details[$i]->discountamount) ? $details[$i]->discountamount : '0.00';
											$discountrate 		= isset($details[$i]->discountrate) ? $details[$i]->discountrate : '0.00';
											$discount 			= ($discounttype == "perc") ? $discountrate 	:	$discount;
											$uom 				= $details[$i]->issueuom;
											$taxcode 			= $details[$i]->taxcode;
											$taxrate 			= $details[$i]->taxrate;
											$amount  			= $details[$i]->amount;
											$uom  				= (empty($quotation_no)) ? $details[$i]->issueuom 	: 	$details[$i]->issueuom;
											$warehouse_code		= (empty($quotation_no)) ? $details[$i]->warehouse 	: 	'';
											$warehouse_name		= (empty($quotation_no)) ? $details[$i]->description: 	'';
											$itemdiscount  		= (isset($details[$i]->discountamount)) ? $details[$i]->discountamount : 0;
											$discountedamount 	= (isset($details[$i]->discountedamount))? $details[$i]->discountedamount : 0;
											
									?>	
											<?php if ($parentcode == '') { ?>
												<tr class="clone" valign="middle" style = "font-weight:bold">

											<?php } else { ?>
												<tr class="clone" valign="middle" style = "font-weight:normal">
											<?php } ?>
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
												<input type = "hidden" id = <?php echo 'h_itemcode['.$row.']'; ?> name = <?php echo 'h_itemcode['.$row.']'; ?> class = "h_itemcode" value = "<?php echo $itemcode ?>">
												<input type = "hidden" id = <?php echo 'h_parentcode['.$row.']'; ?> name = <?php echo 'h_parentcode['.$row.']'; ?> class = "h_parentcode" value = "<?php echo $parentcode ?>">
												<input type = "hidden" id = <?php echo 'h_isbundle['.$row.']'; ?> name = <?php echo 'h_isbundle['.$row.']'; ?> class = "h_isbundle" value = "<?php echo $isbundle ?>">
												<input type = "hidden" id = <?php echo 'h_parentline['.$row.']'; ?> name = <?php echo 'h_parentline['.$row.']'; ?> class = "h_parentline" value = "<?php echo $parentline ?>">
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('detailparticulars['.$row.']')
															->setId('detailparticulars['.$row.']')
															->setClass('detailparticulars')
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
														->setSplit('', 'col-md-12')
														->setName("warehouse[".$row."]")
														->setId("warehouse[".$row."]")
														->setClass('warehouse')
														->setList($warehouses)
														->setValidation('required')
														->setValue($value)
														->draw($show_input);
												?>
												<input type = "hidden" id = <?php echo 'h_warehouse['.$row.']'; ?> name = <?php echo 'h_warehouse['.$row.']'; ?> class = "h_warehouse" value = "<?php echo $value ?>">
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
												<input type = "hidden" id = <?php echo 'h_quantity['.$row.']'; ?> name = <?php echo 'h_quantity['.$row.']'; ?> class = "h_quantity" value = "<?php echo $bundle_itemqty ?>">
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
													$is_required = ($parentcode == '') ? 'required' : '';
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('itemprice['.$row.']')
															->setId('itemprice['.$row.']')
															->setClass("price text-right")
															->setAttribute(array("maxlength" => "20"))
															->setValidation('decimal '.$is_required)
															->setValue(number_format($itemprice,'2','.',','))
															->draw($show_input);
												?>
											</td>
											<td class = "remove-margin text-right">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('discount['.$row.']')
															->setId('discount['.$row.']')
															->setClass("text-right discount")
															->setAttribute(array("maxlength" => "20"))
															->setValue($discount)
															->setValidation('decimal')
															//->addHidden(true)
															->draw($show_input);
												?>
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('dropdown')
															->setSplit('', 'col-md-12')
															->setName('taxcode['.$row.']')
															->setId('taxcode['.$row.']')
															->setClass("taxcode")
															->setAttribute(array('maxlength' => '20'))
															->setList($tax_codes)
															->setNone('none')
															->setValue($taxcode)
															->draw($show_input);
												?>
												<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12 taxrate' type = 'hidden' value="<?php echo $taxrate;?>" > 
												<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12 taxamount' type = 'hidden' >
											</td>
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
												<input id = '<?php echo 'itemdiscount['.$row.']'; ?>' name = '<?php echo 'itemdiscount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $itemdiscount;?>'>
												<input id = '<?php echo 'discountedamount['.$row.']'; ?>' name = '<?php echo 'discountedamount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $discountedamount;?>'>
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

								<tr id="vatable_sales" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VATable Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12 col-sm-12')
													->setName('t_vatsales')
													->setId('t_vatsales')
													->setClass("input_label text-right remove-margin")
													->setAttribute(array("readOnly"=>"readOnly"))
													->setValue(number_format($t_vatsales,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
								
								<tr id="vat_exempt_sales" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VAT-Exempt Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_vatexempt')
													->setId('t_vatexempt')
													->setAttribute(array("readOnly"=>"readOnly"))
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($t_vatexempt,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr id="vat_zerorated_sales" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VAT Zero Rated Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_vatzerorated')
													->setId('t_vatzerorated')
													->setAttribute(array("readOnly"=>"readOnly"))
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($t_vatzerorated,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr id="total_sales" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Total Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_subtotal')
													->setId('t_subtotal')
													->setClass("input_label text-right")
													->setAttribute(array("readOnly"=>"readOnly"))
													->setAttribute(array("maxlength" => "40"))
													->setValue(number_format($t_subtotal,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr id="total_sales" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Add 12% VAT</label>
									</td>
									<td class="text-right"> 
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_vat')
													->setId('t_vat')
													->setClass("input_label text-right")
													->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
													->setValue(number_format($t_vat,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr id="total_amount_due">
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Total Amount</label>
									</td>
									<td class="text-right" style="border-top:1px solid #DDDDDD;">
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_total')
													->setId('t_total')
													->setClass("input_label text-right")
													->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
													->setValue(number_format($t_total,'2','.',','))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
								<tr>
									<td colspan="9"></td>
								</tr>
								<tr id="discount" >
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Discount</label>
									</td>
									<td class="text-right" >
										<div class="col-md-7"></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('t_discount')
													->setId('t_discount')
													->setClass("input_label text-right")
													->setAttribute(array("readOnly"=>"readOnly"))
													->setAttribute(array("maxlength" => "40"))
													->setValue(number_format($t_discount,2))
													->draw($show_input);
										?>
										<?php 	//if($show_input) : ?>
											<!-- <div class = 'row'>
												<div class="col-md-6">
													<div class="form-group">
														<div class="col-md-12">
															<div class="input-group">
																<div class="input-group-addon with-checkbox">
																	<?php
																		// echo $ui->setElement('radio')
																		// 		->setName('discounttype')
																		// 		->setClass('discounttype')
																		// 		->setDefault('perc')
																		// 		->setValue($discounttype)
																		// 		->draw($show_input);
																	?>
																</div>
																<?php
																	// echo $ui->setElement('text')
																	// 		->setId('discountrate')
																	// 		->setName('discountrate')
																	// 		->setClass('discount_entry rate text-right')
																	// 		->setAttribute(array('data-max' => 99.99, 'data-min' => '0.00'))
																	// 		->setValidation('decimal')
																	// 		->setValue(((empty($discountrate)) ? '0.00' : number_format($discountrate, 2)))
																	// 		->draw($show_input);
																?>
																<div class="input-group-addon">
																	<strong>%</strong>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<div class="col-md-12">
															<div class="input-group">
																<div class="input-group-addon with-checkbox">
																	<?php
																	// echo $ui->setElement('radio')
																	// 		->setName('discounttype')
																	// 		->setClass('discounttype')
																	// 		->setDefault('amt')
																	// 		->setValue($discounttype)
																	// 		->draw($show_input);
																	?>
																</div>
																<?php
																// echo $ui->setElement('text')
																// 		->setId('discountamount')
																// 		->setName('discountamount')
																// 		->setClass('discount_entry text-right')
																// 		->setAttribute(array('data-min' => '0.00'))
																// 		->setValidation('decimal')
																// 		->setValue(((empty($discountamount)) ? '0.00' : number_format($discountamount, 2)))
																// 		->draw($show_input);
																?>
															</div>
														</div>
													</div>
												</div>
											</div> -->
										<?php 	
												// else:
												// 	echo $ui->setElement('text')
												// 			->setName('discountamount')
												// 			->setClass('total_discount')
												// 			->setValue(((empty($discountamount)) ? '0.00' : number_format($discountamount, 2)))
												// 			->draw(false);
												// endif 
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
							</tfoot>
						</table>
					</div>

					<?php if (isset($cancelled_items) && $cancelled_items): ?>
					</div>
					<div role="tabpanel" class="tab-pane" id="delivered">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable2">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Item</th>
									<th class="col-md-2 text-center">Description</th>
									<th class="col-md-1 text-center">Warehouse</th>
									<th class="col-md-1 text-center">Quantity</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-1 text-center">Discount</th>
									<th class="col-md-1 text-center">Tax</th>
									<th class="col-md-2 text-center">Amount</th>
									<?php if($task !="view"):?><th class="text-center"></th><?php endif;?>
								</tr>
							</thead>
							<tbody>
							<?php 
								$vatable_sales	= 0;
								$vat_exempt		= 0;
								$vat_zerorated	= 0;
								$total_discount	= 0;
								$tax			= 0;
								$total_amount 	= 0;
								?>
								<?php foreach ($delivered_items as $row): ?>
								<?php 
									if($row->taxrate > 0.00 || $row->taxrate > 0 )	{
										$vatable_sales += $row->amount-$row->discountamount;
									}
									else {
										if ($row->taxcode == '' || $row->taxcode == 'none' || $row->taxcode == 'ES') {
											$vat_exempt += $row->amount-$row->discountamount;
										}
										else {
											$vat_zerorated += $row->amount-$row->discountamount;
										}
									}

									$tax += ($row->amount - $row->discountamount) * $row->taxrate;
									$total_discount += $row->discountamount;
								?>
									<tr class="clone" valign="middle">
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setClass('itemcode')
													->setList($itemcodes)
													->setValue($row->itemcode)
													->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setValue($row->detailparticular)
														->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setClass('warehouse')
													->setList($warehouses)
													->setValue($row->warehouse)
													->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass('quantity text-right')
														->setValidation('integer')
														->setValue(number_format($row->issueqty, 0))
														->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right")
														->setValue($uom)
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right price")
														->setValue(number_format($row->unitprice, 2))
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												if ($discounttype == "perc") {
													echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right discount")
														->setValue($row->discountrate)
														->setValidation('decimal')
														->draw(false);
												}
												else {
													echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right discount")
														->setValue($row->discountamount)
														->setValidation('decimal')
														->draw(false);
												}
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
														->setSplit('', 'col-md-12')
														->setClass("taxcode")
														->setList($tax_codes)
														->setValue($row->taxcode)
														->setNone('none')
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right")
														->setValidation('decimal')
														->setValue(number_format($row->amount-$row->discountamount, 2))
														->draw(false);
											?>
										</td>		
									</tr>
								<?php endforeach ?>
								<?php
									$total_sales = $vatable_sales + $vat_exempt + $vat_zerorated;
									$total_amount_due = $vatable_sales + $vat_exempt + $vat_zerorated + $tax;
								?>
							</tbody>
							<tfoot class="summary">
								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VATable Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12 col-sm-12')
													->setName('t_vatsales')
													->setId('t_vatsales')
													->setClass("input_label text-right remove-margin")
													->setAttribute(array("readOnly"=>"readOnly"))
													->setValue(number_format($vatable_sales,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
								
								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VAT-Exempt Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($vat_exempt,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">VAT Zero Rated Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($vat_zerorated,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Total Sales</label>
									</td>
									<td class="text-right" >
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right")
													->setValue(number_format($total_sales,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Add 12% VAT</label>
									</td>
									<td class="text-right"> 
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right")
													->setValue(number_format($tax,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>

								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Total Amount</label>
									</td>
									<td class="text-right" style="border-top:1px solid #DDDDDD;">
										<div class = 'col-md-7'></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right")
													->setValue(number_format($total_amount_due,'2','.',','))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
								<tr>
									<td colspan="9"></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td class="right" colspan="2">
										<label class="control-label col-md-12">Discount</label>
									</td>
									<td class="text-right" >
										<div class="col-md-7"></div>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setClass("input_label text-right")
													->setAttribute(array("readOnly"=>"readOnly"))
													->setValue(number_format($total_discount,2))
													->draw($show_input);
										?>
									</td>
									<?php if($task != "view"):?><td></td><?php endif;?>
								</tr>
							</tfoot>
						</table>
					</div>
					<div role="tabpanel" class="tab-pane" id="cancelled">
						<table class="table table-hover table-condensed table-sidepad" id="itemsTable3">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Item</th>
									<th class="col-md-2 text-center">Description</th>
									<th class="col-md-1 text-center">Warehouse</th>
									<th class="col-md-1 text-center">Quantity</th>
									<th class="col-md-1 text-center">UOM</th>
									<th class="col-md-1 text-center">Price</th>
									<th class="col-md-1 text-center">Discount</th>
									<th class="col-md-1 text-center">Tax</th>
									<th class="col-md-2 text-center">Amount</th>
									<?php if($task !="view"):?><th class="text-center"></th><?php endif;?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($cancelled_items as $row): ?>
									<tr class="clone" valign="middle">
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setClass('itemcode')
													->setList($itemcodes)
													->setValue($row->itemcode)
													->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setValue($row->detailparticular)
														->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('	', 'col-md-12')
													->setClass('warehouse')
													->setList($warehouses)
													->setValue($row->warehouse)
													->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass('quantity text-right')
														->setValidation('integer')
														->setValue(number_format($row->balance_qty, 0))
														->draw(false);
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right")
														->setValue($uom)
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right price")
														->setValue(number_format($row->unitprice, 2))
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												if ($discounttype == "perc") {
													echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right discount")
														->setValue($row->discountrate)
														->setValidation('decimal')
														->draw(false);
												}
												else {
													echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right discount")
														->setValue($row->discountamount)
														->setValidation('decimal')
														->draw(false);
												}
											?>
										</td>
										<td class = "remove-margin">
											<?php
												echo $ui->formField('dropdown')
														->setSplit('', 'col-md-12')
														->setClass("taxcode")
														->setList($tax_codes)
														->setValue($row->taxcode)
														->setNone('none')
														->draw(false);
											?>
										</td>
										<td class = "remove-margin text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setClass("text-right")
														->setValidation('decimal')
														->setValue(number_format($row->unitprice * $row->balance_qty, 2))
														->draw(false);
											?>
										</td>		
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif ?>

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

<div class="modal fade" id="changeCustomerModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
			</div>
			<div class="modal-body" id="message">
				Changing the current customer will clear out the items section. Do you wish to proceed?
			</div>
			<div class="modal-footer text-center">
				<button type="button" class="btn btn-info btn-flat" id="disc_yes" data-dismiss='modal'>Yes</button>
				<button type="button" class="btn btn-default btn-flat" id="disc_no" >No</button>
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

		$('<option value="'+optionvalue+'">'+optionvalue+" - "+optiondesc+'</option>').insertAfter("#sales_order_form #customer option:last-child");
		$('#sales_order_form #customer').val(optionvalue);
		
		retrieveCreditLimit(optionvalue);
		retrieveCurrentIncurredReceivables(optionvalue);
		retrieveCurrentOutstandingReceivables(optionvalue);
		computeforremainingcredit();

		getPartnerInfo(optionvalue);

		$('#customer_modal').modal('hide');
		$('#customer_modal').find("input[type=text], textarea, select").val("");
	}

	function retrieveCurrentOutstandingReceivables(customercode){
		$.post('<?php echo BASE_URL?>sales/sales_order/ajax/retrieve_outstanding_receivables', "customercode=" + customercode, function(data) {
			$('#h_outstanding').val(data.outstanding_receivables);
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

var items 		= [];

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
				document.getElementById('h_itemcode'+row).value 			=	itemcode;
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

			if (data.bundle == '1') {
				document.getElementById('h_isbundle'+row).value 			=	"Yes";
			}
			else {
				document.getElementById('h_isbundle'+row).value 			=	"No";
			}
		});

	}
}

$('.itemcode').on('change', function() {
	var itemcode = $(this).val();
	var name = $(this).attr("name");
	var linenum = name.match(/\d+/)[0];
	var row = $(this);
	var customer = $('#customer').val();
	$.post('<?=BASE_URL?>sales/sales_order/ajax/get_bundle_items',"itemcode="+itemcode+"&linenum="+linenum, function(data) {
		if(data.table != "" && customer != '') {
			var table = data.table;
			row.closest('tr').attr('class', 'clone ' + linenum);
			$('#itemsTable tbody tr.clone select').select2('destroy');
			row.closest('tr.'+linenum).after(table);
			row.closest('tr').find('.h_parentline').val(linenum);
		} else {
			if(row.closest('tr').hasClass(linenum))	{
				row.closest('tr').nextAll('tr.'+linenum).remove();
			}
		}
		resetIds();
		drawTemplate();
	});
});

$('.quantity').on('blur', function() {
	var itemcode = $(this).closest('tr').find('.itemcode').val();
	var parent = $(this).closest('tr').find('.h_parentline').val();
	var quantity = $(this).val();
	var task = $('#task').val();
	$('#itemsTable tbody tr').find('.h_parentline[value="'+parent+'"]').each(function(index, value) {
		var itemqty = $(this).closest('tr').find('.quantity').attr('data-id');
		var total = quantity * itemqty;
		$(this).closest('tr.parts').find('.quantity').val(total);
		if (task == 'edit') {
			var bundleqty = $(this).closest('tr.parts').find('.h_quantity').val();
			var total = quantity * bundleqty;
			$(this).closest('tr.parts').find('.quantity').val(total);
		}
	});
});

$('.warehouse').on('change', function() {
	var itemcode = $(this).closest('tr').find('.itemcode').val();
	var parent = $(this).closest('tr').find('.h_parentline').val();
	var warehouse = $(this).val();
	var task = $('#task').val();
	$(this).closest('tr').find('.h_warehouse').val(warehouse);
	$('#itemsTable tbody tr').find('.h_parentline[value="'+parent+'"]').each(function(index, value) {
		$(this).closest('tr.parts').find('.warehouse').val(warehouse);
		$(this).closest('tr.parts').find('.h_warehouse').val(warehouse);
		if (task == 'edit') {
			$(this).closest('tr.parts').find('.warehouse').val(warehouse).trigger('change.select2');
			$(this).closest('tr.parts').find('.h_warehouse').val(warehouse).trigger('change.select2');
		}
	});
});

$( document ).ready(function() {
    $('#itemsTable tbody tr').find('.itemcode').each(function(index, value) {
		parent = $(this).closest('tr').find('.h_parentcode').val();
		linenum = $(this).closest('tr').find('.h_parentline').val();
		if (parent != '') {
			$(this).closest('tr').find('.itemcode').prop('disabled',true)
			$(this).closest('tr').find('.warehouse').prop('disabled',true)
			$(this).closest('tr').find('.taxcode').prop('disabled',true)
			$(this).closest('tr').find('.detailparticulars').prop('readonly',true)
			$(this).closest('tr').find('.quantity').prop('readonly',true)
			$(this).closest('tr').find('.price').prop('readonly',true)
			$(this).closest('tr').find('.discount').prop('readonly',true)
			$(this).closest('tr').find('.confirm-delete').prop('disabled',true)
			$(this).closest('tr').addClass('parts '+linenum);
		}
		else {
			$(this).closest('tr').addClass(linenum);
		}
	});
});

/**COMPUTE ROW AMOUNT**/
function computeAmount()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.tBodies[0].rows.length;

	// var discounttype 	= $('#itemsTable tfoot .discounttype:checked').val();
	// var discount_rate 	= removeComma($('#itemsTable tfoot #discountrate').val());
	// var discount 		= removeComma($('#itemsTable tfoot #discountamount').val());

	var vatex 	 		= $('#vat_ex').val();
	var discounttype 	= $('#discounttype').val();
	
	var total_amount   	= 0;
	var total_tax 		= 0;
	for(row = 1; row <= count; row++) {  
		var vat 		=	document.getElementById('taxrate['+row+']');
		var itemprice 	=	document.getElementById('itemprice['+row+']');
		var discount 	=	document.getElementById('discount['+row+']');
		var quantity 	=	document.getElementById('quantity['+row+']');

		vat 			=	removeComma(vat.value);
		vat 			= 	(vat == "" || vat == undefined) 	?	0 	:	vat;
		itemprice 		=	removeComma(itemprice.value);
		quantity 		=	removeComma(quantity.value);
		discount 		=	removeComma(discount.value);

		var totalprice 	=	parseFloat(itemprice) 	* 	parseFloat(quantity);
		var amount 		=	0;
		var vat_amount 	=	0;

		if(vatex == 'yes'){
			amount 		= parseFloat(totalprice);
		} else {
			amount		= parseFloat(totalprice) / ( 1 + parseFloat(vat) );
		}	
			
		var itemdiscount = 0;
		var discountedamount = 0;

		if(discounttype!="none" || discounttype!=""){
			if(parseFloat(discount) > 0){
				itemdiscount 	= (discounttype == 'amt') ? parseFloat(discount) : parseFloat(amount) * (parseFloat(discount)/100);
				discountedamount= parseFloat(amount) - parseFloat(itemdiscount);
			} else {
				itemdiscount 	= 0;
				discountedamount= parseFloat(amount);
			}

			document.getElementById('itemdiscount['+row+']').value 	= addCommas(itemdiscount.toFixed(2));
			document.getElementById('discountedamount['+row+']').value 	= addCommas(discountedamount.toFixed(2));
			
			amount 		=	discountedamount;
		}

		if(vatex == 'yes'){
			if(parseFloat(discountedamount) > 0){
				vat_amount	= parseFloat(discountedamount) * parseFloat(vat);
			} else {
				vat_amount	= parseFloat(totalprice) * parseFloat(vat);
			}
		} else {
			vat_amount	= parseFloat(amount)	*	parseFloat(vat);
		}

		amount			= 	(amount>0) 		?	Math.round(amount*1000) / 1000 	:	0;
		vat_amount		= 	(vat_amount>0) 	?	Math.round(vat_amount*100) / 100:	0;

		document.getElementById('amount['+row+']').value 			=	addCommas(amount.toFixed(2));
		document.getElementById('h_amount['+row+']').value 			=	addCommas(amount.toFixed(5));
		// document.getElementById('taxrate['+row+']').value 			= 	addCommas(vat.toFixed(2));
		document.getElementById('taxamount['+row+']').value 		= 	addCommas(vat_amount.toFixed(5));
		document.getElementById('discountedamount['+row+']').value 	= 	addCommas(amount.toFixed(5));

		total_amount 	+= amount;
		total_tax 		+= vat_amount;
	}

	// if (discounttype == 'perc') {
	// 	discount = total_amount * (discount_rate / 100);
	// 	$('#itemsTable tfoot #discountamount').val(addComma(discount));
	// }

	addAmounts(); 
}

/**COMPUTE TOTAL AMOUNTS**/
function addAmounts() {
	var total_h_vatable		= 0;
	var total_h_vatex		= 0;
	var total_h_zero		= 0;
	var total_h_vat			= 0;
	var total_discount		= 0;
	var total_gross_disc	= 0;
	var total_amount 		= 0;
	var subtotal 			= 0;

	var table				= document.getElementById('itemsTable');
	var count				= table.tBodies[0].rows.length;

	//console.log(count);
	var vatex 	 			= $('#vat_ex').val();
	var discounttype  		= $('#discounttype').val();

	// var discounttype = $('#itemsTable tfoot .discounttype:checked').val();
	// var discount_rate = removeComma($('#itemsTable tfoot #discountrate').val());
	// 	total_discount = removeComma($('#itemsTable tfoot #discountamount').val());
	
	for (var i = 1; i <= count; i++) {
		var row = '[' + i + ']';
		var x_unitprice			= document.getElementById('itemprice' + row);
		var x_discount			= document.getElementById('discount' + row);
		var x_quantity			= document.getElementById('quantity' + row);
		var x_taxrate			= document.getElementById('taxrate' + row);
		var x_amount			= document.getElementById('amount' + row);
		var x_taxamount			= document.getElementById('taxamount' + row);
		var h_amount			= document.getElementById('h_amount' + row);
		var h_itemdiscount		= document.getElementById('itemdiscount' + row);
		var h_discountedamount	= document.getElementById('discountedamount' + row);
		var taxcode				= document.getElementById('taxcode' + row);
		
		var unitprice			= removeComma(x_unitprice.value);
		var discount			= removeComma(x_discount.value);
		var taxrate				= removeComma(x_taxrate.value);
		var quantity 			= removeComma(x_quantity.value);
		var h_discountedamount	= removeComma(h_discountedamount.value);

		var totalprice 	=	parseFloat(unitprice) 	* 	parseFloat(quantity);

		var amount 		=	0;
		var vat_amount 	=	0;

		if(vatex == 'yes'){
			amount 		= parseFloat(totalprice);
			if(parseFloat(discount)>0 && (discounttype != "none" || discounttype!="")){
				discount 	= (discounttype == "amt") ? parseFloat(discount) : parseFloat(totalprice) * (parseFloat(discount)/100);
				amount 		= parseFloat(totalprice) - parseFloat(discount);
			}
			vat_amount	= parseFloat(totalprice) * parseFloat(taxrate);
		} else {
			amount		= parseFloat(totalprice) / ( 1 + parseFloat(taxrate) );
			vat_amount	= parseFloat(amount)	*	parseFloat(taxrate);
		}

		var net_of_vat		= 0;
		var vat_ex			= 0;
		var vat_zero		= 0;
		var vat				= 0;
		
		x_amount.value		= addCommas(amount.toFixed(2));
		h_amount.value		= amount.toFixed(2);

		var tax_code = '';
		if (taxcode != null) {
			tax_code = taxcode.value;
		}

		if( parseFloat(taxrate) > 0.00 || parseFloat(taxrate) > 0 )	{
			net_of_vat 		= amount;
		}
		else {
			if (tax_code == '' || tax_code == 'none' || tax_code == 'ES') {
				vat_ex = amount;
			}
			else {
				vat_zero = amount;
			}
		}


		net_of_vat 			= net_of_vat * 1;
		//vat_ex				= amount - net_of_vat;
		vat					= h_discountedamount * taxrate;
		
		net_of_vat 			= Math.round(net_of_vat * 100) / 100;
		vat_ex 				= Math.round(vat_ex * 100) / 100;
		vat_zero 			= Math.round(vat_zero * 100) / 100;
		vat 				= Math.round(vat * 100) / 100;

		total_h_vatable		+= net_of_vat;
		total_h_vatex		+= vat_ex;
		total_h_zero		+= vat_zero;
		total_h_vat			+= vat;
		total_discount 		+= discount;
	}

	vatable_sales 		= 0;
	subtotal 			= total_h_vatable + total_h_vatex + total_h_zero;
	final_total 		= (total_h_vatable + total_h_vatex + total_h_zero - total_discount + total_h_vat);	
	
	if(vatex=="yes"){
		vatable_sales 		= (parseFloat(total_h_vatable));
		vatable_sales 		= (vatable_sales > 0) ? vatable_sales 	: 0;
		total_h_vat 		= (parseFloat(total_h_vatable))*0.12;
		total_h_vat 		= (total_h_vat > 0) ? total_h_vat 	: 0;
		total_h_vatable 	= vatable_sales;
		final_total 		= (total_h_vatable + total_h_vatex + total_h_zero + total_h_vat);
	}	

	final_total 		= Math.round(100*final_total)/100;
	total_h_vatable	 	= Math.round(100*total_h_vatable)/100;
	total_h_vatex	 	= Math.round(100*total_h_vatex)/100;
	total_h_zero	 	= Math.round(100*total_h_zero)/100;
	subtotal	 		= Math.round(100*subtotal)/100;
	total_h_vat	 		= Math.round(100*total_h_vat)/100;
	total_discount 		= Math.round(100*total_discount)/100;

	document.getElementById('t_vatsales').value		= addCommas(total_h_vatable.toFixed(2));
	document.getElementById('t_vatexempt').value	= addCommas(total_h_vatex.toFixed(2));
	document.getElementById('t_vatzerorated').value	= addCommas(total_h_zero.toFixed(2));
	document.getElementById('t_subtotal').value 	= addCommas(subtotal.toFixed(2));
	document.getElementById('t_discount').value 	= addCommas(total_discount.toFixed(2));
	document.getElementById('t_vat').value			= addCommas(total_h_vat.toFixed(2));
	document.getElementById('t_total').value 		= addCommas(final_total.toFixed(2));
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

	//console.log( " count = "+count);

	x = 1;
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];

		row.cells[0].getElementsByTagName("select")[0].id 	= 'itemcode['+x+']';
		row.cells[0].getElementsByTagName("input")[0].id 	= 'h_itemcode['+x+']';
		row.cells[0].getElementsByTagName("input")[1].id 	= 'h_parentcode['+x+']';
		row.cells[0].getElementsByTagName("input")[2].id 	= 'h_isbundle['+x+']';
		row.cells[0].getElementsByTagName("input")[3].id 	= 'h_parentline['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("select")[0].id 	= 'warehouse['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'h_warehouse['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[1].id 	= 'h_quantity['+x+']';
		row.cells[4].getElementsByTagName("input")[0].id 	= 'uom['+x+']';
		row.cells[5].getElementsByTagName("input")[0].id 	= 'itemprice['+x+']';
		row.cells[6].getElementsByTagName("input")[0].id 	= 'discount['+x+']';
		row.cells[7].getElementsByTagName("select")[0].id 	= 'taxcode['+x+']';
		row.cells[7].getElementsByTagName("input")[0].id 	= 'taxrate['+x+']';
		row.cells[7].getElementsByTagName("input")[1].id 	= 'taxamount['+x+']';
		row.cells[8].getElementsByTagName("input")[0].id 	= 'amount['+x+']';
		row.cells[8].getElementsByTagName("input")[1].id 	= 'h_amount['+x+']';
		row.cells[8].getElementsByTagName("input")[2].id 	= 'itemdiscount['+x+']';
		row.cells[8].getElementsByTagName("input")[3].id 	= 'discountedamount['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[0].getElementsByTagName("input")[0].name 	= 'h_itemcode['+x+']';
		row.cells[0].getElementsByTagName("input")[1].name 	= 'h_parentcode['+x+']';
		row.cells[0].getElementsByTagName("input")[2].name 	= 'h_isbundle['+x+']';
		row.cells[0].getElementsByTagName("input")[3].name 	= 'h_parentline['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("select")[0].name = 'warehouse['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'h_warehouse['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[1].name 	= 'h_quantity['+x+']';
		row.cells[4].getElementsByTagName("input")[0].name 	= 'uom['+x+']';
		row.cells[5].getElementsByTagName("input")[0].name 	= 'itemprice['+x+']';
		row.cells[6].getElementsByTagName("input")[0].name 	= 'discount['+x+']';
		row.cells[7].getElementsByTagName("select")[0].name = 'taxcode['+x+']';
		row.cells[7].getElementsByTagName("input")[0].name 	= 'taxrate['+x+']';
		row.cells[7].getElementsByTagName("input")[1].name 	= 'taxamount['+x+']';
		row.cells[8].getElementsByTagName("input")[0].name 	= 'amount['+x+']';
		row.cells[8].getElementsByTagName("input")[1].name 	= 'h_amount['+x+']';
		row.cells[8].getElementsByTagName("input")[2].name 	= 'itemdiscount['+x+']';
		row.cells[8].getElementsByTagName("input")[3].name 	= 'discountedamount['+x+']';

		row.cells[9].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[9].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

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
	document.getElementById('h_itemcode['+newid+']').value 			= '';
	document.getElementById('h_parentcode['+newid+']').value 		= '';
	document.getElementById('h_isbundle['+newid+']').value 			= '';
	document.getElementById('h_parentline['+newid+']').value 		= '';
	document.getElementById('detailparticulars['+newid+']').value 	= '';
	document.getElementById('warehouse['+newid+']').value 			= '';
	document.getElementById('h_warehouse['+newid+']').value 		= '';
	document.getElementById('quantity['+newid+']').value 			= '0';
	document.getElementById('h_quantity['+newid+']').value 			= '0';
	document.getElementById('uom['+newid+']').value 				= '';
	document.getElementById('itemprice['+newid+']').value 			= '0.00';
	document.getElementById('discount['+newid+']').value 			= '0.00';
	document.getElementById('taxcode['+newid+']').value 			= 'none';
	document.getElementById('taxrate['+newid+']').value 			= '0.00';
	document.getElementById('taxamount['+newid+']').value 			= '0.00';
	document.getElementById('amount['+newid+']').value 				= '0.00';
	document.getElementById('h_amount['+newid+']').value 			= '0.00';
	document.getElementById('itemdiscount['+newid+']').value 		= '0.00';
	document.getElementById('discountedamount['+newid+']').value 	= '0.00';
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
	$("#sales_order_form").find('.form-group').find('input, textarea, select').trigger('blur_validate');

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

	computeforremainingcredit();

	var credit_limit_exceed =	checkIfExceededCreditLimit();
	if(credit_limit_exceed == 1){
		no_error = false;
	}

	computeAmount();

	// console.log($('#sales_order_form').find('.form-group.has-error').length);

	if($('#sales_order_form .form-group .has-error').length == 0 && no_error){	
		$('#save').val(type);
		if($("#sales_order_form #s_address").val() != '' && $("#sales_order_form #itemcode\\[1\\]").val() != '' && $("#sales_order_form #quantity\\[1\\]").val() != '' && $("#sales_order_form #quantity\\[1\\]").val() != '' && $("#sales_order_form #warehouse\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #customer").val() != '')
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

	$("#sales_order_form").find('.form-group').find('input, textarea, select').trigger('blur_validate');

	var no_error = true;
	$('.quantity').each(function() {
		if( $(this).val() <= 0 )
		{
			no_error = false;
			$(this).closest('div').addClass('has-error');
		}
	});

	$('.price').each(function() {
		var validations = $(this).attr('data-validation').split(' ');
		if( $(this).val() <= 0 && validations.includes('required'))
		{
			no_error = false;
			$(this).closest('div').addClass('has-error');
		}
	});

	computeforremainingcredit();

	var credit_limit_exceed =	checkIfExceededCreditLimit();
	if(credit_limit_exceed == 1){
		no_error = false;
	}
	// console.log($('#sales_order_form').find('.form-group.has-error').length);
	if($('#sales_order_form .form-group .has-error').length == 0 && no_error)
	{
		if($("#sales_order_form #itemcode\\[1\\]").val() != ''  && $("#sales_order_form #quantity\\[1\\]").val() != '' && $("#sales_order_form #quantity\\[1\\]").val() != '' && $("#sales_order_form #warehouse\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #due_date").val() != '' && $("#sales_order_form #customer").val() != '')
		{
			setTimeout(function() {
				computeAmount();
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

		$( "#customer" ).on('select2:selecting',function(e) {
			var curr_id	=  $(this).val();	
			var new_id 	= e.params.args.data.id;
			if( curr_id != "" ){
				e.preventDefault();
				$('#changeCustomerModal').modal('show');
				$(this).select2('close');
			} else {
				retrieveCreditLimit(new_id);
				retrieveCurrentIncurredReceivables(new_id);
				retrieveCurrentOutstandingReceivables(new_id);
				computeforremainingcredit();

				getPartnerInfo(new_id);
			}
			$('#h_curr_customer').val(new_id);
		});

		$('#changeCustomerModal').on('click','#disc_yes',function(){
			var customer_id = $('#h_curr_customer').val();
			$('#customer').val(customer_id).trigger('change');

			retrieveCreditLimit(customer_id);
			retrieveCurrentIncurredReceivables(customer_id);
			retrieveCurrentOutstandingReceivables(customer_id);
			computeforremainingcredit();

			getPartnerInfo(customer_id);
			// if( $('#itemcode\\[1\\]').val() != "" ){
			// 	$('.itemcode').trigger('change');
			// }

			$('#itemsTable tbody').find('tr').not(':first').remove();
			setZero();
			resetIds();
		});

		$('#changeCustomerModal').on('click','#disc_no',function(){
			$('#changeCustomerModal').modal('hide');
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

		$('.itemcode').on('change', function(e) {
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

		$('#sales_order_form').on('change','.taxcode',function(e){
			
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			var code 	= 	$(this).val();
			var taxcode_= 	$(this);

			$.post('<?=BASE_URL?>sales/sales_invoice/ajax/get_value', "taxcode=" + code + "&event=getTaxRate", function(data) {
				taxcode_.closest('tr').find('.taxrate').val(data.taxrate).trigger('change');
			});

			computeAmount();
		});

		$('#sales_order_form').on('change','.quantity',function(e){
			var element = $(this);
			var items = [];
			
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
			
			var this_element = $(this);
			var warehouse_element = $(this).closest('tr').find('select.warehouse');
			var warehouse = warehouse_element.val();
			var itemcode = $(this).closest('tr').find('.itemcode').val();
			if (warehouse == '') {
				warehouse_element.trigger('blur');
				$(this).val(0);
			}
			// if (removeComma($(this).val()) > 0) {
			// 	var quantity = getQuantity(itemcode, warehouse);
					
			// 	$.post('<?php //echo BASE_URL?>sales/sales_order/ajax/retrieve_item_quantity', "itemcode="+itemcode+"&warehouse="+warehouse, function(data) {
			// 		var data_qty = data.qty;
			// 		var x = removeComma(data_qty);
			// 		if ((quantity > x) ){
			// 			$('#orderQtymodal').modal('show');
			// 			$(this_element).val(0);
			// 		}
			// 	});
				
			// }
		});


		function getQuantity(itemcode_d, warehouse_d) {
			var quantities = [];
			$('.quantity').each(function() {
				var itemcode = $(this).closest('tr').find('.itemcode').val();
				var warehouse = $(this).closest('tr').find('select.warehouse').val();
				var quantity = parseFloat(removeComma($(this).closest('tr').find('.quantity').val()));
				if (typeof quantities[itemcode] == 'undefined') {
					quantities[itemcode] = [];
				}
				if (typeof quantities[itemcode][warehouse] == 'undefined') {
					quantities[itemcode][warehouse] = 0;
				}
				quantities[itemcode][warehouse] += quantity;
			});
			return quantities[itemcode_d][warehouse_d];
		}

		$('#sales_order_form').on('change','.price',function(e){
			
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
			
			var linenum = $("#itemsTable tbody tr").attr('class').split(' ');
			var clone = $("#itemsTable tbody tr.clone:first").attr('class', 'clone').clone(true); 

			var ParentRow = $("#itemsTable tbody tr.clone").last();
			
			var table 		= document.getElementById('itemsTable');
			var rows 		= table.tBodies[0].rows.length;

			clone.clone(true).insertAfter(ParentRow);
			$("#itemsTable tbody tr.clone:first").attr('class', 'clone ' + linenum[1]);
			setZero();

			$('#itemsTable tbody tr.clone select').select2({width: "100%"});
			drawTemplate();
		});
		
	// -- For Items -- End

	// -- For Discount --
		$('#sales_order_form').on('change','.discount',function(e){
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			var dtype 	= 	$('#discounttype').val();
			var value 	= 	$(this).val();

			var price 	= 	removeComma($(this).closest('tr').find('.price').val());
			var form 	=	$(this);
			if( parseFloat(value) > 0 && dtype == ""){
				$('#discounttype').closest('.form-group').addClass('has-error');
			} else {
				if(dtype == "perc" && parseFloat(value) > 100){
					form.closest('div').addClass('has-error');
					form.addClass('greaterthan100');
				} else {
					form.closest('div').removeClass('has-error');
				}
				if(dtype == "perc" && parseFloat(value) < 0){
					form.closest('div').addClass('has-error');
					form.addClass('negativediscount');
				} else {
					form.closest('div').removeClass('has-error');
				}
				if( parseFloat(value) > 0 && parseFloat(value) > parseFloat(price) ){
					form.addClass('greaterthanprice');
					form.closest('div').addClass('has-error');
				} else {
					form.closest('div').removeClass('has-error');
				}
				var pricediscounterrors 	=	$('#sales_order_form .greaterthanprice').closest('tr').find('.has-error').length;
				var greaterthan100 			=	$('#sales_order_form .greaterthan100').closest('tr').find('.has-error').length;
				var negativediscount 		=	$('#sales_order_form .negativediscount').closest('tr').find('.has-error').length;
				
				if(pricediscounterrors > 0){
					$('#discountError').removeClass('hidden');
				} else {
					$('#discountError').addClass('hidden');
				}

				if(greaterthan100 > 0){
					$('#discount100Error').removeClass('hidden');
				} else {
					$('#discount100Error').addClass('hidden');
				}

				if(negativediscount > 0){
					$('#negaDiscountError').removeClass('hidden');
				} else {
					$('#negaDiscountError').addClass('hidden');
				}

			}
			formatNumber(id);
			computeAmount();
		});

		$('#sales_order_form').on('change','.price',function(e){
			var id 		= 	$(this).attr("id");
			var row 	=	id.replace(/[a-z]/g, '');
			var dtype 	= 	$('#discounttype').val();
			var price 	= 	removeComma($(this).val());

			var discount 	= 	removeComma($(this).closest('tr').find('.discount').val());
			var form 	=	$(this).closest('tr').find('.discount');
			if( parseFloat(discount) > 0 && dtype == ""){
				$('#discounttype').closest('.form-group').addClass('has-error');
			} else {
				if(dtype == "perc" && parseFloat(discount) > 100){
					form.closest('div').addClass('has-error');
					form.addClass('greaterthan100');
				} else {
					form.closest('div').removeClass('has-error');
				}
				if(dtype == "perc" && parseFloat(discount) < 0){
					form.closest('div').addClass('has-error');
					form.addClass('negativediscount');
				} else {
					form.closest('div').removeClass('has-error');
				}
				if( parseFloat(discount) > 0 && parseFloat(discount) > parseFloat(price) ){
					form.addClass('greaterthanprice');
					form.closest('div').addClass('has-error');
				} else {
					form.closest('div').removeClass('has-error');
				}
				var pricediscounterrors 	=	$('#sales_order_form .greaterthanprice').closest('tr').find('.has-error').length;
				var greaterthan100 			=	$('#sales_order_form .greaterthan100').closest('tr').find('.has-error').length;
				var negativediscount 		=	$('#sales_order_form .negativediscount').closest('tr').find('.has-error').length;

				if(pricediscounterrors > 0){
					$('#discountError').removeClass('hidden');
				} else {
					$('#discountError').addClass('hidden');
				}

				if(greaterthan100 > 0){
					$('#discount100Error').removeClass('hidden');
				} else {
					$('#discount100Error').addClass('hidden');
				}

				if(negativediscount > 0){
					$('#negaDiscountError').removeClass('hidden');
				} else {
					$('#negaDiscountError').addClass('hidden');
				}

			}
			formatNumber(id);
			computeAmount();
		});

		$('#discounttype').on('select2:selecting',function(e){
			var curr_type	=  $(this).val();	
			var new_type 	= e.params.args.data.id;
			//var parentcode = $('.itemcode').closest('tr').find('.h_parentcode').val();
			
			if(curr_type == "none" || curr_type == ""){
				$('#discounttype').closest('.form-group').removeClass('has-error');

				if(new_type=="none" || new_type==""){
					$('.discount').prop('readOnly',true);
				} else {
					$('#itemsTable tbody tr').find('.itemcode').each(function(index, value) {
						parent = $(this).closest('tr').find('.h_parentcode').val();
						if (parent == '') {
							$(this).closest('tr').find('.discount').prop('readonly',false)
						}
					});
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
				$('#itemsTable tbody tr').find('.itemcode').each(function(index, value) {
					parent = $(this).closest('tr').find('.h_parentcode').val();
					if (parent == '') {
						$(this).closest('tr').find('.discount').prop('readonly',false)
					}
				});
			}
			computeAmount();
		});

		$('#discounttypeModal').on('click','#disc_no',function(){
			$('#discounttypeModal').modal('hide');
		});
	// -- For Discount -- End

	// -- For Saving -- 

		// Process New Transaction
		if('<?= $task ?>' == "create")
		{
			$("#sales_order_form").on('change blur',function(){
				computeAmount();
				if($("#sales_order_form #itemcode\\[1\\]").val() != '' && $("#sales_order_form #transaction_date").val() != '' && $("#sales_order_form #customer").val() != '')
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

			var discounttype = $('#discounttype').val();
			if(discounttype != "" && discounttype != "none"){
				//$('#sales_order_form .discount').prop('readonly',false);
				$('#itemsTable tbody tr').find('.itemcode').each(function(index, value) {
					parent = $(this).closest('tr').find('.h_parentcode').val();
					if (parent == '') {
						$(this).closest('tr').find('.discount').prop('readonly',false)
					}
				});
			} else {
				$('#sales_order_form .discount').prop('readonly',true);
			}
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
		var parentcode = [];
		$('#deleteItemModal #btnYes').click(function() 
		{
			parentcode = [];
			var id = $('#deleteItemModal').data('id');
			var table 		= document.getElementById('itemsTable');
			var rowCount 	= table.tBodies[0].rows.length;
			
			$('.h_parentcode').each(function() {
				if($(this).val() == '') {
					parentcode.push('none');
				}
			});

			var isbundle = $('.confirm-delete').closest('tr.'+id).find('.h_isbundle').val();
			if(parentcode.length > 1 && isbundle == 'Yes') {
				$('#itemsTable tbody').find('tr.'+id).remove();
				resetIds();
			} else if(parentcode.length == 1 && isbundle == 'Yes') {
				$('#itemsTable tbody').find('tr.parts.'+id).remove();
				setZero();
				drawTemplate();
			} else {
				deleteItem(id);
			}

			addAmounts();
			
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

	// Discount
	// $('#itemsTable tfoot .discount_entry').on('input blur', function() {
	// 	$(this).closest('tr').find('.discounttype').iCheck('uncheck');
	// 	$(this).closest('.input-group').find('.discounttype').iCheck('check');
	// });
	// $('#itemsTable tfoot').on('ifChecked', '.discounttype', function() {
	// 	$(this).closest('tr').find('.discounttype:not(:checked)').closest('.input-group').find('.discount_entry.rate').val('0.00');
	// 	computeAmount();
	// });
});

</script>