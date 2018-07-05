<section class="content">
<div class="box box-primary">
	<form action="" method="post" class="form-horizontal">
		<div class="box-body">
			<br>
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						<div class="col-md-6">
							<?php // if ($show_input && $ajax_task != 'ajax_edit'): ?>
							<?php if ($show_input): ?>
								<div class="form-group">
									<label for="voucherno" class="control-label col-md-4">Sales Return No.</label>
									<div class="col-md-8">
										<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
									</div>
								</div>
							<?php else: ?>
								<?php
									echo $ui->formField('text')
										->setLabel('Sales Return No.')
										->setSplit('col-md-4', 'col-md-8')
										->setName('voucherno')
										->setId('voucherno')
										->setValue($voucherno)
										->addHidden($voucherno)
										->setValidation('required')
										// ->draw(($show_input && $ajax_task != 'ajax_edit'));
										->draw($show_input);
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
									->setAttribute(array('readonly', 'data-date-start-date'=>$close_date))
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
									// ->addHidden(($ajax_task != 'ajax_create'))
									->draw($show_input);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('Invoice No.')
									->setSplit('col-md-4', 'col-md-8')
									->setName('source_no')
									->setId('source_no')
									->setAttribute(array('readonly'))
									->setAddon('search')
									->setValue($source_no)
									// ->addHidden($source_no)
									->setValidation('required')
									// ->draw(($show_input && $ajax_task != 'ajax_edit'));
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
						<th class="col-xs-2">Warehouse</th>
						<th class="col-xs-<?php echo ($show_input) ? '1' : '2' ?> text-right">Unit Cost</th>
						<?php if ($show_input): ?>
						<th class="col-xs-1 text-right">Invoiced Qty</th>
						<th class="col-xs-1 text-right">Qty Left</th>
						<?php endif ?>
						<th class="col-xs-<?php echo ($show_input) ? '1' : '2' ?> text-right">Qty</th>
						<th class="col-xs-1">UOM</th>
						<th class="col-xs-1 text-right">Amount</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
				<tfoot class="summary text-right" <?php echo ($ajax_task == 'ajax_create') ? 'style="display: none"' : '' ?>>
					<tr>
						<td colspan="<?php echo ($show_input) ? '9' : '6' ?>"><label class="control-label">Total Amount</label></td>
						<td colspan="1">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('header_amount')
										->setClass('total_amount')
										->setValue(((empty($amount)) ? '0.00' : number_format($amount, 2)))
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
						if ($stat == 'Returned' && !$restrict_ra || empty($stat)) {
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
<div id="invoice_list_modal" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Sales Invoice List</h4>
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
			<table id="invoice_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
				<thead>
					<tr class="info">
						<th class="col-xs-3">PR No.</th>
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
	delete other_details.unitprice;
	delete other_details.amount;
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
						->setName('unitprice[]')
						->setClass('unitprice')
						->setValue('` + addComma(details.unitprice) + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<?php if ($show_input): ?>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setValue('` + addComma(details.realqty) + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setValue('` + addComma(details.maxqty) + `')
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
						->setValue('` + (addComma(details.issueqty) || 0) + `')
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
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('detail_amount[]')
						->setClass('amount')
						->setValue('` + addComma(parseFloat(details.issueqty) * (parseFloat(details.unitprice) || 0).toFixed(2)) + `')
						->addHidden()
						->draw($show_input);
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
	$('#tableList tbody').find('tr:last .issueqty').each(function() {
		if (details.issueqty > 0) {
			$(this).removeAttr('readonly').val(addComma($(this).attr('data-value')));
			$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
		} else {
			$('#tableList tbody').find('tr:last .issueqty').attr('readonly', '').val(0);
			$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
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
				<td colspan="10" class="text-center"><b>Select Sales Invoice No.</b></td>
			</tr>
		`);
	}
	<?php if ($show_input): ?>
	recomputeAll();
	<?php endif ?>
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
function recomputeAll() {
	if ($('#tableList tbody tr .unitprice').length) {
		var total_amount = 0;
		$('#tableList tbody tr').each(function() {
			var price = removeComma($(this).find('.unitprice').val());
			var quantity = removeComma($(this).find('.issueqty').val());

			var amount = (price * quantity);
			total_amount += amount;
			$(this).find('.amount').val(addComma(amount)).closest('.form-group').find('.form-control-static').html(addComma(amount));
		});
		$('#tableList tfoot .total_amount').val(total_amount).closest('.form-group').find('.form-control-static').html(addComma(total_amount));
		$('#tableList tfoot.summary').show();
	}
}
displayHeader(header_values);
</script>
<?php if ($show_input): ?>
<script>
$('#addNewItem').on('click', function() {
	addVoucherDetails();
});
$('#tableList tbody').on('blur recompute', '.issueqty', function(e) {
	recomputeAll();
});
<?php // if ($ajax_task == 'ajax_create'): ?>
$('#source_no').on('focus', function() {
	var customer = $('#customer').val();
	ajax.customer = customer;
	if (customer == '') {
		$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Customer');
		$('#customer').trigger('blur');
	} else {
		$('#invoice_tableList tbody').html(`<tr>
			<td colspan="4" class="text-center">Loading Items</td>
		</tr>`);
		$('#pagination').html('');
		getList();
	}
});
function getList() {
	ajax.limit = 5;
	$('#invoice_list_modal').modal('show');
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_invoice_list', ajax, function(data) {
		$('#invoice_tableList tbody').html(data.table);
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
			<td colspan="10" class="text-center"><b>Select Sales Invoice No.</b></td>
		</tr>
	`);
});
$('tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
	$(this).closest('tr').find('.issueqty').attr('readonly', '').val(0);
});
$('tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
	var n = $(this).closest('tr').find('.issueqty');
	n.removeAttr('readonly', '').val(addComma(n.attr('data-value')));
});
$('#invoice_tableList').on('click', 'tr[data-id]', function() {
	var so = $(this).attr('data-id');
	$('#source_no').val(so).trigger('blur');
	$('#invoice_list_modal').modal('hide');
	loadSalesDetails();
});
function loadSalesDetails() {
	var voucherno = $('#source_no').val();
	if (voucherno) {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_invoice_details', { voucherno: voucherno }, function(data) {
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
		var items = 0;
		$('.issueqty:not([readonly])').each(function() {
			items += removeComma($(this).val());
		});
		if ($('.issueqty:not([readonly])').length > 0 && items > 0) {
			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data, function(data) {
				if (data.success) {
					window.location = data.redirect;
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