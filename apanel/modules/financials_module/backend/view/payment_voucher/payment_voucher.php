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

<form method = "post" class="form-horizontal" id = "payableForm">
	<div class="box box-primary">
		<div class="box-body">
			<div class = "row">
				<div class = "col-md-12">&nbsp;</div>
				<div class = "col-md-11">
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
										->setLabel('Voucher Date')
										->setSplit('col-md-4', 'col-md-8')
										->setName('document_date')
										->setId('document_date')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => '','data-date-start-date'=>$close_date))
										->setAddon('calendar')
										->setValue($transactiondate)
										->setValidation('required')
										->draw($show_input);
							?>
						</div>
					</div>

					<div class = "row">
						<div class = "col-md-6">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Supplier')
									->setPlaceholder('Select Supplier')
									->setSplit('col-md-4', 'col-md-8')
									->setName('vendor')
									->setId('vendor')
									->setList($vendor_list)
									->setValue($vendorcode)
									->setAttribute(array("onChange" => "getPartnerInfo(this.value);"))
									->setValidation('required')
									->addHidden(($task == 'view'))
									->draw($show_input);
							?>
						</div>
						<div class = "col-md-6">
							<?php
								echo $ui->formField('dropdown')
										->setLabel('Payment Mode')
										->setSplit('col-md-4', 'col-md-8')
										->setClass("payment_mode")
										->setName('paymentmode')
										->setId('paymentmode')
										->addHidden(($task == 'view'))
										->setList(array("cash" => "Cash", "cheque" => "Cheque"))
										->setAttribute(
											array(
												"onChange" => "toggleCheckInfo(this.value); validateField('payableForm',this.id, 'paymentmode_help');"
											)
										)
										->setValue($paymenttype)
										->draw($show_input);
							?>
						</div>
					</div>
					
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group">
								<label for="apv" class="control-label col-md-4">Total Payment</label>
								<div class="col-md-8">
									<?php
									if(!$show_input){
										echo '<p class="form-control-static">'.number_format($sum_applied,2).'</p>';
									}else{

									?>
									<button type="button" id="apv" class="btn btn-block btn-success btn-flat">
										<em class="pull-left"><small>Click to view tagged payables</small></em>
										<strong id="pv_amount" class="pull-right"><?=number_format($sum_applied,2)?></strong>
									</button>
									<?php
									}
									?>
								</div>
							</div>
						</div>
						<!-- Text Area for selected payables -->
						<textarea class = "hidden" id = "selected_rows" name = "selected_rows">[]</textarea>
						<div class = "col-md-6">
							<?php
								echo $ui->formField('text')
										->setLabel('Reference No')
										->setSplit('col-md-4', 'col-md-8')
										->setName('paymentreference')
										->setId('paymentreference')
										->setAttribute(array("maxlength" => "50"))
										->setValue($referenceno)
										->draw($show_input);
							?>
						</div>
					</div>
					<div class="row">
						<div class = "col-md-12">
							<?php
								echo $ui->formField('textarea')
										->setLabel('Notes:')
										->setSplit('col-md-2', 'col-md-10')
										->setName('remarks')
										->setId('remarks')
										->setValue($particulars)
										->setAttribute(
											array(
												'rows' => 5
											)
										)
										->draw($show_input);
							?>
						</div>
					</div>

				</div>
			</div>

			<!--Cheque Details-->
				<div class="has-error">
					<span id="chequeCountError" class="help-block hidden small">
						<i class="glyphicon glyphicon-exclamation-sign"></i> 
						Please specify at least one(1) cheque.
					</span>
					<span id="chequeAmountError" class="help-block hidden small">
						<i class="glyphicon glyphicon-exclamation-sign"></i> 
						Please complete the fields on the highlighted row(s).
					</span>
					<span id="paymentAmountError" class="help-block hidden small">
						<i class="glyphicon glyphicon-exclamation-sign"></i> 
						Please make sure that the total payment applied (<strong id="disp_tot_payment">0</strong>) should be equal to (<strong id="disp_tot_cheque">0</strong>).
					</span>
					<span id="checkNumberError" class="help-block hidden">
						<i class="glyphicon glyphicon-exclamation-sign"></i> 
						The Cheque Number you entered has already been used
					</span>
				</div>
			<div class="panel panel-default <?php echo $show_cheques?>" id="cheque_details">
				<div class="panel-heading">
					<strong>Cheque Details</strong>
				</div>
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-hover" id="chequeTable">
						<thead>
							<tr class="info">
								<th class="col-md-4">Bank Account</th>
								<th class="col-md-3">Cheque Number</th>
								<th class="col-md-2">Cheque Date</th>
								<th class="col-md-2">Amount</th>
								<th class="col-md-1">Action</th>
								<!-- <?if($show_input):?><th class="col-md-1">Action</th><?endif;?> -->
							</tr>
						</thead>
						<tbody id="tbody_cheque">
							<?if($task=='create'):?>
							<tr class="clone">
								<td class="">
									<?php
										echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12 field_col')
												->setPlaceholder('Select One')
												->setClass("cheque_account")
												->setName('chequeaccount[1]')
												->setId('chequeaccount[1]')
												->setName('chequeaccount[1]')
												->setClass('chequeaccount')
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
												->setClass('chequenumber')
												->setValidation('required alpha_num')
												->setMaxLength(30)
												->setAttribute(array("onBlur" => "validateChequeNumber(this.id, this.value, this)"))
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
												->setMaxLength(50)
												->setValue($transactiondate)
												// ->setAddOn("calendar")
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
												->setMaxLength(20)
												->setAttribute(array("onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
												->setValue("0.00")
												->draw(true);
									?>
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
								</td>
							</tr>
							<?else:
								$row 				=	1;
								$totalchequeamt 	=	0;
								if(!empty($listofcheques) && !is_null($listofcheques)):
									foreach ($listofcheques as $index => $cheque):
										$accountcode 	=	$cheque['chequeaccount'];
										$chequeno	 	=	$cheque['chequenumber'];
										$chequedate 	=	$cheque['chequedate'];
										$chequeamount 	=	$cheque['chequeamount'];
										$convertedamt 	=	$cheque['chequeconvertedamount'];
							?>	
								<tr class="clone">
									<td>
										<?php
											echo $ui->formField('dropdown')
													->setSplit('', 'col-md-12 field_col')
													->setPlaceholder('Select One')
													->setClass("cheque_account")
													->setName('chequeaccount['.$row.']')
													->setId('chequeaccount['.$row.']')
													->setName('chequeaccount['.$row.']')
													->setClass('chequeaccount')
													->setList($cash_account_list)
													->setValue($accountcode)
													->draw($show_input);
										?>
									</td>
									<td>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12 field_col')
													->setClass("")
													->setName('chequenumber['.$row.']')
													->setId('chequenumber['.$row.']')
													->setClass('chequenumber')
													->setMaxLength(30)
													->setValidation('required alpha_num')
													->setAttribute(array("onBlur" => "validateChequeNumber(this.id, this.value, this)"))
													->setValue($chequeno)
													->draw($show_input);
										?>
										<input class="hidden chequeno" value= "<?=$chequeno?>">
									</td>
									<td>
									<div class="input-group date remove-margin">
										<?if($show_input):?>
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
										<?endif;?>
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12 field_col')
													->setClass("datepicker-input")
													->setName('chequedate['.$row.']')
													->setId('chequedate['.$row.']')
													->setMaxLength(50)
													->setValue($chequedate)
													->draw($show_input);
										?>
										</div>
									</td>
									<td class="text-right"> 
										<?php
											echo $ui->formField('text')
													->setSplit('', 'col-md-12 field_col')
													->setClass("chequeamount text-right")
													->setName('chequeamount['.$row.']')
													->setId('chequeamount['.$row.']')
													->setValidation('decimal')
													->setMaxLength(20)
													->setAttribute(array("onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
													->setValue(number_format($chequeamount,2))
													->draw($show_input);
										?>
									</td>	
								
									<? if($show_input):?>
									<td class="text-center">
										<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
									</td>
									<?php else : ?>
										<td class="text-center">
										<button type="button" class="btn btn-info btn-flat print_check"  style="outline:none;" ><span class="glyphicon glyphicon-download-alt"></span></button>
									</td>
								</tr>
									<? endif; ?>	
							<?
								
								$row++;
								$totalchequeamt 	+=	$chequeamount;
									endforeach;
								else:
							?>
							<tr class="clone">
								<td class="">
									<?php
										echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12 field_col')
												->setPlaceholder('Select One')
												->setClass("cheque_account")
												->setName('chequeaccount[1]')
												->setId('chequeaccount[1]')
												->setName('chequeaccount[1]')
												->setClass('chequeaccount')
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
												->setClass('chequenumber')
												->setMaxLength(30)
												->setAttribute(array("onBlur" => "validateChequeNumber(this.id, this.value, this)"))
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
												// ->setAddOn("calendar")
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
												->setValidation('decimal')
												->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
												->setValue("0.00")
												->draw(true);
									?>
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
								</td>
							</tr>
							<?
								endif;
							endif;?>
						</tbody>
						<tfoot>
							<tr>
								<? if($show_input):?>
								<td colspan="2">
									<a type="button" class="btn btn-link add-cheque"  style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Cheque</a>
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
								<? else:?>
								<td class="text-right" colspan="3"><label class="control-label">Total</label></td>
								<td class="text-right">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 field_col')
												->setClass("text-right input_label")
												->setId("totalcheques")
												->setAttribute(array("readonly" => "readonly"))
												->setValue(number_format($totalchequeamt, 2))
												->draw($show_input);
									?>
								</td>
								<td></td>
								<?endif;?>
							</tr>	
						</tfoot>
					</table>
				</div>
			</div>
			<!--End of Cheque Details-->
			<hr/>
			<!--Account Entries-->
			<div class="has-error">
				<span id="totalAmountError" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					The total Debit Amount and Credit Amount must match.
				</span>
				<span id="zeroTotalAmountError" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					Total Debit and Total Credit must have a value.
				</span>
				<span id="accountcodeError" class="help-block hidden small">
					<i class="glyphicon glyphicon-exclamation-sign"></i> 
					Account Code field must have a value.
				</span>
			</div>
			<div class="panel panel-default" id="accounting_details">
				<div class="panel-heading">
					<strong>Accounting Details</strong>
				</div>
				<div class="table-responsive">
					<table class="table table-hover table-condensed " id="entriesTable">
						<thead>
							<tr class="info">
								<th class="col-md-4">Account</th>
								<th class="col-md-3">Description</th>
								<th class="col-md-2">Debit</th>
								<th class="col-md-2">Credit</th>
								<?if($show_input){?><th class="col-md-1"></th><?}?>
							</tr>
						</thead>
						<tbody id = "ap_items">
							<?php
								$row 				= 1;

								$total_debit 		= 0;
								$total_credit 		= 0;

								if($task == 'create')
								{
									$accountcode 		= '';
									$detailparticulars 	= '';
									$debit 				= '0.00';
									$credit 			= '0.00';
		
							?>

							<tr class="clone">
								<td>
									<?php
										echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('', 'col-md-12')
												->setName("accountcode[".$row."]")
												->setClass("accountcode")
												->setId("accountcode[".$row."]")
												->setList($account_entry_list)
												->setValue($accountcode)
												->draw($show_input);
									?>
									<input type = "hidden" class="h_accountcode" name='h_accountcode[<?=$row?>]' id='h_accountcode[<?=$row?>]'>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('detailparticulars['.$row.']')
												->setId('detailparticulars['.$row.']')
												->setClass('description')
												->setAttribute(array("maxlength" => "100"))
												->setValue($detailparticulars)
												->draw($show_input);
									?>	
									<input type = "hidden" class="ischeck" name='ischeck[<?=$row?>]' id='ischeck[<?=$row?>]'>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('debit['.$row.']')
												->setId('debit['.$row.']')
												->setClass("text-right debit")
												->setValidation('decimal')
												->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
												->setValue(number_format($debit, 2))
												->draw($show_input);
									?>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('credit['.$row.']')
												->setId('credit['.$row.']')
												->setClass("text-right account_amount credit")
												->setValidation('decimal')
												->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
												->setValue(number_format($credit, 2))
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

							<tr class="clone">
								<td>
									<?php
										echo $ui->formField('dropdown')
												->setPlaceholder('Select One')
												->setSplit('', 'col-md-12')
												->setName("accountcode[".$row."]")
												->setClass("accountcode")
												->setId("accountcode[".$row."]")
												->setList($account_entry_list)
												->setValue($accountcode)
												->draw($show_input);
									?>
									<input type = "hidden" class="h_accountcode" name='h_accountcode[<?=$row?>]' id='h_accountcode[<?=$row?>]'>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('detailparticulars['.$row.']')
												->setId('detailparticulars['.$row.']')
												->setAttribute(array("maxlength" => "100"))
												->setClass('description')
												->setValue($detailparticulars)
												->draw($show_input);
									?>
									<input type = "hidden" class="ischeck" name='ischeck[<?=$row?>]' id='ischeck[<?=$row?>]'>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('debit['.$row.']')
												->setId('debit['.$row.']')
												->setClass("text-right debit")
												->setvalidation('decimal')
												->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
												->setValue(number_format($debit, 2))
												->draw($show_input);
									?>
								</td>
								<td>
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('credit['.$row.']')
												->setClass("text-right account_amount credit")
												->setId('credit['.$row.']')
												->setValidation('decimal')
												->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
												->setValue(number_format($credit, 2))
												->draw($show_input);
									?>
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
								</td>
							</tr>

							<?php
								}else{
									$aPvJournalDetails 	= $data['details'];
									$detail_row 		= '';
									if(!empty($aPvJournalDetails)){
										foreach ($aPvJournalDetails as $aPvJournalDetails_Index => $aPvJournalDetails_Value) {
											$accountcode 		= $aPvJournalDetails_Value->accountcode;
											$detailparticulars 	= $aPvJournalDetails_Value->detailparticulars;
											$debit 				= $aPvJournalDetails_Value->debit;
											$credit 			= $aPvJournalDetails_Value->credit;
											$ischeck 			= isset($aPvJournalDetails_Value->ischeck) 	?	$aPvJournalDetails_Value->ischeck	:	"no";

											$disable_code 		= "";
											$added_class 		= "";
											$added_function_db 	= "";
											$added_function_cr	= "";
											$indicator 			= "";

											if($aPvJournalDetails_Index > 0 && $paymenttype == 'cheque' && $ischeck == 'yes' ){
												$disable_debit		= 'readOnly';
												$disable_credit		= 'readOnly';
												$disable_code 		= 'disabled';
												$added_class 		= 'added_row';
												$indicator 			= "cheque";
											} else if($aPvJournalDetails_Index > 0 && $accountcode == $discount_code ){
												$disable_debit		= 'readOnly';
												$disable_credit		= 'readOnly';
												$disable_code 		= 'disabled';
												$added_class 		= 'discount_row';
											} else {
												$disable_debit		= ($debit > 0) ? '' : 'readOnly';
												$disable_credit		= ($credit > 0) ? '' : 'readOnly';
											}

											$total_debit 		+= $debit;
											$total_credit 		+= $credit;
											$detail_row	.= '<tr class="clone '.$added_class.'">';

											$detail_row	.= '<td>';
											$detail_row .= $ui->formField('dropdown')
															->setPlaceholder('Select One')
															->setSplit('', 'col-md-12')
															->setName("accountcode[".$row."]")
															->setClass("accountcode")
															->setId("accountcode[".$row."]")
															->setList($account_entry_list)
															->setValue($accountcode)
															->setAttribute(array($disable_code))
															->draw($show_input);
											$detail_row	.= '	<input type = "hidden" class="h_accountcode" value="'.$accountcode.'" name="h_accountcode['.$row.']" id="h_accountcode['.$row.']">
															</td>';

											$detail_row	.= '<td>';
											$detail_row .= $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('detailparticulars['.$row.']')
															->setId('detailparticulars['.$row.']')
															->setClass('description')
															->setAttribute(array("maxlength" => "100"))
															->setValue($detailparticulars)
															->draw($show_input);
											$detail_row	.= '	<input type = "hidden" class="ischeck" value="'.$ischeck.'" name="ischeck['.$row.']" id="ischeck['.$row.']">
															</td>';

											$detail_row	.= '<td class="text-right">';
											$detail_row .= $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('debit['.$row.']')
															->setClass("debit text-right $indicator")
															->setId('debit['.$row.']')
															->setValidation('decimal')
															->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", $disable_debit))
															->setValue(number_format($debit, 2))
															->draw($show_input);
											$detail_row	.= '</td>';

											$detail_row	.= '<td class="text-right">';
											$detail_row .= $ui->formField('text')
															->setSplit('', 'col-md-12')
															->setName('credit['.$row.']')
															->setValidation('decimal')
															->setClass("account_amount credit text-right $indicator")
															->setId('credit['.$row.']')
															->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", $disable_credit))
															->setValue(number_format($credit, 2))
															->draw($show_input);
											$detail_row	.= '</td>';

											if( $show_input ){
												$detail_row .= '<td class="text-center">';
												$detail_row .= '	<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="'.$row.'" name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');" '.$disable_code.'><span class="glyphicon glyphicon-trash"></span></button>';
												$detail_row .= '</td>';
											}

											$detail_row	.= '</tr>';

											$row++;
										}

										echo $detail_row;
									}
								}
							?>
						</tbody>
						<tfoot>
							<? if($show_input): ?>
							<tr>
								<td>
									<a type="button" class="btn btn-link add-entry" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Entry</a>
								</td>	
							</tr>	
							<?endif;?>
							<tr id="total">
								<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
								<td class="right" style="border-top:1px solid #DDDDDD;">
									<label class="control-label col-md-12">Total</label>
								</td>
								<td class="text-right" style="border-top:1px solid #DDDDDD;">
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
								<td class="text-right" style="border-top:1px solid #DDDDDD;">
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
							</tr>	
						</tfoot>
					</table>
				</div>
			</div>
			<!--End of Accounting Entries-->

			<div class="row">
				<div class="col-md-12 col-sm-12 text-center">
					<!-- <?if($show_input):?>
					<input type = "button" value = "Save" name = "save" id = "btnSave" class="btn btn-primary btn-flat"/>
					<input class = "form_iput" value = "" name = "h_save" id = "h_save" type = "hidden">
					<?endif;?>
					&nbsp; -->
					<?php
					if( $show_input )
					{
						$save		= ($task == 'create') ? 'name="save"' : '';
						$save_new	= ($task == 'create') ? 'name="save_new"' : '';
					?>
						<input class = "form_iput" value = "" name = "h_save" id = "save" type = "hidden">
						
						
						<div class="btn-group" id="save_group">
							<input type = "button" value = "Save & Preview" name = "save" id = "btnSave" class="btn btn-primary btn-flat"/>
						    <input class = "form_iput" value = "" name = "h_save" id = "h_save" type = "hidden">
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
									&nbsp;&nbsp;Save &amp; Exit
									<input type = "hidden" value = "" name = "h_save_preview" id = "h_save_preview"/>
								</li>
							</ul>
							<?php
							}
							?>
						</div>
						&nbsp;&nbsp;&nbsp;
						
					<? } ?>
						



					<?
					if(($status == 'open' && $has_access == 1) && $restrict_pv){
						echo '<a role = "button" href="'.MODULE_URL.'edit/'.$generated_id.'" class="btn btn-primary btn-flat">Edit</a>';
					}
					?>
					<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" id="btnCancel">Cancel</button>
				</div>
			</div>
			
		</div>
	</div>
</form>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Tag Payables</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-addon">
								<i class="fa fa-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<form class="form-horizontal" id="paymentForm">
					<br/>
					<div class="row">
						<label class="control-label col-md-2">
							Total Payment
						</label>
						<div class="col-md-3">
						<?php
							echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setClass("input-sm text-right")
									->setName('total_payment')
									->setId('total_payment')
									->setPlaceHolder("0.00")
									->setAttribute(
										array(
											"maxlength" => "50", 
											"readonly" => "readonly"
										)
									)
									->setValue(number_format($sum_applied,2))
									->draw(true);
						?>
						</div>
						<label class="control-label col-md-2">
							Total Discount
						</label>
						<div class="col-md-3">
						<?php
							echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setClass("input-sm text-right")
									->setName('total_discount')
									->setId('total_discount')
									->setPlaceHolder("0.00")
									->setAttribute(
										array(
											"maxlength" => "50", 
											"readonly" => "readonly"
										)
									)
									->setValue(number_format($sum_discount,2))
									->draw(true);
						?>
						</div>
					</div>

					<div class="has-error">
						<span id="appCountError" class="help-block hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please select at least one(1) payable.
						</span>
						<span id="appAmountError" class="help-block hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please make sure that the amount paid for the payable(s) below are greater than zero(0).
						</span>
					</div>
					<div class="table-responsive">
						<table class="table table-condensed table-bordered table-hover" id="app_payableList">
							<thead>
								<tr class="info">
									<?if($show_input):?><th class="col-md-1 center"></th><?endif;?>
									<th class="col-md-1 text-center">Date</th>
									<th class="col-md-1 text-center">Voucher</th>
									<th class="col-md-1 text-center">Reference</th>
									<th class="col-md-2 text-center">Total Amount</th>
									<th class="col-md-2 text-center">Balance</th>
									<th class="col-md-2 text-center">Amount to Pay</th>
									<th class="col-md-2 text-center">Discount</th>
								</tr>
							</thead>
							<tbody id="payable_list_container">
							</tbody>
							<tfoot>
								<tr>
									<td class="center" colspan = "7" id="app_page_links"></td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="pagination"></div>
					<div class="modal-footer">
						<div class="col-md-12 col-sm-12 col-xs-12 text-center">
							<?if($show_input):?>
								<div class="btn-group">
									<button type = "button" class = "btn btn-primary btn-sm btn-flat" onClick = "getPVDetails();">Tag</button>
								</div>
								&nbsp;&nbsp;&nbsp;
							<?endif;?>
							<div class="btn-group">
								<!-- noted by Sir Mark to remove this onclick function upon cancel. onClick="clearPayment();"-->
								<button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal" >Cancel</button>
							</div>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>
</section>

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

<!-- Delete Cheque Confirmation Modal -->
<div class="modal fade" id="deleteChequeModal" tabindex="-1" data-backdrop="static">
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

 <!-- ON CHANGING OF VENDOR MODAL -->
<div class="modal fade" id="change_vendor_modal" tabindex="-1" data-backdrop="static">
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
							<button type="button" class="btn btn-info btn-flat" id="yes_to_reset">Yes</button>
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

<script>
<?php if ($task == 'create'):?>
	getLockAccess('create');
<?php endif;?>
<?php if ($task == 'edit'):?>
	getLockAccess('edit');
<?php endif;?>
	var edited = false;
	$('#paymentModal').on('blur', 'input', function() {
	edited = true;
});

function addVendorToDropdown() {
	var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
	var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

	$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#paymentForm #vendor option");
	$('#paymentForm #vendor').val(optionvalue);

	getPartnerInfo(optionvalue);

	$('#vendor_modal').modal('hide');
	$('#vendor_modal').find("input[type=text], textarea, select").val("");
}

function closeModal(){
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
var ajax 	 = {};

var id_array 		= [];
var accounts 		= [];
var acct_details 	= [];

var checker 	= new Array();
var cheque_arr 	= [];
var table 		= document.getElementById('ap_items');
var newid 		= table.rows.length;
	newid 		= parseFloat(newid);

var task 		= '<?= $task ?>';

// Get Initial Clone of First Row. In this case, disabled cheque entries. 
var initial_clone 		 = $('#entriesTable tbody tr.clone:first');
	// enable them to allow a cloned row with enabled dropdown and input fields
var initial_debit 		= initial_clone.find('.debit').val();
var initial_credit 		= initial_clone.find('.crebit').val() || 0;
	initial_clone.find('.debit').attr("value",'0.00');
	initial_clone.find('.credit').attr("value",'0.00');
var clone_acct 	= $('#entriesTable tbody tr.clone:first')[0].outerHTML;
	// after cloning, set the first row to its initial state ( again, in this case, a disabled fields )
	initial_clone.find('.debit').val(initial_debit);
	initial_clone.find('.credit').val(initial_credit);

function storedescriptionstoarray(){
	acct_details 	=	[];
	$('#entriesTable tbody tr.added_row').each(function() {
		var accountcode = $(this).find('.accountcode').val();
		var description = $(this).find('.description').val();
		var ischeck 	= $(this).find('.ischeck').val();
		var debit		= $(this).find('.account_amount').val();

		if(description!=""){
			if (typeof acct_details[accountcode] === 'undefined') {
				acct_details[accountcode] = "";
			}
			acct_details[accountcode] = description;
		}
	});
}

function displaystoreddescription(){
	$('#entriesTable tbody tr.added_row select.accountcode').each(function() {
		var ischeck = $(this).closest('tr').find('.ischeck').val();
		if(ischeck == 'yes'){
			if (typeof acct_details[$(this).val()] === 'undefined') {
				$(this).closest('tr').find('.description').val("");
			} else {
				var description = acct_details[$(this).val()] || "";
				$(this).closest('tr').find('.description').val(description);	
			}	
		}
	});
}

$('#chequeTable .cheque_account').on('change', function()  {
	storedescriptionstoarray();
	if ($('#entriesTable tbody tr.clone select').data('select2')) {
		$('#entriesTable tbody tr.clone select').select2('destroy');
	}
	var val = $(this).val();
	
	cheque_arr = [];

	$('#entriesTable tbody tr.added_row').remove();
	$('#chequeTable tbody tr select.cheque_account').each(function() {
		var account = $(this).val();
		if(account!="" && jQuery.inArray(account,cheque_arr) == -1){
			cheque_arr.push(account);
		}
	});

	var row = $("#entriesTable tbody tr.clone").length;
	$('#entriesTable tbody tr.clone .accountcode').each(function(index) {
		var account = $(this).val();
		var ischeck = $(this).closest('tr').find('.ischeck').val();
		if(task == 'create' && account == "" || account == "" && ischeck == 'yes'){
			$(this).closest('tr').remove();
		}
	});
	row = $("#entriesTable tbody tr.clone").length + 1;
	cheque_arr.forEach(function(account) {
		var ParentRow = $("#entriesTable tbody tr.clone").last();
		$('#entriesTable tbody tr.added_row').find('.ischeck').val('yes');
		// clone_acct(initial state of first row) will be placed on the last cloned row. 
		ParentRow.after(clone_acct);
		resetIds();
		$("#accountcode\\["+ row +"\\]").val(account).trigger('change.select2');
		$("#entriesTable button#"+row).prop('disabled',true);
		$("#entriesTable debit#"+row).prop('disabled',true);
		$("#accountcode\\["+ row +"\\]").closest('tr').addClass('added_row');
		$('#entriesTable tbody tr.added_row').find('.ischeck').val('yes');
		$("#accountcode\\["+ row +"\\]").val(account).trigger('change.select2');
		disable_acct_fields(row);
		row++;
	});
	accounts.push(val);
	recomputechequeamts();
	acctdetailamtreset();
	displaystoreddescription();
	drawTemplate();
});

function disable_acct_fields(row){
	$("#accountcode\\["+ row +"\\]").prop("disabled", true);
	$("#debit\\["+ row +"\\]").prop("readonly", true);
	$("#credit\\["+ row +"\\]").prop("readonly", true);
	$("#entriesTable button#"+row).prop('disabled',true);
}

function acctdetailamtreset(){
	$('#entriesTable tbody .added_row').each(function() {
		var accountcode = $(this).find('.accountcode').val();
		if(!checker.hasOwnProperty('acc-'+accountcode)){
			$(this).remove();
		}
		$(this).closest('tr').find('.ischeck').val('yes');
	});
	var total_payment = 0;
	$('#entriesTable tbody tr select.accountcode').each(function() {
		if (typeof checker['acc-' + $(this).val()] === 'undefined') {
		} else {
			var ischeck 	=	$(this).closest('tr').find('.ischeck').val();
			var ca = checker['acc-' + $(this).val()] || '0.00';
				ca = removeComma(ca);
			if($(this).val() == ""){
				ca = '0.00';
			}
			if(ischeck == 'yes'){
				$(this).closest('tr').find('.account_amount').val(addComma(ca));
			}
			$(this).closest('tr').find('.h_accountcode').val($(this).val());	
		}	
	});
}

function recomputechequeamts(){
	checker = [];
	$('#chequeTable tbody tr select.cheque_account').each(function() {
		var account = $(this).val();
		var ca = $(this).closest('tr').find('.chequeamount').val();
			ca = removeComma(ca);
		if (typeof checker['acc-' + account] === 'undefined') {
			checker['acc-' + account] = 0;
		}
		checker['acc-' + account] += parseFloat(ca);
	});
}

// Change event for chequeamount
$('#chequeTable .chequeamount').on('change', function() {
	chequeamount = $(this).val();
	acc = $(this).closest('tr').find('.cheque_account').val();
	storedescriptionstoarray();
	recomputechequeamts();
	acctdetailamtreset();
	displaystoreddescription();
	addAmountAll('credit');
});

function computeDueDate() {
	var invoice = $("#transactiondate").val();
	var terms 	= $("#vendor_terms").val();

	if(invoice != '')
	{
		var newDate	= moment(invoice).add(terms, 'days').format("MMM DD, YYYY");
		$("#due_date").val(newDate);
	}
}

function getPartnerInfo(code) {
	if(code == '' || code == 'add' || code == "none")
	{
		$("#vendor_tin").val("");
		$("#vendor_terms").val("");
		$("#vendor_address").val("");

		bootbox.dialog({
			message: "Please select Vendor.",
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
	} else {
		$.post('<?=BASE_URL?>financials/payment_voucher/ajax/get_value', "code=" + code + "&event=getPartnerInfo", function(data) 
		{
			var address		= data.address.trim();
			var tinno		= data.tinno.trim();
			var terms		= data.terms.trim();
			
			$("#vendor_tin").val(tinno);
			$("#vendor_terms").val(terms);
			$("#vendor_address").val(address);

			// Remove disabled for Payment Mode
			// $("#paymentmode").removeAttr("disabled");

			computeDueDate();
		});
	}
}

function addCommas(nStr) {
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
function toggleExchangeRate(tp) {
	tp = typeof tp !== 'undefined' ? tp : '';

	if(tp == '') {
		var amount 				= $('#payableForm #h_amount').val();
		var exchangerate 		= $('#payableForm #h_exchangerate').val();
		var convertedamount 	= $('#payableForm #h_convertedamount').val();

		var oldamount 			= amount * 1;
		var rate 				= exchangerate * 1;
		var newamount 			= convertedamount * 1;

		// Set hidden values
		$('#rateForm #oldamount_').val(addCommas(oldamount.toFixed(2)));
		$('#rateForm #rate_').val(addCommas(rate.toFixed(2)));
		$('#rateForm #newamount_').val(addCommas(newamount.toFixed(2)));		

		$('#rateModal').modal('toggle');	
	} else {
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

		$('#receiptForm #paymentamount\\[1\\]').val($('#paymentRateForm #paymentoldamount').val());

		$('#paymentRateModal').modal('toggle');
	}
}

/**RESET IDS OF ROWS**/
function resetIds() {
	var table 	= document.getElementById('entriesTable');
	var count	= table.rows.length - 3;

	x = 1;
	for(var i = 1;i <= count;i++) {
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'accountcode['+x+']';
		row.cells[0].getElementsByTagName("input")[0].id 	= 'h_accountcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[1].getElementsByTagName("input")[1].id 	= 'ischeck['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'debit['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'credit['+x+']';
		
		row.cells[0].getElementsByTagName("select")[0].name = 'accountcode['+x+']';
		row.cells[0].getElementsByTagName("input")[0].name 	= 'h_accountcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[1].getElementsByTagName("input")[1].name 	= 'ischeck['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'debit['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'credit['+x+']';
		
		row.cells[4].getElementsByTagName("button")[0].setAttribute('id',x);
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

		x++;
	}
}

function resetChequeIds() {
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;

	x = 1;

	for(var i = 1;i <= count;i++) {
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
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); addAmounts();');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequeDelete('+x+')');
		x++;
	}

}

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero() {
	resetIds();

	var table 		= document.getElementById('entriesTable');
	var newid 		= table.rows.length - 3;
	var account		= document.getElementById('accountcode['+newid+']');

	if(document.getElementById('accountcode['+newid+']') != null) {
		document.getElementById('accountcode['+newid+']').value 		= '';
		document.getElementById('detailparticulars['+newid+']').value 	= '';
		document.getElementById('debit['+newid+']').value 				= '0.00';
		document.getElementById('credit['+newid+']').value 				= '0.00';

		document.getElementById('debit['+newid+']').readOnly 			= false;
		document.getElementById('credit['+newid+']').readOnly 			= false;
	}
}

function setChequeZero() {
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

/**VALIDATE FIELD**/
function validateField(form,id,help_block) {
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

/**VALIDATION FOR NUMERIC FIELDS**/
function isNumberKey(evt,exemptChar)  {
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
function isNumberKey2(evt)  {
	if(evt.which != 0){
		var charCode = (evt.which) ? evt.which : evt.keyCode 
		if(charCode == 46) return true; 
		if (charCode > 31 && (charCode < 48 || charCode > 57)) 
		return false; 
		return true;
	}
}

/**HIGHTLIGHT CONTENT OF INPUT**/
function SelectAll(id) {
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id) {
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

/**COMPUTE TOTAL CHEQUE AMOUNT**/
function addAmounts()  {
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

function addAmountAll(field) {
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
		var is_cheque   = $("#ischeck\\["+i+"\\]").val();

		if(document.getElementById(notfield+'['+i+']')!=null)
		{          
			if(inputs.value && inputs.value != '0' && inputs.value != '0.00')
			{                            
				inData = inputs.value.replace(/,/g,'');
				if(is_cheque == 'yes'){
					inputs.readOnly   = true;
					disables.readOnly = true;
				}else {
					disables.readOnly = true;
				}
			}
			else
			{             
				inData = 0;
				if(is_cheque == 'yes'){
					inputs.readOnly   = true;
					disables.readOnly = true;
				}else {
					disables.readOnly = false;
				}
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

function confirmDelete(id){
	$('#deleteItemModal').data('id', id).modal('show');
}

function confirmChequeDelete(row){
	$('#deleteChequeModal').data('row', row).modal('show');
}

function deleteItem(row){
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= '<?= COMPANYCODE ?>';
	var table 		= document.getElementById('entriesTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;

	console.log("row: " + rowCount);

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
				console.log("1");
				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/delete_row",{table:datatable,condition:condition})
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
				console.log("2");
				table.deleteRow(row);	
				resetIds();
				addAmountAll('debit');
				addAmountAll('credit');
			}
		}
		else
		{	
			console.log("else");
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
		console.log("else 2");
		if(rowCount > 2)
		{
			table.deleteRow(row);	
			resetIds();
			addAmountAll('debit');
			addAmountAll('credit');
		}
	}
}

/**VALIDATE ACCOUNT ROWS**/
function validateDetails(){
	var table 			= document.getElementById('entriesTable');
	var total_debit 	= $('#total_debit').val();
	var total_credit 	= $('#total_credit').val();
	total_debit 		= total_debit.replace(/\,/g,'');
	total_credit 		= total_credit.replace(/\,/g,'');

	/**
	* Validate if total debit / credit is equal to the total amount specified
	*/
	var total_amount	= $('#payableForm #h_convertedamount').val();
	//total_amount 		= total_amount.replace(/\,/g,'');
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
				$("#payableForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid1++;
			}
			else
			{
				$("#payableForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
			
			if(parseFloat(debit) == 0 && parseFloat(credit) == 0)
			{
				$("#payableForm #accountcode\\["+i+"\\]").closest('tr').addClass('danger');
				valid2++;
			}
			else
			{
				$("#payableForm #accountcode\\["+i+"\\]").closest('tr').removeClass('danger');
			}
		}
		
		if(valid1 > 0)
		{
			$("#payableForm #accountcodeError").removeClass('hidden');
		}
		else
		{
			$("#payableForm #accountcodeError").addClass('hidden');
		}
		
		if(valid2 > 0)
		{
			$("#payableForm #zeroTotalAmountError").removeClass('hidden');
		}
		else
		{
			$("#payableForm #zeroTotalAmountError").addClass('hidden');
		}
		
		if(parseFloat(total_debit) != parseFloat(total_credit)){
			$("#payableForm #totalAmountError").removeClass('hidden');
			$('#payableForm .accountcode').each(function(index){
				var debit = $('#entriesTable #debit\\['+index+'\\]').val();
				var credit = $('#entriesTable #credit\\['+index+'\\]').val();

				if(debit != undefined && debit > 0){
					$('#entriesTable #debit\\['+index+'\\]').parent().addClass('has-error');
				}
				if(credit != undefined && credit > 0){
					$('#entriesTable #credit\\['+index+'\\]').parent().addClass('has-error');
				}
			});
			valid1 = 1;
		}
		else
		{
			$("#payableForm #totalAmountError").addClass('hidden');

			if(parseFloat(total_amount) > 0)
			{
				if(parseFloat(total_amount) != parseFloat(total_debit))
				{
					$("#payableForm #detailEqualError strong").html(addCommas(newtotal_amount.toFixed(2)));
					$("#payableForm #detailEqualError").removeClass('hidden');
					valid1 = 1;
				}
				else
				{
					$("#payableForm #detailEqualError strong").html('');
					$("#payableForm #detailEqualError").addClass('hidden');
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
function cancelTransaction(vno){
	var voucher		= document.getElementById('h_voucher_no').value;
	var companycode	= "<?= COMPANYCODE ?>";

	var datatable	= 'accountspayable';
	var detailtable	= 'ar_details';
	var condition	= " voucherno = '"+vno+"' AND stat = 'temporary' ";
	
	if(task == 'create')
	{	
		var data	= "table="+datatable+"&condition="+condition;
		var data2	= "table="+detailtable+"&condition="+condition;

		$.post("<?=BASE_URL?>financials/payment_voucher/ajax/delete_row",data)
		.done(function(data1) 
		{
			if(data1.msg == "success")
			{
				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/delete_row",data2)
				.done(function(data2) 
				{

					if(data2.msg == "success")
					{
						window.location.href = '<?=BASE_URL?>financials/payment_voucher';
					}
				});
			}
		});
	}
	else
	{
		window.location.href	= "<?=BASE_URL?>financials/payment_voucher";
	}
}

/**TOGGLE CHECK DATE FIELD**/
function toggleCheckInfo(val){
	var selected_rows = $("#selected_rows").html();

	if(val == 'cheque'){
		if(selected_rows != '[]'){
			$("#payableForm #cheque_details").removeClass('hidden');
		}else{
			
			var list 	= (vendor != '') ? "<ul><li>Total Payment</li></ul>" : "<ul><li>Vendor</li><li>Total Payment</li></ul>";
			var msg 	= "The following fields are required to process a '<strong>Cheque</strong>' payment."+list;
			bootbox.dialog({
				message: msg,
				title: "Oops!",
					buttons: {
					yes: {
					label: "Ok",
					className: "btn-primary btn-flat",
					callback: function(result) {
							$("#payableForm #paymentmode").val('cash');
							$('#payableForm #paymentmode').select2('destroy');
							$('#payableForm #paymentmode').select2({width: "100%"});
						}
					}
				}
			});
		}
	} else {
		//For Reseting initial PV & Cheque Details
		clearChequePayment();
		getPVDetails();
		$("#payableForm #cheque_details").addClass('hidden');
		$('#totalcheques').val(0);
		formatNumber('totalcheques');
	}

}

function clearInput(id){
	document.getElementById(id).value = '';
}

function clearPayment(){	
	var today	= moment().format("MMM D, YYYY");

	clearInput("total_payment");

	$("#payableForm #paymentdate").val('<?= $transactiondate ?>');
	$("#payableForm #paymentmode").val('cash');
	toggleCheckInfo('cash');
	$("#payableForm #paymentcheckdate").val('');
	$("#payableForm #pv_amount").html("0.00");
}

function confirmChequePrint(row){
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
					var link 	 		= '<?= BASE_URL ?>financials/payment_voucher/generateCheck/'+paymentvoucher+'/'+chequeno;
					// 'popups/generateCheck.php?sid='+paymentvoucher+'&cn='+chequeno;
					window.open(link);
				}
			},
			voucher: {
			label: "Cheque with Voucher",
			className: "btn-success btn-flat",
			callback: function(result) {
					var link 	 		= '<?= BASE_URL ?>financials/payment_voucher/generateCheckVoucher/'+paymentvoucher+'/'+chequeno+'/rv';
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

function showList()
{
	var vnose 		= JSON.stringify(container);
	var	vendor_code	= $('#payableForm #vendor').val();
	voucherno 		= $('#payableForm #h_voucher_no').val();

	var ajax_call	= '';
	ajax.limit 		= 5;
	if (ajax_call != '') {
		ajax_call.abort();
	}

	ajax.vendor 	= vendor_code;
	ajax.voucherno 	= voucherno;
	ajax.vno 		= vnose;
	ajax.task 		= task;
	ajax_call 		= $.post("<?= BASE_URL ?>financials/payment_voucher/ajax/load_payables", ajax )
						.done(function( data ) 
						{
						if ( ! edited) {
							$('#pagination').html(data.pagination);
							$('#paymentModal #payable_list_container').html(data.table);
							
							
						} else {
							$('#pagination').html(data.pagination);
							$('#paymentModal #payable_list_container').html(data.table);
						}

						if (ajax.page > data.page_limit && data.page_limit > 0) {
							ajax.page = data.page_limit;
							showList();
						}
						
						if('<?= $task ?>' == "edit" && !edited)
							$("#payableForm #selected_rows").html(data.json_encode);

						if(!($("paymentModal").data('bs.modal') || {isShown: false}).isShown)
						{
							var check_rows = $('#payableForm #selected_rows').html();
							var obj = (check_rows != "") ? JSON.parse(check_rows) : 0;

							for(var i = 0; i < obj.length; i++)
							{
								$('input#row_check' + obj[i]["row"]).iCheck('check');
							} 
							$('#paymentModal').modal('show');
						};
					});
}

$('#table_search').on('input', function() {
	ajax.page = 1;
	ajax.search = $(this).val();
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
});

$('body').on('click' , '.print_check', function(){
	var cno 	= $(this).closest('tr').find('.chequeno').val();
	var vno  	= $('#h_voucher_no').val();
	window.open('<?=MODULE_URL?>print_check/' + vno +  '/'+ cno , '_blank');
})

function showIssuePayment(){
	var valid		= 0;
	var	vendor_code	= $('#payableForm #vendor').val();
	$('#payableForm #vendor').trigger('blur');
	var h_voucher_no = $("#payableForm #h_voucher_no").val();

	valid			+= validateField('payableForm','vendor', "vendor_help");

	if(valid == 0 && vendor_code != "")
	{
		showList();
		setChequeZero();
		clearChequePayment();
		$('#payable_list_container tbody').html(`<tr>
			<td colspan="4" class="text-center">Loading Items</td>
		</tr>`);
		$('#pagination').html('');
		// showList();
	}
	else
	{
		bootbox.dialog({
			message: "Please select vendor first.",
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

/**COMPUTE TOTAL PAYMENTS APPLIED**/
function addPaymentAmount() {
	var sum 		= 0;
	var subtotal 	= 0;
	var subdiscount = 0;

	var subData 	= 0;
	var subDis		= 0;

	var table 	= document.getElementById('payable_list_container'); // app_payableList
	var count	= table.rows.length;
	// console.log(container);
	var count_container = Object.keys(container).length;
	amount = 0; 
	discount = 0;
	for(i = 0; i < count_container; i++) {
		amt_ = (container[i]['amt']).replace(/,/g,'');
		dis = parseFloat(0) || (container[i]['dis']) ;
		amt = parseFloat(amt_);
		dis = parseFloat(dis) ;
		amount += amt;
		discount += dis;
	}
	if(isNaN(discount)) {
		discount = 0;
	}
	amount = addCommas(amount.toFixed(2));
	$('#total_payment').val(amount);
	discount = addCommas(discount.toFixed(2));
	$('#total_discount').val(discount);

	for(i = 0; i < count; i++)  {  
		//var inputpay = ('<?= $task ?>' == "create") ? 'paymentamount['+i+']' : 'amount_paid['+i+']';
		var row = table.rows[i];
		var inputamt	= row.cells[6].getElementsByTagName("input")[0];
		var inputdis	= row.cells[7].getElementsByTagName("input")[0];
		
		if(inputamt != null)
		{          
			if( (inputamt.value && inputamt != '0' && inputamt.value != '0.00') )
			{                            
				subData = inputamt.value.replace(/,/g,'');
				subDis 	= inputdis.value.replace(/,/g,'');
			}
			else
			{             
				subData = 0;
				subDis  = 0;
			}
			subtotal = parseFloat(subtotal) + parseFloat(subData) ;
			subdiscount = parseFloat(subdiscount) + parseFloat(subDis) ;
		}	

	}

	subtotal	= Math.round(1000*subtotal)/1000;
	subdiscount	= Math.round(1000*subdiscount)/1000;
	// document.getElementById('total_payment').value 		= addCommas(subtotal.toFixed(2));	
	// document.getElementById('total_discount').value 	= addCommas(subdiscount.toFixed(2));	
}

function getCheckAccounts() {
	var cheques_obj = {};
	var cheques = [];

	$('#chequeTable tbody tr').each(function() {
		var chequeaccount = $(this).find('.cheque_account').val();
		var chequeamount = $(this).find('.chequeamount').val();
		cheques_obj[chequeaccount] = (cheques_obj[chequeaccount] >= 0) ? cheques_obj[chequeaccount] + removeComma(chequeamount) : removeComma(chequeamount);
	});

	return cheques_obj
}

var payments 		= <?=$payments;?>;
var container 		= (payments != '') ? payments : [];
var selectedIndex 	= -1;
function getPVDetails(){
	var vendorcode   	= $("#vendor").val();
	var selected_rows 	= JSON.stringify(container);

	var cheques = [];
	cheques_obj = getCheckAccounts();

	for (var chequeaccount in cheques_obj) {
		if (cheques_obj.hasOwnProperty(chequeaccount)) {
			cheques.push({accountcode: chequeaccount, chequeamount: cheques_obj[chequeaccount]});
		}
	}
	
	cheques = JSON.stringify(cheques);

	$("#selected_rows").html(selected_rows);
	
	var data 		 = "checkrows=" + selected_rows + "&vendor=" + vendorcode + "&cheques=" + cheques;

	if(selected_rows == "")
	{
		bootbox.dialog({
			message: "Please select AP Voucher.",
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
		$.post("<?=BASE_URL?>financials/payment_voucher/ajax/getpvdetails", data )
		.done(function(data)
		{
			var discount_code 	= data.discount_code;
			var total_payment = $("#paymentModal #total_payment").val();
			$("#paymentModal").modal("hide");

			if(selected_rows != "")
				$("#paymentmode").removeAttr("disabled");

			if('<?= $task ?>' == "create" || '<?= $task ?>' == "edit") {
				// if(task == 'create'){
				// }
				// load payables
				$("#entriesTable tbody").html(data.table);
				$("#pv_amount").html(total_payment);
				// display total of debit
				addAmountAll("debit");
				
				var count_container = Object.keys(container).length;
				var discount_amount =	0;

				for(i = 0; i < count_container; i++) {
					discount_amount += parseFloat(0) || parseFloat(container[i]['dis']) ;
				}
				$('#entriesTable tbody tr.discount_row').remove();
				var row = $("#entriesTable tbody tr.clone").length +1;
				if( parseFloat(discount_amount) != 0 ){
					discount_amount 	=	addCommas(discount_amount.toFixed(2));
					var ParentRow = $("#entriesTable tbody tr.clone").last();
						ParentRow.after(clone_acct);
					resetIds();
					$("#accountcode\\["+ row +"\\]").closest('tr').addClass('discount_row');
					$('#accountcode\\['+row+'\\]').val(discount_code).trigger('select2.change');
					$('#h_accountcode\\['+row+'\\]').val(discount_code);
					$('#credit\\['+row+'\\]').val(discount_amount);
					disable_acct_fields(row);
				}
				addAmountAll("credit");
			}
		});
	}
}

function selectPayable(id,toggle){
	var check 			= $('#payable_list_container #check'+id);
	var paymentamount 	= $('#payable_list_container #paymentamount'+id);
	var discountamount 	= $('#payable_list_container #discountamount'+id);
	var balance 		= $('#payable_list_container #payable_balance'+id).attr('data-value');

	if(check.prop('checked')){
		if(toggle == 1){
			check.prop('checked', false);
			paymentamount.prop('disabled',true);
			paymentamount.val('');
			discountamount.prop('disabled',true);
			// discountamount.val('');
		}else{
			check.prop('checked', true);
			paymentamount.prop('disabled',false);
			paymentamount.val(balance);
			discountamount.prop('disabled',false);
			// discountamount.val(balance);
		}
	}else{
		if(toggle == 1){
			check.prop('checked', true);
			paymentamount.prop('disabled',false);
			paymentamount.val(balance);
			discountamount.prop('disabled',false);
			// discountamount.val(balance);
		}else{
			check.prop('checked', false);
			paymentamount.prop('disabled',true);
			paymentamount.val('');
			discountamount.prop('disabled',true);
			// discountamount.val('0.00');
		}
	}

	$('#payable_list_container #check'+id).iCheck('update');

	// Get number of checkboxes and assign to textarea
	add_storage(id,balance,0);
	addPaymentAmount();
}

function init_storage(){
	if (localStorage.selectedPayables) {
		container = JSON.parse(localStorage.selectedPayables);
		// console.log(container);
	}
}

function add_storage(id,balance,discount){
	var amount 		= $('#paymentModal #paymentamount'+id).val();
	var newvalue 	= {vno:id,amt:amount,bal:balance,dis:discount};
	if(amount != ''){
		var found = false;
		for(var i=0; element=container[i]; i++) {
			if(element.vno == newvalue.vno) {
				var original_amount 	=	(removeComma(element.amt) > 0) ? removeComma(element.amt)  : 0;
				var original_balance 	=	(removeComma(element.bal) > 0) ? removeComma(element.bal)  : 0;
				var original_discount	=	(removeComma(element.dis) > 0) ? removeComma(element.dis)  : 0;
				
				console.log("Original || "+original_amount+ " | " + original_balance + " | "+original_discount);

				var new_amount 			=	(removeComma(newvalue.amt) > 0) ? removeComma(newvalue.amt) : 0;
				var new_balance 		=	(removeComma(newvalue.bal) > 0) ? removeComma(newvalue.bal)	: 0;
				var discount 			=	(removeComma(newvalue.dis) > 0) ? removeComma(newvalue.dis) : 0;

				var available_balance 	=	(parseFloat(original_balance) - parseFloat(original_discount)) - new_amount;
				available_balance 		=	((available_balance > 0) ? addCommas(available_balance.toFixed(2)) : 0);
			
				var discounted_amount 	=	(parseFloat(new_amount) + parseFloat(original_discount)) - discount;
				discounted_amount 		=	addCommas(discounted_amount.toFixed(2));

				$('#payable_list_container #payable_balance'+id).html(available_balance);
				$('#payable_list_container #paymentamount'+id).val(discounted_amount);

				console.log("New || "+discounted_amount+ " | " + new_balance + " | "+discount);

				found = true;
				if(parseFloat(new_amount) === 0) {
					container[i] = false;
					
				} else {
					newvalue.amt 	=	discounted_amount;
					container[i] = newvalue;
				}            
				// console.log(newvalue);
			}
		}
		if(found === false) {
			var discount_val 	=	0;
			container.push(newvalue);
			$('#payable_list_container #payable_balance'+id).html('0.00');
			$('#payable_list_container #discountamount'+id).val(addCommas(discount_val.toFixed(2)));
		}
		
	}else{
		$('#payable_list_container #payable_balance'+id).html(balance);;
		container = container.filter(function( obj ) {
			return obj.vno !== id;
		});
	}
	localStorage.selectedPayables = JSON.stringify(container);
	init_storage();
	//console.log(JSON.parse(localStorage.getItem('selectedPayables')));
}

/**CHECK BALANCE**/
function checkBalance(val,id){
	var table 			= document.getElementById('payable_list_container');
	var dueamount 		= $('#payable_list_container #payable_balance'+id).attr('data-value');
	var discountamount 	= $('#payable_list_container #discountamount'+id).val();

	dueamount			= removeComma(dueamount);
	discount			= removeComma(discountamount);
	var newval			= removeComma(val);

	var condition = "";
	var input 	  = "";

	condition 			= (parseFloat(newval) || parseFloat(discount)) > (parseFloat(dueamount) );

	if(condition)
	{
		$('#payable_list_container #paymentamount'+id).value = '';
	}else{
		$('#payable_list_container #paymentamount'+id).value = val;
	}

	// console.log(parseFloat(val) + " " + parseFloat(totalamountval));

	if(condition)
	{
		bootbox.alert("Payment amount is greater than the due amount of this Bill.", function() 
		{
			//add_storage(id);
		});
	}
	else
	{
		add_storage(id,dueamount,discount);
	}
		
	addPaymentAmount();	
}

function validateCheques(){
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
		$("#payableForm #chequeCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#payableForm #chequeCountError").addClass('hidden');
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
		$("#payableForm #chequeAmountError").removeClass('hidden');
	}
	else
	{
		$("#payableForm #chequeAmountError").addClass('hidden');
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
function totalPaymentGreaterThanChequeAmount(){
	var total_payment	= document.getElementById('total_payment').value;
	var total_cheque	= document.getElementById('totalcheques').value;

	// original
	// $('#payableForm #disp_tot_payment').html(total_payment);
	// $('#payableForm #disp_tot_cheque').html(total_cheque);

	$('#payableForm #disp_tot_cheque').html(total_payment);
	$('#payableForm #disp_tot_payment').html(total_cheque);


	total_payment    	= total_payment.replace(/\,/g,'');
	total_cheque    	= total_cheque.replace(/\,/g,'');

	if(parseFloat(total_payment) == parseFloat(total_cheque))
	{
		$("#payableForm #paymentAmountError").addClass('hidden');
		return 0;
	}
	else
	{
		$("#payableForm #paymentAmountError").removeClass('hidden');
		return 1;
	}
}

//------------ NOT USED 
// validates invoices from issue payment modal
//-----------
/**VALIDATE AMOUNTS IN SELECTED INVOICES**/
function validateInvoices(){
	var table 	= document.getElementById('payable_list_container'); //app_payableList
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

function applySelected_(){
	var paymentvendor		= $("#payableForm #vendor").val();
	var paymentdate			= document.getElementById('document_date').value;
	var paymentmode			= document.getElementById('paymentmode').value;
	var paymentreference	= document.getElementById('paymentreference').value;
	var voucherno 			= $("#voucherno").val();

	var valid				= 0;

	valid	+= validateField('payableForm','vendor', "vendor_help");
	valid	+= validateField('payableForm','document_date', "document_date_help");
	valid	+= validateField('payableForm','paymentmode', "paymentmode_help");

	if(paymentmode == 'cheque')
	{
		valid	+= validateCheques();
		// valid	+= totalPaymentGreaterThanChequeAmount();
	}

	return valid;
}

function getPayments(voucherno){
	$.post("<?=BASE_URL?>financials/payment_voucher/ajax/get_payments", "voucherno=" + voucherno)
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

/**EDIT RECIEVED PAYMENTS**/
function editPaymentRow(e,id, apvoucherno, voucherno){
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

	// Set Values of Issue Payment Modal
	$("#paymentForm #paymentmode").val(paymentmode).trigger("change");
	$("#paymentForm #paymentaccount").val(paymentaccount).trigger("change");
	$("#paymentForm #paymentreference").val(paymentreference);
	$("#paymentForm #total_payment").val(paymentamount);
	$("#paymentForm #paymentnotes").val(paymentnotes);

	showList();

	$("#voucherno").val(voucherno);
}

function loadCheques(){
	var cheques 		= $('#payableForm #rollArray').val();

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

			$('#payableForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

			$('#payableForm #chequenumber\\['+row+'\\]').val(chequenumber);
			$('#payableForm #chequedate\\['+row+'\\]').val(chequedate);
			$('#payableForm #chequeamount\\['+row+'\\]').val(chequeamount);
			// $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(chequeconvertedamount);

			/**Add new row based on number of rolls**/
			if(row != arr_len)
			{
				$('body .add-cheque').trigger('click');
			}
			// $('#receiptForm #'+row).addClass('disabled');

			$('#payableForm #checkprint\\['+row+'\\]').removeClass('hidden');
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

function validateChequeNumber(id, value, n){
	id = id.replace(/[a-z\[\]]/g, '');

	$.post("<?=BASE_URL?>financials/payment_voucher/ajax/check", "chequevalue=" + value)
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

function clearChequePayment(){
	$('#tbody_cheque .clone').each(function(index) {
		accounts = accounts.splice(1,1);
		if (index > 0) {
			$(this).remove();
		}
	});
	
	setChequeZero();
}

function clear_acct_input(){
	$('.accountcode').val('').change();
	$('.h_accountcode').val('');	
	$('.description').val('');
	$('.debit').val('0.00');
	$('.credit').val('0.00');
	addAmountAll('debit');
	addAmountAll('credit');
}

$(document).ready(function() {
	// Call toggleExchangeRate
	$( "#exchange_rate" ).click(function() {
		toggleExchangeRate();
	});

	// For adding new rol
	$('body').on('click', '.add-data', function() {	
		$('#itemsTable tbody tr.clone select').select2('destroy');
		
		var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

		var ParentRow = $("#itemsTable tbody tr.clone").last();

		clone.clone(true).insertAfter(ParentRow);
		
		setZero();
		
		$('#itemsTable tbody tr.clone select').select2({width: "100%"});
	});
	
	/**ADD NEW BANK ROW**/
	$('body').on('click', '.add-cheque', function() {
		$('#chequeTable tbody tr.clone select').select2('destroy');

		var clone1 = $("#chequeTable tbody tr.clone:first").clone(true);

		var ParentRow = $("#chequeTable tbody tr.clone").last();
		
		clone1.clone(true).insertAfter(ParentRow);
		
		setChequeZero();
		
		$('#chequeTable tbody tr.clone select').select2({width: "100%"});
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({ format: 'M dd, yyyy', autoclose: true });

		// Trigger add new line .add-data
		$(".add-data").trigger("click");

	});

	/**
	* Apply Exchange Rate and converted amount
	*/
	$('#rateForm #btnProceed').click(function(e){
		console.log(e);

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
		
		// Validation
		$("#rateForm").find('.form-group').find('input, textarea, select').trigger('focus');
		valid 	+= $("#rateForm").find('.form-group.has-error').length;

		if(valid == 0)
		{
			if(parseFloat(amount) == 0)
			{
				if(accountentry == '')
				{
					$.post('<?=BASE_URL?>financials/payment_voucher/ajax/get_value', "account=" + account + "&event=exchange_rate")
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
							className: "btn-primary btn-flat",
							callback: function(result) 
							{
								
									var data = "account=" + account + "&event=exchange_rate";
									$.post("<?=BASE_URL?>financials/accounts_payable/ajax/get_value",data)
									.done(function(response) 
									{
										var accountnature		= data.accountnature;

										$('#btnRate').html(exchangerate+'&nbsp;&nbsp;');

										$('#payableForm #amount').val(oldamount);
										$('#payableForm #exchangerate').val(exchangerate);
										$('#payableForm #convertedamount').val(newamount);

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

				$('#payableForm #h_amount').val(oldamount);
				$('#payableForm #h_exchangerate').val(exchangerate);
				$('#payableForm #h_convertedamount').val(newamount);

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
	$('#paymentRateForm #btnProceed').click(function(e){
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
	$('#deleteItemModal #btnYes').click(function() {
		// handle deletion here
		var id = $('#deleteItemModal').data('id');

		var table 		= document.getElementById('entriesTable');
		var rowCount 	= table.rows.length - 2;

		deleteItem(id);
		
		$('#deleteItemModal').modal('hide');
	});

	/**SCRIPT FOR HANDLING DELETE RECORD CONFIRMATION**/
	$('#payableForm').on('click','#btnCancel', function() {
		if(task != 'view'){
			$('#cancelModal').modal('show');
		} else {
			window.location.href	= "<?=BASE_URL?>financials/payment_voucher";
		}
	});

	/**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function()  {
		var invoice		= $("#invoiceno\\[1\\]").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');

		$.post("<?= BASE_URL?>financials/payment_voucher/ajax/delete_payments", "voucher=" + id)
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

	// Deletion of Row
	$('#deleteChequeModal #btnYes').click(function() {
		var row 		= $('#deleteChequeModal').data('row');	
		var table 		= document.getElementById('chequeTable');
		var rowCount 	= table.rows.length - 2;
		var valid		= 1;
		var rowindex	= table.rows[row];

		var account 	= $('#chequeaccount\\['+row+'\\]').val();
		var acctamt 	= $('#chequeamount\\['+row+'\\]').val();

		if($('#chequeaccount\\['+row+'\\]').val() != '') {
			// console.log(checker);
			if(rowCount > 1) {
				table.deleteRow(row);
				checker['acc-'+account] 	-=	acctamt;	
				
				storedescriptionstoarray();
				recomputechequeamts();
				acctdetailamtreset();
				resetChequeIds();
				addAmounts();
				addAmountAll('debit');
				addAmountAll('credit');
			} else {	
				document.getElementById('chequeaccount['+row+']').value 	= '';

				$('#chequeaccount\\['+row+'\\]').trigger("change.select2");
				
				document.getElementById('chequenumber['+row+']').value 		= '';
				document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';//today();
				document.getElementById('chequeamount['+row+']').value 		= '0.00';
				
				checker['acc-'+account] 	-=	acctamt;	
				storedescriptionstoarray();
				recomputechequeamts();
				acctdetailamtreset();
				resetChequeIds();
				addAmounts();
				addAmountAll('debit');
				addAmountAll('credit');
			}
		} else {
			if(rowCount > 1) {
				table.deleteRow(row);	
				checker['acc-'+account] 	-=	acctamt;	
				storedescriptionstoarray();
				recomputechequeamts();
				acctdetailamtreset();
				resetChequeIds();
				addAmounts();
				addAmountAll('debit');
				addAmountAll('credit');
			} else {
				document.getElementById('chequeaccount['+row+']').value 	= '';
				
				$('#chequeaccount\\['+row+'\\]').trigger("change.select2");

				document.getElementById('chequenumber['+row+']').value 		= '';
				document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';//today();
				document.getElementById('chequeamount['+row+']').value 		= '0.00';
				
				checker['acc-'+account] 	-=	acctamt;
				storedescriptionstoarray();
				recomputechequeamts();
				acctdetailamtreset();
				resetChequeIds();
				addAmounts();
				addAmountAll('debit');
				addAmountAll('credit');

			}
		}
		resetIds();
		$('#deleteChequeModal').modal('hide');
	});

	/**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function()  {
		var invoice		= $("#invoiceno\\[1\\]").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');

		$.post("<?= BASE_URL?>financials/payment_voucher/ajax/delete_payments", "voucher=" + id)
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

	$('#cancelModal #btnYes').click(function() {
		var record = document.getElementById('h_voucher_no').value;
		cancelTransaction(record);
	});

	/**TOGGLE ISSUE PAYMENT**/
	$("#btnReceive").click(function(){
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

	$('#paymentreference').on('blur', function(e) {
		if($("#paymentmode").val() == "cheque")
			validateField('paymentForm', e.target.id, e.target.id + "_help");
	});

	// Process New Transaction
	if('<?= $task ?>' == "create"){
		/**SAVE TEMPORARY DATA THROUGH AJAX**/
		$("#payableForm").on('change',function(e)
		{
			if( $("#entriesTable #accountcode\\[1\\]").val() != '' && $("#payableForm #document_date").val() != '' && (parseFloat($("#itemsTable #debit\\[1\\]").val()) > 0 || parseFloat($("#itemsTable #credit\\[1\\]").val()) > 0) && (parseFloat($("#itemsTable #debit\\[2\\]").val()) > 0 || parseFloat($("#itemsTable #credit\\[2\\]").val()) > 0) && $("#payableForm #vendor").val() != '' )
			{
				setTimeout(function() 
				{
					$("#payableForm #btnSave").addClass('disabled');
					$("#payableForm #btnSave_toggle").addClass('disabled');
					
					$("#payableForm #btnSave").html('Saving...');
					
					$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
					.done(function(data)
					{	
						if(data.code == '1')
						{
							$("#payableForm #h_voucher_no").val(data.voucher);
							// window.location.href = '<?//=BASE_URL?>financials/payment_voucher';
						}
					});
				});
			}
		});

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO LIST**/
		$("#payableForm #btnSave").click(function()
		{
			var valid	= 0;
			var selected_rows 	= JSON.stringify(container);
			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			if(selected_rows == "[]")
			{
				bootbox.dialog({
					message: "Please tag Payables first.",
					title: "Oops!",
						buttons: {
						yes: {
						label: "OK",
						className: "btn-primary btn-flat",
						callback: function(result) {
								valid		+= 1;
							}
						}
					}
				});
			}

			if(valid == 0){
				var paymentmode = $('#paymentmode').val();
				/**validate accounts**/
				if(paymentmode == 'cheque'){
					valid 	+=	applySelected_();
				}
				valid		+= validateDetails();
			}

			if(valid == 0)
			{
				$("#payableForm #btnSave").addClass('disabled');
				$("#payableForm #btnSave_toggle").addClass('disabled');
				
				$("#payableForm #btnSave").html('Saving...');

				$("#payableForm #h_save").val("h_save");

				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function(data)
				{	
					if(data.code == 1)
					{
						$("#payableForm #h_voucher_no").val(data.voucher);
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

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO CREATE NEW INVOICE**/
		$("#payableForm #save_new").click(function()
		{
			var valid	= 0;

			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			valid		+= validateField('payableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			var paymentmode = $('#paymentmode').val();
			/**validate accounts**/
			if(paymentmode == 'cheque'){
				valid 	+=	applySelected_();
			}
			if(valid == 0)
			{
				$("#payableForm #h_save").val("h_save_new");

				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function(data)
				{
					if(data.code == 1)
					{
						$("#payableForm #h_voucher_no").val(data.voucher);
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

		/**FINALIZE TEMPORARY DATA AND REDIRECT TO PREVIEW INVOICE**/
		$("#payableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			valid		+= validateField('payableForm','duedate', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			var paymentmode = $('#paymentmode').val();
			/**validate accounts**/
			if(paymentmode == 'cheque'){
				valid 	+=	applySelected_();
			}
			if(valid == 0)
			{
				$("#payableForm #h_save").val("h_save_preview");

				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function(data)
				{
					if(data.code == 1)
					{
						$("#payableForm #h_voucher_no").val(data.voucher);
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

	} else if( task == "edit") {
		var paymentmode = $("#paymentmode").val();

		var selected_rows 	= JSON.stringify(container);
		$('#selected_rows').html(selected_rows);

		if(paymentmode == "cheque"){
			// toggleCheckInfo(paymentmode);
			// loadCheques();
			addAmounts();
		}

		$("#paymentmode").removeAttr("disabled");

		/**SAVE CHANGES AND REDIRECT TO LIST**/
		$("#payableForm #btnSave").click(function(e)
		{
			var valid			= 0;
			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			valid		+= validateField('payableForm','due_date', "due_date_help");
			
			/**validate items**/
			var paymentmode = $('#paymentmode').val();
			/**validate accounts**/
			if(paymentmode == 'cheque'){
				valid 	+=	applySelected_();
			}
			valid		+= validateDetails();
			
			if(valid == 0)
			{
				$("#payableForm #btnSave").addClass('disabled');
				$("#payableForm #btnSave_toggle").addClass('disabled');
				
				$("#payableForm #btnSave").html('Saving...');

				$("#payableForm #h_save").val("h_save");
				
				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function(data)
				{
					if(data.code == 1)
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
			
		/**SAVE CHANGES AND REDIRECT TO CREATE NEW INVOICE**/
		$("#payableForm #save_new").click(function()
		{
			var valid	= 0;
			
			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			valid		+= validateField('payableForm','due_date', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			var paymentmode = $('#paymentmode').val();
			/**validate accounts**/
			if(paymentmode == 'cheque'){
				valid 	+=	applySelected_();
			}
			if(valid == 0)
			{
				$("#payableForm #btnSave").addClass('disabled');
				$("#payableForm #btnSave_toggle").addClass('disabled');
				
				$("#payableForm #btnSave").html('Saving...');

				$("#payableForm #h_save").val("h_save_new");
				
				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function(data)
				{
					if(data.code == 1)
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
			
		$("#payableForm #save_preview").click(function()
		{
			var valid	= 0;
			
			/**validate vendor field**/
			valid		+= validateField('payableForm','document_date', "document_date_help");
			valid		+= validateField('payableForm','vendor', "vendor_help");
			
			valid		+= validateField('payableForm','duedate', "due_date_help");
			
			/**validate items**/
			valid		+= validateDetails();
			var paymentmode = $('#paymentmode').val();
			/**validate accounts**/
			if(paymentmode == 'cheque'){
				valid 	+=	applySelected_();
			}
			if(valid == 0)
			{
				$("#payableForm #btnSave").addClass('disabled');
				$("#payableForm #btnSave_toggle").addClass('disabled');
				
				$("#payableForm #btnSave").html('Saving...');

				$("#payableForm #h_save").val("h_save_preview");
				
				$.post("<?=BASE_URL?>financials/payment_voucher/ajax/create_payments",$("#payableForm").serialize())
				.done(function( data ) 
				{
					if(data.code == 1)
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
	if(hash != ''){
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
				location.href	= "<?= BASE_URL ?>financials/payable";//'index.php?mod=financials&type=accounts_payable';
			});
		}
	}
	else{
		$("#receiptForm").addClass('hidden');
	}

	$('#app_payableList').on('ifToggled', '.icheckbox', function(event){
		event.type = "checked";
		var selectid = $(this).attr('row');
		var selecttoggleid = $(this).attr('toggleid');
		
		selectPayable(selectid,selecttoggleid);
	});

	$('body').on('click','#apv',function(e){
		showIssuePayment();
	});

	$('body').on('click', '.add-entry', function()  {	

		var ParentRow = $("#entriesTable tbody tr:not(.added_row)").last();
			ParentRow.after(clone_acct);
		setZero();
		drawTemplate();
	});

	var cheque_detail 	=	$('#paymentmode').val();

	$('#change_vendor_modal').on('click','#yes_to_reset',function(){
		
		$('#ap_items .clone').each(function(index) {
			if (index > 0) {
				$(this).remove();
			}
		});
		
		setChequeZero()
		clearChequePayment();

		$('#change_vendor_modal').modal('hide');
		
		container = [];
		clearPayment();
	});

	$('#vendor').on('change',function(){
		if ($('.accountcode').val()	 != '' || $('.chequeaccount').val()	 != '' ) {
			$('#change_vendor_modal').modal('show');
		} 
	});

	$('#entriesTable').on('change','.accountcode',function(){
		var vendor 	= $('#vendor').val();
		var payable = JSON.stringify(container);
		var flag 	= 1;
		
		var account = $(this).val();

		if( account != "" ){
			$(this).closest('tr').find('.h_accountcode').val(account);
			if( vendor == "" ){
				bootbox.dialog({
					message: "Please select a Vendor First",
					title: "Oops!",
						buttons: {
							yes: {
								label: "OK",
								className: "btn-primary btn-flat",
								callback: function(result) {
									clear_acct_input();
								}
						}
					}
				});
			} else if( payable == "[]"){
				bootbox.dialog({
					message: "Please tag payables first.",
					title: "Oops!",
						buttons: {
						yes: {
						label: "OK",
						className: "btn-primary btn-flat",
						callback: function(result) {
								clear_acct_input();
							}
						}
					}
				});
			}
		}
	});
}); // end

</script>