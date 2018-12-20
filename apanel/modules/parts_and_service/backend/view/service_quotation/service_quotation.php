<section class="content">
	<?php if($ajax_task == 'view'):?>
	<ul id="section_tab" class="nav nav-tabs">
		<li class="active"><a href="Details" data-toggle="tab">Details</a></li>
		<li><a href="Attachment" data-toggle="tab">Attachment</a></li>
	</ul>
	<?php endif;?>
	<div id='details_section' class="box box-primary">
		<form action="" method="post" class="form-horizontal">
			<div class="box-body">
				<br>
				<div class="row">
					<div class="col-md-11">
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
							<th class="col-md-1">Tax</th>
							<th class="col-md-2 text-right">Amount</th>
							<?php if ($show_input): ?>
							<th style="width: 50px;"></th>
							<?php endif ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($voucher_details as $key => $row) { ?>
						<?php 
							if($row->parentcode != ''){
								$attrdisabled = array('disabled',true);
								$attrreadonly = array('readonly', 'readonly');
								$trprop 	  = 'subitem'.$row->parentline;
							}
							else{
								$attrdisabled = array('', '');
								$attrreadonly = array('', '');
								$trprop 	= 'class="item'.$row->linenum.' items" data-linenum="'.$row->linenum.'" data-isbundle="1"';
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
<input type="hidden" class='linenum' 	name="linenum[]" 	value=''>
<input type="hidden" class='parentcode' name="parentcode[]" value='<?=$row->parentcode?>'>
<input type="hidden" class='parentline' name="parentline[]" value='<?=$row->parentline?>'>
<input type="hidden" class='childqty' 	name="childqty[]" 	value='<?=$row->childqty?>'>
<input type='hidden' class='haswarranty' name='haswarranty[]' value='<?=$row->haswarranty?>'>
<input type="hidden" class='isbundle' 	name="isbundle[]" 	value='<?=$row->isbundle?>'>
<input type='hidden' class='discountamount' name='discountamount[]' value='<?=$row->discountamount?>'>
<input type='hidden' class='discounttype_details' name='discounttype_details[]' value='<?=$row->discounttype?>'>
<input type='hidden' class='taxrate' 	name='taxrate[]' 	value='<?=$row->taxrate?>'>
<input type='hidden' class='taxamount' 	name='taxamount[]' 	value='<?=$row->taxamount?>'>
							</td>
							<td>
								<?php
									echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('detailparticular[]')
										->setClass('detailparticular')
										->setAttribute($attrreadonly)
										->setValue($row->detailparticular)
										->draw($show_input);
								?>
							</td>
							<td class="text-center">
								<?php
									echo $ui->formField('checkbox')
									->setName('warranty[]')
									->setClass('warranty')
									->setAttribute($attrdisabled)
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
										->setValue($row->qty)
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
										->setClass('discount text-right')
										->setAttribute(array('readonly'=>'true'))
										->setValidation('required decimal')
										->setValue(number_format($row->discountrate,2))
										->draw($show_input);
								?>
							</td>
							<td>
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
											->setValue(number_format($t_vatable_sales,2))
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
											->setValue(number_format($t_vat_exempt_sales,2))
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
											->setValue(number_format($t_vatsales,2))
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
							
							echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
							
							echo '&nbsp;&nbsp;&nbsp;';
							echo $ui->drawCancel();
						?>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
<script>
	var delete_row	= {};
	var ajax		= {};
	var ajax_call	= '';
	var min_row		= 1;
	function addVoucherDetails() {
		var linenum = $('#tableList tbody tr.items').length + 1;
		var row = `<tr class='item`+ linenum +` items' data-linenum='`+ linenum +`'>
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
		var parentline = element.closest('tr').data('linenum');
		var customer = $('#customer').val();

		$.post("<?=MODULE_URL?>ajax/get_item_details","itemcode="+itemcode, function(data){
			element.closest("tr").find(".detailparticular").val(data.itemdesc);
			element.closest("tr").find(".unitprice").val(addComma(data.itemprice));
			element.closest("tr").find(".uom").val(data.uom);
			element.closest("tr").data('isbundle', data.isbundle);
			$.post('<?=MODULE_URL?>ajax/get_bundle_items',"itemcode="+itemcode+"&linenum="+parentline, function(bundle) {
				if(bundle.table != "" && customer != '') {
					var table = bundle.table;
					
					$('#tableList tbody tr select').select2('destroy');
					element.closest('tr.item'+parentline).after(table);
				} else {
					if(element.closest('tr').data('isbundle')==1)	{ 
						element.closest('tr').nextAll('tr.subitem'+parentline).remove();
					}
				}
				drawTemplate();
			});
			$.each($('tr.subitem'+parentline), function(){
				$(this).find('input').attr('readonly','readonly');
				$(this).find('select').attr('readonly','readonly');
			});
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

				else if(discount_type == "perc" && discount < 101)
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
		if ($('#tableList tbody tr').length < 1) 
			addVoucherDetails();
	});
	
	$('body').on('click', '#addNewItem', function() {
		addVoucherDetails();
	});

	$('body').on('click', '.delete_row', function() {
		delete_row = $(this).closest('tr');
		delete_row.remove();
		if ($('#tableList tbody tr').length < min_row) {
			addVoucherDetails();
		}
		if($(this).closest('tr').data('isbundle', data.isbundle)==1)	{ 
			$(this).closest('tr').nextAll('tr.subitem'+linenum).remove();
		}
	});

	$('#tableList tbody').on('change', '.itemcode', function(e) {
		var customer 	=	$('#customer').val();
		if( customer != "" )
		{
			var itemcode = $(this).val();
			getItemDetails(itemcode, $(this));
		}
		else
		{	
			$(this).val('');
			drawTemplate();
			$('#customer').focus();
		}
	});

	$('#tableList tbody').on('input', '.quantity', function(e) {
		var value 	=	removeComma($(this).val());
		if ($(this).closest('tr').hasClass('items')) {
			if (value < 1) 
				$(this).parent().parent().addClass('has-error');
			else
				$(this).parent().parent().removeClass('has-error');
		}
		
		if ($(this).closest('tr').data('isbundle') == 1) {
			var linenum = $(this).closest('tr').data('linenum');
			$.each($('.subitem'+linenum), function(){
				var subitemqty 	= $(this).closest('tr').find('.childqty').val();
				subitemqty 		= subitemqty * value;
				$(this).find('.quantity').val(subitemqty);
			});
		}
	});

	$('#tableList tbody').on('change', '.warehouse', function(e) {
		var value 	= $(this).val();
		var linenum = $(this).closest('tr').data('linenum');
		if ($(this).closest('tr').data('isbundle') == 1) {
			$.each($('.subitem'+linenum), function(){
				$(this).find('.warehouse').val(value);
			});
			drawTemplate();
		}
	});

	$('#tableList tbody').on('input', '.unitprice', function(e) {
		var value 	=	removeComma($(this).val());
		if( value >= 0 ){
			$(this).parent().parent().removeClass('has-error');
		}
		else{	
			$(this).parent().parent().addClass('has-error');
		}
	});

	$('#tableList tbody').on('input', '.discount', function(e) {
		var value 	= removeComma($(this).val());
		var type 	= $('#discount_type').val();
		if (type == 'perc' && value > 100){
			$(this).parent().parent().addClass('has-error');
		}
		else if (type == 'amt' && value < 0){
			$(this).parent().parent().addClass('has-error');
		}
		else{	
			$(this).parent().parent().removeClass('has-error');
		}
	});

	$('#tableList tbody').on('blur', '.discount, .quantity', function(e) {
		var value 	= removeComma($(this).val());
		if (value == '') {
			value = '0.00';

		$(this).val(value);
		}
	});

    $("#tableList tbody").on("ifChecked", ".warranty", function(){
        $(this).closest('tr').find('.haswarranty').val('Yes');
        var linenum = $(this).closest('tr').data('linenum');

        if ($(this).closest('tr').data('isbundle') == 1) {
			$.each($('.subitem'+linenum), function(){
				$(this).find('.warranty').iCheck('check');
				$(this).find('.haswarranty').val('Yes');
			});
		}

    });

	$("#tableList tbody").on("ifUnchecked", ".warranty", function(){
        $(this).closest('tr').find('.haswarranty').val('No');
        var linenum = $(this).closest('tr').data('linenum');

        if ($(this).closest('tr').data('isbundle') == 1) {
			$.each($('.subitem'+linenum), function(){
				$(this).find('.warranty').iCheck('uncheck');
				$(this).find('.haswarranty').val('No');
			});
		}
    });

	$('#tableList tbody').on('change', '.warehouse', function(e) {
		var warehouse 	=	$(this).val();
		if( warehouse != '' )
			$(this).parent().parent().removeClass('has-error');
		
		else
			$(this).parent().parent().addClass('has-error');
		
	});

	$("#discount_type").on("change", function(){
		if ($(this).val()=='none') {
			$('#tableList tbody tr.items .discount').val('0.00');
			$('#tableList tbody tr.items .discount').prop('readonly', true);
		}
		else{
			$('#tableList tbody tr.items .discount').prop('readonly', false);
		}
	});

	$('#tableList tbody').on('input change blur', '.unitprice, .quantity, .discount, .taxcode', function() {
		var row 		= $(this).closest("tr");
		var unitprice 	= removeComma(row.find(".unitprice").val());
		var quantity 	= removeComma(row.find(".quantity").val());
		var discounttype = $('#discount_type').val();
		var discount 	= removeComma(row.find('.discount'));
		var amount 	 	= 0;
		if (unitprice >= 0 && quantity > 0) {
			amount 		= unitprice * quantity;
			if(discounttype=='amt' && amount < discount)
				row.find('.discount').parent().parent().addClass('has-error');
			else if(discounttype=='perc' && discount > 100)
				row.find('.discount').parent().parent().addClass('has-error');
			else{
				row.find('.discount').parent().parent().removeClass('has-error');
				recomputeAll();
			}
		}
	});
	

	$('form').on('click', '[type="submit"]', function(e) {
		e.preventDefault();
		recomputeAll();

		var form_element = $(this).closest('form');
		var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
		
		form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');

		//$('#submit_container [type="submit"]').attr('disabled', true);

		$('.quantity').each(function() {

			if( $(this).val() < 1 && $(this).closest('tr').hasClass('items')){
				$(this).parent().parent().addClass('has-error');
			}
		});
		$linenum = 1;
		$('.linenum').each(function(){
			$(this).val($linenum);
			$linenum++;
		})

		if (form_element.find('.has-error').length < 1) {console.log(form_element.serialize());
			if ($('.quantity:not([readonly])').length > 0) {
				
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() , function(data) {
				 
					if (data.query1) {
						$('#delay_modal').modal('show');
						//setTimeout(function(){window.location = '<?=MODULE_URL?>';}, 1000);
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
<?php endif ?>