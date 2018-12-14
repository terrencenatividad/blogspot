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
											<label for="voucherno" class="control-label col-md-4">Service Quotation No.</label>
											<div class="col-md-8">
												<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
											</div>
										</div>
									<?php else: ?>
										<?php
											echo $ui->formField('text')
												->setLabel('Service Quotation No.')
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
											->setLabel('Job Type')
											->setPlaceholder('Select Job Type')
											->setSplit('col-md-4', 'col-md-8')
											->setName('job_type')
											->setId('job_type')
											->setList($job_list)
											->setValue($job_type)
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
											->setName('discount_type')
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
							<tr class="clone">
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
										echo $ui->loadElement('check_task')
												->addCheckbox()
												->setValue('')
												->draw();
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
											->setValue('')
											->draw($show_input);
									?>
								</td>
								<td class="text-right">
									<?php
										echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('price[]')
											->setClass('price text-right')
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
											->setAttribute(array('disabled'=>'true'))
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
											->setNone('none')
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
												->setName('t_vatable_sales')
												->setId('t_vatable_sales')
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
												->setName('t_vat_exempt_sales')
												->setId('t_vat_exempt_sales')
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
												->setName('t_vatsales')
												->setId('t_vatsales')
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
								if ($stat == 'Prepared' && $restrict_dr || empty($stat)) {
									echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
								}
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
		// function addVoucherDetails(details) {
		// 	var details = details || {itemcode: '', detailparticular: '', warranty: '', warehouse: '', quantity: '0', uom: 'PCS', price: '0.00', discount: '0.00', amount: '0.00', taxcode: '', taxrate: '',taxamount: '0.00'};
			

		// 	var row = `
				
		// 	`;

		// 	$('#tableList tbody').append(row);

		// 	if (details.itemcode != '') {
		// 		$('#tableList tbody').find('tr:last .itemcode').val(details.itemcode);
		// 	}
		// 	if (details.warehouse != '') {
		// 		$('#tableList tbody').find('tr:last .warehouse').val(details.warehouse);
		// 	}
		// 	if (details.taxcode != '') {
		// 		$('#tableList tbody').find('tr:last .taxcode').val(details.taxcode);
		// 	}

		// 	try {
		// 		drawTemplate();
		// 	} catch(e) {};

		// 	var itemlist = <?= json_encode($item_list) ?>;
		// 	itemlist.forEach(function(item) {
		// 		if (item.ind == details.itemcode) {
		// 			$('#temp_view_' + index).html(item.val);
		// 		}
		// 	});

		// 	var warehouselist = <?= json_encode($warehouse_list) ?>;
		// 	warehouselist.forEach(function(warehouse) {
		// 		if (warehouse.ind == details.warehouse) {
		// 			$('#temp_view_warehouse_' + index).html(warehouse.val);
		// 		}
		// 	});

		// 	var taxrate_list = <?= json_encode($taxrate_list) ?>;
		// 	taxrate_list.forEach(function(tax) {
		// 		if (tax.ind == details.taxcode) {
		// 			$('#temp_view_taxrate_' + index).html(tax.val);
		// 		}
		// 	});

		// 	if (details.warranty == 'yes') {
		// 		$(this).removeAttr('readonly').val($(this).attr('data-value'));
		// 		$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
		// 	} 
		// 	else {
		// 		$('#tableList tbody').find('tr:last .warranty_hidden').attr('readonly', '').val(0);
		// 		$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
		// 	}
		// }

		function getItemDetails(itemcode, row){
			$.post("<?=MODULE_URL?>ajax/get_item_details","itemcode="+itemcode , function(data){
				
				switch(data.uom){
					case "Pieces":
						uom = "PCS";
					case "Kilo":
						uom = "KG";
					case "Gram":
						uom = "GRAM";
					default:
						uom = "PCS";
				}
				row.closest("tr").find(".detailparticular").val(data.itemdesc);
				row.closest("tr").find(".price").val(addComma(data.itemprice));
				row.closest("tr").find(".uom").val(uom);
				console.log(data.itemdesc);
			});
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
			var taxrates = <?php echo $taxrates ?>;
			var discount_type = $("#discount_type").val();
			var vat_sales 		= 0;
			var vatexempt_sales = 0;
			var total_sales 	= 0;
			var vat_total 		= 0;
			var total_amount	= 0;
			var total_discount 	= 0;

			if ($('#tableList tbody tr .price').length) {
				$('#tableList tbody tr').each(function() {
					var discount 	= $(this).find(".discount").val();
					var price 		= removeComma($(this).find('.price').val());
					var quantity 	= removeComma($(this).find('.quantity').val());
					var amount 		= (price * quantity);
					var tax 		= $(this).find('.taxcode').val();
					var taxrate 	= taxrates[tax] || 0;
					

					if (discount_type != "none" && discount_type != ""){
						itemdiscount 	= (discount_type == "amt") ? discount : discount/100; 
						total_discount 	+= itemdiscount;
						amount 			= amount - itemdiscount;
					}
					
					if (taxrate > 0) {
						var taxamount 	= amount * taxrate;
						vat_sales 		+= amount
						vat_total 		+= taxamount;
					}
					else
						vatexempt_sales += amount;

					$(this).find('.amount').val(addComma(amount));
				});

				total_sales 	= vat_sales + vatexempt_sales;
				total_amount 	= total_sales + vat_total;
				$("form").find("#t_vatable_sales").val(addCommas(vat_sales.toFixed(2)));
				$("form").find("#t_vat_exempt_sales").val(addCommas(vatexempt_sales.toFixed(2)));
				$("form").find("#t_vatsales").val(addCommas(total_sales.toFixed(2)));
				$("form").find("#t_vat").val(addCommas(vat_total.toFixed(2)));
				$("form").find("#t_amount").val(addCommas(total_amount.toFixed(2)));
				$("form").find("#t_discount").val(addCommas(total_discount.toFixed(2)));
			}
		}

		$('#tableList tbody').on('input change blur', '.price, .quantity, .discount, .taxcode', function() {
			recomputeAll();
		});

		var header_values 	= <?php echo $header_values ?>;
		var voucher_details = <?php echo $voucher_details ?>;
		displayHeader(header_values);
		displayDetails(voucher_details);
	</script>

	<?php if ($show_input): ?>

	<script>
		$('body').on('click', '#addNewItem', function() 
		{
			$('#tableList tbody tr.clone select').select2('destroy');
			
			var clone = $("#tableList tbody tr.clone:first").clone(true); 

			var ParentRow = $("#tableList tbody tr.clone").last();
			
			clone.clone(true).insertAfter(ParentRow);
			$('#tableList tbody tr.clone select').select2({width: "100%"});
		});

		$('.itemcode').on('change', function(e) {
			var customer 	=	$('#customer').val();
			
			if( customer != "" )
			{
				var itemcode = $(this).val();
				getItemDetails(itemcode, $(this));

			}
			else
			{
				$(this).find(".itemcode").val('');
				$('#customer').focus();
			}
		});
		
		$("#discount_type").on("change", function(){
			
			if ($(this).val()=="none" || $(this).val()=="") {
				$(".discount").prop("disabled",true);
			}
			else
				$(".discount").prop("disabled",false);
		});
		
		$('body').on('click', '.delete_row', function() {
			delete_row = $(this).closest('tr');
			delete_row.remove();
			if ($('#tableList tbody tr').length < min_row) {
				addVoucherDetails();
			}
		});
		
		$('form').on('click', '[type="submit"]', function(e) {
			e.preventDefault();
			console.log($('form').serialize());
			var form_element = $(this).closest('form');
			var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
			recomputeAll();
			// $('#submit_container [type="submit"]').attr('disabled', true);
			// form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');
			// if (form_element.find('.form-group.has-error').length == 0) {
			// 	var items = 0;
			// 	$('.issueqty:not([readonly])').each(function() {
			// 		items += removeComma($(this).val());
			// 	});
			// 	if ($('.issueqty:not([readonly])').length > 0 && items > 0) {
			// 		$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data , function(data) {
			// 			if (data.success) {
			// 				$('#delay_modal').modal('show');
			// 				setTimeout(function() {							
			// 					window.location = data.redirect;						
			// 				}, 1000)
			// 			} else {
			// 				$('#submit_container [type="submit"]').attr('disabled', false);
			// 			}
			// 		});
			// 	} else {
			// 		$('#warning_modal').modal('show').find('#warning_message').html('Please Add an Item');
			// 		$('#submit_container [type="submit"]').attr('disabled', false);
			// 	}
			// } else {
			// 	form_element.find('.form-group.has-error').first().find('input, textarea, select').focus();
			// 	$('#submit_container [type="submit"]').attr('disabled', false);
			// }
		});
	</script>
	<?php endif ?>