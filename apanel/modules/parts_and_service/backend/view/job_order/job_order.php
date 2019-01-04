<section class="content">

	<ul class="nav nav-tabs">
		<li class="active"><a href="#main" data-toggle="tab" data-id="main">Details</a></li>
		<?if(!$show_input):?><li><a href="#files" data-toggle="tab" data-id="files">Attachments</a></li><?endif;?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="main">
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
													<input type="text" class="form-control" readonly value="<?= (empty($job_order_no)) ? ' - Auto Generated -' : $job_order_no ?>">
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
								<div class="row">
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
												//->setValidation('required')
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
									<th class="col-md-2">Quantity</th>
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
										<?php if( ($show_input) ): ?>
											<button type="button" id="addNewItemJODetails" class="btn btn-link">Add a New Line</button>
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
									if ($stat == 'prepared' && $restrict_dr || empty($stat)) {
										echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
									}
								?>
								<?php if(!$show_input):?><a href="http://localhost/triglobe/apanel/parts_and_service/job_order/payment/<?php echo $job_order_no ?>" class="btn btn-warning">Issue Parts</a><?endif;?>
								<?php
									// echo '&nbsp;&nbsp;&nbsp;';
									echo $ui->drawCancel();
								?>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?if(!$show_input):?>
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
			</div>
			<?endif;?>
		</div>
		<div class="tab-pane" id="files">
			<div class="box box-primary">
				<form method = "post" class="form-horizontal" id="case_attachments_form" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table id="fileTable" class="table table-bordered">
									<thead>
										<tr class="info">
											<th class="col-md-1">Action</th>
											<th class="col-md-5">File Name</th>
											<th class="col-md-2">File Type</th>
										</tr>
									</thead>
									<tbody class="files" id="attachment_list">
										<tr>
											<!-- <td colspan="4" class="text-center">
												<strong>- No Attachments Available -</strong>
											</td> -->
											<td>
												<button type="button" id="replace_attachment" name="replace_attachment" class="btn btn-primary">Replace</button>
											</td>
											<td><a href="insert uploaded link here">1123132.pdf</a></td>
											<td>PDF</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- <hr/>
					<div class="row">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal" onClick="getList();">Close</button>
						</div>
					</div> -->
					<br/>
				</form>
			</div>
		</div>
	</div>
</section>
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
	<div id="attach_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Attach File</h4>
			<h4 class="modal-title">JO NO.: JO0000000001</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<!-- <label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label> -->
					<?php
						echo $ui->setElement('file')
								->setId('import_csv')
								->setName('import_csv')
								->setAttribute(array('accept' => '.csv'))
								->setValidation('required')
								->draw();
					?>
					<span class="help-block"></span>
				</div>
				<p class="help-block">The file to be imported shall not exceed the size of 1mb and must be a PDF, PNG or JPG file.</p>
			</div>
			<div class="modal-footer">
				<div class="col-md-12 col-sm-12 col-xs-12 text-center">
					<div class="btn-group">
					<button type = "button" class = "btn btn-primary btn-sm btn-flat">Attach</button>
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
	<div id="ordered_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Service Quotation List</h4>
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
								<th class="col-xs-3">Service Quotation No.</th>
								<th class="col-xs-3">Transaction Date</th>
								<th class="col-xs-4">Notes</th>
								<!-- <th class="col-xs-2 text-right">Amount</th> -->
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
	$(document).ready(function(){
		$('.detailparticular').prop('readonly', false);
		//$('.qty').prop('readonly','true');
		$('.detailforBundle').prop('disabled',false);
		$('.WhForBundle').prop('disabled',false);
		$('.whchild').prop('disabled', false);
		$('.parentqty').prop('readonly', false);
		$('.childqty').prop('readonly', false);

		<?php if($show_input && $ajax_task == 'ajax_edit' && $service_quotation != ''): ?>
		$('.detailparticular').prop('readonly', true);
		//$('.qty').prop('readonly','true');
		$('.detailforBundle').prop('disabled',true);
		$('.WhForBundle').prop('disabled',true);
		$('.whchild').prop('disabled', true);
		$('.parentqty').prop('readonly', true);
		$('.childqty').prop('readonly', true);
		$('.ccode').prop('disabled', true);
		document.getElementById('addNewItemJODetails').style.visibility = 'hidden';
		<?php endif ?>
	});
		var delete_row	= {};
		var ajax		= {};
		var ajax_call	= '';
		var min_row		= 1;	
		var parentline  = 0;
		var parent 		= 0;
		function addVoucherDetails(details, index, parent="") {
			// console.log("Parent = "+parent);
			var details = details || {itemcode: '', detailparticular: '', warehouse: '', qty: '0', uom: 'PC', childqty : '0', linenum : '0', isbundle : 'No', parentline : '', parentcode : ''};
			// console.log(details);
			var other_details = JSON.parse(JSON.stringify(details));
			delete other_details.itemcode;
			delete other_details.detailparticular;
			delete other_details.warehouse;
			delete other_details.qty;
			delete other_details.childqty;
			delete other_details.linenum;
			delete other_details.isbundle;
			delete other_details.parentline;
			delete other_details.parentcode;
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
			// I added a condition that if details.linenum (retrieved data) is 0 (meaning, nothing was retrieved), the line number should refer to the passed index..
			var linenum = (details.linenum != 0  && details.linenum != undefined) ? details.linenum : index + 1;
			details.warehouse = (details.warehouse != "" && details.warehouse != undefined) ? details.warehouse : "none";
			var row = ``;
			if(details.parentcode == "" || details.parentcode == null){
				var asd = 'parents'+linenum;
				// parent = linenum;
			}else{
				parentline = parent;
				var asd = 'subitem'+parentline;
			}
			if(details.isbundle == 1){
				var dsa = 'data-isbundle="1"';
			}else{
				var dsa = 'data-isbundle="0"';
			}
			row += `
				<tr class="`+asd+`" ` + dsa +` data-value = "`+details.qty+`" data-linenum="`+linenum+`"  data-parentlinenum="`+parentline+`">`;
			row += `<td>
					`;
					if(details.parentcode == null  || details.parentcode == ""){
						row += `
						<?php
							$value = "<span id='temp_view_itemcode_` + index + `'>` + details.itemcode + `</span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_itemcode[]')
								->setClass('itemcode pcode')
								->setList($item_list)
								->setNone('Selected: None')
								->setValue($value)
								->draw($show_input);

							echo $ui->formField('hidden')
								->setName('h_itemcode[]')
								->setClass('h_itemcode')
								->setValue('` + details.itemcode + `')
								->draw($show_input);
					 ?> `;
					}else{
						console.log( " INDEX "+linenum);
						row += `
						<?php
							$value = "<span id='temp_view_itemcode_` + linenum + `'>` + details.itemcode + `</span>";
							echo $value;
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_itemcode[]')
								->setClass('itemcode ccode')
								->setList($item_list)
								->setNone('Selected: None')
								->setValue($value)
								->draw($show_input);

							echo $ui->formField('hidden')
								->setName('h_itemcode[]')
								->setClass('h_itemcode')
								->setValue('` + details.itemcode + `')
								->draw($show_input);
					 ?> `;
					}
					row +=`<?php
							
							echo $ui->formField('hidden')
								->setName('childqty[]')
								->setClass('childqty')
								->setValue('` + details.BaseQty + `')
								->draw($show_input);
							echo $ui->formField('hidden')
								->setName('parentline[]')
								->setClass('parentline')
								->setValue('` + parentline + `')
								->draw($show_input);
							echo $ui->formField('hidden')
								->setName('isbundle[]')
								->setClass('isbundle')
								->setValue('` + details.isbundle + `')
								->draw($show_input);
							echo $ui->formField('hidden')
								->setName('linenum[]')
								->setClass('linenum')
								->setValue('` + linenum + `')
								->draw($show_input);
						?>
					</td>
					<td>`;
					if(details.parentcode == null || details.parentcode == ""){
					row += `<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('detailparticular parent')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.detailparticular + `')
								->draw($show_input);

								echo $ui->formField('hidden')
								->setName('h_detailparticular[]')
								->setClass('h_detailparticular parts')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.detailparticular + `')
								->draw($show_input);
						?> ` + otherdetails + ` </td>
					`;
					}else{
						row += `<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('detailparticular parts detailforBundle')
								->setValue('` + details.detailparticular + `')
								->setAttribute(array('readOnly' => true, 'data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->draw($show_input);

								echo $ui->formField('hidden')
								->setName('h_detailparticular[]')
								->setClass('h_detailparticular')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.detailparticular + `')
								->draw($show_input);
								?>` + otherdetails + ` </td>
						`;
					}
					if(details.parentcode == null  || details.parentcode == ""){
					row += `<td>
					<?php
							$value = "<span id='temp_view_warehouse_` + index + `'>'` + warehouse + `'</span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('warehouse[]')
								->setClass('warehouse parent WhForBundle')
								->setList($warehouse_list)
								->setNone('Selected: None')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue($value)
								->draw($show_input);

							echo $ui->formField('hidden')
								->setName('h_warehouse[]')
								->setClass('h_warehouse parts')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.warehouse + `')
								->draw($show_input);
						?> ` + otherdetails + ` </td>
						`;
					}else{
						row += `<td>
						<?php
							$value = "<span id='temp_view_warehouse_` + index + `'></span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('warehouse[]')
								->setClass('warehouse parts whchild')
								->setList($warehouse_list)
								->setNone('Selected: None')
								->setAttribute(array('readOnly' => true, 'data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.warehouse + `')
								->draw($show_input);

							echo $ui->formField('hidden')
								->setName('h_warehouse[]')
								->setClass('h_warehouse h_whchild')
								->setAttribute(array('data-linenum' => '` + (linenum) + `', 'data-parentline' => '` + (parentline) + `'))
								->setValue('` + details.warehouse + `')
								->draw($show_input);
						?>` + otherdetails + ` </td>
						`;
					}

					if(details.parentcode == null  || details.parentcode == ""){
					row += `<td class="text-right">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('qty[]')
										->setClass('qty text-right parentqty')
										->setAttribute(array('data-value' => '` + (parseFloat(details.qty) || 0) + `'))
										->setValidation('required integer')
										->setValue('` + (addComma(details.qty, 0) || 0) + `')
										->draw($show_input);
							?> ` + otherdetails + ` </td>
						`;
					}else{
						row += `<td class="text-right">
							<?php
								echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('qty[]')
										->setClass('qty text-right childqty')
										->setAttribute(array('readonly' => true,'data-value' => '` + (parseFloat(details.qty) || 0) + `'))
										->setValidation('required integer')
										->setValue('` + (parseFloat(details.qty) || 0) + `')
										->draw($show_input);
							?> ` + otherdetails + ` </td>
						`;
					}
					row += `;
					<td>
						<?php
							echo $ui->formField('text')
								->setName('uom[]')
								->setClass('uom')
								->setSplit('', 'col-md-12')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw(false);

							echo $ui->formField('hidden')
								->setName('h_uom[]')
								->setClass('h_uom')
								->setSplit('', 'col-md-12')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw($show_input);
						?>
					</td>
					<?php if ($show_input): ?>
					<td class="text-right">
						<button type="button" class="btn btn-danger btn-flat delete_row" style="outline:none;">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</td>s
					<?php endif ?>
				</tr>
			`;
			// var row = `
			// 	<tr>
			// 		<td colspan="6" class="text-center"><i><strong>Please select a Customer and Service Quotation First.</strong></i></td>
			// 	</tr>
			// `;
			if(details.isbundle == 1){
				console.log(parentline);
				// console.log(linenum);
				if($('#tableList tbody tr.parents'+parentline).siblings('.subitem'+parentline).length > 0){
				console.log(linenum);
					$('#tableList tbody tr.subitem'+parentline+'[data-linenum="'+(parseFloat(linenum)-1)+'"]').after(row);
				} else {
					$('#tableList tbody tr').eq(parentline-1).after(row);
				}
			} else {
				$('#tableList tbody').append(row);
			}
			var row2 = `
				<tr>
					<td>
						<?php
							$value = "<span id='temp_view_itemcode_` + index + `'>` + details.itemcode + `</span>";
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('detail_itemcode[]')
								->setClass('itemcode')
								->setList($item_list)
								->setValue($value)
								->draw($show_input);
						?>
						
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setValue('` + details.detailparticular + `')
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
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('qty[]')
								->setClass('qty text-right')
								->setAttribute(array('data-value' => '` + (parseFloat(details.qty) || 0) + `'))
								->setValidation('required integer')
								->setValue('` + (addComma(1, 0) || 0) + `')
								->draw($show_input);
						?>
						` + otherdetails + `
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setValue('` + details.uom.toUpperCase() + `')
								->draw(false);
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
			$('#issuedPartsList tbody').append(row2);
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
					$('#temp_view_itemcode_' + index).html(item.val);
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
		
		function reorderlinenum(){
			// console.log($('#tableList tbody tr'));
			$('#tableList tbody tr').each(function(){
				var linenum = 1;
				$(this).data('linenum',linenum);
				linenum++;
			});
			// console.log("REORDER!");
		}

		function displayDetails(details,linenum="") {
			if (details.length < min_row) { // min_row == 1
				for (var x = details.length; x < min_row; x++) {
					addVoucherDetails('', x);
				}
			}
			if (details.length > 0) {
				parent = linenum;
				details.forEach(function(details, index) {
					index = (linenum != "") ? linenum++ 	:	index;
					addVoucherDetails(details, index, parent);
					
				});
				reorderlinenum();
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
		$('#replace_attachment').on('click',function(e){
			$('#attach_modal').modal('show');
		});
	</script>
	<?php if ($show_input): ?>
	<script>
		$('#addNewItemJODetails').on('click', function() {
			var count_lines = $('#tableList tbody tr').length; // This is to count the initial rows on the table after clicking the add new line button
			addVoucherDetails('',count_lines);
			$('.WhForBundle').prop('disabled', false);
			$('.detailforBundle').prop('disabled', false);
			$('.parentqty').prop('readonly', false);
		});
		<?php // if ($ajax_task == 'ajax_create'): ?>
		$('#service_quotation').on('focus', function() {
			var customer = $('#customer').val();
			ajax.customer = customer;
			if (customer == '') {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Customer');
				$('#customer').trigger('blur');
			} else {
				$('#ordered_tableList tbody').html();
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
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_sq_list', ajax, function(data) {
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
		// $('#customer').on('change', function() {
		// 	ajax.customer = $(this).val();
		// 	$('#source_no').val('');
		// 	$('#tableList tbody').html(`
		// 		<tr>
		// 			<td colspan="9" class="text-center"><b>Select Service Quotation No.</b></td>
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
			$('#service_quotation').val(so).trigger('blur');
			$('#ordered_list_modal').modal('hide');
			loadPackingListDetails();
			document.getElementById('addNewItemJODetails').style.visibility = 'hidden';

		});
		function loadPackingListDetails() {
			var voucherno = $('#service_quotation').val();
			if (voucherno) {
				ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_sq_details', { voucherno: voucherno }, function(data) {
					if ( ! data.success) {
						$('#tableList tbody').html(data.table);
					} else {
						$('#tableList tbody').html('');
						displayDetails(data.details);
						displayHeader(data.header);
						$('.itemcode').prop('disabled','true');
						$('.detailparticular').prop('readonly','true');
						$('.qty').prop('readonly','true');
						$('.warehouse').prop('disabled','true');
						//$('#tableList tbody .warehouse').prop('disabled','true');
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
				$('.qty').each(function() {
					items += removeComma($(this).val());
				});
				if ($('.qty').length > 0 && items > 0) {
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
		// $(body).on('click','.serialqty',function(e){
		// 	$('#sec_modal').modal("show");
		// });

		$('#tableList tbody').on('blur', '.parentqty', function(e) {
			var curr_qty= 	$(this);
			// console.log('clicked = '+curr_qty.closest('tr').data('linenum'));
			var value 	=	removeComma($(this).val());
			if ($(this).closest('tr').hasClass('items')) {
				if (value < 1) 
					$(this).parent().parent().addClass('has-error');
				else
					$(this).parent().parent().removeClass('has-error');
			}
			if ($(this).closest('tr').data('isbundle') == 1) {
				var linenum = $(this).closest('tr').data('linenum');
				var bundle 	= $(this).closest('tr').find('.itemcode').val();
				if(bundle){
					ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_retrieve_bom_qty', { bundle: bundle }, function(data) {
						if (data.result) {
							var content = data.result;
							content.forEach(function(row) {
								var ret_itemcode = row.itemcode;
								var ret_qty 	 = parseFloat(row.quantity) * value;
								$.each($('.subitem'+linenum), function(){
									var subcode = $(this).find('.itemcode').val();
									if(ret_itemcode == subcode){
										$(this).find('.childqty').val(ret_qty.toFixed(0));
									}
								});
							});
						} else {
						
							
						}
					});
				}
			}
	});

	$('#tableList tbody').on('change', '.warehouse', function(e) {
		var value 	= $(this).val();
		var linenum = $(this).closest('tr').data('linenum');	
		$(this).closest('tr').find('.h_warehouse').val(value);
		if ($(this).closest('tr').data('isbundle') == 1) {
			$.each($('.subitem'+linenum), function(){
				$(this).find('.warehouse').val(value);
				$(this).find('.h_warehouse').val(value);
			});
			drawTemplate();
		}
	});

	$('#tableList tbody').on('change', '.itemcode', function(e) {
		var customer 	=	$('#customer').val();
		var curr_code 	=	$(this);
		if( customer != "" ){
			var itemcode = $(this).val();
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_checkbundle',"itemcode="+itemcode, function(data) {
				if (!data.success) {
				}
				else {
					var content = data.result;
					var linenum = curr_code.closest('tr').data('linenum');
					if(content.isbundle == 1){
						// console.log(" Bundle - linenum = "+linenum)
						getItemDetails(itemcode,linenum);
					} else {
						$('.subitem'+linenum).remove();
					}
					curr_code.closest('tr').find('.detailparticular').val(content.detailparticular);
					curr_code.closest('tr').find('.uom').val(content.uom);
					curr_code.closest('tr').find('.h_uom').val(content.uom);
					curr_code.closest('tr').data('isbundle',content.isbundle);
				}
			});
			
		}
	});

	function getItemDetails(itemcode, linenum=""){
		if (itemcode != "") {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_bundle_details',"itemcode="+itemcode, function(data) {
				if ( ! data.success) {
					$('#tableList tbody').html(data.table);
				} else {
					// console.log(" DIpsplay Header Linenum = "+linenum);
					// $('#tableList tbody').html('');
					displayHeader(data.header);	
					displayDetails(data.details,linenum);
					$('.ccode').prop('disabled', true);
					$('.whchild').prop('disabled', true);
					$('.ccode').closest('tr').find('.delete_row').prop('disabled', true);
					reorderlinenum();
				}
			});
		}
	}

	</script>
	<?php endif ?>