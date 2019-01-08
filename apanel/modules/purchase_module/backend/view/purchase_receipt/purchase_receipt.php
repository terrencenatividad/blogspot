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
								<th class="col-xs-3">Item</th>
								<th class="col-xs-3">Description</th>
								<th class="col-xs-3">Warehouse</th>
								<th class="col-xs-1 text-right"></th>
								<th class="col-xs-1 text-right">Qty</th>
								<th class="col-xs-2">UOM</th>
								<th class="col-xs-2"></th>
								<th class="col-xs-2 text-right"></th>
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
								<td colspan="7"><label class="control-label hidden">Total Amount</label></td>
								<td colspan="2">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 hidden')
												->setName('amount')
												->setClass('total_amount')
												->setValue(((empty($amount)) ? '0.00' : number_format($amount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
							</tr>
							<tr>
								<td colspan="7"><label class="control-label hidden">Total Purchase Tax</label></td>
								<td colspan="2">
									<?php
										echo $ui->formField('text')
												->setSplit('', 'col-md-12 hidden')
												->setName('taxamount')
												->setClass('total_tax')
												->setValue(((empty($taxamount)) ? '0.00' : number_format($taxamount, 2)))
												->addHidden()
												->draw($show_input);
									?>
								</td>
							</tr>
							<tr>
								<td colspan="7"><label class="control-label hidden">Discount</label></td>
								<td colspan="2">
									<?php if ($show_input): ?>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<div class="col-md-12 hidden">
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
								<td colspan="7"><label class="control-label hidden">Total Amount Due</label></td>
								<td colspan="2">
									<?php
										echo $ui->formField('dropdown')
												->setSplit('', 'col-md-12 hidden')
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

	<div id="serialize_modal" class="modal fade" tabindex="-1" role="dialog" data-item="" data-itemcode="">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php if ($show_input) { ?>
						<h4 class="modal-title">Input Serial Numbers</h4>
					<?php } else { ?>
						<h4 class="modal-title">View Serial Numbers</h4>
					<?php } ?>
				</div>

				<div class="modal-body no-padding">
					<table id="serialize_tableList" class="table table-hover table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-2 text-center">Item No.</th>
								<th class="col-xs-3 text-center">Item Name</th>
								<th class="col-xs-2 text-center">Serial Number</th>
								<th class="col-xs-2 text-center">Engine Number</th>
								<th class="col-xs-2 text-center">Chassis Number</th>
								<th class="col-xs-1 text-center"></th>
							</tr>
						</thead>
						<tbody id="serialize_tbody" data-item-ident-flag="">
							
						</tbody>
						<?php if ($show_input) {?>
						<tfoot class="summary">
							<tr>
								<td colspan="4">
									<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
								</td>	
							</tr>

						</tfoot>
						<?php } ?>
					</table>
				</div>
				<?php if ($show_input) {?>
				<div class="modal-footer text-center">
					<button type="button" class="btn btn-primary save_serials">Save</button>
					<button type="button" class="btn btn-default close_serials" data-dismiss="modal">Close</button>
				</div>
				<?php } ?>
			</div>
		</div>					
	</div>
	<script>
		var delete_row	= {};
		var ajax		= {};
		var ajax_call	= '';
		var min_row		= 0;

		// var serialize = [];
		var serialize = [{
						'itemcode': ''
					}];

		var serialize_item_selected = '';
		var serialize_icode_selected = '';
		var index_selected = 0;
		var item_max_qty = 0;
		
		function addVoucherDetails(details, index) {
			var details = details || {itemcode: '', detailparticular: '', receiptqty: ''};
			var other_details = JSON.parse(JSON.stringify(details));
			// console.log(other_details);
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
								->setSplit('', 'col-md-12 hidden')
								->setClass('unitprice')
								->setName('unitprice[]')
								->setValue('` + addComma(details.unitprice) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<button type="button" id="serial_`+ details.linenum +`" data-itemcode="`+details.itemcode+`" data-item="`+details.detailparticular+`" class="serialize_button btn btn-block btn-success btn-flat">
							<em class="pull-left"><small>Enter serial numbers (<span class="receiptqty_serialized_display"><?php if ($show_input == '' || $ajax_task == "ajax_edit") { ?>` + (addComma(details.receiptqty, 0) || 0) + `<?php } else { ?>0<?php }?></span>)</small></em>
						</button>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('receiptqty[]')
								->setClass('receiptqty text-right')
								->setID('receiptqty`+ details.linenum +`')
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
							$value = "<span id='temp_view_taxrate_` + index + `'>` + details.taxcode + `</span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12 hidden')
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
									->setName('taxamount[]')
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

							echo $ui->setElement('hidden')
									->setName('serial_no_list[]')
									->setID('serial_no`+index+`')
									->setClass('serial_no')	
									->setValue('')
									->draw();

							echo $ui->setElement('hidden')
									->setName('engine_no_list[]')
									->setID('engine_no`+index+`')
									->setClass('engine_no')	
									->setValue('')
									->draw();

							echo $ui->setElement('hidden')
									->setName('chassis_no_list[]')
									->setID('chassis_no`+index+`')
									->setClass('chassis_no')	
									->setValue('')
									->draw();
							
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12 hidden')
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
			// $('body').append(modal);
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
			
			if (details.item_ident_flag == 0) {
				$('#serial_' + details.linenum).addClass('hidden');
				$('#receiptqty' + details.linenum).removeClass('hidden receiptqty_serialized');
			} else {
				<?php if($ajax_task == "ajax_create") { ?>
				$('#receiptqty' + details.linenum).addClass('hidden receiptqty_serialized').attr('data-value',0);
				<?php } else { ?>
				$('#receiptqty' + details.linenum).addClass('hidden receiptqty_serialized')
				<?php } ?>
				$('#serial' + details.linenum).removeClass('hidden');
				
				var item = {};
				item = {itemcode : details.itemcode,
						numbers : []};
				serialize[index] = item;

				// DECLARE SERIAL NUMBERS BASED ON QUANTITY
				var prop = {};
				for(var i = 1; i <= parseInt(details.receiptqty); i++){
					prop = {serialno: '',
								engineno: '',
								chassisno: ''
								};
					serialize[index].numbers.push(prop);
				};		
			}
			
			<?php if (!$show_input || $ajax_task == 'ajax_edit') { ?> //VIEW POPULATE serialize FROM DB
				var dataFromDB = <?php echo json_encode($serial_db) ?>;
				var dataFromDB_index = [];
				for (x = 0 ; x < dataFromDB.length ; x++){
					if (dataFromDB[x].itemcode === details.itemcode) {
						dataFromDB_index.push(dataFromDB[x]);
					}
				}
				
				for (var k = 0 ; k < parseInt(details.receiptqty) ; k++){
					if(serialize[index].itemcode.length > 0){
					serialize[index].numbers[k].serialno = dataFromDB_index[k].serialno;
					serialize[index].numbers[k].engineno = dataFromDB_index[k].engineno;
					serialize[index].numbers[k].chassisno = dataFromDB_index[k].chassisno;
					}
				}
			<?php } ?>
			

			var warehouse = $('#warehouse').val();
			if (warehouse == details.warehouse) {
				$('#tableList tbody').find('tr:last .receiptqty').each(function() {
					if (details.receiptqty > 0) {
						$(this).removeAttr('readonly').val($(this).attr('data-value'));
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
						$('#tableList tbody').find('tr:last .serialize_button').prop("disabled",false);
					} else {
						$('#tableList tbody').find('tr:last .receiptqty').attr('readonly', '').val(0);
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
						$('#tableList tbody').find('tr:last .serialize_button').prop("disabled",true);
					}
				});
			} else {
				$('#tableList tbody').find('tr:last .receiptqty').attr('readonly', '').val(0);
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
				<?php if ($show_input) { ?>
					$('#tableList tbody').find('tr:last .serialize_button').prop("disabled",true);
				<?php }	?>
			}
			
			$('#serial_' + details.linenum).on("click", function() {
				$('#serialize_tableList tbody').empty();

				// IDENTIFY FIELDS NEEDED
				$("#serialize_tbody").attr("data-item-ident-flag",details.item_ident_flag);

				icode = $('#serial_' + details.linenum).data('itemcode');
				item = $('#serial_' + details.linenum).data('item');
				serialize_item_selected = item;
				serialize_icode_selected = icode;
				index_selected = index;
				item_max_qty = parseInt(details.receiptqty);
				
				var rows = 0;

				for (var i = 0 ; i <= index ; i++){
					if(serialize[i].itemcode == icode){
						
						for (var rows = 0 ; rows < parseInt(details.receiptqty) ; rows++){
							
							if (!serialize[index].numbers[rows].serialno && !serialize[index].numbers[rows].engineno && !serialize[index].numbers[rows].chassisno) {	// CHECK NUMBER OF ROW WITH SERIAL NUMBERS														
								break; //BREAK LOOP IF END OF QUANTITY REACHED
							} else { //ELSE POPULATE EXISTING ROW WITH SERIALS
								sn = serialize[i].numbers[rows].serialno;
								en = serialize[i].numbers[rows].engineno;
								cn = serialize[i].numbers[rows].chassisno;

								addRow(icode, item, rows, sn, en, cn);
							}
						}
						break;
					}
				}

				<?php if ($show_input) { ?>
				// ADD 1 ROW IF NO THERE ARE NO ROWS
				if ($('#serialize_tableList tbody tr').length == 0){
					addRow(icode, item, 0);
				}
				<?php } ?>
				
				console.log(serialize);
				$('.add-data').text("Add a New Line");
				$("#serialize_modal").modal('show');
			});
		}

		$('.add-data').on("click", function() {
			rownum = checkSerialRows();
			// alert(item_max_qty);
			if (rownum < item_max_qty) {
				addRow(serialize_icode_selected, serialize_item_selected);
			} else {
				$(this).text("Maximum items reached");
			}
		});

		function checkSerialRows(){
			rows = $('#serialize_tableList tbody tr').length;
			return rows;
		}

		serial_db = <?php echo json_encode($serial_db_array) ?>;
		engine_db = <?php echo json_encode($engine_db_array) ?>;
		chassis_db = <?php echo json_encode($chassis_db_array) ?>;
		serial_saved = [];
		engine_saved = [];
		chassis_saved =[];
		
		$('.save_serials').on("click", function(){
			saveSerialsInput(index_selected);
			
			serial_saved = [];
			engine_saved = [];
			chassis_saved = [];	
			for(items = 0; items < serialize.length; items++){
				if(serialize[items].itemcode != ''){
					for(serials = 0; serials < serialize[items].numbers.length; serials++){
						if(serialize[items].numbers[serials].serialno){
							serial_saved.push(serialize[items].numbers[serials].serialno);
						}
						if(serialize[items].numbers[serials].engineno){
							engine_saved.push(serialize[items].numbers[serials].engineno);
						}
						if(serialize[items].numbers[serials].chassisno){
							chassis_saved.push(serialize[items].numbers[serials].chassisno);
						}
					}
				}
			}

			serial_input = [];
			engine_input = [];
			chassis_input = [];
		})

		$('.close_serials').on("click", function(){
			serial_input = [];
			engine_input = [];
			chassis_input = [];
		});

		function addRow(icode, item, rownum, serialno, engineno, chassisno){
				if (typeof rownum == 'undefined'){
					rownum = $('#serialize_tableList tbody tr').length;
					// console.log(rownum);
				}

				item_ident_flag = $("#serialize_tbody").attr("data-item-ident-flag");
				
				hasSerial = '';
				hasEngine = '';
				hasChassis = '';
				
				<?php if($show_input) { ?>
				hasSerial = (item_ident_flag[0] == 1) ? '' : 'disabled';
				hasEngine = (item_ident_flag[1] == 1) ? '' : 'disabled';
				hasChassis = (item_ident_flag[2] == 1) ? '' : 'disabled';
				<?php } ?>

				(typeof serialno == 'undefined') ? serialno = '' : serialno=serialno;
				(typeof engineno == 'undefined') ? engineno = '' : engineno=engineno;
				(typeof chassisno == 'undefined') ? chassisno = '' : chassisno=chassisno;

				$('#serialize_tableList tbody').append(
					`<tr id="row`+ (rownum+1) +`">
						
						<td class="item_no col-xs-2">` + icode + `</td>
						<td class="item_name col-xs-3">` + item + `</td>
						<td class="serial_no" class="col-xs-2">
							<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('serial_no_item[`+ rownum +`]')
									->setClass('serial_no_item text-right')
									->setID('serial_no_item[`+ rownum +`]')
									->setValue('`+ serialno +`')
									->setAttribute(
										array(
											'data-value' => "`+ serialno +`",
											'maxlength'=> "20",
											'`+hasSerial+`'
										))
									->draw($show_input);
							?>
							<div><strong><small class="error_message"></small></strong></div>
						</td>
						<td class="engine_no" class="col-xs-2">
							<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('engine_no_item[`+ rownum +`]')
									->setClass('engine_no_item text-right')
									->setID('engine_no_item[`+ rownum +`]')																
									->setValue('`+ engineno +`')
									->setAttribute(
										array(
											'data-value' => "`+ engineno +`",
											'maxlength'=> "20",
											'`+hasEngine+`'
										))
									->draw($show_input);
							?>
							<div><strong><small class="error_message"></small></strong></div>
						</td>
						<td class="chassis_no" class="col-xs-2">
							<?php
								echo $ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('chassis_no_item[`+ rownum +`]')
									->setClass('chassis_no_item text-right')
									->setID('chassis_no_item[`+ rownum +`]')																
									->setValue('`+ chassisno +`')
									->setAttribute(
										array(
											'data-value' => "`+ chassisno +`",
											'maxlength'=> "20",
											'`+hasChassis+`'
										))
									->draw($show_input);
							?>
							<div><strong><small class="error_message"></small></strong></div>
						</td>
						<?php if ($show_input) {?>
						<td class="text-center">
							<button type="button" class="btn btn-danger btn-flat deleteRow" id="deleteRow`+rownum+`" data-delete=`+rownum+`>
								<span class="glyphicon glyphicon-trash"></span>
							</button>
						</td>
						<?php } ?>
					</tr>`
				);
			}
	
		function saveSerialsInput(index){
			// DELETE BLANK ROWS
			initial_number_rows = $('#serialize_tableList tbody tr').length;
			for (j = 0 ; j < initial_number_rows ; j++){
				isSerialBlank = ($('.serial_no_item:eq('+j+')').val() == '');
				isEngineBlank = ($('.engine_no_item:eq('+j+')').val() == '');
				isChassisBlank = ($('.chassis_no_item:eq('+j+')').val() == '');
				if (isSerialBlank && isEngineBlank && isChassisBlank) {
					$('#deleteRow'+j).click();
				}
			}
			// FINAL NUMBER OF ROWS
			number_rows = $('#serialize_tableList tbody tr').length;
			serials = '';
			engines = '';
			chassis = '';

			// EMPTY serialize array 
			for(x = 0; x < serialize[index].numbers.length; x++){
				serialize[index].numbers[x].serialno = '';
				serialize[index].numbers[x].engineno = '';
				serialize[index].numbers[x].chassisno = '';
			}

			// REPLACE serialize array with new values
			for (i = 0; i < number_rows; i++) {
				serialize[index].numbers[i].serialno = $('.serial_no_item:eq('+i+')').val();
				serialize[index].numbers[i].engineno = $('.engine_no_item:eq('+i+')').val();
				serialize[index].numbers[i].chassisno = $('.chassis_no_item:eq('+i+')').val();
				
				if (i==number_rows-1){
					serials += serialize[index].numbers[i].serialno;
					engines += serialize[index].numbers[i].engineno;
					chassis += serialize[index].numbers[i].chassisno;
				} else {
					serials += serialize[index].numbers[i].serialno+',';
					engines += serialize[index].numbers[i].engineno+',';
					chassis += serialize[index].numbers[i].chassisno+',';
				}	
			}
			
			$('#serial_no'+index).val(serials);
			$('#engine_no'+index).val(engines);
			$('#chassis_no'+index).val(chassis);
	
			// CHECK NUMBER OF INPUTS FOR QUANTITY IN DB
			var maxCount = 0;
			var serialCount = 0;
			$(".serial_no_item").each(function() {
				if($(this).val().length > 0) {
					serialCount++;
				}
			})

			var engineCount = 0;
			$(".engine_no_item").each(function() {
				if($(this).val().length > 0) {
					engineCount++;
				}
			})

			var chassisCount = 0;
			$(".chassis_no_item").each(function() {
				if($(this).val().length > 0) {
					chassisCount++;
				}
			})

			maxCount = Math.max(serialCount,engineCount,chassisCount);

			$("#serialize_modal").modal('hide');
			$("#receiptqty"+(index+1)).val(maxCount); //UPDATE QUANTITY BASED ON POPULATED SERIALS
			$("#receiptqty"+(index+1)).closest('tr').find('.receiptqty_serialized_display').text(maxCount);
			
			// console.log(serialize);
			
		}

		// ON EDIT, IMMEDIATELY SAVE SERIALS TO FORM
		<?php if ($ajax_task=='ajax_edit') { ?>
		$(document).ready( function(){
			serials = '';
			engines = '';
			chassis = '';
			for (index = 0 ; index < serialize.length ; index++){
				if(serialize[index].itemcode.length > 0) {
					for(index2 = 0 ; index2 < serialize[index].numbers.length ; index2++) {
						if(index2 == serialize[index].numbers.length-1){
							serials += serialize[index].numbers[index2].serialno;
							engines += serialize[index].numbers[index2].engineno;
							chassis += serialize[index].numbers[index2].chassisno;
						} else {
							serials += serialize[index].numbers[index2].serialno+',';
							engines += serialize[index].numbers[index2].engineno+',';
							chassis += serialize[index].numbers[index2].chassisno+',';
						}
					}
				}
				$('#serial_no'+index).val(serials);
				$('#engine_no'+index).val(engines);
				$('#chassis_no'+index).val(chassis);
			}
		});
		<?php } ?>
		
		$('tbody').on('click', '.deleteRow', function(e) {
			var deleted_row = $(this).data('delete');
			var serial_deleted = $('#serial_no_item['+deleted_row+']').val();
			var serialDeletedIndex_saved = serial_saved.indexOf(serial_deleted);
			var serialDeletedIndex_input = serial_input.indexOf(serial_deleted);

			if( serialDeletedIndex_saved > -1) {
				serial_input.splice(serialDeletedIndex_saved,1);
			}

			if( serialDeletedIndex_input > -1) {
				serial_input.splice(serialDeletedIndex_input,1);
			}

			var engine_deleted = $('#engine_no_item['+deleted_row+']').val();
			var engineDeletedIndex_saved = engine_saved.indexOf(engine_deleted);
			var engineDeletedIndex_input = engine_input.indexOf(engine_deleted);

			if( engineDeletedIndex_saved > -1) {
				engine_input.splice(engineDeletedIndex_saved,1);
			}

			if( engineDeletedIndex_input > -1) {
				engine_input.splice(engineDeletedIndex_input,1);
			}	

			var chassis_deleted = $('#chassis_no_item['+deleted_row+']').val();
			var chassisDeletedIndex_saved = chassis_saved.indexOf(chassis_deleted);
			var chassisDeletedIndex_input = chassis_input.indexOf(chassis_deleted);

			if( chassisDeletedIndex_saved > -1) {
				chassis_input.splice(chassisDeletedIndex_saved,1);
			}

			if( chassisDeletedIndex_input > -1) {
				chassis_input.splice(chassisDeletedIndex_input,1);
			}	

			$(this).closest('tr').remove();
			checkFlags();
		});

		var serial_flag = true;
		var engine_flag = true;
		var chassis_flag = true;
		serial_input = [];
		engine_input = [];
		chassis_input = [];
		function validateSerialNo(newSerialInput){
			if(newSerialInput != ''){
				if ($.inArray( newSerialInput, serial_db ) > -1){
					return "Serial Number already exists in Database";
				} else if($.inArray( newSerialInput, serial_saved ) > -1){
					return "Serial Number already saved";
				} else if($.inArray( newSerialInput, serial_input ) > -1){
					return "Serial Number already entered";
				} else {
					return true;
				}
			}
		}
		function validateEngineNo(newEngineInput){
			if(newEngineInput != ''){
				if ($.inArray( newEngineInput, engine_db ) > -1){
					return "Engine Number already exists in Database";
				} else if($.inArray( newEngineInput, engine_saved ) > -1){
					return "Engine Number already saved";
				} else if($.inArray( newEngineInput, engine_input ) > -1){
					return "Engine Number already entered";;
				} else {
					return true;
				}
			}
		}
		function validateChassisNo(newChassisInput){
			if(newChassisInput != ''){
				if ($.inArray( newChassisInput, chassis_db ) > -1){
					return "Chassis Number already exists in Database";
				} else if($.inArray( newChassisInput, chassis_saved ) > -1){
					return "Chassis Number already saved";
				} else if($.inArray( newChassisInput, chassis_input ) > -1){
					return "Chassis Number already entered";;
				} else {
					return true;
				}
			}
		}

		function checkFlags(){
			var hasError = $('tbody').find('.error_message').text().length;
			if (hasError > 0){
				$('.save_serials').prop('disabled',true);
			}else{
				$('.save_serials').prop('disabled',false);
			}
		}
		
		// DISALLOW SPECIAL CHARACTERS IN INPUT
		$('tbody').on('keypress','.serial_no_item, .engine_no_item, .chassis_no_item', function(event) {
			var regex = new RegExp("^[a-zA-Z0-9\b\-]+$");
			var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
			if (!regex.test(key)) {
				event.preventDefault();
			return false;
			}
		});

		// SERIAL NUMBER VALIDATE START
		$('tbody').on('keyup','.serial_no_item',function(){
			var serialInput = $(this).val();
			
			if (serialInput != $(this).data('value')) { // ADDED IF STATEMENT TO PREVENT VALIDATE ON CTRL+C
				if (validateSerialNo(serialInput) != true){
					serial_flag = false;
					$(this).closest('.form-group').addClass('has-error')
					$(this).closest('.serial_no').find('.error_message').text(validateSerialNo(serialInput)).css('color', 'red');
				} else {
					serial_flag = true;
					$(this).closest('.form-group').removeClass('has-error');
					$(this).closest('.serial_no').find('.error_message').text("");
				}
			}

			checkFlags();
		});

		$('tbody').on('focusin','.serial_no_item',function(){
			$(this).data('value',$(this).val());
			console.log('copy');
		}).on('change','.serial_no_item',function(){
			var prev_serialInput = $(this).data('value');
			var serialInput = $(this).val();
			var serialIndex = serial_input.indexOf(prev_serialInput);
			
			if( serialIndex > -1) {
				serial_input.splice(serialIndex,1);
			}
			if(validateSerialNo(serialInput) == true){
				$(this).data('value',serialInput);
				if(serialInput != ""){
					serial_input.push(serialInput);
				}
			}
			
			// console.log(serial_input);
			checkFlags();
		});
		// SERIAL NUMBER VALIDATE END

		// ENGINE NUMBER VALIDATE START
		$('tbody').on('keyup','.engine_no_item',function(){
			var engineInput = $(this).val();
			
			if (engineInput != $(this).data('value')) { // ADDED IF STATEMENT TO PREVENT VALIDATE ON CTRL+C
				if (validateEngineNo(engineInput) != true){
					engine_flag = false;
					$(this).closest('.form-group').addClass('has-error')
					$(this).closest('.engine_no').find('.error_message').text(validateEngineNo(engineInput)).css('color', 'red');
				} else {
					engine_flag = true;
					$(this).closest('.form-group').removeClass('has-error');
					$(this).closest('.engine_no').find('.error_message').text("");
				}
			}
			
			checkFlags();
		});

		$('tbody').on('focusin','.engine_no_item',function(){
			$(this).data('value',$(this).val());
		}).on('change','.engine_no_item',function(){
			var prev_engineInput = $(this).data('value');
			var engineInput = $(this).val();
			var engineIndex = engine_input.indexOf(prev_engineInput);
			
			if( engineIndex > -1) {
				engine_input.splice(engineIndex,1);
			}
			if(validateEngineNo(engineInput) == true){
				$(this).data('value',engineInput);
				if(engineInput != ""){
					engine_input.push(engineInput);
				}
			}
			// console.log(engine_input);
			checkFlags();
		});
		// ENGINE NUMBER VALIDATE END
		
		// CHASSIS NUMBER VALIDATE START
		$('tbody').on('keyup','.chassis_no_item',function(){
			var chassisInput = $(this).val();
			
			if (chassisInput != $(this).data('value')) { // ADDED IF STATEMENT TO PREVENT VALIDATE ON CTRL+C			
				if (validateChassisNo(chassisInput) != true){
					chassis_flag = false;
					$(this).closest('.form-group').addClass('has-error')
					$(this).closest('.chassis_no').find('.error_message').text(validateChassisNo(chassisInput)).css('color', 'red');
				} else {
					chassis_flag = true;
					$(this).closest('.form-group').removeClass('has-error');
					$(this).closest('.chassis_no').find('.error_message').text("");
				}
			}
			
			checkFlags();
		})

		$('tbody').on('focusin','.chassis_no_item',function(){
			$(this).data('value',$(this).val());
		}).on('change','.chassis_no_item',function(){
			var prev_chassisInput = $(this).data('value');
			var chassisInput = $(this).val();
			var chassisIndex = chassis_input.indexOf(prev_chassisInput);
			
			if( chassisIndex > -1) {
				chassis_input.splice(chassisIndex,1);
			}
			if(validateChassisNo(chassisInput) == true){
				$(this).data('value',chassisInput);
				if(chassisInput != ""){
					chassis_input.push(chassisInput);
				}
			}
			// console.log(chassis_input);
			checkFlags();
		});
		// CHASSIS NUMBER VALIDATE END
		
	
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
					var taxamount = removeComma(addComma(amount + (amount * parseFloat(taxrate))));
					//amount = amount - taxamount;
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
			if (vendor == '') {g
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
					$(this).closest('tr').find('.serialize_button ').prop("disabled",false);
				} else {
					$(this).attr('readonly', '').val(0);
					$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
					$(this).closest('tr').find('.serialize_button ').prop("disabled",true);
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