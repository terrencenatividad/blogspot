	<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php if (($show_input && $ajax_task != 'ajax_edit')): ?>
										<div class="form-group">
											<label for="voucherno" class="control-label col-md-4">Invoice</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Invoice')
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
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Target Shipping Date')
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
											->setLabel('Packing No.')
											->setSplit('col-md-4', 'col-md-8')
											->setName('packing_no')
											->setId('packing_no')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($packing_no)
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
											->setValue($remarks)
											->draw($show_input);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-sidepad">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Item</th>
								<th class="col-xs-3">Description</th>
								<th class="col-xs-3">Warehouse</th>
								<th class="col-xs-2 text-right">Qty</th>
								<th class="col-xs-1">UOM</th>
								<?php if (false): ?>
								<th style="width: 50px;"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
						<?php if (false): ?>
							<tfoot>
								<td colspan="6">
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
								if (($stat == 'Delivered' || $stat == 'Prepared') && $restrict_dr || empty($stat)) {
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
	<div id="packing_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Packing List</h4>
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
					<table id="packing_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-3">PL No.</th>
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
	<div id="warning_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Oops!</h4>
				</div>
				<div class="modal-body">
					<p>Please select a customer.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
								->setName('issueqty[]')
								->setValidation('required integer')
								->setValue('` + (addComma(details.issueqty) || 0) + `')
								->addHidden()
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
		}
		var voucher_details = <?php echo $voucher_details ?>;
		function displayDetails(details) {
			if (details.length < min_row) {
				for (var x = details.length; x < min_row; x++) {
					addVoucherDetails('', x);
				}
			}
			if (details.length > 0) {
				details.forEach(function(details, index) {
					addVoucherDetails(details, index);
				});
			} else if (min_row == 0) {
				$('#tableList tbody').append(`
					<tr>
						<td colspan="5" class="text-center"><b>Select Packing No.</b></td>
					</tr>
				`);
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
	</script>
	<?php if ($show_input): ?>
	<script>
		$('#addNewItem').on('click', function() {
			addVoucherDetails();
		});
		<?php //if ($ajax_task == 'ajax_create'): ?>
		$('#packing_no').on('focus', function() {
			var customer = $('#customer').val();
			ajax.customer = customer;
			if (customer == '') {
				$('#warning_modal').modal('show');
				$('#customer').trigger('blur');
			} else {
				$('#packing_tableList tbody').html(`<tr>
					<td colspan="4" class="text-center">Loading Items</td>
				</tr>`);
				$('#pagination').html('');
				getList();
			}
		});
		function getList() {
			ajax.limit = 5;
			$('#packing_list_modal').modal('show');
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_packing_list', ajax, function(data) {
				$('#packing_tableList tbody').html(data.table);
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
		<?php //endif ?>
		$('#customer').on('change', function() {
			ajax.customer = $(this).val();
			$('#packing_no').val('');
			$('#tableList tbody').html(`
				<tr>
					<td colspan="9" class="text-center"><b>Select Sales Order No.</b></td>
				</tr>
			`);
		});
		$('#packing_tableList').on('click', 'tr[data-id]', function() {
			var so = $(this).attr('data-id');
			$('#packing_no').val(so).trigger('blur');
			$('#packing_list_modal').modal('hide');
			loadPackingDetails();
		});
		function loadPackingDetails() {
			var voucherno = $('#packing_no').val();
			if (voucherno) {
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_packing_details', { voucherno: voucherno }, function(data) {
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
			$('#submit_container [type="submit"]').attr('disabled', true);
			form_element.find('.form-group').find('input, textarea, select').trigger('blur');
			if (form_element.find('.form-group.has-error').length == 0) {
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data, function(data) {
					if (data.success) {
						window.location = data.redirect;
					} else {
						$('#submit_container [type="submit"]').attr('disabled', false);
					}
				});
			} else {
				form_element.find('.form-group.has-error').first().find('input, textarea, select').focus();
				$('#submit_container [type="submit"]').attr('disabled', false);
			}
		});
	</script>
	<?php endif ?>