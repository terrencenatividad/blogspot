<style>
	#vendorDetails2 .col-md-3 > .form-group,
	#vendorDetails2 .col-md-2 > .form-group  {
		margin: 0;
	}

	.text-right {
		text-align: right;
	}

	.text-bold {
		font-weight: bold;
		color: #fff;
	}

	.remove-margin > .form-group,
	.remove-margin .form-group {
		margin-bottom: 0;
	}

	.vendor_div > .form-group {
		margin-bottom: 5px;
	}

	.width35 {
		width: 35%;
	}

	.width27 {
		width: 27%;
	}

	.no-bg {
		border: 0px solid transparent;
		background-color: transparent !important;
	}

</style>

<section class="content">

	<input type = "hidden" id = "valid" name = "valid" value = "0"/>
	<input type = "hidden" id = "prefix" name = "prefix" value = "<?= $prefix ?>"/>
	<input type = "hidden" id = "noCashAccounts" name = "noCashAccounts" value = "<?= $noCashAccounts ?>"/>

	<?php 
	if($task == "view") {
			$applicationPannel = "hidden";
			if(!is_null($data["payments"]) && !empty($data["payments"]))
			{
				$applicationPannel = "";
			}
	?>

	<? }else { ?> 
		<div id = "diverror" class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><i class="icon fa fa-warning"></i> The system has encountered the following error/s!</h4>
			<div id = "errmsg">
				<ul class = "text-bold">

				</ul>
			</div>
			// <p class = "text-bold">Please contact admin to fix this issue.</p>
		</div>

		<div class="box box-primary">
			<form id = "customerDetailForm">
				<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
				<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
				<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
				<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
				<input class = "form_iput" value = "customerdetail" name = "h_form" id = "h_form" type="hidden">
				<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			</form>

			<div class="box-body">
				<form method = "post" class="form-horizontal" id = "receivableForm">

					<input class = "form_iput" value = "0.00" name = "h_amount" id = "h_amount" type="hidden">
					<input class = "form_iput" value = "0.00" name = "h_convertedamount" id = "h_convertedamount" type = "hidden">
					<input class = "form_iput" value = "1.00" name = "h_exchangerate" id = "h_exchangerate" type = "hidden">
					<input class = "form_iput" value = "<?= $task ?>" name = "h_task" id = "h_task" type = "hidden">

					<div class = "clearfix">
						<div class = "col-md-12">&nbsp;</div>
						<div class = "row">
							<div class="col-md-11">
								<div class = "row">
									<div class = "col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Voucher No')
													->setSplit('col-md-4', 'col-md-8')
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
													->setSplit('col-md-4', 'col-md-8')
													->setName('document_date')
													->setId('document_date')
													->setClass('datepicker-input')
													->setAttribute(array('readonly' => ''))
													->setAddon('calendar')
													->setValue($transactiondate)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>

								<div class = "row">
									<div class = "col-md-6 vendor_div ">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Customer')
												->setPlaceholder('None')
												->setSplit('col-md-4', 'col-md-8')
												->setName('customer')
												->setId('customer')
												->setList($customer_list)
												->setValue($customercode)
												->setValidation('required')
												->setButtonAddon('plus')
												->draw($show_input);
										?>
									</div>

									<div class = "col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Due Date')
													->setSplit('col-md-4', 'col-md-8')
													->setName('due_date')
													->setId('due_date')
													->setClass('datepicker-input')
													->setAttribute(array('readonly' => ''))
													->setAddon('calendar')
													->setValue($duedate)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>

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

									<div class = "col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Invoice No')
													->setSplit('col-md-4', 'col-md-8')
													->setName('invoiceno')
													->setId('invoiceno')
													->setValue($invoiceno)
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

									<div class = "col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Reference No')
													->setSplit('col-md-4', 'col-md-8')
													->setName('referenceno')
													->setId('referenceno')
													->setValue($referenceno)
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
								<br/>
								<div class="row">
									<div class = "col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Proforma')
												->setPlaceholder('Select Proforma')
												->setSplit('col-md-4', 'col-md-8')
												->setName('proformacode')
												->setId('proformacode')
												->setList($proforma_list)
												->setValue("")
												->setNone('None')
												->draw($show_input);
										?>
									</div>

									<div class="col-md-6 hidden">
										<?php
											echo $ui->formField('text')
													->setLabel('Exchange Rate')
													->setSplit('col-md-4', 'col-md-8')
													->setName('exchange_rate')
													->setId('exchange_rate')
													->setClass('text-right')
													->setValue($exchangerate)
													->draw($show_input);
										?>
									</div>
								</div>
							
								<div class = "row">
									<div class = "col-md-12">
										<?php
											echo $ui->formField('textarea')
													->setLabel('Notes')
													->setSplit('col-md-2', 'col-md-10')
													->setName('remarks')
													->setId('remarks')
													->setAttribute(
														array(
															'rows' => 4
														)
													)
													->setValue($particulars)
													->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
						<!--VOUCHER DETAILS : START-->
						<div class = "row">
							<div class="has-error col-md-12">
								<span id="detailAccountError" class="help-block hidden col-md-offset-1">
									<i class="glyphicon glyphicon-exclamation-sign"></i> 
									Please specify an account for the highlighted row(s).
								</span>
								<span id="detailAmountError" class="help-block hidden col-md-offset-1">
									<i class="glyphicon glyphicon-exclamation-sign"></i> 
									Please specify a debit or credit amount for the highlighted row(s). 
								</span>
								<span id="detailTotalError" class="help-block hidden col-md-offset-1">
									<i class="glyphicon glyphicon-exclamation-sign"></i> 
									Please make sure total debit and total credit are equal. 
								</span>
								<span id="detailEqualError" class="help-block hidden col-md-offset-1">
									<i class="glyphicon glyphicon-exclamation-sign"></i> 
									Please make sure that the total amount (<strong></strong>) is equal to both total debit or total credit. 
								</span>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="table-responsive">
								<table class="table table-hover table-condensed " id="itemsTable">
									<thead>
										<tr class="info">
											<th class="col-md-3 text-center">Account</th>
											<th class="col-md-4 text-center">Description</th>
											<th class="col-md-2 text-center">Debit</th>
											<th class="col-md-2 text-center">Credit</th>
											<?if($task != 'view'){?>
												<th class="col-md-1 center"></th>
											<?}?>
										</tr>
									</thead>
									<tbody>
										<?php
											if($task == 'create')
											{
												$accountcode 	   = '';
												$detailparticulars = array('');
												$foreignamount	   = '0.00';
												$debit 			   = '0.00';
												$credit 		   = '0.00';
											
												$row 			   = 1;
												$total_debit 	   = 0;
												$total_credit 	   = 0;
												$startnumber 	   = ($row_ctr == 0) ? 1: $row_ctr;
										?>
												<tr class="clone" valign="middle">
													<td class = "remove-margin">
														<?php
															echo $ui->formField('dropdown')
																->setPlaceholder('Select One')
																->setSplit('', 'col-md-12')
																->setName("accountcode[".$row."]")
																->setId("accountcode[".$row."]")
																->setList($account_entry_list)
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
																	->setName('debit['.$row.']')
																	->setId('debit['.$row.']')
																	->setAttribute(array("maxlength" => "20"))
																	->setClass("format_values_db format_values text-right")
																	->setValue($debit)
																	->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
															echo $ui->formField('text')
																	->setSplit('', 'col-md-12')
																	->setName('credit['.$row.']')
																	->setId('credit['.$row.']')
																	->setAttribute(array("maxlength" => "20"))
																	->setClass("format_values_cr format_values text-right")
																	->setValue($credit)
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
												<tr class="clone" valign="middle">
													<td class = "remove-margin">
														<?php
															echo $ui->formField('dropdown')
																	->setPlaceholder('Select One')
																	->setSplit('', 'col-md-12')
																	->setName("accountcode[".$row."]")
																	->setId("accountcode[".$row."]")
																	->setList($account_entry_list)
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
																	->setName('debit['.$row.']')
																	->setId('debit['.$row.']')
																	->setAttribute(array("maxlength" => "20"))
																	->setClass("format_values_db format_values text-right")
																	->setValue($debit)
																	->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
															echo $ui->formField('text')
																	->setSplit('', 'col-md-12')
																	->setName('credit['.$row.']')
																	->setId('credit['.$row.']')
																	->setAttribute(array("maxlength" => "20"))
																	->setClass("format_values_cr format_values text-right")
																	->setValue($credit)
																	->draw($show_input);
														?>
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
													</td>			
												</tr>
										<?
											}else if(!empty($sid) && $task!='create'){
												$row 			= 1;
												$total_debit 	= 0;
												$total_credit 	= 0;

												
												for($i = 0; $i < count($data["details"]); $i++)
												{
													$accountlevel		= $data["details"][$i]->accountcode;
													$accountname		= $data["details"][$i]->accountname;
													$accountcode		= ($task != 'view') ? $accountlevel : $accountname;
													$detailparticulars	= $data["details"][$i]->detailparticulars;
													$debit				= $data["details"][$i]->debit;
													$credit				= $data["details"][$i]->credit;
													$debit_attr			= array();
													$credit_attr		= array();

													$debit_attr['maxlength'] 	= 20;
													$credit_attr['maxlength'] 	= 20;
													if($credit > 0 && $debit == 0){
														$debit_attr['readOnly'] 	= "readOnly";
													}else if($debit > 0 && $credit == 0){
														$credit_attr['readOnly'] 	= "readOnly";
													}
											?>	
													<tr class="clone" valign="middle">
														<td class = "remove-margin">
															<?php
																echo $ui->formField('dropdown')
																		->setPlaceholder('Select One')
																		->setSplit('', 'col-md-12')
																		->setName("accountcode[".$row."]")
																		->setId("accountcode[".$row."]")
																		->setList($account_entry_list)
																		->setValue($accountcode)
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
																		->setValue($detailparticulars)
																		->draw($show_input);
															?>
														</td>
														<td class = "remove-margin">
															<?php
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12')
																		->setName('debit['.$row.']')
																		->setId('debit['.$row.']')
																		->setAttribute($debit_attr)
																		->setClass("format_values_db format_values text-right")
																		->setValue(number_format($debit,2))
																		->draw($show_input);
															?>
														</td>
														<td class = "remove-margin">
															<?php
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12')
																		->setName('credit['.$row.']')
																		->setId('credit['.$row.']')
																		->setAttribute($credit_attr)
																		->setClass("format_values_cr format_values text-right")
																		->setValue(number_format($credit,2))
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
													$total_debit += $debit;
													$total_credit += $credit;
													$row++;	
												}
											}
										?>
									</tbody>
									<tfoot>
										<tr>
											<td>
												<? if($task != 'view') { ?>
													<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
												<? } ?>
											</td>	
										</tr>	
										<tr id="total">
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<label class="control-label col-md-12">Total</label>
											</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('total_debit')
															->setId('total_debit')
															->setClass("input_label text-right")
															->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
															->setValue(number_format($total_debit,2))
															->draw($show_input);
												?>
											</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('total_credit')
															->setId('total_credit')
															->setClass("input_label text-right")
															->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
															->setValue(number_format($total_credit,2))
															->draw($show_input);
												?>
											</td>
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
										</tr>	
									</tfoot>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 col-sm-12 text-center">
								<?php
									$save		= ($task == 'create') ? 'name="save"' : '';
									$save_new	= ($task == 'create') ? 'name="save_new"' : '';
								?>
								<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
								<div class="btn-group" id="save_group">
									
									<input type = "button" value = "Save" name = "save" id = "btnSave" class="btn btn-primary btn-sm btn-flat"/>
									<input type = "hidden" value = "" name = "h_save" id = "h_save"/>

									<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm btn-flat" data-toggle="dropdown">
										<span class="caret"></span>
									</button>
									
									<ul class="dropdown-menu left" role="menu">
										<li id = "save_new" style="cursor:pointer;">
											<!--<input type = "button" value = "Save & New" name = "save_new" id = "save_new" class = "btn btn-default btn-sm btn-flat no-bg"/>-->
											Save & New
											<input type = "hidden" value = "" name = "h_save_new" id = "h_save_new"/>
										</li>
										<li class="divider"></li>
										<li id = "save_preview" style="cursor:pointer;">
											<!--<input type = "button" value = "Save & Preview" name = "save_preview" id = "save_preview" class = "btn btn-default btn-sm btn-flat no-bg"/>-->
											Save & Preview
											<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_preview"/>
										</li>
									</ul>
							
								</div>
								&nbsp;&nbsp;&nbsp;
								<div class="btn-group">
									<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" id="btnCancel">Cancel</button>
								</div>
							</div>
						</div>
						<div class = "col-md-12">&nbsp;</div>
					</div>	
				</form>
			</div>
		</div>
	<? } ?>
    
</section>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Add a Customer
				<!--<h4>Add a Customer
				<button type="button" class="close" data-dismiss="modal">&times;</button></h4>-->
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="newCustomer" autocomplete="off">
					<input class = "form_iput" value = "newCustomer" name = "h_form" id = "h_form" type="hidden">
					<input class = "form_iput" value = "insert" name = "h_querytype" id="h_querytype" type="hidden">
					<div class="alert alert-warning alert-dismissable hidden" id="vendorAlert">
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
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Customer Name: <span class="asterisk"> * </span>')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('customer_name')
										->setId('customer_name')
										->setAttribute(array("maxlength" => "255"))
										->setValue("")
										->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "vendor_name_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
										->setLabel('Address: <span class = "asterisk">*</span>')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('address')
										->setId('address')
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
										->setAttribute(array("maxlength" => "15"))
										->setPlaceHolder("000-000-000-000")
										->setValue("")
										->draw($show_input);
								// <input class="form-control tin-input" maxlength="15" value="" name="tinno" id="tinno" placeholder="000-000-000-000" onkeyup="validateTIN('newCustomer','tinno',this.value);" onblur="validateTIN('newCustomer','tinno',this.value);" onkeypress="return isNumberKey(event,45);" type="text">
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
										<button type="button" class="btn btn-primary btn-flat" id="customerBtnSave">Save</button>
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
<!-- End Customer Modal -->

<!-- Exchange Rate Modal -->
<div class="modal fade" id="rateModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Exchange Rate
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="rateForm">
					<div class="alert alert-warning alert-dismissable hidden" id="sequenceAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class="well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Amount: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('oldamount')
										->setId('oldamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20"))
										->setPlaceHolder("0.00")
										->setValue("")
										->draw($show_input);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "oldamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
							
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Rate: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('rate')
										->setId('rate')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "9"))
										->setPlaceHolder("0.00")
										->setValue("")
										->draw($show_input);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "rate_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Amount: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('newamount')
										->setId('newamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20"))
										->setPlaceHolder("0.00")
										->setValue("")
										->draw($show_input);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "newamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Accounting Entry:')
									->setPlaceholder('Filter Accounting Entry')
									->setSplit('col-md-4', 'col-md-7')
									->setName('defaultaccount')
									->setId('defaultaccount')
									->setList($account_entry_list)
									->setValue("")
									->draw($show_input);
							?>
						</div>
					</div>
					<div class="row row-dense">
						<div class="col-md-12 text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnProceed" >Apply</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<a href="javascript:void(0);" class="btn btn-small btn-default btn-flat" role="button" data-dismiss="modal" style="outline:none;">
									Cancel
								</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Exchange Rate Modal -->

<!-- Delete Record Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this line?
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
<div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to cancel?
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

<!--PAYMENT EXCHANGE RATE MODAL-->
<div class="modal fade" id="paymentRateModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Exchange Rate
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="paymentRateForm">
					<div class="alert alert-warning alert-dismissable hidden" id="sequenceAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class="well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Amount: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('paymentoldamount')
										->setId('paymentoldamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20"))
										->setPlaceHolder("0.00")
										->setValue(number_format(0,2))
										->draw(true);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "paymentoldamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Rate: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('paymentrate')
										->setId('paymentrate')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "9"))
										->setPlaceHolder("0.00")
										->setValue(number_format($v_exchangerate, 2))
										->draw(true);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "paymentrate_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Amount: <span class = "asterisk">*</span>')
										->setSplit('col-md-4', 'col-md-7 field_col')
										->setName('paymentnewamount')
										->setId('paymentnewamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20"))
										->setPlaceHolder("0.00")
										->setValue(number_format(0,2))
										->draw(true);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "paymentnewamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>

							<span class="help-block hidden small req-color" id = "exrateamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i>Amount Exceeded Total Balance</span>

						</div>
					</div>

					<div class="row row-dense">
						<div class="col-md-12 text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnProceed" >Apply</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<a href="javascript:void(0);" class="btn btn-small btn-default btn-flat" role="button" data-dismiss="modal" style="outline:none;">
									Cancel
								</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End PAYMENT EXCHANGE RATE MODAL-->

<script>
function addCustomerToDropdown() 
{
	var optionvalue = $("#customer_modal #supplierForm #partnercode").val();
	var optiondesc 	= $("#customer_modal #supplierForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#receivableForm #customer option");
	$('#receivableForm #customer').val(optionvalue);
	
	getPartnerInfo(optionvalue);

	$('#customer_modal').modal('hide');
	$('#customer_modal').find("input[type=text], textarea, select").val("");
}

function closeModal()
{
	$('#customer_modal').modal('hide');
}
$('#customer_button').click(function() 
{
	$('#customer_modal').modal('show');
});
</script>
<?php
	echo $ui->loadElement('modal')
		->setId('customer_modal')
		->setContent('maintenance/supplier/create')
		->setHeader('Add a Customer')
		->draw();
?>

<script>
var ajax = {};

function computeDueDate()
{
	var invoice = $("#document_date").val();
	
	var terms 	= $("#customer_terms").val();
	
	if(invoice != '')
	{
		var newDate	= moment(invoice).add(terms, 'days').format("MMM DD, YYYY");
		$("#due_date").val(newDate);
	}
}

function getPartnerInfo(code)
{
	if(code == '' || code == 'add')
	{
		$("#customer_tin").val("");
		$("#customer_terms").val("");
		$("#customer_address").val("");

		computeDueDate();
	}
	else
	{
		$.post('<?=BASE_URL?>financials/accounts_receivable/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
		{
			var address		= data.address.trim();
			var tinno		= (data.tinno != null ) ? data.tinno.trim() : "000-000-000-000";
			var terms		= data.terms.trim();
			
			$("#customer_tin").val(tinno);
			$("#customer_terms").val(terms);
			$("#customer_address").val(address);

			computeDueDate();
		});
	}
}

function addNewModal(type,val,row)
{
	row 		= row.replace(/[a-z]/g, '');
	
	if(val == 'add')
	{
		if(type == 'Customer')
		{
			$('#customerModal').modal();
			$('#customer').val('');
		}
	}
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

/**
* This function toggles the field for getting the foreign amount
* @param  {float} rate - exchange rate to be used to compute the converted amount
*/
function toggleExchangeRate(tp)
{
	tp = typeof tp !== 'undefined' ? tp : '';

	if(tp == '')
	{
		var amount 				= $('#receivableForm #h_amount').val();
		var exchangerate 		= $('#receivableForm #h_exchangerate').val();
		var convertedamount 	= $('#receivableForm #h_convertedamount').val();

		var oldamount 			= amount * 1;
		var rate 				= exchangerate * 1;
		var newamount 			= convertedamount * 1;

		$('#rateForm #oldamount').val(addCommas(oldamount.toFixed(2)));
		$('#rateForm #rate').val(addCommas(rate.toFixed(2)));
		$('#rateForm #newamount').val(addCommas(newamount.toFixed(2)));

		$('#rateModal').modal('toggle');
	}
	else
	{
		var amount 				= $('#receiptForm #paymentamount\\[1\\]').val();
		var exchangerate 		= $('#receiptForm #exchangerate\\[1\\]').val();
		var convertedamount 	= $('#receiptForm #convertedamount\\[1\\]').val();

		amount 					= amount.replace(/,/g,'');
		exchangerate 			= exchangerate.replace(/,/g,'');
		convertedamount 		= convertedamount.replace(/,/g,'');

		var oldamount 	= amount * 1;
		var rate 		= exchangerate * 1;
		var newamount 	= convertedamount * 1;

		$('#paymentRateForm #paymentoldamount').val(addCommas(oldamount.toFixed(2)));
		$('#paymentRateForm #paymentrate').val(addCommas(rate.toFixed(2)));
		$('#paymentRateForm #paymentnewamount').val(addCommas(newamount.toFixed(2)));

		computeExchangeRate('paymentRateForm','paymentnewamount');

		$('#receiptForm #paymentamount\\[1\\]').val($('#paymentRateForm #paymentoldamount').val());

		$('#paymentRateModal').modal('toggle');
	}
}

/**RESET IDS OF ROWS**/
function resetIds()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.rows.length - 3;
	
	x = 1;
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'accountcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'debit['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'credit['+x+']';
		
		row.cells[0].getElementsByTagName("select")[0].name = 'accountcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'debit['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'credit['+x+']';
		
		row.cells[4].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

		x++;
	}
	
}

function resetChequeIds()
{
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	x = 1;
	for(var i = 1;i<=count;i++)
	{
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'chequeamount['+x+']';
		row.cells[4].getElementsByTagName("input")[0].id 	= 'chequeconvertedamount['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'chequeamount['+x+']';
		row.cells[4].getElementsByTagName("input")[0].name 	= 'chequeconvertedamount['+x+']';
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); computeExchangeRate(\'paymentRateForm\',\'chequeamount['+x+']\',\''+x+'\');');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');

		row.cells[3].getElementsByTagName("input")[0].classList.remove("chequeamount"); 
		
		row.cells[4].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeconvertedamount['+x+']\'); computeExchangeRate(\'paymentRateForm\',\'chequeamount['+x+']\',\''+x+'\');');
		row.cells[4].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeconvertedamount['+x+']\')');

		// row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequePrint('+x+')');
		row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequeDelete('+x+')');
		x++;
	}
	
}

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero()
{
	resetIds();
	
	var table 		= document.getElementById('itemsTable');
	var newid 		= table.rows.length - 3;
	var account		= document.getElementById('accountcode['+newid+']');

	if(document.getElementById('accountcode['+newid+']') != null)
	{
		document.getElementById('accountcode['+newid+']').value 		= '';
		document.getElementById('detailparticulars['+newid+']').value 	= '';
		document.getElementById('debit['+newid+']').value 				= '0.00';
		document.getElementById('credit['+newid+']').value 				= '0.00';
	
		document.getElementById('debit['+newid+']').readOnly 			= false;
		document.getElementById('credit['+newid+']').readOnly 			= false;
	}
}

function setChequeZero()
{
	resetChequeIds();
	
	var table 		= document.getElementById('chequeTable');
	var newid 		= table.rows.length - 2;
	var account		= document.getElementById('chequeaccount['+newid+']');
	
	if(document.getElementById('chequeaccount['+newid+']')!=null)
	{
		document.getElementById('chequeaccount['+newid+']').value 			= '';
		document.getElementById('chequenumber['+newid+']').value 			= '';
		document.getElementById('chequeamount['+newid+']').value 			= '0.00';
		document.getElementById('chequeconvertedamount['+newid+']').value 	= '0.00';
	}
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

	if((field == '' || parseFloat(field) == 0) || help_block == "exrateamount_help" )
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');
		
		$("#"+form+" #"+help_block)
			.removeClass('hidden');
			
		// if($("#"+form+" #"+id).parent().next(".help-block")[0])
		// {
		// 	$("#"+form+" #"+id)
		// 	.parent()
		// 	.next(".help-block")
		// 	.removeClass('hidden');
		// }

		if($("#"+form+" .row-dense").next(".help-block")[0])
		{
			$("#"+form+" #"+help_block)
			// .parent()
			// .next(".help-block")
			.removeClass('hidden');
		}

		return 1;
	}
	else
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+help_block)
			.addClass('hidden');
			
		// if($("#"+form+" #"+id).parent().next(".help-block")[0])
		// {
		// 	$("#"+form+" #"+id)
		// 	.parent()
		// 	.next(".help-block")
		// 	.addClass('hidden');
		// }

		if($("#"+form+" .row-dense").next(".help-block")[0])
		{
			$("#"+form+" #"+help_block)
			// .parent()
			// .next(".help-block")
			.removeClass('hidden');
		}

		return 0;
	}
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

/**SAVE CHANGES ON EDITED FIELDS**/
function saveField(id)
{		
	var field		= document.getElementById(id);
	var vustomer		= document.getElementById('customer').value;

	document.getElementById('h_address1').value 	= document.getElementById('customer_address').value;
	document.getElementById('h_tinno').value 		= document.getElementById('customer_tin').value;
	document.getElementById('h_terms').value 		= document.getElementById('customer_terms').value;
	document.getElementById('h_condition').value 	= customer;
	
	if(field.readOnly == false)
	{
		$.post('<?=BASE_URL?>financials/accounts_receivable/ajax/save_data', $("#customerDetailForm").serialize(), function(data) 
		{
			if(data.msg == "success")
			{
				field.readOnly 				= true;
				field.style.backgroundColor	= 'transparent';
			}
		});
	}	
}

/**HIGHTLIGHT CONTENT OF INPUT**/
function SelectAll(id)
{
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

/**ENABLE EDITABLE FIELDS**/
function editField(id)
{
	var field	= document.getElementById(id);
	field.readOnly 				= false;
	field.style.backgroundColor	= '#ffffff';
	
	SelectAll(id);
}

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

/**COMPUTE TOTAL CHEQUE AMOUNT**/
function addAmounts() 
{
	var subconverted= 0;
	var subtotal 	= 0;
	
	var subData 			= 0;
	var subDataConverted	= 0;
	
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	for(i = 1; i <= count; i++) 
	{  
		var inputamt		= document.getElementById('chequeamount['+i+']');
		var convertedamt	= document.getElementById('chequeconvertedamount['+i+']');
		
		if(document.getElementById('chequeamount['+i+']')!=null)
		{          
			if(inputamt.value && inputamt.value != '0' && inputamt.value != '0.00')
			{                            
				subData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				subData = 0;
			}

			if(convertedamt.value && convertedamt.value != '0' && convertedamt.value != '0.00')
			{                            
				subDataConverted = convertedamt.value.replace(/,/g,'');
			}
			else
			{             
				subDataConverted = 0;
			}

			subtotal 	= parseFloat(subtotal) + parseFloat(subData);
			subconverted= parseFloat(subconverted) + parseFloat(subDataConverted);
		}	
	}

	subtotal	 = Math.round(1000*subtotal)/1000;
	subconverted = Math.round(1000*subconverted)/1000;

	document.getElementById('total').value 					= addCommas(subtotal.toFixed(2));
	document.getElementById('total_converted').value 		= addCommas(subconverted.toFixed(2));

	document.getElementById('paymentamount[1]').value 			= addCommas(subtotal.toFixed(2));
	document.getElementById('convertedamount[1]').value 		= addCommas(subconverted.toFixed(2));
}

/**
* This computes the converted amount based on the exchange rate and foreign amount
*/
function computeExchangeRate(activeForm,active,row)
{
	row = typeof row !== 'undefined' ? row : '';

	// console.log("form: " + activeForm + " active: " + active);

	if(row == '')
	{
		// console.log("1");
		if(activeForm == 'paymentRateForm')
		{
			var amount 	= $('#'+activeForm+' #paymentoldamount').val();
			amount 		= amount.replace(/,/g,'');
			var rate 	= $('#'+activeForm+' #paymentrate').val();
			rate 		= rate.replace(/,/g,'');
			var base 	= $('#'+activeForm+' #paymentnewamount').val();
			base 		= base.replace(/,/g,'');

			var newamount = 0;

			// console.log( "\n a: " + amount + " r: " + rate + " b: " + base);

			if(parseFloat($('#'+activeForm+' #paymentrate').val()) >= 1)
			{
				if(active == 'paymentoldamount' && parseFloat(base) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					console.log("\n1");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentoldamount' && parseFloat(rate) > 0)
				{
					console.log("\n2");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'paymentrate' && parseFloat(amount) > 0)
				{
					console.log("\n3");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentrate' && parseFloat(rate) > 0)
				{
					console.log("\n4");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentnewamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					console.log("\n5");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentnewamount' && parseFloat(rate) > 0)
				{
					console.log("\n6");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
				}
			}
			else
			{
				console.log("sdsdsdsd");
				$('#'+activeForm+' #convertedamount').val('0.00');
				$('#'+activeForm+' #exchangerate').val('1.00');
				$('#'+activeForm+' #amount').val('0.00');
			}
		}
		else
		{
			// console.log("else");
			var amount 	= $('#'+activeForm+' #oldamount').val();
			amount 		= amount.replace(/,/g,'');
			var rate 	= $('#'+activeForm+' #rate').val();
			rate 		= rate.replace(/,/g,'');
			var base 	= $('#'+activeForm+' #newamount').val();
			base 		= base.replace(/,/g,'');

			var newamount = 0;

			if(parseFloat($('#'+activeForm+' #rate').val()) > 1)
			{
				if(active == 'oldamount' && parseFloat(base) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'oldamount' && parseFloat(rate) > 0)
				{
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'rate' && parseFloat(amount) > 0)
				{
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'rate' && parseFloat(rate) > 0)
				{
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'newamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'newamount' && parseFloat(rate) > 0)
				{
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));
				}
			}
			else
			{
				$('#'+activeForm+' #convertedamount').val('0.00');
				$('#'+activeForm+' #exchangerate').val('1.00');
				$('#'+activeForm+' #amount').val('0.00');
			}
		}
	}
	else
	{
		var amount 	= $('#receiptForm #chequeamount\\['+row+'\\]').val();
		amount 		= amount.replace(/,/g,'');
		var rate 	= $('#'+activeForm+' #paymentrate').val();
		rate 		= rate.replace(/,/g,'');
		var base 	= $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val();
		base 		= base.replace(/,/g,'');
		
		var newamount = 0;

		// console.log("a: " + amount + " r: " + rate + " b: " + base );

		// if(active == 'chequeamount['+row+']' && parseFloat(base) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))

		if(active == 'chequeamount['+row+']' && parseFloat(base) > 0 && (parseFloat(rate) == 0))
		{
			// console.log("1");
			newamount = parseFloat(base) / parseFloat(amount);
			$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

			// console.log("newa: " + newamount);
			// console.log("payrate: " + $("#" + activeForm + " #paymentrate").val());
		}
		else if(active == 'chequeamount['+row+']' && parseFloat(rate) > 0)
		{
			// console.log("\n 2");
			newamount = parseFloat(amount) * parseFloat(rate);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	

			// console.log("newa: " + newamount);
			// console.log("chequeconvertedamount: " + $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val());
		}
		else if(active == 'paymentrate' && parseFloat(amount) > 0)
		{
			// console.log("\n 3");
			newamount = parseFloat(amount) * parseFloat(rate);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));

			// console.log("newa: " + newamount);
			// console.log("chequeconvertedamount: " + $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val());
		}
		else if(active == 'paymentrate' && parseFloat(rate) > 0)
		{
			// console.log("\n 4");
			newamount = parseFloat(base) / parseFloat(rate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	

			// console.log("newa: " + newamount);
			// console.log("chequeamount: " + $('#receiptForm #chequeamount\\['+row+'\\]').val());
		}
		else if(active == 'chequeconvertedamount['+row+']' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
		{
			// console.log("\n 5");
			newamount = parseFloat(base) / parseFloat(amount);
			$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

			// console.log("newa: " + newamount);
			// console.log("payrate: " + $('#'+activeForm+' #paymentrate').val());

		}
		else if(active == 'chequeconvertedamount['+row+']' && parseFloat(rate) > 0)
		{
			// console.log("\n 6");
			newamount = parseFloat(base) / parseFloat(rate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	

			// console.log("newa: " + newamount);
			// console.log("chequeamount: " + $('#receiptForm #chequeamount\\['+row+'\\]').val());
		}

		addAmounts();
	}
}

function addAmountAll(field) 
{
	var sum    = 0;       
	var valid  = true;
	var inData = 0;
	
	var chk	   = document.getElementsByName('chk[]');

	if(field == 'debit')
	{
		notfield	= 'credit';
	}
	else
	{
		notfield	= 'debit';
	}
	
	for(i =0; i <= chk.length; i++) 
	{  
		var inputs 		= document.getElementById(field+'['+i+']');
		var disables 	= document.getElementById(notfield+'['+i+']');
		
		if(document.getElementById(notfield+'['+i+']')!=null)
		{          
			if(inputs.value && inputs.value != '0' && inputs.value != '0.00')
			{                            
				inData = inputs.value.replace(/,/g,'');
				disables.readOnly = true;
			}
			else
			{             
				inData = 0;
				disables.readOnly = false;
			}                  
			sum = parseFloat(sum) + parseFloat(inData);
		}	
	}
			
	if(field == 'debit')
	{
		document.getElementById('total_debit').value = addCommas(sum.toFixed(2));
	}
	else
	{
		document.getElementById('total_credit').value = addCommas(sum.toFixed(2));
	}
}

function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}

function confirmChequeDelete(row)
{
	var table 		= document.getElementById('chequeTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;
	var rowindex	= table.rows[row];
	
	if($('#chequeaccount\\['+row+'\\]').val() != '')
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();
		}
		else
		{	
			document.getElementById('chequeaccount['+row+']').value 		= '';
			$('#chequeaccount\\['+row+'\\]').val('');

			// $(destino).val("x").trigger("change")
			$('#chequeaccount\\['+row+'\\]').trigger("change");
			
			document.getElementById('chequenumber['+row+']').value 			= '';
			document.getElementById('chequedate['+row+']').value 			= '<?= $date ?>';
			document.getElementById('chequeamount['+row+']').value 			= '0.00';
			document.getElementById('chequeconvertedamount['+row+']').value = '0.00';
			
			addAmounts();
		}
	}
	else
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();
		}
		else
		{
			document.getElementById('chequeaccount['+row+']').value 		= '';
			$('#chequeaccount\\['+row+'\\]').val('');
			$('#chequeaccount\\['+row+'\\]').trigger("change");
			document.getElementById('chequenumber['+row+']').value 			= '';
			document.getElementById('chequedate['+row+']').value 			= '<?= $date ?>';
			document.getElementById('chequeamount['+row+']').value 			= '0.00';
			document.getElementById('chequeconvertedamount['+row+']').value = '0.00';
			addAmounts();
		}
	}
}

function deleteItem(row)
{

	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= '<?= COMPANYCODE ?>';
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;

	var rowindex	= table.rows[row];
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'ar_details';
		var condition	= " linenum = '"+index+"' AND voucherno = '"+voucher+"'";
		// var condition	= " linenum = '"+index+"' AND voucherno = '"+voucher+"' AND companycode = '"+companycode+"' ";
		
		if(rowCount > 3)
		{
			if(task == 'create')
			{
				// console.log("1");
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/delete_row",{table:datatable,condition:condition})
				.done(function( data ) 
				{
					table.deleteRow(row);	
					resetIds();
					addAmountAll('debit');
					addAmountAll('credit');
				});
			}
			else
			{
				// console.log("2");
				table.deleteRow(row);	
				resetIds();
				addAmountAll('debit');
				addAmountAll('credit');
			}
		}
		else
		{	
			// console.log("else");
			resetIds();
			
			document.getElementById('accountcode['+row+']').value 			= '';
			document.getElementById('detailparticulars['+row+']').value 	= '';
			document.getElementById('debit['+row+']').value 				= '0.00';
			document.getElementById('credit['+row+']').value 				= '0.00';
			
			$('#accountcode\\['+row+'\\]').trigger('change');

			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
	else
	{
		// console.log("else 2");
		if(rowCount > 3)
		{
			table.deleteRow(row);	
			resetIds();
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
}

/**VALIDATE ITEM ROWS**/
function validateDetails()
{
	var table 			= document.getElementById('itemsTable');
	var total_debit 	= $('#total_debit').val();
	var total_credit 	= $('#total_credit').val();
	total_debit 		= total_debit.replace(/\,/g,'');
	total_credit 		= total_credit.replace(/\,/g,'');
	
	/**
	* Validate if total debit / credit is equal to the total amount specified
	*/
	var total_amount	= $('#receivableForm #h_convertedamount').val();
	total_amount 		= total_amount.replace(/\,/g,'');
	var newtotal_amount = total_amount * 1;

	count				= table.rows.length - 3;
	var valid1			= 0;
	var valid2			= 0;
	var valid3			= 0;
	
	if(valid1 == 0)
	{
		for(var i=1;i<=count;i++)
		{
			var accountcode = document.getElementById('accountcode['+i+']').value;
			var debit 		= document.getElementById('debit['+i+']').value;
			var credit 		= document.getElementById('credit['+i+']').value;
		
			if(accountcode == '')
			{
				$("#receivableForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid1++;
			}
			else
			{
				$("#receivableForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
			
			if(parseFloat(debit) == 0 && parseFloat(credit) == 0)
			{
				$("#receivableForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid2++;
			}
			else
			{
				$("#receivableForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
		}
		
		if(valid1 > 0)
		{
			$("#receivableForm #detailAccountError").removeClass('hidden');
		}
		else
		{
			$("#receivableForm #detailAccountError").addClass('hidden');
		}
		
		if(valid2 > 0)
		{
			$("#receivableForm #detailAmountError").removeClass('hidden');
		}
		else
		{
			$("#receivableForm #detailAmountError").addClass('hidden');
		}
		
		if(parseFloat(total_debit) != parseFloat(total_credit))
		{
			$("#receivableForm #detailTotalError").removeClass('hidden');
			valid1 = 1;
		}
		else
		{
			$("#receivableForm #detailTotalError").addClass('hidden');

			if(parseFloat(total_amount) > 0)
			{
				if(parseFloat(total_amount) != parseFloat(total_debit))
				{
					$("#receivableForm #detailEqualError strong").html(addCommas(newtotal_amount.toFixed(2)));
					$("#receivableForm #detailEqualError").removeClass('hidden');
					valid1 = 1;
				}
				else
				{
					$("#receivableForm #detailEqualError strong").html('');
					$("#receivableForm #detailEqualError").addClass('hidden');
				}
			}
		}
	}
 
	if(valid1 > 0 || valid2 > 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

/**CANCEL TRANSACTION**/
function cancelTransaction(vno)
{
	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= "<?= COMPANYCODE ?>";
	
	var datatable	= 'accountsreceivable';
	var detailtable	= 'ar_details';
	var condition	= " voucherno = '"+vno+"' AND stat = 'temporary' ";

	if(task == 'create')
	{	
		var data	= "table="+datatable+"&condition="+condition;
		var data2	= "table="+detailtable+"&condition="+condition;

		$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/delete_row",data)
		.done(function(data1) 
		{
			if(data1.msg == "success")
			{
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/delete_row",data2)
				.done(function(data2) 
				{
	
					if(data2.msg == "success")
					{
						window.location.href = '<?=BASE_URL?>financials/accounts_receivable';
					}
				});
			}
		});
	}
	else
	{
		window.location.href	= "<?=BASE_URL?>financials/accounts_receivable";
	}
}

/**TOGGLE CHECK DATE FIELD**/
function toggleCheckInfo(val)
{	
	if(val == 'cheque')
	{
		$("#receiptForm #check_field").addClass('hidden');
		$("#receiptForm #cash_payment_details").addClass('hidden');
		$("#receiptForm #check_details").removeClass('hidden');
	}
	else
	{
		$("#receiptForm #check_field").removeClass('hidden');
		$("#receiptForm #payment_field").addClass('hidden');
		
		$("#receiptForm #cash_payment_details").removeClass('hidden');
		$("#receiptForm #check_details").addClass('hidden');
	}
}

function clearInput(id)
{
	document.getElementById(id).value = '';
}


function validateCheck()
{
	// var mode	= $("#receiptForm #paymentmode\\[1\\]").val();
	
	// if(mode == 'cheque')
	// {
	// 	var checknum	= $("#receiptForm #paymentreference\\[1\\]").val();
	// 	var bankacct	= $("#receiptForm #paymentaccount\\[1\\]").val();

	// 	$.post('./ajax/validate_check.php',{ checkno : checknum, bankacct : bankacct, type : 'receipt' })
	// 	.done(function(data)
	// 	{
	// 		var resp	= data.split('|');
	// 		var code	= resp[0].trim();
		
	// 		if(code == 1)
	// 		{
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.closest('.field_col')
	// 				.addClass('has-error');
		
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.next(".help-block")
	// 				.html('<i class="glyphicon glyphicon-exclamation-sign"></i> Cheque number <b>['+checknum+']</b> already exists.');
			
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.next(".help-block")
	// 				.removeClass('hidden');
					
	// 			$("#valid").val(1);
	// 		}
	// 		else if(code == 0)
	// 		{
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.closest('.field_col')
	// 				.removeClass('has-error');
		
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.next(".help-block")
	// 				.html('<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.');
			
	// 			$("#receiptForm #paymentreference\\[1\\]")
	// 				.next(".help-block")
	// 				.addClass('hidden');
					
	// 			$("#valid").val(0);
	// 		}
	// 	});
	// }
}

function checkBalance()
{
	var payablerate			= $('#receiptForm #payablerate').val();
	var exchangerate		= $('#receiptForm #exchangerate\\[1\\]').val();
	payablerate				= payablerate.replace(/,/g,'');
	exchangerate			= exchangerate.replace(/,/g,'');

	var paymentrow			= $('#receiptForm #paymentrow').val();
	row 					= paymentrow.replace(/[a-z]/g, '');
	var paymentrowamount	= 0;
	var paymentapplied		= 0;
	var discountapplied		= 0;

	if(paymentrow != '')
	{
		var paymentapplied	= document.getElementById('paymentamount'+row).value;
		paymentapplied		= paymentapplied.replace(/,/g,'');
		var discountapplied	= document.getElementById('paymentdiscount'+row).value;
		discountapplied		= discountapplied.replace(/,/g,'');
		
		paymentrowamount	= parseFloat(paymentapplied) + parseFloat(discountapplied);
	}
	
	var paymentmode		= $('#receiptForm #paymentmode\\[1\\]').val();

	var payment			= $('#receiptForm #convertedamount\\[1\\]').val();
	payment				= payment.replace(/,/g,'');
	
	var prevpayment1 	= document.getElementById('prevpayment').value;
	prevpayment 		= prevpayment1.replace(/,/g,'');
	
	var totalInvoice 	= document.getElementById('totalInvoice').value;
	totalInvoice 		= totalInvoice.replace(/,/g,'');
	var totalPayment 	= document.getElementById('totalPayment').value;
	totalPayment 		= totalPayment.replace(/,/g,'');
	var totalDiscount 	= document.getElementById('totalDiscount').value;
	totalDiscount 		= totalDiscount.replace(/,/g,'');
	var totalForex 		= document.getElementById('totalForex').value;
	totalForex 			= totalForex.replace(/,/g,'');

	var default_payment	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	default_payment		= addCommas(default_payment.toFixed(2));;
	
	var discount 		= $('#receiptForm #paymentdiscount\\[1\\]').val();
	discount 			= discount.replace(/,/g,'');
	
	if(paymentrowamount)
	{
		var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(paymentrowamount) + parseFloat(totalForex);
	}
	else
	{
		var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	}
	
	/**Include discount in checking**/
	if(discount)
	{
		balance		 		= parseFloat(balance) - parseFloat(discount);
	}

	/**
	* Check if exchangerate is different from payable
	*/
	var forex_gain_loss	= false;
	if(parseFloat(totalForex) > 0 || (parseFloat(payablerate) != parseFloat(exchangerate)))
	{
		forex_gain_loss	= true;
	}

	var result 			= parseFloat(balance).toFixed(2);
	if((parseFloat(payment) > parseFloat(result)) && !forex_gain_loss)
	{
		bootbox.dialog({
			message: "Payment amount is greater the remaining balance of this Receivable.",
			title: "Warning",
			buttons: {
				success: {
					label: "OK",
					className: "btn-primary btn-flat",
					callback: function() {
						if(paymentmode == 'cheque')
						{
							document.getElementById('paymentdiscount[1]').value 	= '0.00';
						}
						else
						{
							if(prevpayment != '')
							{
								document.getElementById('convertedamount[1]').value 	= prevpayment;
								document.getElementById('paymentamountfield').value 	= prevpayment;
								document.getElementById('paymentdiscount[1]').value 	= '0.00';
							}
							else
							{
								document.getElementById('convertedamount[1]').value 	= default_payment;
								document.getElementById('paymentamountfield').value 	= default_payment;
								document.getElementById('paymentdiscount[1]').value 	= '0.00';
							}
						}
					}	
				}
			}
		});
		return 1;
	}else{
		return 0;
	}
}

function computeDiscount()
{
	var totalInvoice 	= document.getElementById('totalInvoice').value;
	totalInvoice 		= totalInvoice.replace(/,/g,'');
	var totalPayment 	= document.getElementById('totalPayment').value;
	totalPayment 		= totalPayment.replace(/,/g,'');
	var totalDiscount 	= document.getElementById('totalDiscount').value;
	totalDiscount 		= totalDiscount.replace(/,/g,'');
	var totalForex 		= document.getElementById('totalForex').value;
	totalForex 			= totalForex.replace(/,/g,'');
	
	var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);

	var paymentamount	= $('#receiptForm #convertedamount\\[1\\]').val();
	paymentamount    	= paymentamount.replace(/\,/g,'');
	var paymentdiscount	= $('#receiptForm #paymentdiscount\\[1\\]').val();
	paymentdiscount    	= paymentdiscount.replace(/\,/g,'');
	
	if(parseFloat(paymentdiscount) > 0)
	{
		if(parseFloat(paymentamount) > 0 && (parseFloat(paymentamount) == parseFloat(balance)))
		{
			var new_amount	= parseFloat(paymentamount) - parseFloat(paymentdiscount);

			if(new_amount >= 0)
			{
				new_amount		= addCommas(new_amount.toFixed(2));
		
				$('#receiptForm #convertedamount\\[1\\]').val(new_amount);
				$('#receiptForm	#paymentamountfield').val(new_amount);		
			}
			else
			{
				balance 		= addCommas(balance.toFixed(2));
				//alert('Payment amount and discount should be less than or equal to '+balance);
				bootbox.dialog({
					message: "Payment amount and discount should be less than or equal to "+balance,
					title: "Warning",
					buttons: {
						success: {
							label: "OK",
							className: "btn-primary btn-flat",
							callback: function() {
								$('#receiptForm #paymentdiscount\\[1\\]').val('0.00');
							}
						}
					}
				});
				
			}
		}
		checkBalance();
		
	}
	else
	{
		var new_amount	= parseFloat(paymentamount) - parseFloat(paymentdiscount);
		balance 		= addCommas(balance.toFixed(2));
	}
}

function savePaymentRow(e,id)
{
	e.preventDefault();
	
	id 				= id.replace(/[a-z]/g, '');
	var type		= document.getElementById('type').value;

	var table 			= document.getElementById('paymentsTable');
	var paymentmode 	= document.getElementById('paymentmode[1]').value;
	var paymentamount 	= document.getElementById('convertedamount[1]').value;
	paymentamount		= paymentamount.replace(/,/g,'');

	var paymentaccount 	= document.getElementById('paymentaccount[1]').value;

	var row 		= table.rows[id];
	var valid		= 0;
	
	/**validate payment fields**/
	// $("#receiptForm").find('.form-group').find('input, textarea, select').trigger('blur');
	valid		+= validateField('receiptForm','paymentmode\\['+id+'\\]', 'paymentmode\\['+id+'\\]_help');
	
	// validateField('receiptForm','paymentdate\\['+id+'\\]');
	//validateField('receiptForm','convertedamount\\['+id+'\\]', 'convertedamount\\['+id+'\\]_help');
	// valid		+= validateField('receiptForm','paymentmode\\['+id+'\\]', 'paymentmode\\['+id+'\\]_help');

	if(paymentmode == 'cash' || paymentmode == 'transfer')
	{
		if(parseFloat(Number(paymentamount)) > 0)
		{
			valid		+= validateField('receiptForm','paymentaccount\\['+id+'\\]', 'paymentaccount\\['+id+'\\]_help');
			//(paymentaccount == "") ? 1 : 0; 
		}
	}
	else
	{
		valid	+= validateCheques();
		valid	+= totalPaymentGreaterThanChequeAmount();
	}
	
	valid		+= checkBalance();

	if(valid == 0)
	{
		var invoiceno				= $('#receiptForm #invoiceno\\['+id+'\\]').val();
		var paymentamount			= $('#receiptForm #paymentamount\\['+id+'\\]').val();
		var exchangerate			= $('#receiptForm #exchangerate\\['+id+'\\]').val();
		var convertedamount			= $('#receiptForm #convertedamount\\['+id+'\\]').val();
		var paymentaccount			= $('#receiptForm #paymentaccount\\['+id+'\\]').val();
		var paymentdate				= $('#receiptForm #paymentdate\\['+id+'\\]').val();
		var paymentmode				= $('#receiptForm #paymentmode\\['+id+'\\]').val();
		var paymentreference		= $('#receiptForm #paymentreference\\['+id+'\\]').val();
		var paymentnotes			= $('#receiptForm #paymentnotes\\['+id+'\\]').val();
		var paymentvendor			= $('#receiptForm #customer\\['+id+'\\]').val();
		var paymentnumber			= $('#receiptForm #paymentnumber\\['+id+'\\]').val();
		var paymentdiscount			= $('#receiptForm #paymentdiscount\\['+id+'\\]').val();
		
		var selected 				= [];
		var selectedamount 			= [];
		var selectedrate 			= [];
		var selectedconverted 		= [];

		var selecteddate 			= [];
		var selectedaccount			= [];
		var selectedmode			= [];
		var selectedreference		= [];
		var selectednotes			= [];
		var selectedvendor			= [];
		var selectednumber			= [];
		var selecteddiscount		= [];
		
		var selectedcheque			= [];
		var selectedchequenumber	= [];
		var selectedchequedate		= [];
		var selectedchequeamount 	= [];
		var selectedchequeconvamount= [];
		
		selected.push(invoiceno);
		selectedamount.push(paymentamount);
		selectedrate.push(exchangerate);
		selectedconverted.push(convertedamount);

		selecteddate.push(paymentdate);
		
		if(paymentmode == 'cash' || paymentmode == 'transfer')
		{
			selectedaccount.push(paymentaccount);
		}
		selectedmode.push(paymentmode);
		selectedreference.push(paymentreference);
		
		selectednotes.push(paymentnotes);
		
		selectednumber.push(paymentnumber);
		selecteddiscount.push(paymentdiscount);
		
		/**Multiple Cheque payments**/
		var chequeTable		= document.getElementById('chequeTable');
		var chequeCount		= chequeTable.rows.length - 2;
		
		for(var j=1;j<=chequeCount;j++)
		{
			var chequeRow   = chequeTable.rows[j];
			
			if(document.getElementById('chequeaccount['+j+']').value != '')
			{
				var chequeaccount 			= document.getElementById('chequeaccount['+j+']').value;
				var chequenumber 			= document.getElementById('chequenumber['+j+']').value;
				var chequedate 				= document.getElementById('chequedate['+j+']').value;
				var chequeamount 			= document.getElementById('chequeamount['+j+']').value;
				var chequeconvertedamount 	= document.getElementById('chequeconvertedamount['+j+']').value;
				
				selectedcheque.push(chequeaccount);
				selectedchequenumber.push(chequenumber);
				selectedchequedate.push(chequedate);
				selectedchequeamount.push(chequeamount);
				selectedchequeconvamount.push(chequeconvertedamount);
			}
		}
	
		$.post("<?= BASE_URL ?>financials/accounts_receivable/ajax/apply_payments",
		{
			"invoiceno[]": selected, 
			"paymentdate[]": selecteddate, 
			"paymentnumber[]": selectednumber, 
			"paymentaccount[]": selectedaccount,
			"paymentmode[]": selectedmode,
			"paymentreference[]": selectedreference,
			"paymentamount[]": selectedamount,
			"paymentdiscount[]": selecteddiscount,
			"paymentnotes[]": selectednotes,
			"paymentrate[]": selectedrate,
			"paymentconverted[]": selectedconverted,
			"Customer[]": paymentvendor,
			"chequeaccount[]": selectedcheque,
			"chequenumber[]": selectedchequenumber,
			"chequedate[]": selectedchequedate,
			"chequeamount[]": selectedchequeamount,
			"chequeconvertedamount[]": selectedchequeconvamount
		}).done(function( data ) 
		{
			var hash 		= window.location.hash.substring(1);
			
			console.log("msg: " + data.msg);
			console.log("hash: " + hash);
			
			if(hash != '')
			{
				console.log("hash if");
				var url 				= document.URL;
				var newurl 				= url.replace('#payment','');
				document.location.href	= newurl;
			}
			else
			{
				console.log("hash else");
				location.reload();
			}
		});
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
			// var chequeaccount = $('#chequeaccount\\['+i+'\\]').chosen();

			if(chequeaccount != '')
			{
				selected++;
			}
		}
	}

	if(selected == 0 && (count > 0))
	{
		$("#receiptForm #chequeCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#receiptForm #chequeCountError").addClass('hidden');
	}
	
	if(valid == 0 && count > 0)
	{
		for(var i=1;i<=count;i++)
		{
			var chequeaccount 			= $('#chequeaccount\\['+i+'\\]').val(); //$('#chequeaccount\\['+i+'\\]').chosen().val();
			var chequenumber 			= $('#chequenumber\\['+i+'\\]').val();
			var chequedate 				= $('#chequedate\\['+i+'\\]').val();
			var chequeamount 			= $('#chequeamount\\['+i+'\\]').val();
			var chequeconvertedamount 	= $('#chequeamount\\['+i+'\\]').val();
			
			if(chequeaccount == '' || chequenumber == '' || chequedate == '' || parseFloat(chequeamount) <= 0 || chequeamount == '' || parseFloat(chequeconvertedamount) <= 0)
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
		$("#receiptForm #chequeAmountError").removeClass('hidden');
	}
	else
	{
		$("#receiptForm #chequeAmountError").addClass('hidden');
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
	var total_payment	= document.getElementById('paymentamount[1]').value;
	var total_cheque	= $('#receiptForm #total').val();
	
	$('#receiptForm #disp_tot_payment').html(total_payment);
	$('#receiptForm #disp_tot_cheque').html(total_cheque);
	
	total_payment    	= total_payment.replace(/\,/g,'');
	total_cheque    	= total_cheque.replace(/\,/g,'');

	if(parseFloat(total_payment) == parseFloat(total_cheque))
	{
		$("#receiptForm #paymentAmountError").addClass('hidden');
		return 0;
	}
	else
	{
		$("#receiptForm #paymentAmountError").removeClass('hidden');
		return 1;
	}
}

 /**LOAD CHEQUES**/
function loadCheques(i)
{
	var cheques 		= $('#paymentsTable #chequeInput'+i).val();

	if(cheques != '')
	{
		var arr_from_json 	= JSON.parse(cheques);
		var arr_len			= arr_from_json.length;

		var row		= 1;
		for(var x=0;x < arr_len;x++)
		{	
			var chequeaccount			= arr_from_json[x]['chequeaccount'];
			var chequenumber			= arr_from_json[x]['chequenumber'];
			var chequedate				= arr_from_json[x]['chequedate'];
			var chequeamount			= arr_from_json[x]['chequeamount'];
			var chequeconvertedamount	= arr_from_json[x]['chequeconvertedamount'];

			$('#receiptForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

			$('#receiptForm #chequenumber\\['+row+'\\]').val(chequenumber);
			$('#receiptForm #chequedate\\['+row+'\\]').val(chequedate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(chequeamount);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(chequeconvertedamount);

			/**Add new row based on number of rolls**/
			if(row != arr_len)
			{
				$('body .add-cheque').trigger('click');
			}
			$('#receiptForm #'+row).addClass('disabled');

			$('#receiptForm #checkprint\\['+row+'\\]').removeClass('hidden');
			row++;
		}
		addAmounts();
	}

	/**
	* Script to delete extra added lines
	*/
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;

	if(count > arr_len)

	for(j=count;j > arr_len;j--)
	{
		table.deleteRow(j);	
	}
}

/**EDIT RECIEVED PAYMENTS**/
function editPaymentRow(e,id)
{
	e.preventDefault();
	
	$("#receiptForm").removeClass('hidden');
	row 			= id.replace(/[a-z]/g, '');
	
	var paymentmode			= document.getElementById('paymentmode'+row).value;
	var paymentdate			= document.getElementById('paymentdate'+row).value;
	var paymentreference	= document.getElementById('paymentreference'+row).value;
	var paymentcheckdate	= document.getElementById('paymentcheckdate'+row).value;
	var paymentamount		= document.getElementById('paymentamount'+row).value;
	var paymentconverted	= document.getElementById('paymentconverted'+row).value;
	var paymentrate			= document.getElementById('paymentrate'+row).value;
	var paymentnumber		= document.getElementById('paymentnumber'+row).value;
	var paymentaccount		= document.getElementById('paymentaccount'+row).value;
	var paymentnotes		= document.getElementById('paymentnotes'+row).value;
	var paymentdiscount		= document.getElementById('paymentdiscount'+row).value;

	document.getElementById('paymentdate[1]').value			= paymentdate;
	$("#paymentmode\\[1\\]").val(paymentmode).trigger("change");
	document.getElementById('paymentreference[1]').value	= paymentreference;
	document.getElementById("convertedamount[1]").value		= paymentconverted;
	
	document.getElementById('exchangerate[1]').value		= paymentrate;
	document.getElementById('paymentamount[1]').value		= paymentamount;
	document.getElementById('paymentamountfield').value		= paymentconverted;
	
	document.getElementById('prevpayment').value			= paymentconverted;
	document.getElementById('paymentnumber[1]').value		= paymentnumber;
	
	$("#paymentaccount\\[1\\]").val(paymentaccount).trigger("change");
	document.getElementById('paymentnotes[1]').value		= paymentnotes;
	document.getElementById('paymentdiscount[1]').value		= paymentdiscount;
	
	document.getElementById('paymentrow').value				= id;
	
	$('#receiptForm #paymentexchangerate\\[1\\]').val(paymentrate);

	loadCheques(row);
	
	toggleCheckInfo(paymentmode);
	
	$('html, body').animate({ scrollTop: 0 }, 'slow');
}

/**COMPUTE TOTAL PAYMENTS**/
function addPayments() 
{
	var sum 		= 0;
	var total 		= 0;
	var inData 		= 0;
	
	var table 	= document.getElementById('paymentsTable');
	var count	= table.rows.length - 1;
	
	for(i = 1; i < count; i++) 
	{  
		var inputamt	= document.getElementById('paymentamount'+i);

		if(document.getElementById('paymentamount'+i)!=null)
		{          
			if(inputamt.value && inputamt != '0' && inputamt.value != '0.00')
			{                            
				inData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				inData = 0;
			}

			total = parseFloat(total) + parseFloat(inData);
		}	
	}

	total		= Math.round(1000*total)/1000;
	
	document.getElementById('totalPaymentCaption').innerHTML 	= addCommas(total.toFixed(2));
	document.getElementById('totalPayment').value 				= addCommas(total.toFixed(2));
}

/**RESET GENERATED ID OF PAYMENT ROWS**/
function resetPaymentRow()
{
	var table 	= document.getElementById('paymentsTable');
	var wtax 	= 'wtax';
	var count	= table.rows.length - 2;

	for(var x = 1; x <= count; x++)
	{
		var row = table.rows[x];
		
		row.cells[0].getElementsByTagName("input")[0].id 	= 'paymentdate'+x;
		row.cells[0].getElementsByTagName("input")[1].id 	= 'paymentnumber'+x;
		
		row.cells[1].getElementsByTagName("select")[0].id 	= 'paymentmode'+x;
		row.cells[2].getElementsByTagName("input")[0].id 	= 'paymentreference'+x;
		row.cells[2].getElementsByTagName("input")[1].id 	= 'paymentcheckdate'+x;
			
		if(wtax != '')
		{
			row.cells[3].getElementsByTagName("select")[0].id 	= 'paymentaccount'+x;
			
			row.cells[4].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[4].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[4].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;	
		}
		else
		{
			row.cells[3].getElementsByTagName("select")[0].id 	= 'paymentaccount'+x;
			
			row.cells[4].getElementsByTagName("select")[0].id 	= 'paymenttaxcode'+x;
			row.cells[5].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[5].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[5].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;
		}
		
		row.cells[0].getElementsByTagName("input")[0].name 	= '';
		row.cells[0].getElementsByTagName("input")[1].name 	= '';
		row.cells[1].getElementsByTagName("select")[0].name = '';
		row.cells[2].getElementsByTagName("input")[0].name 	= '';
		row.cells[2].getElementsByTagName("input")[1].name 	= '';
		
		if(wtax != '')
		{
			row.cells[3].getElementsByTagName("select")[0].name = '';
			row.cells[4].getElementsByTagName("input")[0].name 	= '';
			row.cells[4].getElementsByTagName("input")[1].name 	= '';
			row.cells[4].getElementsByTagName("input")[2].name 	= '';
		}
		else
		{
			row.cells[3].getElementsByTagName("select")[0].name = '';
			row.cells[4].getElementsByTagName("select")[0].name = '';
			row.cells[5].getElementsByTagName("input")[0].name 	= '';
			row.cells[5].getElementsByTagName("input")[1].name 	= '';
			row.cells[5].getElementsByTagName("input")[2].name 	= '';
		}
	}
}

/**CANCEL PAYMENT ROW**/
function deletePaymentRow(e,id)
{	
	e.preventDefault();
	row 			= id.replace(/[a-z]/g, '');
	var table 		= document.getElementById('paymentsTable');
	var count		= table.rows.length - 2;
	
	if(document.getElementById('paymentnumber'+row)!=null)
	{
		if(document.getElementById('paymentnumber'+row).value != '')
		{
			var voucher	= document.getElementById('paymentnumber'+row).value;
			var amount	= document.getElementById('paymentamount'+row).value;
			
			$('#deletePaymentModal').data('id', voucher);
			$('#deletePaymentModal').data('row', row);
			$('#deletePaymentModal').data('amount', amount);
			$('#deletePaymentModal').modal('show');
		}
		else
		{
			table.deleteRow(row);
		}
	}
	addPayments();
	resetPaymentRow();
}

/**CLEAR PAYMENT ROW**/
function clearPaymentRow(e)
{	
	e.preventDefault();
	
	clearInput('paymentmode[1]');
	clearInput('paymentreference[1]');
	clearInput('convertedamount[1]');
	clearInput('exchangerate[1]');
	clearInput('paymentamount[1]');
	clearInput('prevpayment');
	clearInput('paymentnumber[1]');
	clearInput('paymentaccount[1]');
	clearInput('paymentnotes[1]');
	clearInput('paymentdiscount[1]');
	toggleCheckInfo('');
}


function confirmChequePrint(row)
{
	var paymentvoucher 	= $('#receiptForm #paymentnumber\\[1\\]').val();
	var chequeno 		= $('#receiptForm #chequenumber\\['+row+'\\]').val();
	
	bootbox.dialog({
		message: "Please select one of the option to proceed.",
		title: "Print Cheque",
			buttons: {
			check: {
			label: "Cheque Only",
			className: "btn-primary btn-flat",
			callback: function(result) {
					var link 	 		= '<?= BASE_URL ?>financials/accounts_receivable/generateCheck/'+paymentvoucher+'/'+chequeno;
					// 'popups/generateCheck.php?sid='+paymentvoucher+'&cn='+chequeno;
					window.open(link);
				}
			},
			voucher: {
			label: "Cheque with Voucher",
			className: "btn-success btn-flat",
			callback: function(result) {
					var link 	 		= '<?= BASE_URL ?>financials/accounts_receivable/generateCheckVoucher/'+paymentvoucher+'/'+chequeno+'/rv';
					// 'popups/generateCheckVoucher.php?sid='+paymentvoucher+'&cn='+chequeno+'&type=rv';
					window.open(link);
				}
			},
			no: {
			label: "Cancel",
			className: "btn-default btn-flat",
			callback: function(result) {
					//alert(result);
				}
			}
		}
	});

}

function validateChequeNumber(id, value, n)
{
	id = id.replace(/[a-z\[\]]/g, '');
	
	$.post("<?=BASE_URL?>financials/payment/ajax/check", "chequevalue=" + value)
	.done(function(data)
	{
		if(data.success)
		{
			$(n).closest('.form-group').addClass('has-error');
			$("#chequeTable #chequenumber\\["+ id +"\\]").val("");

			$("#checkNumberError").removeClass("hidden");
		}
		else
		{
			$(n).closest('.form-group').removeClass('has-error');

			$("#checkNumberError").addClass("hidden");
		}
			
	});
}

$(document).ready(function() 
{
	// Get getPartnerInfo
	$( "#customer" ).change(function() 
	{
		$vendor_id = $("#customer").val();

		if( $vendor_id != "" )
  			getPartnerInfo($vendor_id);
	});

	// Call toggleExchangeRate
	// $( "#exchange_rate" ).click(function() 
	// {
	// 	toggleExchangeRate();
	// });

	// Add new Customer
	$("#newCustomer #customerBtnSave").click(function()
	{
		var valid	= 0;

		/**validate Customer fields**/
		valid		+= validateField('newCustomer','partnercode', "partnercode_help");
		valid		+= validateField('newCustomer','customer_name', "customer_name_help");
		valid		+= validateField('newCustomer','address', "address_help");
		valid		+= validateField('newCustomer','businesstype', "businesstype_help");

		if(valid == 0)
		{
			$.post('<?=BASE_URL?>financials/accounts_receivable/ajax/save_data', $("#newCustomer").serialize(), function(data) 
			{
				var optionvalue = $("#newCustomer #partnercode").val();
				var optiondesc 	= $("#newCustomer #customer_name").val();

				if(data.msg == "success")
				{
					$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#receivableForm #customer option:nth-child(4)");
					$('#receivableForm #customer').val(optionvalue);
					
					getPartnerInfo(optionvalue);

					$('#customerModal').modal('hide');
					$('#customerModal').find("input[type=text], textarea, select").val("");
					return true;
				}
				else
				{
					$("#vendorAlert p").html(data.msg);
					$("#vendorAlert").removeClass('hidden');
				}
			});
		}
	});

	// For adding new rol
	$('body').on('click', '.add-data', function() 
	{	
		$('#itemsTable tbody tr.clone select').select2('destroy');
		
		var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

		var ParentRow = $("#itemsTable tbody tr.clone").last();
	
		clone.clone(true).insertAfter(ParentRow);
		
		setZero();
		
		$('#itemsTable tbody tr.clone select').select2({width: "100%"});
	});

	/**ADD NEW BANK ROW**/
	$('body').on('click', '.add-cheque', function() 
	{
		$('#chequeTable tbody tr.clone select').select2('destroy');

		var clone1 = $("#chequeTable tbody tr.clone:first").clone(true);

		var ParentRow = $("#chequeTable tbody tr.clone").last();
		
		clone1.clone(true).insertAfter(ParentRow);
		
		setChequeZero();
		
		$('#chequeTable tbody tr.clone select').select2({width: "100%"});
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({format: 'M dd, yyyy', autoclose: true});
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

	$('#customer_terms').on('blur', function(e) 
	{
		computeDueDate();
	});

	$('#document_date').on('change', function(e) 
	{
		computeDueDate();
	});

	$('#tinno').on('keypress blur', function(e) 
	{
		// if(e.type == "blur")
		// {
		// 	validateTIN('newCustomer','tinno', e.target.value);
		// }
		if(e.type == "keypress")
			return isNumberKey(e,45);
	});

	$('#terms').on('keypress', function(e) 
	{
		if(e.type == "keypress")
			return isNumberKey(e,45);
	});

	// Validation for Exchange Rate
	$('#oldamount, #rate, #newamount, #paymentoldamount, #paymentrate, #paymentnewamount').on('blur click', function(e) 
	{
		var formid = e.target.form.id;

		if(e.type == "blur")
		{
			computeExchangeRate(formid, e.target.id); 
			formatNumber(e.target.id); 
			validateField(formid, e.target.id, e.target.id + "_help");
		}
		if(e.type == "blur" && e.target.id == "paymentrate")
		{
			var newamount 		= $('#paymentRateForm #paymentnewamount').val();
			newamount 			= newamount.replace(/,/g,'');
			var totalInvoice 	= $("#receiptForm #totalInvoice").val();

			// Check if exchange rate amount > total invoice
			if( parseFloat(totalInvoice) < parseFloat(newamount) )
			{
				$("#paymentRateForm #paymentnewamount")
					.closest('.field_col')
					.addClass('has-error');
				
				$("#paymentRateForm #exrateamount_help")
					.removeClass('hidden');
			}
			else
			{
				$("#paymentRateForm #paymentnewamount")
					.closest('.field_col')
					.removeClass('has-error');

				$("#paymentRateForm #exrateamount_help")
					.addClass('hidden');
			}

		}
		if(e.type == "click")
		{
			SelectAll(e.target.id);
		}
		
	});

	// Validation for Customer Modal
	$('#partnercode, #customer_name, #address').on('keyup', function(e) 
	{
		validateField('newCustomer',e.target.id, e.target.id + "_help");
	});

	$('#businesstype').on('change', function(e) 
	{
		validateField('newCustomer',e.target.id, e.target.id + "_help");
	});

	// Validation for Debit and Credit
	$('.format_values').on('blur click keypress', function(e) 
	{
		if(e.type == "blur")
			formatNumber(e.target.id);
		if(e.type == "click")
			SelectAll(e.target.id);
		if(e.type == "keypress")
			isNumberKey2(e);
	});

	$('.format_values_db').on('blur', function(e) 
	{	
		addAmountAll("debit");
	});

	$('.format_values_cr').on('blur', function(e) 
	{
		addAmountAll("credit");
	});

	/**
	* Apply Exchange Rate and converted amount
	*/
	$('#rateForm #btnProceed').click(function(e)
	{
		var valid 			= 0;
		var oldamount 		= $('#rateForm #oldamount').val();
		oldamount			= oldamount.replace(/,/g,'');
		var exchangerate 	= $('#rateForm #rate').val();
		exchangerate		= exchangerate.replace(/,/g,'');
		var newamount 		= $('#rateForm #newamount').val();
		newamount			= newamount.replace(/,/g,'');
		var account 		= $('#rateForm #defaultaccount').val();

		var amount 			= $('#receivableForm #h_amount').val();
		var accountentry	= $('#receivableForm #accountcode\\[1\\]').val();

		valid		+= validateField('rateForm','oldamount', "oldamount_help");
		valid		+= validateField('rateForm','rate', "rate_help");
		valid		+= validateField('rateForm','newamount', "newamount_help");
		// valid		+= validateField('rateForm','defaultaccount');

		if(valid == 0)
		{
			if(parseFloat(amount) == 0)
			{
				if(accountentry == '')
				{
					$.post('<?=BASE_URL?>financials/accounts_receivable/ajax/get_value', "account=" + account + "&event=exchange_rate")
					.done(function(data) 
					{
						var accountnature		= data.accountnature;

						$('#exchange_rate').val(exchangerate);

						$('#receivableForm #h_amount').val(oldamount);
						$('#receivableForm #h_exchangerate').val(exchangerate);
						$('#receivableForm #h_convertedamount').val(newamount);

						$('#receivableForm #accountcode\\[1\\]').val(account);

						if(accountnature == 'Debit' || accountnature == 'debit')
						{
							$('#receivableForm #debit\\[1\\]').val($('#rateForm #newamount').val());
							$('#receivableForm #credit\\[1\\]').prop('readOnly',true);

							$('#receivableForm #credit\\[1\\]').val('0.00');
							$('#receivableForm #debit\\[1\\]').prop('readOnly',false);
							addAmountAll("debit");
						}
						else
						{
							$('#receivableForm #credit\\[1\\]').val($('#rateForm #newamount').val());
							$('#receivableForm #debit\\[1\\]').prop('readOnly',true);

							$('#receivableForm #debit\\[1\\]').val('0.00');
							$('#receivableForm #credit\\[1\\]').prop('readOnly',false);
							addAmountAll("credit");
						}

						$('#receivableForm #accountcode\\[1\\]').trigger("change");
						$('#rateForm #defaultaccount').prop('disabled',true);
						$('#rateForm #defaultaccount').trigger("change");
						$('#rateModal').modal('hide');
					});
				}
				else
				{
					bootbox.dialog({
						message: "Are you sure you want to apply this exchange rate? <br/><br/>"
						+"Applying this would overwrite the first entry you've added.",
						title: "Confirmation",
							buttons: {
							yes: {
							label: "Yes",
							className: "btn-primary btn-flat",
							callback: function(result) 
							{	
								var data = "account=" + account + "&event=exchange_rate";
								$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/get_value", data)
								.done(function(data) 
								{
									var accountnature		= data.accountnature;

									$('#btnRate').html(exchangerate+'&nbsp;&nbsp;');

									$('#receivableForm #h_amount').val(oldamount);
									$('#receivableForm #h_exchangerate').val(exchangerate);
									$('#receivableForm #h_convertedamount').val(newamount);

									$('#receivableForm #accountcode\\[1\\]').val(account);

									if(accountnature == 'Debit' || accountnature == 'debit')
									{
										$('#receivableForm #debit\\[1\\]').val($('#rateForm #newamount').val());
										$('#receivableForm #credit\\[1\\]').prop('readOnly',true);

										$('#receivableForm #credit\\[1\\]').val('0.00');
										$('#receivableForm #debit\\[1\\]').prop('readOnly',false);
										addAmountAll("debit");
									}
									else
									{
										$('#receivableForm #credit\\[1\\]').val($('#rateForm #newamount').val());
										$('#receivableForm #debit\\[1\\]').prop('readOnly',true);

										$('#receivableForm #debit\\[1\\]').val('0.00');
										$('#receivableForm #credit\\[1\\]').prop('readOnly',false);
										addAmountAll("credit");
									}

									$('#receivableForm #accountcode\\[1\\]').trigger("change");
									$('#rateForm #defaultaccount').prop('disabled',true);
									$('#rateForm #defaultaccount').trigger("change");

									$('#rateModal').modal('hide');
								});
							}
						},
							no: {
							label: "No",
							className: "btn-default btn-flat",
							callback: function(result) {
									
								}
							}
						}
					});
				}

			}
			else
			{
				$('#exchange_rate').val(exchangerate);

				$('#receivableForm #h_amount').val(oldamount);
				$('#receivableForm #h_exchangerate').val(exchangerate);
				$('#receivableForm #h_convertedamount').val(newamount);

				$('#rateModal').modal('hide');
			}
		}
		else
		{
			bootbox.dialog({
				message: "Please complete all required fields.",
				title: "Warning",
				buttons: {
					success: {
						label: "OK",
						className: "btn-primary btn-flat",
						callback: function() {

						}
					}
				}
			});
		}
		
	});

	/**
	* Apply Exchange Rate and converted amount
	*/
	$('#paymentRateForm #btnProceed').click(function(e)
	{
		var valid 			= 0;
		var oldamount 		= $('#paymentRateForm #paymentoldamount').val();
		oldamount			= oldamount.replace(/,/g,'');
		var exchangerate 	= $('#paymentRateForm #paymentrate').val();
		exchangerate		= exchangerate.replace(/,/g,'');
		var newamount 		= $('#paymentRateForm #paymentnewamount').val();

		var totalInvoice 	= $("#receiptForm #totalInvoice").val();

		// var amount 			= $('#receiptForm #amount').val();

		valid		+= validateField('paymentRateForm','paymentoldamount', "paymentoldamount_help");
		valid		+= validateField('paymentRateForm','paymentrate', "paymentrate_help");
		valid		+= validateField('paymentRateForm','paymentnewamount', "paymentnewamount_help");

		var newamount_ = newamount.replace(/,/g,'')

		if( parseFloat(totalInvoice) < parseFloat(newamount_) )
			valid		+= validateField('paymentRateForm','paymentnewamount', "exrateamount_help");
			

		if(valid == 0)
		{
			$('#receiptForm #paymentexchangerate\\[1\\]').val(exchangerate);

			$('#receiptForm #paymentamount\\[1\\]').val(oldamount);
			$('#receiptForm #exchangerate\\[1\\]').val(exchangerate);
			$('#receiptForm #convertedamount\\[1\\]').val(newamount);

			$('#paymentRateModal').modal('hide');
		}
		else
		{
			bootbox.dialog({
				message: "Please complete all required fields.",
				title: "Warning",
				buttons: {
					success: {
						label: "OK",
						className: "btn-primary btn-flat",
						callback: function() {

						}
					}
				}
			});
		}	
	});

	// Deletion of Row
	$('#deleteItemModal #btnYes').click(function() 
	{
		// handle deletion here
		var id = $('#deleteItemModal').data('id');

		var table 		= document.getElementById('itemsTable');
		var rowCount 	= table.rows.length - 2;

		deleteItem(id);
		
		$('#deleteItemModal').modal('hide');
	});

	/**SCRIPT FOR HANDLING DELETE RECORD CONFIRMATION**/
	$('#btnCancel').click(function() 
	{
		$('#cancelModal').modal('show');
	});

	/**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function() 
	{
		var invoice		= $("#invoiceno\\[1\\]").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');

		$.post("<?= BASE_URL?>financials/accounts_receivable/ajax/delete_payments", "voucher=" + id)
		.done(function( data ) 
		{	
			if(data.msg == "success")
			{
				console.log("test");
				table.deleteRow(row);
				$('#deletePaymentModal').modal('hide');
				location.reload();
			}
			else
			{
				console.log("else");
				console.log(data.msg);
			}
		});
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

	/**TOGGLE ISSUE PAYMENT**/
	$("#btnReceive").click(function()
	{
		if ($("#receiptForm.hidden")[0])
		{
			var totalInvoice 	= document.getElementById('totalInvoice').value;
			totalInvoice 		= totalInvoice.replace(/,/g,'');
			var totalPayment 	= document.getElementById('totalPayment').value;
			totalPayment 		= totalPayment.replace(/,/g,'');
			var totalDiscount 	= document.getElementById('totalDiscount').value;
			totalDiscount 		= totalDiscount.replace(/,/g,'');
			var totalForex 		= document.getElementById('totalForex').value;
			totalForex 			= totalForex.replace(/,/g,'');
			var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	
			var result 			= addCommas(balance.toFixed(2));
		
			$("#receiptForm").removeClass('hidden');
			$("#receiptForm #convertedamount\\[1\\]").val(result);

			$('html, body').animate({ scrollTop: 0 }, 'slow');
		}
		else
		{
			$("#receiptForm").addClass('hidden');
		}
	});

	$('.payment_mode').on('change', function(e) 
	{
		toggleCheckInfo(e.target.value);
	});

	$('.payexrate').on('click', function(e) 
	{
		toggleExchangeRate('payment');
	});

	$('.pay_discount').on('blur', function(e) 
	{
		computeDiscount();
	});

	$('.pay_account').on('change', function(e) 
	{
		validateField('receiptForm','paymentaccount\\[1\\]', 'paymentaccount\\[1\\]_help'); 
	});

	$('.chequeamount').on('blur click', function(e) 
	{
		if(e.type == "blur")
		{
			formatNumber(e.target.id); 
			// computeExchangeRate(e.target.form.id,e.target.id,'1');
			
			computeExchangeRate('paymentRateForm',e.target.id,'1');
		}
		if(e.type == "click")
		{
			SelectAll(e.target.id);
		}
	});

	// Process New Transaction
	if('<?= $task ?>' == "create")
	{
		/**SAVE TEMPORARY DATA THROUGH AJAX**/
		$("#receivableForm").change(function()
		{
			if($("#receivableForm #accountcode\\[1\\]").val() != '' && $("#receivableForm #document_date").val() != '' && (parseFloat($("#receivableForm #debit\\[1\\]").val()) > 0 || parseFloat($("#receivableForm #credit\\[1\\]").val()) > 0) && $("#receivableForm #customer").val() != '')
			{
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "error")
					{
						// $("#receivableForm #btnSave").removeClass('disabled');
						// $("#receivableForm #btnSave_toggle").removeClass('disabled');
				
						// $("#receivableForm #btnSave").html('Save');
						$(".alert.alert-warning ").removeClass("hidden");
						$(".alert #errmsg").append(data.msg);
					}
					else
					{
						$(".alert.alert-warning").addClass("hidden");
						$(".alert #errmsg").append("");

						$("#receivableForm #btnSave").removeClass('disabled');
						$("#receivableForm #btnSave_toggle").removeClass('disabled');
				
						$("#receivableForm #btnSave").html('Save');
					}
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO LIST**/
		$("#receivableForm #btnSave").click(function()
		{
			var valid	= 0;
		
			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;

			/**validate items**/
			valid		+= validateDetails();

			if(valid == 0)
			{
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save").val("h_save");

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;

			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;
			
			/**validate items**/
			valid		+= validateDetails();

			if(valid == 0)
			{
				$("#receivableForm #h_save_new").val("h_save_new");

				// setTimeout(function() 
				// {
				// 	// $("#receivableForm #save").attr('name','save_new');
				// 	// $("#receivableForm").submit();
				// },1000);

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});

			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO PREVIEW INVOICE**/
		$("#receivableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				$("#receivableForm #h_save_preview").val("h_save_preview");

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});


				// setTimeout(function() 
				// {
				// 	$("#receivableForm").submit();
				// },1000);
			}
		});

	}
	else if('<?= $task ?>' == "edit") 
	{
		/**SAVE CHANGES AND REDIRECT TO LIST**/
		$("#receivableForm #btnSave").click(function(e)
		{
			var valid	= 0;

			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save").val("h_save");
				
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});
			}
		});
			
		/**SAVE CHANGES AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;
			
			/**validate Customer field**/
			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');
				
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});
			}
		});
			
		$("#receivableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			//$("#receivableForm").find('.form-group').find('input, textarea, select').trigger('blur');
			$("#receivableForm #customer").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			$("#receivableForm #document_date").trigger('blur');
			valid 	+= $("#receivableForm").find('.form-group.has-error').length;
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save_preview").val("h_save_preview");
				
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function( data ) 
				{
					if(data.msg == "success")
					{
						$("#receivableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#diverror").removeClass("hidden");
						$("#diverror #errmsg ul").html(msg);
					}
				});
			}
		});
	}

	/**APPLY PAYMENT LINK**/
	var hash 	= window.location.hash.substring(1);
	if(hash != '')
	{
		var noCashAccounts 	= document.getElementById('noCashAccounts').value;
		var totalInvoice 	= document.getElementById('totalInvoice').value;
		totalInvoice 		= totalInvoice.replace(/,/g,'');
		var totalPayment 	= document.getElementById('totalPayment').value;
		totalPayment 		= totalPayment.replace(/,/g,'');
		var totalDiscount 	= document.getElementById('totalDiscount').value;
		totalDiscount 		= totalDiscount.replace(/,/g,'');
		var totalForex 		= document.getElementById('totalForex').value;
		totalForex 			= totalForex.replace(/,/g,'');

		totalForex
		var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	
		var result 			= addCommas(balance.toFixed(2));
		
		$("#receiptForm").removeClass('hidden');
		
		$("#receiptForm #convertedamount\\[1\\]").val(result);
		if(noCashAccounts == true)
		{
			bootbox.alert("Please make sure to maintain at least one(1) Cash account. <br><small>Click <strong><a href='index.php?mod=maintenance&type=bank_detail&task=create'>here</a></strong> to add a cash account.</small>", function() 
			{
				location.href	= "<?= BASE_URL ?>financials/accounts_receivable";//'index.php?mod=financials&type=accounts_receivable';
			});
		}
	}
	else
	{
		$("#receiptForm").addClass('hidden');
	}

	$('#proformacode').change(function()
	{
		var code = this.value;
		
		
		$.post("<?= BASE_URL ?>financials/accounts_receivable/ajax/apply_proforma",{code:code})
		.done(function(data) 
		{
			var tablerow	= data.table;

			var table 		= document.getElementById('itemsTable');
			var count		= table.rows.length - 3;
			var firstaccount= $('#receivableForm #accountcode\\[1\\]').val();

			if(count > 0 && firstaccount != '')
			{
				bootbox.dialog({
					message: "Are you sure you want to apply this proforma? <br/><br/>"
					+"Applying this would overwrite the existing entries you've added.",
					title: "Confirmation",
						buttons: {
						yes: {
						label: "Yes",
						className: "btn-primary btn-flat",
						callback: function(result) {
								$('#itemsTable tbody').html(tablerow);

								resetIds();

								$('#itemsTable tbody tr.clone select').select2({width: "100%",allow_single_deselect: true,placeholder_text_single:'Select an Account'});
							}
						},
						no: {
						label: "No",
						className: "btn-default btn-flat",
						callback: function(result) {
								$('#receivableForm #proformacode').val('').trigger("change");
								// $('#receivableForm #proformacode').chosen().trigger('chosen:updated');
							}
						}
					}
				});
			}
			else
			{
				$('#itemsTable tbody').html(tablerow);

				resetIds();

				$('#itemsTable tbody tr.clone select').select2({width: "100%",allow_single_deselect: true,placeholder_text_single:'Select an Account'});
			}
				
		});

	});
	


}); // end

</script>