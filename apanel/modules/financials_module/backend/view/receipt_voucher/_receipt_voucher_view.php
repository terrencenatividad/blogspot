<style>
	#customerDetails2 .col-md-3 > .form-group {
		margin: 0;
	}

	#customerDetails2 .col-md-2 > .form-group {
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

	.customer_div > .form-group {
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

</style>

<section class="content">

	<form id = "customerDetailForm">
		<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
		<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
		<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
		<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
		<input class = "form_iput"   value = "" name = "h_condition" id = "h_condition" type="hidden">
		<input class = "form_iput" value = "customerdetail" name = "h_form" id = "h_form" type="hidden">
	</form>

	<form method = "post" class="form-horizontal" id = "paymentForm">
		<input class = "form_iput" value = "0.00" name = "h_amount" id = "h_amount" type="hidden">
		<input class = "form_iput" value = "0.00" name = "h_convertedamount" id = "h_convertedamount" type = "hidden">
		<input class = "form_iput" value = "1.00" name = "h_exchangerate" id = "h_exchangerate" type = "hidden">
		<input class = "form_iput" value = "<?= $task ?>" name = "h_task" id = "h_task" type = "hidden">

		<input value = "" name = "voucherno" id = "voucherno" type = "hidden">

		<div class="nav-tabs-custom">

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

					<div class = "box box-primary">
						<div class = "panel panel-default">
							<div class = "panel-body">
								<div class = "row">
									<div class="col-md-8 col-sm-8 col-xs-8">
										<h2><strong>Accounts Receivable</strong> <small><?='('.$voucherno.')'?></small></h2>
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
										<h4>Customer :</h4>
										<h4><strong><?=$customer?></strong></h4>
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

								<!--PAYMENT RECEIVED-->
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
																		<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" title="Print Receipt Voucher"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/accounts_receivable/print_preview/'.$sid.'" title="Print Receipt Voucher" ><span class="glyphicon glyphicon-print"></span></a>';
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
								<!--PAYMENT RECEIVED: END-->

								<div class = "row">
									<div class="col-md-2 left">&nbsp;</div>
									<div class="col-md-7 text-center">
										<?if(empty($data["payments"])){?>
											<a href="<?=BASE_URL?>financials/receipt_voucher/edit/<?=$editbtnvoucherno?>" class="btn btn-primary btn-md btn-flat">Receive Payment</a>&nbsp;
										<?} else { ?> 	
											<a href="<?=BASE_URL?>financials/receipt_voucher/edit/<?=$editbtnvoucherno?>" class="btn btn-primary btn-md btn-flat">Edit</a>
										<? } ?>
									</div>
									<div class="col-md-3 text-right">
										<a href="<?=BASE_URL?>financials/receipt_voucher" role="button" class="btn btn-primary btn-md btn-flat" id="btnExit" >Exit</a>
									</div>
								</div>

							</div>
						</div>
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
$(document).ready(function()
{
	/**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function() 
	{
		var invoice		= $("#sid").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');

		// $.post("<?= BASE_URL?>financials/receipt_voucher/ajax/delete_payments", "voucher=" + id)
		// .done(function( data ) 
		// {	
		// 	if(data.msg == "success")
		// 	{
		// 		table.deleteRow(row);
		// 		$('#deletePaymentModal').modal('hide');
		// 		location.reload();
		// 	}
		// 	else
		// 	{
		// 		console.log("else");
		// 		console.log(data.msg);
		// 	}
		// });
	});
});

</script>