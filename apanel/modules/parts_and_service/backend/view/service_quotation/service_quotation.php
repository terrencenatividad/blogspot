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
											<label for="voucherno" class="control-label col-md-4">Service Quotation No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Service Quotation No.')
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
											->setLabel('Job Type')
											->setPlaceholder('Select Job Type')
											->setSplit('col-md-4', 'col-md-8')
											->setName('job_type')
											->setId('job_type')
											->setList($job_list)
											->setValue($job_type)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Target Date ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('targetdate')
											->setId('targetdate')
											->setClass('datepicker-input')
											->setAttribute(array('readonly' => ''))
											->setAddon('calendar')
											->setValue($targetdate)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('dropdown')
											->setLabel('Customer ')
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
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Reference')
											->setSplit('col-md-4', 'col-md-8')
											->setName('reference')
											->setId('reference')
											->setValue($reference)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									&nbsp;
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('dropdown')
											->setLabel('Discount Type ')
											->setPlaceholder('None')
											->setSplit('col-md-4', 'col-md-8')
											->setName('discount_type')
											->setId('discount_type')
											->setList($discount_type_list)
											->setValue($discount_type)
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
											->setName('notes')
											->setId('notes')
											->setValue($notes)
											->draw($show_input);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-condensed table-sidepad only-checkbox full-form">
						<thead>
							<tr class="info">
								<th class="col-xs-2">Item</th>
								<th class="col-xs-<?php echo ($show_input) ? 2 : 2 ?>">Description</th>
								<th class="col-xs-1">w/ Warranty</th>
								<th class="col-xs-1">Warehouse</th>
								<th class="col-xs-1 text-right">Qty</th>
								<th style="width: 50px;">UOM</th>
								<th class="col-xs-1 text-right">Price</th>
								<th class="col-xs-1 text-right">Discount</th>
								<th class="col-xs-1">Tax</th>
								<th class="col-xs-2 text-right">Amount</th>
								<?php if ($show_input): ?>
								<th style="width: 50px;"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
						<tfoot class="summary">
							<tr>
								<td>
									<?php if ($show_input): ?>
										<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
									<?php endif ?>
								</td>
								<td class="right" colspan="8">
									<label class="control-label col-md-12">VATable Sales</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_vatable_sales')
												->setId('t_vatable_sales')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_vatable_sales,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">
									<label class="control-label col-md-12">VAT Exempt Sales</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_vat_exempt_sales')
												->setId('t_vat_exempt_sales')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_vat_exempt_sales,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">
									<label class="control-label col-md-12">Total Sales</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_vatsales')
												->setId('t_vatsales')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_vatsales,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">
									<label class="control-label col-md-12">Add 12% VAT</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_vat')
												->setId('t_vat')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_vat,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">&nbsp;</td>
								<td class="text-right" >
									<hr/>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">
									<label class="control-label col-md-12">Total Amount</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_amount')
												->setId('t_amount')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_amount,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">&nbsp;</td>
								<td class="text-right" >
									<br/>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
							</tr>
							<tr>
								<td class="right" colspan="9">
									<label class="control-label col-md-12">Discount</label>
								</td>
								<td class="text-right" >
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 col-sm-12')
												->setName('t_discount')
												->setId('t_discount')
												->setClass("input_label text-right remove-margin")
												->setAttribute(array("readOnly"=>"readOnly"))
												->setValue(number_format($t_discount,2))
												->draw($show_input);
									?>
								</td>
								<?php if($show_input):?><td></td><?php endif;?>
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
								if ($stat == 'Prepared' && $restrict_dr || empty($stat)) {
									echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
								}
								echo '&nbsp;&nbsp;&nbsp;';
								echo $ui->drawCancel();
							?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<script>
		var delete_row	= {};
		var ajax		= {};
		var ajax_call	= '';
		var min_row		= 1;
		function addVoucherDetails(details, index) {
			var details = details || {itemcode: '', detailparticular: '', warranty: '', warehouse: '', quantity: '0', uom: 'PCS', price: '0.00', discount: '0.00', amount: '0.00', taxcode: '', taxrate: '',taxamount: '0.00'};
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			//delete other_details.warranty;
			delete other_details.warehouse;
			delete other_details.quantity;
			delete other_details.uom;
			delete other_details.price;
			delete other_details.discount;
			delete other_details.amount;
			delete other_details.taxcode;
			var otherdetails = '';
			for (var key in other_details) {
				if (other_details.hasOwnProperty(key)) {
					otherdetails += `<?php 
						echo $ui->setElement('hidden')
								->setName('` + key + `[]')
								->setValue('` + other_details[key] + `')
								->setClass('.` + key + `_hidden')
								->draw();
					 ?>`;
				}
			}
			var row = `
				<tr>
					<td>
						<?php
							$value = "<span id='temp_view_itemcode_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_itemcode[]')
								->setClass('itemcode')
								->setList($item_list)
								->setValue($value)
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setValue('` + details.detailparticular + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-center">
						<?php
							echo $ui->loadElement('check_task')
									->addCheckbox()
									->setValue('` + details.warranty + `')
									->draw();
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
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('quantity[]')
								->setClass('quantity text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.quantity) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.quantity, 0) || 0) + `')
								->draw($show_input);
						?>
						` + otherdetails + `
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw(false);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('price[]')
								->setClass('price text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.price) || 0) + `'))
								->setValidation('required decimal')
								->setValue('` + (addComma(details.price, 0) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('discount[]')
								->setClass('discount text-right')
								->setAttribute(
									array(
										'data-value' => '` + (parseFloat(details.discount) || 0) + `'
									)
								)
								->setValidation('required decimal')
								->setValue('` + (addComma(details.discount, 0) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							$value = "<span id='temp_view_taxcode_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('taxcode[]')
								->setClass('taxcode')
								->setList($taxrate_list)
								->setValue($value)
								->setNone('none')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('amount[]')
								->setClass('amount text-right')
								->setAttribute(
									array(
										'data-value' => '` + (parseFloat(details.amount) || 0.00) + `',
										'readOnly' => 'readOnly'
									)
								)
								->setValidation('required decimal')
								->setValue('` + (addComma(details.amount, 0.00) || 0.00) + `')
								->draw($show_input);
						?>
					</td>
					<?php if ($show_input): ?>
					<td>
						<button type="button" class="btn btn-danger btn-flat delete_row" style="outline:none;">
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
			if (details.warranty == 'yes') {
				$(this).removeAttr('readonly').val($(this).attr('data-value'));
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
			} else {
				$('#tableList tbody').find('tr:last .warranty_hidden').attr('readonly', '').val(0);
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
			}
		}
		var voucher_details = <?php echo $voucher_details ?>;
		function displayDetails(details) {
			//$('#tableList tfoot.summary').hide();
			if (details.length < min_row) {
				for (var x = details.length; x < min_row; x++) {
					addVoucherDetails('', x);
				}
			}
			if (details.length > 0) {
				details.forEach(function(details, index) {
					addVoucherDetails(details, index);
				});
				//$('#tableList tfoot.summary').show();
			} else if (min_row == 0) {
				$('#tableList tbody').append(`
					<tr>
						<td colspan="9" class="text-center"><b>Select Sales Order No.</b></td>
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
					var quantity = removeComma($(this).find('.issueqty').val());
					var tax = $(this).find('.taxcode').val();
					var taxrate = taxrates[tax] || 0;

					var amount = (price * quantity);
					var taxamount = (amount * taxrate);
					amount = amount - taxamount;
					total_amount += amount;
					total_tax += taxamount;
					
					$(this).find('.taxrate').val(taxrate);
					$(this).find('.taxamount').val(taxamount);
					$(this).find('.amount').val(addComma(amount));
				});
				var discounttype = $('#tableList tfoot .discounttype:checked').val();
				var discount_rate = $('#tableList tfoot #discountrate').val();
				var discount_amount = $('#tableList tfoot #discountamount').val();
				if (discounttype == 'perc') {
					discount_amount = total_amount * discount_rate / 100;
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
			}
		}
		$('#tableList tbody').on('input change blur', '.taxcode, .unitprice, .issueqty', function() {
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
			var customer = $('#customer').val();
			ajax.customer = customer;
			if (customer == '') {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Customer');
				$('#customer').trigger('blur');
			} else {
				$('#ordered_tableList tbody').html(`<tr>
					<td colspan="4" class="text-center">Loading Items</td>
				</tr>`);
				$('#pagination').html('');
				getList();
			}
		});
		function getList() {
			ajax.limit = 5;
			$('#ordered_list_modal').modal('show');
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_ordered_list', ajax, function(data) {
				$('#ordered_tableList tbody').html(data.table);
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
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});
		<?php // endif ?>
		// $('#customer').on('change', function() {
		// 	ajax.customer = $(this).val();
		// 	$('#source_no').val('');
		// 	$('#tableList tbody').html(`
		// 		<tr>
		// 			<td colspan="9" class="text-center"><b>Select Sales Order No.</b></td>
		// 		</tr>
		// 	`);
		// });
		// $('#warehouse').on('change', function() {
		// 	var warehouse = $(this).val();
		// 	$('#tableList tbody .issueqty').each(function() {
		// 		var warehouse_row = $(this).closest('tr').find('.warehouse').val();
		// 		if (warehouse == warehouse_row) {
		// 			$(this).removeAttr('readonly').val($(this).attr('data-value'));
		// 			$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('check').iCheck('enable');
		// 		} else {
		// 			$(this).attr('readonly', '').val(0);
		// 			$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
		// 		}
		// 	});
		// 	recomputeAll();
		// });
		$('tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
			$(this).closest('tr').find('.issueqty').attr('readonly', '').val(0).trigger('blur');
		});
		$('tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
			var n = $(this).closest('tr').find('.issueqty');
			n.removeAttr('readonly', '').val(n.attr('data-value')).trigger('blur');
		});
		$('#ordered_tableList').on('click', 'tr[data-id]', function() {
			var so = $(this).attr('data-id');
			$('#source_no').val(so).trigger('blur');
			$('#ordered_list_modal').modal('hide');
			loadPackingListDetails();
		});
		function loadPackingListDetails() {
			var voucherno = $('#source_no').val();
			if (voucherno) {
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_ordered_details', { voucherno: voucherno }, function(data) {
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
				$('.issueqty:not([readonly])').each(function() {
					items += removeComma($(this).val());
				});
				if ($('.issueqty:not([readonly])').length > 0 && items > 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data , function(data) {
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