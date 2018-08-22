<style>
	#customerDetails2 .col-md-3 > .form-group,
	#customerDetails2 .col-md-2 > .form-group  {
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

	.customer_div > .form-group {
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
	<div id = "errordiv" class="alert alert-warning alert-dismissible hidden">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<h4><i class="icon fa fa-warning"></i>The system has encountered the following error/s!</h4>
		<div id = "msg_error">
			<ul class = "text-bold">

			</ul>
		</div>
		<p class = "text-bold">Please contact admin to fix this issue.</p>
	</div>
	
	<input value = "" name = "list_type" id = "list_type" type = "hidden">

	<form id = "customerDetailForm">
		<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
		<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
		<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
		<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" 
			type="hidden">
		<input class = "form_iput" value = "customerdetail" name = "h_form" id = "h_form" 
			type="hidden">
		<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" 
			type="hidden">
	</form>

	<form method = "post" class="form-horizontal" id = "receivableForm">

		<input class = "form_iput" value = "0.00" name = "h_amount" id = "h_amount" type="hidden">
		<input class = "form_iput" value = "0.00" name = "h_convertedamount" 
		id = "h_convertedamount" type = "hidden">
		<input class = "form_iput" value = "1.00" name = "h_exchangerate" 
		id = "h_exchangerate" type = "hidden">
		<input class = "form_iput" value = "<?= $task ?>" name = "h_task" 
		id = "h_task" type = "hidden">

		<input value = "" name = "voucherno" id = "voucherno" type = "hidden">
		<?php if($task == "view") { ?>
			<!--DELETE PAYMENT CONFIRMATION MODAL-->
					<div class="modal fade" id="deletePaymentModal" tabindex="-1" data-backdrop="static">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									Confirmation
									<button type="button" class="close" data-dismiss="modal">&times;</button>
								</div>
								<div class="modal-body">
									Are you sure you want to delete this payment?
								</div>
								<div class="modal-footer">
									<div class="row row-dense">
										<div class="col-md-12 center">
											<div class="btn-group">
												<button type="button" class = "btn btn-primary btn-flat" 
												id="btnYes">Yes</button>
											</div>
												&nbsp;&nbsp;&nbsp;
											<div class="btn-group">
												<button type="button" class = "btn btn-default btn-flat" 
												data-dismiss="modal">No</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>	
					<!--END DELETE PAYMENT CONFIRMATION MODAL-->

					<div class = "well well-lg">
						<div class = "panel panel-default">
							<div class = "panel-body">
								<div class = "row">
									<div class="col-md-8 col-sm-8 col-xs-8">
										<h2><strong>Receipt Voucher</strong> 
											<small><?='('.$voucherno.')'?></small>
										</h2>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4" style="vertical-align:middle;">
										<div class="row">
											<div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
												<h4>Date</h4>
											</div>
											<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
												<h4>: <strong><?= $transactiondate ?></strong></h4>
											</div>
										</div>
										<div class="row">
											<div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
												<h4>Reference No</h4>
											</div>
											<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
												<h4>: <strong><?=$referenceno;?></strong></h4>
											</div>
										</div>
									</div>
								</div>

								<div class = "row">
									<div class="col-md-8 col-sm-8 col-xs-8">
										<h4>Customer :</h4>
										<h4><strong><?=$c_customer?></strong></h4>
										<div class="row">
											<div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
												Email
											</div>
											<div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
												: <?=$c_email?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
												TIN
											</div>
											<div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
												: <?=$tinno?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
												Address
											</div>
											<div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
												: <?=$address1?>
											</div>
										</div>
									</div>
								</div>

								<div class = "row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<hr/>
									</div>
								</div>

								<div class = "panel panel-default">
									<div class="box-body table-responsive no-pad">
										<table class="table table-hover">
											<thead>
												<tr class="info">
													<th class="col-md-3 text-center">Account code</th>
													<th class="col-md-4 text-center">Description</th>
													<th class="col-md-2 text-center">Debit</th>
													<th class="col-md-2 text-center">Credit</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$total_debit 	= 0;
													$total_credit 	= 0;
													
													if(!is_null($data["details"]) && !empty($sid))
													{
														for($i = 0; $i < count($data["details"]); $i++)
														{
															$accountlevel		= $data["details"][$i]->accountcode;
															$accountname		= $data["details"][$i]->accountname;
															$accountcode		= ($task != 'view') ? $accountlevel : $accountname;
															$detailparticular	= $data["details"][$i]->detailparticulars;
															$debit				= $data["details"][$i]->debit;
															$credit				= $data["details"][$i]->credit;
															
															echo '<tr>';	
																echo '<td>'.$accountname.'</td>';
																echo '<td>'.$detailparticular.'</td>';
																echo '<td class="text-right">'.number_format($debit,2).'</td>';
																echo '<td class="text-right">'.number_format($credit,2).'</td>';
															echo '</tr>';
															
															$total_debit += $debit;
															$total_credit += $credit;
														}
													}
												?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="2" class="text-right" style="border-top:1px solid #DDDDDD;">
														<strong>Total</strong>
													</td>
													<td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_debit,2)?></strong></td>
													<td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_credit,2)?></strong></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>

								<!--PAYMENT Received-->
								<br/>
								<div class = "table-responsive">
									<div class="panel panel-default">
										<div class="panel-heading">
											<strong>Received Payments</strong>
										</div>
										<table class="table table-striped table-condensed table-bordered" id="paymentsTable">
											<thead>
												<tr class="info">
													<th class="col-md-2 text-center">Date</th>
													<th class="col-md-2 text-center">Mode</th>
													<!--<th class="hidden col-md-2 text-center">AP Voucherno</th>-->
													<th class="col-md-4 text-center">Reference</th>
													<!--<th class="col-md-2 text-center">Payment Account</th>-->
													<th class="col-md-2 text-center">Amount</th>
													<th class="col-md-2 text-center">Discount</th>
													<th class="col-md-1 text-center">Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$totalPayment	= 0;
												$totaldiscount	= 0;
												$row = 1;

												if(!is_null($data["payments"][0]->voucherno) && !empty($data["payments"]))
												{
													for($i = 0; $i < count($data["payments"]); $i++)
													{
														$paymentnumber		= $data["payments"][$i]->voucherno;
														$paymentdate		= $data["payments"][$i]->transactiondate;
														$paymentdate		= date("M d, Y",strtotime($paymentdate));
														$paymentaccountcode	= $data["payments"][$i]->accountcode;
														$paymentaccount		= $data["payments"][$i]->accountname;
														$paymentmode		= $data["payments"][$i]->paymenttype;
														$reference			= $data["payments"][$i]->referenceno;
														$paymentamount		= $data["payments"][$i]->amount;
														$paymentstat		= $data["payments"][$i]->stat;
														$paymentcheckdate	= $data["payments"][$i]->checkdate;
														$paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? 
														date("M d, Y",strtotime($paymentcheckdate)) : "";
														$paymentatccode		= $data["payments"][$i]->atcCode;
														$paymentnotes		= $data["payments"][$i]->particulars;
														$checkstat			= $data["payments"][$i]->checkstat;
														$paymentdiscount	= $data["payments"][$i]->discount;
														$paymentrate		= (isset($data["payments"][$i]->exchangerate) 
														&& !empty($data["payments"][$i]->exchangerate)) ? 
														$data["payments"][$i]->exchangerate : 1;
														// $paymentconverted	= (isset($data["payments"][$i]->convertedamount) 
														// && $data["payments"][$i]->convertedamount > 0) ? 
														// $data["payments"][$i]->convertedamount : $paymentamount;
														//var_dump($data["payments"][$i]->convertedamount);
														$paymentconverted   = $paymentamount;
														$arvoucherno		= $data["payments"][$i]->arvoucherno;
														$sum				= $data["payments"][$i]->sum;

														$cheque_values		= (!is_null($rollArray) 
														&& !empty($rollArray[$paymentnumber])) ? 
														json_encode($rollArray[$paymentnumber]) : "";

														echo '<tr>';
																echo '<td>';
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input_label")
																		->setName('paymentdate'.$row)
																		->setId('paymentdate'.$row)
																		->setValue($paymentdate)
																		->setAttribute(array("readonly" => "readonly"))
																		->draw(true);
																echo '<input value="'.$paymentnumber.'" 
																name = "paymentnumber'.$row.'" id = "paymentnumber'.$row.'" 
																type = "hidden">';
																echo '</td>';

																echo '<td>';
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input_label")
																		->setName("pmode1".$row)
																		->setId("pmode1".$row)
																		->setAttribute(array("disabled" => "disabled"))
																		->setValue(ucwords($paymentmode))
																		->draw(true);
																
																echo $ui->formField('dropdown')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input-sm hidden")
																		->setPlaceholder('None')
																		->setName('paymentmode'.$row)
																		->setId('paymentmode'.$row)
																		->setList(array("cash" => "Cash", "cheque" => "Cheque"))
																		->setValue($paymentmode)
																		->draw(true);
																echo '</td>';

																// echo '<td class = "hidden">';
																// echo $ui->formField('text')
																// 			->setSplit('', 'col-md-12 no-pad')
																// 			->setClass("input_label")
																// 			->setName("pay_apv".$row)
																// 			->setId("pay_apv".$row)
																// 			->setAttribute(array("disabled" => "disabled"))
																// 			->setValue($apvoucherno)
																// 			->draw(true);
																// echo '</td>';

																echo '<td>';
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input_label")
																		->setName("paymentreference".$row)
																		->setId("paymentreference".$row)
																		->setAttribute(array("readonly" => "readonly"))
																		->setValue($reference)
																		->draw(true);
																echo '<input value="'.$paymentcheckdate.'" 
																name = "paymentcheckdate'.$row.'" 
																id = "paymentcheckdate'.$row.'" type = "hidden">';
																echo '<input value="'.$paymentnotes.'" 
																name = "paymentnotes'.$row.'" id = "paymentnotes'.$row.'" 
																type = "hidden">';
																echo '</td>';

																//echo '<td>';
																// echo $ui->formField('text')
																// 		->setSplit('', 'col-md-12 no-pad')
																// 		->setClass("input_label")
																// 		->setName("pacct".$row)
																// 		->setId("pacct".$row)
																// 		->setValue($paymentaccount)
																// 		->setAttribute(array("readonly" => "readonly"))
																// 		->draw(true);

																// echo $ui->formField('dropdown')
																// 		->setSplit('', 'col-md-12 no-pad')
																// 		->setClass("input-sm hidden")
																// 		->setPlaceholder('None')
																// 		->setName('paymentaccount'.$row)
																// 		->setId('paymentaccount'.$row)
																// 		->setList($cash_account_list)
																// 		->setValue($paymentaccountcode)
																// 		->draw(true);
																
																// echo '</td>';

																echo '<td>';
																echo '<input value="'.number_format($paymentamount,2).'" 
																name = "paymentamount'.$row.'" id = "paymentamount'.$row.'" 
																type = "hidden">';
																echo '<input value="'.number_format($paymentrate,2).'" 
																name = "paymentrate'.$row.'" id = "paymentrate'.$row.'" 
																type = "hidden">';
																
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input_label text-right")
																		->setName("paymentconverted".$row)
																		->setId("paymentconverted".$row)
																		->setAttribute(array("readonly" => "readonly"))
																		->setValue(number_format($paymentconverted,2))
																		->draw(true);

																echo $ui->formField('textarea')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("hidden")
																		->setName("chequeInput".$row)
																		->setId("chequeInput".$row)
																		->setValue($cheque_values)
																		->draw(true);
																echo '</td>';

																echo '<td>';
																echo $ui->formField('text')
																		->setSplit('', 'col-md-12 no-pad')
																		->setClass("input_label text-right")
																		->setName("paymentdiscount".$row)
																		->setId("paymentdiscount".$row)
																		->setAttribute(array("readonly" => "readonly"))
																		->setValue(number_format($paymentdiscount,2))
																		->draw(true);
																echo '</td>';

																echo '<td class="text-center">';
																echo (strtolower($checkstat) != 'cleared') ? '
																	
																	<a role="button" class="btn btn-default btn-xs" 
																	href="'.BASE_URL.'financials/receipt_voucher/
																	print_preview/'.$sid.'" title="Print Receipt Voucher">
																	<span class="glyphicon glyphicon-print"></span></a>' : 
																	'<a role="button" class="btn btn-default btn-xs" 
																	href="'.BASE_URL.'financials/receipt_voucher/
																	print_preview/'.$sid.'" title="Print Receipt Voucher" >
																	<span class="glyphicon glyphicon-print"></span></a>';
																echo '</td>';

															// <button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row.'\');" title="Delete Payment"><span class="glyphicon glyphicon-trash"></span></button>

														echo '</tr>';

														$row++;

														$totalPayment += $paymentconverted;
														$totaldiscount+= $paymentdiscount;
													}
													
												}
												else
												{
													echo '<tr><td colspan = "8" class = "text-center">
															No payments received for this receivable</td></tr>';
												}
											?>
											</tbody>
											<tfoot>
												<tr>
													<td class="text-right" colspan="4" style="border-top:1px solid #DDDDDD;" >
														<label for="subtotal" class="control-label">Total </label>
													</td>
													<td style="border-top:1px solid #DDDDDD;" class="text-right">
														<label class="control-label" id="totalPaymentCaption" 
														style = "padding: 0 12px 0 12px;"><?=number_format($totalPayment,2)?></label>
														<input class="form_iput" value="<?= $totalPayment?>" 
														name="totalPayment" id="totalPayment" type="hidden">
													</td>
													<td style="border-top:1px solid #DDDDDD;" class="text-right">
														<label class="control-label" id="totalDiscountCaption" 
														style = "padding: 0 12px 0 12px;">
														<?=number_format($totaldiscount,2)?></label>

														<input class="form_iput" value="<?= $totaldiscount?>" 
														name="totalDiscount" id="totalDiscount" type="hidden">
														<input class="form_iput" value="<?= $forexamount?>" 
														name="totalForex" id="totalForex" type="hidden">
													</td>
													<td style="border-top:1px solid #DDDDDD;">
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<!--PAYMENT Received : END-->

								<div class = "row">
									<div class="col-md-2 col-sm-2 col-xs-2 left">
									&nbsp;
									</div>
									<div class="col-md-8 col-sm-8 col-xs-8 text-center">

										<a href="<?=BASE_URL?>financials/receipt_voucher/edit/<?=$sid?>" 
										class="btn btn-primary btn-md btn-flat">Edit</a>
									</div>
									<div class="col-md-2 col-sm-2 col-xs-2 text-right">
										<a href="<?=BASE_URL?>financials/receipt_voucher" role="button" 
										class="btn btn-primary btn-md btn-flat" id="btnExit" >Exit</a>
									</div>
								</div>


							</div>
						</div>
					</div>

		<? }else { ?>
		<div class="box box-primary">
			<div class = "col-md-12">&nbsp;</div>
				<textarea class = "hidden" name="h_check_rows_" id="h_check_rows_"></textarea>

					<div class = "row">
						<div class = "col-md-6">
							<?php
								echo $ui->formField('text')
										->setLabel('Voucher No:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('voucher_no')
										->setId('voucher_no')
										->setAttribute(array("disabled" => "disabled"))
										->setPlaceholder("- auto generate -")
										->setValue($voucherno)
										->draw($show_input);
							?>
							<input type = "hidden" id = "h_voucher_no" name = "h_voucher_no" 
							value = "<?= $generated_id ?>">
						</div>

						<div class = "col-md-6 remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Transaction Date')
										->setSplit('col-md-3', 'col-md-8')
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
						<div class = "col-md-6 customer_div remove-margin">

							<?php
								echo $ui->formField('dropdown')
									->setLabel('Customer: ')
									->setPlaceholder('None')
									->setSplit('col-md-3', 'col-md-8')
									->setName('customer')
									->setId('customer')
									->setList($customer_list)
									->setValue($customercode)
									->setAttribute(array("onChange" => "getPartnerInfo(this.value);"))
									->setValidation('required')
									->setButtonAddon('plus')
									->draw($show_input);
							?>
						</div>

						<div class="col-md-6 remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Exchange Rate:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('exchange_rate')
										->setId('exchange_rate')
										->setClass("btn btn-success btn-flat text-right text-bold")
										->setValue($exchangerate)
										->draw($show_input);
							?>
						</div>

					</div>
					
					<div class="row">
						<div class="col-md-6 remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('<i>Tin</i>')
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_tin')
										->setId('customer_tin')
										->setAttribute(array("maxlength" => "15", "rows" => "1", 
										"onKeyPress" => "return isNumberKey(event,45);", 
										"onBlur" => "saveField(this.id);", 
										"onClick" => "editField(this.id);"))
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
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_terms')
										->setId('customer_terms')
										->setAttribute(array("readonly" => "", "maxlength" => "15", 
										"onKeyPress" => "return isNumberKey(event,45);", 
										"onBlur" => "saveField(this.id); computeDueDate();", 
										"onClick" => "editField(this.id);"))
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
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_address')
										->setId('customer_address')
										->setClass("input_label")
										->setAttribute(array("readonly" => "", "rows" => "1", 
										"onBlur" => "saveField(this.id);", "onClick" => "editField(this.id);"))
										->setValue($address1)
										->draw($show_input);
							?>
						</div>
					</div>	

					<div class = "row">
						<div class="col-md-6 remove-margin">
							<?php
								echo $ui->formField('text')
									->setLabel('AR Voucher.')
									->setSplit('col-md-3', 'col-md-8')
									->setName('arv')
									->setId('arv')
									->setAttribute(array("onClick" => "showReceivePayment();"))
									->setAddon('search')
									->setValue("")
									->draw($show_input);
							?>
						</div>

						<div class = "col-md-6 remove-margin">
							<?php
								echo $ui->formField('dropdown')
										->setLabel('Payment Mode: ')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setClass("payment_mode")
										->setName('paymentmode')
										->setId('paymentmode')
										->setList(array("cash" => "Cash", "cheque" => "Cheque"))
										->setAttribute(array("onChange" => "toggleCheckInfo(this.value); 
										validateField('receivableForm',this.id, 'paymentmode_help');", "disabled"))
										->setValue($paymenttype)
										->draw($show_input);
							?>
							<div class="col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" 
							id = "paymentmode_help" style = "margin-bottom: 0px">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
					</div>
				
					<div class="row" id = "cash_payment_details">
						<div class = "col-md-6 remove-margin" id = "check_field">
							<?php
								echo $ui->formField('text')
										->setLabel('Reference Number: ')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setClass("input-sm")
										->setName('paymentreference')
										->setId('paymentreference')
										->setPlaceHolder("Cheque/Reference No")
										->setAttribute(array("maxlength" => "50"))
										->setValue($referenceno)
										->draw(true);
							?>
							<div class="col-md-4" style = "width: 36%;">&nbsp;</div>
							<span class="help-block hidden small req-color" 
							id = "paymentreference_help" style = "margin-bottom: 0px">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
										->setValue($particulars)
										->draw($show_input);
							?>
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
								Please make sure that the total amount (<strong></strong>) is equal 
								to both total debit or total credit. 
							</span>
						</div>
					</div>

					<div class="has-error">
						<span id="chequeCountError" class="help-block hidden small col-md-offset-1">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please specify at least one(1) cheque.
						</span>
						<!--<span id="appCountError" class="help-block hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please select at least one(1) payable.
						</span>-->
						<span id="chequeAmountError" class="help-block hidden small col-md-offset-1">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please complete the fields on the highlighted row(s).
						</span>
						<!--<span id="appAmountError" class="help-block hidden small col-md-offset-1">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure that the amount paid for the payable(s) below are greater than zero(0).
						</span>-->
						<span id="paymentAmountError" class="help-block hidden small col-md-offset-1">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure that the total payment applied 
							(<strong id="disp_tot_payment">0</strong>) should be equal to 
							(<strong id="disp_tot_cheque">0</strong>).
						</span>
					</div>

					<!-- Cheque Details -->
					<textarea class = "hidden" id = "rollArray">
						<?php echo (!is_null($rollArray) && !empty($rollArray[$sid])) ? 
						json_encode($rollArray[$sid]) : ""; ?>
					</textarea>

					<!-- Cheque Details -->
					<div class="panel panel-default hidden" id="check_details">

						<div class="panel-heading">
							<strong>Cheque Details</strong>
						</div>

						<div class="table-responsive">
							<!--<fieldset disabled>-->
							<table class="table table-condensed table-bordered table-hover" id="chequeTable">
								<thead>
									<tr class="info">
										<th class="col-md-2 text-center">Bank Account</th>
										<th class="col-md-2 text-center">Cheque Number</th>
										<th class="col-md-2 text-center">Cheque Date</th>
										<th class="col-md-2 text-center">Amount</th>
										<th class="col-md-1 text-center"></th>
									</tr>
								</thead>
								<tbody>
									<tr class="clone">
										<td class="">
											<?php
												echo $ui->formField('dropdown')
														->setSplit('', 'col-md-12 field_col')
														->setPlaceholder('Select One')
														->setClass("cheque_account")
														->setName('chequeaccount[1]')
														->setId('chequeaccount[1]')
														->setList($cash_account_list)
														->setValue("")
														->draw(true);
											?>
										</td>

										<td>
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("")
														->setName('chequenumber[1]')
														->setId('chequenumber[1]')
														->setAttribute(array("maxlength" => "100"))
														->setValue("")
														->draw(true);
											?>
										</td>

										<td>
											<div class="input-group date remove-margin">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>

												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12 field_col')
															->setClass("datepicker-input")
															->setName('chequedate[1]')
															->setId('chequedate[1]')
															->setAttribute(array("maxlength" => "50"))
															->setValue($transactiondate)
															->draw(true);
												?>
											</div>
										</td>

										<td>
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("text-right chequeamount")
														->setName('chequeamount[1]')
														->setId('chequeamount[1]')
														->setAttribute(array("maxlength" => "20", 
														"onBlur" => "formatNumber(this.id); addAmounts();", 
														"onClick" => "SelectAll(this.id);"))
														->setValue("0.00")
														->draw(true);
											?>
										</td>

										<td class="text-center">
											<button type="button" class="btn btn-sm btn-danger 
											btn-flat confirm-delete" name="chk_[]" style="outline:none;" 
											onClick="confirmChequeDelete(1);">
											<span class="glyphicon glyphicon-trash"></span></button>
										</td>
									</tr>
								</tbody>

								<tfoot>
									<tr>
										<td colspan="2">
											<a type="button" class="btn btn-sm btn-link add-cheque"  
											style="text-decoration:none; outline:none;" 
											href="javascript:void(0);">Add a New Line</a>
										</td>
										<td class="text-right"><label class="control-label">Total</label></td>
										<td class="text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("text-right input_label")
														->setId("totalcheques")
														->setAttribute(array("readonly" => "readonly"))
														->setValue(number_format(0, 2))
														->draw($show_input);
											?>
										</td>
									


									</tr>	
								</tfoot>

							</table>
							<!--<fieldset>-->
						</div>
					</div>

				</div>

				<div class="panel panel-default">
					<div class="table-responsive">
						<table class="table table-hover table-condensed " id="itemsTable">
							<thead>
								<tr class="info">
									<th class="col-md-3 text-center">Account code</th>
									<th class="col-md-4 text-center">Description</th>
									<th class="col-md-2 text-center">Debit</th>
									<th class="col-md-2 text-center">Credit</th>
									<?if($task != 'view'){?>
										<th class="col-md-1 center"></th>
									<?}?>
								</tr>
							</thead>
							<tbody id = "ar_items">
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
										<tr>
											<td class="text-center" style="vertical-align:middle;" colspan="5">
											- No Records Found -</td>
										</tr>
										
								<?
									}else if(!empty($sid) && $task!='create')
									{
										$row 			= 1;
										$total_debit 	= 0;
										$total_credit 	= 0;
										$disable_debit	= '';
										$disable_credit	= '';

										$debit 			   = '0.00';
										$credit 		   = '0.00';

										if(!empty($data["details"]))
										{

											for($i = 0; $i < count($data["details"]); $i++)
											{
												$accountlevel		= $data["details"][$i]->accountcode;
												$accountname		= $data["details"][$i]->accountname;
												$accountcode		= ($task != 'view') ? $accountlevel : 
																	  $accountname;
												$detailparticulars	= $data["details"][$i]->detailparticulars;
												$debit				= $data["details"][$i]->debit;
												$credit				= $data["details"][$i]->credit;
												
												$disable_debit		= ($task == 'edit' && ($credit > 0 
															&& $debit == 0)) ? "readonly = 'readonly'" : "";
												$disable_credit		= ($task == 'edit' && ($debit > 0 
															&& $credit == 0)) ? "readonly = 'readonly'" : 
															"false";		
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
																	->setAttribute(array("maxlength" => "20", 
																	"onBlur" => "formatNumber(this.id); 
																	addAmountAll('debit');", 
																	"onClick" => "SelectAll(this.id);", 
																	"onKeyPress" => "isNumberKey2(event);", 
																	$disable_debit))
																	// ->setClass("format_values_db format_values")
																	->setValue(number_format($debit, 2))
																	->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
															echo $ui->formField('text')
																	->setSplit('', 'col-md-12')
																	->setName('credit['.$row.']')
																	->setId('credit['.$row.']')
																	->setAttribute(array("maxlength" => "20", 
																	"onBlur" => "formatNumber(this.id); 
																	addAmountAll('credit');", 
																	"onClick" => "SelectAll(this.id);", 
																	"onKeyPress" => "isNumberKey2(event);", 
																	$disable_credit))
																	// ->setClass("format_values_cr format_values")
																	->setValue(number_format($credit, 2))
																	->draw($show_input);
														?>
													</td>
													<?if($task!='view'){ ?>
													<td class="text-center">
														<button type="button" class="btn btn-danger 
														btn-flat confirm-delete" data-id="<?=$row?>" 
														name="chk[]" style="outline:none;" 
														onClick="confirmDelete(<?=$row?>);">
														<span class="glyphicon glyphicon-trash"></span></button>
													</td>
													<?}?>		
												</tr>
									<?	
												$total_debit += $debit;
												$total_credit += $credit;
												$row++;	

											}
										}
										else
										{
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
																->setAttribute(array("maxlength" => "20", 
																"onBlur" => "formatNumber(this.id); 
																addAmountAll('debit');", 
																"onClick" => "SelectAll(this.id);", 
																"onKeyPress" => "isNumberKey2(event);"))
																->setValue(number_format($debit, 2))
																->draw($show_input);
													?>
												</td>
												<td class = "remove-margin">
													<?php
														echo $ui->formField('text')
																->setSplit('', 'col-md-12')
																->setName('credit['.$row.']')
																->setId('credit['.$row.']')
																->setAttribute(array("maxlength" => "20", 
																"onBlur" => "formatNumber(this.id); 
																addAmountAll('credit');", 
																"onClick" => "SelectAll(this.id);", 
																"onKeyPress" => "isNumberKey2(event);"))
																->setValue(number_format($credit, 2))
																->draw($show_input);
													?>
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger btn-flat 
													confirm-delete" data-id="<?=$row?>" name="chk[]" 
													style="outline:none;" onClick="confirmDelete(<?=$row?>);">
													<span class="glyphicon glyphicon-trash"></span></button>
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
																->setAttribute(array("maxlength" => "20", 
																"onBlur" => "formatNumber(this.id); 
																addAmountAll('debit');", "onClick" => 
																"SelectAll(this.id);", "onKeyPress" => 
																"isNumberKey2(event);"))
																->setValue(number_format($debit, 2))
																->draw($show_input);
													?>
												</td>
												<td class = "remove-margin">
													<?php
														echo $ui->formField('text')
																->setSplit('', 'col-md-12')
																->setName('credit['.$row.']')
																->setId('credit['.$row.']')
																->setAttribute(array("maxlength" => "20", 
																"onBlur" => "formatNumber(this.id); 
																addAmountAll('credit');", 
																"onClick" => "SelectAll(this.id);", 
																"onKeyPress" => "isNumberKey2(event);"))
																->setValue(number_format($credit), 2)
																->draw($show_input);
													?>
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger btn-flat 
													confirm-delete" data-id="<?=$row?>" name="chk[]" 
													style="outline:none;" onClick="confirmDelete(<?=$row?>);">
													<span class="glyphicon glyphicon-trash"></span></button>
												</td>			
											</tr>
									<?
										}
									}
								?>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<? if($task != 'view') { ?>
											<a type="button" class="btn btn-link add-data" 
											style="text-decoration:none; outline:none;" 
											href="javascript:void(0);">Add a New Line</a>
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
													->setSplit('col-md-3', 'col-md-8')
													->setName('total_debit')
													->setId('total_debit')
													->setClass("input_label")
													->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
													->setValue(number_format($total_debit,2))
													->draw($show_input);
										?>
									</td>
									<td class="right" style="border-top:1px solid #DDDDDD;">
										<?php
											echo $ui->formField('text')
													->setSplit('col-md-3', 'col-md-8')
													->setName('total_credit')
													->setId('total_credit')
													->setClass("input_label")
													->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
													->setValue(number_format($total_credit,2))
													->draw($show_input);
										?>
									</td>
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
						
						<? if($task != "view") { ?> 
							<div class="btn-group" id="save_group">
								
								<input type = "button" value = "Save" name = "save" id = "btnSave" 
									class="btn btn-primary btn-sm btn-flat"/>
								<input type = "hidden" value = "" name = "h_save" id = "h_save"/>

								<button type="button" id="btnSave_toggle" class="btn btn-primary 
									dropdown-toggle btn-sm btn-flat" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								
								<ul class="dropdown-menu left" role="menu">
									<li id = "save_new" style="cursor:pointer;">
										Save & New
										<input type = "hidden" value = "" name = "h_save_new" id = "h_save_new"/>
									</li>
									<li class="divider"></li>
									<li id = "save_preview" style="cursor:pointer;">
										Save & Preview
										<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_preview"/>
									</li>
								</ul>
							</div>
						&nbsp;&nbsp;&nbsp;
						<? }else { ?> 
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" 
								onClick = "location.href = '<?=BASE_URL?>financials/receipt_voucher/edit/<?=$sid?>'" 
								id="btnEdit">Edit</button>
							</div>
						<? } ?>
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" 
							id="btnCancel">Cancel</button>
						</div>
					</div>
				</div>

		<!--</div>-->

			<!-- Payment Modal -->
			<div class="modal fade" id="paymentModal" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<b>Receipt Payments</b>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body no-padding">
							<form class="form-horizontal" id="paymentForm">
								
								<div class="row row-dense">
									<div class = "col-md-6 remove-margin">
										<?php
											echo $ui->formField('dropdown')
													->setLabel('')
													->setSplit('col-md-4 force-left', 'col-md-6 field_col')
													->setValue("")
													->draw(false);
										?>
									</div>

									<div class = "col-md-6 remove-margin">
										<?php
											echo $ui->formField('text')
													->setLabel('Total Payment: ')
													->setSplit('col-md-4 force-left', 'col-md-7 field_col')
													->setClass("input-sm")
													->setName('total_payment')
													->setId('total_payment')
													->setPlaceHolder("0.00")
													->setAttribute(array("maxlength" => "50", "readonly" => "readonly"))
													->setValue("")
													->draw(true);
										?>
										<div class="col-md-3" style = "width: 17%;">&nbsp;</div>
										<span class="help-block hidden small req-color" 
										id = "total_payment_help" style = "margin-bottom: 0px">
										<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
									</div>
								</div>

								<div class="has-error">
									<span id="appCountError" class="help-block hidden small">
										<i class="glyphicon glyphicon-exclamation-sign"></i> 
										Please select at least one(1) receivable.
									</span>
									<span id="appAmountError" class="help-block hidden small">
										<i class="glyphicon glyphicon-exclamation-sign"></i> 
										Please make sure that the amount paid for the receivable(s) below are 
										greater than zero(0).
									</span>
									<!--<span id="paymentAmountError" class="help-block hidden small">
										<i class="glyphicon glyphicon-exclamation-sign"></i> 
										Please make sure that the total payment applied (<strong id="disp_tot_payment">0</strong>) should be equal to (<strong id="disp_tot_cheque">0</strong>).
									</span>-->
								</div>
								<div class="table-responsive">
									<table class="table table-condensed table-bordered table-hover" id="app_receivableList">
										<thead>
											<tr class="info">
												<th class="col-md-1 center"></th>
												<th class="col-md-2 text-center">Date</th>
												<th class="col-md-1 text-center">Voucher</th>
												<th class="col-md-2 text-center">Total Amount</th>
												<th class="col-md-2 text-center">Balance</th>

												<?php if($task == "create") { ?> 
													<th class="col-md-2 text-center">Amount to Pay</th>
												<? }else { ?> 
													<th class="col-md-2 text-center">Amount Paid</th>
												<? } ?>
											</tr>
										</thead>
										<tbody id="receivable_list_container">
											<tr>
												<td class="text-center" style="vertical-align:middle;" 
												colspan="7">- No Records Found -</td>
											</tr>
										</tbody>
										<tfoot>
											<tr> <!-- class="info" -->
												<!--<td class="col-md-3 center" id="app_page_info">&nbsp;</td>-->
												<!--<td class="col-md-9 center" id="app_page_links"></td>-->
												<td class="center" colspan = "7" id="app_page_links"></td>
											</tr>
										</tfoot>
									</table>

									<!-- Text Area for AR selected -->
									<textarea class = "hidden" id = "h_check_rows" name = "h_check_rows"></textarea>

								</div>

								<div class="modal-footer">
									<div class="col-md-12 col-sm-12 col-xs-12 text-center">
										<div class="btn-group">

										<!-- Call ajax for retrieving PV Details to be display in table -->
											<button type = "button" class = "btn btn-primary btn-sm btn-flat" 
											onClick = "getRVDetails();">Done&nbsp;</button>
										</div>
											&nbsp;&nbsp;&nbsp;
										<div class="btn-group">
											<button type="button" class="btn btn-default btn-sm btn-flat" 
											data-dismiss="modal" onClick="clearPayment();">Cancel</button>
										</div>
									</div>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div> 
		<?}?>
	</form>
</section>

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
						<button type="button" class="close" data-dismiss="alert" 
						aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>

					<input type = "hidden" id = "oldamount_" value = ""/>
					<input type = "hidden" id = "rate_" value = ""/>
					<input type = "hidden" id = "newamount_" value = ""/>

					<div class="well well-md">
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Amount: ')
										->setSplit('col-md-4', 'col-md-7')
										->setName('oldamount')
										->setId('oldamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20", "onClick" => 
										"SelectAll(this.id);", "onBlur" => 
										"computeExchangeRate('rateForm',this.id); formatNumber(this.id);"))
										->setValidation('required')
										->setValue("0.00")
										->draw($show_input);
							?>
							<!--<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "oldamount_help">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>-->
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Rate: ')
										->setSplit('col-md-4', 'col-md-7')
										->setName('rate')
										->setId('rate')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "9", "onClick" => 
										"SelectAll(this.id);", "onBlur" => 
										"computeExchangeRate('rateForm',this.id); formatNumber(this.id);"))
										->setValidation('required')
										->setValue("1.00")
										->draw($show_input);
							?>
							<!--<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "rate_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>-->
						</div>
						<div class="row row-dense">
							<?php
								echo $ui->formField('text')
										->setLabel('Amount: ')
										->setSplit('col-md-4', 'col-md-7')
										->setName('newamount')
										->setId('newamount')
										->setClass("text-right")
										->setAttribute(array("maxlength" => "20", "onClick" => 
										"SelectAll(this.id);", "onBlur" => 
										"computeExchangeRate('rateForm',this.id); formatNumber(this.id);"))
										->setValidation('required')
										->setValue("0.00")
										->draw($show_input);
							?>
							<!--<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "newamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>-->
						</div>
						<div class="row row-dense">
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
								<button type="button" class="btn btn-primary btn-flat" 
								id="btnProceed" >Apply</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<a href="javascript:void(0);" class="btn btn-small btn-default btn-flat" 
								role="button" data-dismiss="modal" style="outline:none;">
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
				Are you sure you want to delete this record?
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
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No
							</button>
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
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No
							</button>
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
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
						&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class="well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Amount: ')
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
							<span class="help-block hidden small req-color" id = "paymentoldamount_help">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Currency Rate: ')
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
							<span class="help-block hidden small req-color" id = "paymentrate_help">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Amount: ')
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
							<span class="help-block hidden small req-color" id = "paymentnewamount_help">
							<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>

							<span class="help-block hidden small req-color" id = "exrateamount_help">
							<i class="glyphicon glyphicon-exclamation-sign"></i>Amount Exceeded Total Balance
							</span>

						</div>
					</div>

					<div class="row row-dense">
						<div class="col-md-12 text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnProceed" >
								Apply</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<a href="javascript:void(0);" class="btn btn-small btn-default btn-flat" 
								role="button" data-dismiss="modal" style="outline:none;">
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

<!--TRANSACTION LIST MODAL-->
<div class="modal fade" id="transactionListModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<div class="col-md-8 col-sm-6">
						<b>Transactions List</b>
					</div>
					
					<input class="form_iput" value="main.transactiondate" name="sort" id="sort" type="hidden">
					<input class="form_iput" value="1" name="row_num" id="row_num" type="hidden">
					<input class="form_iput" value="DESC" name="sortBy" id="sortBy" type="hidden">

					<div class="col-md-4 col-sm-5 col-xs-12">
						<div class="input-group" id="option_search">
							<div class="input-group">
								<?php
									echo $ui->formField('text')
										->setPlaceholder('Search transactions')
										->setSplit('', 'col-md-12 no-pad')
										->setName("search")
										->setId("search")
										->setAttribute(array("maxlength" => "100", "onKeyUp" => "showList();"))
										->setValue("")
										->draw($show_input);
								?>

								<div class="input-group-btn">
									<button type="button" class="btn btn-default" id="daterange-btn">
										<i class="fa fa-search"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal-body" >
				<table id = "dr_list_table" class="table table-hover table-condensed">
					<thead>
						<tr class="info" id="header">
							<th class="col-md-1 center"></th>
							<th class="col-md-2 text-center">
								<a href="javascript:void(0);" onClick="sortList('transactionListModal',
								'sort','main.transactiondate', 'sortBy');" class="link" 
								id="sort_main_transactiondate" style="outline:none;">
								Transaction Date<span class=""></span>
								</a>
							</th>
							<th class="col-md-2 text-center">
								<a href="javascript:void(0);" onClick="sortList('transactionListModal',
								'sort','main.voucherno', 'sortBy');" class="link" id="sort_main_voucherno" 
								style="outline:none;">AR Voucher No<span class=""></span></a>
							</th>

							<th class="col-md-2 text-center">
								<a href="javascript:void(0);" onClick="sortList('transactionListModal',
								'sort','main.amount', 'sortBy');" class="link" id="sort_main_voucherno" 
								style="outline:none;">Total Amount<span class=""></span></a>
							</th>

							<th class="col-md-2 text-center">
								<a href="javascript:void(0);" onClick="sortList('transactionListModal',
								'sort','main.balance', 'sortBy');" class="link" id="sort_main_balance" 
								style="outline:none;">Balance<span class=""></span></a>
							</th>

							<!--<th class="col-md-3 text-center">Amount to Pay</th>-->
						</tr>
					</thead>
					<tbody id="transactions_list_container">
						
					</tbody>
					<tfoot>	
						<tr>
							<td class="center" id="transactions_page_info">&nbsp;</td>
							<td colspan="2" class="center" id="transactions_page_links">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
			
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 right">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" 
							data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--END TRANSACTION LIST MODAL-->
<script>
var edited = false;
$('#paymentModal').on('blur', 'input', function() {
	edited = true;
});
function addCustomerToDropdown() 
{
	var optionvalue = $("#customer_modal #supplierForm #partnercode").val();
	var optiondesc 	= $("#customer_modal #supplierForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#paymentForm #customer option");
	$('#paymentForm #customer').val(optionvalue);
	
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
var id_array = [];
//
// Change event for chequeaccount
$('#chequeTable .cheque_account').on('change', function() 
{
	var val = $(this).val();
	var id 	= $(this).attr("id");
		id 	= id.replace(/[a-z\[\]]/g, '');
	
	// Get length of ar_items
	var table 		= document.getElementById('ar_items');
	var newid 		= table.rows.length;
		newid 		= parseFloat(newid);
	
	// Set value for RV Details
	var accountcode = $("#accountcode\\["+ newid +"\\]").val(val).trigger('change.select2');
});
//
// Change event for chequeamount
$('#chequeTable .chequeamount').on('change', function() 
{
	var val = $(this).val();
	var id 	= $(this).attr("id");
		id 	= id.replace(/[a-z\[\]]/g, '');

	// Get length of ar_items
	var table 		= document.getElementById('ar_items');
	var newid 		= table.rows.length;
		newid 		= parseFloat(newid);

	var chequeamount  = $("#chequeamount\\["+ id +"\\]").val();

	// Set value for RV Details
	$("#ar_items #debit\\["+ newid +"\\]").val(chequeamount);
	formatNumber("debit["+ newid +"]");
	addAmountAll('debit');
});
//
function computeDueDate()
{
	var invoice = $("#transactiondate").val();
	var terms 	= $("#customer_terms").val();
	
	if(invoice != '')
	{
		var newDate	= moment(invoice).add(terms, 'days').format("MMM DD, YYYY");
		$("#due_date").val(newDate);
	}
}
//
function getPartnerInfo(code)
{
	if(code == '' || code == 'add' || code == "none")
	{
		$("#customer_tin").val("");
		$("#customer_terms").val("");
		$("#customer_address").val("");

		bootbox.dialog({
			message: "Please select Customer.",
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

		computeDueDate();
	}
	else
	{
		$.post('<?=BASE_URL?>financials/receipt_voucher/ajax/get_value', 
				"code=" + code + "&event=getPartnerInfo", function(data) 
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
//
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
//
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

		// Set hidden values
		$('#rateForm #oldamount_').val(addCommas(oldamount.toFixed(2)));
		$('#rateForm #rate_').val(addCommas(rate.toFixed(2)));
		$('#rateForm #newamount_').val(addCommas(newamount.toFixed(2)));		

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
//
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
//
function resetChequeIds()
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
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick',
											'clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick',
											'clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); addAmounts();');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequeDelete('+x+')');
		x++;
	}
	
}
//
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
//
function setChequeZero()
{
	resetChequeIds();

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
//
/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
	var field	= $("#"+form+" #"+id).val();

	if(id.indexOf('_chosen') != -1)
	{
		var id2	= id.replace("_chosen","");
		field	= $("#"+form+" #"+id2).val();
	}

	if((field == '' || parseFloat(field) == 0) || help_block == "exrateamount_help" || field == "none" )
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');
		
		$("#"+form+" #"+help_block)
			.removeClass('hidden');
	

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
//
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
//
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

//
/**SAVE CHANGES ON EDITED FIELDS**/
function saveField(id)
{		
	var field		= document.getElementById(id);
	var customer		= document.getElementById('customer').value;

	document.getElementById('h_address1').value 	= document.getElementById('customer_address').value;
	document.getElementById('h_tinno').value 		= document.getElementById('customer_tin').value;
	document.getElementById('h_terms').value 		= document.getElementById('customer_terms').value;
	document.getElementById('h_condition').value 	= customer;
	
	if(field.readOnly == false)
	{
		$.post('<?=BASE_URL?>financials/receipt_voucher/ajax/save_data', 
				$("#customerDetailForm").serialize(), function(data) 
		{
			if(data.msg == "success")
			{
				field.readOnly 				= true;
				field.style.backgroundColor	= 'transparent';
			}
		});
	}	
}
//
/**HIGHTLIGHT CONTENT OF INPUT**/
function SelectAll(id)
{
	document.getElementById(id).focus();
	document.getElementById(id).select();
}
//
/**ENABLE EDITABLE FIELDS**/
function editField(id)
{
	var field	= document.getElementById(id);
	field.readOnly 				= false;
	field.style.backgroundColor	= '#ffffff';
	
	SelectAll(id);
}
//
/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}
//
/**COMPUTE TOTAL CHEQUE AMOUNT**/
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
	
	$("#chequeTable #totalcheques").val(addCommas(subtotal.toFixed(2)));
}
//
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
				if(active == 'paymentoldamount' && parseFloat(base) > 0 && 
				(parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					//console.log("\n1");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentoldamount' && parseFloat(rate) > 0)
				{
					//console.log("\n2");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'paymentrate' && parseFloat(amount) > 0)
				{
					//onsole.log("\n3");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentrate' && parseFloat(rate) > 0)
				{
					//console.log("\n4");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentnewamount' && parseFloat(amount) > 0 
					&& (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					//console.log("\n5");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentnewamount' && parseFloat(rate) > 0)
				{
					//console.log("\n6");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
				}
			}
			else
			{
					//console.log("sdsdsdsd");
				$('#'+activeForm+' #convertedamount').val('0.00');
				$('#'+activeForm+' #exchangerate').val('1.00');
				$('#'+activeForm+' #amount').val('0.00');
			}
		}
		else
		{
			var amount 	= $('#'+activeForm+' #oldamount').val();
			amount 		= amount.replace(/,/g,'');
			var rate 	= ($('#'+activeForm+' #rate').val() != "") ? 
						  $('#'+activeForm+' #rate').val() : "1.00";
			rate 		= rate.replace(/,/g,'');
			var base 	= $('#'+activeForm+' #newamount').val();
			base 		= base.replace(/,/g,'');

			var newamount = 0;

			if(parseFloat($('#'+activeForm+' #rate').val()) > 1)
			{
				// console.log("if");

				if(active == 'oldamount' && parseFloat(base) > 0 && 
					(parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					// console.log("a");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'oldamount' && parseFloat(rate) > 0)
				{
					// console.log("b");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'rate' && parseFloat(amount) > 0)
				{
					// console.log("c");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'rate' && parseFloat(rate) > 0)
				{
					// console.log("d");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));	
				}
				else if(active == 'newamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
				{
					// console.log("e");
					newamount = parseFloat(base) / parseFloat(amount);
					$('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'newamount' && parseFloat(rate) > 0)
				{
					// console.log("f");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));
				}
			}
			else
			{
				newamount = parseFloat(amount) * parseFloat(rate);
				$('#'+activeForm+' #newamount').val( addCommas(newamount.toFixed(2)) );

				// $('#'+activeForm+' #convertedamount').val('0.00');
				// $('#'+activeForm+' #exchangerate').val('1.00');
				// $('#'+activeForm+' #amount').val('0.00');
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

		if(active == 'chequeamount['+row+']' && parseFloat(base) > 0 
			&& (parseFloat(rate) == 0))
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
		else if(active == 'chequeconvertedamount['+row+']' && parseFloat(amount) > 0 
		&& (parseFloat(rate) == 0 || parseFloat(rate) == 1))
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
//
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

	for(i = 0; i <= chk.length; i++) 
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
//
function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}
//
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
			document.getElementById('chequeaccount['+row+']').value 	= '';

			$('#chequeaccount\\['+row+'\\]').trigger("change.select2");
			
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
			resetChequeIds();
			addAmounts();
		}
		else
		{
			document.getElementById('chequeaccount['+row+']').value 	= '';
			
			$('#chequeaccount\\['+row+'\\]').trigger("change.select2");

			document.getElementById('chequenumber['+row+']').value 		= '';
			document.getElementById('chequedate['+row+']').value 		= '<?= $date ?>';//today();
			document.getElementById('chequeamount['+row+']').value 		= '0.00';
			addAmounts();
		}
	}
}
//
function deleteItem(row)
{
	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= '<?= COMPANYCODE ?>';
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;

	//console.log("row: " + rowCount);

	var rowindex	= table.rows[row];
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'ar_details';
		var condition	= " linenum = '"+index+"' AND voucherno = '"+voucher+"'";

		if(rowCount > 2)
		{
			if(task == 'create')
			{
				//console.log("1");
				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/delete_row",{
					table:datatable,condition:condition})
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
				//console.log("2");
				table.deleteRow(row);	
				resetIds();
				addAmountAll('debit');
				addAmountAll('credit');
			}
		}
		else
		{	
			//console.log("else");
			resetIds();
			
			document.getElementById('accountcode['+row+']').value 			= '';
			document.getElementById('detailparticulars['+row+']').value 	= '';
			document.getElementById('debit['+row+']').value 				= '0.00';
			document.getElementById('credit['+row+']').value 				= '0.00';
		
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
	else
	{
		//console.log("else 2");
		if(rowCount > 2)
		{
			table.deleteRow(row);	
			resetIds();
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
}
//
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
//
/**CANCEL TRANSACTION**/
function cancelTransaction(vno)
{
	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= "<?= COMPANYCODE ?>";
	
	var datatable	= 'accountsreceivable';
	var detailtable	= 'ar_details';
	var condition	= " voucherno = '"+vno+"' AND stat = 'temporary' ";
	// var condition	= " voucherno = '"+vno+"' AND companycode = '"+companycode+"' AND stat = 'temporary' ";

	if(task == 'create')
	{	
		var data	= "table="+datatable+"&condition="+condition;
		var data2	= "table="+detailtable+"&condition="+condition;

		$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/delete_row",data)
		.done(function(data1) 
		{
			if(data1.msg == "success")
			{
				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/delete_row",data2)
				.done(function(data2) 
				{
	
					if(data2.msg == "success")
					{
						window.location.href = '<?=BASE_URL?>financials/receipt_voucher';
					}
				});
			}
		});
	}
	else
	{
		window.location.href	= "<?=BASE_URL?>financials/receipt_voucher";
	}
}
//
/**TOGGLE CHECK DATE FIELD**/
function toggleCheckInfo(val)
{
	var h_check_rows = $("#h_check_rows").html();

	if(val == 'cheque')
	{
		// Trigger click event to add new row
		$(".add-data").trigger("click");
		
		$("#receivableForm #cash_payment_details").addClass('hidden');
		$("#receivableForm #check_details").removeClass('hidden');
	}
	else
	{
		// Delete last row in details if paymentmode is cash
		var $tbody = $("#ar_items");
		var $last  = $tbody.find('tr:last');

		if(!$last.is(':first-child'))
		{
			$last.remove();
		}

		$("#receivableForm #cash_payment_details").removeClass('hidden');
		$("#receivableForm #check_details").addClass('hidden');
	}

}
//
function clearInput(id)
{
	document.getElementById(id).value = '';
}


// -------- NOT USED -----
function checkBalance_()
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
			message: "Payment amount is greater the remaining balance of this Payable.",
			title: "Warning",
			buttons: {
				success: {
					label: "Ok",
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
//
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
	
	var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) 
						+ parseFloat(totalForex);

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
							label: "Ok",
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
//
function savePaymentRow_(e,id)
{
	e.preventDefault();
	
	id 				= id.replace(/[a-z]/g, '');
	var type		= document.getElementById('type').value;

	var table 			= document.getElementById('paymentsTable');
	var paymentmode 	= document.getElementById('paymentmode[1]').value;
	var paymentamount 	= document.getElementById('convertedamount[1]').value;
	paymentamount		= paymentamount.replace(/,/g,'');

	var row 		= table.rows[id];
	var valid		= 0;
	
	/**validate payment fields**/
	valid		+= validateField('receiptForm','paymentdate\\['+id+'\\]');
	valid		+= validateField('receiptForm','paymentmode\\['+id+'\\]', 'paymentmode\\['+id+'\\]_help');

	if(paymentmode == 'cash')
	{
		if(parseFloat(Number(paymentamount)) > 0)
		{
			valid		+= validateField('receiptForm','paymentaccount\\['+id+'\\]', 'paymentaccount\\['+id+'\\]_help');
		}
	}
	else
	{
		valid	+= validateCheques();
		valid	+= totalPaymentGreaterThanChequeAmount();
	}
	
	valid		+= validateField('receiptForm','convertedamount\\['+id+'\\]', 
					'convertedamount\\['+id+'\\]_help');
	
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
		var paymentcustomer			= $('#receiptForm #customer\\['+id+'\\]').val();
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
		var selectedcustomer		= [];
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
		
		if(paymentmode == 'cash')
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
	
		$.post("<?= BASE_URL ?>financials/receipt_voucher/ajax/apply_payments",
		{
			// "type": type, 
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
			"customer[]": paymentcustomer,
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

// ---------- NOT USED --------
function validateCheques_()
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

//
 /**LOAD CHEQUES**/
function loadCheques_(i)
{
	var cheques 		= $('#receivableForm #chequeInput'+i).val();

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

//
/**EDIT RECEIVED PAYMENTS**/
function editPaymentRow(e,id, arvoucherno)
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
//
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
//
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
		row.cells[3].getElementsByTagName("input")[0].id 	= 'paymentreference'+x;
		row.cells[3].getElementsByTagName("input")[1].id 	= 'paymentcheckdate'+x;
			
		if(wtax != '')
		{
			row.cells[4].getElementsByTagName("select")[0].id 	= 'paymentaccount'+x;
			
			row.cells[5].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[5].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[5].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;	
		}
		else
		{
			row.cells[4].getElementsByTagName("select")[0].id 	= 'paymentaccount'+x;
			
			row.cells[5].getElementsByTagName("select")[0].id 	= 'paymenttaxcode'+x;
			row.cells[6].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[6].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[6].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;
		}
		
		row.cells[0].getElementsByTagName("input")[0].name 	= '';
		row.cells[0].getElementsByTagName("input")[1].name 	= '';
		row.cells[1].getElementsByTagName("select")[0].name = '';
		row.cells[3].getElementsByTagName("input")[0].name 	= '';
		row.cells[3].getElementsByTagName("input")[1].name 	= '';
		
		if(wtax != '')
		{
			row.cells[4].getElementsByTagName("select")[0].name = '';
			row.cells[5].getElementsByTagName("input")[0].name 	= '';
			row.cells[5].getElementsByTagName("input")[1].name 	= '';
			row.cells[5].getElementsByTagName("input")[2].name 	= '';
		}
		else
		{
			row.cells[4].getElementsByTagName("select")[0].name = '';
			row.cells[5].getElementsByTagName("select")[0].name = '';
			row.cells[6].getElementsByTagName("input")[0].name 	= '';
			row.cells[6].getElementsByTagName("input")[1].name 	= '';
			row.cells[6].getElementsByTagName("input")[2].name 	= '';
		}
	}
}
//
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
//
function clearPayment()
{	

	var today	= moment().format("MMM D, YYYY");

	clearInput("total_payment");
	
	$("#receivableForm #paymentdate").val(today);
	$("#receivableForm #paymentmode").val('cash');
	toggleCheckInfo('cash');
	$("#receivableForm #paymentcheckdate").val('');

}
//
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
					var link 	 		= '<?= BASE_URL ?>financials/receipt_voucher/generateCheck/'
											+paymentvoucher+'/'+chequeno;
					window.open(link);
				}
			},
			voucher: {
			label: "Cheque with Voucher",
			className: "btn-success btn-flat",
			callback: function(result) {
					var link 	 		= '<?= BASE_URL ?>financials/receipt_voucher/generateCheckVoucher/'
											+paymentvoucher+'/'+chequeno+'/rv';
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
//
// ------------ ADDED ----------
function sortList(form, input, sortby, orderby)
{
	var name_sort    = "#" + form + " #" + input;
	var name_orderby = "#" + form + " #" + orderby;

	$(name_sort).val(sortby);

	if($(name_orderby).val() == "DESC")
		$(name_orderby).val("ASC");
	else
		$(name_orderby).val("DESC");

	showList();
}
//
function showList(voucherno)
{
	var valid		= 0;

	var	customer_code	= $('#receivableForm #customer').val();
	voucherno 		= (voucherno == undefined) ? "" : voucherno;

	valid			+= validateField('receivableForm','customer', "customer_help");

	if(valid == 0)
	{
		$.post("<?= BASE_URL ?>financials/receipt_voucher/ajax/load_receivables", 
				"customer=" + customer_code + "&voucherno=" + voucherno + '&task=<?=$task?>')
		.done(function( data ) 
		{
			if ( ! edited) {
				$('#paymentModal #receivable_list_container').html(data.table);
			}
			
			if('<?= $task ?>' == "edit" && !edited)
				$("#paymentModal #h_check_rows").html(data.json_encode);

			if(!($("paymentModal").data('bs.modal') || {isShown: false}).isShown)
			{
				var check_rows = $('#paymentModal #h_check_rows').html();

				var obj = (check_rows != "") ? JSON.parse(check_rows) : 0;

				for(var i = 0; i < obj.length; i++)
				{
					// console.log("row : " + obj[i]["row"]);

					$('input#row_check' + obj[i]["row"]).iCheck('check');
				} 

				$('#paymentModal').modal('show');
			};
		});
	}
	else if(valid != 0)
	{
		bootbox.dialog({
			message: "Please select Customer.",
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
	}

}

function showReceivePayment()
{
	var valid		= 0;
	var	customer_code	= $('#receivableForm #customer').val();
	var h_voucher_no = ('<?= $task ?>' == "edit") ? $("#receivableForm #h_voucher_no").val() : "";

	valid			+= validateField('receivableForm','customer', "customer_help");

	if(valid == 0 && customer_code != "none")
	{
		// Call showList to display receivable of chosen customer
		showList(h_voucher_no);
	}
	else
	{
		bootbox.dialog({
			message: "Please select Customer.",
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
	}

}
//
/**COMPUTE TOTAL PAYMENTS APPLIED**/
function addPaymentAmount() 
{
	var sum 		= 0;
	var subtotal 	= 0;
	
	var subData 	= 0;
	
	var table 	= document.getElementById('receivable_list_container'); // app_receivableList
	var count	= table.rows.length;

	for(i = 0; i < count; i++) 
	{  
		var inputpay = ('<?= $task ?>' == "create") ? 'paymentamount['+i+']' : 'amount_paid['+i+']';

		var inputamt	= document.getElementById(inputpay);
		
		if(document.getElementById(inputpay) != null)
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
//
function getRVDetails()
{
	var customercode   = $("#customer").val();
	var h_check_rows = ($("#h_check_rows").html() != "[]") ? 
						$("#h_check_rows").html() : "";

	var data 		 = "checkrows=" + h_check_rows + "&customer=" + customercode;

	if(h_check_rows == "")
	{
		bootbox.dialog({
			message: "Please select AR Voucher.",
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
	}
	else
	{
		$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/getrvdetails", data )
		.done(function(data)
		{
			$("#paymentModal").modal("hide");

			if(h_check_rows != "")
				$("#paymentmode").removeAttr("disabled");

			if('<?= $task ?>' == "create")
			{
				// load receivables
				$("#ar_items").html(data.table);

				// display total of credit
				addAmountAll("credit");
			}
		});
	}
}
//
function selectReceivable(id,toggle)
{
	var table 		= document.getElementById('receivable_list_container');
	
	var row   		= table.rows[id];
	var dueamount	= row.cells[4].innerHTML;
	dueamount		= dueamount.replace(/\,/g,'');
	dueamount		= parseFloat(dueamount);
	dueamount		= addCommas(dueamount.toFixed(2));

	var arvoucher 	= $("#arvoucher_modal" + id).html();

	var j 			= parseFloat(id) + 1;

	var totalamount, balance, newdue = 0;

	if(row.cells[0].getElementsByTagName("input")[0].checked)
	{
		// console.log("if");

		if(toggle == 1)
		{
			// console.log("1");

			row.cells[0].getElementsByTagName("input")[0].checked	= false;
			row.cells[5].getElementsByTagName("input")[0].disabled	= true;

			if('<?= $task ?>' == "create")
			{
				document.getElementById('paymentamount['+id+']').value	= '';
				$("#pay_amount\\["+j+"\\]").val("0.00");
			}
		}
		else
		{
			// console.log("2");

			row.cells[0].getElementsByTagName("input")[0].checked	= true;
			
			if('<?= $task ?>' == "edit")
			{
				row.cells[5].getElementsByTagName("input")[0].disabled	= false;

				// Get total amount and balance values
				totalamount		= row.cells[3].innerHTML;
				totalamount		= totalamount.replace(/\,/g,'');
				totalamount		= parseFloat(totalamount);

				balance			= row.cells[4].innerHTML;
				balance			= balance.replace(/\,/g,'');
				balance			= parseFloat(balance);

				newdue			= totalamount - balance;
				newdue			= addCommas(newdue.toFixed(2));

				document.getElementById('amount_paid['+id+']').value	= newdue;
				$("#paid_amount\\["+j+"\\]").val(newdue);
			}
			else
			{
				row.cells[5].getElementsByTagName("input")[0].disabled	= false;
				document.getElementById('paymentamount['+id+']').value	= dueamount;
				
				// console.log("due: " + dueamount);
				
				$("#pay_amount\\["+j+"\\]").val(dueamount);
			}

			$("#invoice\\["+ id +"\\]").val(arvoucher);

			addJsonData(id);
		}
	}
	else
	{
		// console.log("else");

		if(toggle == 1)
		{
			// console.log("3");
			row.cells[0].getElementsByTagName("input")[0].checked	= true;
			row.cells[5].getElementsByTagName("input")[0].disabled	= false;
			
			if('<?= $task ?>' == "create")
			{
				document.getElementById('paymentamount['+id+']').value	= dueamount;
				$("#pay_amount\\["+j+"\\]").val(dueamount);
			}
		}
		else
		{
			// console.log("4");
			
			row.cells[0].getElementsByTagName("input")[0].checked	= false;
			
			if('<?= $task ?>' == "edit")
			{
				row.cells[5].getElementsByTagName("input")[0].disabled	= true;
				document.getElementById('amount_paid['+id+']').value	= '';
				$("#paid_amount\\["+j+"\\]").val("0.00");
			}
			else
			{
				row.cells[5].getElementsByTagName("input")[0].disabled	= true;
				document.getElementById('paymentamount['+id+']').value	= '';
				$("#pay_amount\\["+j+"\\]").val("0.00");
				
			}
			
			$("#invoice\\["+ id +"\\]").val("");

			// remove id from array if unchecked
			id_array = id_array.filter(function(currentChar) 
			{
				return currentChar !== id;
			});
			
			addJsonData();
		}
	}
	
	// Get number of checkboxes and assign to textarea
	// addJsonData(id);

	addPaymentAmount();
}
//
function find_duplicate_in_array(arra1) 
{
  var i,
  len    = arra1.length,
  result = [],
  obj    = {}; 

  for (i = 0; i < len; i++)
  {
 	 obj[arra1[i]] = 0;
  }

  for (i in obj) 
  {
  	result.push(i);
  }
  	return result;
}
//
function addJsonData(id)
{
	// Get number of checkboxes and assign to textarea
	var select_array	= new Array();
	var check_count  	= $("#paymentModal input[name='checkBox[]']:checked").length;
	
	id = (id != undefined) ? id : "";

	if(id != "")
	{
		id_array.push(id);

		id_array = find_duplicate_in_array(id_array);

		id_array = id_array.filter( function( item, index, inputArray ) 
		{
           	return inputArray.indexOf(item) == index;
   		});
	}

	// console.log("id_array: " + id_array);
	
	for(var i = 0;i < id_array.length; i++)
	{
		var valuesToPush 	= {};
		
		var row				= id_array[i];
		var arvoucher		= $('#paymentModal #arvoucher_modal'+id_array[i]+'').html();
		
		if('<?= $task ?>' == "create")
		{	
			var amount 			= $('#paymentModal #paymentamount\\['+id_array[i]+'\\]').val();
		}
		else if('<?= $task ?>' == "edit")
		{
			var amount 			= $('#paymentModal #amount_paid\\['+id_array[i]+'\\]').val();
		}

		valuesToPush['row']			= row;
		valuesToPush['arvoucher']	= arvoucher;
		valuesToPush['amount']		= amount;

		select_array.push(valuesToPush);
	}

	var myJsonString = JSON.stringify(select_array);
	$('#paymentModal #h_check_rows').html(myJsonString)


	// ('<?= $task ?>' == "create") ? $('#paymentModal #h_check_rows').html(myJsonString) : $('#paymentModal #h_check_rows_edit').html(myJsonString);
}
//
/**CHECK BALANCE**/
function checkBalance(val,id)
{
	var table 		= document.getElementById('receivable_list_container'); // app_receivableList
	
	var row   		= table.rows[id];
	var dueamount	= row.cells[4].innerHTML;
	dueamount		= dueamount.replace(/\,/g,''); 

	val	= val.replace(/,/g,'');

	var j = parseFloat(id) + 1;

	// Set hidden values from modal
	var paymentamount = $("#paymentamount\\["+id+"\\]").val();
	$("#pay_amount\\["+j+"\\]").val(paymentamount);

	var condition = "";
	var input 	  = "";

	if('<?= $task ?>' == "create")
	{
		condition = parseFloat(val) > parseFloat(dueamount);

		if(condition)
			input = document.getElementById('paymentamount['+id+']').value = 0.00;
	}
	else if('<?= $task ?>' == "edit")
	{
		var balanceval 	   = $("#balance_modal" + id).html();
			balanceval 	   = balanceval.replace(/\,/g,'');

		var totalamountval = $("#totalamountval\\["+j+"\\]").val();
			totalamountval = totalamountval.replace(/\,/g,''); 
			totalamountval = parseFloat(totalamountval) + parseFloat(balanceval);

		var amountpaid = $("#amount_paid\\["+id+"\\]").val();
		$("#paid_amount\\["+j+"\\]").val(amountpaid);

		condition = parseFloat(val) > parseFloat(totalamountval);

		if(condition)
		{
			input = document.getElementById('amount_paid['+id+']').value = 0.00;
		}
	}

	// console.log(parseFloat(val) + " " + parseFloat(totalamountval));

	if(condition)
	{
		bootbox.alert("Payment amount is greater than the due amount of this Bill.", function() 
		{
			input;
			addJsonData(id);
		});
	}
	else
	{
		addJsonData(id);
	}
		

	addPaymentAmount();	
}
//
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
		$("#receivableForm #chequeCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#receivableForm #chequeCountError").addClass('hidden');
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
		$("#receivableForm #chequeAmountError").removeClass('hidden');
	}
	else
	{
		$("#receivableForm #chequeAmountError").addClass('hidden');
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
//
/**COMPARE TOTAL CHEQUE AMOUNT WITH PAYMENT**/
function totalPaymentGreaterThanChequeAmount()
{
	var total_payment	= document.getElementById('total_payment').value;
	var total_cheque	= document.getElementById('totalcheques').value;
	
	// original
	// $('#payableForm #disp_tot_payment').html(total_payment);
	// $('#payableForm #disp_tot_cheque').html(total_cheque);

	$('#receivableForm #disp_tot_cheque').html(total_payment);
	$('#receivableForm #disp_tot_payment').html(total_cheque);

	
	total_payment    	= total_payment.replace(/\,/g,'');
	total_cheque    	= total_cheque.replace(/\,/g,'');

	if(parseFloat(total_payment) == parseFloat(total_cheque))
	{
		$("#receivableForm #paymentAmountError").addClass('hidden');
		return 0;
	}
	else
	{
		$("#receivableForm #paymentAmountError").removeClass('hidden');
		return 1;
	}
}

//------------ NOT USED 
/**VALIDATE AMOUNTS IN SELECTED INVOICES**/
function validateInvoices()
{
	var table 	= document.getElementById('receivable_list_container'); //app_receivableList
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
		$("#paymentForm").modal("show");
		return 1;
	}
	else
	{
		return 0;
	}
}

// -------- NOT USED -------
// function applySelected(e)
// {
// 	e.preventDefault();
	
// 	var paymentcustomer		= $("#receivableForm #customer").val();
// 	var paymentdate			= document.getElementById('document_date').value;
// 	var paymentaccount		= document.getElementById('paymentaccount').value;
// 	var paymentmode			= document.getElementById('paymentmode').value;
// 	var paymentreference	= document.getElementById('paymentreference').value;
// 	var paymentnotes		= document.getElementById('paymentnotes').value;
// 	var voucherno 			= $("#voucherno").val();

// 	var valid				= 0;
	
// 	valid	+= validateField('receivableForm','customer', "customer_help");
// 	valid	+= validateField('paymentForm','document_date', "document_date_help");
// 	valid	+= validateField('paymentForm','paymentmode', "paymentmode_help");
	
// 	if(paymentmode == 'cash')
// 	{
// 		valid	+= validateField('paymentForm','paymentaccount', "paymentaccount_help");
// 	}
// 	else
// 	{
// 		valid	+= validateCheques();
// 		valid	+= totalPaymentGreaterThanChequeAmount();
// 	}
	
// 	valid	+= validateInvoices();
	
// 	if(valid == 0)
// 	{
// 		var table 		= document.getElementById('app_payableList');
// 		var count 		= table.rows.length;
		
// 		var selected 			= [];
// 		var selectedamount 		= [];
		
// 		var selecteddate 		= [];
// 		var selectedaccount		= [];
// 		var selectedmode		= [];
// 		var selectedreference	= [];
// 		var selectednotes		= [];
// 		var selectedvendor		= [];
		
// 		var selectedcheque		= [];
// 		var selectedchequenumber= [];
// 		var selectedchequedate	= [];
// 		var selectedchequeamount= [];
		
// 		for(var i=0;i<count;i++)
// 		{
// 			var row   = table.rows[i];
			
// 			if(row.cells[0].getElementsByTagName("input")[0].checked)
// 			{
// 				var invoiceno 		= document.getElementById('invoice['+i+']').value;
// 				var paymentamount	= document.getElementById('paymentamount['+i+']').value;
			
// 				selected.push(invoiceno);
// 				selectedamount.push(paymentamount);
				
// 				selecteddate.push(paymentdate);
// 				selectedaccount.push(paymentaccount);
// 				selectedmode.push(paymentmode);
// 				selectedreference.push(paymentreference);
// 				selectednotes.push(paymentnotes);
// 				//selectednotes.push(paymentvendor);
// 			}
// 		}
		
// 		/**Multiple Cheque payments**/
// 		var chequeTable		= document.getElementById('chequeTable');
// 		var chequeCount		= chequeTable.rows.length - 2;
		
// 		for(var j=1;j<=chequeCount;j++)
// 		{
// 			var chequeRow   = chequeTable.rows[j];
			
// 			if(document.getElementById('chequeaccount['+j+']').value != '')
// 			{
// 				var chequeaccount 	= document.getElementById('chequeaccount['+j+']').value;
// 				var chequenumber 	= document.getElementById('chequenumber['+j+']').value;
// 				var chequedate 		= document.getElementById('chequedate['+j+']').value;
// 				var chequeamount 	= document.getElementById('chequeamount['+j+']').value;
				
// 				selectedcheque.push(chequeaccount);
// 				selectedchequenumber.push(chequenumber);
// 				selectedchequedate.push(chequedate);
// 				selectedchequeamount.push(chequeamount);
// 			}
// 		}

// 		// Set hidden value
// 		$("#h_stattemp").val("temporary");
// 		var stat = $("#h_stattemp").val();

// 		$.post("<?= BASE_URL ?>financials/receipt_voucher/ajax/apply_payments",
// 		{ 
// 			"invoiceno[]": selected, 
// 			"paymentdate[]": selecteddate, 
// 			"paymentnumber[]": '', 
// 			"paymentaccount[]": selectedaccount,
// 			"paymentmode[]": selectedmode,
// 			"paymentreference[]": selectedreference,
// 			"paymentamount[]": selectedamount,
// 			"paymentnotes[]": selectednotes,
// 			"customer": paymentcustomer,
// 			"chequeaccount[]": selectedcheque,
// 			"chequenumber[]": selectedchequenumber,
// 			"chequedate[]": selectedchequedate,
// 			"chequeamount[]": selectedchequeamount,
// 			"stat" : stat,
// 			"voucherno" : voucherno
// 		}).done(function(data)
// 		{
// 			if(data.success)
// 			{
// 				// Clear inputs from payment modal
// 				$("#paymentaccount").val("").trigger("change");
// 				$("#total_payment").val("");
// 				$("#paymentreference").val("");
// 				$("#paymentnotes").val("");

// 				// $("#voucherno").val(data.voucherno);
// 				$('#paymentModal').modal('hide');

// 			}
				
// 		});
// 	}
// }
//
function applySelected_()
{
	// e.preventDefault();
	
	var paymentcustomer		= $("#receivableForm #customer").val();
	var paymentdate			= document.getElementById('document_date').value;
	var paymentmode			= document.getElementById('paymentmode').value;
	var paymentreference	= document.getElementById('paymentreference').value;
	var voucherno 			= $("#voucherno").val();

	var valid				= 0;
	
	valid	+= validateField('receivableForm','customer', "customer_help");
	valid	+= validateField('receivableForm','document_date', "document_date_help");
	valid	+= validateField('receivableForm','paymentmode', "paymentmode_help");
	
	if(paymentmode == 'cheque')
	{
		valid	+= validateCheques();
		valid	+= totalPaymentGreaterThanChequeAmount();
	}
}
//
function getPayments(voucherno)
{
	$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/get_payments", "voucherno=" + voucherno)
	.done(function(data)
	{	
		$("#issue_payment").html(data.list);

		$("#totalPaymentCaption").html(data.totalPayment);
		$("#totalPayment").val(data.totalPayment);
		
		$("#totalDiscountCaption").html(data.totaldiscount);
		$("#totalDiscount").val(data.totaldiscount);

		$("#totalForex").val(data.totalForex);
		
		$("#voucherno").val("");
	});
}

//
/**EDIT RECIEVED PAYMENTS**/
function editPaymentRow(e,id, arvoucherno, voucherno)
{
	e.preventDefault();

	row 					= id.replace(/[a-z]/g, '');
	
	var paymentmode			= $("#paymentsTable #pmode" + row).val(); //document.getElementById('pmode'+row).value;
	paymentmode 			= paymentmode.toLowerCase();

	// var paymentdate			= //document.getElementById('paymentdate'+row).value;
	var paymentreference	= $("#paymentsTable #paymentreference" + row).val(); //document.getElementById('paymentreference'+row).value;
	var paymentcheckdate	= $("#paymentsTable #paymentcheckdate" + row).val(); //document.getElementById('paymentcheckdate'+row).value;
	var paymentamount		= $("#paymentsTable #paymentamount" + row).val(); //document.getElementById('paymentamount'+row).value;
	var paymentconverted	= $("#paymentsTable #paymentconverted" + row).val(); //document.getElementById('paymentconverted'+row).value;
	var paymentrate			= $("#paymentsTable #paymentrate" + row).val(); //document.getElementById('paymentrate'+row).value;
	var paymentnumber		= $("#paymentsTable #paymentnumber" + row).val(); //document.getElementById('paymentnumber'+row).value;
	var paymentaccount		= $("#paymentsTable #paymentaccount" + row).val(); //document.getElementById('paymentaccount'+row).value;
	var paymentnotes		= $("#paymentsTable #paymentnotes" + row).val(); //document.getElementById('paymentnotes'+row).value;
	var paymentdiscount		= $("#paymentsTable #paymentdiscount" + row).val(); //document.getElementById('paymentdiscount'+row).value;

	// Set Values of Receive Payment Modal
	$("#paymentForm #paymentmode").val(paymentmode).trigger("change");
	$("#paymentForm #paymentaccount").val(paymentaccount).trigger("change");
	$("#paymentForm #paymentreference").val(paymentreference);
	$("#paymentForm #total_payment").val(paymentamount);
	$("#paymentForm #paymentnotes").val(paymentnotes);
	
	showList(voucherno);

	$("#voucherno").val(voucherno);
}

//
function loadCheques(i)
{
	var cheques 		= $('#receivableForm #rollArray').val();

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
			// var chequeconvertedamount	= arr_from_json[x]['chequeconvertedamount'];

			$('#receivableForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

			$('#receivableForm #chequenumber\\['+row+'\\]').val(chequenumber);
			$('#receivableForm #chequedate\\['+row+'\\]').val(chequedate);
			$('#receivableForm #chequeamount\\['+row+'\\]').val(chequeamount);
			// $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(chequeconvertedamount);

			/**Add new row based on number of rolls**/
			if(row != arr_len)
			{
				$('body .add-cheque').trigger('click');
			}
			// $('#receiptForm #'+row).addClass('disabled');

			$('#receivableForm #checkprint\\['+row+'\\]').removeClass('hidden');
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

$(document).ready(function() 
{
	// Call toggleExchangeRate
	$( "#exchange_rate" ).click(function() 
	{
		toggleExchangeRate();
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
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({autoclose: true});

		// Trigger add new line .add-data
		$(".add-data").trigger("click");

	});	

	/**
	* Apply Exchange Rate and converted amount
	*/
	$('#rateForm #btnProceed').click(function(e)
	{
		console.log(e);

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
		
		// Validation
		$("#rateForm").find('.form-group').find('input, textarea, select').trigger('focus');
		valid 	+= $("#rateForm").find('.form-group.has-error').length;

		if(valid == 0)
		{
			if(parseFloat(amount) == 0)
			{
				if(accountentry == '')
				{
					$.post('<?=BASE_URL?>financials/receiptvoucher/ajax/get_value', 
					"account=" + account + "&event=exchange_rate")
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
									$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/get_value",
									data)
									.done(function(response) 
									{
										var accountnature		= data.accountnature;

										$('#btnRate').html(exchangerate+'&nbsp;&nbsp;');

										$('#receivableForm #amount').val(oldamount);
										$('#receivableForm #exchangerate').val(exchangerate);
										$('#receivableForm #convertedamount').val(newamount);

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
						label: "Ok",
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

		$.post("<?= BASE_URL?>financials/receipt_voucher/ajax/delete_payments", "voucher=" + id)
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

	$('#paymentreference').on('blur', function(e) 
	{
		if($("#paymentmode").val() == "cheque")
			validateField('paymentForm', e.target.id, e.target.id + "_help");
	});

	// Process New Transaction
	if('<?= $task ?>' == "create")
	{
		/**SAVE TEMPORARY DATA THROUGH AJAX**/
		$("#receivableForm").change(function()
		{
			if( $("#itemsTable #accountcode\\[1\\]").val() != '' && 
				$("#receivableForm #document_date").val() != '' && 
				(parseFloat($("#itemsTable #debit\\[1\\]").val()) > 0 || 
				 parseFloat($("#itemsTable #credit\\[1\\]").val()) > 0) && 
				(parseFloat($("#itemsTable #debit\\[2\\]").val()) > 0 || 
				parseFloat($("#itemsTable #credit\\[2\\]").val()) > 0) && 
				$("#receivableForm #customer").val() != '' )
			{
				// validate form
				// applySelected_();

				setTimeout(function() 
				{
					$("#salesForm #btnSave").addClass('disabled');
					$("#salesForm #btnSave_toggle").addClass('disabled');
					
					$("#salesForm #btnSave").html('Saving...');
					
					$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
						$("#receivableForm").serialize())
					.done(function(data)
					{	
						// if(data.success)
						// {
						// 	$("#receivableForm #btnSave").removeClass('disabled');
						// 	$("#receivableForm #btnSave_toggle").removeClass('disabled');
						
						// 	$("#receivableForm #btnSave").html('Save');
						// }
						// else
						// 	console.log("else");	
					});
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO LIST**/
		$("#receivableForm #btnSave").click(function()
		{
			var valid	= 0;
			
			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			/**validate items**/
			valid		+= validateDetails();

			if(valid == 0)
			{
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save").val("h_save");
				$("#h_check_rows_").val($("#h_check_rows").val());

				// validate form
				applySelected_();

				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
					$("#receivableForm").serialize())
				.done(function(data)
				{	
					if(data.success)
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

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
					}
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;

			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();

			if(valid == 0)
			{
				// validate form
				applySelected_();

				$("#receivableForm #h_save_new").val("h_save_new");
				$("#h_check_rows_").val($("#h_check_rows").val());

				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
				$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.success)
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

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
					}
					
				});

			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO PREVIEW INVOICE**/
		$("#receivableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','duedate', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				// validate form
				applySelected_();

				$("#receivableForm #h_save_preview").val("h_save_preview");
				$("#h_check_rows_").val($("#h_check_rows").val());

				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
				$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.success)
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

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
					}
				});
			}
		});

	}
	else if('<?= $task ?>' == "edit") 
	{
		var paymentmode = $("#paymentmode").val();

		if(paymentmode == "cheque")
		{
			toggleCheckInfo(paymentmode);
			loadCheques();
		}

		$("#paymentmode").removeAttr("disabled");

		/**SAVE CHANGES AND REDIRECT TO LIST**/
		$("#receivableForm #btnSave").click(function(e)
		{
			var valid	= 0;

			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				// validate form
				applySelected_();

				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save").val("h_save");
				$("#h_check_rows_").val($("#h_check_rows").val());
				
				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
				$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.success)
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

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
					}
				});
			}
		});
			
		/**SAVE CHANGES AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;
			
			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				// validate form
				applySelected_();
				
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save_new").val("h_save_new");
				$("#h_check_rows_").val($("#h_check_rows").val());
				
				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
					$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.success)
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

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
					}
				});
			}
		});
			
		$("#receivableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','duedate', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				// validate form
				applySelected_();

				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save_preview").val("h_save_preview");
				$("#h_check_rows_").val($("#h_check_rows").val());
				
				$.post("<?=BASE_URL?>financials/receipt_voucher/ajax/apply_payments",
				$("#receivableForm").serialize())
				.done(function( data ) 
				{
					if(data.success)
					{
						$("#payableForm").submit();
					}
					else
					{
						var msg = "";

						for(var i = 0; i < data.msg.length; i++)
						{
							msg += data.msg[i];
						}

						$("#errordiv").removeClass("hidden");
						$("#errordiv #msg_error ul").html(msg);
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
				location.href	= "<?= BASE_URL ?>financials/receivable";//'index.php?mod=financials&type=accounts_payable';
			});
		}
	}
	else
	{
		$("#receiptForm").addClass('hidden');
	}

	$('#app_receivableList').on('ifToggled', '.icheckbox', function(event)
	{
		event.type = "checked";
		var selectid = $(this).attr('row');
		var selecttoggleid = $(this).attr('toggleid');
		
		selectReceivable(selectid,selecttoggleid);
	});

}); // end

</script>