<style>
	#vendorDetails2 .col-md-3 > .form-group {
		margin: 0;
	}

	#vendorDetails2 .col-md-2 > .form-group {
		margin: 0;
	}

	.text-right {
		text-align: right;
	}

	.text-bold {
		font-weight: bold;
		color: #fff;
	}

	.remove-margin > .form-group {
		margin-bottom: 0;
	}

	.vendor_div > .form-group {
		margin-bottom: 5px;
	}

	.req-color {
		color: #a94442;
	}

	.width35 {
		width: 35%;
	}

	.width27 {
		width: 27%;
	}

	#msg_error {
		font-weight: bold;
	}

</style>

<section class="content">

	<form id = "vendorDetailForm">
		<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
		<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
		<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
		<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
		<input class="form_iput"   value = "" name = "h_condition" id = "h_condition" type="hidden">
		<input class = "form_iput" value = "vendordetail" name = "h_form" id = "h_form" type="hidden">
	</form>

	<div id = "diverror" class="alert alert-warning alert-dismissible hidden">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<h4><i class="icon fa fa-warning"></i>The system has encountered the following error/s!</h4>
		<div id = "msg_error">
			<ul class = "text-bold">

			</ul>
		</div>
		<p class = "text-bold">Please contact admin to fix this issue.</p>
	</div>

	<form method = "post" class="form-horizontal" id = "paymentForm">
		<input class = "form_iput" value = "0.00" name = "h_amount" id = "h_amount" type="hidden">
		<input class = "form_iput" value = "0.00" name = "h_convertedamount" id = "h_convertedamount" type = "hidden">
		<input class = "form_iput" value = "1.00" name = "h_exchangerate" id = "h_exchangerate" type = "hidden">
		
		<?php if($task == "view") { ?> 

			<input class = "form_iput" value = "<?= $sid ?>" name = "sid" id = "sid" type="hidden">

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
										<button type="button" class = "btn btn-primary btn-flat" id="btnYes">Yes</button>
									</div>
										&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class = "btn btn-default btn-flat" data-dismiss="modal">No</button>
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
								<h2><strong>Disbursement Voucher</strong> <small><?='('.$voucherno.')'?></small></h2>
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
										<h4>Notes</h4>
									</div>
									<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
										<h4>: <strong><?=$particulars;?></strong></h4>
									</div>
								</div>
							</div>
						</div>

						<div class = "row">
							<div class="col-md-8 col-sm-8 col-xs-8">
								<h4>Vendor :</h4>
								<h4><strong><?=$vendor?></strong></h4>
								<div class="row">
									<div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
										Email
									</div>
									<div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
										: <?=$email?>
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

						<!-- Cheque Details -->
						<? if($paymenttype == "cheque") { ?> 
							<div class = "panel panel-default">
								<div class="panel-heading">
									<strong>Cheque Details</strong>
								</div>

								<div class="box-body table-responsive no-pad">
									<table class="table table-hover">
										<thead>
											<tr class="info">
												<th class="col-md-2 text-center">Cheque Date</th>
												<th class="col-md-2 text-center">Bank Account</th>
												<th class="col-md-3 text-center">Cheque Number</th>
												<th class="col-md-2 text-center">Amount</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$total_cheque_amount 	= 0;

												if(!is_null($data["rollArrayv"]) && !empty($sid))
												{
													for($i = 0; $i < count($data["rollArrayv"][$sid]); $i++)
													{
														$accountname	= $data["rollArrayv"][$sid][$i]["accountname"];
														$chequenumber	= $data["rollArrayv"][$sid][$i]["chequenumber"];
														$chequedate		= $data["rollArrayv"][$sid][$i]["chequedate"];
														$chequeamount	= $data["rollArrayv"][$sid][$i]["chequeamount"];
														
														echo '<tr>';	
															echo '<td class="text-right">'.$chequedate.'</td>';
															echo '<td class="text-right">'.$accountname.'</td>';
															echo '<td class="text-right">'.$chequenumber.'</td>';
															echo '<td class="text-right">'.number_format($chequeamount,2).'</td>';
														echo '</tr>';
														
														$total_cheque_amount += $chequeamount;
													}
												}
											?>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="3" class="text-right" style="border-top:1px solid #DDDDDD;">
													<strong>Total</strong>
												</td>
												<td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_cheque_amount,2)?></strong></td>
												<!--<td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_credit,2)?></strong></td>-->
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						<? } ?>
						<!-- Cheque Details END-->

						<!--PAYMENT ISSUED-->
						<div class = "table-responsive">
							<div class="panel panel-default">
								<div class="panel-heading">
									<strong>Issued Payments</strong>
								</div>
								<table class="table table-striped table-condensed table-bordered" id="paymentsTable">
									<thead>
										<tr class="info">
											<th class="col-md-1 text-center">Date</th>
											<th class="col-md-1 text-center">Mode</th>
											<th class="col-md-2 text-center">Reference</th>
											<th class="col-md-2 text-center">Payment Account</th>
											<th class="col-md-2 text-center">Amount</th>
											<!--<th class="col-md-2 text-center">Discount</th>-->
											<th class="col-md-1 text-center">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$totalPayment	= 0;
											$totaldiscount	= 0;
											$row 			= 1;
											$rollArray 		= $data["rollArray"];

											if(!is_null($data["payments"]) && !empty($data["payments"]))
											{
												for($i = 0; $i < count($data["payments"]); $i++)
												{
													$paymentnumber		= $data["payments"][$i]->voucherno;
													$paymentdate		= $data["payments"][$i]->transactiondate;
													$paymentdate		= $this->date->dateFormat($paymentdate);
													$paymentaccountcode	= $data["payments"][$i]->accountcode;
													$paymentaccount		= $data["payments"][$i]->accountname;
													$paymentmode		= $data["payments"][$i]->paymenttype;
													$reference			= $data["payments"][$i]->referenceno;
													$paymentamount		= $data["payments"][$i]->amount;
													$paymentstat		= $data["payments"][$i]->stat;
													$paymentcheckdate	= $data["payments"][$i]->checkdate;
													$paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? $this->date->dateFormat($paymentcheckdate) : "";
													$paymentatccode		= $data["payments"][$i]->atcCode;
													$paymentnotes		= $data["payments"][$i]->particulars;
													$checkstat			= $data["payments"][$i]->checkstat;

													$paymentdiscount	= $data["payments"][$i]->discount;
													$paymentrate		= (isset($data["payments"][$i]->exchangerate) && !empty($data["payments"][$i]->exchangerate)) ? $data["payments"][$i]->exchangerate : 1;
													$paymentconverted	= (isset($data["payments"][$i]->convertedamount) && $data["payments"][$i]->convertedamount > 0) ? $data["payments"][$i]->convertedamount : $paymentamount;
													
													$cheque_values		= (!is_null($rollArray) && !empty($rollArray[$paymentnumber])) ? json_encode($rollArray[$paymentnumber]) : "";

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
															echo '<input value="'.$paymentnumber.'" name = "paymentnumber'.$row.'" id = "paymentnumber'.$row.'" type = "hidden">';
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

															echo '<td>';
															echo $ui->formField('text')
																	->setSplit('', 'col-md-12 no-pad')
																	->setClass("input_label")
																	->setName("paymentreference".$row)
																	->setId("paymentreference".$row)
																	->setAttribute(array("readonly" => "readonly"))
																	->setValue($reference)
																	->draw(true);
															echo '<input value="'.$paymentcheckdate.'" name = "paymentcheckdate'.$row.'" id = "paymentcheckdate'.$row.'" type = "hidden">';
															echo '<input value="'.$paymentnotes.'" name = "paymentnotes'.$row.'" id = "paymentnotes'.$row.'" type = "hidden">';
															echo '</td>';

															echo '<td>';
															echo $ui->formField('text')
																	->setSplit('', 'col-md-12 no-pad')
																	->setClass("input_label")
																	->setName("pacct".$row)
																	->setId("pacct".$row)
																	->setValue($paymentaccount)
																	->setAttribute(array("readonly" => "readonly"))
																	->draw(true);

															echo $ui->formField('dropdown')
																	->setSplit('', 'col-md-12 no-pad')
																	->setClass("input-sm hidden")
																	->setPlaceholder('None')
																	->setName('paymentaccount'.$row)
																	->setId('paymentaccount'.$row)
																	->setList($account_entries)
																	->setValue($paymentaccountcode)
																	->draw(true);
															
															echo '</td>';

															echo '<td>';
															echo '<input value="'.number_format($paymentamount,2).'" name = "paymentamount'.$row.'" id = "paymentamount'.$row.'" type = "hidden">';
															echo '<input value="'.number_format($paymentrate,2).'" name = "paymentrate'.$row.'" id = "paymentrate'.$row.'" type = "hidden">';
															
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

															// echo '<td>';
															// echo $ui->formField('text')
															// 		->setSplit('', 'col-md-12 no-pad')
															// 		->setClass("input_label text-right")
															// 		->setName("paymentdiscount".$row)
															// 		->setId("paymentdiscount".$row)
															// 		->setAttribute(array("readonly" => "readonly"))
															// 		->setValue(number_format($paymentdiscount,2))
															// 		->draw(true);
															// echo '</td>';

															echo '<td class="text-center">';
															echo (strtolower($checkstat) != 'cleared') ? '
																<button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
																<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/payment/print_preview/'.$sid.'" title="Print Payment Voucher"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/payment/print_preview/'.$sid.'" title="Print Payment Voucher" ><span class="glyphicon glyphicon-print"></span></a>';
															echo '</td>';

													echo '</tr>';

													$row++;

													$totalPayment += $paymentconverted;
													$totaldiscount+= $paymentdiscount;
												}
											}
											else
											{
												echo '<tr><td colspan = "7" class = "text-center">No payments issued for this payable</td></tr>';
											}

										?>
									</tbody>
									<tfoot>
										<tr>
											<td class="text-right" colspan="4" style="border-top:1px solid #DDDDDD;" >
												<label for="subtotal" class="control-label">Total </label>
											</td>
											<td style="border-top:1px solid #DDDDDD;" class="text-right">
												<label class="control-label" id="totalPaymentCaption" style = "padding: 0 12px 0 12px;"><?=number_format($totalPayment,2)?></label>
												<input class="form_iput" value="<?= $totalPayment?>" name="totalPayment" id="totalPayment" type="hidden">
											</td>
											<!--<td style="border-top:1px solid #DDDDDD;" class="text-right">
												<label class="control-label" id="totalDiscountCaption" style = "padding: 0 12px 0 12px;"><?=number_format($totaldiscount,2)?></label>

												<input class="form_iput" value="<?= $totaldiscount?>" name="totalDiscount" id="totalDiscount" type="hidden">
												<input class="form_iput" value="0.00" name="totalForex" id="totalForex" type="hidden">

												<!-- $forexamount -->
											<!-- </td>-->
											<td style="border-top:1px solid #DDDDDD;">
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
						<!--PAYMENT ISSUED: END-->

						<div class = "row">
							<div class="col-md-2 left">&nbsp;</div>
							<div class="col-md-7 text-center">
								<?if(empty($data["payments"])){?>
									<a href="<?=BASE_URL?>financials/payment/edit/<?=$sid?>" class="btn btn-primary btn-md btn-flat">Issue Payment</a>&nbsp;
								<?} else { ?> 	
									<a href="<?=BASE_URL?>financials/payment/edit/<?=$sid?>" class="btn btn-primary btn-md btn-flat">Edit</a>
								<? } ?>
							</div>
							<div class="col-md-3 text-right">
								<a href="<?=BASE_URL?>financials/payment" role="button" class="btn btn-primary btn-md btn-flat" id="btnExit" >Exit</a>
							</div>
						</div>

					</div>
				</div>
			</div>

		<? }else { ?> 
				<div class="box box-primary">
					<div class = "col-md-12">&nbsp;</div>
			
					<div class="row">
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
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($transactiondate)
										->setValidation('required')
										->draw($show_input);
							?>
						</div>
					</div>

					<div class="row">
						<div class = "col-md-6 vendor_div">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Vendor: ')
									->setPlaceholder('None')
									->setSplit('col-md-3', 'col-md-8')
									->setName('vendor')
									->setId('vendor')
									->setList($vendor_list)
									->setValue($vendor)
									->setAttribute(array("onChange" => "getPartnerInfo(this.value);"))
									->setValidation('required')
									->setButtonAddon('plus')
									->draw($show_input);
							?>
						</div>

						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
										->setLabel('Exchange Rate:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('exchange_rate')
										->setId('exchange_rate')
										->setClass("btn btn-success btn-flat text-right text-bold")
										->setAttribute(array("onClick" => "toggleExchangeRate('');"))
										->setValue("1.00")
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
										->setName('vendor_tin')
										->setId('vendor_tin')
										->setAttribute(array("maxlength" => "15", "rows" => "1"))
										->setPlaceholder("000-000-000-000")
										->setClass("input_label")
										->setAttribute(array("onKeyPress" => "return isNumberKey(event,45);", "onBlur" => "saveField(this.id);", "onClick" => "editField(this.id);"))
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
										->setName('vendor_terms')
										->setId('vendor_terms')
										->setAttribute(array("readonly" => "", "maxlength" => "15"))
										->setPlaceholder("0")
										->setClass("input_label")
										->setAttribute(array("onKeyPress" => "return isNumberKey(event,45);", "onBlur" => "saveField(this.id);", "onClick" => "editField(this.id);"))
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
										->setName('vendor_address')
										->setId('vendor_address')
										->setClass("input_label")
										->setAttribute(array("readonly" => "", "rows" => "1", "onBlur" => "saveField(this.id);", "onClick" => "editField(this.id);"))
										->setValue($address1)
										->draw($show_input);
							?>
						</div>
					</div>

					<div class="row">
						<div class = "col-md-6">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Proforma:')
									->setPlaceholder('Filter Proforma')
									->setSplit('col-md-3', 'col-md-8')
									->setName('proformacode')
									->setId('proformacode')
									->setList($proforma_list)
									->setValue($proformacode)
									->setNone('None')
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
										->setAttribute(array("onChange" => "toggleCheckInfo(this.value); validateField('paymentForm',this.id, 'paymentmode_help');"))
										->setValue($paymenttype)
										->draw($show_input);
							?>
							<div class="col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "paymentmode_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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

					<!-- Cheque Details -->
					<textarea class = "hidden" id = "rollArray">
						<?php echo (!is_null($rollArray) && !empty($rollArray[$sid])) ? json_encode($rollArray[$sid]) : ""; ?>
					</textarea>

					<div class="has-error" style = "margin-left: 10px;">
						<span id="checkNumberError" class="help-block hidden">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							The Cheque Number you entered has already been used
						</span>
					</div>

					<div class="panel panel-default hidden" id="check_details">
						<div class="panel-heading">
							<strong>Cheque Details</strong>
						</div>

						<div class="table-responsive">
							<table class="table table-condensed table-bordered table-hover" id="chequeTable">
								<thead>
									<tr class="info">
										<th class="col-md-2 text-center">Bank Account</th>
										<th class="col-md-2 text-center">Cheque Number</th>
										<th class="col-md-2 text-center">Cheque Date</th>
										<th class="col-md-2 text-center">Amount</th>
										<!--<th class="col-md-2 text-center">Converted Amount</th>-->
										<th class="col-md-1 text-center">Action</th>
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
														->setAttribute(array("maxlength" => "100", "onBlur" => "validateChequeNumber(this.id, this.value, this)"))
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
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
														->setValue("0.00")
														->draw(true);
											?>
										</td>

										<!--<td>
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("input-sm text-right")
														->setName('chequeconvertedamount[1]')
														->setId('chequeconvertedamount[1]')
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
														->setValue("0.00")
														->draw(true);
											?>
										</td>-->

										<td class="text-center">
											<!--<button type="button" class="btn btn-sm btn-success btn-flat" id="checkprint[1]" style="outline:none;" onClick="confirmChequePrint(1);" title="Print Cheque"><span class="glyphicon glyphicon-print"></span></button>
											&nbsp;-->
											<button type="button" class="btn btn-sm btn-danger btn-flat confirm-delete" name="chk[]" style="outline:none;" onClick="confirmChequeDelete(1);"><span class="glyphicon glyphicon-trash"></span></button>
										</td>
									</tr>
								</tbody>

								<tfoot>
									<tr>
										<td colspan="2">
											<a type="button" class="btn btn-sm btn-link add-cheque"  style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
										</td>
										<td class="text-right"><label class="control-label">Total</label></td>
										<td class="text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("text-right input_label")
														->setId("total_cheque")
														->setAttribute(array("readonly" => "readonly"))
														->setValue(number_format(0, 2))
														->draw(true);
											?>
										</td>
										<!--<td class="text-right">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setClass("text-right input_label")
														->setId("total_converted")
														->setValue(number_format(0, 2))
														->draw(true);
											?>
										</td>-->
									</tr>	
								</tfoot>

							</table>
						</div>
					</div>

					<!--VOUCHER DETAILS : START-->
					<div class="has-error">
						<span id="detailAccountError" class="help-block hidden">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please specify an account for the highlighted row(s).
						</span>
						<span id="detailAmountError" class="help-block hidden">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please specify a debit or credit amount for the highlighted row(s). 
						</span>
						<span id="detailTotalError" class="help-block hidden">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure total debit and total credit are equal. 
						</span>
						<span id="detailEqualError" class="help-block hidden">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure that the total amount (<strong></strong>) is equal to both total debit or total credit. 
						</span>
					</div>	
					
				</div>

				<div class="panel panel-default">
					<div class="table-responsive">
						<fieldset>
							<table class="table table-hover table-condensed " id="itemsTable">
								<thead>
									<tr class="info">
										<th class="col-md-4 text-center">Account Code</th>
										<th class="col-md-4 text-center">Description</th>
										<th class="col-md-2">Debit</th>
										<th class="col-md-2">Credit</th>
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
															->setSplit('	', 'col-md-12')
															->setName("accountcode[".$row."]")
															->setId("accountcode[".$row."]")
															->setList($account_entries)
															->setClass("disabled accountcode")
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
																->setClass('format_values_db format_values')
																->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('debit');"))
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
																->setClass("format_values_cr  format_values")
																->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('credit');"))
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
																->setSplit('	', 'col-md-12')
																->setName("accountcode[".$row."]")
																->setId("accountcode[".$row."]")
																->setList($account_entries)
																->setClass("disabled accountcode")
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
																->setClass('format_values_db format_values')
																->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('debit');"))
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
																->setClass("format_values_cr format_values")
																->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('credit');"))
																->setValue($credit)
																->draw($show_input);
													?>
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
												</td>			
											</tr>
									<?
										}
										else if(!empty($sid) && $task!='create')
										{
											$row 			= 1;
											$total_debit 	= 0;
											$total_credit 	= 0;
											$disable_debit	= '';
											$disable_credit	= '';
											
											for($i = 0; $i < count($details); $i++)
											{
												$costcenter 		= $details[$i]->costcentercode;
												$accountcode		= $details[$i]->accountcode;
												//$accountname		= $details[$i]->accountname;
												//$accountcode		= ($task != 'view') ? $accountlevel : $accountname;
												$detailparticulars	= $details[$i]->detailparticulars;
												$debit				= $details[$i]->debit;
												$credit				= $details[$i]->credit;
												
												$disable_debit		= ($task == 'edit' && ($credit > 0 && $debit == 0)) ? "true" : "false";
												$disable_credit		= ($task == 'edit' && ($debit > 0 && $credit == 0)) ? "true" : "false";		
										?>	
												<tr class="clone" valign="middle">
													<td class = "remove-margin">
														<?php
															echo $ui->formField('dropdown')
																	->setPlaceholder('Select One')
																	->setSplit('', 'col-md-12')
																	->setName("accountcode[".$row."]")
																	->setId("accountcode[".$row."]")
																	->setList($account_entries)
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
																	->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('debit');"))
																	->setClass("format_values_db format_values")
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
																	->setAttribute(array("maxlength" => "20", "onKeyPress" => "isNumberKey2(event);", "onClick" => "SelectAll(this.id);", "onBlur" => "formatNumber(this.id); addAmountAll('credit');"))
																	->setClass("format_values_cr format_values")
																	->setValue($credit)
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
														->setSplit('col-md-3', 'col-md-8')
														->setName('total_debit')
														->setId('total_debit')
														->setClass("input_label")
														->setAttribute(array("maxlength" => "40"))
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
														->setAttribute(array("maxlength" => "40"))
														->setValue(number_format($total_credit,2))
														->draw($show_input);
											?>
										</td>
									</tr>	
								</tfoot>
							</table>

						</fieldset>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 col-sm-12 text-center">
						<?php
							$save		= ($task == 'create') ? 'name="save"' : '';
							$save_new	= ($task == 'create') ? 'name="save_new"' : '';
						?>
						
						<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
						
						<?php

						if( $show_input )
						{
							$save		= ($task == 'create') ? 'name="save"' : '';
							$save_new	= ($task == 'create') ? 'name="save_new"' : '';
						?>					
							<div class="btn-group" id="save_group">
								<button type="button" id="btnSave" class="btn btn-primary btn-sm btn-flat">Save</button>
								<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm btn-flat" data-toggle="dropdown">
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
										<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_preview"/>
									</li>
								</ul>
							</div>
						<? 	
						}
						else
						{ 	
						?>
							<div class="btn-group">
								<a class="btn btn-info btn-flat" role="button" href="<?=BASE_URL?>sales/sales_order/edit/<?=$sid?>" style="outline:none;">Edit</a>
							</div>
						<?
						}
						?>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" id="btnCancel">Cancel</button>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class = "col-md-12">&nbsp;</div>
				</div>
		<? } ?>
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
										->setAttribute(array("maxlength" => "20", "onBlur" => "computeExchangeRate('rateForm',this.id); formatNumber(this.id); validateField('rateForm',this.id, 'oldamount_help');", "onClick" => "SelectAll(this.id);"))
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
										->setAttribute(array("maxlength" => "9", "onBlur" => "computeExchangeRate('rateForm',this.id); formatNumber(this.id); validateField('rateForm',this.id, 'rate_help');", "onClick" => "SelectAll(this.id);"))
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
										->setAttribute(array("maxlength" => "20", "onBlur" => "computeExchangeRate('rateForm',this.id); formatNumber(this.id); validateField('rateForm',this.id, 'rate_help');", "onClick" => "SelectAll(this.id);"))
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
									->setList($account_entries)
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

<script>
function addVendorToDropdown() 
{
	var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
	var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#paymentForm #vendor option");
	$('#paymentForm #vendor').val(optionvalue);
	
	getPartnerInfo(optionvalue);

	$('#vendor_modal').modal('hide');
	$('#vendor_modal').find("input[type=text], textarea, select").val("");
}

function closeModal()
{
	$('#vendor_modal').modal('hide');
}
$('#vendor_button').click(function() 
{
	$('#vendor_modal').modal('show');
});
</script>
<?php
	echo $ui->loadElement('modal')
		->setId('vendor_modal')
		->setContent('maintenance/supplier/create')
		->setHeader('Add a Vendor')
		->draw();
?>


<script>

var ajax = {};

var edited = false;
$('#itemsTable').on('change', 'input, select', function() 
{
	edited = true;
});

// Change event for chequeaccount
/*$('#chequeTable .cheque_account').on('change', function() 
{
	var val 		= $(this).val();
	var id 			= $(this).attr("id");
		id 			= id.replace(/[a-z\[\]]/g, '');

	accountcode = $("#accountcode\\["+ id +"\\]").val(val).trigger("change");
});
*/

/*
$('#chequeTable .cheque_account').on('change', function() 
{
	var val 		= $(this).val();
	var id 			= $(this).attr("id");
		id 			= id.replace(/[a-z\[\]]/g, '');
	
	var accountcode = "";
	
	console.log("edited: " + edited);

	if(!edited)
	{
		// Set value for DV Details
		accountcode = $("#accountcode\\["+ id +"\\]").val(val).trigger("change");
	}
	else
	{
		// Iterate all options
		$(".accountcode > option:selected").each(function() 
		{
			console.log(val + " " + this.value);
			if(val == this.value)
			{
				console.log("a");
				var selectid = $(this).parent().attr("id");
					selectid = selectid.replace(/[a-z\[\]]/g, '');

				console.log("id: " + selectid);

				// Set value for DV Details
				accountcode = $("#accountcode\\["+ selectid +"\\]").val(val).trigger("change");
			}
			else
			{
				console.log("b");
				var selectid = $(this).parent().attr("id");
					selectid = selectid.replace(/[a-z\[\]]/g, '');
				
				console.log("id: " + selectid);

				accountcode = $("#accountcode\\["+ selectid +"\\]").val();

				if(accountcode == "")
				{
					// Set value for DV Details
					accountcode = $("#accountcode\\["+ selectid +"\\]").val(val).trigger("change");
				}
				
			}
		});	
	}
});
*/

// Change event for chequeamount
/*$('#chequeTable .chequeamount').on('change', function() 
{
	var val = $(this).val();
	var id 	= $(this).attr("id");
		id 	= id.replace(/[a-z\[\]]/g, '');

	var chequeamount  = $("#chequeamount\\["+ id +"\\]").val();

	// Set value for DV Details
	$("#credit\\["+ id +"\\]").val(chequeamount);
	formatNumber("credit["+ id +"]");
	addAmountAll('credit');

});
*/

/*
$('#chequeTable .chequeamount').on('change', function() 
{
	var val = $(this).val();
	var id 	= $(this).attr("id");
		id 	= id.replace(/[a-z\[\]]/g, '');

	var chequeamount  = $("#chequeamount\\["+ id +"\\]").val();

	console.log("id: " + id);

	// Iterate all options
	$(".format_values_cr").each(function() 
	{
		var crval = this.value;
		var selectid = $(this).attr("id");
			selectid = selectid.replace(/[a-z\[\]]/g, '');

		console.log("selectid: " + selectid);

		if(crval != 0.00)
		{
			// Set value for DV Details
			$("#credit\\["+ selectid +"\\]").val(chequeamount);
			formatNumber("credit["+ selectid +"]");
			addAmountAll('credit');
		}
	});	

});
*/

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
		$.post('<?=BASE_URL?>financials/payment/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
		{
			var address		= data.address.trim();
			var tinno		= (data.tinno != null) ? data.tinno.trim() : "000-000-000-000";
			var terms		= data.terms.trim();
			
			$("#vendor_tin").val(tinno);
			$("#vendor_terms").val(terms);
			$("#vendor_address").val(address);

			computeDueDate();
		});
	}
}

function computeDueDate()
{
	var invoice = $("#transaction_date").val();
	var terms 	= $("#vendor_terms").val();
	
	if(invoice != '')
	{
		var newDate	= moment(invoice).add(terms, 'days').format("MMM DD, YYYY");
		
		// document.getElementById('duedate').value	= newDate;
		$("#due_date").val(newDate);
	}
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
		var amount 				= $('#paymentForm #h_amount').val();
		var exchangerate 		= $('#paymentForm #h_exchangerate').val();
		var convertedamount 	= $('#paymentForm #h_convertedamount').val();

		var oldamount 			= parseFloat(amount) * 1;
		var rate 				= parseFloat(exchangerate) * 1;
		var newamount 			= parseFloat(convertedamount) * 1;
	
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

		var oldamount 			= parseFloat(amount) * 1;
		var rate 				= parseFloat(exchangerate) * 1;
		var newamount 			= parseFloat(convertedamount) * 1;

		$('#paymentRateForm #paymentoldamount').val(addCommas(oldamount.toFixed(2)));
		$('#paymentRateForm #paymentrate').val(addCommas(rate.toFixed(2)));
		$('#paymentRateForm #paymentnewamount').val(addCommas(newamount.toFixed(2)));

		computeExchangeRate('paymentRateForm','paymentnewamount');

		$('#receiptForm #paymentamount\\[1\\]').val($('#paymentRateForm #paymentoldamount').val());

		$('#paymentRateModal').modal('toggle');
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
* This computes the converted amount based on the exchange rate and foreign amount
*/
function computeExchangeRate(activeForm,active,row)
{
	row = typeof row !== 'undefined' ? row : '';

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

/**COMPUTE TOTAL CHEQUE AMOUNT**/
function addAmounts() 
{
	var subconverted= 0;
	var subtotal 	= 0;
	
	var subData 			= 0;
	var subDataConverted	= 0;
	
	var table 	= document.getElementById('chequeTable');
	var count	= table.tBodies[0].rows.length;
	
	for(i = 1; i <= count; i++) 
	{  
		var inputamt		= document.getElementById('chequeamount['+i+']');
		var convertedamt	= 0; //document.getElementById('chequeconvertedamount['+i+']');
		
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

	document.getElementById('total_cheque').value 					= addCommas(subtotal.toFixed(2));
	// document.getElementById('total_converted').innerHTML 		= addCommas(subconverted.toFixed(2));

	// document.getElementById('paymentamount[1]').value 			= addCommas(subtotal.toFixed(2));
	// document.getElementById('convertedamount[1]').value 		= addCommas(subconverted.toFixed(2));
}

/**COMPUTE TOTAL AMOUNT**/
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

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
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

/**VALIDATE ITEM ROWS**/
function validateDetails()
{
	var table 			= document.getElementById('itemsTable');
	var total_debit 	= $('#total_debit').val();
	var total_credit 	= $('#total_credit').val();
	total_debit 		= total_debit.replace(/,/g,'');
	total_credit 		= total_credit.replace(/,/g,'');
	
	/**
	* Validate if total debit / credit is equal to the total amount specified
	*/
	var total_amount	= $('#paymentForm #h_convertedamount').val();
	total_amount 		= total_amount.replace(/,/g,'');
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
				$("#paymentForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid1++;
			}
			else
			{
				$("#paymentForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
			
			if(parseFloat(debit) == 0 && parseFloat(credit) == 0)
			{
				$("#paymentForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid2++;
			}
			else
			{
				$("#paymentForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
		}
		
		if(valid1 > 0)
		{
			$("#paymentForm #detailAccountError").removeClass('hidden');
		}
		else
		{
			$("#paymentForm #detailAccountError").addClass('hidden');
		}
		
		if(valid2 > 0)
		{
			$("#paymentForm #detailAmountError").removeClass('hidden');
		}
		else
		{
			$("#paymentForm #detailAmountError").addClass('hidden');
		}
		
		if(parseFloat(total_debit) != parseFloat(total_credit))
		{
			$("#paymentForm #detailTotalError").removeClass('hidden');
			valid1 = 1;
		}
		else
		{
			$("#paymentForm #detailTotalError").addClass('hidden');

			if(parseFloat(total_amount) > 0)
			{
				if(parseFloat(total_amount) != parseFloat(total_debit))
				{
					$("#paymentForm #detailEqualError strong").html(addCommas(newtotal_amount.toFixed(2)));
					$("#paymentForm #detailEqualError").removeClass('hidden');
					valid1 = 1;
				}
				else
				{
					$("#paymentForm #detailEqualError strong").html('');
					$("#paymentForm #detailEqualError").addClass('hidden');
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

/**RESET IDS OF ROWS**/
function resetIds()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.tBodies[0].rows.length;
	
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

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero()
{
	resetIds();
	
	var table 		= document.getElementById('itemsTable');
	var newid 		= table.tBodies[0].rows.length;
	
	var account		= document.getElementById('accountcode['+newid+']');
	var costcenter  = document.getElementById('costcenter['+newid+']');

	///if( account != null && costcenter != null ) 
	if( account != "" && account != null )
	{
		//document.getElementById('costcenter['+newid+']').value 			= '';
		document.getElementById('accountcode['+newid+']').value 		= '';
		document.getElementById('detailparticulars['+newid+']').value 	= '';
		document.getElementById('debit['+newid+']').value 				= '0.00';
		document.getElementById('credit['+newid+']').value 				= '0.00';
	
		document.getElementById('debit['+newid+']').readOnly 			= false;
		document.getElementById('credit['+newid+']').readOnly 			= false;
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


/** Delete Per Row **/
function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}

/**SAVE CHANGES ON EDITED FIELDS**/
function saveField(id)
{		
	var field		= document.getElementById(id);
	var vendor		= document.getElementById('vendor').value;

	document.getElementById('h_address1').value 	= document.getElementById('vendor_address').value;
	document.getElementById('h_tinno').value 		= document.getElementById('vendor_tin').value;
	document.getElementById('h_terms').value 		= document.getElementById('vendor_terms').value;
	document.getElementById('h_condition').value 	= vendor;
	
	if(field.readOnly == false)
	{
		$.post('<?=BASE_URL?>financials/payment/ajax/save_data', $("#vendorDetailForm").serialize(), function(data) 
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

/**DELETE ROW**/
function deleteItem(row)
{
	var task		= '<?= $task ?>';
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= '<?= $cmp ?>';
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.rows.length - 2;//table.tBodies[0].rows.length;
	var valid		= 1;

	var rowindex	= table.rows[row];
	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'pv_details';
		var condition	= " linenum = '"+index+"' AND voucherno = '"+voucher+"'";

		if(rowCount > 2)
		{
			if(task == 'create')
			{
				// console.log("1");
				$.post("<?=BASE_URL?>financials/payment/ajax/delete_row",{table:datatable,condition:condition})
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
		
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
	else
	{
		// console.log("else 2");
		if(rowCount > 2)
		{
			table.deleteRow(row);	
			resetIds();
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
}

/** FINALIZE SAVING **/
function finalizeTransaction()
{
	var valid	= 0;

	// $("#paymentForm").find('.form-group').find('input, textarea, select').trigger('blur');
	// valid 	+= $("#paymentForm").find('.form-group.has-error').length;
	
	/**validate items**/
	valid		+= validateDetails();
	
	var final 	= $('#save').val();

	if( valid == 0 )
	{
		$("#paymentForm #btnSave").addClass('disabled');
		$("#paymentForm #btnSave_toggle").addClass('disabled');
		
		$("#paymentForm #btnSave").html('Saving...');

		setTimeout(function() 
		{
			$.post("<?=BASE_URL?>financials/payment/ajax/save_temp_data",$("#paymentForm").serialize())
			.done(function(data)
			{	
				if(data.msg == "success")
				{
					console.log("success");
					$('#paymentForm').submit();
				}
				else
				{
					var msg = "";

					for(var i = 0; i < data.msg.length; i++)
					{
						msg += data.msg[i];
					}

					$("#diverror").removeClass("hidden");
					$("#diverror #msg_error ul").html(msg);
				}
			});

		},1000);
	}
}

/** FINALIZE EDIT TRANSACTION **/
function finalizeEditTransaction()
{
	var valid	= 0;

	/**validate vendor fields**/
	// $("#paymentForm").find('.form-group').find('input, textarea, select').trigger('blur');
	// valid 	+= $("#paymentForm").find('.form-group.has-error').length;
	
	/**validate items**/
	valid		+= validateDetails();
	
	var btn 	= $('#save').val();

	if(valid == 0)
	{	
		setTimeout(function() 
		{
			$.post("<?=BASE_URL?>financials/payment/ajax/<?=$task?>",$("#paymentForm").serialize()+'<?=$ajax_post?>',function(data)
			{	
				if(data.msg == "success")
				{
					$('#paymentForm').submit();
				}
				else
				{
					var msg = "";

					for(var i = 0; i < data.msg.length; i++)
					{
						msg += data.msg[i];
					}

					$("#diverror").removeClass("hidden");
					$("#diverror #msg_error ul").html(msg);
				}
			});

		},1000);
	}

}

/**CANCEL TRANSACTIONS**/
function cancelTransaction(vno)
{
	var voucher		= document.getElementById('h_voucher_no').value;

	$.post("<?=BASE_URL?>financials/payment/ajax/cancel", "voucher=" + voucher)
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>financials/payment';
		}
	});
}

function toggleCheckInfo(val)
{
	if(val == 'cheque')
	{
		$("#paymentForm #cash_payment_details").addClass('hidden');
		$("#paymentForm #check_details").removeClass('hidden');

		// $("fieldset, .disabled").attr("disabled", "disabled");
	}
	else
	{
		$("#paymentForm #cash_payment_details").removeClass('hidden');
		$("#paymentForm #check_details").addClass('hidden');

		$("fieldset, .disabled").removeAttr("disabled");
	}
}

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
		// row.cells[4].getElementsByTagName("input")[0].id 	= 'chequeconvertedamount['+x+']';
		
		row.cells[0].getElementsByTagName("select")[0].name = 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'chequeamount['+x+']';
		// row.cells[4].getElementsByTagName("input")[0].name 	= 'chequeconvertedamount['+x+']';
		
		row.cells[4].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); addAmounts();');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');

		// row.cells[4].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeconvertedamount['+x+']\'); computeExchangeRate(\'paymentRateForm\',\'chequeamount['+x+']\',\''+x+'\');');
		// row.cells[4].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeconvertedamount['+x+']\')');

		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequeDelete('+x+')');
		x++;
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
		document.getElementById('chequeaccount['+newid+']').value 	= '';
		document.getElementById('chequenumber['+newid+']').value 	= '';
		document.getElementById('chequeamount['+newid+']').value 	= '0.00';
		// document.getElementById('chequeconvertedamount['+newid+']').value 	= '0.00';
	}

	// Delete extra row for DV Details
	// extraid = parseFloat(newid) + 1;
	// deleteItem(extraid);
}

function confirmChequeDelete(row)
{
	var table 		= document.getElementById('chequeTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;
	var rowindex	= table.rows[row];
	
	if($('#chequeaccount\\['+row+'\\]').val() != '')
	{
		// console.log("if");

		if(rowCount > 1)
		{
			// console.log("1");

			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();

			// Call delete for DV Details
			// deleteItem(row);
			// resetIds();
			// addAmounts("debit");
			// addAmounts("credit");

		}
		else
		{	
			// console.log("2");

			document.getElementById('chequeaccount['+row+']').value 	= '';

			$('#chequeaccount\\['+row+'\\]').trigger("change");
			
			document.getElementById('chequenumber['+row+']').value 		= '';
			document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';
			document.getElementById('chequeamount['+row+']').value 		= '0.00';
			// document.getElementById('chequeconvertedamount['+row+']').value = '0.00';
			
			addAmounts();
		}
	}
	else
	{
		// console.log("else");

		if(rowCount > 1)
		{
			// console.log("3");

			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();
		}
		else
		{
			// console.log("4");

			document.getElementById('chequeaccount['+row+']').value 	= '';
			
			$('#chequeaccount\\['+row+'\\]').trigger("change");

			document.getElementById('chequenumber['+row+']').value 		= '';
			document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';
			document.getElementById('chequeamount['+row+']').value 		= '0.00';
			// document.getElementById('chequeconvertedamount['+row+']').value = '0.00';

			addAmounts();
		}
	}
}

function clearInput(id)
{
	document.getElementById(id).value = '';
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

 /**LOAD CHEQUES**/
function loadCheques(i)
{
	var cheques 		= $('#paymentForm #rollArray').val();

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

			$('#paymentForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

			$('#paymentForm #chequenumber\\['+row+'\\]').val(chequenumber);
			$('#paymentForm #chequedate\\['+row+'\\]').val(chequedate);
			$('#paymentForm #chequeamount\\['+row+'\\]').val(chequeamount);
			// $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(chequeconvertedamount);

			/**Add new row based on number of rolls**/
			if(row != arr_len)
			{
				$('body .add-cheque').trigger('click');
			}
			// $('#paymentForm #'+row).addClass('disabled');

			$('#paymentForm #checkprint\\['+row+'\\]').removeClass('hidden');

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
	/**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function() 
	{
		var invoice		= $("#sid").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');

		$.post("<?= BASE_URL?>financials/payment/ajax/delete_payments", "voucher=" + id)
		.done(function( data ) 
		{	
			if(data.msg == "success")
			{
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
	
	//Apply Exchange Rate and converted amount
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

		var amount 			= $('#paymentForm #h_amount').val();
		var accountentry	= $('#paymentForm #accountcode\\[1\\]').val();

		valid				+= validateField('rateForm','oldamount', "oldamount_help");
		valid				+= validateField('rateForm','rate', "rate_help");
		valid				+= validateField('rateForm','newamount', "newamount_help");

		if(valid == 0)
		{
			if(parseFloat(amount) == 0)
			{
				if(accountentry == '')
				{
					$.post('<?=BASE_URL?>financials/payment/ajax/get_value', "account=" + account + "&event=exchange_rate")
					.done(function(data) 
					{
						var accountnature		= data.accountnature;

						$('#exchange_rate').val(exchangerate);

						$('#paymentForm #h_amount').val(oldamount);
						$('#paymentForm #h_exchangerate').val(exchangerate);
						$('#paymentForm #h_convertedamount').val(newamount);

						$('#paymentForm #accountcode\\[1\\]').val(account);

						if(accountnature == 'Debit' || accountnature == 'debit')
						{
							$('#paymentForm #debit\\[1\\]').val($('#rateForm #newamount').val());
							$('#paymentForm #credit\\[1\\]').prop('readOnly',true);

							$('#paymentForm #credit\\[1\\]').val('0.00');
							$('#paymentForm #debit\\[1\\]').prop('readOnly',false);
							addAmountAll("debit");
						}
						else
						{
							$('#paymentForm #credit\\[1\\]').val($('#rateForm #newamount').val());
							$('#paymentForm #debit\\[1\\]').prop('readOnly',true);

							$('#paymentForm #debit\\[1\\]').val('0.00');
							$('#paymentForm #credit\\[1\\]').prop('readOnly',false);
							addAmountAll("credit");
						}

						$('#paymentForm #accountcode\\[1\\]').trigger("change");
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
							className: "btn-info btn-flat",
							callback: function(result) 
							{	
								var data = "account=" + account + "&event=exchange_rate";
								$.post("<?=BASE_URL?>financials/payment/ajax/get_value", data)
								.done(function(data) 
								{
									var accountnature		= data.accountnature;

									$('#btnRate').html(exchangerate+'&nbsp;&nbsp;');

									$('#paymentForm #h_amount').val(oldamount);
									$('#paymentForm #h_exchangerate').val(exchangerate);
									$('#paymentForm #h_convertedamount').val(newamount);

									$('#paymentForm #accountcode\\[1\\]').val(account);

									if(accountnature == 'Debit' || accountnature == 'debit')
									{
										$('#paymentForm #debit\\[1\\]').val($('#rateForm #newamount').val());
										$('#paymentForm #credit\\[1\\]').prop('readOnly',true);

										$('#paymentForm #credit\\[1\\]').val('0.00');
										$('#paymentForm #debit\\[1\\]').prop('readOnly',false);
										addAmountAll("debit");
									}
									else
									{
										$('#paymentForm #credit\\[1\\]').val($('#rateForm #newamount').val());
										$('#paymentForm #debit\\[1\\]').prop('readOnly',true);

										$('#paymentForm #debit\\[1\\]').val('0.00');
										$('#paymentForm #credit\\[1\\]').prop('readOnly',false);
										addAmountAll("credit");
									}

									$('#paymentForm #accountcode\\[1\\]').trigger("change");
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

				$('#paymentForm #h_amount').val(oldamount);
				$('#paymentForm #h_exchangerate').val(exchangerate);
				$('#paymentForm #h_convertedamount').val(newamount);

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
						label: "Ok",
						className: "btn-info",
						callback: function() {

						}
					}
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
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({autoclose: true});

		// Trigger click for .add-data
		// $(".add-data").trigger("click");
	});

	// Deletion of Row
	$('#deleteItemModal #btnYes').click(function() 
	{
		// handle deletion here
		var id = $('#deleteItemModal').data('id');

		var table 		= document.getElementById('itemsTable');
		var rowCount 	= table.tBodies[0].rows.length;

		deleteItem(id);
		
		$('#deleteItemModal').modal('hide');
	});

	// Process New Transaction
	if('<?= $task ?>' == "create")
	{
		$("#paymentForm").change(function()
		{
			if($("#paymentForm #accountcode\\[1\\]").val() != '' && $("#paymentForm #document_date").val() != '' && (parseFloat($("#paymentForm #debit\\[1\\]").val()) > 0 || parseFloat($("#paymentForm #credit\\[1\\]").val()) > 0) && $("#paymentForm #vendor").val() != '')
			{
				setTimeout(function() 
				{
					$.post("<?=BASE_URL?>financials/payment/ajax/save_temp_data",$("#paymentForm").serialize())
					.done(function(data)
					{	

					});
				},2000);
			}
		});

		//Final Saving
		$('#paymentForm #btnSave').click(function()
		{
			$('#save').val("final");

			finalizeTransaction();
		});

		//Save & Preview
		$("#paymentForm #save_preview").click(function()
		{
			$('#save').val("final_preview");

			finalizeTransaction();
		});

		//Save & New
		$("#paymentForm #save_new").click(function()
		{
			$('#save').val("final_new");

			finalizeTransaction();
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
			

		//Final Saving
		$('#paymentForm #btnSave').click(function()
		{
			$('#save').val("final");

			finalizeEditTransaction();
		});

		//Save & Preview
		$("#paymentForm #save_preview").click(function()
		{
			$('#h_save_preview').val("final_preview");

			finalizeEditTransaction();
		});

		//Save & New
		$("#paymentForm #save_new").click(function()
		{
			$('#h_save_new').val("final_new");

			finalizeEditTransaction();
		});
	}
	
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
			window.location =	"<?= BASE_URL ?>financials/payment/";
		}
	});
	// -- For Cancel -- End

	/**Applying Proforma**/
	$('#proformacode').change(function()
	{
		var code = this.value;
		
		if(code != '' && code != "none")
		{
			$.post("<?= BASE_URL ?>financials/payment/ajax/apply_proforma",{code:code})
			.done(function(data) 
			{
				var tablerow	= data.table;

				var table 		= document.getElementById('itemsTable');
				var count		= table.tBodies[0].rows.length;
				var firstaccount= $('#paymentForm #accountcode\\[1\\]').val();

				if(count > 0 && firstaccount != '')
				{
					bootbox.dialog({
						message: "Are you sure you want to apply this proforma? <br/><br/>"
						+"Applying this would overwrite the existing entries you've added.",
						title: "Confirmation",
							buttons: {
							yes: {
							label: "Yes",
							className: "btn-info btn-flat",
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
									$('#paymentForm #proformacode').val('').trigger("change");
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
		}

	});
});

</script>