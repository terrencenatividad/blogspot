<section class = 'content'>
	<div class="box box-primary">
		<form method="post" class="form-horizontal" id="stockTransferForm">
			<div class="box-body">
				<div id="header_hidden"></div>
				<?
					echo $ui->setElement("hidden")
							->setId('transaction_no')
							->setValue($transactionno)
							->draw();
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
								->setName('reference')
								->setId('reference')
								->setValue($reference)
								->draw($show_input );
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
								->setName('source')
								->setId('source')
								->setList($warehouse_list)
								->setValue($source)
								->setValidation('required')
								->addHidden()
								->draw($show_input && $task != 'release');
					?>
					</div>
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
								->setAttribute(array("readonly"=>"","data-date-start-date"=>$close_date))
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
								->setName('destination')
								->setId('destination')
								->setList($warehouse_list)
								->setValue($destination)
								->setValidation('required')
								->addHidden()
								->draw($show_input && $task != 'release');
						?>
					</div>
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
								->setAttribute(array("readonly"=>"","data-date-start-date"=>$close_date))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
						echo $ui->formField('text')
								->setLabel('Approved By')
								->setSplit('col-md-4', 'col-md-8')
								->setName('approved_by')
								->setId('approved_by')
								->setValue($approved_by)
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
								->draw($show_input );
						
						echo $ui->setElement('hidden')
								->setName('source_no')
								->setId('source_no')
								->setValue($source_no)
								->draw();
						?>			
					</div>
				</div>  
			</div>
			
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover">
					<thead>
						<tr class="info">
							<?php if ($show_input): ?>
							<th class="text-center" style="width: 20px"><input type="checkbox" class="checkall"></th>
							<?php endif ?>
							<th>Item Code</th>
							<th>Item Name</th>
							<th>Available On Hand</th>
							<th>Requested Qty</th>
							<th>Transfer Qty</th>
							<th>UOM</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					
				</table>
			</div>
			<div class="box-body">
				<hr>
				<div class="row">
					<div class="col-md-12 text-center" id="submit-box">
					<?php if ($task == "release" || $task == 'edit_approval'): ?>
						<button type="button" id="btnRelease" class="btn btn-primary btn-sm btn-flat">Save</button>
					<?php elseif ($task == "received"): ?>	
							<button type="button" id="btnReceive" class="btn btn-success btn-sm btn-flat">Receive</button>
					<? endif; ?>	
						<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>

<script type='text/javascript'>

	var delete_row	= {};
	var ajax		= {};
	var ajax_call	= '';
	var min_row		= 0;
	var header_min_row = 0;
	function addRowDetails(details, index) 
	{
		var details = details || {itemcode: '', detailparticular: '', ohqty: '', qtytoapply: '', price: '', amount: ''};
					if(details.ohqty == null || details.ohqty == ''){ details.ohqty = '0.00';}
					if(details.detailparticular == ""){ details.detailparticular = "";}
					if(details.ohqty == null || details.ohqty == ""){ details.ohqty = '0';}
					if(details.qtytoapply == null || details.qtytoapply == ""){ details.qtytoapply = '0';}
					if(details.price == null || details.price == ""){ details.price = '0.00';}
					if(details.amount == null || details.amount == ""){ details.amount = '0.00';}
		var other_details = JSON.parse(JSON.stringify(details));
		delete other_details.itemcode;
		delete other_details.detailparticular;
		delete other_details.issueqty;
		delete other_details.warehouse;
		delete other_details.ohqty;
		delete other_details.qtytoapply;
		delete other_details.qtytransferred;
		delete other_details.price;
		delete other_details.amount;
		delete other_details.maxqty;
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
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('ohqty[]')
							->setValue('` + addComma(details.ohqty, 0) + `')
							->addHidden()
							->draw($show_input);
					?>
				</td>
				<td>
					<?php
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('qtytoapply[]')
							->setValue('` + addComma(details.qtytoapply, 0) + `')
							->addHidden()
							->draw($show_input);
					?>
				</td>
				<td>
					<?php
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('qtytransferred[]')
							->setClass('qtytransferred')
							->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.qtytransferred) || 0) + `'))
							->setValidation('required integer')
							->setValue('` + (parseInt(details.qtytransferred) || 0) + `')
							->draw($show_input);
					?>
				</td>
				<td >
					<?php
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('uom[]')
							->setValue('` + details.uom.toUpperCase() + `')
							->draw(false);
					?>
					` + otherdetails + `
				</td>
				<td class="hidden">
					<?php	
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('price[]')
							->setClass('price')
							->setClass('format_values')
							->setValidation('required')
							->setValue('` + (parseFloat(details.price) || 0.00) + `')
							->draw($show_input);
					?>
				</td>
				<td class="hidden">
					<?php
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('amount[]')
							->setValidation('required integer')
							->setClass('format_values amount')
							->setValidation('required')
							->setAttribute(array("readonly"=>""))
							->setValue('` + (parseFloat(details.amount) || 0.00) + `')
							->draw($show_input);
					?>
				</td>
				<td class='hidden'>
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
		$('#tableList tbody').find('tr:last .qtytransferred').each(function() {
			if (details.qtytransferred > 0) {
				$(this).removeAttr('readonly').val($(this).attr('data-value'));
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
			} else {
				$('#tableList tbody').find('tr:last .qtytransferred').attr('readonly', '').val(0);
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('enable');
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

	$('form').on('click', '#btnRelease', function(e) {
		e.preventDefault();
		var form_element = $(this).closest('form');
		var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();

		form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');
		if (form_element.find('.form-group.has-error').length == 0) {
			$('btnRelease').attr('disabled', false);
			var items = 0;
			$('.qtytransferred:not([readonly])').each(function() {
				items += removeComma($(this).val());
			});
			if ($('.qtytransferred:not([readonly])').length > 0 && items > 0) {
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize() + '<?=$ajax_post?>' + submit_data , function(data) {
					if (data.success) {
						window.location = data.redirect;
					} 
				});
			} else {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Add an Item');
				$('btnRelease').attr('disabled', true);
			}
		} else {
			form_element.find('.form-group.has-error').first().find('input, textarea, select').focus();
			$('btnRelease').attr('disabled', true);
		}
	});
	
	$(document.body).on('click','#btnReceive',function(){
		ajax.transaction_no 	=	$('#transaction_no').val();
		ajax.approved_by 		=	$('#approved_by').val();
		$.post('<?=MODULE_URL?>ajax/set_received', ajax, 
		function(data) {
			if (data.msg =='success') {
				window.location = '<?=MODULE_URL?>';
			}
		 });
	});

	// For Approval - Check & Uncheck
	$('tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
		$(this).closest('tr').find('.qtytransferred').attr('readonly', '').val(0).trigger('blur');
	});
	$('tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
		var n = $(this).closest('tr').find('.qtytransferred');
		n.removeAttr('readonly', '').val(n.attr('data-value')).trigger('blur');
	});
</script>