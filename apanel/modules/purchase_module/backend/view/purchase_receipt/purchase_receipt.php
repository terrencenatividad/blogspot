	<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php if ($show_input && $ajax_task != 'ajax_edit'): ?>
										<div class="form-group">
											<label for="voucherno" class="control-label col-md-4">Purchase Receipt No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Purchase Receipt No.')
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
											->setAttribute(array('readonly', 'data-date-start-date' => $close_date))
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
											->setLabel('Supplier ')
											->setPlaceholder('Select Supplier')
											->setSplit('col-md-4', 'col-md-8')
											->setName('vendor')
											->setId('vendor')
											->setList($vendor_list)
											->setValue($vendor)
											->setValidation('required')
											// ->addHidden(($ajax_task != 'ajax_create'))
											->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Invoice No. ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('invoiceno')
											->setId('invoiceno')
											->setValue($invoiceno)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('PO No. ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('source_no')
											->setId('source_no')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($source_no)
											// ->addHidden($source_no)
											->setValidation('required')
											->draw($show_input);
											// ->draw($show_input && $ajax_task != 'ajax_edit');
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('dropdown')
											->setLabel('Warehouse ')
											->setPlaceholder('Select Warehouse')
											->setSplit('col-md-4', 'col-md-8')
											->setName('warehouse')
											->setId('warehouse')
											->setList($warehouse_list)
											->setValue($warehouse)
											->setValidation('required')
											// ->addHidden(($ajax_task != 'ajax_create'))
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
											->setValue($remarks)
											->draw($show_input);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-sidepad only-checkbox full-form">
						<thead>
							<tr class="info">
								<?php if ($show_input): ?>
								<th class="text-center" style="width: 20px"><input type="checkbox" class="checkall"></th>
								<?php endif ?>
								<th class="col-xs-2">Item</th>
								<th class="col-xs-2">Description</th>
								<th class="col-xs-1">Warehouse</th>
								<th class="col-xs-1 text-right">Price</th>
								<th class="col-xs-1 text-right">Qty</th>
								<th class="col-xs-1">UOM</th>
								<th class="col-xs-2">Tax</th>
								<th class="col-xs-2 text-right">Amount</th>
								<?php if (false): ?>
								<th style="width: 50px;"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
						<?php if (false): ?>
							<tfoot>
								<td colspan="9">
									<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
								</td>
							</tfoot>
						<?php endif ?>
						<tfoot class="summary text-right" style="display: none">
							<tr>
								<td colspan="7"><label class="control-label">Total Amount</label></td>
								<td colspan="2">
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
							</tr>
							<tr>
								<td colspan="7"><label class="control-label">Total Purchase Tax</label></td>
								<td colspan="2">
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
							</tr>
							<tr>
								<td colspan="7"><label class="control-label">Discount</label></td>
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
																		->setValue(((empty($discountrate)) ? '0.00' : number_format($discountrate, 2)))
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
							</tr>
							<tr>
								<td colspan="7"><label class="control-label">Withholding Tax</label></td>
								<td colspan="2">
									<div class="row">
										<?php if ($show_input): ?>
											<div class="col-md-6">
											<?php
												echo $ui->formField('dropdown')
														->setSplit('', 'col-md-12')
														->setName('wtaxcode')
														->setClass('wtaxcode text-left')
														->setNone('None')
														->setList($wtaxcode_list)
														->setValue(((empty($wtaxcode)) ? '0.00' : $wtaxcode))
														->draw($show_input);
											?>
											</div>
											<div class="col-md-6">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('wtaxamount')
														->setClass('wtaxamount')
														->setValue(((empty($wtaxamount)) ? '0.00' : number_format($wtaxamount, 2)))
														->addHidden()
														->draw($show_input);

												echo $ui->setElement('hidden')
														->setName('wtaxrate')
														->setClass('wtaxrate')	
														->setValue(((empty($wtaxrate)) ? '0.00' : number_format($wtaxrate, 2)))
														->draw();
											?>
											</div>
										<?php else: ?>
										<div class="col-md-12">
											<?php
												echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('wtaxamount')
														->setClass('wtaxamount')
														->setValue(((empty($wtaxamount)) ? '0.00' : number_format($wtaxamount, 2)))
														->addHidden()
														->draw($show_input);
											?>
											</div>
										<?php endif ?>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="6"></td>
								<td colspan="3">
									<hr style="margin: 0">
								</td>
							</tr>
							<tr>
								<td colspan="7"><label class="control-label">Total Amount Due</label></td>
								<td colspan="2">
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
							</tr>
						</tfoot>
					</table>
					<div id="header_values"></div>
				</div>
				<div class="box-body">
					<hr>
					<div class="row">
						<div id="submit_container" class="col-md-12 text-center">
							<?php
								if ($stat == 'Received' && $restrict_pr || empty($stat)) {
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
	<div id="purchase_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Purchase Order List</h4>
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
					<table id="purchase_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-3">PO No.</th>
								<th class="col-xs-3">Transaction Date</th>
								<th class="col-xs-4">Notes</th>
								<th class="col-xs-2 text-right">Amount</th>
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
		var min_row		= 0;
		function addVoucherDetails(details, index) {
			var details = details || {itemcode: '', detailparticular: '', receiptqty: ''};
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.receiptqty;
			delete other_details.warehouse;
			delete other_details.unitprice;
			delete other_details.amount;
			delete other_details.taxamount;
			delete other_details.discountamount;
			delete other_details.withholdingamount;
			delete other_details.taxcode;
			delete other_details.taxrate;
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
					<?php if ($show_input): ?>
					<td>
						<?php
							echo $ui->loadElement('check_task')
									->addCheckbox()
									->setValue('` + details.itemcode + `')
									->draw();
						?>
					</td>
					<?php endif ?>
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
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setValue('` + details.detailparticular + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							$value = "<span id='temp_view_warehouse_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_warehouse[]')
								->setClass('warehouse')
								->setList($warehouse_list)
								->setValue($value)
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setClass('unitprice')
								->setName('unitprice[]')
								->setValue('` + addComma(details.unitprice) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('receiptqty[]')
								->setClass('receiptqty text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.receiptqty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.receiptqty, 0) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + details.receiptuom.toUpperCase() + `')
								->draw(false);
						?>
					</td>
					<td>
						<?php
							$value = "<span id='temp_view_taxrate_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('taxcode[]')
								->setClass('taxcode')
								->setList($taxrate_list)
								->setValue($value)
								->setNone('None')
								->draw($show_input);

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
									
							echo $ui->setElement('hidden')
									->setName('detail_discountamount[]')
									->setClass('discountamount')	
									->setValue('` + (parseFloat(details.discountamount) || 0) + `')
									->draw();
									
							echo $ui->setElement('hidden')
									->setName('detail_withholdingamount[]')
									->setClass('withholdingamount')	
									->setValue('` + (parseFloat(details.withholdingamount) || 0) + `')
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
								->addHidden()
								->draw($show_input);
						?>
						` + otherdetails + `
					</td>
					<?php if (false): ?>
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
			if (details.warehouse != '') {
				$('#tableList tbody').find('tr:last .warehouse').val(details.warehouse);
			}
			if (details.taxcode != '') {
				$('#tableList tbody').find('tr:last .taxcode').val(details.taxcode);
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
			var warehouselist = <?= json_encode($warehouse_list) ?>;
			warehouselist.forEach(function(warehouse) {
				if (warehouse.ind == details.warehouse) {
					$('#temp_view_warehouse_' + index).html(warehouse.val);
				}
			});
			var taxrate_list = <?= json_encode($taxrate_list) ?>;
			taxrate_list.forEach(function(tax) {
				if (tax.ind == details.taxcode) {
					$('#temp_view_taxrate_' + index).html(tax.val);
				}
			});
			var warehouse = $('#warehouse').val();
			if (warehouse == details.warehouse) {
				$('#tableList tbody').find('tr:last .receiptqty').each(function() {
					if (details.receiptqty > 0) {
						$(this).removeAttr('readonly').val($(this).attr('data-value'));
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
					} else {
						$('#tableList tbody').find('tr:last .receiptqty').attr('readonly', '').val(0);
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
					}
				});
			} else {
				$('#tableList tbody').find('tr:last .receiptqty').attr('readonly', '').val(0);
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
			}
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
						<td colspan="9" class="text-center"><b>Select Purchase Order No.</b></td>
					</tr>
				`);
			}
			if (<?php echo ($show_input) ? 'true' : 'false' ?>) {
				recomputeAll();
			}
		}
		displayDetails(voucher_details);
		var header_values = <?php echo $header_values ?>;
		function displayHeader(header) {
			var inputs = '';
			for (var key in header) {
				if (header.hasOwnProperty(key)) {
					inputs += `<?php 
						echo $ui->setElement('hidden')
								->setName('header_` + key + `')
								->setValue('` + header[key] + `')
								->draw();
					 ?>`;
				}
			}
			$('#header_values').html(inputs);
		}
		displayHeader(header_values);
		function recomputeAll() {
			var taxrates = <?php echo $taxrates ?>;
			if ($('#tableList tbody tr .unitprice').length) {
				var total_amount = 0;
				var total_tax = 0;
				$('#tableList tbody tr').each(function() {
					var price = removeComma($(this).find('.unitprice').val());
					var quantity = removeComma($(this).find('.receiptqty').val());
					var tax = $(this).find('.taxcode').val();
					var taxrate = taxrates[tax] || 0;

					var amount = (price * quantity);
					var taxamount = removeComma(addComma(amount - (amount / (1 + parseFloat(taxrate)))));
					amount = amount - taxamount;
					total_amount += amount;
					total_tax += taxamount;
					
					$(this).find('.taxrate').val(taxrate);
					$(this).find('.taxamount').val(taxamount);

					$(this).find('.amount').val(addComma(amount)).closest('.form-group').find('.form-control-static').html(addComma(amount));
				});
				var discounttype = $('#tableList tfoot .discounttype:checked').val();
				var discount_rate = removeComma($('#tableList tfoot #discountrate').val());
				var discount_amount = removeComma($('#tableList tfoot #discountamount').val());
				if (discounttype == 'perc') {
					discount_amount = (total_amount + total_tax) * discount_rate / 100;
					$('#tableList tfoot #discountamount').val(addComma(discount_amount));
				}
				var wtaxcode = $('#tableList tfoot .wtaxcode').val();
				var wtaxrate = taxrates[wtaxcode] || 0;
				var withholding_tax = total_amount * wtaxrate;

				var total_amount_due = total_amount + total_tax - discount_amount - withholding_tax;
				$('#tableList tfoot .total_amount').val(total_amount).closest('.form-group').find('.form-control-static').html(addComma(total_amount));
				$('#tableList tfoot .total_tax').val(total_tax).closest('.form-group').find('.form-control-static').html(addComma(total_tax));
				$('#tableList tfoot .wtaxrate').val(wtaxrate);
				$('#tableList tfoot .wtaxamount').val(withholding_tax).closest('.form-group').find('.form-control-static').html(addComma(withholding_tax));
				$('#tableList tfoot .total_amount_due').val(total_amount_due).closest('.form-group').find('.form-control-static').html(addComma(total_amount_due));


				$('#tableList tbody tr').each(function() {
					var price = removeComma($(this).find('.unitprice').val());
					var quantity = removeComma($(this).find('.receiptqty').val());
					var tax = $(this).find('.taxcode').val();
					var taxrate = taxrates[tax] || 0;

					var amount = (price * quantity);
					var discount_rate = removeComma($('#tableList tfoot #discountrate').val()) / 100;
					var total_discount = removeComma($('#tableList tfoot #discountamount').val());
					
					if (discounttype != 'perc') {
						discount_rate = total_discount / (total_amount + total_tax);
					}
					withholdingrate = removeComma(addComma(withholding_tax)) / total_amount;
					var discount_amount = amount * discount_rate;
					var taxamount = removeComma(addComma(amount - (amount / (1 + parseFloat(taxrate)))));
					amount = removeComma(addComma(amount - taxamount));
					var withholdingamount = amount * withholdingrate;

					$(this).find('.discountamount').val(discount_amount);
					$(this).find('.withholdingamount').val(withholdingamount);
				});
			}
		}
		$('#tableList tbody').on('input change blur', '.taxcode, .unitprice, .receiptqty', function() {
			recomputeAll();
		});
		$('#tableList tfoot').on('input change blur', '.wtaxcode', function() {
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
		$('#addNewItem').on('click', function() {
			addVoucherDetails();
		});
		<?php // if ($ajax_task == 'ajax_create'): ?>
		$('#source_no').on('focus', function() {
			var vendor = $('#vendor').val();
			ajax.vendor = vendor;
			if (vendor == '') {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Supplier');
				$('#vendor').trigger('blur');
			} else {
				$('#purchase_tableList tbody').html(`<tr>
					<td colspan="4" class="text-center">Loading Items</td>
				</tr>`);
				$('#pagination').html('');
				getList();
			}
		});
		function getList() {
			ajax.limit = 5;
			$('#purchase_list_modal').modal('show');
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_purchase_list', ajax, function(data) {
				$('#purchase_tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		$('#table_search').on('input', function() {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		<?php // endif ?>
		$('#vendor').on('change', function() {
			ajax.vendor = $(this).val();
			$('#source_no').val('');
			$('#tableList tbody').html(`
				<tr>
					<td colspan="9" class="text-center"><b>Select Purchase Order No.</b></td>
				</tr>
			`);
		});
		$('#warehouse').on('change', function() {
			var warehouse = $(this).val();
			$('#tableList tbody .receiptqty').each(function() {
				var warehouse_row = $(this).closest('tr').find('.warehouse').val();
				if (warehouse == warehouse_row) {
					$(this).removeAttr('readonly').val($(this).attr('data-value'));
					$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('check').iCheck('enable');
				} else {
					$(this).attr('readonly', '').val(0);
					$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
				}
			});
			recomputeAll();
		});
		$('tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
			$(this).closest('tr').find('.receiptqty').attr('readonly', '').val(0).trigger('blur');
		});
		$('tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
			var n = $(this).closest('tr').find('.receiptqty');
			n.removeAttr('readonly', '').val(n.attr('data-value')).trigger('blur');
		});
		$('#purchase_tableList').on('click', 'tr[data-id]', function() {
			var so = $(this).attr('data-id');
			$('#source_no').val(so).trigger('blur');
			$('#purchase_list_modal').modal('hide');
			loadPurchaseDetails();
		});
		function loadPurchaseDetails() {
			var voucherno = $('#source_no').val();
			if (voucherno) {
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_purchase_details', { voucherno: voucherno }, function(data) {
					if ( ! data.success) {
						$('#tableList tbody').html(data.table);
					} else {
						$('#tableList tbody').html('');
						displayDetails(data.details);
						displayHeader(data.header);
					}
				});
			}
		}
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
		$('form').on('click', '[type="submit"]', function(e) {
			e.preventDefault();
			var form_element = $(this).closest('form');
			var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
			recomputeAll();
			$('#submit_container [type="submit"]').attr('disabled', true);
			form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (form_element.find('.form-group.has-error').length == 0) {
				var items = 0;
				$('.receiptqty:not([readonly])').each(function() {
					items += removeComma($(this).val());
				});
				if ($('.receiptqty:not([readonly])').length > 0 && items > 0) {
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
	</script>
	<?php endif ?>