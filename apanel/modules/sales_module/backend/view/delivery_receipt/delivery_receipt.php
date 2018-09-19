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
											<label for="voucherno" class="control-label col-md-4">Delivery No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Delivery No.')
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
											->setLabel('Customer ')
											->setPlaceholder('Select Customer')
											->setSplit('col-md-4', 'col-md-8')
											->setName('customer')
											->setId('customer')
											->setList($customer_list)
											->setValue($customer)
											->setValidation('required')
											// ->addHidden(($ajax_task != 'ajax_create'))
											->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Target Shipping Date ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('deliverydate')
											->setId('deliverydate')
											->setClass('datepicker-input')
											->setAttribute(array('readonly' => ''))
											->setAddon('calendar')
											->setValue($deliverydate)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Sales Order No. ')
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
								<th class="col-xs-<?php echo ($show_input) ? 3 : 6 ?>">Description</th>
								<th class="col-xs-2">Warehouse</th>
								<?php if ($show_input): ?>
								<th class="col-xs-2 text-right">Available On Hand</th>
								<th class="col-xs-1 text-right">Qty Left</th>
								<?php endif ?>
								<th class="col-xs-2 text-right">Qty</th>
								<th style="width: 50px;">UOM</th>
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
								echo $ui->drawCancel();
							?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<div id="ordered_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Order List</h4>
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
					<table id="ordered_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Sales Order No.</th>
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
			var details = details || {itemcode: '', detailparticular: '', issueqty: ''};
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.issueqty;
			delete other_details.warehouse;
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
					<?php if ($show_input): ?>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + addComma(details.available, 0) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + addComma(details.qtyleft, 0) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<?php endif ?>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('issueqty[]')
								->setClass('issueqty text-right')
								->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.issueqty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.issueqty, 0) || 0) + `')
								->draw($show_input);
						?>
						` + otherdetails + `
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + details.issueuom.toUpperCase() + `')
								->draw(false);
						?>
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
				$('#tableList tbody').find('tr:last .issueqty').each(function() {
					if (details.issueqty > 0) {
						$(this).removeAttr('readonly').val($(this).attr('data-value'));
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
					} else {
						$('#tableList tbody').find('tr:last .issueqty').attr('readonly', '').val(0);
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
					}
				});
			} else {
				$('#tableList tbody').find('tr:last .issueqty').attr('readonly', '').val(0);
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
			ajax.page = $(this).attr('data-page');
			getList();
		});
		<?php // endif ?>
		$('#customer').on('change', function() {
			ajax.customer = $(this).val();
			$('#source_no').val('');
			$('#tableList tbody').html(`
				<tr>
					<td colspan="9" class="text-center"><b>Select Sales Order No.</b></td>
				</tr>
			`);
		});
		$('#warehouse').on('change', function() {
			var warehouse = $(this).val();
			$('#tableList tbody .issueqty').each(function() {
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