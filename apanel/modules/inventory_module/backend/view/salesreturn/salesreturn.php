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
										<input type="text" name="voucherno" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
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
								->setValue(($customer)? $customer:'')
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
						<div class="col-md-6">
							<?php
								echo $ui->formField('textarea')
								->setLabel('Reason ')
								->setSplit('col-md-4', 'col-md-8')
								->setName('reason')
								->setId('reason')
								->setMaxLength(100)
								->setValue($reason)
								->setValidation('required')
								// ->addHidden(($ajax_task != 'ajax_create'))
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
						<th class="text-center" style="width: 20px"><input type="checkbox" class="checkallitem" data-target='chkitem'></th>
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
							<input type="hidden" id="total_netamount" name="total_netamount" value="<?=$total_netamount;?>">
							
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
				<div class="col-md-6" id = "filters">
					<div class="input-group">
						<input type = "radio" checked = "true" name = "voucher_type" id = "voucher_type" value = "Delivery Receipt"> Delivery Receipt
						&nbsp;&nbsp;
						<input type = "radio" name = "voucher_type" id = "voucher_type" value = "si"> Sales Invoice
					</div>
				</div>
				<div class="col-md-4 col-md-offset-2">
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
<div id="serial_tableList" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Item Serial List</h4>
		</div>
		<!-- <div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-md-offset-">
					<div class="input-group">
						<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
						<div class="input-group-addon">
							<i class="fa fa-search"></i>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="modal-body no-padding">
			<table id="invoice_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
				<thead>
					<tr class="info">
						<?php if ($show_input) : ?>
						<th class="col-xs-1"><input type="checkbox" class="checkallitem" data-target="chkserial"></th>
						<?php endif; ?>
						<th class="col-xs-5">Item</th>
						<th class="col-xs-2">Serial No.</th>
						<th class="col-xs-2">Chassis No.</th>
						<th class="col-xs-2">Engine No.</th>
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
		<?php if ($show_input) : ?>
		<div class="modal-footer">
			
			<button type='button' class="btn btn-primary btnselectserial">Confirm</button>
			<button type='button' class="btn btn-default btncancel">Cancel</button>
		</div>
		<?php endif; ?>
	</div>
</div>
</div>
<script>
var delete_row	= {};
var ajax		= {};
var ajax_call	= '';
var min_row		= 0;
var show_input 	= '<?=$show_input;?>';
var ajax_task 	= '<?=$ajax_task;?>';
var selected_serial = [];
var header = '<?=$source_no?>';

function addVoucherDetails(details, index) {
	var details 	= details || {itemcode: '', detailparticular: '', issueqty: ''};
	var other_details = JSON.parse(JSON.stringify(details));
	var netamount;
	var issueqty 	= 0;
	var serials 	= details.serialnumbers;

	delete other_details.itemcode;
	delete other_details.detailparticular;
	delete other_details.issueqty;
	delete other_details.warehouse;
	delete other_details.unitprice;
	delete other_details.amount;
	delete other_details.discountrate;
	delete other_details.taxcode;

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
	if (ajax_task == 'ajax_edit') {
		issueqty 	= details.issueqty;
	}
	if (ajax_task == 'view') {
		serials 	= details.selectedserial; 
	}
	if (details.serialnumbers == null || details.serialnumbers.length <= 0) {
		var issueqty_element = '';
		issueqty_element = '<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('issueqty[]')
									->setClass('issueqty text-right')
									->setAttribute(array(
									'data-max' 		=> '\' + (parseFloat(details.maxqty) || 0) + \'',
									'data-value' 	=> '\' + (parseFloat(details.issueqty) || 0) + \'')
									)
									->setValidation('required integer')
									->setValue('\' + addComma(details.issueqty) + \'')
									->draw($show_input);
							?>';

	} else {
		issueqty_element = '<button type="button" class="btn btn-flat btn-success btnserial text-right col-xs-12" data-serialid="'+serials+'" data-maxitem="'+parseFloat(details.maxqty)+'">0</button>';
		issueqty_element += '<input type="hidden" class="allserial" name="allserial[]" value="'+serials+'">';
		issueqty_element += '<input type="hidden" class="issueqty" name="issueqty[]" data-value="'+issueqty+'">';
	}
	if (details.netamount == null) {
		netamount = 0;
	} else{
		netamount = details.netamount;
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
						->setName('txtdefective[]')
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
				` + issueqty_element + `
				<input type='hidden' class='netamount' name='netamount[]' value=`+netamount+`>
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
						->setValue('` + addComma(details.netamount) + `')
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

	if (details.itemcode != '') {
		$('#tableList tbody').find('tr:last .itemcode').val(details.itemcode);
	}
	if (details.warehouse != '') {
		$('#tableList tbody').find('tr:last .warehouse').val(details.warehouse);
	}
	if (details.serialnumbers != '') {
		$('#tableList tbody').find('tr:last .serialnumbers').val('');
	}
	if (details.taxcode != '') {
		$('#tableList tbody').find('tr:last .taxcode').val(details.taxcode);
	}
	if (details.defective == 'Yes') {
		$('#tableList tbody').find('tr:last .defective').iCheck('check').iCheck('update');
		$('#tableList tbody').find('tr:last input[name="defective[]"]').val('Yes');
		$('#tableList tbody').find('tr:last p[name="txtdefective[]"]').html('Yes')
	}
	if (details.replacement == 'Yes') {
		$('#tableList tbody').find('tr:last .replacement').iCheck('check').iCheck('update');
		$('#tableList tbody').find('tr:last input[name="replacement[]"]').val('Yes');
		$('#tableList tbody').find('tr:last p[name="txtreplacement[]"]').html('Yes')
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
		if (details.maxqty > 0) {
			$(this).removeAttr('readonly').val(addComma($(this).attr('data-value')));
			$('#tableList tbody').find('tr:last .chkitem').iCheck('check').iCheck('enable');
		} else {
			$('#tableList tbody').find('tr:last .issueqty').attr('readonly', 'readonly').val(0);
			$('#tableList tbody').find('tr:last .chkitem').iCheck('uncheck').iCheck('disable');
			$('#tableList tbody').find('tr:last .btnserial').attr('disabled', true);
			$('#tableList tbody').find('tr:last .issueqty').attr('readonly', 'readonly').val(0);
			$('#tableList tbody').find('tr:last [name="discountrate[]"]').text('0.00');
			$('#tableList tbody').find('tr:last .replacement').iCheck('uncheck').attr('disabled', true).iCheck('update');
			$('#tableList tbody').find('tr:last .defective').iCheck('uncheck').attr('disabled', true).iCheck('update');
			$('#tableList tbody').find('tr:last [name="amount[]"]').text('0.00');
		}
	});
	$('#tableList tbody').find('tr:last .btnserial').text(selected_serial.length);
}

function checkSelectedSerial(checkbox){
	$.each(checkbox, function(index, value){
		var serialid 	= $(this).data('serialid');


		for(var i=0; i<selected_serial.length; i++){
            if (serialid == selected_serial[i]) 
            {
                $(this).iCheck("check").iCheck('update');
            }
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
			if (ajax_task != 'ajax_create') {
				var serial = details.selectedserial.split(',');
				for (var i = 0; i < serial.length; i++) {
					if (serial[i] != '') {
						selected_serial.push(parseInt(serial[i]));
					}
				}
			}

			addVoucherDetails(details, index);
			
		});
		checkSelectedSerial($('.chkserial'));
		recomputeAll();
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
			var srcqty 			= removeComma($(this).find('.srcqty').val());
			var qty 			= removeComma($(this).find('.issueqty').val());
			var unitprice 		= removeComma($(this).find('.unitprice').val());
			var amount 			= unitprice * qty;
			var discounttype 	= $(this).find('.discounttype').val();
			var discountrate 	= removeComma($(this).find('.discountrate').val());
			var discountamount 	= 0;
			
			discountamount = discountrate * (qty / srcqty);
			
			var netamount 	= amount - discountamount;
			var taxcode 	= $(this).find('.taxcode').val();
			var taxrate 	= removeComma($(this).find('.taxrate').val());
			var taxamount 	= netamount * taxrate;

			$(this).find('[name="amount[]"]').text(addComma(netamount));
			$(this).find('[name="discountrate[]"]').text(addComma(discountamount));
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

function getSerialList(sourceno, linenum, serialid) {
	var voucherno = $('input[name="voucherno"]').val();
	var data = {
				sourceno 	: sourceno, 
				linenum 	: linenum, 
				serials 	: serialid, 
				showinput 	: show_input,
				task 		: ajax_task,
				voucherno 	: voucherno
			};
	$.post('<?=MODULE_URL;?>ajax/getSerialItemList', data, function(data){
		$('#serial_tableList tbody').html(data.table);
		$('button.btnselectserial').attr('data-linenum',data.linenum);

	});
}
$('#tableList').on('click', '.btnserial', function(){
	var sourceno 	= $('#source_no').val();
	var linenum 	= $(this).closest('tr').find('.linenum').val();;
	var serialid 	= $(this).data('serialid');
	$('#serial_tableList').modal('show');


	getSerialList(sourceno, linenum, serialid);
	
});
</script>

<?php if ($ajax_task == 'ajax_create'): ?>
	
<script>
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

$('#source').on('change', function(){
	ajax.page = 1;
	getList();
});

$('#source_no').on('focus', function() {
	$('#invoice_tableList tbody').html(`<tr>
		<td colspan="4" class="text-center">Loading Items</td>
	</tr>`);
	$('#pagination').html('');
	getList();
});

$('#invoice_tableList').on('click', 'tr[data-id]', function() {
	var so = $(this).attr('data-id');
	$('#source_no').val(so).trigger('blur');
	$('#invoice_list_modal').modal('hide');
	loadSalesDetails();
});
$('#filters').on('ifToggled', 'input[name="voucher_type"]:checked', function () {
	getList();
});
</script>

<?php endif;?>

<?php if ($show_input): ?>
<script>
function getList() {
	ajax.limit = 5;
	var data = $('#filters input[name="voucher_type"]:checked').val();
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

function saveSelectedSerial(checkbox){
	$.each(checkbox, function(){
		var serialid 	= $(this).data('serialid');
		var indexof 	= selected_serial.indexOf(serialid);

		if (indexof < 0) {
			if ($(this).is(':checked')) {
				selected_serial.push(serialid);
			}
		} 
		else {
			if (!$(this).is(':checked')) {
				selected_serial.splice(indexof,1);
			}
		}
	});
}


/*
	UPDATE INPUT HIDDEN OF CHECKBOX
*/

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

/*
	END UPDATE INPUT HIDDEN OF CHECKBOX
*/


$('.btnselectserial').on('click', function(){
	saveSelectedSerial($('.chkserial'));

	var linenum = $(this).data('linenum');
	var row 	= $('.linenum[value="'+linenum+'"]').closest('tr');

	row.find('.serialnumbers').val(selected_serial.join(','));
	row.find('.btnserial').text(selected_serial.length);
	row.find('.issueqty').val(selected_serial.length);
	row.find('.issueqty').trigger('blur');

	$('#serial_tableList').modal('hide');
});

$('.btncancel').on('click', function(){
	$('#serial_tableList').modal('hide');
});

$('table').on('ifToggled', 'tr [type="checkbox"].checkallitem', function() {
	var checked 	= $(this).prop('checked');
	var check_type 	= 'ifUnchecked';
	var target 		= $(this).data('target');

	if (checked) {
		check_type = 'ifChecked';
	}
	$(this).closest('table').find('tbody .'+target+':not(:disabled, .disabled)').prop('checked', checked).iCheck('update').trigger(check_type);
});


$('#tableList tbody').on('blur recompute', '.issueqty', function(e) {
	var conversion 		= $(this).closest('tr').find('.conversion').val();
	var issueqty 		= $(this).val();
	var convissueqty 	= issueqty * conversion;
	$(this).closest('tr').find('.convissueqty').val(convissueqty);
	$(this).val(addComma($(this).val()));
	recomputeAll();
});


$('tbody').on('ifUnchecked', '.chkitem', function() {
	selected_serial = [];
	$(this).closest('tr').find('.btnserial').attr('disabled', true);
	$(this).closest('tr').find('.btnserial').text(selected_serial.length);
	$(this).closest('tr').find('.issueqty').attr('readonly', 'readonly').val(0);
	$(this).closest('tr').find('[name="discountrate[]"]').text('0.00');
	$(this).closest('tr').find('.replacement').iCheck('uncheck').attr('disabled', true).iCheck('update');
	$(this).closest('tr').find('.defective').iCheck('uncheck').attr('disabled', true).iCheck('update');
	$(this).closest('tr').find('[name="amount[]"]').text('0.00');
	recomputeAll();
});

$('tbody').on('ifChecked', '.chkitem', function() {
	var n = $(this).closest('tr').find('.issueqty');
	n.removeAttr('readonly', '').val(addComma(n.attr('data-value')));
	$(this).closest('tr').find('.btnserial').attr('disabled', false);

	$(this).closest('tr').find('.replacement').attr('disabled', false).iCheck('update');
	$(this).closest('tr').find('.defective').attr('disabled', false).iCheck('update');
	recomputeAll();
});


$('form').on('click', '[type="submit"]', function(e) {
	e.preventDefault();
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
			$('input').trigger('blur_validate')
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