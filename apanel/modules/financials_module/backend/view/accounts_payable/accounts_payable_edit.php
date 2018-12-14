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
	<div id = "diverror" class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><i class="icon fa fa-warning"></i> The system has encountered the following error/s!</h4>
		<div id = "errmsg">
			<ul class = "text-bold">

			</ul>
		</div>
	</div>

	<div class="box box-primary">
		<div class="box-body">
			<form method = "post" class="form-horizontal" id = "payableForm">
				<div class = "clearfix">
					<div class = "col-md-12">&nbsp;</div>
					<div class = "row">
						<div class="col-md-11">
							<div class = "row">
								<?php if($ajax_task == 'ajax_view') : ?>
									<font size = "5"><?php echo $stat ?></font>
								<?php endif ?>
								<?php if($ajax_task == 'ajax_edit') : ?>
									<div class = "col-md-6" hidden>
										<?php
										echo $ui->formField('text')
										->setLabel('Voucher ID')
										->setSplit('col-md-4', 'col-md-8')
										->setName('voucher')
										->setId('voucher')
										->setAttribute(array("readonly" => "readonly"))
										->setPlaceholder("- auto generate -")
										->setValue($voucherno)
										->draw($show_input);
										?>
									</div>
								<?php endif; ?>
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
								</div>

								<div class = "col-md-6">
									<?php
									echo $ui->formField('text')
									->setLabel('Transaction Date')
									->setSplit('col-md-4', 'col-md-8')
									->setName('document_date')
									->setId('document_date')
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
								<div class = "col-md-6 vendor_div ">
									<?php
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
									$val = ($ajax_task != 'ajax_create') ? $tinno : '';
									echo $ui->formField('text')
									->setLabel('<i>Tin</i>')
									->setSplit('col-md-4', 'col-md-8')
									->setName('vendor_tin')
									->setId('vendor_tin')
									->setAttribute(array("readonly" => "","maxlength" => "15", "rows" => "1"))
									->setPlaceholder("000-000-000-000")
									->setClass("input_label")
									->setValue($val)
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
									->setAttribute(array("maxlength" => "20"))
									->setValue($invoiceno)
									->setValidation('alpha_num')
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
									->setName('vendor_terms')
									->setId('vendor_terms')
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
									->setAttribute(array("maxlength" => "20"))
									->setValue($referenceno)
									->setValidation('alpha_num')
									->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 remove-margin">
									<?php
									$val = ($ajax_task != 'ajax_create') ? $address1 : '';
									echo $ui->formField('textarea')
									->setLabel('<i>Address</i>')
									->setSplit('col-md-4', 'col-md-8')
									->setName('vendor_address')
									->setId('vendor_address')
									->setClass("input_label")
									->setValue($val)
									->setAttribute(array("readonly" => "", "rows" => "1"))
									->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class = "col-md-6">
									<?php
									echo $ui->formField('dropdown')
									->setPlaceholder('Select one')
									->setLabel('Asset Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('assetid')
									->setId('assetid')
									->setAttribute(array("maxlength" => "20"))
									->setValue($assetid)
									->setList($asset_list)
									->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<?php $tags = explode(',', $job_no); ?>
										<label class="control-label col-md-4">Job Items </label>
										<div class="col-md-8">
											<?php if($ajax_task != 'ajax_view') {?>
												<input type="hidden" name="jobs_tagged" id = "jobs_tagged" value = "<?php echo $job_no; ?>">
												<button type="button" id="job" class="btn btn-block btn-success btn-flat" <?php echo $val ?>>
													<em class="pull-left"><small>Click to tag job items</small></em>
													<strong id="job" class="pull-right"><?php echo count($tags); ?></strong>
												</button>
											<?php } else { ?>
												<span><?php echo $job_no; ?></span>
											<?php } ?>
										</div>
									</div>
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
									->setValue($proformacode)
									->setNone('None')
									->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<?php
									echo $ui->formField('dropdown')
									->setLabel('Currency')
									->setSplit('col-md-4', 'col-md-8')
									->setName('currencycode')
									->setId('currencycode')
									->setDefault('PHP')
									->setValue($currency)
									->setList($currencycodes)
									->draw($show_input);
									?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6"></div>
								<div class="col-md-6">
									<?php
									echo $ui->formField('text')
									->setLabel('Exchange Rate')
									->setPlaceholder('0.00')
									->setSplit('col-md-4', 'col-md-8')
									->setName('exchangerate')
									->setId('exchangerate')
									->setValue($exchangerate)
									->setClass('text-right')
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
									->setName('particulars')
									->setId('particulars')
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
						<div id="details_error" class="col-md-12">
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
							<table class="table table-hover table-condensed " id="itemsTable">
								<thead>
									<tr class="info">
										<th class="col-md-1 text-center">Withholding Tax</th>
										<th class="col-md-2 text-center">Account</th>
										<th class="col-md-3 text-center">Description</th>
										<th class="col-md-2 text-center" colspan = "2">Debit</th>
										<th class="col-md-2 text-center" colspan = "2">Credit</th>
										<th class="col-md-3 text-center">Currency Amount</th>
										<?if($ajax_task != 'view'){?>
											<th class="col-md-1 center"></th>
											<?}?>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($details as $key => $row) : ?>
											<tr class="clone" valign="middle">
												<?php if($ajax_task == 'ajax_view') { ?>
													<td class = "checkbox-select remove-margin text-center">
														<div class="hidden">
															<?php
															echo $ui->formField('checkbox')
															->setSplit('', 'col-md-12')
															->setId("wtax[]")
															->setClass("wtax")
															->setDefault("")
															->setValue(1)
															->setAttribute(array("disabled" => "disabled"))
															->draw($show_input);
															?>
														</div>
													</td>
												<?php } else { ?>
													<td class = "checkbox-select remove-margin text-center">
														<?php
														echo $ui->formField('checkbox')
														->setSplit('', 'col-md-12')
														->setId("wtax[]")
														->setClass("wtax")
														->setDefault("")
														->setValue(1)
														->setAttribute(array("disabled" => "disabled"))
														->draw($show_input);
														?>
													</td>
												<?php } ?>
												<td class="edit-button text-center " style="display: none">
													<button type="button" class="btn btn-primary btn-flat btn-xs"><i class="glyphicon glyphicon-pencil"></i></button>
												</td>
												<td class = "remove-margin hidden" >
													<?php
													echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName("taxcode[]")
													->setId("taxcode")
													->setClass('taxcode')
													->setValue("")
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin hidden" >
													<?php
													echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName("taxbase_amount[]")
													->setId("taxbase_amount")
													->setClass('taxbase_amount')
													->setValue("")
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin">
													<?php
													echo $ui->formField('dropdown')
													->setPlaceholder('Select One')
													->setSplit('', 'col-md-12')
													->setName("accountcode[]")
													->setId("accountcode")
													->setClass('accountcode')
													->setList($account_list)
													->setValue($row->accountcode)
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin">
													<?php
													echo $ui->formField('text')
													->setSplit('', 'col-md-12')
													->setName('detailparticulars[]')
													->setId('detailparticulars')
													->setAttribute(array("maxlength" => "100"))
													->setClass('detailparticulars')
													->setValue($row->description)
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin" colspan = "2">
													<?php
													echo $ui->formField('text')
													->setPlaceholder('00.00')
													->setSplit('col-md-2', 'col-md-10')
													->setLabel('<span class="label label-default currency_symbol">PHP</span>')
													->setName('debit[]')
													->setId('debit')
													->setAttribute(array("maxlength" => "20"))
													->setClass("debit text-right")
													->setValidation('decimal')
													->setValue(number_format($row->debit,2))
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin" colspan = "2">
													<?php
													echo $ui->formField('text')
													->setPlaceholder('00.00')
													->setSplit('col-md-2', 'col-md-10')
													->setLabel('<span class="label label-default currency_symbol">PHP</span>')
													->setName('credit[]')
													->setId('credit')
													->setAttribute(array("maxlength" => "20"))
													->setClass("credit text-right")
													->setValidation('decimal')
													->setValue(number_format($row->credit,2))
													->draw($show_input);
													?>
												</td>
												<td class = "remove-margin">
													<?php
													echo $ui->formField('text')
													->setPlaceholder('00.00')
													->setSplit('', 'col-md-12')
													->setName('currencyamount[]')
													->setId('currencyamount')
													->setAttribute(array("maxlength" => "20", 'readonly'))
													->setClass("currencyamount text-right")
													->setValidation('decimal')
													->setValue($row->currencyamount)
													->draw($show_input);
													?>
												</td>
												<input type="hidden" name="linenum[]" value = "<?php echo $row->linenum; ?>" class = "linenum">
												<?php $val = ($ajax_task) == 'ajax_view' ? 'hidden' : ''; ?>
												<td class="text-center" <?php echo $val; ?>>
													<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="1" name="chk[]" style="outline:none;"><span class="glyphicon glyphicon-trash"></span></button>
												</td>			
											</tr>
										<?php endforeach; ?>
									</tbody>
									<tfoot>
										<tr>
											<td>
												<? if($ajax_task != 'ajax_view') { ?>
													<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
												<? } ?>
											</td>	
										</tr>	
										<tr id="total">
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<label class="control-label">Total</label>
											</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('total_debit')
												->setId('total_debit')
												->setClass("input_label text-right")
												->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
												->draw($show_input);
												?>
											</td>
											<td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('total_credit')
												->setId('total_credit')
												->setClass("input_label text-right")
												->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
												->draw($show_input);
												?>
											</td>
											<td class="right" style="border-top:1px solid #DDDDDD;">
												<?php
												echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('total_currency')
												->setId('total_currency')
												->setClass("input_label text-right")
												->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
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
							<?php if($ajax_task != 'ajax_view') { ?>
								<div class="col-md-12 col-sm-12 text-center">
									<input class = "form_iput" value = "" name = "save" id = "save" type = "hidden">
									<div class="btn-group" id="save_group">

										<input type = "submit" value = "Save & Preview" name = "save" id = "save_preview" class="btn btn-primary btn-sm btn-flat"/>
										<input type = "hidden" value = "" name = "button_trigger" id = "button_trigger"/>

										<button type="button" id="btnSave_toggle" class="btn btn-primary dropdown-toggle btn-sm btn-flat" data-toggle="dropdown">
											<span class="caret"></span>
										</button>

										<ul class="dropdown-menu left" role="menu">
											<li id = "save_new" style="cursor:pointer;">
												Save &amp; New
											</li>
											<li class="divider"></li>
											<li id = "save_exit" style="cursor:pointer;">
												Save &amp; Exit
											</li>
										</ul>
									</div>
									&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" id = "btnCancel">Cancel</button>
									</div>
								</div>
							<?php  } else if($ajax_task == 'ajax_view') { ?>
								<div class="col-md-12 col-sm-12 text-center">
									<div class="btn-group" id="save_group">
										<?php if($stat != 'cancelled') : ?>
											<a href="<?=MODULE_URL?>edit/<?php echo $voucherno ?>" class = "btn btn-info btn-flat">Edit</a>
										<?php endif; ?>
									</div>
									&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" id = "btnCancel">Cancel</button>
									</div>
								</div>
							<?php  } ?>
						</div>
						<div class = "col-md-12">&nbsp;</div>
					</div>
				</form>
			</div>
		</div>
	</section>

	<!-- Vendor Modal -->
	<div class="modal fade" id="vendorModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					Add a Vendor
				<!--<h4>Add a Vendor
					<button type="button" class="close" data-dismiss="modal">&times;</button></h4>-->
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="newVendor" autocomplete="off">
						<input class = "form_iput" value = "newVendor" name = "h_form" id = "h_form" type="hidden">
						<input class = "form_iput" value = "insert" name = "h_querytype" id="h_querytype" type="hidden">
						<div class="alert alert-warning alert-dismissable hidden" id="vendorAlert">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<p>&nbsp;</p>
						</div>
						<div class = "well well-md">
							<div class="row row-dense remove-margin">
								<?php
								echo $ui->formField('text')
								->setLabel('Vendor Code: <span class="asterisk"> * </span>')
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
								->setLabel('Vendor Name: <span class="asterisk"> * </span>')
								->setSplit('col-md-3', 'col-md-8 field_col')
								->setName('vendor_name')
								->setId('vendor_name')
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
								->setLabel('Address: ')
								->setSplit('col-md-3', 'col-md-8 field_col')
								->setName('address')
								->setId('address')
								->setAttribute(array("rows" => "1"))
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
								->draw($show_input);
								?>
							</div>
						</div>
						<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-primary btn-flat" id="vendorBtnSave">Save</button>
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
	<!-- End Vendor Modal -->

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
								->setList($account_list)
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
					Are you sure you want to cancel this transaction?
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 center">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnCancelYes">Yes</button>
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
								->setLabel('Currency Rate:  ')
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
	<div class="modal fade" id="jobModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					Choose Job to tag
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body table-reponsive no-padding">
					<form id = "jobform" class = "form-horizontal" method = "post">
						<table id="jobsTable" class="table table-hover table-sidepad mb-none">
							<?php
							echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('', array('class' => 'col-md-1'))
							->addHeader('Job Number', array('class' => 'col-md-3'))
							->draw();
							?>
							<tbody>
							</tbody>
							<textarea hidden class = "job_append"></textarea>
						</table>
						<div id="paginate"></div>
					</div>
					<div class="modal-footer force-right">
						<input type="submit" class="btn btn-primary btn-flat" id = "confirmJob" value = "Tag">
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
					</form>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="atcModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					Choose ATC Code
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="newVendor" autocomplete="off">
						<div class = "row">
							<div class = "col-md-10">
								<?php
								echo $ui->formField('dropdown')
								->setLabel('ATC Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('tax_account')
								->setId('tax_account')
								->setClass('tax_account')
								->draw($show_input);
								?>
							</div>

							<div class = "col-md-10">
								<?php
								echo $ui->formField('text')
								->setLabel('Tax Base Amount')
								->setPlaceholder('00.00')
								->setSplit('col-md-4', 'col-md-8')
								->setName('tax_amount')
								->setId('tax_amount')
								->setClass('text-right tax_amount')
								->setValidation('required')
								->draw($show_input);
								?>
							</div>
						</div>
						<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-primary btn-flat" id="tax_apply">Apply</button>
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

	<div class="modal fade" id="error-modal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					Warning
				</div>
				<div class="modal-body">
					<div class = "row">
						<div class="col-md-12">
							<h4 class = "checkers">You need to have at least one Accounts Payable accounts</h4>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 col-sm-12 col-xs-12 text-right">
							<div class="btn-group">
								<button type="button" class="btn btn-info btn-flat" data-dismiss="modal">OK</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	$(document).ready(function() {
		if(removeComma($('.credit').val()) == 0) {
			$('.credit').attr('readonly', 'readonly');
		} else {
			$('.debit').attr('readonly', 'readonly');
		}
	});

		$('#btnCancel').click(function() 
		{
			$('#cancelModal').modal('show');
		});

		$('#btnCancelYes').on('click', function() {
			window.location = '<?= MODULE_URL ?>';
		});

		var job = [];
		$('#job').on('click', function() {
			$.post('<?=MODULE_URL?>ajax/ajax_list_jobs', '&jobs_tagged=' + $('#jobs_tagged').val(), function(data) {
				if(data) {
					$('#jobModal').modal('show');
					$('#jobsTable tbody').html(data.table);
					$('#paginate').html(data.pagination);
				}
			});
		});

		$('#jobModal').on('shown.bs.modal', function() {
			$('#jobsTable tbody tr td input[type="checkbox"]').each(function() {
				if(jQuery.inArray($(this).val(), job) != -1) {
					$(this).closest('tr').iCheck('check');
				}
			});
		});

		$('#assetid').on('change', function() {
			var asset = $(this).val();
			$('#job').attr('disabled', 'disabled');
			$.post('<?=MODULE_URL?>ajax/ajax_get_asset_details', '&asset=' + asset, function(data) {
				if(data) {
					$('#itemsTable tbody tr.clone select:first').val(data).trigger('change').select2({width: "100%"});
				}
			});
		});

		<?php if($ajax_post != 'create') : ?>
			$(document).ready(function() {
				sumDebit();
				sumCredit();
				sumCurrencyAmount();
			});
		<?php endif; ?>

		function consoler($console) {
			console.log($console);
		}

		var debit_currency = 0;
		var credit_currency = 0;
		$('#itemsTable').on('blur', '.debit', function() {
			var rate = removeComma($('#exchangerate').val());
			var debit = removeComma($(this).val());
			if(debit != '0') {
				debit_currency = $(this).val() * rate;
				$(this).closest('tr').find('.currencyamount').val(addComma(debit_currency));
				$(this).closest('tr').find('.credit').attr('readonly', 'readonly');
				$(this).closest('tr').find('.credit').attr('data-validation', 'decimal');
				$(this).closest('tr').find('.asterisk').html('');
				sumDebit();
				sumCurrencyAmount();
			} else {
				$(this).closest('tr').find('.credit').removeAttr('readonly');
			}
		});

		$('#itemsTable').on('blur', '.credit', function() {
			var rate = removeComma($('#exchangerate').val());
			var credit = removeComma($(this).val());
			if(credit != '0') {
				credit_currency = $(this).val() * rate;
				$(this).closest('tr').find('.currencyamount').val(addComma(credit_currency));
				$(this).closest('tr').find('.debit').attr('readonly', 'readonly');
				$(this).closest('tr').find('.debit').attr('data-validation', 'decimal');
				$(this).closest('tr').find('.asterisk').html('');
				sumCredit();
				sumCurrencyAmount();
			} else {
				$(this).closest('tr').find('.debit').removeAttr('readonly');
			}
		});

		$('#confirmJob').on('click',function(e) {
			e.preventDefault();
			job = [];
			$('#jobsTable tbody tr td input[type="checkbox"]:checked').each(function() {
				var get = $(this).val();
				job.push(get);
				$('#assetid').attr('disabled', 'disabled');
				$('#jobModal').modal('hide');
			});
		});

		$('#vendor').on('change', function() {
			var vendor = $(this).val();
			$.post('<?=MODULE_URL?>ajax/ajax_get_details', '&vendor=' + vendor, function(data) {
				if(data) {
					$('#vendor_tin').val(data.tinno);
					$('#vendor_terms').val(data.terms);
					$('#vendor_address').val(data.address1);
				}
			});
		});

		function sumDebit() {
			var total_debit = 0;
			var debit = 0;
			var curr_val = 0;
			$('.debit').each(function() {
				debit = removeComma($(this).val());
				total_debit += +debit;
				$('#total_debit').val(addComma(total_debit));
			});
		}

		function sumCredit() {
			var total_credit = 0;
			var credit = 0;
			var curr_val = 0;
			$('.credit').each(function() {
				credit = removeComma($(this).val());
				total_credit += +credit;
				$('#total_credit').val(addComma(total_credit));
			});
		}

		function sumCurrencyAmount() {
			var total_currency = 0;
			var currency = 0;
			$('.currencyamount').each(function() {
				currency = removeComma($(this).val());
				total_currency += +currency;
				$('#total_currency').val(addComma(total_currency));
			});
		}

		$('#currencycode').on('change', function() {
			var currencycode = $(this).val();
			$('#itemsTable tbody tr td .form-group').find('.currency_symbol').html(currencycode);
			$.post('<?=MODULE_URL?>ajax/ajax_get_currency_val', { currencycode : currencycode }, function(data) {
				if(data) {
					$('#exchangerate').val(data.exchangerate);	
					if($('.debit').val() != '0.00') {
						$('.currencyamount').val(addComma(data.exchangerate * $('.debit').val()));
					} else {
						$('.currencyamount').val(addComma(data.exchangerate * $('.credit').val()));
					}
					sumDebit();
					sumCredit();
					sumCurrencyAmount();
				}
			});
		});

		var row = '';
		$('.accountcode').on('change', function() {
			var accountcode = $(this).val();
			row = $(this).closest('tr');
			$.post('<?=MODULE_URL?>ajax/ajax_check_cwt', '&accountcode=' + accountcode, function(data) {
				if(data.checker == 'true') {
					$('#atcModal').modal('show');
					$('#tax_account').html(data.ret);
					$('#tax_amount').val('');
				} else {
					$(this).closest('tr').find('.checkbox-select').show();
					$(this).closest('tr').find('.edit-button').hide();
				}
			});
		});

		var creditamt = 0;
		var taxaccount = 0;
		var taxamount = 0;
		$('#tax_apply').on('click', function() {
			taxaccount = $('#tax_account').val();
			taxamount = $('#tax_amount').val();

			$.post('<?=MODULE_URL?>ajax/ajax_get_taxrate', {taxaccount : taxaccount, taxamount : taxamount } ,function(data) {
				if(data) {
					creditamt = taxamount * data.tax_rate;
					row.find('.taxcode').val(taxaccount);
					row.find('.taxbase_amount').val(taxamount);
					row.find('.edit-button').show().attr('data-amount', taxamount);
					row.find('.edit-button').attr('data-account', taxaccount);
					row.find('.credit').val(addComma(Math.round(creditamt)));
					row.find('.currencyamount').val(addComma(Math.round($('#exchangerate').val() * creditamt)));
					row.find('.credit').attr('readonly', 'readonly');
					row.find('.checkbox-select').hide();
					$('#atcModal').modal('hide');
					sumCredit();
					sumCurrencyAmount();
				}
			});
		});

		$('#itemsTable').on('click', '.edit-button', function() {
			$('#atcModal').modal('show');
			$('#tax_amount').val($(this).attr('data-amount'));
			$('#tax_account').val($(this).attr('data-account')).trigger('change');
		});

		$("#itemsTable").on('ifToggled','.wtax',function() {
			$('#tax_amount').val('');
			row = $(this).closest('tr');
		});

		var data_id = 2;
		$('.add-data').on('click', function() {
			$('#itemsTable tbody tr.clone select').select2('destroy');

			var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

			var ParentRow = $("#itemsTable tbody tr.clone").last();

			clone.clone(true).insertAfter(ParentRow);

			$('#itemsTable tbody tr.clone select').select2({width: "100%"});
			$('#itemsTable tbody tr.clone #detailparticulars').last().val('');
			$('#itemsTable tbody tr.clone #debit').last().val('');
			$('#itemsTable tbody tr.clone #credit').last().val('');
			$('#itemsTable tbody tr.clone .edit-button').last().hide();
			$('#itemsTable tbody tr.clone .checkbox-select').last().show();
			$('#itemsTable tbody tr.clone .linenum').last().val(++data_id);
		});

		var deleterow = '';
		$('.confirm-delete').on('click', function() {
			var one = 0;
			$('#itemsTable tbody tr td .confirm-delete').each(function() {
				one++;
			});

			if(one >= 3) {
				$('#deleteItemModal').modal('show');
				deleterow = $(this).closest('tr');
			}
		});

		$('#btnYes').on('click', function() {
			deleterow.remove();
			$('#deleteItemModal').modal('hide');
		});	
		var accountcodes = [];
		var good = true;
		$('#payableForm #save_preview').on('click', function(e) {
			e.preventDefault();

			$('#button_trigger').val('save_preview');
			$('.accountcode :selected').each(function() {
				accountcodes.push($(this).val());
			});

			if($('#total_debit').val() != $('#total_credit').val()) {
				$('#error-modal').modal('show');
				$('.checkers').html('<h4>Total Debit should be equal to total credit. </h4>');
				good = 'false';
			}

			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0) {
				if(good == true) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $('#payableForm').serialize() + '&job=' + job + '&account=' + accountcodes, function(data) {
						if(data.check) {
							if(data.success) {
								$('#delay_modal').modal('show');
								setTimeout(function() {
									window.location = data.redirect;
								},500);
							}
						} else {
							$('#error-modal').modal('show');
						}
					});
				} else {
					$('#error-modal').modal('show');
				}
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
		
		$('#payableForm #save_new').click(function(e) {
			e.preventDefault();
			$('#button_trigger').val('save_new');
			$('.accountcode :selected').each(function() {
				accountcodes.push($(this).val());
			});

			if($('#total_debit').val() != $('#total_credit').val()) {
				$('#error-modal').modal('show');
				$('.checkers').html('<h4>Total Debit should be equal to total credit. </h4>');
				good = false;
			}

			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0) {
				if(good == true) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $('#payableForm').serialize() + '&job=' + job + '&account=' + accountcodes, function(data) {
						if(data.check) {
							if(data.success) {
								$('#delay_modal').modal('show');
								setTimeout(function() {
									window.location = data.redirect;
								},500);
							}
						} else {
							$('#error-modal').modal('show');
						}
					});
				} else {
					$('#error-modal').modal('show');
				}
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});

		$('#payableForm #save_exit').click(function(e) {
			e.preventDefault();
			$('#button_trigger').val('save_exit');
			$('.accountcode :selected').each(function() {
				accountcodes.push($(this).val());
			});

			if($('#total_debit').val() != $('#total_credit').val()) {
				$('#error-modal').modal('show');
				$('.checkers').html('<h4>Total Debit should be equal to total credit. </h4>');
				good = 'false';
			}

			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0) {
				if(good == true) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $('#payableForm').serialize() + '&job=' + job + '&account=' + accountcodes, function(data) {
						if(data.check) {
							if(data.success) {
								$('#delay_modal').modal('show');
								setTimeout(function() {
									window.location = data.redirect;
								},500);
							}
						} else {
							$('#error-modal').modal('show');
						}
					});
				} else {
					$('#error-modal').modal('show');
				}
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
	</script>