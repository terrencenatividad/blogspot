<section class="content">
		<div class="box box-primary">
			<form id="main_form" action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php if ($show_input && $ajax_task != 'ajax_edit'): ?>
										<div class="form-group">
											<label for="voucherno" class="control-label col-md-4">Billing No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Billing No.')
												->setSplit('col-md-4', 'col-md-8')
												->setName('voucherno')
												->setId('voucherno')
												->setValue($voucherno)
												->addHidden($voucherno)
												->setValidation('required')
												->draw(($show_input && $ajax_task != 'ajax_edit'));
										?>
									<?php endif ?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Document Date')
											->setSplit('col-md-4', 'col-md-8')
											->setName('transactiondate')
											->setId('transactiondate')
											->setClass('datepicker-input')
											->setAttribute(array('readonly'))
											->setAddon('calendar')
											->setValue($transactiondate)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('dropdown')
											->setLabel('Customer')
											->setPlaceholder('Select Customer')
											->setSplit('col-md-4', 'col-md-8')
											->setName('customer')
											->setId('customer')
											->setList($customer_list)
											->setValue($customer)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
								<div class = "col-md-6">
									<?php
										echo $ui->formField('dropdown')
											->setLabel('Discount Type ')
											->setPlaceholder('Select Discount Type')
											->setSplit('col-md-4', 'col-md-8')
											->setName('discount_type')
											->setId('discounttype')
											->setList($discounttypes)
											->setValue($discounttype)
											->setNone('none')
											->setDefault('none')
											->draw($show_input);
									?>
									<input type="hidden" value="" id="newdisctype" name="newdisctype">
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Job Order No.')
											->setSplit('col-md-4', 'col-md-8')
											->setPlaceholder('Select Job Order')
											->setName('job_orderno')
											->setId('job_orderno')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($job_orderno)
											->draw($show_input);
									?>
									<input type="hidden" value="<?php echo $job_orderno ?>" id="jo_no" name="jo_no">
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
												->setLabel('Reference')
												->setId('referenceno')
												->setName('referenceno')
												->setSplit('col-md-4', 'col-md-8')
												->setValue($referenceno)
												->setMaxLength(20)
												->setValidation('required')
												->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php
										echo $ui->formField('textarea')
											->setLabel('Notes')
											->setSplit('col-md-2', 'col-md-10')
											->setName('remarks')
											->setId('remarks')
											->setMaxLength(300)
											->setValue($remarks)
											->draw($show_input);
									?>
									<?php
										echo $ui->formField('hidden')
											->setName('sq')
											->setId('sq')
											->setValue('')
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
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-sidepad only-checkbox full-form">
						<thead>
							<tr class="info">
								<th class="col-xs-2">Item</th>
								<th class="col-xs-2">Description</th>
								<th class="col-md-1 text-center">Qty</th>
								<th class="col-md-1 text-center">UOM</th>
								<th class="col-md-1 text-center">Price</th>
								<th class="col-md-1 text-center">Discount</th>
								<th class="col-md-2 text-center">Tax</th>
								<th class="col-md-2 text-center">Amount</th>
								<?php if ($show_input): ?>
								<th style="width: 50px;"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
						<tfoot class="summary text-right" style="display: none">
							<tr>
								<td colspan="<?php echo ($show_input) ? 4 : 4 ?>" class="text-left">
									<?php if ($show_input): ?>
										<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
									<?php endif ?>
								</td>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">VATable Sales</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('vat_sales')
												->setClass('vat_sales')
												->setValue(((empty($vat_sales)) ? '0.00' : number_format($vat_sales, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">VAT-Exempt Sales</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('vat_exempt')
												->setClass('vat_exempt')
												->setValue(((empty($vat_exempt)) ? '0.00' : number_format($vat_exempt, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">VAT Zero Rated Sales</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('vat_zerorated')
												->setClass('vat_zerorated')
												->setValue(((empty($vat_zerorated)) ? '0.00' : number_format($vat_zerorated, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">Total Sales</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('amount')
												->setClass('total_amount')
												->setValue(((empty($amount)) ? '0.00' : number_format($amount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<!-- <tr>
								<td colspan="<?php echo ($show_input) ? 4 : 5 ?>"><label class="control-label">Discount</label></td>
								<td colspan="2">
									<?php if ($show_input): ?>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<div class="col-md-12">
														<div class="input-group">
															<div class="input-group-addon with-checkbox">
																<?php
																	echo $ui->setElement('radio')
																			->setName('discounttype')
																			->setClass('discounttype')
																			->setDefault('perc')
																			->setValue($discounttype)
																			->draw($show_input);
																?>
															</div>
															<?php
																echo $ui->setElement('text')
																		->setId('discountrate')
																		->setName('discountrate')
																		->setClass('discount_entry rate text-right')
																		->setAttribute(array('data-max' => 99.99, 'data-min' => '0.00'))
																		->setValidation('decimal')
																		->setValue(((empty($discountrate)) ? '0.00' : $discountrate))
																		->draw($show_input);
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
																	echo $ui->setElement('radio')
																			->setName('discounttype')
																			->setClass('discounttype')
																			->setDefault('amt')
																			->setValue($discounttype)
																			->draw($show_input);
																?>
															</div>
															<?php
																	echo $ui->setElement('text')
																			->setId('discountamount')
																			->setName('discountamount')
																			->setClass('discount_entry text-right')
																			->setAttribute(array('data-min' => '0.00'))
																			->setValidation('decimal')
																			->setValue(((empty($discountamount)) ? '0.00' : number_format($discountamount, 2)))
																			->draw($show_input);
															?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php else:
											echo $ui->setElement('text')
													->setName('discountamount')
													->setClass('total_discount')
													->setValue(((empty($discountamount)) ? '0.00' : number_format($discountamount, 2)))
													->draw(false);
										endif ?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr> -->
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">Total Purchase Tax</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12')
												->setName('taxamount')
												->setClass('total_tax')
												->setValue(((empty($taxamount)) ? '0.00' : number_format($taxamount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 6 : 6 ?>"></td>
								<td colspan="2">
									<hr style="margin: 0">
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">Total Amount Due</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12')
												->setName('netamount')
												->setClass('total_amount_due')
												->setValue(((empty($netamount)) ? '0.00' : number_format($netamount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
							<tr>
								<td colspan="<?php echo ($show_input) ? 7 : 7 ?>"><label class="control-label">Discount</label></td>
								<td colspan="1">
									<?php
										echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12')
												->setName('total_discount')
												->setClass('total_discount')
												->setValue(((empty($discountamount)) ? '0.00' : number_format($discountamount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
								<?php if ($show_input): ?>
								<td></td>
								<?php endif ?>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="box-body">
					<hr>
					<div class="row">
						<div id="submit_container" class="col-md-12 text-center">
							<?php
								if ($stat == 'Unpaid' || empty($stat)) {
									echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
								}
								echo $ui->drawCancel();
							?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
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
	<div id="exchangerate_expired" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Exchange Rate</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger">
						<p>Exchange Rate <strong>Expired</strong> or not yet in effect.</p>
						<p><strong>Please Update!</strong></p>
					</div>
					<h2 id="exchange_label"></h2>
					<form id="modal_form" action="" method="post">
						<?php
							echo $ui->formField('text')
									->setLabel('Effectivity Date')
									->setClass('fulldaterange modal_effectivedate')
									->setName('effectivedate')
									->setAttribute(array('readonly' => ''))
									->setAddon('calendar')
									->setValue('')
									->setValidation('required')
									->draw();
						?>
						<?php
							echo $ui->formField('text')
									->setLabel('Exchange Rate')
									->setClass('modal_exchangerate text-right')
									->setName('exchangerate')
									->setValue('')
									->setAttribute(array('data-min' => '0.0001'))
									->setValidation('required decimal[4]')
									->draw();
						?>
						<?php
							echo $ui->formField('hidden')
									->setId('currency_code')
									->setName('currency_code')
									->setValue('')
									->draw();
						?>
					</form>
				</div>
				<div class="modal-footer">
					<a href="" id="update_exchangerate_button" class="btn btn-primary">Update Exchange Rate</a>
					<button id="cancel_exchangerate_button" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<div id="jo_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Job Order List</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-8">
							<div class="input-group">
								<input id="jo_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-addon">
									<i class="fa fa-search"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body no-padding">
					<table id="jo_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-4">JO No.</th>
								<th class="col-xs-4">Transaction Date</th>
								<th class="col-xs-4 text-right">Service Quotation</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="4" class="text-center">Loading Items</td>
							</tr>
						</tbody>
					</table>
					<div id="pagination"></div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var delete_row	= {};
		var ajax		= {};
		var ajax_call	= '';
		var min_row		= 1;
		function addVoucherDetails(details, index) {
			var sq = $('#sq').val();
			var details = details || {itemcode: '', detailparticular: '', unitprice: ''};
			details.itemcode = details.itemcode || '';
			details.detailparticular = details.detailparticular || '';
			details.unitprice = details.unitprice || '0.00';
			details.discount = details.discount || '0.00';
			details.issueuom = details.issueuom || '';
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.issueqty;
			delete other_details.unitprice;
			delete other_details.amount;
			delete other_details.taxamount;
			delete other_details.discountamount;
			delete other_details.taxcode;
			delete other_details.taxrate;
			delete other_details.discount;
			delete other_details.issueuom;
			var otherdetails = '';
			for (var key in other_details) {
				if (other_details.hasOwnProperty(key)) {
					otherdetails += `<?php 
						echo $ui->setElement('hidden')
								->setName('` + key + `[]')
								->setValue('` + other_details[key] + `')
								->draw();
					 ?>`;
				}
			}
			var row = `
				<tr>
					<td>
						<?php
							$value = "<span id='temp_view_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Item Code')
								->setSplit('', 'col-md-12')
								->setName('itemcode[]')
								->setList($item_list)
								->setValidation('required')
								->setClass('itemcode')
								->setValue($value)
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('detailparticular')
								->setMaxLength(100)
								->setValue('` + details.detailparticular + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('issueqty[]')
								->setClass('issueqty text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.issueqty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.issueqty, 0) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('issueuom[]')
								->setClass('issueuom text-right')
								->setAttribute(array("maxlength" => "20","readonly" => "readonly"))
								->setValue('` + details.issueuom + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setClass('unitprice text-right')
								->setName('unitprice[]')
								->setSwitch()
								->setValidation('required decimal')
								->setValue('` + addComma(details.unitprice) + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setClass('discount text-right')
								->setName('discount[]')
								->setValidation('decimal')
								->setAttribute(array("maxlength" => "20","readonly"=>true))
								->setValue('` + addComma(details.discountrate) + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							$value = "<span id='temp_view_taxrate_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setPlaceholder('Select TAX')
								->setName('tax_code[]')
								->setClass('taxcode')
								->setList($taxrate_list)
								->setValue($value)
								->setNone('None')
								->draw($show_input);

							echo $ui->setElement('hidden')
								->setName('taxcode[]')
								->setClass('tax_field')	
								->setValue('` + (parseFloat(details.taxcode) || 0) + `')
								->draw();

							echo $ui->setElement('hidden')
									->setName('taxrate[]')
									->setClass('taxrate')	
									->setValue('` + (parseFloat(details.taxrate) || 0) + `')
									->draw();

							echo $ui->setElement('hidden')
									->setName('detail_taxamount[]')
									->setClass('taxamount')	
									->setValue('` + (parseFloat(details.taxamount) || 0) + `')
									->draw();
									
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detail_amount[]')
								->setClass('amount text-right')
								->setAttribute(array('readonly' => ''))
								->setValidation('required decimal')
								->setValue('` + (addComma(details.amount) || 0) + `')
								//->addHidden()
								->draw($show_input);

								echo $ui->setElement('hidden')
								->setName('discount_amount[]')
								->setClass('discount_amount')	
								->setValue('` + (parseFloat(details.discountamount) || 0) + `')
								->draw();

								echo $ui->setElement('hidden')
								->setName('discountedamount[]')
								->setClass('discountedamount')	
								->setValue('` + (parseFloat(details.discountedamount) || 0) + `')
								->draw();
						?>
						` + otherdetails + `
					</td>
					<?php if ($show_input): ?>
					<td>
						<button type="button" class="btn btn-danger delete_row" style="outline:none;">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</td>
					<?php endif ?>
				</tr>
			`;
			$('#tableList tbody').append(row);
			if (details.itemcode != '') {
				$('#tableList tbody').find('tr:last .itemcode').val(details.itemcode);
			}
			if (details.taxcode != '') {
				$('#tableList tbody').find('tr:last .taxcode').val(details.taxcode);
				$('#tableList tbody').find('tr:last .tax_field').val(details.taxcode);
			} else {
				$('#tableList tbody').find('tr:last .taxcode').val('none');
			}
			try {
				drawTemplate();
			} catch(e) {};
			var itemlist = <?= json_encode($item_list) ?>;
			itemlist.forEach(function(item) {
				if (item.ind == details.itemcode) {
					$('#temp_view_' + index).html(item.val);
				}
			});
			var taxrate_list = <?= json_encode($taxrate_list) ?>;
			taxrate_list.forEach(function(tax) {
				if (tax.ind == details.taxcode) {
					$('#temp_view_taxrate_' + index).html(tax.val);
				}
			});	
			var discounttype = $('#discounttype').val();
			if (discounttype == 'amt' || discounttype == 'perc') {
				$('#tableList tbody').find('.discount').removeAttr('readonly');
			} 
			else {
				$('#tableList tbody').find('.discount').attr('readonly', '').val('0.00');
			}
			if (sq == '') {
				$('#tableList tbody').find('.unitprice').removeAttr('readonly');
				$('#tableList tbody').find('.discount').removeAttr('readonly');
				$('#tableList tbody').find('.taxcode').removeAttr('disabled');
			} 
			else {
				$('#tableList tbody').find('.unitprice').attr('readonly', true);
				$('#tableList tbody').find('.discount').attr('readonly', true);
				$('#tableList tbody').find('.taxcode').attr('disabled', true);
			}
			$('#discount_type').val(discounttype);
		}
		var voucher_details = <?php echo $voucher_details ?>;
		function displayDetails(details) {
			$('#tableList tfoot.summary').hide();
			if (details.length < min_row) {
				for (var x = details.length; x < min_row; x++) {
					addVoucherDetails('', x);
				}
			}
			if (details.length > 0) {
				details.forEach(function(details, index) {
					addVoucherDetails(details, index);
				});
				$('#tableList tfoot.summary').show();
			} else if (min_row == 0) {
				$('#tableList tbody').append(`
					<tr>
						<td colspan="9" class="text-center"><b>Select Billing No.</b></td>
					</tr>
				`);
			}
			if (<?php echo ($show_input) ? 'true' : 'false' ?>) {
				recomputeAll();
			}
		}
		displayDetails(voucher_details);

		function recomputeAll() {
			var taxrates = <?php echo $taxrates ?>;
			if ($('#tableList tbody tr .unitprice').length) {
				var total_amount = 0;
				var vatable_sales = 0;
				var vatexempt_sales = 0;
				var vatzerorated = 0;
				var total_tax = 0;
				var total_discount = 0;
				var discounttype = $('#discounttype').val();
				$('#tableList tbody tr').each(function() {
					var price = removeComma($(this).find('.unitprice').val());
					var quantity = removeComma($(this).find('.issueqty').val());
					var tax = $(this).find('.taxcode').val();
					var discount = $(this).find('.discount').val();
					var taxrate = taxrates[tax] || 0;

					var amount = (price * quantity);

					var discount_amount = 0;
					var discountedamount = 0;

					if (discounttype == 'perc') {
						discount_amount = amount * (discount/100);
					}
					else {
						discount_amount = discount;
					}

					discountedamount = amount - discount_amount;

					amount = discountedamount;
					total_discount += discount_amount;
					total_amount += discountedamount;

					var taxamount = amount * parseFloat(taxrate);
					total_tax += taxamount;

					$(this).find('.tax_field').val(tax);
					$(this).find('.taxrate').val(taxrate);
					$(this).find('.taxamount').val(taxamount);

					if (taxrate > 0) {
						vatable_sales += amount;
					} 
					else {
						if (tax == '' || tax == 'none' || tax == 'ES') {
							vatexempt_sales = amount;
						}
						else {
							vatzerorated = amount;
						}
					}

					$(this).find('.discount_amount').val(discount_amount);
					$(this).find('.discountedamount').val(discountedamount);

					$(this).find('.amount').val(addComma(amount)).closest('.form-group').find('.form-control-static').html(addComma(amount));;
				});
				//var discounttype = $('#tableList tfoot .discounttype:checked').val();
				//var discount_rate = $('#tableList tfoot #discountrate').val();
				//var discount_amount = removeComma($('#tableList tfoot #discountamount').val());
				// if (discounttype == 'perc') {
				// 	discount_amount = total_amount * discount_rate / 100;
				// 	$('#tableList tfoot #discountamount').val(addComma(discount_amount));
				// }

				var total_amount_due = total_amount + total_tax;
				$('#tableList tfoot .vat_sales').val(vatable_sales).closest('.form-group').find('.form-control-static').html(addComma(vatable_sales));
				$('#tableList tfoot .vat_zerorated').val(vatzerorated).closest('.form-group').find('.form-control-static').html(addComma(vatzerorated));
				$('#tableList tfoot .vat_exempt').val(vatexempt_sales).closest('.form-group').find('.form-control-static').html(addComma(vatexempt_sales));
				$('#tableList tfoot .total_amount').val(total_amount).closest('.form-group').find('.form-control-static').html(addComma(total_amount));
				$('#tableList tfoot .total_tax').val(total_tax).closest('.form-group').find('.form-control-static').html(addComma(total_tax));
				$('#tableList tfoot .total_amount_due').val(total_amount_due).closest('.form-group').find('.form-control-static').html(addComma(total_amount_due));
				$('#tableList tfoot .total_discount').val(total_discount).closest('.form-group').find('.form-control-static').html(addComma(total_discount));
			}
		}
		$('#modal_form').on('submit', function(e) {
			e.preventDefault();
			$('#update_exchangerate_button').attr('disabled', true);
			$('#cancel_exchangerate_button').attr('disabled', true);
			$(this).find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if ($(this).find('.form-group.has-error').length == 0) {
				$.post('<?=MODULE_URL?>ajax/ajax_update_exchangerate', $(this).serialize(), function(data) {
					if (data.success) {
						$('#exchangerate_expired').modal('hide');
						$('#currency_changed').modal('show');
						currency_list = data.currency_list;
					}
					$('#update_exchangerate_button').attr('disabled', false);
					$('#cancel_exchangerate_button').attr('disabled', false);
				});
			} else {
				$('#update_exchangerate_button').attr('disabled', false);
				$('#cancel_exchangerate_button').attr('disabled', false);
			}
		});
		$('#tableList tbody').on('input change blur', '.taxcode, .unitprice, .issueqty, .discount', function() {
			recomputeAll();
		});
		$('#tableList tfoot .discount_entry').on('input blur', function() {
			$(this).closest('tr').find('.discounttype').iCheck('uncheck');
			$(this).closest('.input-group').find('.discounttype').iCheck('check');
		});
		$('#tableList tfoot').on('ifChecked', '.discounttype', function() {
			$(this).closest('tr').find('.discounttype:not(:checked)').closest('.input-group').find('.discount_entry.rate').val('0.00');
			recomputeAll();
		});
	</script>
	<?php if ($show_input): ?>
	<script>
		$('#customer').on('change', function() {
			$('#job_orderno').val('');
		});

		$('#job_orderno').on('focus', function() {
			var customer = $('#customer').val();
			ajax.customer = customer;
			if (customer == '') {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Customer');
				$('#customer').trigger('blur');
			} else {
				$('#jo_tableList tbody').html(`<tr>
					<td colspan="4" class="text-center">Loading Items</td>
					</tr>`);
				$('#pagination').html('');
				getJOList();
			}
		});
		$('#jo_list_modal #jo_search').on('input', function() {
			ajax.page = 1;
			ajax.search = $(this).val();
			getJOList();
		});
		$('#jo_list_modal #pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getJOList();
			}
		});
		function getJOList() {
			ajax.limit = 5;
			$('#jo_list_modal').modal('show');
			if (ajax_call != '') {
					ajax_call.abort();
			}
			var jo_no = $('#jo_no').val();
			ajax.jo_no = jo_no;
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_jo_list', ajax, function(data) {
				$('#jo_tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getJOList();
				}
			});
		}
		$('#jo_tableList').on('click', 'tr[data-id]', function() {
			var jono = $(this).attr('data-id');
			var discount = $(this).attr('data-discount');
			var sq = $(this).attr('data-sq');
			$('#sq').val(sq);
			$('#job_orderno').val(jono).trigger('blur');
			$('#discounttype').val(discount).trigger('change.select2');
			$('#jo_list_modal').modal('hide');
			$('#addNewItem').hide();
			loadJODetails();
		});
		function loadJODetails() {
			var voucherno = $('#job_orderno').val();
			if (voucherno) {
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_ordered_details', { voucherno: voucherno }, function(data) {
					if ( ! data.success) {
						$('#tableList tbody').html(data.table);
					} else {
						$('#tableList tbody').html('');
						displayDetails(data.details);
					}
				});
			}
		}

		$('.fulldaterange').daterangepicker({
			timePicker: true,
			timePickerIncrement: 1,
			locale: {
				format: 'MMM DD, YYYY hh:mm:ss A'
			}
		});
		$('#addNewItem').on('click', function() {
			addVoucherDetails();
		});
		var itemdetail_list = <?= json_encode($itemdetail_list) ?>;
		$('#tableList tbody').on('change', '.itemcode', function() {
			var itemcode_element = $(this).closest('tr').find('.detailparticular');
			var itemcode_uom = $(this).closest('tr').find('.issueuom');
			var itemcode_price = $(this).closest('tr').find('.unitprice');
			var itemcode = $(this).val();
			itemdetail_list.forEach(function(itemdetail) {
				if (itemdetail.itemcode == itemcode) {
					itemcode_element.val(itemdetail.itemdesc);
					itemcode_uom.val(itemdetail.uom_base);
					itemcode_price.val(itemdetail.itemprice);
				}
			});
		});
		function deleteVoucherDetails(id) {
			delete_row.remove();
			if ($('#tableList tbody tr').length < min_row) {
				addVoucherDetails();
			}
		}
		$('body').on('click', '.delete_row', function() {
			delete_row = $(this).closest('tr');
		});
		$(function() {
			linkDeleteToModal('.delete_row', 'deleteVoucherDetails');
		});
		$('#main_form').on('click', '[type="submit"]', function(e) {
			e.preventDefault();
			var form_element = $(this).closest('form');
			var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
			recomputeAll();
			$('#submit_container [type="submit"]').attr('disabled', true);
			form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (form_element.find('.form-group.has-error').length == 0) {
				var items = 0;
				$('.issueqty:not([readonly])').each(function() {
					items += removeComma($(this).val());
				});
				if ($('.issueqty:not([readonly])').length > 0 && items > 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data, function(data) {
						if (data.success) {
							$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = data.redirect;						
							}, 1000)
						} else {
							$('#submit_container [type="submit"]').attr('disabled', false);
						}
					});
				} else {
					$('#warning_modal').modal('show').find('#warning_message').html('Please Add an Item');
					$('#submit_container [type="submit"]').attr('disabled', false);
				}
			} else {
				form_element.find('.form-group.has-error').first().find('input, textarea, select').focus();
				$('#submit_container [type="submit"]').attr('disabled', false);
			}
		});

		$('#discounttype').on('select2:selecting',function(e){
			var curr_type	=  $(this).val();	
			var new_type 	= e.params.args.data.id;
			if(curr_type == "none" || curr_type == ""){
				$('#discounttype').closest('.form-group').removeClass('has-error');

				if(new_type=="none" || new_type==""){
					$('.discount').prop('readOnly',true);
				} else {
					$('#tableList tbody tr').find('.itemcode').each(function(index, value) {
						$(this).closest('tr').find('.discount').prop('readonly',false)
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
			$('#discount_type').val(newtype);
			$('#discounttypeModal').modal('hide');
			$('.discount').val('0.00');

			if(newtype=="none" || newtype==""){
				$('.discount').prop('readOnly',true);
			} else {
				$('#tableList tbody tr').find('.itemcode').each(function(index, value) {
					$(this).closest('tr').find('.discount').prop('readonly',false)
				});
			}
			recomputeAll();
		});

		$('#discounttypeModal').on('click','#disc_no',function(){
			$('#discounttypeModal').modal('hide');
		});

		$('#main_form').on('change','.discount',function(e){
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
			recomputeAll();
		});

		$('#main_form').on('change','.unitprice',function(e){
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
			recomputeAll();
		});

		$(document).ready(function() {
			var discounttype = $('#discounttype').val();
			var job = $('#job_orderno').val();
			if ((discounttype == 'amt' || discounttype == 'perc') && job == '') {
				$('#tableList tbody').find('.discount').removeAttr('readonly');
			} 
			else {
				$('#tableList tbody').find('.discount').attr('readonly', '').val('0.00');
			}
			if (job == '') {
				$('#tableList tbody').find('.unitprice').removeAttr('readonly');
				$('#tableList tbody').find('.taxcode').removeAttr('disabled');
			}
			else {
				$('#tableList tbody').find('.unitprice').attr('readonly', true);
				$('#tableList tbody').find('.taxcode').attr('disabled', true);
			}
		});
	</script>
	<?php endif ?>