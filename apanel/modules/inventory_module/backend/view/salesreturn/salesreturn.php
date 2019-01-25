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
						<?php
							echo $ui->formField('hidden')
								->setName('customer')
								->setId('customer')
								->draw(true);
						?>
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
							<?php
								echo $ui->formField('hidden')
										->setSplit('', '')
										->setName('total_netamount')
										->setId('total_netamount')
										->setValue(number_format($total_netamount,2))
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
	delete other_details.discountrate;
	delete other_details.taxcode;

	console.log(other_details);

	var otherdetails = '';
	for (var key in other_details) {
		if (other_details.hasOwnProperty(key)) {
			otherdetails += `<?php 
				echo $ui->setElement('hidden')
						->setName('` + key + `[]')
						->setClass('` + key + `')
						->setValue('` + other_details[key] + `')
						->draw();
			 ?>\n`;
		}
	}
	console.log(otherdetails);
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
						->setClass('detailparticular')
						->setMaxLength(100)
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
						->setName('warehouse[]')
						->setClass('warehouse')
						->setList($warehouse_list)
						->setValue($value)
						->addHidden()
						->draw($show_input);
				?>
			</td>

			

			

			<?php if ($show_input): ?>

			<td class='text-center'>
				<input type='checkbox' class='defective'>
				<input type='hidden' name='defective[]'	value='No'>
			</td>

			<td class='text-center'>
				<input type='checkbox' class='replacement'>
				<input type='hidden' name='replacement[]' value='No'>
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
				<input type='hidden' class='netamount' name='netamount[]' value = 0>
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
						->setValue('` + details.discountamount + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td>
				<?php
					$value = "<span id='temp_view_taxcode_` + index + `'></span>";
					echo $ui->formField('dropdown')
						->setPlaceholder('Select Tax Code')
						->setSplit('', 'col-md-12')
						->setName('taxcode[]')
						->setList($taxrate_list)
						->setValidation('required')
						->setClass('taxcode')
						->setValue($value)
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('amount[]')
						->setClass('amount')
						->setValue('` + details.amount + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
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
	console.log(details.maxty);
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

	var taxratelist = <?= json_encode($taxrate_list) ?>;

	taxratelist.forEach(function(tax) {
		if (tax.ind == details.taxcode) {
			$('#temp_view_taxcode_' + index).html(tax.val);
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

	var vat_sales 		= 0;
	var vat_exempt 		= 0;
	var vat_zerorated 	= 0;
	var total_sales 	= 0;
	var total_tax 		= 0;
	var total_amount 	= 0;
	var total_netamount = 0;
	var total_discount 	= 0;

	$.each($('#tableList tbody tr'), function(){
		if ($(this).find('.chkitem').is(':checked')) {
			var qty 			= removeComma($(this).find('.issueqty').val());
			var unitprice 		= removeComma($(this).find('.unitprice').val());
			var amount 			= unitprice * qty;
			var discounttype 	= $(this).find('.discounttype').val();
			var discountrate 	= removeComma($(this).find('.discountrate').val());
			var discountamount 	= 0;
			if (discounttype == 'perc') {
				discountamount = amount * discountrate;
			} else {
				discountamount = discountrate;
			}
			var netamount 	= amount - discountamount;
			var taxcode 	= $(this).find('.taxcode').val();
			var taxrate 	= removeComma($(this).find('.taxrate').val());
			var taxamount 	= netamount * taxrate;

			$(this).find('[name="amount[]"]').text(addComma(netamount));
			$(this).find('.amount')			.val(amount);
			$(this).find('.netamount')		.val(netamount);
			$(this).find('.discountamount')	.val(discountamount);
			$(this).find('.taxamount')		.val(taxamount);

			if (taxcode.indexOf('VAT') >= 0) {
				vat_sales += netamount;
			} else if (taxcode == 'ZRS') {
				vat_zerorated += netamount;
			} else {
				vat_exempt += netamount;
			}
			total_netamount += netamount 
			total_discount 	+= discountamount;
			total_tax 		+= taxamount;
		}
	});
	total_sales = vat_sales + vat_zerorated + vat_exempt;
	total_amount = total_sales + total_tax;

	$('#vat_sales')		.val(addComma(vat_sales));
	$('#vat_exempt')	.val(addComma(vat_exempt));
	$('#vat_zerorated')	.val(addComma(vat_zerorated));
	$('#total_sales')	.val(addComma(total_sales));
	$('#total_tax')		.val(addComma(total_tax));
	$('#total_amount')	.val(addComma(total_amount));
	$('#total_netamount').val(addComma(total_netamount));
	$('#total_discount').val(addComma(total_discount));
}

var voucher_details = <?php echo $voucher_details ?>;
displayDetails(voucher_details);
var header_values = <?php echo $header_values ?>;
displayHeader(header_values);

</script>

<?php if ($show_input): ?>
<script>

$('#tableList tbody').on('blur recompute', '.issueqty', function(e) {
	var conversion 		= $(this).closest('tr').find('.conversion').val();
	var issueqty 		= $(this).val();
	var convissueqty 	= issueqty * conversion;
	$(this).closest('tr').find('.convissueqty').val(convissueqty);
	$(this).val(addComma($(this).val()));
	recomputeAll();
});


$('#tableList tbody').on('ifChecked', '.defective', function(e) {
	$(this).closest('td').find('[name="defective[]"]').val('Yes');
});

$('#tableList tbody').on('ifChecked', '.replacement', function(e) {
	$(this).closest('td').find('[name="replacement[]"]').val('Yes');
});


$('#tableList tbody').on('ifUnchecked', '.defective', function(e) {
	$(this).closest('td').find('[name="defective[]"]').val('No');
});

$('#tableList tbody').on('ifUnchecked', '.replacement', function(e) {
	$(this).closest('td').find('[name="replacement[]"]').val('No');
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

	$(this).closest('tr').find('.replacement').iCheck('uncheck').attr('disabled', true).iCheck('update');
	$(this).closest('tr').find('.defective').iCheck('uncheck').attr('disabled', true).iCheck('update');
	$(this).closest('tr').find('[name="amount[]"]').text('0.00');
	recomputeAll();
});

$('tbody').on('ifChecked', '.chkitem', function() {
	var n = $(this).closest('tr').find('.issueqty');
	n.removeAttr('readonly', '').val(addComma(n.attr('data-value')));

	$(this).closest('tr').find('.replacement').attr('disabled', false).iCheck('update');
	$(this).closest('tr').find('.defective').attr('disabled', false).iCheck('update');
	recomputeAll();
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
				$('#tableList tbody').html('');
				$('#customer').val(data.header.customer)
				displayDetails(data.details);

				recomputeAll();
				
			}
		});
	}
}

$('form').on('click', '[type="submit"]', function(e) {
	e.preventDefault();
	console.log('wasda');
	var form_element = $(this).closest('form');
	var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();

	//$('#submit_container [type="submit"]').attr('disabled', true);

	form_element.find('.form-group').find('input, textarea, select').trigger('blur');

	if (form_element.find('.form-group.has-error').length == 0) {

		var items = 0;
		$('.issueqty:not([readonly])').each(function() {
			items += removeComma($(this).val());
		});

		if ($('.issueqty:not([readonly])').length > 0 && items > 0) {

			$.each($('#tableList tbody tr'), function(){
				if(!$(this).find('.chkitem').is(':checked')){
					$(this).find('input:hidden')	.remove();
					$(this).find('input.issueqty')	.removeAttr('name')
				}
			});

			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data, function(data) {
				if (data.success) {
					$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = data.redirect;						
							}, 1000);
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