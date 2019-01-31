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
									<label for="voucherno" class="control-label col-md-4">Purchase Return No.</label>
									<div class="col-md-8">
										<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
									</div>
								</div>
							<?php else: ?>
								<?php
									echo $ui->formField('text')
										->setLabel('Purchase Return No.')
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
								echo $ui->formField('dropdown')
								->setLabel('Reason ')
								->setPlaceholder('Select Reason')
								->setSplit('col-md-4', 'col-md-8')
								->setName('reason')
								->setId('reason')
								->setList(array('1' => 'Defective goods - no replacement', '2' => 'Defective goods - for replacement', '3' => 'Not Defective - Item For Replacement (exact item)', '4' => 'Not defective - Item For Replacement (different item)', '5' => 'Not Defective - Items Returned but not to be replaced"'))
								->setValue($reason)
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
									->setLabel('Receipt No. ')
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
							<?php
								echo $ui->formField('hidden')
									->setName('task')
									->setId('task')
									->setClass('task')
									->setValue($ajax_task)
									->draw('false');
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
						<th class="col-xs-1 text-right">Receipt Qty</th>
						<th class="col-xs-1 text-right">Qty Left</th>
						<?php endif ?>
						<th class="col-xs-<?php echo ($show_input) ? '1' : '2' ?> text-right">Qty</th>
						<th class="col-xs-1 text-center">UOM</th>
						<th class="col-xs-1 text-center">Tax</th>
						<th class="col-xs-2 text-right">Amount</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
				<tfoot class="summary text-right" <?php echo ($ajax_task == 'ajax_create') ? 'style="display: none"' : '' ?>>
					<tr>
						<td colspan="<?php echo ($show_input) ? '9' : '6' ?>"><label class="control-label">Total Purchases</label></td>
						<td style="border-top:1px solid #DDDDDD;"></td>
						<td colspan="2" style="border-top:1px solid #DDDDDD;">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('header_purchase')
										->setClass('total_purchase')
										->setValue(((empty($amount)) ? '0.00' : $amount))
										->addHidden()
										->draw($show_input);
							?>
						</td>
					</tr>
					<tr>
						<td colspan="<?php echo ($show_input) ? '9' : '6' ?>"><label class="control-label">Total Purchases Tax</label></td>
						<td></td>
						<td colspan="2">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('header_purchase_tax')
										->setClass('total_purchase_tax')
										->setValue(((empty($taxamount)) ? '0.00' : $taxamount))
										->addHidden()
										->draw($show_input);
							?>
						</td>
					</tr>
					<tr>
						<td colspan="<?php echo ($show_input) ? '9' : '6' ?>"><label class="control-label">Total Amount</label></td>
						<td style="border-top:1px solid #DDDDDD;"></td>
						<td colspan="2" style="border-top:1px solid #DDDDDD;">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('header_amount')
										->setClass('total_amount')
										->setValue(((empty($amount)) ? '0.00' : $amount + $taxamount))
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
						if ($stat == 'Returned'  && $restrict_ret || empty($stat)) {
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
<div id="receipt_list_modal" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Purchase Receipt List</h4>
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
			<table id="receipt_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
				<thead>
					<tr class="info">
						<th class="col-xs-3">PO No.</th>
						<th class="col-xs-3">PR No.</th>
						<th class="col-xs-3">Transaction Date</th>
						<th class="col-xs-4">Notes</th>
						<th class="col-xs-2">Amount</th>
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

<div class="modal fade" id="serialModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" id = "modal_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Items</h4>
				<h5 class="modal-title">Item Code: <input type = "text" id = "sec_itemcode" style = "width:350px"></h5>
				<h5 class="modal-title">Description: <input type = "text" id = "sec_description" style = "width:350px"></h5>
				<input type = "hidden" id  = "checkcount">
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="input-group">
							<input id="sec_search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-addon">
								<i class="fa fa-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="tableSerialList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
					<thead>
						<tr class="info">
							<!-- <th class="col-xs-2"><input type = "checkbox" class = "checkall"></th> -->
							<th class="col-xs-2"></th>
							<th id = "serial_header">Serial No.</th>
							<th id = "engine_header">Engine No.</th>
							<th id = "chassis_header">Chassis No.</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				<div id="serial_pagination"></div>
			</div>
			<div class="modal-footer">
				<div class="col-md-12 col-sm-12 col-xs-12 text-center">
					<div class="btn-group">
						<?php if($show_input) { ?>
							<button id = "btn_tag" type = "button" class = "btn btn-primary btn-sm btn-flat">Tag</button>
						<?php } ?>
					</div>
					&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<button id = "btn_close" type="button" class="btn btn-default btn-sm btn-flat">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var delete_row	= {};
var ajax		= {};
var ajax_call	= '';
var min_row		= 0;
var serialize = [{
						'itemcode': ''
					}];
function addVoucherDetails(details, index) {
	var details = details || {itemcode: '', detailparticular: '', receiptqty: ''};
	var other_details = JSON.parse(JSON.stringify(details));
	// console.log(details);
	delete other_details.itemcode;
	delete other_details.detailparticular;
	delete other_details.receiptqty;
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
		<tr data-index="`+index+`">
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
				<?php
					echo $ui->formField('hidden')
						->setName('serialnumbers[]')
						->setClass('serialnumbers')
						->setID('serialnumbers`+index+`')
						->setValue('')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('enginenumbers[]')
						->setClass('enginenumbers')
						->setID('enginenumbers`+index+`')
						->setValue('')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('chassisnumbers[]')
						->setClass('chassisnumbers')
						->setID('chassisnumbers`+index+`')
						->setValue('')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('h_itemcode[]')
						->setClass('h_itemcode')
						->setValue('` + details.itemcode + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('parentcode[]')
						->setClass('parentcode')
						->setValue('` + details.parentcode + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('h_detailparticular[]')
						->setClass('h_detailparticular')
						->setValue('` + details.detailparticular + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('bundle_itemqty[]')
						->setClass('bundle_itemqty')
						->setValue('` + details.bundle_itemqty + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('parentline[]')
						->setClass('parentline')
						->setValue('` + details.parentline + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('item_ident_flag[]')
						->setClass('item_ident_flag')
						->setValue('` + details.item_ident_flag + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('linenumber[]')
						->setClass('linenumber')
						->setValue('` + details.linenum + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('quantityleft[]')
						->setClass('quantityleft')
						->setValue('` + details.qtyleft + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setClass('available_qty')
						->setValue('` + details.available + `')
						->draw('false');
				?>
				<?php
					echo $ui->formField('hidden')
						->setClass('checked')
						->setValue('')
						->draw('false');
				?>
			</td>
			<td>
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('detailparticular[]')
						->setClass('description')
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
						->setClass('realqty')
						->setValue('` + addComma(details.realqty) + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setClass('maxqty')
						->setValue('` + addComma(details.maxqty) + `')
						->addHidden()
						->draw($show_input);
				?>
			</td>
			<?php endif ?>
			<td class="text-right">
				<button type="button" id="serial_`+ details.linenum +`" data-itemcode="`+details.itemcode+`" data-item="`+details.detailparticular+`" data-item-ident-flag="`+details.item_ident_flag+`" data-linenum="`+details.linenum+`" class="serialize_button btn btn-block btn-success btn-flat hidden">
					<em class="text-center"><small>Select Items (<span class="receiptqty_serialized_display"><?php if ($show_input == '' || $ajax_task == "ajax_edit") { ?>` + (addComma(details.receiptqty, 0) || 0) + `<?php } else { ?>0<?php }?></span>)</small></em>
				</button>
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('receiptqty[]')
						->setClass('receiptqty text-right')
						->setID('receiptqty`+ details.linenum +`')
						->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.receiptqty) || 0) + `'))
						->setValidation('required integer')
						->setValue('` + (addComma(details.receiptqty, 0) || 0) + `')
						->draw($show_input);
				?>
				` + otherdetails + `
			</td>
			<td>
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setValue('` + details.receiptuom.toUpperCase() + `')
						->draw(false);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12 text-center')
						->setName('detail_taxcode[]')
						->setClass('taxcode')
						->setValue('` + ((details.taxcode == "VATG") ? "VAT Goods 12%" : 
										((details.taxcode == "VATS") ? "VAT Services 12%" : 
										((details.taxcode == "Ptax") ? "Percentage Tax" : "None"))) + `')
						->addHidden()
						->draw($show_input);
				?>
				<?php
					echo $ui->formField('hidden')
						->setName('detail_taxrate[]')
						->setClass('taxrate')
						->setValue('` + parseFloat(details.taxrate) + `')
						->draw($show_input);
				?>
			</td>
			<td class="text-right">
				<?php
					echo $ui->formField('text')
						->setSplit('', 'col-md-12')
						->setName('detail_amount[]')
						->setClass('amount')
						->setValue('` + addComma(parseFloat(details.receiptqty) * (parseFloat(details.unitprice)).toFixed(2)) + `')
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
	$('#tableList tbody').find('tr:last .receiptqty').each(function() {
		if (details.receiptqty > 0) {
			$(this).removeAttr('readonly').val($(this).attr('data-value'));
			$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
		} else {
			$('#tableList tbody').find('tr:last .receiptqty').attr('readonly', '').val(0);
			$('#tableList tbody').find('tr:last .serialize_button').prop('disabled', true);
			$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
		}
	});

	if (details.item_ident_flag == 0 || details.item_ident_flag == null) {		
		$('#serial_' + details.linenum).addClass('hidden');
		$('#receiptqty' + details.linenum).removeClass('hidden receiptqty_serialized');
	} else {
		<?php if($ajax_task == "ajax_create") { ?>
		$('#receiptqty' + details.linenum).addClass('hidden receiptqty_serialized').attr('data-value',0);
		<?php } else { ?>
		$('#receiptqty' + details.linenum).addClass('hidden receiptqty_serialized')
		<?php } ?>
		$('#serial_' + details.linenum).removeClass('hidden');
		
		var item = {};
		item = {itemcode : details.itemcode,
				numbers : []};
		serialize[index] = item;

		// DECLARE SERIAL NUMBERS BASED ON QUANTITY
		var prop = {};
		for(var i = 1; i <= details.maxqty; i++){
			prop = {serialno: '',
						engineno: '',
						chassisno: ''
						};
			serialize[index].numbers.push(prop);
		};		
	}

}

var itemselected = [];
var allserials = [];
var checked_serials = [];
var linenum = '';
var serials = '';
var itemrow = '';
var index = '';
var task = '';
var type = '';
var quantityleft = '';
var item_ident = '';
var serialnumbers = [];
var enginenumbers = [];
var chassisnumbers = [];
$('#tableList tbody').on('click', '.serialize_button', function(){
	linenum = $(this).data('linenum');
	itemcode = $(this).data('itemcode');
	itemrow = $(this);
	index = $(this).closest('tr').attr('data-index');
	// serials = $(this).closest('tr').find('.serialnumbers').val();;
	quantityleft = $(this).closest('tr').find('.quantityleft').val();
	item_ident = $(this).closest('tr').find('.item_ident_flag').val();
	description = $(this).closest('tr').find('.description').val();
	check_num = $(this).closest('tr').find('.maxqty').val();
	// if ($(this).hasClass('mainitem')) {
	// 	type = 'mainitem';
	// }
	// else {
	// 	type = 'itempart';
	// }
	serialnumbers = [];
	enginenumbers = [];
	chassisnumbers = [];
	tagSerial(itemcode, description, serials, check_num, type, quantityleft, item_ident);	
	// $('#serialModal').modal('show');
});

function tagSerial(itemcode, description, serials, check_num, type, quantityleft, item_ident) {
	$('#serialModal').modal('show');
	$('#serialModal #checkcount').val(check_num);
	$("#serialModal #sec_itemcode").val(itemcode).prop('disabled', 'disabled').css('border', 'white').css('background', 'white');
	$("#serialModal #sec_description").val(description).prop('disabled', 'disabled').css('border', 'white').css('background', 'white');
	if (item_ident == '100') {
		$('#serial_header').show().addClass('col-xs-10');
		$('#engine_header').hide();
		$('#chassis_header').hide();
	}
	else if (item_ident == '010') {
		$('#serial_header').hide();
		$('#engine_header').show().addClass('col-xs-10');
		$('#chassis_header').hide();
	}
	else if (item_ident == '001') {
		$('#serial_header').hide();
		$('#engine_header').hide();
		$('#chassis_header').addClass('col-xs-10').show();
	}
	else if (item_ident == '110') {
		$('#serial_header').show().addClass('col-xs-5');
		$('#engine_header').show().addClass('col-xs-5');
		$('#chassis_header').hide();
	}
	else if (item_ident == '101') {
		$('#serial_header').show().addClass('col-xs-5');
		$('#engine_header').hide();
		$('#chassis_header').show().addClass('col-xs-5');
	}
	else if (item_ident == '011') {
		$('#serial_header').hide();
		$('#engine_header').show().addClass('col-xs-5');
		$('#chassis_header').show().addClass('col-xs-5');
	}
	else if (item_ident == '111') {
		$('#serial_header').show().addClass('col-xs-3');
		$('#engine_header').show().addClass('col-xs-3');
		$('#chassis_header').show().addClass('col-xs-4');
	}	
}

function getSerialList() {
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax.limit = 5;
	ajax.itemselected = serials;
	ajax.allserials = $('#main_serial').val();
	// ajax.id = itemrow.closest('tr').find('.serialnumbers').val();
	ajax.item_ident = itemrow.closest('tr').find('.item_ident_flag').val();
	var checked = itemrow.closest('tr').find('.checked').val();
	ajax.checked_serials = checked.toString();
	// console.log(ajax.checked_serials);
	task = $('#task').val();
	ajax.task = $('#task').val();
	// if (task=='ajax_edit') {
		var linenumber = itemrow.closest('tr').find('.linenumber').val();
		ajax.linenumber = linenumber;
		ajax.voucherno = $('#source_no').val();//$('#voucher').val();
	// }
	if (task=='') {
		ajax.voucherno = $('#source_no').text();//$('#voucher').val();
	}
	ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_serial_list', ajax, function(data) {
		$('#tableSerialList tbody').html(data.table);
		$('#serial_pagination').html(data.pagination);
		$('#tableSerialList tbody tr td input[type="checkbox"]').each(function() {
			if(jQuery.inArray($(this).val(), checked_serials) != -1) {
				$(this).closest('tr').iCheck('check');
			}
		});
		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			getSerialList();
		}
	});
}

$('#serial_pagination').on('click', 'a', function(e) {
	e.preventDefault();
	var li = $(this).closest('li');
	$('#tableSerialList tbody tr td input[type="checkbox"]:checked').each(function() {
		var serialnum = $(this).val();
		if($.inArray(serialnum, checked_serials) == -1) {
			checked_serials.push(serialnum);
		}
	});	
	if (li.not('.active').length && li.not('.disabled').length) {
		ajax.page = $(this).attr('data-page');
		getSerialList();
	}
});

$('#serialModal #sec_search').on('input', function() {
	ajax.page = 1;
	ajax.search = $(this).val();
	itemcode = $('#sec_itemcode').val();
	ajax.itemcode = itemcode;
	getSerialList();
});

$("#serialModal").on('shown.bs.modal', function () {
	itemcode = $('#sec_itemcode').val();
	ajax.itemcode = itemcode;
	getSerialList();
});

$('#serialModal #btn_close').on('click', function() {
	$('#serialModal').modal('hide');
});

$('#btn_tag').on('click', function() {
	itemselected = [];
	allserials = [];
	var checkcount = $('#checkcount').val();
	qtyleft =  removeComma(quantityleft);
	$('#tableSerialList tbody tr input[type="checkbox"]:checked').each(function() {
		var serialed = $(this).val();
		var sn = $(this).closest('tr').find('.serialno').text();
		var en = $(this).closest('tr').find('.engineno').text();
		var cn = $(this).closest('tr').find('.chassisno').text();
		if($.inArray(serialed, checked_serials) == -1) {
			checked_serials.push(serialed);
		}
		if($.inArray(sn, serialnumbers) == -1 && sn != ""){
			serialnumbers.push(sn);
		}
		if($.inArray(en, enginenumbers) == -1 && en != ""){
			enginenumbers.push(en);
		}
		if($.inArray(cn, chassisnumbers) == -1 && cn != ""){
			chassisnumbers.push(cn);
		}
		// itemrow.closest('tr').find('.serialnumbers').val(checked_serials.toString());
	});
	// if(!serialnumbers.length && !enginenumbers.length && !chassisnumbers.length) {
	// 	var count = 0;
	// } else {
		var count = Math.max(serialnumbers.length,Math.max(enginenumbers.length,chassisnumbers.length));
	// }
	$('#tableList tbody tr .serialnumbers').each(function() {
		var serials = $(this).val();
		if (serials != '') {
			allserials.push(serials);
			$('#main_serial').val(allserials);
		}
	
	});	
	// if (count != checkcount && type =='itempart') {
	// 	$('#warning_counter .modal-body').html('Selected serial numbers must be equal to the required value.')
	// 	$('#warning_counter').modal('show');
	// 	$('#modal_close').hide();
	// 	$('#btn_close').hide();
	// }
	// else 
	
	// if (count > qtyleft) {
	// 	$('#warning_counter .modal-body').html('Selected serial numbers must not be more than the quantity left.')
	// 	$('#warning_counter').modal('show');
	// 	$('#modal_close').hide();
	// 	// $('#btn_close').hide();
	// }
	// else
	if (count == 0) {
		$('#warning_counter .modal-body').html('There is no selected serial number.')
		$('#warning_counter').modal('show');
		$('#modal_close').hide();
		// $('#btn_close').hide();
	} else {
	// if (type == 'mainitem') {
		if (serialnumbers.length)
		$('#serialnumbers'+index).val(serialnumbers.toString());
		if (enginenumbers.length)
		$('#enginenumbers'+index).val(enginenumbers.toString());
		if (chassisnumbers.length)
		$('#chassisnumbers'+index).val(chassisnumbers.toString());
		// console.log(count);
		// console.log(serialnumbers);
		// console.log(enginenumbers);
		// console.log(chassisnumbers);
	}
	itemrow.closest('tr').find('.receiptqty').val(count);
	itemrow.closest('tr').find('.receiptqty_serialized_display').text(count);
	$('#serialModal').modal('hide');	
	$('#modal_close').show();
	$('#btn_close').show();
	// }
	recomputeAll();
});

$('#tableSerialList').on('ifChecked', '.check_id', function () {
	var serialnum = $(this).val();
	var sn = $(this).closest('tr').find('.serialno').text();
	var en = $(this).closest('tr').find('.engineno').text();
	var cn = $(this).closest('tr').find('.chassisno').text();
	if($.inArray(serialnum, checked_serials) == -1) {
		checked_serials.push(serialnum);
	}
	if($.inArray(sn, serialnumbers) == -1 && sn != ""){
		serialnumbers.push(sn);
	}
	if($.inArray(en, enginenumbers) == -1 && en != ""){
		enginenumbers.push(en);
	}
	if($.inArray(cn, chassisnumbers) == -1 && cn != ""){
		chassisnumbers.push(cn);
	}
	// itemrow.closest('tr').find('.serialnumbers').val(checked_serials);
	// console.log(serialnumbers);
});

$('#tableSerialList').on('ifUnchecked', '.check_id', function () {
	var remove_this  =   $(this).val();
	var remove_sn = $(this).closest('tr').find('.serialno').text();
	var remove_en = $(this).closest('tr').find('.engineno').text();
	var remove_cn = $(this).closest('tr').find('.chassisno').text();
	checked_serials = jQuery.grep(checked_serials, function(value) {
		return value != remove_this;
	});
	serialnumbers = jQuery.grep(serialnumbers, function(value) {
		return value != remove_sn;
	});
	enginenumbers = jQuery.grep(enginenumbers, function(value) {
		return value != remove_en;
	});
	chassisnumbers = jQuery.grep(chassisnumbers, function(value) {
		return value != remove_cn;
	});
	// itemrow.closest('tr').find('.serialnumbers').val(checked_serials);
	// console.log(serialnumbers);
});

$('#tableSerialList').on('ifToggled', 'input[type=checkbox]:not(.checkall)', function() {
	var b = $('#tableSerialList input[type=checkbox]:not(.checkall)');
	var row = $('#tableSerialList >tbody >tr').length;
	var c =  b.filter(':checked').length;
	if(c == row){
		$('#tableSerialList thead tr th').find('.checkall').prop('checked', true).iCheck('update');
	}
	else{
		$('#tableSerialList thead tr th').find('.checkall').prop('checked', false).iCheck('update');
	}
});

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
				<td colspan="10" class="text-center"><b>Select Purchase Receipt No.</b></td>
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
		var total_purchase = 0;
		var total_purchase_tax = 0;
		var total_amount = 0;
		$('#tableList tbody tr').each(function() {
			var price = removeComma($(this).find('.unitprice').val());
			var quantity = removeComma($(this).find('.receiptqty').val());
			var taxrate = removeComma($(this).find('.taxrate').val());
			var purchase = (price * quantity);
			var tax = (price * quantity * taxrate);
			var amount = (price * quantity * (1+taxrate));
			total_purchase += purchase;
			total_purchase_tax += tax;
			total_amount += amount;
			$(this).find('.amount').val(addComma(amount)).closest('.form-group').find('.form-control-static').html(addComma(amount));
		});
		$('#tableList tfoot .total_purchase').val(total_purchase).closest('.form-group').find('.form-control-static').html(addComma(total_purchase));
		$('#tableList tfoot .total_purchase_tax').val(total_purchase_tax).closest('.form-group').find('.form-control-static').html(addComma(total_purchase_tax));
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
$('#tableList tbody').on('blur recompute', '.receiptqty', function(e) {
	recomputeAll();
});
<?php // if ($ajax_task == 'ajax_create'): ?>
$('#source_no').on('focus', function() {
	var vendor = $('#vendor').val();
	ajax.vendor = vendor;
	if (vendor == '') {
		$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Supplier');
		$('#vendor').trigger('blur');
	} else {
		$('#receipt_tableList tbody').html(`<tr>
			<td colspan="4" class="text-center">Loading Items</td>
		</tr>`);
		$('#pagination').html('');
		getList();
	}
});
function getList() {
	ajax.limit = 5;
	$('#receipt_list_modal').modal('show');
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_receipt_list', ajax, function(data) {
		$('#receipt_tableList tbody').html(data.table);
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
$('#vendor').on('change', function() {
	ajax.vendor = $(this).val();
	ajax.vendor = vendor;
	$('#source_no').val('');
	$('#tableList tbody').html(`
		<tr>
			<td colspan="10" class="text-center"><b>Select Purchase Receipt No.</b></td>
		</tr>
	`);
});
$('tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
	$(this).closest('tr').find('.receiptqty').attr('readonly', '').val(0);
	$(this).closest('tr').find('.serialize_button').prop("disabled",true);
});
$('tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
	var n = $(this).closest('tr').find('.receiptqty');
	var m = $(this).closest('tr').find('.serialize_button');
	n.removeAttr('readonly', '').val(addComma(n.attr('data-value')));
	m.prop("disabled",false);
});
$('#receipt_tableList').on('click', 'tr[data-id]', function() {
	var so = $(this).attr('data-id');
	var si = $(this).attr('data-si');
	$('#source_no').val(so).trigger('blur');
	$('#remarks').val('Supplier Invoice #: ' + si + '.');
	$('#receipt_list_modal').modal('hide');
	loadPurchaseDetails();
});
function loadPurchaseDetails() {
	var voucherno = $('#source_no').val();
	if (voucherno) {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_receipt_details', { voucherno: voucherno }, function(data) {
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
		$('.receiptqty:not([readonly])').each(function() {
			items += removeComma($(this).val());
		});
		if ($('.receiptqty:not([readonly])').length > 0 && items > 0) {
			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data, function(data) {
				console.log(data.redirect);
				if (data.success) {console.log(data.redirect);
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