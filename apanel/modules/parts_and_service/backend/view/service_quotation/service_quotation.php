<section class="content">
	<ul id='nav' class="nav nav-tabs">
		<li class="active"><a href="Details" data-toggle="tab">Details</a></li>
		<?if(!$show_input && !empty($filename)):?><li><a href="Attachment" data-toggle="tab">Attachments</a></li><?endif;?>
	</ul>
	<div id='Details' class="box box-primary tab-pane">
		<form action="" method="post" class="form-horizontal">
			<div class="box-body">
				<br>
				<div class="row">
					<div class="col-md-11">
						<div class="row">
		                    <?php 
		                    	if($ajax_task == 'view') :
		                        
		                        	$color = 'default';
									switch ($stat) {
										case 'Pending':
											$color = 'default';
											break;
										case 'Approved':
											$color = 'success';
											break;
										case 'Partial':
											$color = 'warning';
											break;
										case 'Cancelled':
											$color = 'danger';
											break;
										case 'With JO':
											$color = 'info';
											break;
									}
		                        ?> 
	                            <div class="row">
	                                <div class="col-lg-2"></div>
	                                <div class="col-lg-4">
	                                    <font size = "4em"><span class="label label-<?=$color;?>"><?=$stat;?></span></font>
	                                </div>
	                                <div class="col-lg-3"></div>
	                            </div>
	                            <br>
	                        <?php endif; ?>
		                </div>
						<div class="row">
							<div class="col-md-6">

								<?php
									echo $ui->formField('text')
										->setLabel('Service Quotation No.')
										->setSplit('col-md-4', 'col-md-8')
										->setName('voucherno')
										->setId('voucherno')
										->setValue($voucherno)
										->setAttribute(array('readonly'=>'readonly'))
										->setValidation('required')
										->draw($show_input);
								?>
								
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
										->setLabel('Job Type')
										->setPlaceholder('Select Job Type')
										->setSplit('col-md-4', 'col-md-8')
										->setName('jobtype')
										->setId('job_type')
										->setList($job_list)
										->setValue($jobtype)
										->setValidation('required')
										->draw($show_input);

								?>
							</div>
							<div class="col-md-6">
								<?php
									echo $ui->formField('text')
										->setLabel('Target Date ')
										->setSplit('col-md-4', 'col-md-8')
										->setName('targetdate')
										->setId('targetdate')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($targetdate)
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
										->setMaxLength(20)
										->setValidation('required')
										->draw($show_input);
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								&nbsp;
							</div>
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setLabel('Discount Type ')
										->setPlaceholder('None')
										->setSplit('col-md-4', 'col-md-8')
										->setName('discounttype')
										->setId('discount_type')
										->setList($discount_type_list)
										->setNone("None")
										->setValue($discount_type)
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
										->setMaxLength(300)
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
							<th class="col-md-2">Item</th>
							<th class="col-md-2">Description</th>
							<th style="width:50px;" class="text-center">w/ Warranty</th>
							<th class="col-md-1">Warehouse</th>
							<th class="col-md-1 text-right">Qty</th>
							<th class="col-md-1 text-center">UOM</th>
							<th class="col-md-1 text-right">Price</th>
							<th class="col-md-1 text-right">Discount</th>
							<th class="col-md-1 text-center">Tax</th>
							<th class="col-md-2 text-right">Amount</th>
							<?php if ($show_input): ?>
							<th style="width: 50px;"></th>
							<?php endif ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($voucher_details as $key => $row) { ?>
						<?php 
							$checkval = ($row->haswarranty == 'Yes') ? 'check' : 'uncheck';
							if($row->parentcode != ''){
								$attrdisabled 	= array('disabled',true);
								$attrreadonly 	= array('readonly', 'readonly');
								$trprop 	  	= 'class="subitem'.$row->parentline.'"';
								$discountstat 	= array('readonly', 'readonly');
								$discountclass 	= '';
							}
							else{
								$attrdisabled 	= array('', '');
								$attrreadonly 	= array('', '');
								$trprop 		= 'class="item'.$row->linenum.' items"';
								$discountclass 	= 'discount';

								if ($row->discounttype == 'none') 
									$discountstat = array('readonly', 'readonly');
								else
									$discountstat = array();
							}

						?>
						<tr <?=$trprop?>>
							<td>
								<?php 
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Item')
										->setSplit('', 'col-md-12')
										->setName('itemcode[]')
										->setClass('itemcode')
										->setAttribute($attrdisabled)
										->setList($item_list)
										->setValue($row->itemcode)
										->setValidation('required')
										->draw($show_input);
								?>
								<?php if($row->parentcode != ''): ?>
								<input type="hidden" class='itemcode' 	name="itemcode[]"" 	value="<?=$row->itemcode?>">
								<input type="hidden" class='warehouse' 	name="warehouse[]"" value="<?=$row->warehouse?>">
								<input type="hidden" class='taxcode'	name="taxcode[]"" 	value="">
								<?php endif;?>
								<input type="hidden" class='linenum' 	name="linenum[]" 	value='<?=$row->linenum?>'>
								<input type="hidden" class='parentcode' name="parentcode[]" value='<?=$row->parentcode?>'>
								<input type="hidden" class='parentline' name="parentline[]" value='<?=$row->parentline?>'>
								<input type="hidden" class='childqty' 	name="childqty[]" 	value='<?=$row->childqty?>'>
								<input type='hidden' class='haswarranty' name='haswarranty[]' value='<?=$row->haswarranty?>'>
								<input type="hidden" class='isbundle' 	name="isbundle[]" 	value='<?=$row->isbundle?>'>
								<input type='hidden' class='discounttype_details' name='discounttype_details[]' value='<?=$row->discounttype?>'>
								<input type='hidden' class='discountamount' name='discountamount[]' 	 	value='<?=$row->discountamount?>'>
								<input type='hidden' class='taxrate' 	name='taxrate[]' 	value='<?=$row->taxrate?>'>
								<input type='hidden' class='taxamount' 	name='taxamount[]' 	value='<?=$row->taxamount?>'>
							</td>
							<td>
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('detailparticular[]')
										->setClass('detailparticular')
										->setMaxLength(100)
										->setAttribute($attrreadonly)
										->setValue($row->detailparticular)
										->draw($show_input);
								?>
							</td>
							<td class="text-center">
								<?php
									if ($show_input) {
										echo $ui->formField('checkbox')
										->setName('warranty[]')
										->setClass('warranty')
										->setAttribute($attrdisabled)
										->setValue('0')
										->draw(true);
									}
									else{?>

								<div class="form-group">
									<div class="col-md-12">
										<p class = 'form-control-static'><?=$row->haswarranty?></p>	
									</div>
								</div>

									<?php }?>

							</td>
							<td>
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Warehouse')
										->setSplit('', 'col-md-12')
										->setName('warehouse[]')
										->setClass('warehouse')
										->setAttribute($attrdisabled)
										->setList($warehouse_list)
										->setValue($row->warehouse)
										->setValidation('required')
										->draw($show_input);
								?>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('quantity[]')
										->setClass('quantity text-right')
										->setAttribute($attrreadonly)
										->setValidation('required integer')
										->setValue($row->qty+0)
										->draw($show_input);
								?>
								
							</td>
							<td class="text-center">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('uom[]')
										->setClass('uom text-right')
										->setAttribute(
											array("maxlength" => "20",'readOnly' => 'readOnly')
										)
										->setValue($row->uom)
										->draw($show_input);
								?>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('unitprice[]')
										->setClass('unitprice text-right')
										->setAttribute($attrreadonly)
										->setValidation('required decimal')
										->setValue(number_format($row->unitprice,2))
										->draw($show_input);
								?>
							</td>
							<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('discount[]')
										->setClass($discountclass.' text-right')
										->setAttribute($discountstat)
										->setValidation('required decimal')
										->setValue(number_format($row->discountrate,2))
										->draw($show_input);
								?>
							</td>
							<td class="<?=($ajax_task=='view')? 'text-center':'';?>">
								<?php
									
									echo $ui->formField('dropdown')
										->setSplit('', 'col-md-12')
										->setName('taxcode[]')
										->setClass('taxcode')
										->setAttribute($attrdisabled)
										->setList($taxrate_list)
										->setNone('None')
										->setValue($row->taxcode)
										->draw($show_input);
								?>
								
							</td>

							<td class="text-right">
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('amount[]')
										->setClass('amount text-right')
										->setAttribute(
											array(
												'readOnly' => 'readOnly'
											)
										)
										->setValidation('required decimal')
										->setValue(number_format($row->amount,2))
										->draw($show_input);
								?>
							</td>
							<?php if ($row->parentcode != '' && $show_input):?>
							<td>
								<button type="button" class="btn btn-danger btn-flat delete_row" style="outline:none;" disabled>
									<span class="glyphicon glyphicon-trash"></span>
								</button>
							</td>
							<?php elseif ($show_input): ?>
							<td>
								<button type="button" class="btn btn-danger btn-flat delete_row" style="outline:none;">
									<span class="glyphicon glyphicon-trash"></span>
								</button>
							</td>
							<?php endif ?>
						</tr>

						<script>
							$('.item<?=$row->linenum;?>').find('.warranty').iCheck('<?=$checkval?>');
							$('.subitem<?=$row->parentline;?>').find('.warranty').iCheck('<?=$checkval?>');
						</script>

					<?php } ?> 
					</tbody>
					<tfoot class="summary">
						<tr>
							<td>
								<?php if ($show_input): ?>
									<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
								<?php endif ?>
							</td>
							<td class="right" colspan="8">
								<label class="control-label col-md-12">VATable Sales</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('vat_sales')
											->setId('vat_sales')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($vatable_sales,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">
								<label class="control-label col-md-12">VAT Exempt Sales</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('exempt_sales')
											->setId('exempt_sales')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($exempt_sales,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">
								<label class="control-label col-md-12">Total Sales</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('t_sales')
											->setId('t_sales')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($t_sales,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">
								<label class="control-label col-md-12">Add 12% VAT</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('t_vat')
											->setId('t_vat')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($t_vat,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">&nbsp;</td>
							<td class="text-right" >
								<hr/>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">
								<label class="control-label col-md-12">Total Amount</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('t_amount')
											->setId('t_amount')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($t_amount,2))
											->draw($show_input);
								?>
							</td>
							
						</tr>
						<tr>
							<td class="right" colspan="9">
								<label class="control-label col-md-12">Discount</label>
							</td>
							<td class="text-right" >
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12 col-sm-12')
											->setName('t_discount')
											->setId('t_discount')
											->setClass("input_label text-right remove-margin")
											->setAttribute(array("readOnly"=>"readOnly"))
											->setValue(number_format($t_discount,2))
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
							if ($show_input) {
								echo $ui->drawSubmitDropdown(true, isset($ajax_task) ? $ajax_task : '');
							}
							else{ 
								if ($stat=='Pending') {
							?>
							<a href="<?=MODULE_URL;?>edit/<?=$voucherno;?>" class='btn btn-primary'> Edit</a>
							<?php 
								}
							}
							echo '&nbsp;&nbsp;&nbsp;';
							echo $ui->drawCancel();
						?>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?if(!$show_input && !empty($filename)):?>
	<div id="Attachment" class="tab-pane">
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
										<td>
											<button type="button" id="replace_attachment" data-voucherno='<?=$voucherno;?>' name="replace_attachment" class="btn btn-primary">Replace</button>
										</td>
										<td><a href="<?=$filepath;?>" target='_blank'><?=$filename?></a></td>
										<td><?=$filetype?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br/>
			</form>
		</div>
	</div>
	<?php endif;?>

<div class="modal fade" id="discounttypeModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
			</div>
			<div class="modal-body" id="message">
				Changing the Current Discount Type will clear out the Discounts. Do you wish to proceed?
			</div>
			<div class="modal-footer text-center">
				<button type="button" class="btn btn-info btn-flat" id="disc_yes" data-dismiss='modal'>Yes</button>
				<button type="button" class="btn btn-default btn-flat" id="disc_no" >No</button>
			</div>
		</div>
	</div>
</div>
<?php if (!$show_input) :?>
<div id="attach_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
		<form method = "post" id="attachments_form" enctype="multipart/form-data">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Attach File for <span id="modal-voucher"></span></h4>
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
				<p class="help-block">The file to be imported shall not exceed the size of <strong>3mb</strong> and must be a <strong>PDF, PNG or JPG</strong> file.</p>
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
<? endif;?>
</section>
<script>
	var delete_row	= {};
	var ajax		= {};
	var ajax_call	= '';
	var min_row		= 1;
	var prev_discountype = '<?=$discount_type;?>';

	function addVoucherDetails() {
		var linenum = $('tableList tbody tr').length + 1;
		var row = `<tr class='item`+ linenum +` items'>
					<td>
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Item')
								->setSplit('', 'col-md-12')
								->setName('itemcode[]')
								->setClass('itemcode')
								->setList($item_list)
								->setValue('')
								->setValidation('required')
								->draw($show_input);
						?>
						<input type='hidden' name='linenum[]' 		class='linenum' 	value=''>
						<input type="hidden" name="parentcode[]" 	class='parentcode' 	value=''>
						<input type="hidden" name="parentline[]" 	class='parentline' 	value=''>
						<input type="hidden" name="childqty[]" 		class='childqty' 	value='0'>
						<input type="hidden" name="isbundle[]" 		class='isbundle' 	value='No'>
						<input type='hidden' name='haswarranty[]' 	class='haswarranty'	value='No'>
						<input type='hidden' name='discounttype_details[]' class='discounttype_details' value=''>
						<input type='hidden' name='discountamount[]' 	class='discountamount' 	value=''>
						<input type='hidden' name='taxrate[]' 			class='taxrate' 		value=''>
						<input type='hidden' name='taxamount[]' 		class='taxamount' 		value=''>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('detailparticular')
								->setMaxLength(100)
								->setValue('')
								->draw($show_input);
						?>
					</td>
					<td class="text-center">
						<?php
							echo $ui->formField('checkbox')
									->setName('warranty[]')
									->setClass('warranty')
									->setValue('0')
									->draw($show_input);

						?>
					</td>
					<td>
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Warehouse')
								->setSplit('', 'col-md-12')
								->setName('warehouse[]')
								->setClass('warehouse')
								->setList($warehouse_list)
								->setValue('')
								->setValidation('required')
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('quantity[]')
								->setClass('quantity text-right')
								
								->setValidation('required integer')
								->setValue('0')
								->draw($show_input);
						?>
						
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('uom[]')
								->setClass('uom text-right')
								->setAttribute(
									array("maxlength" => "20",'readOnly' => 'readOnly')
								)
								->setValue("")
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('unitprice[]')
								->setClass('unitprice text-right')
								->setValidation('required decimal')
								->setValue(number_format(0,2))
								->draw($show_input);
						?>
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('discount[]')
								->setClass('discount text-right')
								->setAttribute(array('readonly'=>true))
								->setValidation('required decimal')
								->setValue(number_format(0,2))
								->draw($show_input);
						?>
						
					</td>
					<td>
						<?php
							
							echo $ui->formField('dropdown')
								->setSplit('', 'col-md-12')
								->setName('taxcode[]')
								->setClass('taxcode')
								->setList($taxrate_list)
								->setNone('None')
								->setValue('')
								->draw($show_input);
						?>
						
					</td>
					<td class="text-right">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('amount[]')
								->setClass('amount text-right')
								->setAttribute(
									array(
										
										'readOnly' => 'readOnly'
									)
								)
								->setValidation('required decimal')
								->setValue(number_format(0,2))
								->draw($show_input);
						?>
					</td>
					<?php if ($show_input): ?>
					<td>
						<button type="button" class="btn btn-danger btn-flat delete_row" style="outline:none;">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</td>
					<?php endif ?>
				</tr>

		`;
		
		$('#tableList tbody').append(row);
		drawTemplate();
		checkDiscountType();
	}
	function getItemDetails(itemcode, element){
		var row 		= element.closest('tr')
		var parentline 	= row.find('.linenum').val();
		var customer 	= $('#customer').val();
		$.post("<?=MODULE_URL?>ajax/get_item_details","itemcode="+itemcode, function(data){

			row.find(".detailparticular").val(data.itemdesc);
			row.find(".warranty").iCheck('uncheck');
			row.find(".haswarranty").val('No');
			row.find(".warehouse").val('');
			row.find(".quantity").val('0');
			row.find(".uom").val(data.uom)
			row.find(".unitprice").val(addComma(data.itemprice));
			row.find(".discount").val('0.00');
			row.find(".taxcode").val('none');

			if (data.isbundle == 1) 
				row.find(".isbundle").val('Yes');
			else{
				row.find(".isbundle").val('No');
				row.nextAll('tr.subitem'+parentline).remove();
			}

				
			$.post('<?=MODULE_URL?>ajax/get_bundle_items',"itemcode="+itemcode+"&linenum="+parentline, function(bundle) {

				if(bundle.table != "" && customer != '') {

					var table = bundle.table;
					$('#tableList tbody tr select').select2('destroy');
					element.closest('tr.item'+parentline).after(table);
					$('.subitem'+parentline).find('.warehouse').val('');
					setLineNum();
				} 

				drawTemplate();
			});

		});
		
	}
	function setLineNum(){
		var static_linenum = 0;
		$.each($('.linenum'),function(index){
			var linenum = index+1;
			var isparent = $(this).closest('tr').hasClass('items');

			$(this).val(linenum);

			if (isparent) {
				static_linenum = linenum;
				$(this).closest('tr').attr('class', 'items item'+linenum);
			}
			else
				$(this).closest('tr').attr('class', 'subitem'+static_linenum);
			
		});
	}
	function checkDiscountType(){
		if ($('#discount_type').val() == 'amt' || $('#discount_type').val() == 'perc') 
			$('.discount').prop('readonly',false);	
		
		else
			$('.discount').prop('readonly',true);	
	}
	function addCommas(nStr){
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	function recomputeAll() {
		var taxrates = <?php echo $taxrates ?>;
		var discount_type = $("#discount_type").val();
		var vat_sales 		= 0;
		var vatexempt_sales = 0;
		var total_sales 	= 0;
		var vat_total 		= 0;
		var total_amount	= 0;
		var total_discount 	= 0;

		if ($('#tableList tbody tr .unitprice').length) {
			$('#tableList tbody tr.items').each(function() {
				var discount 	= removeComma($(this).find(".discount").val()) ;
				var unitprice 	= removeComma($(this).find('.unitprice').val());
				var quantity 	= removeComma($(this).find('.quantity').val());
				var amount 		= (unitprice * quantity);
				var tax 		= $(this).find('.taxcode').val();
				var taxrate 	= taxrates[tax] || 0;
				var itemdiscount= 0;

				if (discount_type == "amt")
					itemdiscount	= discount

				else if(discount_type == "perc")
					itemdiscount 	= amount * (discount/100);


				if (amount >= itemdiscount) {
					
					total_discount 	+= itemdiscount;
					amount 			= amount - itemdiscount;
					if (taxrate > 0) {
						var taxamount 	= amount * taxrate;
						vat_sales 		+= amount
						vat_total 		+= taxamount;
					}
					else
						vatexempt_sales += amount;

					$(this).find(".discount").parent().parent().removeClass("has-error");
				}
				else{
					$(this).find(".discount").parent().parent().addClass("has-error");
				}
				$(this).find('.taxrate').val(taxrate);
				$(this).find('.taxamount').val(taxamount);
				$(this).find('.discounttype_details').val(discount_type);
				$(this).find('.discountamount').val(itemdiscount);
				$(this).find('.amount').val(addComma(amount));
			});

			total_sales 	= vat_sales + vatexempt_sales;
			total_amount 	= total_sales + vat_total;
			$("form").find("#vat_sales").val(addCommas(vat_sales.toFixed(2)));
			$("form").find("#exempt_sales").val(addCommas(vatexempt_sales.toFixed(2)));
			$("form").find("#t_sales").val(addCommas(total_sales.toFixed(2)));
			$("form").find("#t_vat").val(addCommas(vat_total.toFixed(2)));
			$("form").find("#t_amount").val(addCommas(total_amount.toFixed(2)));
			$("form").find("#t_discount").val(addCommas(total_discount.toFixed(2)));
		}
	}

</script>

<?php if ($show_input): ?>
<script>

	$('#section_tab li a').on('click',function(){
		var section = $('#section_tab li:active a').attr('href');

	});

	$(document).ready(function(){

		if ($('#tableList tbody tr').length < 1) {
			addVoucherDetails();
			setLineNum();
		}
		else
			recomputeAll();
	});
	
	$('body').on('click', '#addNewItem', function() {
		addVoucherDetails();
		setLineNum();
	});

	$('body').on('click', '.delete_row', function() {
		var row 		= $(this).closest('tr');
		var linenum 	= row.find('.linenum').val();
		var isbundle 	= row.find('.isbundle').val();

		if(isbundle == 'Yes')	
			row.nextAll('tr.subitem'+linenum).remove();
		row.remove();


		if ($('#tableList tbody tr').length < min_row) 
			addVoucherDetails();

		setLineNum();
	});

	$('#tableList tbody').on('change', '.itemcode', function(e) {
		var customer 	=	$('#customer').val();

		if( customer != "" ){
			var itemcode = $(this).val();
			getItemDetails(itemcode, $(this));
		}
		else{	
			$(this).val('');
			drawTemplate();
			$('#customer').focus();
		}

	});

	$('#tableList tbody').on('input', '.quantity', function(e) {
		var formgroup 	= $(this).parent().parent();
		var value 		= removeComma($(this).val());
		var linenum 	= $(this).closest('tr').find('.linenum').val();
		var isbundle 	= $(this).closest('tr').find('.isbundle').val();

		if ($(this).closest('tr').hasClass('items')) {
			if (value < 1) 
				formgroup.addClass('has-error');
			else
				formgroup.removeClass('has-error');
		}
		if (isbundle == 'Yes') {

			$.each($('.subitem'+linenum), function(){

				var subitemqty 	= $(this).closest('tr').find('.childqty').val();

				subitemqty 		= subitemqty * value;

				$(this).find('.quantity').val(subitemqty);

			});
		}
	});

	$('#tableList tbody').on('change', '.warehouse', function(e) {
		var value 		= $(this).val();
		var linenum 	= $(this).closest('tr').find('.linenum').val();
		var isbundle 	= $(this).closest('tr').find('.isbundle').val();

		if (isbundle == 'Yes') {

			$.each($('.subitem'+linenum), function(){
				$(this).find('.warehouse').val(value);
			});
			drawTemplate();
		}
	});

	$('#tableList tbody').on('input', '.unitprice', function(e) {
		var value 		=	removeComma($(this).val());
		var formgroup 	= $(this).parent().parent();

		if( value >= 0 ){
			formgroup.removeClass('has-error');
		}
		else{	
			formgroup.addClass('has-error');
		}
	});

	$('#tableList tbody').on('blur', '.discount, .quantity', function(e) {
		var value 	= removeComma($(this).val());

		if (value == '') 
			$(this).val('0.00');
	});

    $("#tableList tbody").on("ifChecked", ".warranty", function(){
        var linenum = $(this).closest('tr').find('.linenum').val();
        var isbundle = $(this).closest('tr').find('.isbundle').val();

        $(this).closest('tr').find('.haswarranty').val('Yes');

        if (isbundle == 'Yes') {
			$.each($('.subitem'+linenum), function(){
				$(this).find('.warranty').iCheck('check');
				$(this).find('.haswarranty').val('Yes');
			});
		}

    });

	$("#tableList tbody").on("ifUnchecked", ".warranty", function(){
        var linenum = $(this).closest('tr').find('.linenum').val();
        var isbundle = $(this).closest('tr').find('.isbundle').val();

        $(this).closest('tr').find('.haswarranty').val('No');

        if (isbundle == 'Yes') {

			$.each($('.subitem'+linenum), function(){

				$(this).find('.warranty').iCheck('uncheck');
				$(this).find('.haswarranty').val('No');

			});
		}
    });

	$('#tableList tbody').on('change', '.warehouse', function(e) {
		var warehouse 	= $(this).val();
		var formgroup 	= $(this).parent().parent();

		if( warehouse != '' )
			formgroup.removeClass('has-error');
		
		else
			formgroup.addClass('has-error');
		
	});

	$("#discount_type").on("change", function(){
		if (prev_discountype != 'none'){
			$('#discounttypeModal').modal('show');
		}
		else{
			prev_discountype = $(this).val();
			$('#tableList tbody tr.items .discount').prop('readonly', false);
		}
	});

	$('#disc_yes').on('click', function(){
		$('#tableList tbody tr.items .discount').val('0.00');
		prev_discountype = $('#discount_type').val();

		if ($('#discount_type').val()=='none')
			$('#tableList tbody tr.items .discount').prop('readonly', true);

		else
			$('#tableList tbody tr.items .discount').prop('readonly', false);
			
		recomputeAll();
		$('#discounttypeModal').modal('hide');
	});

	$('#disc_no').on('click', function(){
		$('#discount_type').val(prev_discountype).trigger('change');
		$('#discounttypeModal').modal('hide');
	});

	$('#tableList tbody').on('input change blur', '.unitprice, .quantity, .discount, .taxcode', function() {
		var row 		= $(this).closest("tr");
		var amount 	 	= 0;
		var discounttype = $('#discount_type').val();
		var unitprice 	= removeComma(row.find(".unitprice").val());
		var quantity 	= removeComma(row.find(".quantity").val());
		var discount 	= removeComma(row.find('.discount'));
		var formgroup 	= row.find('.discount').parent().parent();

		if (unitprice >= 0 && quantity > 0) 
			recomputeAll();
		
	});
	
	$('form').on('click', '[type="submit"]', function(e) {
		e.preventDefault();
		recomputeAll();
		setLineNum();

		var form_element = $(this).closest('form');
		var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
		
		form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');

		$('#submit_container [type="submit"]').attr('disabled', true);

		$('.items .quantity').each(function() {

			if( $(this).val() < 1){
				$(this).parent().parent().addClass('has-error');
			}
		});
		
		
		if (form_element.find('.has-error').length < 1) {
			if ($('.quantity:not([readonly])').length > 0) {
				
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + submit_data , function(data){
				 
					if (data.query1 && data.query2) {
						$('#delay_modal').modal('show');console.log(data.task);
						setTimeout(function(){window.location = '<?=MODULE_URL?>'+data.task;}, 1000);
					} else {
						$('#submit_container [type="submit"]').attr('disabled', false);
					}
				});
			} 
			else {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Add an Item');
				$('#submit_container [type="submit"]').attr('disabled', false);
			}
		} 
		else {
			form_element.find('.form-group.has-error').first().find('input, textarea, select').focus();
			$('#submit_container [type="submit"]').attr('disabled', false);
		}
	});
</script>
<?php else: ?>
<script>
	$(function(){
		$('#Attachment').hide();
	})

	$('#nav li a').on('click', function(){
		$('#nav li').removeClass();
		$('#Details').hide();
		$('#Attachment').hide();

		$(this).closest('li').attr('class','active');
		var tab = $('#nav li.active a').attr('href');
		$('#'+tab).show();
	});

	$('#replace_attachment').on('click', function(){
		var voucherno = $(this).data('voucherno');
		$('#modal-voucher').html(voucherno);
		$('#input_voucherno').val(voucherno);
		$('#attach_modal').modal('show');
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
		var voucherno 	=  $('#input_voucherno').val();
		var task 		=  "view";
		data.formData = {reference: voucherno, task: task};
	});
	$('#attachments_form').bind('fileuploadalways', function (e, data) {
		var error = data.result['files'][0]['error'];
		var form_group = $('#attachments_form #files').closest('.form-group');
		if(!error){
			$('#attach_modal').modal('hide');
			var msg = data.result['files'][0]['name'];
			form_group.removeClass('has-error');
			form_group.find('p.help-block.m-none').html('');

			$('#attachments_form #files').closest('.input-group').find('.form-control').html('');
			$('#delay_modal').modal('show');
			setTimeout(function(){window.location = '<?=MODULE_URL?>view/<?=$voucherno;?>';}, 1000);
		}else{
			var msg = data.result['files'][0]['name'];
			form_group.addClass('has-error');
			form_group.find('p.help-block.m-none').html(msg);
		}
	});
});
</script>
<?php endif ?>