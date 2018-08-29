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

	/*.req-color {
		color: #a94442;
	}*/

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
				<div class = "panel-heading">
					<form class = "form-horizontal <?= $applicationPannel ?>" method="POST" id="receiptForm">
						<input class="form_iput" value = "<?= $c_voucherno ?>" name="invoiceno[1]" id="invoiceno[1]" type="hidden">
						<input class="form_iput" value = "<?= $c_customercode ?>" name="customer[1]" id="customer[1]" type="hidden">
						<input class="form_iput" value = "accounts_receivable" name="type" id="type" type="hidden">
						<input class="form_iput" value = "<?= $c_convertedamount?>" name="totalInvoice" id="totalInvoice" type="hidden">
						<input class = "form_iput" value = "" name = "paymentrow" id = "paymentrow" type = "hidden">
						<input class = "form_iput" value = "<?= $c_exchangerate ?>" name = "payablerate" id = "payablerate" type = "hidden">
						<input class="form_iput" value="<?= $c_exchangerate ?>" name="exchangerate[1]" id="exchangerate[1]" type="hidden">
						<input class="form_iput" value="<?= $c_convertedamount ?>" name="paymentamount[1]" id="paymentamount[1]" type="hidden">

						<div class="row">
							<div class="col-md-4 col-sm-6 col-xs-6">
								<h4><strong>Account Receivable : </strong><?=$c_voucherno?></h4>
							</div>
							<label for="paymentdate[1]" class="control-label col-md-offset-4 col-md-2 col-sm-2 col-xs-4">Payment Date</label>
							<div class="col-md-2 col-sm-4 col-xs-8">
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input class="form-control input-sm datepicker" value = "<?= $v_transactiondate ?>" id="paymentdate[1]" name = "paymentdate[1]" type="text" maxlength="20">
								</div>
							</div>
						</div>
						<hr/>

						<div class="row remove-margin" id="payment">
							<div class = "col-md-6">
								<?php
									echo $ui->formField('dropdown')
											->setLabel('Payment Mode: ')
											->setSplit('col-md-3', 'col-md-4 field_col')
											->setClass("input-sm payment_mode")
											->setName('paymentmode[1]')
											->setId('paymentmode[1]')
											->setList(array("cash" => "Cash", "cheque" => "Cheque"))
											->setValue("")
											->draw(true);
								?>
								<div class="col-md-3">&nbsp;</div>
								<span class="help-block hidden small req-color" id = "paymentmode[1]_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
							</div>

							<div class = "col-md-6" id = "check_field">
								<?php
									echo $ui->formField('text')
											->setLabel('Reference Number: ')
											->setSplit('col-md-4', 'col-md-4 field_col')
											->setClass("input-sm")
											->setName('paymentreference[1]')
											->setId('paymentreference[1]')
											->setAttribute(array("maxlength" => "50"))
											->setValue("")
											->draw(true);
								?>
								<div class="col-md-3" style = "width: 17%;">&nbsp;</div>
								<span class="help-block hidden small req-color" id = "paymentreference[1]_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
							</div>

							<div class="col-md-2 col-sm-4 col-xs-8 field_col hidden" id="payment_field">

								<?php
									echo $ui->formField('text')
											->setSplit('col-md-4', 'col-md-4 field_col')
											->setClass("input-sm text-right")
											->setName('paymentamountfield')
											->setId('paymentamountfield')
											->setValue("")
											->draw(true);
								?>

								<span class="help-block hidden small" id = "paymentamountfield_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Please specify amount.</span>
							</div>

						</div>

						<div class="row" id="cash_payment_details">
							<div class = "col-md-6 remove-margin">
								<input type = "hidden" id = "prevpayment"/>
								<?php
									echo $ui->formField('text')
											->setLabel('Payment Amount: ')
											->setSplit('col-md-3', 'col-md-4 field_col')
											->setClass("input-sm pay_amount")
											->setName('convertedamount[1]')
											->setId('convertedamount[1]')
											->setAttribute(array("maxlength" => "50"))
											->setValue("")
											->draw(true);
								?>
								<div class="col-md-3" style = "width: 26%;">&nbsp;</div>
								<span class="help-block hidden small req-color" id = "convertedamount[1]_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Please specify amount.</span>

								<input class="form-control " maxlength="50" value="" name="paymentnumber[1]" id="paymentnumber[1]" type="hidden">
							</div>

							<div class = "col-md-6 remove-margin">
								<?php
									echo $ui->formField('dropdown')
											->setLabel('Paid To: ')
											->setSplit('col-md-4', 'col-md-4 field_col')
											->setClass("input-sm pay_account")
											->setPlaceholder('None')
											->setName('paymentaccount[1]')
											->setId('paymentaccount[1]')
											->setList($cash_account_list)
											->draw(true);
								?>
								<div class="col-md-4" style = "width: 35%;">&nbsp;</div>
								<span class="help-block hidden small req-color" id = "paymentaccount[1]_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Please select an account.</span>
							</div>

						</div>

						<div class="row">
							<div class = "col-md-6">
								<?php
									echo $ui->formField('text')
											->setLabel('Discount: ')
											->setSplit('col-md-3', 'col-md-4 field_col')
											->setClass("input-sm pay_discount format_values")
											->setName('paymentdiscount[1]')
											->setId('paymentdiscount[1]')
											->setPlaceHolder("0.00")
											->setValue("")
											->draw(true);
								?>
							</div>

							<div class = "col-md-6">
								<?php
									echo $ui->formField('text')
											->setLabel('Exchange Rate:')
											->setSplit('col-md-4', 'col-md-4 field_col')
											->setName('paymentexchangerate[1]')
											->setId('paymentexchangerate[1]')
											->setClass("btn btn-success btn-flat text-right text-bold payexrate")
											->setValue($c_exchangerate)
											->draw(true);
								?>
							</div>

						</div>

						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('textarea')
											->setLabel('Notes:')
											->setSplit('col-md-3', 'col-md-8')
											->setName('paymentnotes[1]')
											->setId('paymentnotes[1]')
											->setValue("")
											->draw(true);
								?>
							</div>
						</div>

						<!-- MULTIPLE CHEQUES -->
						<span id="chequeAmountError" class="text-danger hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please complete the fields on the highlighted row(s)<br/>
						</span>
						<span id="paymentAmountError" class="text-danger hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure that the total cheque amount applied (<strong id="disp_tot_cheque">0</strong>) should be equal to the total payment amount (<strong id="disp_tot_payment">0</strong>)<br/>
						</span>
						<div class="panel panel-default hidden" id="check_details">
							<div class="panel-heading">
								<strong>Cheque Details</strong>
							</div>
							<div class="table-responsive">
								<table class="table table-condensed table-bordered table-hover" id="chequeTable">
									<thead>
										<tr class="info">
											<th class="col-md-3 text-center">Bank Account</th>
											<th class="col-md-2 text-center">Cheque Number</th>
											<th class="col-md-2 text-center">Cheque Date</th>
											<th class="col-md-2 text-center">Currency Amount</th>
											<th class="col-md-2 text-center">Converted Amount</th>
											<th class="col-md-2 text-center">Action</th>
										</tr>
									</thead>
									<tbody>
										<tr class="clone">
											<td class="">
												<?php
													echo $ui->formField('dropdown')
															->setSplit('', 'col-md-12 field_col')
															->setPlaceholder('Select One')
															->setClass("input-sm test")
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
															->setClass("input-sm")
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
																->setClass("input-sm datepicker")
																->setName('chequedate[1]')
																->setId('chequedate[1]')
																->setAttribute(array("maxlength" => "50"))
																->setValue($date)
																->draw(true);
													?>
												</div>
											</td>
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12 field_col')
															->setClass("input-sm text-right chequeamount")
															->setName('chequeamount[1]')
															->setId('chequeamount[1]')
															->setAttribute(array("maxlength" => "20"))
															->setValue("0.00")
															->draw(true);
												?>
										
											</td>
											<td>
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12 field_col')
															->setClass("input-sm text-right")
															->setName('chequeconvertedamount[1]')
															->setId('chequeconvertedamount[1]')
															->setAttribute(array("maxlength" => "20"))
															->setValue("0.00")
															->draw(true);
												?>
											
											</td>
											<td class="text-center">
												<button type="button" class="btn btn-sm btn-success btn-flat hidden" id="checkprint[1]" style="outline:none;" onClick="confirmChequePrint(1);" title="Print Cheque"><span class="glyphicon glyphicon-print"></span></button>
												&nbsp;
												<button type="button" class="btn btn-sm btn-danger btn-flat confirm-delete" name="chk[]" style="outline:none;" onClick="confirmChequeDelete(1);"><span class="glyphicon glyphicon-trash"></span></button>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="2">
												<a type="button" class="btn btn-sm btn-link add-cheque" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
											</td>
											<td class="text-right"><label class="control-label">Total</label></td>
											<td class="text-right">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12 field_col')
															->setClass("text-right input_label")
															->setId("total")
															->setValue(number_format(0, 2))
															->draw(true);
												?>
											
											</td>
											<td class="text-right">
												<?php
													echo $ui->formField('text')
															->setSplit('', 'col-md-12 field_col')
															->setClass("text-right input_label")
															->setId("total_converted")
															->setValue(number_format(0, 2))
															->draw(true);
												?>
											
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<hr/>
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 text-right">
								<button type="button" class="btn btn-primary btn-sm btn-flat" onClick="savePaymentRow(event,'button1');">Save</button>
								&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default btn-sm btn-flat" onClick="clearPaymentRow(event)">Clear</button>
							</div>
						</div>

						<div class = "row">&nbsp;</div>
					</form>
				</div>
				<div class = "panel-body">
					<div class = "row">
						<div class="col-md-8 col-sm-8 col-xs-8">
							<h2><strong>Accounts Receivable</strong> <small><?='('.$c_voucherno.')'?></small></h2>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4" style="vertical-align:middle;">
							<div class="row">
								<div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
									<h4>Date</h4>
								</div>
								<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
									<h4>: <strong><?=date("M d, Y",strtotime($c_transactiondate));?></strong></h4>
								</div>
							</div>
							<div class="row">
								<div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
									<h4>Due Date</h4>
								</div>
								<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
									<h4>: <strong><?=date("M d, Y",strtotime($c_duedate));?></strong></h4>
								</div>
							</div>
							<div class="row">
								<div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
									<h4>Invoice No</h4>
								</div>
								<div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
									<h4>: <strong><?=$c_referenceno;?></strong></h4>
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
									: <?=$c_tinno?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
									Address
								</div>
								<div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
									: <?=$c_address1?>
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
					<div class = "row">
						<div class="col-md-2 col-sm-2 col-xs-2 left">
						&nbsp;
						</div>
						<div class="col-md-8 col-sm-8 col-xs-8 text-center">
							<?if($c_balance > 0){?>
							<button type="button" class="btn btn-primary btn-md btn-flat" id="btnReceive">Receive Payment</button>
							&nbsp;
							<?}?>
							
							<?if(empty($data["payments"])){?>
							<a href="<?=BASE_URL?>financials/accounts_receivable/edit/<?=$sid?>" class="btn btn-primary btn-md btn-flat">Edit</a>
							<?}?>
						</div>
						<div class="col-md-2 col-sm-2 col-xs-2 text-right">
							<a href="<?=BASE_URL?>financials/accounts_receivable" role="button" class="btn btn-primary btn-md btn-flat" id="btnExit" >Exit</a>
						</div>
					</div>

					<!--PAYMENT ISSUED-->
					<br/>
					<div class = "table-responsive">
						<div class="panel panel-default">
							<div class="panel-heading">
								<strong>Received Payments</strong>
							</div>
							<table class="table table-striped table-condensed table-bordered" id="paymentsTable">
								<thead>
									<tr class="info">
										<th class="col-md-1 text-center">Date</th>
										<th class="col-md-1 text-center">Mode</th>
										<th class="col-md-2 text-center">Reference</th>
										<th class="col-md-2 text-center">Payment Account</th>
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
									
									if(!is_null($data["payments"]) && !empty($data["payments"]))
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
											$paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? date("M d, Y",strtotime($paymentcheckdate)) : "";
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
															->draw(true);

													echo $ui->formField('dropdown')
															->setSplit('', 'col-md-12 no-pad')
															->setClass("input-sm hidden")
															->setPlaceholder('None')
															->setName('paymentaccount'.$row)
															->setId('paymentaccount'.$row)
															->setList($cash_account_list)
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
															->setValue(number_format($paymentdiscount,2))
															->draw(true);
													echo '</td>';

													echo '<td class="text-center">';
													echo (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row.'\');" title="Edit Payment" ><span class="glyphicon glyphicon-pencil"></span></button>
														<button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
														<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" title="Print Payment Voucher" onClick = "print(\''.$paymentnumber.'\');"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" onClick = "print(\''.$paymentnumber.'\');" title="Print Payment Voucher" ><span class="glyphicon glyphicon-print"></span></a>';
													echo '</td>';

											echo '</tr>';

											$row++;

											$totalPayment += $paymentconverted;
											$totaldiscount+= $paymentdiscount;
										}
										
									}
									else
									{
										echo '<tr><td colspan = "7" class = "text-center">No payments received for this receivable</td></tr>';
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
										<td style="border-top:1px solid #DDDDDD;" class="text-right">
											<label class="control-label" id="totalDiscountCaption" style = "padding: 0 12px 0 12px;"><?=number_format($totaldiscount,2)?></label>

											<input class="form_iput" value="<?= $totaldiscount?>" name="totalDiscount" id="totalDiscount" type="hidden">
											<input class="form_iput" value="<?= $forexamount?>" name="totalForex" id="totalForex" type="hidden">
										</td>
										<td style="border-top:1px solid #DDDDDD;">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<!--PAYMENT ISSUED : END-->

				</div>
			</div>
		</div>
	</div>
	<? }else { ?> 
		<div class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><strong>Error!</strong></h4>
			<div id = "errmsg"></div>
		</div>

		<div class="box box-primary">
			<form id = "customerDetailForm">
				<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
				<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
				<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
				<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
				<input class = "form_iput" value = "customerdetail" name = "h_form" id = "h_form" type="hidden">
				<input class = "form_iput"   value = "" name = "h_condition" id = "h_condition" type="hidden">
			</form>

			<form method = "post" class="form-horizontal" id = "receivableForm">

				<input class = "form_iput" value = "0.00" name = "h_amount" id = "h_amount" type="hidden">
				<input class = "form_iput" value = "0.00" name = "h_convertedamount" id = "h_convertedamount" type = "hidden">
				<input class = "form_iput" value = "1.00" name = "h_exchangerate" id = "h_exchangerate" type = "hidden">
				<input class = "form_iput" value = "<?= $task ?>" name = "h_task" id = "h_task" type = "hidden">

				<div class = "clearfix">
					<div class = "col-md-12">&nbsp;</div>
				
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
							<input type = "hidden" id = "h_voucher_no" name = "h_voucher_no" value = "<?= $generated_id ?>">
						</div>

						<div class = "col-md-6">
							<?php
								echo $ui->formField('text')
										->setLabel('Invoice No:')
										->setSplit('col-md-3', 'col-md-8')
										->setName('referenceno')
										->setId('referenceno')
										->setValue($referenceno)
										->draw($show_input);
							?>
						</div>
					</div>

					<div class = "row">
						<div class = "col-md-6 customer_div">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Customer:')
									->setPlaceholder('Filter Customer')
									->setSplit('col-md-3', 'col-md-8 field_col')
									->setName('customer')
									->setId('customer')
									->setList($customer_list)
									->setValue($customercode)
									->setAttribute(array("onChange" => "getPartnerInfo(this.value)"))
									->setNone('Choose Customer')
									->setValidation('required')
									->setButtonAddon('plus')
									->draw($show_input);
							?>
							<div class="width27 col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "customer_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Please select a customer.</span>
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
							<!--<div class="form-group">
								<label class="control-label col-md-3" for="daterangefilter">Document Date:</label>
								<div class = "col-md-8 field_col"> 
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control pull-right datepicker" value = "<?= $transactiondate ?>" id="document_date" name = "document_date" type="text">
									</div>
								</div>
							</div>
							<div class="col-md-3">&nbsp;</div>
							<span class="help-block hidden small req-color col-md-9" id = "document_date_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Please select a document date.</span>-->
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
										->setAttribute(array("maxlength" => "15", "rows" => "1"))
										->setPlaceholder("000-000-000-000")
										->setClass("input_label")
										->setValue($tinno)
										->draw($task != "view");
							?>
						</div>

						<div class = "col-md-6 remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Due Date: ')
										->setSplit('col-md-3', 'col-md-8')
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
										->setLabel('<i>Terms</i>')
										->setSplit('col-md-3', 'col-md-8')
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
										->setSplit('col-md-3', 'col-md-8')
										->setName('customer_address')
										->setId('customer_address')
										->setClass("input_label")
										->setAttribute(array("readonly" => "", "rows" => "1"))
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
									->setValue("")
									->setNone('None')
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
										->setValue($exchangerate)
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
										->setValue($particulars)
										->draw($show_input);
							?>
						</div>
					</div>

					<!--VOUCHER DETAILS : START-->
					<div class="has-error">
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
																->setAttribute(array("maxlength" => "20"))
																->setClass("format_values_cr format_values")
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
																->setAttribute(array("maxlength" => "20"))
																->setClass("format_values_cr format_values")
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

											for($i = 0; $i < count($data["details"]); $i++)
											{
												$accountlevel		= $data["details"][$i]->accountcode;
												$accountname		= $data["details"][$i]->accountname;
												$accountcode		= ($task != 'view') ? $accountlevel : $accountname;
												$detailparticulars	= $data["details"][$i]->detailparticulars;
												$debit				= $data["details"][$i]->debit;
												$credit				= $data["details"][$i]->credit;
												
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
																	->setAttribute(array("maxlength" => "20"))
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
																	->setAttribute(array("maxlength" => "20"))
																	->setClass("format_values_cr format_values")
																	->setValue($credit)
																	->draw($show_input);
														?></td>
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
							<div class="btn-group" id="save_group">

								<input type = "button" value = "Save" name = "save" id = "btnSave" class="btn btn-primary btn-sm btn-flat"/>
								<input type = "hidden" value = "" name = "h_save" id = "h_save"/>

								<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm btn-flat" data-toggle="dropdown">
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
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" id="btnCancel">Cancel</button>
							</div>
						</div>
					</div>
					<div class = "col-md-12">&nbsp;</div>
				</div>	
			</form>
		</div>
    <? } ?>
</section>

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
					<input class = "form_iput" value = "insert" name = "h_querytype" id="h_querytype" type="hidden">
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
							<span class="help-block hidden small req-color" id = "customer_name_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
									->setLabel('Business Type: ')
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
										->setLabel('Currency Amount: ')
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
										->setLabel('Currency Rate: ')
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
										->setLabel('Amount: ')
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
							<span class="help-block hidden small req-color" id = "paymentoldamount_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
										->setValue(number_format($c_exchangerate, 2))
										->draw(true);
							?>
							<div class="width35 col-md-4">&nbsp;</div>
							<span class="help-block hidden small req-color" id = "paymentrate_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
function addVendorToDropdown() 
{
	var optionvalue = $("#customer_modal #customerForm #partnercode").val();
	var optiondesc 	= $("#customer_modal #customerForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#payableForm #customer option");
	$('#payableForm #customer').val(optionvalue);
	
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
		->setContent('maintenance/customer/create')
		->setHeader('Add a Customer')
		->draw();
?>

<script type="text/javascript">

var ajax = {};
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
			var tinno		= data.tinno.trim();
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
		if(type == 'customer')
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

		row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequePrint('+x+')');
		row.cells[5].getElementsByTagName("button")[1].setAttribute('onClick','confirmChequeDelete('+x+')');
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

		if($("#"+form+" .row-dense").next(".help-block")[0])
		{
			$("#"+form+" #"+help_block)

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
	var vendor		= document.getElementById('customer').value;

	document.getElementById('h_address1').value 	= document.getElementById('customer_address').value;
	document.getElementById('h_tinno').value 		= document.getElementById('customer_tin').value;
	document.getElementById('h_terms').value 		= document.getElementById('customer_terms').value;
	document.getElementById('h_condition').value 	= vendor;
	
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
					//console.log("\n3");
					newamount = parseFloat(amount) * parseFloat(rate);
					$('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentrate' && parseFloat(rate) > 0)
				{
					//console.log("\n4");
					newamount = parseFloat(base) / parseFloat(rate);
					$('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
				}
				else if(active == 'paymentnewamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
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

		if(active == 'chequeamount['+row+']' && parseFloat(base) > 0 && (parseFloat(rate) == 0))
		{
			// console.log("1");
			newamount = parseFloat(base) / parseFloat(amount);
			$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

		}
		else if(active == 'chequeamount['+row+']' && parseFloat(rate) > 0)
		{
			// console.log("\n 2");
			newamount = parseFloat(amount) * parseFloat(rate);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	
		}
		else if(active == 'paymentrate' && parseFloat(amount) > 0)
		{
			// console.log("\n 3");
			newamount = parseFloat(amount) * parseFloat(rate);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));
		}
		else if(active == 'paymentrate' && parseFloat(rate) > 0)
		{
			// console.log("\n 4");
			newamount = parseFloat(base) / parseFloat(rate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	

		}
		else if(active == 'chequeconvertedamount['+row+']' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1))
		{
			// console.log("\n 5");
			newamount = parseFloat(base) / parseFloat(amount);
			$('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

		}
		else if(active == 'chequeconvertedamount['+row+']' && parseFloat(rate) > 0)
		{
			// console.log("\n 6");
			newamount = parseFloat(base) / parseFloat(rate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	

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

		if(rowCount > 2)
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
			message: "Payment amount is greater the remaining balance of this Payable.",
			title: "Warning",
			buttons: {
				success: {
					label: "Ok",
					className: "btn-info btn-flat",
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
							label: "Ok",
							className: "btn-info btn-flat",
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
	
	valid		+= validateField('receiptForm','convertedamount\\['+id+'\\]', 'convertedamount\\['+id+'\\]_help');
	
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
	
		$.post("<?= BASE_URL ?>financials/accounts_receivable/ajax/apply_payments",
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

function print(pv_voucherno)
{
	$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/print", "rv_voucherno=" + rv_voucherno)
	.done(function(data)
	{
		
	});
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
			className: "btn-info btn-flat",
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

$(document).ready(function() 
{
	// Date picker
    $('.datepicker, .datepicker_').datepicker({
      autoclose: true
    });

	// Set default date to date of the day
	$(".datepicker").datepicker("setDate", new Date());

	// Get getPartnerInfo
	$( "#customer" ).change(function() 
	{
		$customer_id = $("#customer").val();

		if( $customer_id != "" )
  			getPartnerInfo($customer_id);
	});

	// Call toggleExchangeRate
	$( "#exchange_rate" ).click(function() 
	{
		toggleExchangeRate();
	});

	// Add new Customer
	$("#newCustomer #customerBtnSave").click(function()
	{
		var valid	= 0;

		/**validate vendor fields**/
		valid		+= validateField('newCustomer','partnercode', "partnercode_help");
		valid		+= validateField('newCustomer','vendor_name', "vendor_name_help");
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
					$("#customerAlert p").html(data.msg);
					$("#customerAlert").removeClass('hidden');
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
		computeDueDate()
	});

	$('#tinno').on('keypress blur', function(e) 
	{
		// if(e.type == "blur")
		// {
		// 	validateTIN('newVendor','tinno', e.target.value);
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

	// Validation for Vendor Modal
	$('#partnercode, #vendor_name, #address').on('keyup', function(e) 
	{
		validateField('newVendor',e.target.id, e.target.id + "_help");
	});

	$('#businesstype').on('change', function(e) 
	{
		validateField('newVendor',e.target.id, e.target.id + "_help");
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

		var amount 			= $('#payableForm #h_amount').val();
		var accountentry	= $('#payableForm #accountcode\\[1\\]').val();

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

						$('#payableForm #h_amount').val(oldamount);
						$('#payableForm #h_exchangerate').val(exchangerate);
						$('#payableForm #h_convertedamount').val(newamount);

						$('#payableForm #accountcode\\[1\\]').val(account);

						if(accountnature == 'Debit' || accountnature == 'debit')
						{
							$('#payableForm #debit\\[1\\]').val($('#rateForm #newamount').val());
							$('#payableForm #credit\\[1\\]').prop('readOnly',true);

							$('#payableForm #credit\\[1\\]').val('0.00');
							$('#payableForm #debit\\[1\\]').prop('readOnly',false);
							addAmountAll("debit");
						}
						else
						{
							$('#payableForm #credit\\[1\\]').val($('#rateForm #newamount').val());
							$('#payableForm #debit\\[1\\]').prop('readOnly',true);

							$('#payableForm #debit\\[1\\]').val('0.00');
							$('#payableForm #credit\\[1\\]').prop('readOnly',false);
							addAmountAll("credit");
						}

						$('#payableForm #accountcode\\[1\\]').trigger("change");
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
								$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/get_value", data)
								.done(function(data) 
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
						label: "Ok",
						className: "btn-info btn-flat",
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
						className: "btn-info btn-flat",
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

	$('.pay_amount').on('blur', function(e) 
	{
		checkBalance();
		formatNumber(e.target.id);
		validateField('receiptForm',"convertedamount\\[1\\]", "convertedamount\\[1\\]_help"); 
		computeExchangeRate('paymentRateForm','paymentnewamount');
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
						// $("#payableForm #btnSave").removeClass('disabled');
						// $("#payableForm #btnSave_toggle").removeClass('disabled');
				
						// $("#payableForm #btnSave").html('Save');
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
			
			/**validate vendor field**/
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

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving. Please contact admin to fix this issue");
					}
					else
					{
						$("#receivableForm").submit();
					}
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;

			/**validate vendor field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();

			if(valid == 0)
			{
				$("#receivableForm #h_save_new").val("h_save_new");

				// setTimeout(function() 
				// {
				// 	// $("#payableForm #save").attr('name','save_new');
				// 	// $("#payableForm").submit();
				// },1000);

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving. Please contact admin to fix this issue");
					}
					else
					{
						$("#receivableForm").submit();
						// window.location.href = data.msg;
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
				$("#receivableForm #h_save_preview").val("h_save_preview");

				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function(data)
				{
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving.");
					}
					else
					{
						$("#receivableForm").submit();
					}
				});


				// setTimeout(function() 
				// {
				// 	$("#payableForm").submit();
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

			/**validate customer field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
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
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving. Please contact admin to fix this issue");
					}
					else
					{
						$("#receivableForm").submit();
					}
				});
			}
		});
			
		/**SAVE CHANGES AND REDIRECT TO CREATE NEW INVOICE**/
		$("#receivableForm #save_new").click(function()
		{
			var valid	= 0;
			
			/**validate vendor field**/
			valid		+= validateField('receivableForm','document_date', "document_date_help");
			valid		+= validateField('receivableForm','customer', "customer_help");
			
			valid		+= validateField('receivableForm','due_date', "due_date_help");
			
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
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving. Please contact admin to fix this issue");
					}
					else
					{
						$("#payableForm").submit();
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
				$("#receivableForm #btnSave").addClass('disabled');
				$("#receivableForm #btnSave_toggle").addClass('disabled');
				
				$("#receivableForm #btnSave").html('Saving...');

				$("#receivableForm #h_save_preview").val("h_save_preview");
				
				$.post("<?=BASE_URL?>financials/accounts_receivable/ajax/save_receivable_data",$("#receivableForm").serialize())
				.done(function( data ) 
				{
					if(data.msg == "error")
					{
						$(".alert.alert-warning").removeClass("hidden");
						$(".alert #errmsg").append("The system has encountered an error in saving.");
					}
					else
					{
						$("#receivableForm").submit();
					}

					// setTimeout(function() 
					// {
					// 	$("#payableForm #save").attr('name','save_preview');
					// 	$("#payableForm").submit();
					// },500);
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
				location.href	= "<?= BASE_URL ?>financials/accounts_receivable";//'index.php?mod=financials&type=accounts_payable';
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
		
		if(code != '' && code != "none")
		{
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
									$('#receivableForm #proformacode').val('').trigger("change");
								
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
		else
			location.href	= "<?= BASE_URL ?>financials/accounts_receivable/create";

	});
	


}); 
</script>