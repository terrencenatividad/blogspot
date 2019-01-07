<section class="content">
		<div class="box box-primary">
			<?php if ($stat == 'Delivered') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a></li>
				<li role="presentation"><a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">Attachment</a></li>
			</ul>
			<?php } ?>
			<form action="" method="post" class="form-horizontal">
				<?php if ($stat == 'Delivered') { ?>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="details">
				<?php } ?>
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
													if ($ajax_task == 'ajax_edit') {
														echo $ui->formField('hidden')
															->setName('voucher')
															->setId('voucher')
															->setValue($voucherno)
															->draw($show_input);
													}
												?>
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
										<div class = "col-md-12">
											<?php
												echo $ui->formField('textarea')
														->setLabel('Shipping Address:')
														->setSplit('col-md-2', 'col-md-10')
														->setName('s_address')
														->setId('s_address')
														->setValue($s_address)
														->setAttribute(array("maxlength" => "105"))
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
											<?php
												echo $ui->formField('hidden')
													->setName('main_serial')
													->setId('main_serial')
													->setClass('main_serial')
													->draw($show_input);
											?>
											<?php
												echo $ui->formField('hidden')
													->setName('task')
													->setId('task')
													->setClass('task')
													->setValue($ajax_task)
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
						</div>
					<?php if ($stat == 'Delivered') { ?>
					<div role="tabpanel" class="tab-pane" id="attachment">
						<table id="AttachmentTable" class="table table-hover table-sidepad only-checkbox full-form">
							<thead>
								<tr class="info">
									<th class="col-xs-2">Action</th>
									<th class = "col-xs-7">File Name</th>
									<th class = "col-xs-3">File Type</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><button type = "button" class = "btn btn-primary btn-sm replace"><span class = "glyphicon glyphicon-pencil"></span> Replace</button></td>
									<td><a target="_blank" href = "<?php echo $fileurl ?>"><?php echo $filename ?></a></td>
									<td><?php echo $filetype ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			<?php } ?>
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
	<div class="modal fade" id="serialModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" id = "modal_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Items</h4>
					<h5 class="modal-title">Item Code: <input type = "text" id = "sec_itemcode"></h5>
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
	<div id="attachment_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<form method = "post" id="attachments_form" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Attach File for <span id="modal-voucher" style = "font-weight:bold"></span></h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<input type="hidden" name="voucherno" id='input_voucherno'>
							<?php
								echo $ui->setElement('file')
										->setId('files')
										->setName('files')
										->setAttribute(array('accept' => '.pdf, .jpg, .png'))
										->setValidation('required')
										->draw();
							?>
						</div>
						<p class="help-block">The file to be imported shall not exceed the size of <strong>1mb</strong> and must be a <strong>PDF, PNG or JPG</strong> file.</p>
					</div>
					<div class="modal-footer">
						<div class="col-md-12 col-sm-12 col-xs-12 text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-sm btn-flat" id="attach_button">Attach</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div id="attachment_success" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
				</div>
				<div class="modal-body">
					<p>You have successfully updated the attached file.</p>
				</div>
				<div class="modal-footer">
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
			var details = details || {itemcode: '', detailparticular: '', issueqty: '', serialnumbers : ''};
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.issueqty;
			delete other_details.warehouse;
			delete other_details.serialnumbers;
			delete other_details.parentcode;
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
			var row = ``;
			if (details.parentcode == '') {
				row += `<tr style = 'font-weight:bold'>`;
			}
			else {
				row += `<tr>`;
			}

					<?php if ($show_input): ?>
					if(details.parentcode == '') {
			row += `<td>
						<?php
							echo $ui->loadElement('check_task')
									->addCheckbox()
									->setValue('` + details.itemcode + `')
									->draw();
						?>
					</td>`;
					} else {
						row += `<td>
						<?php
							echo $ui->loadElement('check_task')
									->addCheckbox(false)
									->setValue('` + details.itemcode + `')
									->draw();
						?>
					</td>`;
					}
					<?php endif ?>
			row += `<td>
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
						<?php if ($ajax_task != '') { ?>
							<?php
								echo $ui->formField('hidden')
									->setName('serialnumbers[]')
									->setClass('serialnumbers')
									->setValue('` + details.serialnumbers + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('h_itemcode[]')
									->setClass('h_itemcode')
									->setValue('` + details.itemcode + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('parentcode[]')
									->setClass('parentcode')
									->setValue('` + details.parentcode + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('h_detailparticular[]')
									->setClass('h_detailparticular')
									->setValue('` + details.detailparticular + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('bundle_itemqty[]')
									->setClass('bundle_itemqty')
									->setValue('` + details.bundle_itemqty + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('parentline[]')
									->setClass('parentline')
									->setValue('` + details.parentline + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('item_ident_flag[]')
									->setClass('item_ident_flag')
									->setValue('` + details.item_ident_flag + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('linenumber[]')
									->setClass('linenumber')
									->setValue('` + details.linenum + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setName('quantityleft[]')
									->setClass('quantityleft')
									->setValue('` + details.qtyleft + `')
									->draw($show_input);
							?>
							<?php
								echo $ui->formField('hidden')
									->setClass('available_qty')
									->setValue('` + details.available + `')
									->draw($show_input);
							?>
						<?php } ?>
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
					</td>`;

					<?php if ($show_input): ?>
					row += `<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + addComma(details.available, 0) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>
					<td class="text-right qtyleft">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + addComma(details.qtyleft, 0) + `')
								->addHidden()
								->draw($show_input);
						?>
					</td>`;
					<?php endif ?>
					if (details.parentcode == '' && details.item_ident_flag == 0) {
						row += `<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('issueqty[]')
										->setClass('issueqty mainitem text-right')
										->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.issueqty) || 0) + `'))
										->setValidation('required integer')
										->setValue('` + (addComma(details.issueqty, 0) || 0) + `')
										->draw($show_input);
								?>` + otherdetails + `</td>`;
					}
					else if (details.parentcode == '' && details.item_ident_flag != 0) {
						<?php if ($ajax_task != '') { ?>
							row += `<td class="text-right qty_col"><input type = "button" class = "btn btn-md btn-success btn-flat col-md-12 text-right mainitem issueqty serialbtn" data-value = "` + (parseFloat(details.issueqty) || 0) + `" disabled value = "0">` + otherdetails + `<input type = "hidden" class = "issueqty" name = "issueqty[]" data-value = "` + (parseFloat(details.issueqty) || 0) + `" value = "` + (parseFloat(details.issueqty) || 0) + `"/></td>`;
						<?php } else { ?>
							row += `<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setClass('mainitem issueqty text-right')
										->setValue('` + (addComma(details.issueqty, 0) || 0) + `')
										->addHidden()
										->draw($show_input);
								?> ` + otherdetails + ` </td>`;
						<?php } ?> 
					}
					else {
						<?php if ($ajax_task != '') { ?>
							if (details.item_ident_flag == 0) {
								row += `<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('issueqty[]')
										->setClass('itempart issueqty text-right')
										->setAttribute(array('readonly' => 'readonly', 'data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.issueqty) || 0) + `'))
										->setValidation('integer')
										->setValue(0)
										->draw($show_input);
								?> ` + otherdetails + ` </td>
							`; } else {
								row += `<td class="text-right qty_col"><input type = "button" class = "btn btn-md btn-success btn-flat col-md-12 text-right itempart issueqty serialbtn" data-value = "` + (parseFloat(details.issueqty) || 0) + `" disabled value = "0">` + otherdetails + `<input type = "hidden" class = "issueqty" name = "issueqty[]" data-value = "` + (parseFloat(details.issueqty) || 0) + `" value = "` + (parseFloat(details.issueqty) || 0) + `"/></td>`;
							} 
						<?php } else { ?>
							row += `<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setClass('itempart issueqty text-right')
										->setValue('` + (addComma(details.issueqty, 0) || 0) + `')
										->addHidden()
										->draw($show_input);
								?> ` + otherdetails + ` </td>`;
						<?php } ?> 
					}
				row +=`	<td>
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
						$(this).val($(this).attr('data-value'));
						$(this).removeAttr('readonly').val($(this).attr('data-value'));
						if ($(this).hasClass('serialbtn')) {
							$(this).removeAttr('disabled').val($(this).attr('data-value'));
						}
						$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('check').iCheck('enable');
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
					} else {
						if ($(this).hasClass('serialbtn')) {
							$(this).attr('disabled', 'disabled').val(0);
						}
						$('#tableList tbody').find('tr:last .issueqty').attr('readonly', '').val(0);
						$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
					}
				});
			} else {
				if ($(this).hasClass('serialbtn')) {
					$(this).attr('disabled', 'disabled').val(0);
				}
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
		$('#tableList tbody').on('input change blur', '.taxcode, .unitprice', function() {
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
				var parentline = $(this).closest('tr').find('.parentline').val();
				if (warehouse == warehouse_row) {
					if ($(this).closest('tr').find('.issueqty').hasClass('itempart')) {
						var parent = $('#tableList tbody tr').find('.parentline[value="'+parentline+'"]:first');
						available = parent.closest('tr').find('.available_qty').val();
						if (available == 0) {
							if ($(this).hasClass('serialbtn')) {
								$(this).attr('disabled', 'disabled').val(0);
							}
							$(this).attr('readonly', '').val(0);
						}
						else {
							$(this).removeAttr('readonly').val($(this).attr('data-value'));
							if ($(this).hasClass('serialbtn')) {
								$(this).removeAttr('disabled').val($(this).attr('data-value'));
							}
						}
					}
					else {
						$(this).removeAttr('readonly').val($(this).attr('data-value'));
						if ($(this).hasClass('serialbtn')) {
							$(this).removeAttr('disabled').val($(this).attr('data-value'));
						}
					}
					$(this).closest('tr').find('.check_task [type="checkbox"]').iCheck('check').iCheck('enable');
				} 
				else {
					if ($(this).hasClass('serialbtn')) {
						$(this).attr('disabled', 'disabled').val(0);
					}
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
			var address = $(this).attr('data-address');
 			$('#source_no').val(so).trigger('blur');
			$('#s_address').val(address).trigger('blur');
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

			var count_err = 0;
			$('#tableList tbody tr').find('.serialbtn').each(function() {
				if ($(this).attr('disabled')) {}
				else {
					var req_val = $(this).val();
					var serial_list = $(this).closest('tr').find('.serialnumbers').val();
					var serials = serial_list.split(",");
					if (serials == '' || serials == 'undefined') {
						count_err++;
						$('#warning_counter .modal-body').html('Selected serial numbers must be equal to the required value.')
						$('#warning_counter').modal('show');
					}
				}
			});
			if (count_err == 0) {
				if (form_element.find('.form-group.has-error').length == 0) {
					var items = 0;
					$('.issueqty:not([readonly])').each(function() {
						items += removeComma($(this).val());
					});
					$('.serialnumbers').each(function() {
						if(($(this).val() == 'undefined')){
							$(this).val('');
						}
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
			}
			$('#submit_container [type="submit"]').attr('disabled', false);
		});
	</script>
	<?php endif; ?>

	<script>
		$('#tableList tbody').on('blur', '.issueqty', function() {
			if ($(this).hasClass('mainitem')) {
				var qty = $(this).val();
				var parentline = $(this).closest('tr').find('.parentline').val();
				var maxqty = $(this).closest('tr').find('.issueqty').attr('data-max');
				if ($(this).hasClass('itempart')) {
					$('#tableList tbody tr').find('.parentline[value="'+parentline+'"]').not(':first').each(function() {
						var itemqty = $(this).closest('tr').find('.bundle_itemqty').val();
						var total = qty * itemqty;
						var qtyleft = $('#tableList tbody tr td').closest('.qtyleft').find('input').val();
						if (qtyleft >= qty) {
							$(this).closest('tr').find('.issueqty').val(total);
						}
						else {
							total = maxqty * itemqty;
							$(this).closest('tr').find('.issueqty').val(total);
						}
					});
				}
				recomputeAll();
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
		var item_ident = '';
		$('#tableList tbody').on('click', '.serialbtn', function() {
			itemrow = $(this);
			linenum = $(this).closest('tr').find('span').attr('id')
			itemcode = $(this).closest('tr').find('.h_itemcode').val();
			description = $(this).closest('tr').find('.h_detailparticular').val();
			serials = $(this).closest('tr').find('.serialnumbers').val();
			quantityleft = $(this).closest('tr').find('.quantityleft').val();
			item_ident = $(this).closest('tr').find('.item_ident_flag').val();
			check_num = $(this).val();
			if ($(this).hasClass('mainitem')) {
				type = 'mainitem';
			}
			else {
				type = 'itempart';
			}
			tagSerial(itemcode, description, serials, check_num, type, quantityleft, item_ident);	
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
			ajax.itemselected = serials;
			//ajax.linenum = linenum;
			ajax.allserials = $('#main_serial').val();
			ajax.id = itemrow.closest('tr').find('.serialnumbers').val();
			ajax.item_ident = itemrow.closest('tr').find('.item_ident_flag').val();
			task = $('#task').val();
			ajax.task = $('#task').val();
			if (task=='ajax_edit') {
				var linenumber = itemrow.closest('tr').find('.linenumber').val();
				ajax.linenumber = linenumber;
				ajax.voucherno = $('#voucher').val();
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
					itemrow.closest('tr').find('.issueqty').val(count);
				}
				$('#serialModal').modal('hide');	
				$('#modal_close').show();
				$('#btn_close').show();
			}
		});

		$('#btn_ok').on('click', function() {
			$('#warning_counter').modal('hide');
		});

		$('#AttachmentTable').on('click', '.replace', function() {
			var voucherno = $('#voucherno').val();
			$('#modal-voucher').html(voucherno);
			$('#input_voucherno').val(voucherno);
			$('#attachment_modal').modal('show');
		});
	</script>
	<script>
		$(function () {
			'use strict';

			$('#attachments_form').fileupload({
				url: '<?= MODULE_URL ?>ajax/ajax_upload_file',
				maxFileSize: 2000000,
				disableExifThumbnail :true,
				previewThumbnail:false,
				autoUpload:false,
				add: function (e, data) {            
					$("#attach_button").off('click').on('click', function () {
						data.submit();
					});
				},
			});
			$('#attachments_form').addClass('fileupload-processing');
			$.ajax({
				url: $('#attachments_form').fileupload('option', 'url'),
				dataType: 'json',
				context: $('#attachments_form')[0]
			}).always(function () {
				$(this).removeClass('fileupload-processing');
			}).done(function (result) {
				$(this).fileupload('option', 'done')
					.call(this, $.Event('done'), {
						result: result
					});
			});

			$('#attachments_form').bind('fileuploadadd', function (e, data) {
				var filename = data.files[0].name;
				$('#attachments_form #files').closest('.input-group').find('.form-control').html(filename);
			});
			$('#attachments_form').bind('fileuploadsubmit', function (e, data) {
				var voucherno 		=  $('#input_voucherno').val();
				data.formData = {dr_voucherno: voucherno};
			});
			$('#attachments_form').bind('fileuploadalways', function (e, data) {
				var error = data.result['files'][0]['error'];
				var form_group = $('#attachments_form #files').closest('.form-group');
				if(!error){
					var voucherno 		=  $('#input_voucherno').val();
					$('#attachment_modal').modal('hide');
					$('#attachment_success').modal('show');
					setTimeout(function() {							
						window.location = '<?=MODULE_URL?>view/'+voucherno;						
					}, 1000)
					var msg = data.result['files'][0]['name'];
					form_group.removeClass('has-error');
					form_group.find('p.help-block.m-none').html('');

					$('#attachments_form #files').closest('.input-group').find('.form-control').html('');
					getList();
				}else{
					var msg = data.result['files'][0]['name'];
					form_group.addClass('has-error');
					form_group.find('p.help-block.m-none').html(msg);
				}
			});
		});
	</script>
	