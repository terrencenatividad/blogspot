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
											<label for="voucherno" class="control-label col-md-4">JO No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('JO No.')
												->setSplit('col-md-4', 'col-md-8')
												->setName('job_order_no')
												->setId('job_order_no')
												->setValue($job_order_no)
												->addHidden($job_order_no)
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
									<input type="hidden" name="h_transactiondate" value="<?php echo $transactiondate ?>">
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
							<div class="row familyislove">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Service Quotation No. ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('service_quotation')
											->setId('service_quotation')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($service_quotation)
											->setValidation('required')
											->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Customer PO No.')
											->setSplit('col-md-4', 'col-md-8')
											->setName('po_number')
											->setId('po_number')
											->setValue($po_number)
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
								<th class="col-md-3">Item</th>
								<th class="col-md-3">Description</th>
								<th class="col-md-2">Warehouse</th>
								<th class="col-md-1">Order Quantity</th>
								<th class="col-md-1">Quantity</th>
								<th class="col-md-1">UOM</th>
								<?php if ($show_input): ?>
								<th class="col-md-1"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot class="summary">
							<tr>
								<td colspan="6">
									<?php if ($show_input): ?>
										<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
									<?php endif ?>
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
								if ($stat == 'Prepared' && $restrict_dr || empty($stat)) {
									// echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
								}
							?>
							<?php if(!$show_input):?><a class="btn btn-flat btn-success" id="isyu">Issue</a><?endif;?>
							<?php
								// echo '&nbsp;&nbsp;&nbsp;';
								echo $ui->drawCancel();
							?>
						</div>
					</div>
				</div>
			</form>
		</div>	
		<div class="box box-warning">
			<div class="box-body">
				<div class="row">
					<div class="col-md-11">
						<h3>Issued Parts:</h3>
					</div>
				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="issuedPartsList" class="table table-hover table-condensed table-sidepad only-checkbox full-form">
					<thead>
						<tr class="info">
							<th class="col-md-3">Item</th>
							<th class="col-md-3">Description</th>
							<th class="col-md-2">Warehouse</th>
							<th class="col-md-2">Quantity</th>
							<th class="col-md-1">UOM</th>
							<th colspan="2" class="col-md-1 text-center">Action	</th>
							<?php if ($show_input): ?>
							<th class="col-md-1"></th>
							<?php endif ?>
						</tr>
					</thead>
					<tbody>
					
					</tbody>
					<tfoot class="summary">
						<tr>
							<td colspan="6" class="text-center">
							</td>
						</tr>
					</tfoot>
				</table>
				<div id="header_values"></div>
			</div>
		</div>
		<!-- <div class="text-center">
			<?php
				// echo '<button type="button" class="btn btn-primary" id="btnSave">Save</button>';
				// echo $ui->drawCancel();
			?>
		</div> -->
	</section>
	<div class="modal fade" id="serialModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" id = "modal_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Items</h4>
					<h5 class="modal-title">Item Code: <input type = "text" id = "sec_itemcode" val="123"></h5>
					<h5 class="modal-title">Description: <input type = "text" id = "sec_description"></h5>
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
								<th class="col-xs-2"></th>
								<th class="col-xs-3">Serial No.</th>
								<th class="col-xs-3">Engine No.</th>
								<th class="col-xs-4">Chassis No.</th>
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
							<button id = "btn_tag" type = "button" class = "btn btn-primary btn-sm btn-flat">Tag</button>
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
	<div id="sec_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Items</h4>
			<h5 class="modal-title">Item Code: SERVICE001</h5>
			<h5 class="modal-title">Description: L3608</h5>
			</div>
			<div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-md-offset-8">
				<div class="input-group">
					<input id="ar_search" class="form-control pull-right" placeholder="Search" type="text">
					<div class="input-group-addon">
					<i class="fa fa-search"></i>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="modal-body no-padding">
			<table id="" class="table table-hover table-clickable table-sidepad no-margin-bottom">
				<thead>
				<tr class="info">
					<th class="col-xs-2"></th>
					<th class="col-xs-10">Serial No.</th>
				</tr>
				<tr>
					<td><?php
						echo $ui->loadElement('check_task')
							->addCheckbox()
							->setValue('')
							->draw(false);
					?>
					</td>
					<td>SERIAL000001</td>
				</tr>
				<tr>
					<td><?php
						echo $ui->loadElement('check_task')
							->addCheckbox()
							->setValue('')
							->draw(false);
					?>
					</td>
					<td>SERIAL000002</td>
				</tr>
				<tr>
					<td><?php
						echo $ui->loadElement('check_task')
							->addCheckbox()
							->setValue('')
							->draw(false);
					?>
					</td>
					<td>SERIAL000003</td>
				</tr>
				</thead>
				<tbody>
				
				</tbody>
			</table>
			</div>
			<div class="modal-footer">
			<div class="col-md-12 col-sm-12 col-xs-12 text-center">
				<div class="btn-group">
				<button type = "button" class = "btn btn-primary btn-sm btn-flat">Tag</button>
				</div>
				&nbsp;&nbsp;&nbsp;
				<div class="btn-group">
				<button type="button" class="btn btn-default btn-sm btn-flat">Cancel</button>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
	<div class="modal fade" id="warning_counter" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Oooops!</h4>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<div class="btn-group">
							<button id = "btn_ok" type = "button" class = "btn btn-default btn-sm btn-flat">Ok</button>
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
		var min_row		= 1;
		function addVoucherDetails(details, index) {
			var details = details || {itemcode: '', detailparticular: '', warehouse: '', quantity: '0', uom: 'PCS', childqty : '0', linenum : '0', isbundle : 'No', parentline : '', parentcode : '', item_ident_flag : '0'};
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.warehouse;
			delete other_details.quantity;
			delete other_details.childqty;
			delete other_details.linenum;
			delete other_details.isbundle;
			delete other_details.parentline;
			delete other_details.parentcode;
			delete other_details.item_ident_flag;
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
			var linenum = (details.linenum != 0) ? details.linenum : index + 1; 	
			var row = ``;
			if(details.parentcode == ""){
				var asd = 'mainitem parents'+linenum;
			}else{
				var asd = 'itempart subitem'+details.parentline;
			}
			if(details.isbundle == "yes"){
				var dsa = 'data-isbundle="1"';
			}else{
				var dsa = 'data-isbundle="0"';
			}
			row += `
				<tr class="`+asd+`" ` + dsa +` data-value = "`+details.bomqty+`" data-linenum="`+linenum+`">`;
			row += `<td>
						<?php
							echo $ui->formField('hidden')
								->setName('serialnumbers[]')
								->setClass('serialnumbers')
								->setValue('')
								->draw(true);
						?>
						<?php
							$value = "<span id='temp_view_itemcode_` + index + `'>` + details.itemcode + `</span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_itemcode[]')
								->setClass('itemcode')
								->setList($item_list)
								->setValue($value)
								->draw($show_input);
						 	echo $ui->formField('hidden')
								->setName('h_itemcode[]')
								->setClass('h_itemcode')
								->setValue('` + details.itemcode + `')
								->draw(true);
							echo $ui->formField('hidden')
								->setName('linenum[]')
								->setClass('linenum')
								->setValue('` + details.linenum + `')
								->draw(true);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('detailparticular')
								->setValue('` + details.detailparticular + `')
								->draw($show_input);
							echo $ui->formField('hidden')
								->setSplit('', 'col-md-12')
								->setName('h_detailparticular[]')
								->setClass('h_detailparticular')
								->setValue('` + details.detailparticular + `')
								->draw(true);
						?>
					</td>
					<td>
						<?php
							$value = "<span id='temp_view_warehouse_` + details.warehouse + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_warehouse[]')
								->setClass('warehouse')
								->setList($warehouse_list)
								->setValue('` + details.warehouse + `')
								->draw($show_input);
								echo $ui->formField('hidden')
								->setSplit('', 'col-md-12')
								->setName('h_warehouse[]')
								->setClass('h_warehouse')
								->setValue('` + details.warehouse + `')
								->draw(true);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('orderqty[]')
								->setClass('orderqty text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.bomqty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.quantity, 0) || 0) + `')
								->draw($show_input);

							echo $ui->formField('hidden')
								->setSplit('', 'col-md-12')
								->setName('h_orderqty[]')
								->setClass('h_orderqty text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.bomqty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(details.quantity, 0) || 0) + `')
								->draw(true);
						?>
						` + otherdetails + `
					</td>`;
					if(details.parentcode != ''){
						if(details.item_ident_flag == 0){
						row +=`
						<td class="text-right">
							<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('quantity[]')
									->setClass('quantity text-right')
									->setAttribute(array('readonly'=>'readonly','data-value' => '` + (parseFloat(details.quantity) || 0) + `'))
									->setValidation('required integer')
									->setValue('` + (addComma(details.quantity, 0) || 0) + `')
									->draw(true);
							?>
							` + otherdetails + `
						</td>`;
					} else {
						row += `<td class="text-right qty_col"><input type = "button" class = "btn btn-md btn-success serialbtn btn-flat col-md-12 text-right itempart quantity partbtn" data-value = "` + (parseFloat(details.quantity) || 0) + `" value = "` + (parseFloat(details.quantity) || 0) + `">` + otherdetails + `<input type = "hidden" class = "quantity serialbtn" name = "quantity[]" data-value = "` + (parseFloat(details.quantity) || 0) + `" value = "` + (parseFloat(details.quantity) || 0) + `"/></td>`;
					} 
					}else{
						if(details.item_ident_flag == 0){
						row +=`
						<td class="text-right">
							<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('quantity[]')
									->setClass('quantity text-right')
									->setAttribute(array('data-value' => '` + (parseFloat(details.quantity) || 0) + `'))
									->setValidation('required integer')
									->setValue('` + (addComma(details.quantity, 0) || 0) + `')
									->draw(true);
							?>
							` + otherdetails + `
						</td>`;
					} else {
						row += `<td class="text-right qty_col"><input type = "button" class = "btn btn-md btn-success btn-flat col-md-12 text-right serialbtn itempart quantity partbtn" data-value = "` + (parseFloat(details.quantity) || 0) + `" value = "` + (parseFloat(details.quantity) || 0) + `">` + otherdetails + `<input type = "hidden" class = "quantity serialbtn" name = "quantity[]" data-value = "` + (parseFloat(details.quantity) || 0) + `" value = "` + (parseFloat(details.quantity) || 0) + `"/></td>`;
					}
					}
					
					row += `
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw(false);
							echo $ui->formField('hidden')
								->setName('h_uom[]')
								->setClass('h_uom')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw(true);
						?>
					</td>
					<?php if ($show_input): ?>
					<td class="text-right">
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
				$('#issuedPartsList tbody').find('tr:last .itemcode').val(details.itemcode);
			}
			if (details.warehouse != '') {
				$('#tableList tbody').find('tr:last .warehouse').val(details.warehouse);
				$('#issuedPartsList tbody').find('tr:last .warehouse').val(details.warehouse);
			}
			if (details.taxcode != '') {
				$('#tableList tbody').find('tr:last .taxcode').val(details.taxcode);
				$('#issuedPartsList tbody').find('tr:last .taxcode').val(details.taxcode);
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

			var row2 = "<tr><td colspan='6' class='text-center'>No Records Found</td></tr>";
			$('#issuedPartsList tbody').append(row2);

			} else if (min_row == 0) {
				$('#tableList tbody').append(`
					<tr>
						<td colspan="9" class="text-center"><b>Select Service Quotation No.</b></td>
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

		$('#isyu').on('click',function(e){
			var form = $('form').serialize();
			$.post('<?=MODULE_URL?>ajax/ajax_create_issue', form + '<?=$ajax_post?>' , function(data) {
					getList();				
			});
		});

		$('#tableList tbody').on('blur', '.quantity', function(e) {
			var value 	=	removeComma($(this).val());
			var orderqty 	=	removeComma($(this).closest('tr').find('.h_orderqty').val());
			if ($(this).closest('tr').hasClass('items')) {
				if (value < 1) 
					$(this).parent().parent().addClass('has-error');
				else
					$(this).parent().parent().removeClass('has-error');
			}
			if(value > orderqty){
				$(this).val(orderqty);
				value = orderqty;
			}
			if ($(this).closest('tr').data('isbundle') == 1) {
				var linenum = $(this).closest('tr').data('linenum');
				$.each($('.subitem'+linenum), function(){
					var subitemqty 	= $(this).closest('tr').data('value');
					console.log(value);
					subitemqty 		= subitemqty * value;
					$(this).find('.quantity').val(subitemqty);
				});
			}
	});	

		var itemselected = [];
		var allserials = [];
		var linenum = '';
		var serials = '';
		var itemrow = '';
		var task = '';
		var type = '';
		var quantityleft = '';
		$('#tableList tbody').on('click', '.serialbtn', function() {
			itemrow = $(this);
			linenum = $(this).closest('tr').find('span').attr('id')
			itemcode = $(this).closest('tr').find('.h_itemcode').val();
			description = $(this).closest('tr').find('.h_detailparticular').val();
			serials = $(this).closest('tr').find('.serialnumbers').val();
			quantityleft = $(this).closest('tr').find('.h_orderqty').val();
			check_num = $(this).val();
			if ($(this).hasClass('mainitem')) {
				type = 'mainitem';
			}
			else {
				type = 'itempart';
			}
			tagSerial(itemcode, description, serials, check_num, type, quantityleft);	
		});

		function tagSerial(itemcode, description, serials, check_num, type, quantityleft) {
			$('#serialModal').modal('show');
			$('#serialModal #checkcount').val(check_num);
			$("#serialModal #sec_itemcode").val(itemcode).prop('disabled', 'disabled').css('border', 'white').css('background', 'white');
			$("#serialModal #sec_description").val(description).prop('disabled', 'disabled').css('border', 'white').css('background', 'white');
		}

		function getSerialList() {
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax.itemselected = serials;
			//ajax.linenum = linenum;
			ajax.allserials = $('#main_serial').val();
			ajax.itemcode  = itemcode;
			ajax.id = itemrow.closest('tr').find('.serialnumbers').val();
			task = $('#task').val();
			ajax.task = $('#task').val();
			if (task=='ajax_edit') {
				var linenumber = itemrow.closest('tr').find('.linenum').val();
				ajax.linenumber = linenumber;
				ajax.voucherno = $('#job_order_no').val();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_serial_list', ajax, function(data) {
				$('#tableSerialList tbody').html(data.table);
				$('#serial_pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getSerialList();
				}
			});
		}

		$('#serial_pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
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
			var count = 0;
			var checkcount = $('#checkcount').val();
			qtyleft =  removeComma(quantityleft);
			$('#tableSerialList tbody tr input[type="checkbox"]:checked').each(function() {
				count++;
				var serialed = $(this).val();
				itemselected.push(serialed);
			console.log(quantityleft);		
			// console.log(itemselected);		
				
				itemrow.closest('tr').find('.serialnumbers').val(itemselected);
			});
			$('#tableList tbody tr .serialnumbers').each(function() {
				var serials = $(this).val();
				if (serials != '') {
					allserials.push(serials);
					$('#main_serial').val(allserials);
				}	
			});	
			if (count != checkcount && type =='itempart') {
				$('#warning_counter .modal-body').html('Selected serial numbers must be equal to the required value.')
				$('#warning_counter').modal('show');
				$('#modal_close').hide();
				$('#btn_close').hide();
			}
			else if (count > qtyleft && type == 'mainitem') {
				$('#warning_counter .modal-body').html('Selected serial numbers must not be more than the quantity left.')
				$('#warning_counter').modal('show');
				$('#modal_close').hide();
				$('#btn_close').hide();
			}
			else if (count == 0 && type == 'mainitem') {
				$('#warning_counter .modal-body').html('There is no selected serial number.')
				$('#warning_counter').modal('show');
				$('#modal_close').hide();
				$('#btn_close').hide();
			}
			else {
				if (type == 'mainitem') {
					itemrow.closest('tr').find('.quantity').val(count);
				}
				$('#serialModal').modal('hide');	
				$('#modal_close').show();
				$('#btn_close').show();
			}
		});
		$('#btn_ok').on('click', function() {
			$('#warning_counter').modal('hide');
		});
		$(document).ready(function(){
			getList();
		});
		function getList() {
			var jobno = $('#job_order_no').val();
			$.post('<?=MODULE_URL?>ajax/ajax_load_issue', 'jobno='+ jobno + '<?=$ajax_post?>' , function(data) {
				$('#issuedPartsList tbody').html(data.issuedparts);
			});
		}
		$('#issuedPartsList tbody').on('click','.deleteip', function(){
			var id = $(this).closest('tr').data('id');
			$('#delete_modal').modal('show');
			
			$('#delete_yes').on('click', function(){
				$.post('<?=MODULE_URL?>ajax/ajax_delete_issue', 'id='+ id , function(data) {
				$('#delete_modal').modal('hide');					
			});
			getList();
			});
		});
		$('#issuedPartsList tbody').on('click','.editip', function(){
			$('html, body').animate({
				scrollTop: $(".familyislove").offset().top
			}, 500);
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
		$(body).on('click','.serialqty',function(e){
			$('#sec_modal').modal("show");
		});

		

	</script>
	<?php endif ?>