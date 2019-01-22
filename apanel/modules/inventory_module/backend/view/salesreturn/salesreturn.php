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
					<!-- <div class="row">
						<div class="col-md-6">
							<?php
								// echo $ui->formField('dropdown')
								// 	->setLabel('Customer')
								// 	->setPlaceholder('Select Customer')
								// 	->setSplit('col-md-4', 'col-md-8')
								// 	->setName('customer')
								// 	->setId('customer')
								// 	->setList($customer_list)
								// 	->setValue($customer)
								// 	->setValidation('required')
								// 	// ->addHidden(($ajax_task != 'ajax_create'))
								// 	->draw($show_input);
							?>
						</div>
					</div> -->
					<div class="row">
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('SI/DR No. ')
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
						<!-- <div class="col-md-6">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Return Type ')
									->setPlaceholder('Select Return Type')
									->setSplit('col-md-4', 'col-md-8')
									->setName('stat')
									->setId('stat')
									->setList(array('1' => 'Returned', '2' => 'Scrapped'))
									->setValue($stat)
									->setValidation('required')
									// ->addHidden(($ajax_task != 'ajax_create'))
									->draw($show_input);
							?>
						</div> -->
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php
								echo $ui->formField('textarea')
								->setLabel('Notes')
								->setSplit('col-md-4', 'col-md-8')
								->setName('remarks')
								->setId('remarks')
								->setValue($remarks)
								->draw($show_input);
							?>
						</div>
						<div class="col-md-6">
							<?php
								echo $ui->formField('textarea')
								->setLabel('Reason ')
								->setSplit('col-md-4', 'col-md-8')
								->setName('reason')
								->setId('reason')
								->setValue($reason)
								->setValidation('required')
								// ->addHidden(($ajax_task != 'ajax_create'))
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
						<th class="text-center" style="width: 20px"><input type="checkbox" class="checkallitem"></th>
						<?php endif ?>
						<th class="col-xs-1">Item</th>
						<th class="col-xs-2">Description</th>
						<th class="col-xs-1">Warehouse</th>
						<th class="col-xs-1 text-right">Defective</th>
						<th class="col-xs-1 text-right">Replacement</th>
						<?php if ($show_input): ?>
						<th class="col-xs-1 text-center">Qty Delivered</th>
						<?php endif ?>
						<th class="col-xs-1 text-right">Qty</th>
						<th class="text-center" style="width: 20px;">UOM</th>
						<th class="col-xs-1 text-right">Unit Cost</th>
						<th class="col-xs-1 text-right">Discount</th>
						<th class="col-xs-1">Tax</th>
						<th class="col-xs-1 text-right">Amount</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
				<tfoot class="summary">
					<tr>
						<td colspan="13" style="border-top:1px solid #E2E2E2;">
							
						</td>	
					</tr>	

					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">VATable Sales</label>
						</td>
						<td class="text-right" colspan="3">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('vat_sales')
										->setId('vat_sales')
										->setClass("input_label text-right remove-margin")
										->setAttribute(array('readOnly'=>"readOnly"))
										->setValue(number_format($vat_sales,2))
										->draw($show_input);
							?>
						</td>
						
					</tr>
					
					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">VAT-Exempt Sales</label>
						</td>
						<td class="text-right" colspan="3">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('vat_exempt')
										->setId('vat_exempt')
										->setAttribute(array('readOnly'=>"readOnly"))
										->setClass("input_label text-right remove-margin")
										->setValue(number_format($vat_exempt,2))
										->draw($show_input);
							?>
						</td>
					</tr>

					<tr id="vat_zerorated_sales" >
						<td colspan="7"></td>
						<td class="right" colspan="3">
							<label class="control-label col-md-12">VAT Zero Rated Sales</label>
						</td>
						<td class="text-right" colspan="3">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('vat_zerorated')
										->setId('vat_zerorated')
										->setAttribute(array("readOnly"=>"readOnly"))
										->setClass("input_label text-right remove-margin")
										->setValue(number_format($vat_zerorated,2))
										->draw($show_input);
							?>
						</td>
					</tr>

					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">Total Sales</label>
						</td>
						<td class="text-right" colspan="3">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('total_sales')
										->setId('total_sales')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
										->setValue(number_format($total_sales,2))
										->draw($show_input);
							?>
						</td>
					</tr>

					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">Add 12% VAT</label>
						</td>
						<td class="text-right" colspan="3">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('total_tax')
										->setId('total_tax')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
										->setValue(number_format($total_tax,2))
										->draw($show_input);
							?>
						</td>
					</tr>

					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">Total Amount Due</label>
						</td>
						<td class="text-right"  colspan="3" style="border-top:1px solid #DDDDDD;">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('total_amount')
										->setId('total_amount')
										->setClass("input_label text-right")
										->setAttribute(array("maxlength" => "40","readOnly"=>"readOnly"))
										->setValue(number_format($total_amount,2))
										->draw($show_input);
							?>
						</td>
					</tr>
					
					<tr>
						<td colspan="7">
						<td class="right" colspan="3">
							<label class="control-label col-md-12">Discount</label>
						</td>
						<td class="text-right" colspan="3">
							<input type = "hidden" value = "<?=$disctype?>" name = "disctype" id = "disctype"/>
							<?php
								echo $ui->formField('text')
										->setSplit('', '')
										->setName('total_discount')
										->setId('total_discount')
										->setClass("input_label text-right")
										->setAttribute(array('readOnly'=>"readOnly"))
										->setValue(number_format($total_discount,2))
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
						if ($stat == 'Returned' && $restrict_ri || empty($stat)) {
							echo $ui->drawSubmit($show_input); 
						}
					?>
					<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
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
			<h4 class="modal-title">Source List</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<?php
						echo $ui->formField('dropdown')
								->setLabel('')
								->setPlaceholder('Select Source Type')
								->setSplit('', 'col-md-12')
								->setName('source')
								->setId('source')
								->setList(array('1' => 'Delivery Receipt', '2' => 'Sales Invoice'))
								->setValue('1')
								// ->addHidden(($ajax_task != 'ajax_create'))
								->draw($show_input);
					?>
				</div>
				<div class="col-md-4 col-md-offset-">
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
						<th class="col-xs-3">Source No.</th>
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
				<input type='checkbox' class='chkitem'>
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

			<td class='text-center'>
				<input type='checkbox' name='defective[]' class='defective' value='Yes'>
			</td>

			<?php else: ?>

			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('txtddefective[]')
						->setClass('txtdefective')
						->setValue('No')
						->addHidden()
						->draw(!$show_input);
				?>
			</td>

			<?php endif ?>
			<?php if ($show_input): ?>
				
			<td class='text-center'>
				<input type='checkbox' name='replacement[]' class='replacement' value='Yes'>
			</td>

			<?php else: ?>

			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('txtreplacement[]')
						->setClass('txtreplacement')
						->setValue('No')
						->addHidden()
						->draw(!$show_input);
				?>
			</td>

			<?php endif ?>


			<?php if ($show_input): ?>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setValue('` + addComma(details.issueqty) + `')
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
						->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 20) + `', 'data-value' => '` + (parseFloat(details.issueqty) || 0) + `'))
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
						->setName('unitprice[]')
						->setClass('unitprice')
						->setValue('` + addComma(details.unitprice) + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('discountrate[]')
						->setClass('discountrate')
						->setValue('` + details.discountrate + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td>
				<?php
					$value = "<span id='temp_view_taxrate_` + index + `'></span>";
					echo $ui->formField('dropdown')
						->setPlaceholder('Select Tax Code')
						->setSplit('', 'col-md-12')
						->setName('taxrate[]')
						->setList($taxrate_list)
						->setValidation('required')
						->setClass('taxrate')
						->setValue($value)
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('amount1[]')
						->setClass('amount1')
						->setValue('` + details.amount + `')
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
	// <td class="text-right">
	// 			<?php
	// 				echo $ui->formField('text')
	// 					->setSplit('', 'col-md-12')
	// 					->setValue('` + addComma(details.maxqty) + `')
	// 					->addHidden()
	// 					->draw($show_input);
	// 			?>
	// 		</td>

	$('#tableList tbody').append(row);

	if (details.itemcode != '') {
		$('#tableList tbody').find('tr:last .itemcode').val(details.itemcode);
	}
	if (details.warehouse != '') {
		$('#tableList tbody').find('tr:last .warehouse').val(details.warehouse);
	}
	if (details.taxcode != '') {
		$('#tableList tbody').find('tr:last .taxrate').val(details.taxcode);
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

	var taxratelist = <?= json_encode($taxrate_list) ?>;

	taxratelist.forEach(function(tax) {
		if (tax.ind == details.taxcode) {
			$('#temp_view_taxrate_' + index).html(tax.val);
		}
	});

	$('#tableList tbody').find('tr:last .issueqty').each(function() {
		if (details.issueqty > 0) {
			$(this).removeAttr('readonly').val(addComma($(this).attr('data-value')));
			$('#tableList tbody').find('tr:last .chkitem').iCheck('check').iCheck('enable');
		} else {
			$('#tableList tbody').find('tr:last .issueqty').attr('readonly', 'readonly').val(0);
			$('#tableList tbody').find('tr:last .chkitem').iCheck('uncheck').iCheck('enable');
		}
	});
}

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
				<td colspan="13" class="text-center"><i>Select <b>Sales Invoice/Delivery Receipt</b> No.</i></td>
			</tr>
		`);
	}
}

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

var voucher_details = <?php echo $voucher_details ?>;
displayDetails(voucher_details);
var header_values = <?php echo $header_values ?>;
displayHeader(header_values);

</script>

<?php if ($show_input): ?>
<script>

$('#tableList tbody').on('blur recompute', '.issueqty', function(e) {
	$(this).val(addComma($(this).val()));
	recomputeAll();
});

$('#source_no').on('focus', function() {
	$('#invoice_tableList tbody').html(`<tr>
		<td colspan="4" class="text-center">Loading Items</td>
	</tr>`);
	$('#pagination').html('');
	getList();
});

function getList() {
	ajax.limit = 5;
	var data = $('#source option:selected').text();
	$('#invoice_list_modal').modal('show');

	if (ajax_call != '') {
		ajax_call.abort();
	}

	ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_invoice_list', {ajax, source:data}, function(data) {
		$('#invoice_tableList tbody').html(data.table);
		$('#pagination').html(data.pagination);
		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			getList();
		}
	});
}

$('#source').on('change', function(){
	ajax.page = 1;
	getList();
});

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

$('tbody').on('ifUnchecked', '.chkitem', function() {
	$(this).closest('tr').find('.issueqty').attr('readonly', 'readonly').val(0);
});

$('tbody').on('ifChecked', '.chkitem', function() {
	var n = $(this).closest('tr').find('.issueqty');
	n.removeAttr('readonly', '').val(addComma(n.attr('data-value')));
});

$('#invoice_tableList').on('click', 'tr[data-id]', function() {
	var so = $(this).attr('data-id');
	$('#source_no').val(so).trigger('blur');
	$('#invoice_list_modal').modal('hide');
	loadSalesDetails();
});

$('table').on('ifToggled', 'tr [type="checkbox"].checkallitem', function() {
	var checked = $(this).prop('checked');
	var check_type = 'ifUnchecked';
	if (checked) {
		check_type = 'ifChecked';
	}
	$(this).closest('table').find('tbody .chkitem:not(:disabled, .disabled)').prop('checked', checked).iCheck('update').trigger(check_type);
});

function loadSalesDetails() {
	var voucherno = $('#source_no').val();
	if (voucherno) {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_invoice_details', { voucherno: voucherno }, function(data) {
			if ( ! data.success) {
				$('#tableList tbody').html(data.table);
			} else {
				var header = data.header;
				$('#tableList tbody').html('');
				displayDetails(data.details);
				$('.vat_sales')		.val(header.vat_sales);
				$('.vat_exempt')	.val(header.vat_exempt);
				$('.vat_zerorated')	.val(header.vat_zerorated);
				$('.total_sales')	.val(header.total_sales);
				$('.total_tax')		.val(header.total_tax);
				$('.total_amount')	.val(header.total_amount);
				$('.total_discount').val(header.total_discount);
			}
		});
	}
}

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
					$('#delay_modal').modal('show');
							// setTimeout(function() {							
							// 	window.location = data.redirect;						
							// }, 1000)
				} else {
					$('#submit_container [type="submit"]').attr('disabled', false);
				}
			});
		} else {
			$('#warning_modal').modal('show').find('#warning_message').html('Please Add an Item');
			$('#submit_container [type="submit"]').attr('disabled', false);
		}
	} else {
		$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
		$('#submit_container [type="submit"]').attr('disabled', false);
	}
});
</script>
<?php endif ?>

<script type="text/javascript">
	
</script>