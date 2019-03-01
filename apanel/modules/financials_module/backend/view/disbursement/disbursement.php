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
		<input type = "hidden" id = "bank_name" name = "bank_name" >
		<?php if($task == 'edit') { ?>
			<input type = "hidden" id = "bankcode" name = "bankcode" value = "<?php echo $bankcode; ?>">
		<?php } else if($task == 'create') { ?>
			<input type = "hidden" id = "bankcode" name = "bankcode">
		<?php } ?>
		<input type = "hidden" id = "book_id" >
		<input type = "hidden" id = "book_ids" name = "book_ids" >
		<input type = "hidden" id = "book_last" name = "book_last" >
		<input type = "hidden" id = "book_end" name = "book_end" >
		<div class="box box-primary">
			<div class="box-body">
				<div class = "row">
					<div class = "col-md-12">&nbsp;</div>
					<div class = "col-md-11">
						<div class = "row">
							<div class="col-md-12 col-xs-12">
								<div class="row">
									<div class="col-md-offset-1 col-md-10">
										<h3><?php echo $status_badge;?></h3>
										<div>
										</div>
									</div>
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
										->setAttribute(array('readonly' => '','data-date-start-date'=> $close_date))
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
											->setPlaceholder('Select Supplier')
											->setSplit('col-md-4', 'col-md-8')
											->setName('vendor')
											->setId('vendor')
											->setList($vendor_list)
											->setValue($vendorcode)
											->setValidation('required')
											->setButtonAddon('plus')
											->draw($show_input);
										}else{
											echo $ui->formField('text')
											->setLabel('Supplier ')
											->setSplit('col-md-4', 'col-md-8')
											->setValue($vendorcode)
											->draw($show_input);

											echo '<input type="hidden" id="vendor" name="vendor" value="'.$vendorcode.'">';
										}
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
										->setList(array("cash" => "Cash", "cheque" => "Check"))
										->setAttribute(
											array(
												"onChange" => "toggleCheckInfo(this.value);"
											)
										)
										->setValue($paymenttype)
										->draw($show_input);
										?>
									</div>
								</div>
								<div class = "row">
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
										<input type="hidden" id="total_payment" name="total_payment"/>
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
										->setMaxLength(300)
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
						
					</div>
				</div>					
				<!--Cheque Details-->
				<div class="panel panel-default <?php echo $show_cheques?>" id="cheque_details">
					<div class="panel-heading">
						<strong>Check Details</strong>
					</div>
					<div class="has-error">
						<span id="chequeCountError" class="help-block hidden small">
							<i class="glyphicon glyphicon-exclamation-sign"></i> 
							Please specify at least one(1) check.
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
							The Check Number you entered has already been used
						</span>
					</div>
					<div class="table-responsive">
						<table class="table table-condensed table-bordered table-hover" id="chequeTable">
							<thead>
								<tr class="info">
									<th class="col-md-4">Bank Account</th>
									<th class="col-md-3">Check Number</th>
									<th class="col-md-2">Check Date</th>
									<th class="col-md-2">Amount</th>
									<?php if($main_status != 'cancelled'){ ?><th class="col-md-1">Action</th><?php } ?>
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
										->setMaxLength(30)
								// ->setValidation('required alpha_num')
										// ->setAttribute(array("readOnly"=>""))
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
										->setValidation('decimal')
										->setMaxLength(20)
										->setAttribute(array("onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
										->setValue("0.00")
										->draw(true);
										?>
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
										<input type="hidden" id="not_cancelled[1]" value="no" name="not_cancelled[1]" class="not_cancelled">
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
										$stat 			=	$cheque['stat'];
										$status 		=  	($stat == 'cancelled') ? "cancelled" : ''; 
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
												->setClass('chequeaccount '.$status.'')
												->setList($cash_account_list)
												->setValue($accountcode)
										// ->setAttribute(array($status))
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
												->setClass('chequenumber '.$status.' ' )
												->setMaxLength(30)
												->setValidation('required alpha_num')
										// ->setAttribute(array("onBlur" => "validateChequeNumber(this.id, this.value, this)"))
										// ->setAttribute(array($status))
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
													->setClass("datepicker-input ".$status."")
													->setName('chequedate['.$row.']')
													->setId('chequedate_' . $row)
													->setMaxLength(50)
													->setValue($chequedate)
											// ->setAttribute(array($status))
													->draw($show_input);
													?>
												</div>
											</td>
											<td class="text-right"> 
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12 field_col')
												->setClass("chequeamount text-right ".$status."")
												->setName('chequeamount['.$row.']')
												->setId('chequeamount['.$row.']')
												->setValidation('decimal')
												->setMaxLength(20)
												->setAttribute(array("onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
										// ->setAttribute(array($status))
												->setValue(number_format($chequeamount,2))
												->draw($show_input);
												?>
											</td>	

											<? if($show_input):?>
												<? if($stat != 'cancelled'):?>
													<td class="text-center">
														<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
														<input type="hidden" id="not_cancelled" value="no" name="not_cancelled[<?=$row?>]" class="not_cancelled">
													</td>
													<?php else : ?>
														<td class="text-center">
															<button type="button" class="btn btn-danger btn-flat confirm-delete delete <?=$status?>" data-id="<?=$row?>" name="chk_[]"  style="outline:none;"><span class="glyphicon glyphicon-ban-circle"></span></button>
															<input type="hidden" id="not_cancelled" value="yes" name="not_cancelled[<?=$row?>]" class="not_cancelled">
														</td>
													<?php endif; ?>

													<?php else :  ?>
														<?php if($main_status == 'posted'){?>
															<td class="text-center">
																<button type="button" class="btn btn-info btn-flat print_check <?=$status?>"  style="outline:none;" ><span class="glyphicon glyphicon-download-alt"></span></button>
															</td>	
														<?php } ?>
													<?php endif; ?>
												</tr>	
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
													->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmounts();", "onClick" => "SelectAll(this.id);"))
													->setValue("0.00")
													->draw(true);
													?>
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="<?=$row?>" name="chk_[]" style="outline:none;" onClick="confirmChequeDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
													<input type="hidden" id="not_cancelled[1]" value="no" name="not_cancelled[1]" class="not_cancelled">

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
												<a type="button" class="btn btn-link add-cheque hidden"  style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Check</a>
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
												<?endif;?>
												<td class="text-right"></td>
											</tr>	
										</tfoot>
									</table>
								</div>
							</div>
							<!--End of Cheque Details-->
							<hr/>
							<!--Account Entries-->
							<div class="panel panel-default" id="accounting_details">
								<div class="panel-heading">
									<strong>Accounting Details</strong>
								</div>
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
								<div class="table-responsive">
									<table class="table table-hover table-condensed " id="entriesTable">
										<thead>
											<tr class="info">
												<th class="col-md-2">Budget Code</th>
												<th class="col-md-3">Account</th>
												<th class="col-md-3">Description</th>
												<th class="col-md-2">Debit</th>
												<th class="col-md-3">Credit</th>
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
														->setName("budgetcode[".$row."]")
														->setClass("budgetcode")
														->setId("budgetcode[".$row."]")
														->setList($budget_list)
														->draw($show_input);
														?>
													</td>
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
														->setSplit('', 'col-md-12 field_col')
														->setName('debit['.$row.']')
														->setId('debit['.$row.']')
														->setClass("text-right debit")
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
														->setValue(number_format($debit, 2))
														->setValidation('decimal')
														->draw($show_input);
														?>
													</td>
													<td>
														<?php
														echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setName('credit['.$row.']')
														->setId('credit['.$row.']')
														->setClass("text-right account_amount  credit")
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
														->setValue(number_format($credit, 2))
														->setValidation('decimal')
														->draw($show_input);
														?>
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
														<!-- <input type="hidden" id="not_cancelled" value="no" name="not_cancelled[]" class="not_cancelled"> -->
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
														->setName("budgetcode[".$row."]")
														->setClass("budgetcode")
														->setId("budgetcode[".$row."]")
														->setList($budget_list)
														->draw($show_input);
														?>
													</td>
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
														->setSplit('', 'col-md-12 field_col')
														->setName('debit['.$row.']')
														->setId('debit['.$row.']')
														->setClass("text-right debit")
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
														->setValue(number_format($debit, 2))
														->setValidation('decimal')
														->draw($show_input);
														?>
													</td>
													<td>
														<?php
														echo $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setName('credit['.$row.']')
														->setClass("text-right account_amount credit")
														->setId('credit['.$row.']')
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);"))
														->setValue(number_format($credit, 2))
														->setValidation('decimal')
														->draw($show_input);
														?>
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
														<!-- <input type="hidden" id="not_cancelled" value="no" name="not_cancelled[]" class="not_cancelled"> -->

													</td>
												</tr>

												<?php
											}else{ 
												$aPvJournalDetails 	= $data['details'];
												$detail_row 		= '';
												if(!empty($aPvJournalDetails)){
													foreach ($aPvJournalDetails as $aPvJournalDetails_Index => $aPvJournalDetails_Value) {
														$accountcode 		= $aPvJournalDetails_Value->accountcode;
														$budgetcode 		= $aPvJournalDetails_Value->budgetcode;
														$detailparticulars 	= $aPvJournalDetails_Value->detailparticulars;
														$debit 				= $aPvJournalDetails_Value->debit;
														$credit 			= $aPvJournalDetails_Value->credit;
														$ischeck 			= isset($aPvJournalDetails_Value->ischeck) 	?	$aPvJournalDetails_Value->ischeck	:	"no";

														$disable_code 		= "";
														$disable_budget 	= "";
														$added_class 		= "";
														$indicator 			= "";
														if($aPvJournalDetails_Index > 0 && $paymenttype == 'cheque' && $ischeck == 'yes'){
															$disable_debit		= 'readOnly';
															$disable_credit		= 'readOnly';
															$disable_code 		= 'disabled';
															$added_class 		= 'added_row';
															$indicator 			= "cheque";
														} else {
															$disable_debit		= ($debit > 0) ? '' : 'readOnly';
															$disable_credit		= ($credit > 0) ? '' : 'readOnly';
														}

														$total_debit 		+= $debit;
														$total_credit 		+= $credit;
														$detail_row	.= '<tr class="clone '.$added_class.'">';

														$detail_row	.= '<td>';
														$detail_row	.= $ui->formField('dropdown')
														->setPlaceholder('Select One')
														->setSplit('', 'col-md-12')
														->setName("budgetcode[".$row."]")
														->setClass("budgetcode")
														->setId("budgetcode[".$row."]")
														->setValue($budgetcode)
														->setList($budget_list)
														->draw($show_input);

														$detail_row .= '</td>';

														$detail_row	.= '<td>';
														$detail_row .= $ui->formField('dropdown')
														->setPlaceholder('Select One')
														->setSplit('', 'col-md-12')
														->setName("accountcode[".$row."]")
														->setClass("accountcode")
														->setId("accountcode[".$row."]")
														->setAttribute(array($disable_code))
														->setList($account_entry_list)
														->setValue($accountcode)
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
														->setSplit('', 'col-md-12 field_col')
														->setName('debit['.$row.']')
														->setClass("debit text-right $indicator")
														->setId('debit['.$row.']')
														->setValidation('decimal')
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('debit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", $disable_debit))
														->setValue(number_format($debit, 2))
														->setValidation('decimal')
														->draw($show_input);
														$detail_row	.= '</td>';

														$detail_row	.= '<td class="text-right">';
														$detail_row .= $ui->formField('text')
														->setSplit('', 'col-md-12 field_col')
														->setName('credit['.$row.']')
														->setValidation('decimal')
														->setClass("account_amount credit text-right $indicator")
														->setId('credit['.$row.']')
														->setAttribute(array("maxlength" => "20", "onBlur" => "formatNumber(this.id); addAmountAll('credit');", "onClick" => "SelectAll(this.id);", "onKeyPress" => "isNumberKey2(event);", $disable_credit))
														->setValue(number_format($credit, 2))
														->setValidation('decimal')
														->draw($show_input);
														$detail_row	.= '</td>';

														if( $show_input ){
															$detail_row .= '<td class="text-center">';
															$detail_row .= '<button type="button" class="btn btn-danger btn-flat confirm-delete delete" data-id="'.$row.'" name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');"  '.$disable_code.'><span class="glyphicon glyphicon-trash"></span></button>';
										// $detail_row .= '<input type="hidden" id="not_cancelled" value="no" name="not_cancelled[]" class="not_cancelled">';
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
										<?if($show_input):?>
										<? if($task == 'create') {
											echo $ui->addSavePreview()
											->addSaveNew()
											->addSaveExit()
											->drawSaveOption();
										}

										if($task == 'view') {
											echo $ui->drawSubmit($show_input);
										} else if($task == 'edit') { ?>
											<input type = "button" value = "Save" name = "save" id = "save" class="btn btn-primary btn-sm btn-flat"/>
										<?php }
										?>
										<?endif;?>
										&nbsp;
										<?
										if(($main_status == 'open' && $has_access == 1) && $restrict_dv){
											echo '<a role = "button" href="'.MODULE_URL.'edit/'.$generated_id.'" class="btn btn-primary btn-flat">Edit</a>';
										}
										?>
										<input type = "hidden" value = "" name = "h_save" id = "h_save"/>
										<button type="button" class="btn btn-default btn-flat" data-id="<?=$generated_id?>" id="btnCancel">Cancel</button>
									</div>
								</div>

							</div>
						</form>
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
									Are you sure you want to Cancel this Transaction?
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


					<div class="modal fade" id="noBankModal" tabindex="-1" data-backdrop="static">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<span class="glyphicon glyphicon-warning-sign"> Notice!
									</div>
									<div class="modal-body">
										Please create bank on Bank Maintenance 
										<input type="hidden" id="recordId"/>
									</div>
									<div class="modal-footer">
										<div class="row row-dense">
											<div class="col-md-12 center">
												<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> 


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
													<button type="button" class="btn btn-default btn-flat" id="no_to_reset" data-dismiss="modal">No</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						

						<!-- Check Modal  -->
						<div class="modal fade" id="checkModal" tabindex="-1" data-backdrop="static">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header ">
										<strong>Select Book Number</strong>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<div class="modal-body">
										<select id="booknum_list" class=""> 

										</select>
										<input type="hidden" id="current_bank" value=""/>
									</div>
									<div class="modal-footer">
										<div class="row row-dense">
											<div class="col-md-12 center">
												<div class="btn-group">
													<button type="button" class="btn btn-primary btn-flat" id="check_yes">Select</button>
												</div>
												&nbsp;&nbsp;&nbsp;
												<div class="btn-group">
													<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Check Modal  -->
						<div class="modal fade" id="set_check_modal" tabindex="-1" data-backdrop="static">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header ">
										<strong>Select Book Number</strong>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<div class="modal-body">
							<!-- <select id="booknum_list" class=""> 

							</select> -->
							Please set default check book on Bank Maintenance!
						</div>
						<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 center">
									<!-- <div class="btn-group">
										<button type="button" class="btn btn-primary btn-flat" id="check_yes">Select</button>
									</div> -->
									&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Ok</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="nocheckModal" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<strong>Confirmation</strong>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							There are no available checks on this bank. Please verify check number series in bank maintenance.
							<input type="hidden" id="recordId"/>
						</div>
						<div class="modal-footer">
							<div class="row row-dense">
								<!-- <div class="col-md-12 center">
									<div class="btn-group">
										<button type="button" class="btn btn-primary btn-flat" id="check_yes">Yes</button>
									</div>
									&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
									</div>
								</div> -->
							</div>
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

			<div class="modal fade" id="chequeList" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							Cheque List
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-condensed table-bordered table-hover" id="table_chequelist">
									<thead>
										<tr class="info">
											<th class="col-md-1 text-center">First Cheque No.</th>
											<th class="col-md-1 text-center">Last Cheque No.</th>
											<th class="col-md-1 text-center">Next Cheque No.</th>
										</tr>
									</thead>
									<tbody id="cheque_list_container">
									</tbody>
								</table>
							</div>
							<div id="cheque_pagination"></div>
						</div>
						<div class="modal-footer">
						</div>
					</div>
				</div>
			</div>

			<script>
				<?php if($task == 'edit') : ?>
					$(document).ready(function() {
						$('#bank_name').val($('.cheque_account :selected').text());
					});
				<?php endif; ?>
				function checkifpairexistsinbudget(accountcode, budget, field, type){
					$.post('<?=MODULE_URL?>ajax/checkifpairexistsinbudget', "accountcode=" + accountcode + "&budgetcode=" + budget, function(data) {
						if(data.result == 1) {
							$('#accountchecker-modal').modal('hide');
							$('#accounterror').html('');
							if(type == "budget") {
								field.closest('.form-group').removeClass('has-error');
							} else {
								field.closest('tr').find('.budgetcode').find('.form-group').removeClass('has-error');
							}
						} else {
							$('#accountchecker-modal').modal('show');
							$('#accounterror').html("The account is not in your Budget Code.");
							if(type == "budget") {
								field.closest('.form-group').addClass('has-error');
							} else {
								field.closest('tr').find('.budgetcode').find('.form-group').addClass('has-error');
							}
						}
					});
				}

				$('.accountcode').on('change', function() {
					var accountcode = $(this).val();
					var acctfield 	= $(this);
					var budget 		= $(this).closest('tr').find('.budgetcode').val();
					row = $(this).closest('tr');
					if(budget=="") {
						$.post('<?=MODULE_URL?>ajax/checkifacctisinbudget', "accountcode=" + accountcode, function(data) {
							if(data.result == 1){
								acctfield.closest('tr').find('.budgetcode').closest('.form-group').addClass('has-error');
							} else {
								acctfield.closest('tr').find('.budgetcode').closest('.form-group').removeClass('has-error');
							}
						});
					} else {
						checkifpairexistsinbudget(accountcode, budget, acctfield, 'item');
					}
				});

				$('#entriesTable').on('change','.budgetcode',function(){
					var budgetfield= $(this);
					var budgetcode = $(this).val();
					var accountcode= $(this).closest('tr').find('.accountcode').val();

					if(accountcode){
						checkifpairexistsinbudget(accountcode, budgetcode, budgetfield, 'budget');
					}
				});	

				var edited = false;
				$('#paymentModal').on('blur', 'input', function() {
					edited = true;
				});

				function addVendorToDropdown() {

					var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
					var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

					$('<option value="'+optionvalue+'">'+optionvalue+" - "+optiondesc+'</option>').insertAfter("#payableForm #vendor option:last-child");
					$('#payableForm #vendor').val(optionvalue);

					// getPartnerInfo(optionvalue);
					$('#vendor').val(optionvalue).trigger('change');

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
			->setHeader('Add a Supplier')
			->draw();
			?>
			<script>
				var ajax 	 = {};

				var id_array 		= [];
				var accounts 		= [];
				var acct_details 	= [];
				var cheque 			= [];

				var checker 	= new Array();
				var cheque_arr 	= [];
				var table 		= document.getElementById('ap_items');
				var newid 		= table.rows.length;
				newid 		= parseFloat(newid);

				var task 		= '<?= $task ?>';

				var min_row 	=	2;

		// Get Initial Clone of First Row. In this case, disabled cheque entries. 
		var initial_clone 		 = $('#entriesTable tbody tr.clone:first');
		// enable them to allow a cloned row with enabled dropdown and input fields
		var initial_debit 		= initial_clone.find('.debit').val();
		var initial_credit 		= initial_clone.find('.crebit').val() || 0;
		initial_clone.find('.debit').attr("value",0);
		initial_clone.find('.credit').attr("value",0);
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

				if(description!="" ){
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

		// Check Array //
		

		var currentcheck = {}; 
		var newnext = [];
		var newlast = [];
		var book_ids = {};
		var book_last = {};
		var book_end = {};
		var curr_bank_seq = [];
		var curr_bank = [];

		function storechequetobank(){
			var new_cheque 	=	[];
			$('#chequeTable tbody tr').each(function() {
				var val 	= $(this).find('.cheque_account').val();
				var check_num 	= $(this).find('.chequenumber').val();
				new_cheque.push(val);
				if(check_num!="" ){
					curr_bank_seq[val] = check_num;
				}
			});
			curr_bank_seq.forEach(function(val, index) {
				if (new_cheque.indexOf(index.toString()) < 0) {
					curr_bank_seq[index] = 0;
				}
			});
		}
		// console.log(new_cheque);
		var cheque_element = '';
		var val_bank = '';
		$('#chequeTable .cheque_account').on('change', function()  {
			storedescriptionstoarray();
			storechequetobank();
			if ($('#entriesTable tbody tr.clone select').data('select2')) {
				$('#entriesTable tbody tr.clone select').select2('destroy');
			}

			val_bank = $('.cheque_account :selected').text();
			$('#current_bank').val(val_bank);
			$('#bank_name').val(val_bank);
			var num = curr_bank_seq[val_bank] || 0;
			cheque_element = $(this);
			
			$.post("<?=BASE_URL?>financials/disbursement/ajax/getNumbers" , { bank: val_bank, curr_seq: num } ).done(function(data){
				if(data.table){
			
					if(data.count == 1) {
						cheque_element.closest('tr').find('.chequenumber').val(data.table);
						$('#bankcode').val(data.bankcode);
					} else {
						var row = $("#chequeTable tbody tr").length;
						$('#table_chequelist tbody').html(data.table);
						$('#bankcode booknum').val(data.bankcode);
						$('#cheque_pagination').html(data.pagination);
						$('#chequeList').modal('show');
					}
				} else {
					$('#nocheckModal').modal('show');
					$('#entriesTable #accountcode\\[2\\]').val('').trigger('change');
					$('.chequenumber').val('');
				}
			});

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
				if(task == 'create' && index != 0 && account == "" || account == "" && index != 0 && ischeck == 'yes'){
					$(this).closest('tr').remove();
				}
			});

			row = $("#entriesTable tbody tr.clone").length + 1;
			cheque_arr.forEach(function(account) {
				var ParentRow = $("#entriesTable tbody tr.clone").last();
				$('#entriesTable tbody tr.added_row').find('.ischeck').val('yes');
				ParentRow.after(clone_acct);
				resetIds();
				$("#budgetcode\\["+ row +"\\]").val('').trigger('change.select2');
				$("#accountcode\\["+ row +"\\]").val(account).trigger('change.select2');
				$("#entriesTable button#"+row).prop('disabled',true);
				$("#entriesTable debit#"+row).prop('disabled',true);
				$("#accountcode\\["+ row +"\\]").closest('tr').addClass('added_row');
				$('#entriesTable tbody tr.added_row').find('.ischeck').val('yes');
				$("#accountcode\\["+ row +"\\]").val(account).trigger('change.select2');
				disable_acct_fields(row);
				row++;
			});

			accounts.push(val_bank);
			recomputechequeamts();
			acctdetailamtreset();
			displaystoreddescription();
			drawTemplate(); 
		});

		$('#check_yes').on('click', function(){
			storechequetobank();
			var booknum = $('#checkModal #booknum_list').val();
			var val = $('#current_bank').val();
			if (typeof book_ids[val] === 'undefined') {
				book_ids[val] = [];
			}
			book_ids[val].push(booknum);
			$('#book_ids').val(JSON.stringify(book_ids));


			$.post("<?=BASE_URL?>financials/disbursement/ajax/get_next_booknum", 'bank='+val+'&bookno='+booknum ).done(function(data){
				if (data){
					newnext[val] = parseFloat(data.nno) || 0;
					newlast[val] = parseFloat(data.last) || 0;

					if (typeof book_end[val] === 'undefined') {
						book_end[val] = [];
					}
					book_end[val].push(data.last);
					$('#book_end').val(JSON.stringify(book_end));

					var row = $("#chequeTable tbody tr").length;
					if (typeof cheque["bank-"+val] === 'undefined') {
						$('#chequeTable #chequenumber\\['+row+'\\]').val(newnext[val]);
					}
					currentcheck[val] = newnext[val];
				}
				$('#chequeTable #chequenumber\\['+row+'\\]').val(newnext[val]);	
			});
			
			$('#checkModal').modal('hide');
		});

		$('#table_chequelist #cheque_list_container').on('click', 'tr', function() {
			storechequetobank();
			var num = $(this).find('.nextchequeno').html();
			var bankcode = $(this).find('.bankcode').val();
			$('#bankcode').val(bankcode);
			curr_bank_seq[val_bank] = num;
			cheque_element.closest('tr').find('.chequenumber').val(num);
			$('#chequeList').modal('hide');
			if (typeof book_ids[val_bank] === 'undefined') {
				book_ids[val_bank] = [];
			}
			book_ids[val_bank].push(num);
			$('#book_ids').val(JSON.stringify(book_ids));
		});

		function getnum(val, next){ 
			$('#check_yes').on('click', function(){
				$.post("<?=BASE_URL?>financials/disbursement/ajax/getCheckdtl", 'bank='+val+'&current_check='+next ).done(function(data){
					ajax.val = val;
					ajax.next = next;
					// $.post("<?=BASE_URL?>financials/disbursement/ajax/update_check_status", ajax ).done(function(data){
						$('#checkModal').modal('hide');
						// 	})
					})
			})       
		}

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
				} else {10.00
					var ischeck 	=	$(this).closest('tr').find('.ischeck').val();
					var ca = checker['acc-' + $(this).val()] || '0.00';
					ca = removeComma(ca);
					if($(this).val() == ""){
						ca = '0.00';
					}
					total_payment += ca;		
					if(ischeck == 'yes'){
						$(this).closest('tr').find('.account_amount').val(addComma(ca));
					}
					$(this).closest('tr').find('.h_accountcode').val($(this).val());
				}	
			});
			$('#total_payment').val(total_payment);
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

		/**RESET IDS OF ROWS**/
		function resetIds() {
			var table 	= document.getElementById('entriesTable');
			var count	= table.rows.length - 3;

			x = 1;
			for(var i = 1;i <= count;i++) {
				var row = table.rows[i];

				row.cells[0].getElementsByTagName("select")[0].id 	= 'budgetcode['+x+']';
				row.cells[1].getElementsByTagName("select")[0].id 	= 'accountcode['+x+']';
				row.cells[1].getElementsByTagName("input")[0].id 	= 'h_accountcode['+x+']';
				row.cells[2].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
				row.cells[2].getElementsByTagName("input")[1].id 	= 'ischeck['+x+']';
				row.cells[3].getElementsByTagName("input")[0].id 	= 'debit['+x+']';
				row.cells[4].getElementsByTagName("input")[0].id 	= 'credit['+x+']';

				row.cells[0].getElementsByTagName("select")[0].name = 'budgetcode['+x+']';
				row.cells[1].getElementsByTagName("select")[0].name = 'accountcode['+x+']';
				row.cells[1].getElementsByTagName("input")[0].name 	= 'h_accountcode['+x+']';
				row.cells[2].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
				row.cells[2].getElementsByTagName("input")[1].name 	= 'ischeck['+x+']';
				row.cells[3].getElementsByTagName("input")[0].name 	= 'debit['+x+']';
				row.cells[4].getElementsByTagName("input")[0].name 	= 'credit['+x+']';

				row.cells[5].getElementsByTagName("button")[0].setAttribute('id',x);
				row.cells[1].getElementsByTagName("select")[0].setAttribute('data-id',x);
				row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

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
				row.cells[4].getElementsByTagName("input")[0].id 	= 'not_cancelled['+x+']';

				row.cells[0].getElementsByTagName("select")[0].name = 'chequeaccount['+x+']';
				row.cells[1].getElementsByTagName("input")[0].name 	= 'chequenumber['+x+']';
				row.cells[2].getElementsByTagName("input")[0].name 	= 'chequedate['+x+']';
				row.cells[3].getElementsByTagName("input")[0].name 	= 'chequeamount['+x+']';
				row.cells[4].getElementsByTagName("input")[0].name 	= 'not_cancelled['+x+']';

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
		function setZero(id="") {
			resetIds();

			var table 		= document.getElementById('entriesTable');
			var newid 		= table.rows.length - 2;
			var account		= document.getElementById('accountcode['+newid+']');

			if(document.getElementById('accountcode['+newid+']') != null) {
				document.getElementById('budgetcode['+newid+']').value 			= '';
				document.getElementById('accountcode['+newid+']').value 		= '';
				document.getElementById('h_accountcode['+newid+']').value 		= '';
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
				document.getElementById('not_cancelled['+newid+']').value 	= 'no';

				$('#chequeaccount\\['+newid+'\\]').trigger("change.select2");
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

			if($('#'+id)[0].type != undefined || $('#'+id)[0].type == 'select-one'){
				field	= $("#"+form+" #"+id).val();
			}

			if((field == '' || parseFloat(field) == 0) || help_block == "exrateamount_help" || field == "none" )
			{
				$("#"+form+" #"+id)
				.closest('.field_col')
				.addClass('has-error');

				$("#"+form+" #"+id)
				.find('.form-group')
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
						$.post("<?=BASE_URL?>financials/disbursement/ajax/delete_row",{table:datatable,condition:condition})
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
						table.deleteRow(row);	
						resetIds();
						addAmountAll('debit');
						addAmountAll('credit');
					}
				}
				else
				{	
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
					$("#accounting_details #totalAmountError").removeClass('hidden');
					$('#accounting_details .accountcode').each(function(index){
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

				$.post("<?=BASE_URL?>financials/disbursement/ajax/delete_row",data)
				.done(function(data1) 
				{
					if(data1.msg == "success")
					{
						$.post("<?=BASE_URL?>financials/disbursement/ajax/delete_row",data2)
						.done(function(data2) 
						{

							if(data2.msg == "success")
							{
								window.location.href = '<?=BASE_URL?>financials/disbursement';
							}
						});
					}
				});
			}
			else
			{
				window.location.href	= "<?=BASE_URL?>financials/disbursement";
			}
		}

		/**TOGGLE CHECK DATE FIELD**/
		function toggleCheckInfo(val){
			var selected_rows = $("#selected_rows").html();

			if(val == 'cheque'){
				if(selected_rows != '[]'){
					$("#payableForm #cheque_details").removeClass('hidden');
					clear_acct_input();
				}else{

					var list 	= (vendor != '') ? "<ul><li>Total Payment</li></ul>" : "<ul><li>Vendor</li><li>Total Payment</li></ul>";
					var msg 	= "The following fields are required to process a '<strong>Check</strong>' payment."+list;
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
				storedescriptionstoarray();
				recomputechequeamts();
				acctdetailamtreset();
				addAmounts();
				clear_acct_input();
				$("#payableForm #cheque_details").addClass('hidden');

				var curr_acctg_rows 	=	$('#entriesTable #ap_items>tr').length;

				if(curr_acctg_rows < min_row){
					$('.add-entry').click();
				}

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

		function clearChequePayment(){
			checker 	= 	[];
			$('#tbody_cheque .clone').each(function(index) {
				accounts = accounts.splice(1,1);
				if (index > 0) {
					$(this).remove();
				}
			});

			setChequeZero();
		}

		function clear_acct_input(){
			$('.budgetcode').val('').change();
			$('.accountcode').val('').change();
			$('.description').val('');
			$('.debit').val('0.00');
			$('.credit').val('0.00');
			addAmountAll('debit');
			addAmountAll('credit');
		}

		function confirmChequePrint(row){
			var paymentvoucher 	= $('#receiptForm #paymentnumber\\[1\\]').val();
			var chequeno 		= $('#receiptForm #chequenumber\\['+row+'\\]').val();

			bootbox.dialog({
				message: "Please select one of the option to proceed.",
				title: "Print Check",
				buttons: {
					check: {
						label: "Check Only",
						className: "btn-primary btn-flat",
						callback: function(result) {
							var link 	 		= '<?= BASE_URL ?>financials/disbursement/generateCheck/'+paymentvoucher+'/'+chequeno;
							// 'popups/generateCheck.php?sid='+paymentvoucher+'&cn='+chequeno;
							window.open(link);
						}
					},
					voucher: {
						label: "Check with Voucher",
						className: "btn-success btn-flat",
						callback: function(result) {
							var link 	 		= '<?= BASE_URL ?>financials/disbursement/generateCheckVoucher/'+paymentvoucher+'/'+chequeno+'/rv';
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
			ajax_call 		= $.post("<?= BASE_URL ?>financials/disbursement/ajax/load_payables", ajax )
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

		function showIssuePayment(){
			var valid		= 0;
			var	vendor_code	= $('#payableForm #vendor').val();
			$('#payableForm #vendor').trigger('blur');
			var h_voucher_no = $("#payableForm #h_voucher_no").val();

			valid			+= validateField('payableForm','vendor', "vendor_help");

			if(valid == 0 && vendor_code != "")
			{
				showList();
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

			$('#payableForm #disp_tot_cheque').html(total_payment);
			$('#payableForm #disp_tot_payment').html(total_cheque);

			total_payment    	= total_payment.replace(/\,/g,'');
			total_cheque    	= total_cheque.replace(/\,/g,'');

			console.log("Total Payment = "+total_payment);
			console.log("Total Check = "+total_cheque);

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

		function validateHeaderDetails(){
			var paymentvendor		= $("#payableForm #vendor").val();
			var paymentdate			= document.getElementById('document_date').value;
			var paymentmode			= document.getElementById('paymentmode').value;
			var paymentreference	= document.getElementById('paymentreference').value;
			var voucherno 			= $("#voucherno").val();

			var valid				= 0;

			valid	+= validateField('payableForm','vendor', "vendor_help");
			valid	+= validateField('payableForm','document_date', "document_date_help");
			// valid	+= validateField('payableForm','paymentmode', "paymentmode_help");

			return valid;
		}

		function getPayments(voucherno){
			$.post("<?=BASE_URL?>financials/disbursement/ajax/get_payments", "voucherno=" + voucherno)
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

		function loadCheques(i){
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
					var not_cancelled			= arr_from_json[x]['not_cancelled'];

					// var chequeconvertedamount	= arr_from_json[x]['chequeconvertedamount'];

					$('#payableForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

					$('#payableForm #chequenumber\\['+row+'\\]').val(chequenumber);
					$('#payableForm #chequedate\\['+row+'\\]').val(chequedate);
					$('#payableForm #chequeamount\\['+row+'\\]').val(chequeamount);
					$('#payableForm #not_cancelled\\['+row+'\\]').val(not_cancelled);
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

				$.post("<?=BASE_URL?>financials/disbursement/ajax/check", "chequevalue=" + value)
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

			function finalize_saving(valid, button_name){
				if(valid == 0){
					$("#payableForm #btnSave").addClass('disabled');
					$("#payableForm #btnSave_toggle").addClass('disabled');
					$('.cancelled').prop("disabled",false)
					$("#payableForm #btnSave").html('Saving...');

					$("#payableForm #h_save").val(button_name);

					$.post("<?=BASE_URL?>financials/disbursement/ajax/apply_payments",$("#payableForm").serialize())
					.done(function(data)
					{
						if(data.code == 1) {
							if(data.warning != '') {
								$('#warning-modal').modal('show');
								$('#errors').html(data.warning);
								$('#errors').append('<br><i>Notify Department Head<i/>');
								$('#warning-modal').on('hidden.bs.modal', function() {
									$('#delay_modal').modal('show');
									setTimeout(function() {					
										$("#payableForm #h_voucher_no").val(data.voucher);
										$("#payableForm").submit();									
									}, 1000);
								});
							} else if(data.error != '') {
								$('#accountchecker-modal').modal('show');
								$('#accounterror').html(data.error);
								$('#accounterror').append('<br><i>Notify Department Head<i/>');
							} else if(data.date_checker != ''){
								$('#accountchecker-modal').modal('show');
								$('#accounterror').html(data.date_checker);
							} else {
								$('#delay_modal').modal('show');
								setTimeout(function() {					
									$("#payableForm #h_voucher_no").val(data.voucher);
									$("#payableForm").submit();									
								}, 1000)	
							}
						} else {
							next = $('#payableForm').find(".has-error").first();
							$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
						}
					});
				} else {
					next = $('#payableForm').find(".has-error").first();
					$('html,body').animate({ scrollTop: (next.offset().top - 100) }, 'slow');
				}
			}

			function computefortotalaccounts(){
				var count 	=	0;
				$('#entriesTable tbody tr select.accountcode').each(function() {
					var accountcode = $(this).val();
					if(accountcode != "" && accountcode != undefined){
						count++;
					} 
				});
				return count;
			}

			$(document).ready(function() {
				/**ADD NEW BANK ROW**/
				$('body').on('click', '.add-cheque', function() {

					$('#chequeTable tbody tr.clone select').select2('destroy');

					var clone1 = $("#chequeTable tbody tr.clone:first").clone(true);

					clone1.find('input, select, button').prop('disabled', false);
					clone1.find('input, select, button').removeClass('cancelled');
					clone1.find('input, select, button').find('.glyphicon-ban-circle').replaceWith("<span class='glyphicon glyphicon-trash'></span>")


					var ParentRow = $("#chequeTable tbody tr.clone").last();

					// $("#chequeTable tbody tr.clone:first").removeClass('');


					clone1.clone(true).insertAfter(ParentRow);

					setChequeZero();



					$('#chequeTable tbody tr.clone select').select2({width: "100%"});
					$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({ format: 'M dd, yyyy', autoclose: true });

					// Trigger add new line .add-data
					// $(".add-data").trigger("click");
					setZero();
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
						window.location.href	= "<?=BASE_URL?>financials/disbursement";
					}
				});

				/**DELETE RECEIVED PAYMENT : START**/
				$('#deletePaymentModal #btnYes').click(function()  {
					var invoice		= $("#invoiceno\\[1\\]").val();
					var table 		= document.getElementById('paymentsTable');

					var id 	= $('#deletePaymentModal').data('id');
					var row = $('#deletePaymentModal').data('row');

					$.post("<?= BASE_URL?>financials/disbursement/ajax/delete_payments", "voucher=" + id)
					.done(function( data ) 
					{	
						if(data.msg == "success")
						{
							table.deleteRow(row);
							$('#deletePaymentModal').modal('hide');
							location.reload();
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
					var rowlength = $("#chequeTable tbody tr").length;

					var account 	= $('#chequeaccount\\['+row+'\\]').val();
					var acctamt 	= $('#chequeamount\\['+row+'\\]').val();

					if($('#chequeaccount\\['+row+'\\]').val() != '') {
						if(rowCount > 1) {
							$('#chequeaccount\\['+row+'\\]').closest('tr').find('.glyphicon-trash').replaceWith("<span class='glyphicon glyphicon-ban-circle disabled'></span>")
							$('#chequeaccount\\['+row+'\\]').closest('tr').find('.not_cancelled').val('yes');
							$('#chequeaccount\\['+row+'\\]').closest('tr').find('.delete').prop('disabled',true);
							checker['acc-'+account] 	-=	acctamt;	
							storedescriptionstoarray();
							recomputechequeamts();
							acctdetailamtreset();
							resetChequeIds();
							addAmounts();
							addAmountAll('debit');
							addAmountAll('credit');

							if (rowlength == row){
								table.deleteRow(row);
							}


						} else {	
							document.getElementById('chequeaccount['+row+']').value 	= '';

							$('#chequeaccount\\['+row+'\\]').trigger("change.select2");

							document.getElementById('chequenumber['+row+']').value 		= '';
							document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';//today();
							document.getElementById('chequeamount['+row+']').value 		= '0.00';
							document.getElementById('not_cancelled['+row+']').value 		= '';


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
							// table.deleteRow(row);
							$('#chequeaccount\\['+row+'\\]').closest('tr').find('.glyphicon-trash').replaceWith("<span class='glyphicon glyphicon-ban-circle'></span>")
							$('#chequeaccount\\['+row+'\\]').closest('tr').find('.not_cancelled').val('yes');
							checker['acc-'+account] 	-=	acctamt;	
							storedescriptionstoarray();
							recomputechequeamts();
							acctdetailamtreset();
							resetChequeIds();
							addAmounts();
							addAmountAll('debit');
							addAmountAll('credit');

							if (rowlength == row){
								table.deleteRow(row);
							}

						} else {
							document.getElementById('chequeaccount['+row+']').value 	= '';

							$('#chequeaccount\\['+row+'\\]').trigger("change.select2");

							document.getElementById('chequenumber['+row+']').value 		= '';
							document.getElementById('chequedate['+row+']').value 		= '<?= $transactiondate ?>';//today();
							document.getElementById('chequeamount['+row+']').value 		= '0.00';
							document.getElementById('not_cancelled['+row+']').value 		= '';

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
					storechequetobank();
					$('#deleteChequeModal').modal('hide');
				});
				/**DELETE RECEIVED PAYMENT : START**/
				$('#deletePaymentModal #btnYes').click(function()  {
					var invoice		= $("#invoiceno\\[1\\]").val();
					var table 		= document.getElementById('paymentsTable');

					var id 	= $('#deletePaymentModal').data('id');
					var row = $('#deletePaymentModal').data('row');

					$.post("<?= BASE_URL?>financials/disbursement/ajax/delete_payments", "voucher=" + id)
					.done(function( data ) 
					{	
						if(data.msg == "success")
						{
							table.deleteRow(row);
							$('#deletePaymentModal').modal('hide');
							location.reload();
						}
					});
				});

				$('#cancelModal #btnYes').click(function() {
					var record = document.getElementById('h_voucher_no').value;
					cancelTransaction(record);
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

								$.post("<?=BASE_URL?>financials/disbursement/ajax/apply_payments",$("#payableForm").serialize())
								.done(function(data)
								{	
									if(data.code == '1')
									{
										$("#payableForm #h_voucher_no").val(data.voucher);
										// window.location.href = '<?=BASE_URL?>financials/payment_voucher';
									}
								});
							});
						}
					});
				} else if( task == "edit") {
					var paymentmode = $("#paymentmode").val();

					if(paymentmode == "cheque"){
						addAmounts();
					}
				}

				$("#paymentmode").removeAttr("disabled");

				$("#payableForm #save").click(function(){
					var valid		= 0;
					var button_name = $(this).attr('name');
					var paymentmode = $('#paymentmode').val();

					var form_element = $(this).closest('form');
					form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');

					valid		+= validateDetails();

					if(paymentmode == 'cheque'){
						valid 	+= validateCheques();
					}

					finalize_saving(valid, button_name);
				});

				$("#payableForm #save_new").click(function(){
					var valid		= 0;
					var button_name = $(this).attr('name');
					var paymentmode = $('#paymentmode').val();
					// console.log('new = button_name ' + button_name );

					var form_element = $(this).closest('form');
					form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');

					valid		+= validateDetails();

					if(paymentmode == 'cheque'){
						valid 	+= validateCheques();
					}

					finalize_saving(valid, button_name);
				}); 

				$("#payableForm #save_preview").click(function(){
					var valid		= 0;
					var button_name = $(this).attr('name');
					var paymentmode = $('#paymentmode').val();

					var form_element = $(this).closest('form');
					form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');

					valid		+= validateDetails();

					if(paymentmode == 'cheque'){
						valid 	+= validateCheques();
					}

					finalize_saving(valid, button_name);
				});

				$("#payableForm #save_exit").click(function(){
					var valid		= 0;
					var button_name = $(this).attr('name');
					var paymentmode = $('#paymentmode').val();

					var form_element = $(this).closest('form');
					form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');

					valid		+= validateDetails();

					if(paymentmode == 'cheque'){
						valid 	+= validateCheques();
					}

					finalize_saving(valid, button_name);
				});

				// Isabelle -  eto ung pag clone ng td sa may accounting details 
				$('body').on('click', '.add-entry', function()  {	
					var ParentRow = $("#entriesTable tbody tr:not(.added_row)").last();
					ParentRow.after(clone_acct);
					setZero();
					$("#entriesTable tbody tr:not(.added_row):last").find('.accountcode').val('').trigger('change');
					$("#entriesTable tbody tr:not(.added_row):last").find('.budgetcode').val('').trigger('change');
					$("#entriesTable tbody tr:not(.added_row):last").find('.credit').attr('readonly',false);
					drawTemplate();
				});

				var cheque_detail 	=	$('#paymentmode').val();

				$('#change_vendor_modal').on('click','#yes_to_reset',function(){
					var vendor = $('#new_vendor').val();
					$('#vendor').val(vendor).trigger('change');

					$('#ap_items .clone').each(function(index) {
						if (index > 0) {
							$(this).remove();
						}
					});

					clearChequePayment();

					$('#change_vendor_modal').modal('hide');

					clearPayment();
				});

				$('#change_vendor_modal').on('click','#no_to_reset',function(){

					$('#change_vendor_modal').modal('hide');
				});


				// $('#vendor').on('change',function(){
					// 	if ($('.accountcode').val()	 != '' || $('.chequeaccount').val()	 != '' ) {
						// 		$('#change_vendor_modal').modal('show');
						// 	} 
						// });

						$('#vendor').on('select2:selecting', function(e){
							var accounts_selected 	= computefortotalaccounts();
							if(accounts_selected > 0){
								e.preventDefault();
								$('#change_vendor_modal').modal('show');
								$(this).select2('close');
							}
							var new_vendor = e.params.args.data.id;
							$('#new_vendor').val(new_vendor);
						});	

						$('#entriesTable').on('change','.accountcode',function(){
							var vendor 	= $('#vendor').val();
					// var payable = JSON.stringify(container);
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
						} 
					}
				});

						$('body').on('click' , '.print_check', function(){
							var cno 	= $(this).closest('tr').find('.chequeno').val();
							var vno  	= $('#h_voucher_no').val();
							window.open('<?=MODULE_URL?>print_check/' + vno +  '/'+ cno , '_blank');
						})

						$('.cancelled').focus(function() {
							$(this).trigger('blur');
						});

						$(function() {
					// $('select.cancelled').select2("enable",false);
					$('.cancelled').prop("disabled",true);
					$('select.cancelled').prop("disabled",true);
					$('select.cancelled').attr("style", "pointer-events: none;");
					$('.cancelled.datepicker-input').removeClass('datepicker-input').datepicker('remove');
				});

						$('#chequeTable .chequeaccount').on('select2:open',function(){
							var bank_count = $(this).find('option').length - 1;
							if (bank_count == 0){
								$('#noBankModal').modal('show');
								$(this).select2('close');
							}
						})

				// Open Modal
				$('#vendor_button').click(function(){
					$('#vendor_modal').modal('show');
				});

				// $('.cancelled').focus(function() {
					// 	$(this).attr('readonly', true);
					// });

				// $('.cancelled').bind('click dblclick focus').function(event){
					// 	if ($(this).hasClass('cancelled')) event.preventDefault();
					// });


}); // end

</script>