<section class = 'content'>
	<div class="box box-primary">
		<form method="post" class="form-horizontal" id="stockTransferForm">
			<div class="box-body">
			
				<div id="header_hidden"></div>
				<?echo $ui->formField('text')
							->setName('total_amount')
							->setId('total_amount')
							->addHidden(true)
							->draw($show_input);
				?>			
				<br/>
					<div class="row">
						<div class="col-md-6">
								<?php if ($show_input): ?>
									<div class="form-group">
										<label for="transactionno" class="control-label col-md-4">Transaction #</label>
										<div class="col-md-8">
											<input type="text" class="form-control" readonly value="<?= (empty($transactionno)) ? ' - Auto Generated -' : $transactionno ?>">
										</div>
									</div>
								<?php else: ?>
									<?php
										echo $ui->formField('text')
											->setLabel('Transaction #')
											->setSplit('col-md-4', 'col-md-8 transaction_input')
											->setName('transactionno')
											->setId('transactionno')
											->setValue($transactionno)
											->setValidation('required')
											->draw($show_input);
									?>
								<?php endif ?>
						</div>
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
										->setLabel('Referenceno #')
										->setSplit('col-md-4', 'col-md-8 reference_input')
										->setName('referenceno')
										->setId('referenceno')
										->setValue($reference)
										//->setValidation('required')
										->draw($show_input);
							?>
						</div>
					</div>	
					<div class="row">
						<div class="col-md-6">
						<?
							echo $ui->formField('dropdown')
									->setLabel('Request From Warehouse')
									->setPlaceholder('Select Warehouse')
									->setSplit('col-md-4', 'col-md-8')
									->setName('site_source')
									->setId('site_source')
									->setList($warehouse_list)
									->setValue($source)
									->setValidation('required')
									->draw($show_input);
						?>
						</div>
						<input id="h_site_source" name="h_site_source" type="hidden" value="<?=$source?>">
						<div class="col-md-6">
						<?
							echo $ui->formField('text')
									->setLabel('Transaction Date:')
									->setSplit('col-md-4', 'col-md-8')
									->setName('transactiondate')
									->setId('transactiondate')
									->setClass('datepicker-input')
									->setAddon('calendar')
									->setValue($transactiondate)
									->setAttribute(array('readonly'=>"","data-date-start-date"=>$close_date))
									->setValidation('required')
									->draw($show_input);
						?>
						</div>		
					</div>	
					<div class="row">
						<div class="col-md-6">
							<?php
							echo $ui->formField('dropdown')
									->setLabel('Destination Warehouse')
									->setPlaceholder('Select Destination Warehouse')
									->setSplit('col-md-4', 'col-md-8')
									->setName('site_destination')
									->setId('site_destination')
									->setList($warehouse_list)
									->setValue($destination)
									->setAttribute(array("readonly"=>""))
									->setValidation('required')
									->draw($show_input);
							?>
						</div>
						<input id="h_site_destination" name="h_site_destination" type="hidden">
						<div class="col-md-6">
							<?php

								echo $ui->formField('text')
									->setLabel('Transfer Date:')
									->setSplit('col-md-4', 'col-md-8')
									->setName('transferdate')
									->setId('transferdate')
									->setClass('datepicker-input')
									->setAddon('calendar')
									->setValue($transferdate)
									->setAttribute(array('readonly'=>"","data-date-start-date"=>$close_date))
									->setValidation('required')
									->draw($show_input);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php
							echo $ui->formField('text')
									->setLabel('Prepared By')
									->setSplit('col-md-4', 'col-md-8')
									->setName('prepared_by')
									->setId('prepared_by')
									->setValue($prepared_by)
									->setValidation('required')
									->draw($show_input);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?
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
			
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover">
					<thead>
						<tr class="info">
							<th >Item Code</th>
							<th>Item Name</th>
							<!--<th>Request Site</th>
							<th>Destination Site</th>-->
							<!-- <th>On Hand Qty</th> -->
							<th>Requested Qty</th>
							<?php if (!$show_input): ?>
								<th>Balance Qty</th>
							<?php endif; ?>
							<th>UOM</th>
							<!-- <th>Price</th>
							<th>Amount</th> -->
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					<?php if ($show_input): ?>
						<tfoot>
							<td colspan="4">
								<button type="button" id="addNewItem" class="btn btn-link">Add a New Line</button>
							</td>
						</tfoot>
					<?php endif; ?>
				</table>
			</div>
			<div class="box-body">
				<hr>
				<div class="row">
					<div class="col-md-12 text-center" id="submit-box">
						<?php  
								if( ($stat == 'open' || $stat == '') && $restrict_str ){
									echo $ui->drawSubmit($show_input);
								} 
						?>
						<? if( $stat == 'open' && $task != 'edit' && $restrict_str ){ ?>
							<a class="approve btn btn-warning" data-id="<?=$transactionno?>">Approve</a>
							<a class="reject btn btn-danger" data-id="<?=$transactionno?>">Reject</a>	
						<? } ?>
						<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>

<div id="row_limit" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Item Limit</h4>
			</div>
			<div class="modal-body">
				<p>Sorry, but the printout for this record is only limited to <strong><?=$item_limit?></strong> items.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>

		var delete_row	= {};
		var ajax		= {};
		var ajax_call	= '';
		var min_row		= 1;
		var header_min_row = 0;
		var on_hand 	= 0; 
		var task 		= '<?=$task?>';
		function addRowDetails(details, index) 
		{
			var details = details || {itemcode: '', detailparticular: '', source: '',destination: '',
						ohqty: '', qtytoapply: '', price: '', amount: '', uom : ''};
						if(details.ohqty == null || details.ohqty == ''){ details.ohqty = '0.00';}
						if(details.detailparticular == null){ details.detailparticular = "";}
						if(details.source == ""){ details.source = "";}
						if(details.destination == ""){ details.destination = "";}
						if(details.ohqty == null || details.ohqty == ""){ details.ohqty = '0';}
						if(details.qtytoapply == null || details.qtyapply == ""){ details.qtyapply = '0';}
						if(details.uom == null){ details.uom = "";}
						if(details.price == null || details.price == ""){ details.price = '0.00';}
						if(details.amount == null || details.amount == ""){ details.amount = '0.00';}
						on_hand = details.ohqty;
						//alert("onhand initial test: " + on_hand);
			var row = `
				<tr>
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
								//->addHidden(true)
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setPlaceholder('Item Name')
								->setName('detailparticular[]')
								->setClass('detailparticular')
								->setAttribute(array("readonly"=>""))
								->setValue('` + details.detailparticular + `')
								//->addHidden(true)
								->draw($show_input);
						?>
					</td>

					<td class="hidden">
						<?php
							// echo $ui->formField('text')
							// 	->setSplit('', 'col-md-12')
							// 	->setPlaceholder('Site Source')
							// 	->setName('sitesource[]')
							// 	->setClass('sitesource')
							// 	->setAttribute(array("readonly"=>""))
							// 	->setValue('` + details.source + `')
							// 	//->addHidden(true)
							// 	->draw($show_input);
						?>
					</td>
					<td class="hidden">
						<?php
							// echo $ui->formField('text')
							// 	->setSplit('', 'col-md-12')
							// 	->setPlaceholder('Site Destination')
							// 	->setName('sitedestination[]')
							// 	->setClass('sitedestination')
							// 	->setAttribute(array("readonly"=>""))
							// 	->setValue('` + details.destination + `')
							// 	//->addHidden(true)
							// 	->draw($show_input);
						?>
					</td>
					<td class = "hidden">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('ohqty[]')
								->setClass('ohqty')
								->setAttribute(array("readonly"=>""))
								->setValue('` + (parseInt(details.ohqty) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('qtytoapply[]')
								->setClass('qtytoapply')
								->setValidation('required integer')
								->setValue('` + (parseInt(details.qtytoapply) || 0) + `')
								->draw($show_input);
								
						?>
					</td>
					<?php if (!$show_input): ?>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('qtytoapply[]')
								->setClass('qtytoapply')
								->setValidation('required integer')
								->setValue('` + (parseInt(details.balanceqty) || 0) + `')
								->draw($show_input);
								
						?>
					</td>
					<?php endif; ?>
					<td >
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('uom[]')
								->setClass('uomcode')
								//->setValidation('required integer')
								->setAttribute(array("readonly"=>""))
								->setValue('` + details.uom.toUpperCase() + `')
								->draw($show_input);
								
						?>
					</td>
					<td class = "hidden">
						<?php	
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('price[]')
								->setClass('price')
								->setClass('format_values')
								//->setValidation('required')
								->setAttribute(array("readonly"=>""))
								->setValidation('decimal')
								->setValue('` + (parseFloat(details.price) || 0.00) + `')
								->draw($show_input);
						?>
					</td>
					<td class = "hidden">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('amount[]')
								->setValidation('required integer')
								->setClass('format_values amount')
								//->setValidation('required')
								->setAttribute(array("readonly"=>""))
								->setValidation('decimal')
								->setValue('` + (parseFloat(details.amount) || 0.00) + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php if ($show_input): ?>
						<button type="button" class="btn btn-danger delete_row" style="outline:none;">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
						<?php endif ?>
					</td>
				</tr>
			`;

			$('#tableList tbody').append(row);
			if (details.itemcode != '') {
				$('#tableList tbody').find('tr:last .itemcode').val(details.itemcode);
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
		}
		 var row_details = <?php echo $row_details ?>;

		function displayDetails(details) {
			
			if (details.length < min_row) {
				for (var x = details.length; x < min_row; x++) {
					addRowDetails('', x);
				}
			}else if(details.length > 0){
				details.forEach(function(details, index) {
					addRowDetails(details, index);
				});
			} 
			
		}
		displayDetails(row_details);


	function approve_request(transferno){
		$.post('<?=MODULE_URL?>ajax/update_request_status', 'transferno=' + transferno + "&status=approved", function(data) {
			window.location = '<?=MODULE_URL;?>';
		});	
	}

	function reject_request(transferno){
		$.post('<?=MODULE_URL?>ajax/update_request_status', 'transferno=' + transferno + "&status=rejected", function(data) {
			window.location = '<?=MODULE_URL;?>';
		});	
	}

	function close_request(transferno){
		$.post('<?=MODULE_URL?>ajax/update_request_status', 'transferno=' + transferno + "&status=rejected", function(data) {
			window.location = '<?=MODULE_URL;?>';
		});	
	}

	$(function(){
		createConfimationLink('.approve', 'approve_request', 'Are you sure you want to Approve this Request?');
		createConfimationLink('.reject', 'reject_request', 'Are you sure you want to Reject this Request?');
		createConfimationLink('.close_request', 'close_request', 'Are you sure you want to Close this Request?');
	});


<?php if ($show_input): ?>
	
	function get_item_details(itemcode, warehouse, l){
		ajax_call = $.post('<?=MODULE_URL?>ajax/get_item_details', { itemcode: itemcode, warehouse: warehouse }, 
		function(data) {

			var price  	= 	parseFloat(data.price);
			// var l 		=	$('#tableList');
			if(data.notfound){
			}else{
				l.closest('tr').find(".detailparticular").val(data.itemdesc);
				l.closest('tr').find(".sitesource").val(warehouse);
				l.closest('tr').find(".ohqty").val(data.onhandQty);
				l.closest('tr').find('.uomcode').val(data.uom_base);	
				if( data.price != null && data.price != "" ){
					l.closest('tr').find(".price").val(price.toFixed(2));
				} else {
					l.closest('tr').find(".price").val('0.00');
				}					
				on_hand = data.onhandQty;
				if(on_hand > 0){
					$("#submit-box button[type=submit]").prop("disabled",false);
				}else{
					console.log('disable');
					$("#submit-box button[type=submit]").prop("disabled",true);
				}
				// var qty = l.closest('tr').find('.qtytoapply').val();
				// console.log('qty=' + qty);
				// if( qty > on_hand ){
				// 	$('.qtytoapply').closest('div').find('.form-group').addClass('has-error');
				// } else {
				// 	$('.qtytoapply').closest('div').find('.form-group').removeClass('has-error');
				// }

				addAmounts();
			}

		});
	}

	$('#stockTransferForm').on('change','.itemcode',function(){
		var itemcode = $(this).val();
		var warehouse = $("#h_site_source").val();
		var task  	 = '<?=$task?>';
		var l = $(this);

		if( warehouse!="" ){
			get_item_details(itemcode, warehouse,l);
		} else {
			$('#warning_modal #warning_message').html("<b>Please choose a Warehouse first.</b>");
			$('#site_source').blur();
			$('#warning_modal').modal('show');
		}
	});

	$('#addNewItem').on('click', function() {

		var length 	=	$('#tableList tbody tr').length;
		var rowlimit 	= '<?echo $item_limit?>';

		if(rowlimit == 0 || length < rowlimit){
			addRowDetails();
		} else {
			$('#row_limit').modal('show');
		}
	});

	function addAmounts() 
	{	var subtotal = 0;
		var counter = 0;
		$( ".amount" ).each(function() {
			var value = $( this ).val();
			if(value && value != '0' && value != '0.00'){                            
				value = removeComma(value);
			} else {             
				value = 0;
				counter++;
			}
			subtotal 	= parseFloat(subtotal) + parseFloat(value);
	

		subtotal	 = Math.round(1000*subtotal)/1000;
		
		document.getElementById('total_amount').value 					= subtotal.toFixed(2);
		});
	}

	function deleteRowDetails(id) {
		delete_row.remove();
		if ($('#tableList tbody tr').length < min_row) {
			addRowDetails();
		}
	}

	function getWarehouseList(current)
	{
		$.post('<?=MODULE_URL?>ajax/get_warehouse_list', "warehouse="+current, function(data) {
			$('#site_destination').html(data.list);
		});
	}

	$('body').on('click', '.delete_row', function() {
		delete_row = $(this).closest('tr');
		$('.itemcode').trigger('change');
	});
	
	$(function(){
		linkDeleteToModal('.delete_row', 'deleteRowDetails');
		
		if( task != 'edit' ){
			$("#submit-box button[type=submit]").prop("disabled",true);
		}

		$(document.body).on('blur','.format_values', function(e) {
			var amount = $(this).val();
			amount     = removeComma(amount);
			var result = amount * 1;
			
			amount = addComma(result);
			$(this).val(amount);
		});

		$(document.body).on('keypress','.format_values', function(e) {
			isNumberKey2(e);
		});

		$(document.body).on("change","#site_source",function(){
			//check site_destination
			var sitedestination = $("#site_destination").val();
			$('#h_site_source').val($(this).val());

			if( $.trim($(this).val()) == $.trim(sitedestination) ){
				$(this).val('').trigger("change");
				$('#site_destination').prop('readonly',true);
			} else{
				// $(".sitesource").val($(this).val());
				getWarehouseList($(this).val());
				$('#site_destination').prop('readonly',false);

				var existing_content  = checkitemcodes();
				
				if( existing_content > 0 ){
					$('.itemcode').trigger('change');
				}
			}	
		});

		function checkitemcodes(){
			var count = 0;
			var itemcode = $("#tableList tbody").find('tr').find('.itemcode:first-child').val();

			if( itemcode != "" ){
				count 	+= 	1;
			} 
			return count;
		}

		$(document.body).on("change","#site_destination",function(){
			//check site_source
			var sitesource = $("#site_source").val();
			$('#h_site_destination').val($(this).val());

			if( $.trim($(this).val()) == $.trim(sitesource) ){
				$(this).val('').trigger("change");
			}else{
				$(".sitedestination").val($(this).val());
			}
		});

		$(document.body).on("blur",".price",function(){
			var l = $(this);
			var price = l.val();
			var qtytoapply = l.closest('tr').find(".qtytoapply").val();
	
			if(price != '0' && price != '0.00')
			{                            
				price = removeComma(price);
			}
			else
			{             
				price = '0.00';
			}

			if(qtytoapply != '0' && qtytoapply != '0.00')
			{                            
				qtytoapply = removeComma(qtytoapply);
			}
			else
			{             
				qtytoapply = 0;
			}

			amount 	= parseFloat(price) * parseFloat(qtytoapply);
			amount	 = Math.round(1000*amount)/1000;
			l.closest('tr').find(".amount").val(addComma(amount.toFixed(2)));
			addAmounts();
		});

		$(document.body).on("blur",".qtytoapply",function(){
			var l = $(this);
			var qtytoapply = l.val();
			var price = l.closest('tr').find(".price").val();
			var onhand = l.closest('tr').find(".ohqty").val();
			// console.log('onhand  '+onhand);
			if(price != '0' && price != '0.00')
			{                            
				price = removeComma(price);
			}
			else
			{             
				price = 0;
			}

			if(qtytoapply != '0' && qtytoapply != '0.00')
			{                            
				qtytoapply = removeComma(qtytoapply);
				
				// if( onhand < qtytoapply ){
				// 	$(this).closest('.form-group').addClass("has-error");
				// 	$("#submit-box button[type=submit]").prop("disabled",true);
				// 	$('#warning_modal #warning_message').html("<b>You cannot input a quantity greater than the Onhand.</b>");
				// 	$('#warning_modal').modal('show');
				// } else {
				// 	$(this).closest('.form-group').removeClass("has-error");
				// 	$("#submit-box button[type=submit]").prop("disabled",false);
				// }
				$(this).closest('.form-group').removeClass("has-error");
				$("#submit-box button[type=submit]").prop("disabled",false);
			}
			else
			{             
				qtytoapply = 0;
				$(this).closest('.form-group').addClass("has-error");
				$("#submit-box button[type=submit]").prop("disabled",true);
				$('#warning_modal #warning_message').html("<b>Please input a quantity.</b>");
				$('#warning_modal').modal('show');
			}

			amount 	= parseFloat(price) * parseFloat(qtytoapply);
			amount	 = Math.round(1000*amount)/1000;
			l.closest('tr').find(".amount").val(addComma(amount.toFixed(2)));
			addAmounts();
		});
	});

	$('form').submit(function(e) {
		e.preventDefault();
		addAmounts(); 
		$(this).find('.form-group').find('input, textarea, select').trigger('blur');
		if ($(this).find('.form-group.has-error').length == 0) {
			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
				if (data.success) {
					$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = data.redirect;						
							}, 1000)
				}
			});
		} else {
			$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			$('#warning_modal #warning_message').html("<b>Please fill in the required fields.</b>");
			$('#warning_modal').modal('show');
		}
	});

	/**LIMIT INPUT TO NUMBERS ONLY**/
	function isNumberKey2(evt) 
	{

		if(evt.which != 0){
			var charCode = (evt.which) ? evt.which : evt.keyCode 
			if(charCode == 46) return true; 
			if (charCode > 31 && (charCode < 48 || charCode > 57)) 
			return false; 
			return true;
		}
	}

	function addComma(nStr)
	{
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

<?endif;?>
</script>