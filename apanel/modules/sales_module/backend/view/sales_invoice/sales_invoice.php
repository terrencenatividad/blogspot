<section class="content">

	<div class="box box-primary">
		<div class="box-body">
			<form id = "customerDetailForm">
				<input class = "form_iput" value = "" name = "terms" id="terms" type="hidden">
				<input class = "form_iput" value = "" name = "tinno" id="tinno" type="hidden">
				<input class = "form_iput" value = "" name = "address1" id="address1" type="hidden">
				<input class = "form_iput" value = "update" name = "querytype" id="querytype" type="hidden">
				<input class = "form_iput" value = "" name = "id" id = "id" type="hidden">
			</form>

			<form method = "post" class="form-horizontal" id="sales_invoice_form">
				
				<div class = "row">
					<div class = "col-md-12">&nbsp;</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Invoice No')
									->setSplit('col-md-3', 'col-md-8')
									->setAttribute(array("readOnly" => "readOnly"))
									->setPlaceholder("- auto generate -")
									->setValue($voucherno)
									->draw($ro_input);
						?>
						<input type="hidden" name="voucherno" id="voucherno" value="<?php echo $voucherno?>">
					</div>

					<div class = "col-md-6">
						<?php
							// $datepickerclass= ($task != 'view') ? "date" : "";
							echo $ui->formField('text')
									->setLabel('Invoice Date')
									->setSplit('col-md-3', 'col-md-8')
									->setName('transactiondate')
									->setId('transactiondate')
									->setClass('datepicker datepicker-input')
									->setValue($transactiondate)
									->setValidation('required')
									->setAttribute(array('data-date-start-date' => $close_date))
									->setAddon('calendar')
									->draw($show_input);
						?>
					</div>
				</div>
				<?php
				if($dr_linked)
				{
				?>
				<div class="row">
					<div class = "col-md-6">
					<?php
						if($ro_input){
							echo $ui->formField('dropdown')
								->setLabel('Customer ')
								->setPlaceholder('None')
								->setSplit('col-md-3', 'col-md-8')
								->setName('customer')
								->setId('customer')
								->setList($customer_list)
								->setValue($customercode)
								->setValidation('required')
								->setButtonAddon('plus')
								->draw($ro_input);
						}else{
							echo $ui->formField('text')
								->setLabel('Customer')
								->setSplit('col-md-3', 'col-md-8')
								->setValue($customername)
								->draw($ro_input);

							echo '<input type="hidden" id="customer" name="customer" value="'.$customercode.'">';
						}
							
							
						?>
					
					</div>
					<div class="col-md-6">
					<?php
						echo $ui->formField('text')
								->setLabel('Reference')
								->setId('referenceno')
								->setName('referenceno')
								->setSplit('col-md-3', 'col-md-8')
								->setValue($referenceno)
								->draw($show_input);
					?>
					</div>
				</div>
				<?php
				}
				?>
				<div class = "row">
					<div class = "col-md-6">
						<?php
						if($ro_input)
						{
							// echo $ui->formField('dropdown')
							// 		->setLabel('Delivery Receipt')
							// 		->setPlaceholder('None')
							// 		->setSplit('col-md-3', 'col-md-8')
							// 		->setName('drno')
							// 		->setId('drno')
							// 		->setList($deliveries)
							// 		->setValue($drno)
							// 		->setAddon('search')
							// 		->setValidation('required')
							// 		->draw($ro_input);
							echo $ui->formField('text')
									->setLabel('Delivery Receipt ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('drno')
									->setId('drno')
									->setAttribute(array('readonly'))
									->setAddon('search')
									->setValue($drno)
									->setValidation('required')
									->draw($ro_input);
						}else{
							echo $ui->formField('text')
									->setLabel('Delivery Receipt')
									->setSplit('col-md-3', 'col-md-8')
									->setValue($drno)
									->draw($ro_input);

							echo '<input type="hidden" id="drno" name="drno" value="'.$drno.'">';
						}
					?>
						
						<!--<div class="form-group">
							<label for="voucher_no" class="control-label col-md-3">Customer</label>
							<div class="col-md-8">
								<div class="input-group">
									<select>
										<option>Test</option>
									</select>
									<span class="input-group-btn">
										<a role="button" class="btn btn-info" onClick = "addNewModal('Customer','add','Customer');" href="javascript:void(0);">
											<span class="glyphicon glyphicon-plus"></span>
										</a>
									</span>
								</div>
								<span class="help-block hidden small req-color" id = "customer_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
							</div>
						</div>-->
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Due Date')
									->setSplit('col-md-3', 'col-md-8')
									->setName('duedate')
									->setId('duedate')
									->setValue($duedate)
									->setClass('datepicker datepicker-input')
									->setAttribute(array('data-date-start-date' => $close_date))
									->setAddon('calendar')
									->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<?php
								echo $ui->formField('text')
										->setLabel('<i>Tin</i>')
										->setSplit('col-md-3', 'col-md-9')
										->setName('customer_tin')
										->setId('customer_tin')
										->setAttribute(
											array(
												"maxlength" => "15",
												"rows" => "1",
												"tabindex" => -1
											)
										)
										->setPlaceholder("000-000-000-000")
										->setClass("input_label")
										->setValue($tinno)
										->draw($show_input);
							?>
						</div>
						<div class="row">
							<?php
								echo $ui->formField('text')
										->setLabel('<i>Terms</i>')
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_terms')
										->setId('customer_terms')
										->setAttribute(
											array(
												"readonly" => "", 
												"maxlength" => "15",
												"tabindex" => -1
											)
										)
										->setPlaceholder("0")
										->setClass("input_label")
										->setValue($terms)
										->draw($show_input);
							?>
						</div>
						<div class="row">
							<?php
								echo $ui->formField('textarea')
										->setLabel('<i>Address</i>')
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_address')
										->setId('customer_address')
										->setClass("input_label")
										->setAttribute(
											array(
												"readonly" => "", 
												"rows" => "1",
												"tabindex" => -1
											)
										)
										->setValue($address1)
										->draw($show_input);
							?>
						</div>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
									->setLabel('Notes')
									->setSplit('col-md-3', 'col-md-8')
									->setName('remarks')
									->setAttribute(
										array(
											'rows'=>6
										)
									)
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
									<th class="col-md-2 text-left">Item</th>
									<th class="col-md-3 text-left">Description</th>
									<th class="col-md-1 text-left">Quantity</th>
									<th class="col-md-1 text-left">UOM</th>
									<th class="col-md-1 text-left">Price</th>
									<th class="col-md-2 text-left">Tax</th>
									<th class="col-md-2 text-left">Amount</th>
									<th class="col-md-1 taxt-left"></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if($task == 'create')
									{
										$itemcode 	   		= '';
										$detailparticulars 	= '';
										$quantity 			= 1;
										$price	   			= '0.00';
										$taxcode 			= '';
										$amount 			= '0.00';
										$itemdiscount  		= 0;
										$discountedamount 	= 0;

										$row 			   	= 1;
										$vatable_sales 	   	= 0;
										$vatexempt_sales 	= 0;
										$total_sales		= 0;
										$discountamount		= 0;
										$total_tax			= 0;
										$total 				= 0;

								?>
										<!-- <tr class="clone" valign="middle">
											<td>
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
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('detailparticulars['.$row.']')
															->setId('detailparticulars['.$row.']')
															->setAttribute(
																array(
																	"maxlength" => "100"
																)
															)
															->setValue("")
															->draw($show_input);
												?>
											</td>
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('quantity['.$row.']')
															->setId('quantity['.$row.']')
															->setClass('quantity text-right')
															->setAttribute(
																array(
																	"maxlength" => "20"
																)
															)
															->setValue($quantity)
															->draw($show_input);
												?>
											</td>
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('itemprice['.$row.']')
															->setId('itemprice['.$row.']')
															->setClass("text-right price ")
															->setAttribute(
																array(
																	"maxlength" => "20"
																)
															)
															->setValue($price)
															->draw($show_input);
												?>
											</td>
											<td>
												<?php
													echo $ui->formField('dropdown')
															->setSplit('', 'col-md-12')
															->setName('taxcode['.$row.']')
															->setId('taxcode['.$row.']')
															->setClass("taxcode")
															->setAttribute(
																array(
																	"maxlength" => "20",
																	"readonly" => true
																)
															)
															->setList($tax_codes)
															->setNone('none')
															->draw($show_input);
												?>
												<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00' >
												<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value='0.00'>
											</td>
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('amount['.$row.']')
															->setId('amount['.$row.']')
															->setClass("text-right amount")
															->setAttribute(
																array(
																	"maxlength" => "20"
																)
															)
															->setValue($amount)
															->draw($show_input);
												?>

												<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
												<input id = '<?php echo 'itemdiscount['.$row.']'; ?>' name = '<?php echo 'itemdiscount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $itemdiscount;?>'>
												<input id = '<?php echo 'discountedamount['.$row.']'; ?>' name = '<?php echo 'discountedamount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $discountedamount;?>'>
											</td>
											<td class="text-center">
												<button type="button" class="btn btn-danger btn-flat btn-sm confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
											</td>			
										</tr> -->
										<tr>
											<td colspan="7" class="text-center">
												<em>Please select a <strong>Delivery Receipt</strong> to load items.</em>
											</td>
										</tr>
								<?
									}
									else if(!empty($sid) && $task!='create')
									{
										$row 			= 1;
										$disable_debit	= '';
										$disable_credit	= '';
										
										// $quantity_attr 	= (!empty($drno)) ? array(
										// 	"maxlength" => "20",
										// 	"readOnly" => "readOnly"
										// ) : array(
										// 	"maxlength" => "20"
										// );


										for($i = 0; $i < count($details); $i++)
										{
											$itemcode 	 		= $details[$i]->itemcode;
											$detailparticular	= $details[$i]->detailparticular;
											$quantity 			= $details[$i]->issueqty;
											$uom 				= $details[$i]->issueuom;
											$uom 				= strtoupper($uom);
											$itemprice 			= $details[$i]->unitprice;
											$taxcode 			= $details[$i]->taxcode;
											$taxrate 			= $details[$i]->taxrate;
											$taxamount 			= $details[$i]->taxamount;
											$amount  			= $details[$i]->amount;
											$itemdiscount  		= $details[$i]->itemdiscount;
											$discountedamount 	= $details[$i]->discountedamount;
											
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
														->setAttribute(array("disabled"=>true))
														->setValidation('required')
														->draw($show_input);
												?>
												<input id = '<?php echo 'h_itemcode['.$row.']'; ?>' name = '<?php echo 'h_itemcode['.$row.']';?>' class = 'col-md-12' type = 'hidden' value = '<?php echo $itemcode;?>'>
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('detailparticulars['.$row.']')
															->setId('detailparticulars['.$row.']')
															->setAttribute(array("maxlength" => "100","readOnly"=>"readOnly"))
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
															->setClass('text-right quantity')
															->setAttribute(array("maxlength" => "20","readOnly" => "readOnly"))
															->setValue(number_format($quantity,0))
															->draw($show_input);
												?>
											</td>
											<td class = "remove-margin">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setValue($uom)
															->draw(false);
												?>
											</td>
											<td class = "remove-margin text-right">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('itemprice['.$row.']')
															->setId('itemprice['.$row.']')
															->setClass("text-right price")
															->setAttribute(array("maxlength" => "20","readOnly"=>"readOnly"))
															->setValue(number_format($itemprice,2))
															->draw($show_input);
												?>
											</td>
											<td class = "remove-margin">
												<?php

													if( $task == 'view' )
														$value 	=	$taxcode;
													else
														$value 	=	$taxcode;

													echo $ui->formField('dropdown')
															->setSplit('', 'col-md-12')
															->setName('taxcode['.$row.']')
															->setId('taxcode['.$row.']')
															->setClass("taxcode")
															->setAttribute(array("maxlength" => "20","disabled" => true))
															->setList($tax_codes)
															->setNone('none')
															->setValue($value)
															->draw($show_input);
												?>
												<input id = '<?php echo 'h_taxcode['.$row.']'; ?>' name = '<?php echo 'h_taxcode['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value = '<?php echo $taxcode;?>'>
												<input id = '<?php echo 'taxrate['.$row.']'; ?>' name = '<?php echo 'taxrate['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value = '<?php echo $taxrate;?>'>
												<input id = '<?php echo 'taxamount['.$row.']'; ?>' name = '<?php echo 'taxamount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' value = '<?php echo $taxamount;?>'>	
											</td>
											<td class = "remove-margin text-right">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('amount['.$row.']')
															->setId('amount['.$row.']')
															->setClass("text-right amount")
															->setAttribute(array("maxlength" => "20","readOnly"=>"readOnly"))
															->setValue(number_format($amount,2))
															->draw($show_input);
												?>
												
												<input id = '<?php echo 'h_amount['.$row.']'; ?>' name = '<?php echo 'h_amount['.$row.']';?>' maxlength = '20' class = 'col-md-12' type = 'hidden' >
												<input id = '<?php echo 'itemdiscount['.$row.']'; ?>' name = '<?php echo 'itemdiscount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $itemdiscount;?>'>
												<input id = '<?php echo 'discountedamount['.$row.']'; ?>' name = '<?php echo 'discountedamount['.$row.']';?>' maxlength = '20' type = 'hidden' value = '<?php echo $discountedamount;?>'>
											</td>
											<?if($task!='view'){ ?>
												<td class="text-center">
													<!--onClick="confirmDelete(<?=$row?>);"-->
													<button type="button" class="btn btn-danger btn-flat confirm-delete disabled" data-id="<?=$row?>" name="chk[]" style="outline:none;" ><span class="glyphicon glyphicon-trash"></span></button>
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
									<td colspan="7" style="border-top:1px solid #E2E2E2;">
										<? if($task != 'view') { ?>
											<!-- <a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a> -->
										<? } ?>
									</td>	
								</tr>	

								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">VATable Sales</label>
									</td>
									<td class="text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('vatable_sales')
													->setId('vatable_sales')
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($vatable_sales,2))
													->draw($show_input);
										?>
									</td>
									
								</tr>
								
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">VAT-Exempt Sales</label>
									</td>
									<td class="text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('vatexempt_sales')
													->setId('vatexempt_sales')
													->setClass("input_label text-right remove-margin")
													->setValue(number_format($vatexempt_sales,2))
													->draw($show_input);
										?>
									</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">Total Sales</label>
									</td>
									<td class="text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('total_sales')
													->setId('total_sales')
													->setClass("input_label text-right")
													->setAttribute(array("maxlength" => "40"))
													->setValue(number_format($total_sales,2))
													->draw($show_input);
										?>
									</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">Discount</label>
									</td>
									<td class="text-right">
										<div class = 'col-md-7'>
										<? if($show_input) {?>
											<!-- <div class="btn-group btn-group-xs" data-toggle="buttons">
												<label class="btn btn-default <?=$disc_radio_amt?>">
													<input type="radio" class='d_opt' name="discounttype" id="discounttype1" autocomplete="off" value="amt" <?=$disc_amt?>>amt
												</label>
												<label class="btn btn-default <?=$disc_radio_perc?>">
													<input type="radio" class='d_opt' name="discounttype" id="discounttype2" autocomplete="off" value="perc" <?=$disc_perc?>>%
												</label>
											</div> -->
											<? } ?>
										</div>
										<div class = 'col-md-5' >
											<input type = "hidden" value = "<?=$disctype?>" name = "disctype" id = "disctype"/>
											<?php
												echo $ui->formField('text')
														->setSplit('', '')
														->setName('discountamount')
														->setId('discountamount')
														->setClass("input_label text-right")
														->setValue(number_format($discountamount,2) . "" . $percentage )
														->draw($show_input);
											?>
										</div>

									</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">Add 12% VAT</label>
									</td>
									<td class="text-right">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('total_tax')
													->setId('total_tax')
													->setClass("input_label text-right")
													->setAttribute(array("maxlength" => "40"))
													->setValue(number_format($total_tax,2))
													->draw($show_input);
										?>
									</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="right"></td>
									<td class="right">
										<label class="control-label col-md-12">Total Amount Due</label>
									</td>
									<td class="text-right" style="border-top:1px solid #DDDDDD;">
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('total')
													->setId('total')
													->setClass("input_label text-right")
													->setAttribute(array("maxlength" => "40"))
													->setValue(number_format($total,2))
													->draw($show_input);
										?>
									</td>
								</tr>
							</tfoot>
						</table>
						<br/>
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
								<button type="button" id="btnSave" class="btn btn-primary">Save</button>
								<?php
								if($task == 'create'){
								?>
								<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu left" role="menu">
									<li style="cursor:pointer;" id="save_new">
										&nbsp;&nbsp;Save &amp; New
										<input type = "hidden" value = "" name = "h_save_new" id = "h_save_new"/>
									</li>
									<li class="divider"></li>
									<li style="cursor:pointer;" id="save_preview">
										&nbsp;&nbsp;Save &amp; Preview
										<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_new"/>
									</li>
								</ul>
								<?php
								}
								?>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-id="<?=$generated_id?>" id="btnCancel" data-toggle="back_page">Cancel</button>
							</div>
						<? 	
						}
						else
						{ 	
							if($status == 'posted' && $restrict_si)
							{
						?>
							<div class="btn-group">
								<a class="btn btn-primary" role="button" href="<?=BASE_URL?>sales/sales_invoice/edit/<?=$sid?>" style="outline:none;">Edit</a>
							</div>
							&nbsp;&nbsp;&nbsp;

						<?php
							}
						?>
							<div class="btn-group">
								<a class="btn btn-default" role="button" href="<?=BASE_URL?>sales/sales_invoice" data-toggle="back_page" style="outline:none;   ">Cancel</a>
							</div>
						<?
						}
						?>
					</div>
				</div>
			</form>
			<div class="row">
				<div class = "col-md-12">&nbsp;</div>
			</div>
		</div>
	</div>
</section>

<script>
function addCustomerToDropdown() {
	var optionvalue = $("#customerModal #customerForm #partnercode").val();
	var optiondesc 	= $("#customerModal #customerForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optionvalue+" - "+optiondesc+'</option>').insertAfter("#sales_invoice_form #customer option:nth-child(4)");
	$('#sales_invoice_form #customer').val(optionvalue);
	
	getPartnerInfo(optionvalue);

	$('#customerModal').modal('hide');
	$('#customerModal').find("input[type=text], textarea, select").val("");

}

function closeModal(){
	$('#customerModal').modal('hide');
}

</script>
<?php
echo $ui->loadElement('modal')
	->setId('customerModal')
	->setContent('maintenance/customer/create')
	->setHeader('Add a Customer')
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

<div id="customer_required" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Oops!</h4>
			</div>
			<div class="modal-body">
				<p>Please select a customer first</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="delivery_list_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Delivery Receipt List</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input type="text" id="order_list_search" class="form-control" placeholder="Search...">
							<div class="input-group-addon">
								<i class="glyphicon glyphicon-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="delivery_receiptList" class="table table-hover table-clickable table-bordered">
					<thead>
						<tr class="info">
							<th class="col-xs-2">DR No.</th>
							<th class="col-xs-2">Document Date</th>
							<th class="col-xs-8">Notes</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="3" class="text-center">Loading Items</td>
						</tr>
					</tbody>
				</table>
				<div id="pagination"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

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
		$.post('<?=BASE_URL?>sales/sales_invoice/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
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

/**RETRIEVE DELIVERY RECEIPT ITEMS**/
function getDeliveries(code)
{
	$.post('<?=BASE_URL?>sales/sales_invoice/ajax/get_deliveries', "code=" + code)
	.done(function(data)
	{
		var customer 	= data.customer;
		var notes 		= data.notes;
		var items 		= data.items;

		$('#sales_invoice_form #customer').val(customer);
		//$('#sales_invoice_form #customer').trigger('change');
		$('#sales_invoice_form #remarks').val(notes);
		$('#itemsTable tbody').html(items);

		addAmounts();
		
		var disc_perc 	= data.discount;
		var total_sales = $('#total_sales').val();
		// console.log('total_sales '+total_sales);
		var discountamt = parseFloat(total_sales) * parseFloat(disc_perc);
		$('#sales_invoice_form #discountamount').val(discountamt.toFixed(2));
		$('#sales_invoice_form #remarks').trigger('change');

		addAmounts();
	});
}

/**COMPUTES DUE DATE**/
function computeDueDate()
{
	var invoice = $("#transactiondate").val();
	var terms 	= $("#customer_terms").val(); 

	if(invoice != '')
	{
		var newDate	= moment(new Date(invoice)).add(terms, 'days').format("MMM DD, YYYY");

		$("#duedate").val(newDate);
		// $("#duedate").datepicker('setDate',newDate);
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

/**RETRIEVAL OF ITEM DETAILS**/
function getItemDetails(id)
{
	var itemcode 	=	document.getElementById(id).value;
	var row 		=	id.replace(/[a-z]/g, '');
	
	if(itemcode != '')
	{
			$.post('<?=BASE_URL?>sales/sales_order/ajax/get_item_details',"itemcode="+itemcode , function(data) 
		{
			document.getElementById('detailparticulars'+row).value 	=	data.itemname;
			document.getElementById('itemprice'+row).value 			= 	data.price;
			
			computeAmount();
		});
	}
}

/**COMPUTE ROW AMOUNT**/
function computeAmount()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.tBodies[0].rows.length;

	var discount		= parseFloat(document.getElementById('discountamount').value || 0.00);
	var total_amount   	= 0;
	for(row = 1; row <= count; row++) 
	{  
		var vat 		=	document.getElementById('taxrate['+row+']');
		
		var itemprice 	=	document.getElementById('itemprice['+row+']');
		var quantity 	=	document.getElementById('quantity['+row+']');

		vat 			=	vat.value.replace(/,/g,'');
		itemprice 		=	itemprice.value.replace(/,/g,'');
		quantity 		=	quantity.value.replace(/,/g,'');
		
		var totalprice 	=	parseFloat(itemprice) 	* 	parseFloat(quantity);
		var amount 		=	parseFloat(totalprice) / ( 1 + parseFloat(vat) );
		
		var vat_amount 	=	parseFloat(amount) * parseFloat(vat);
		
		amount			= 	Math.round(parseFloat(amount)*100) / 100;
		vat_amount		= 	Math.round(parseFloat(vat_amount)*100) / 100;

		document.getElementById('amount['+row+']').value 			=	addCommas(amount.toFixed(5));
		document.getElementById('h_amount['+row+']').value 			=	addCommas(amount.toFixed(5));
	
		document.getElementById('taxamount['+row+']').value 		= 	addCommas(vat_amount.toFixed(5));
		document.getElementById('discountedamount['+row+']').value 	= 	addCommas(amount.toFixed(5));
		total_amount 	+= amount;
	}

	var discount_type 	= document.getElementById('disctype').value;
	discount_perc 		= (discount_type == 'perc') ? discount/100 : discount / total_amount;
	
	if(discount_perc > 0){
		/**
		 * Apply discount to item rows
		 */
		for(row = 1; row <= count; row++) 
		{
			var amount 		=	document.getElementById('amount['+row+']');
			amount 			=	amount.value.replace(/,/g,'');

			var itemdiscount 	= parseFloat(amount) * parseFloat(discount_perc);
			var discountedamount = parseFloat(amount) - parseFloat(itemdiscount);

			document.getElementById('itemdiscount['+row+']').value 	= addCommas(itemdiscount.toFixed(2));
			document.getElementById('discountedamount['+row+']').value 	= addCommas(discountedamount.toFixed(2));
			
		}
	}
	
	addAmounts(); 
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
	var total 				= 0;
	var discount_perc 		= 0;

	var table				= document.getElementById('itemsTable');
	var count				= table.tBodies[0].rows.length;

	var discount			= parseFloat(document.getElementById('discountamount').value || 0.00);
	
	// var discount_type 		= document.getElementById('disctype').value;
	// discount_perc 			= (discount_type == 'perc') ? discount : discount / 100;
	
	for (var i = 1; i <= count; i++) {
		var row = '[' + i + ']';
		var x_unitprice			= document.getElementById('itemprice' + row);
		var x_quantity			= document.getElementById('quantity' + row);
		var x_taxrate			= document.getElementById('taxrate' + row);
		var x_amount			= document.getElementById('amount' + row);
		var x_taxamount			= document.getElementById('taxamount' + row);
		var h_amount			= document.getElementById('h_amount' + row);
		var h_itemdiscount		= document.getElementById('itemdiscount' + row);
		var h_discountedamount	= document.getElementById('discountedamount' + row);

		var unitprice				= x_unitprice.value.replace(/[,]+/g, '');
		var taxrate					= parseFloat(x_taxrate.value);
		var quantity 				= x_quantity.value.replace(/[,]+/g,'');
		//var tax_amount		= ( quantity * unitprice ) * taxrate;
		var amount					= ( quantity * unitprice ) / (taxrate + 1);
		var h_discountedamount		= h_discountedamount.value.replace(/[,]+/g, '');
		
		var net_of_vat		= 0;
		var vat_ex			= 0;
		var vat				= 0;
		var temp_amount 	= 0;

		x_amount.value		= addCommas(amount.toFixed(2));
		h_amount.value		= amount.toFixed(2);
		// x_taxamount.value	= tax_amount.toFixed(2);

		if( taxrate > 0.00 || taxrate > 0 )	
		{
			net_of_vat 		= amount;
		}

		net_of_vat 			= net_of_vat * 1;
		vat_ex				= amount - net_of_vat;
		vat					= amount * taxrate;
		x_taxamount.value	= amount * taxrate;

		/**
		 * Round off to 2 decimals before getting total
		 */
		net_of_vat 			= Math.round(net_of_vat * 100) / 100;
		vat_ex 				= Math.round(vat_ex * 100) / 100;
		vat 				= Math.round(vat * 100) / 100;

		total_h_vatable		+= net_of_vat;
		total_h_vatex		+= vat_ex;
		total_h_vat			+= vat;
	}
	
	subtotal 				= total_h_vatable + total_h_vatex;

	// if( discount_type == 'perc' )
	// {
	// 	total_discount 		= subtotal * ( discount / 100 );
	// }
	// else if( discount_type == 'amt' )
	// {
	// 	total_discount 		= discount;
	// }

	total_discount = discount;
	console.log("TOTAL_DISCOUNT = "+total_discount);
	/**
	 * Round off to 2 decimals before getting total
	 */

	total_h_vatable	 	= Math.round(100*total_h_vatable)/100;
	total_h_vatex	 	= Math.round(100*total_h_vatex)/100;
	subtotal	 		= Math.round(100*subtotal)/100;
	total_h_vat	 		= Math.round(100*total_h_vat)/100;

	document.getElementById('vatable_sales').value		= addCommas(total_h_vatable.toFixed(2));
	document.getElementById('vatexempt_sales').value	= addCommas(total_h_vatex.toFixed(2));
	document.getElementById('total_sales').value 		= addCommas(subtotal.toFixed(2));
	document.getElementById('total_tax').value			= addCommas(total_h_vat.toFixed(2));
	document.getElementById('total').value 				= addCommas(( total_h_vatable + total_h_vatex - total_discount + total_h_vat ).toFixed(2));
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
		row.cells[2].getElementsByTagName("input")[0].id 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'itemprice['+x+']';
		row.cells[4].getElementsByTagName("select")[0].id 	= 'taxcode['+x+']';
		row.cells[4].getElementsByTagName("input")[0].id 	= 'taxrate['+x+']';
		row.cells[4].getElementsByTagName("input")[1].id 	= 'taxamount['+x+']';
		row.cells[5].getElementsByTagName("input")[0].id 	= 'amount['+x+']';
		row.cells[5].getElementsByTagName("input")[1].id 	= 'h_amount['+x+']';
		row.cells[5].getElementsByTagName("input")[2].id 	= 'itemdiscount['+x+']';
		row.cells[5].getElementsByTagName("input")[3].id 	= 'discountedamount['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'quantity['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'itemprice['+x+']';
		row.cells[4].getElementsByTagName("select")[0].name = 'taxcode['+x+']';
		row.cells[4].getElementsByTagName("input")[0].name	= 'taxrate['+x+']';
		row.cells[4].getElementsByTagName("input")[1].name 	= 'taxamount['+x+']';
		row.cells[5].getElementsByTagName("input")[0].name 	= 'amount['+x+']';
		row.cells[5].getElementsByTagName("input")[1].name 	= 'h_amount['+x+']';
		row.cells[5].getElementsByTagName("input")[2].name 	= 'itemdiscount['+x+']';
		row.cells[5].getElementsByTagName("input")[3].name 	= 'discountedamount['+x+']';
		
		row.cells[6].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[6].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

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
	// if(newid > rowlimit)
	// {
	// 	table.deleteRow(newid);
	// }else{
	// 	document.getElementById('itemcode['+newid+']').value 			= '';
	// 	document.getElementById('detailparticulars['+newid+']').value 	= '';
	// 	document.getElementById('quantity['+newid+']').value 			= '1';
	// 	document.getElementById('itemprice['+newid+']').value 			= '0.00';
	// 	document.getElementById('taxcode['+newid+']').value 			= '';
	// 	document.getElementById('taxrate['+newid+']').value 			= '0';
	// 	document.getElementById('amount['+newid+']').value 				= '0.00';
	// 	document.getElementById('h_amount['+newid+']').value 			= '0.00';
	// }
	
	document.getElementById('itemcode['+newid+']').value 			= '';
	document.getElementById('detailparticulars['+newid+']').value 	= '';
	document.getElementById('quantity['+newid+']').value 			= '1';
	document.getElementById('itemprice['+newid+']').value 			= '0.00';
	document.getElementById('taxcode['+newid+']').value 			= 'none';
	document.getElementById('taxrate['+newid+']').value 			= '0';
	document.getElementById('amount['+newid+']').value 				= '0.00';
	document.getElementById('h_amount['+newid+']').value 			= '0.00';
	document.getElementById('itemdiscount['+newid+']').value 		= '0';
	document.getElementById('discountedamount['+newid+']').value 	= '0';

	$('#itemcode\\['+newid+'\\]').trigger('change');
	$('#taxcode\\['+newid+'\\]').trigger('change');
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	if(vno != '')
	{
		// $.post("<?=BASE_URL?>sales/sales_invoice/ajax/cancel",'voucherno='+vno)
		// .done(function(data) 
		// {
		// 	if(data.msg == 'success')
		// 	{
		// 		window.location.href = '<?=BASE_URL?>sales/sales_invoice';
		// 	}
		// });
		window.location.href = '<?=BASE_URL?>sales/sales_invoice';
	}else{
		window.location.href = '<?=BASE_URL?>sales/sales_invoice';
	}
}

/** FINALIZE SAVING **/
function finalizeTransaction()
{
	var valid	= 1;

	$('#sales_invoice_form #transactiondate').trigger('blur');
	$('#sales_invoice_form #customer').trigger('blur');
	$('#sales_invoice_form #drno').trigger('blur');
	$("#sales_invoice_form .itemcode").trigger('blur');

	if ($('#sales_invoice_form').find('.form-group.has-error').length == 0)
	{	
		computeAmount();

		if($("#sales_invoice_form #itemcode\\[1\\]").val() != '' && $("#sales_invoice_form #transactiondate").val() != '' && $("#sales_invoice_form #duedate").val() != '' && $("#sales_invoice_form #customer").val() != '')
		{
			$('#sales_invoice_form #btnSave').addClass('disabled');
			$('#sales_invoice_form #btnSave_toggle').addClass('disabled');
			$('#sales_invoice_form #btnSave').html('Saving...');

			$.post("<?=BASE_URL?>sales/sales_invoice/ajax/save_data/temp_data",$("#sales_invoice_form").serialize())
			.done(function(data)
			{	
				var code 	= data.code;
				var result	= data.msg;

				if(code == 1)
				{
					$('#delay_modal').modal('show');
						setTimeout(function() {									
							$('#sales_invoice_form').submit();
					}, 1000)

					$('#sales_invoice_form #btnSave').removeClass('disabled');
					$('#sales_invoice_form #btnSave_toggle').removeClass('disabled');
					$('#sales_invoice_form #btnSave').html('Save');
				}
			});
			// setTimeout(function() {
			// 	$('#sales_invoice_form').submit();
			// },500);
		}
	}else{
		next = $('#sales_invoice_form').find(".has-error").first();
		$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var valid	= 	0;
	var btn 	=	$('#save').val();
	var voucher = $("#sales_invoice_form #voucherno").val();			
	/**validate Customer fields**/
	$('#sales_invoice_form #transactiondate').trigger('blur');
	//$('#sales_invoice_form #customer').trigger('blur');
	//$('#sales_invoice_form #drno').trigger('blur');
	$("#sales_invoice_form .itemcode").trigger('blur');

	if ($(this).find('.form-group.has-error').length == 0)
	{
		if($("#sales_invoice_form #itemcode\\[1\\]").val() != '' && $("#sales_invoice_form #transactiondate").val() != '' && $("#sales_invoice_form #duedate").val() != '' && $("#sales_invoice_form #customer").val() != '')
		{
			setTimeout(function() {

				$(this).find('.form-group').find('input, textarea, select').trigger('blur');
					
				if( $(this).find('.form-group.has-error').length == 0 ) 
				{
					$.post("<?=BASE_URL?>sales/sales_invoice/ajax/<?=$task?>",$("#sales_invoice_form").serialize(),function(data)
					{		
						var code 	= data.code;
						var result 	= data.msg;

						if( code == 1 )
						{
							if( result == 'final' )
							{
								$('#delay_modal').modal('show');
								setTimeout(function() {							
									window.location 	=	"<?=BASE_URL?>sales/sales_invoice";
								}, 1000)
							}
							else if( result == 'final_preview' )
							{
								window.location 	=	"<?=BASE_URL?>sales/sales_invoice/view/"+voucher;								
							}
							else if( result == 'final_new' )
							{
								window.location 	=	"<?=BASE_URL?>sales/sales_invoice/create";								
							}
							
						}
						else
						{
							//insert error message / MOdal heree
						}
					});
				}
				else 
				{
					$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
				}

			},500);
		}
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
	var voucher		= document.getElementById('voucherno').value;
	var customer 	= document.getElementById('customer').value;
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.tBodies[0].rows.length;;
	var valid		= 1;

	var rowindex	= table.rows[row];
	
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var datatable	= 'salesinvoice_details';
		
		if(rowCount > 1)
		{
			if(task == 'create')
			{
				ajax.table 		=	datatable;
				ajax.linenum 	= 	row;
				ajax.voucherno 	= 	voucher;

				$.post("<?=BASE_URL?>sales/sales_invoice/ajax/delete_row",ajax)
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
		}else{	
			setZero();
			addAmounts();
		}
	}
}

/**ENABLE EDITABLE FIELDS**/
function editField(id)
{
	var field	= document.getElementById(id);
	field.readOnly 				= false;
	field.style.backgroundColor	= '#ffffff';
	
	SelectAll(id);
}


/**SAVE CHANGES ON EDITED FIELDS**/
function saveField(id)
{		
	var field		= document.getElementById(id);
	var customer	= document.getElementById('customer').value;

	var address 	= document.getElementById('customer_address').value;
	var tinno 		= document.getElementById('customer_tin').value;
	var terms 		= document.getElementById('customer_terms').value;
	var code		= customer;
	
	$('#customerDetailForm #address1').val(address);
	$('#customerDetailForm #tinno').val(tinno);
	$('#customerDetailForm #terms').val(terms);
	$('#customerDetailForm #id').val(code);

	if(field.readOnly == false)
	{
		$.post("<?=BASE_URL?>sales/sales_invoice/ajax/save_data/customerdetails", $("#customerDetailForm").serialize())
		.done(function(data)
		{	
			if(data.msg == "success")
			{
				field.readOnly 				= true;
				field.style.backgroundColor	= 'transparent';
			}
		});
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


$(document).ready(function(){

	// -- For Date -- 
	function today()
	{
		var m_names = new Array("Jan", "Feb", "Mar", 
		"Apr", "May", "Jun", "Jul", "Aug", "Sep", 
		"Oct", "Nov", "Dec");

		var d = new Date();
		var curr_date = d.getDate();
		var curr_month = d.getMonth();
		var curr_year = d.getFullYear();
		var today	= m_names[curr_month]+" "+curr_date+", "+ curr_year;
		
		return today;
	}

	//Date picker
	$('.date').datepicker({
		autoclose: true,
		"setDate": today(),
		format: "M dd, yyyy",
		multidateSeparator: '|'
	});

	// -- For Date -- End

	// -- For Customer -- 
	$('#customer_button').click(function()
	{
		$('#customerModal').modal('show');
	});

	// Get getPartnerInfo
	// $( "#drno" ).change(function() 
	// {
	// 	$drno = $("#drno").val();

	// 	if( $drno != "" )
	// 		getDeliveries($drno);
	// });
	var page 	= 1;
	var limit 	= 5;
	var search 	= $('#delivery_list_modal #order_list_search').val();

	$('#drno').on('focus', function() {
		$('#delivery_list_modal #order_list_search').val('');

		var customer = $('#customer').val();
		if (customer == '') {
			$('#customer_required').modal('show');
			$('#customer').trigger('blur');
		} else {
			$.post('<?=MODULE_URL?>ajax/ajax_load_delivery_list', {customer:customer,limit:limit,page:page}, function(data) {
				$('#delivery_receiptList tbody').html(data.table);
				$('#delivery_list_modal').modal('show');
				$('#pagination').html(data.pagination);
			});
		}
	});

	$('#delivery_list_modal #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var customer = $('#customer').val();
		page = $(this).attr('data-page');
		$.post('<?=MODULE_URL?>ajax/ajax_load_delivery_list', {customer:customer,limit:limit,page:page,search:search}, function(data) {
			$('#delivery_receiptList tbody').html(data.table);
			$('#delivery_list_modal').modal('show');
			$('#pagination').html(data.pagination);
		});
	});

	$('#delivery_list_modal #order_list_search').on('input', function(e) {
		e.preventDefault();
		var customer = $('#customer').val();
		search = $(this).val();
		$.post('<?=MODULE_URL?>ajax/ajax_load_delivery_list', {customer:customer,limit:limit,page:page,search:search}, function(data) {
			$('#delivery_receiptList tbody').html(data.table);
			$('#delivery_list_modal').modal('show');
			$('#pagination').html(data.pagination);
		});
	});

	$('#sales_invoice_form').on('click', '#drno + div.input-group-addon', function(e){
		var customer = $('#customer').val();
		if (customer == '') {
			$('#customer_required').modal('show');
			$('#customer').trigger('blur');
		} else {
			$.post('<?=MODULE_URL?>ajax/ajax_load_delivery_list', {customer:customer}, function(data) {
				$('#delivery_receiptList tbody').html(data.table);
				$('#delivery_list_modal').modal('show');
			});
		}
	});
	
	$('#customer').on('change', function() {
		ajax.customer = $(this).val();
		$('#drno').val('');
	});

	$('#delivery_receiptList').on('click', 'tr[data-id]', function() {
		var drno = $(this).attr('data-id');
		//$('#drno').val(drno);
		$('#drno').val(drno).trigger('blur');
		$('#delivery_list_modal').modal('hide');
		getDeliveries(drno);
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
		$customer_id = $("#customer").val();

		if( $customer_id != "" )
			if(e.type == "keypress")
				return isNumberKey(e,45);
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
	});

	$('#customer_address').on('blur click', function(e) 
	{
		$customer_id = $("#customer").val();

		if( $customer_id != "" )
			if(e.type == "blur")
				saveField(e.target.id);
			if(e.type == "click")
				editField(e.target.id);
	});

	$('#customer_terms').on('blur', function(e) 
	{
		$customer_id = $("#customer").val();

		if( $customer_id != "" )
			computeDueDate()
	});

	$('#newCustomer #tinno').on('keypress blur', function(e) 
	{
		if(e.type == "keypress")
			return isNumberKey(e,45);
	});

	$('#newCustomer #terms').on('keypress', function(e) 
	{
		if(e.type == "keypress")
			return isNumberKey(e,45);
	});

	// Validation for Customer Modal
	$('#newCustomer #partnercode, #first_name, #last_name, #address1, #businesstype').on('keyup', function(e) 
	{
		validateField('newCustomer',e.target.id, e.target.id + "_help");
	});

	$('#newCustomer #businesstype').on('change', function(e) 
	{
		validateField('newCustomer',e.target.id, e.target.id + "_help");
	});

	// -- For Customer -- End

	// -- For Items -- 
	//For Edit
	//computeAmount();

	$('tbody').on('change', '.itemcode', function(e){
		var id = $(this).attr("id");
		getItemDetails(id);
	});

	$('tbody').on('change', '.taxcode', function(e){
		var code 		= 	$(this).val();
		var id 			= 	$(this).attr("id");
		var row 		=	id.replace(/[a-z]/g, '');
		
		$.post('<?=BASE_URL?>sales/sales_invoice/ajax/get_value', "taxcode=" + code + "&event=getTaxRate", function(data) 
		{
			document.getElementById('taxrate' + row).value = data.taxrate;

			computeAmount();
		});
	});

	$('tbody').on('change', '.quantity', function(e){
		
		var id 		= 	$(this).attr("id");
		var row 	=	id.replace(/[a-z]/g, '');

		computeAmount();
	});

	$('tbody').on('change', '.price , .amount', function(e){
		
		var id 		= 	$(this).attr("id");
		var row 	=	id.replace(/[a-z]/g, '');

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
		
		if(rowlimit == 0 || rows < rowlimit)
		{
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
		$('#disctype').val(type);
		
		//computeTotalAmount();
	});

	$('#disctype').on('click',function(){
		computeAmount();
	});
	
	$('input[type=radio][name=discounttype]').on('change',function(){
		$('#discountamount').trigger('change');
	});
	

	$('#discountamount').on('change',function(){
		var disc_id =	$('input[type=radio][name=discounttype]:checked').attr('id');
		if( disc_id != "" || disc_id != undefined )
		{
			var type 	=	$('#'+disc_id).val();
			var value 	=	$(this).val();
			
			$('#disctype').val(type);	
			if(type == 'perc'){
				if( value < 100)
				{
					computeAmount();
				}
				else
				{
					//Add Modal here
					bootbox.dialog({
						title: 'Discount Percentage Error!',
						message: "<p>Please make sure that the discount percentage is not equal or greater than 100.</p>",
						buttons: {
							ok: {
								label: "Ok",
								className: 'btn-info',
								callback: function(){
									$('#discountamount').val(0);
									computeAmount();
								}
							}
						}
						});
					
				}
			}else{
				computeAmount();
			}
		}
	});

	// -- For Discount -- End

	// -- For Saving -- 
		
	// Process New Transaction
	if('<?= $task ?>' == "create")
	{

		$("#sales_invoice_form").change(function()
		{
			if($("#sales_invoice_form #itemcode\\[1\\]").val() != '' && $("#sales_invoice_form #transactiondate").val() != '' && $("#sales_invoice_form #customer").val() != '')
			{
				$.post("<?=BASE_URL?>sales/sales_invoice/ajax/save_data/temp_data",$("#sales_invoice_form").serialize())
				.done(function(data)
				{	
					var code 	= data.code;
					var result	= data.msg;

					if(code == 1)
					{
						$("#sales_invoice_form #voucherno").val(result);
					}
				});
			}
		});

		//Final Saving
		$('#sales_invoice_form #btnSave').click(function(){
			
			$('#save').val("final");

			finalizeTransaction();

		});

		//Save & Preview
		$("#sales_invoice_form #save_preview").click(function()
		{
			$('#save').val("final_preview");
			
			finalizeTransaction();
		});

		//Save & New
		$("#sales_invoice_form #save_new").click(function()
		{
			$('#save').val("final_new");

			finalizeTransaction();
		});
	}
	else if('<?= $task ?>' == "edit")
	{
		//Final Saving
		$('#sales_invoice_form #btnSave').click(function(){
			
			$('#save').val("final");

			finalizeEditTransaction();
		});

		//Save & Preview
		$("#sales_invoice_form #save_preview").click(function()
		{
			$('#save').val("final_preview");

			finalizeEditTransaction();
		});

		//Save & New
		$("#sales_invoice_form #save_new").click(function()
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
			var record = document.getElementById('voucherno').value;
			cancelTransaction(record);
		}
		else
		{
			window.location =	"<?= BASE_URL ?>sales/sales_invoice/";
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

</script>