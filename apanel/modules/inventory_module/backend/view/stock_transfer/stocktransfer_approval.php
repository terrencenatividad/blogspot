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
								->setLabel('Released By')
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
							<!-- <th>Item Name</th> -->
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
	<div id="serial_modalList" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Item Serialized</h4>
                </div>
                <div class="modal-body">
                    <table id="serial_tableList" class="table table-sidepad no-margin-bottom">
                        <thead>
                            <tr class="info">
                                <th></th>
                                <th class="text-center">Item Code</th>
                                <th class="text-center">Item Name</th>
                                <th class="text-center">Serial Number</th>
                                <th class="text-center">Chassis Number</th>
                                <th class="text-center">Engine Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">Loading Items</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                	<?php if($task == 'view_approval'): ?>
                		<button id="btn_modal_close" class="btn btn-primary btn-flat">Confirm</button>
                	<?php else: ?>
                    <button id="btn_serial_select" class="btn btn-primary btn-flat">Confirm</button>
                    <button id="btn_modal_close" class="btn btn-default btn-flat">Cancel</button>
                	<?php endif;?>
                </div>
            </div>
        </div>
    </div>
</section>

<script type='text/javascript'>

	var delete_row	= {};
	var ajax		= {};
	var ajax_call	= '';
	var min_row		= 0;
	var header_min_row = 0;
	var selected = [];
	var task = '<?=$task;?>';
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
		delete other_details.isserialized;
		var otherdetails = '';
		var linenum = index+1;

		for (var key in other_details) {
			if (other_details.hasOwnProperty(key)) {
				otherdetails += `<?php 
					echo $ui->setElement('hidden')
							->setName('` + key + `[]')
							->setClass('` + key + `')
							->setValue('` + other_details[key] + `')
							->draw();
					?>`;
			}
		}

		if(task == 'view_approval'){
			var btnlabel = 'View selected serialized item';
			var selected_value = details.isserialized;
		}
		else{
			var btnlabel = 'Click to tag serialized item';
			var selected_value = 0;
		}

		if (details.isserialized>0) {
			var max 	= (parseFloat(details.maxqty) || 0);
			var trclass = 'serialized';
			var inputqty = `<button type="button" class="btnserial btn btn-block btn-success btn-flat" data-max =`+max+` data-validation="required integer">
                                <em class="pull-left"><small>`+ btnlabel +`</small></em>
                                <strong class="txtqtytransferred pull-right">`+selected_value+`</strong>
                            </button>
                            <input type='hidden' class='qtytransferred' name='qtytransferred[]' value='0'>`
		}
		else{
			var trclass = '';
			var inputqty = `<?php
						echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('qtytransferred[]')
							->setClass('qtytransferred')
							->setAttribute(array('data-max' => '` + (parseFloat(details.maxqty) || 0) + `', 'data-value' => '` + (parseFloat(details.qtytransferred) || 0) + `'))
							->setValidation('required integer')
							->setValue('` + (parseInt(details.qtytransferred) || 0) + `')
							->draw($show_input);
					?>`
		}

		var row = `
			<tr id='`+index+`' class='`+trclass+`'>
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
				<td class="hidden">
					<?php
						echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticular[]')
								->setClass('itemname')
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
					`+inputqty+`
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
		$('#tableList tbody').find('tr:last').each(function() {
			var qtytransferred = $(this).find('.qtytransferred');
			if (details.qtytransferred > 0) {

				qtytransferred.removeAttr('readonly').val(qtytransferred.attr('data-value'));
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('check').iCheck('enable');
			} else {
				$('#tableList tbody').find('tr:last .btnserial').attr('disabled', true);
				$('#tableList tbody').find('tr:last .qtytransferred').attr('readonly', '').val(0);
				$('#tableList tbody').find('tr:last .check_task [type="checkbox"]').iCheck('uncheck').iCheck('disable');
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

	function getList(row) {
        var itemcode 	= row.find('.itemcode').val();
        var itemname 	= row.find('.itemname').val();
        var linenum 	= row.find('.linenum').val();
        var sourceno 	= $('#transaction_no').val();
        var max 		= row.find('.btnserial').data('max');
        var data 		= {task:task, sourceno:sourceno, itemcode:itemcode, itemname:itemname, linenum:linenum, max:max};

        $('#serial_tableList tbody').html(`<tr><td colspan="6" class="text-center">Loading Items</td></tr>`);
        $('#serial_modalList').modal('show');

        $.post('<?=MODULE_URL?>ajax/ajax_load_serial', data, function(data) {
            $('#serial_tableList tbody').html(data);
        });
    }
	
	function checkSelected(checkbox){
		$.each(checkbox, function(index, value){
			var itemcode 	= $(this).data('itemcode');
			var linenum 	= $(this).data('linenum');
			var serialno 	= $(this).data('serial');
			var chassisno 	= $(this).data('chassis');
			var engineno 	= $(this).data('engine');

			for(var i=0; i<selected.length; i++){
	            if (itemcode   	== selected [i] .itemcode && 
	            	linenum 	== selected [i] .linenum &&
	                serialno	== selected [i] .serialno &&
	                chassisno	== selected [i] .chassisno &&
	                engineno 	== selected [i] .engineno) 
	            {
	                $(this).iCheck("check");
	            }
            }
		});
	}

	function saveSelectedSerial(checkbox){
		
		$.each(checkbox, function(index, value){
			var itemcode 	= $(this).data('itemcode');
			var linenum 	= $(this).data('linenum');
			var serialno 	= $(this).data('serial');
			var chassisno 	= $(this).data('chassis');
			var engineno 	= $(this).data('engine');

			for(var i=0; i<selected.length; i++){
	            if (itemcode   	== selected [i] .itemcode && 
	            	linenum 	== selected [i] .linenum &&
	                serialno	== selected [i] .serialno &&
	                chassisno	== selected [i] .chassisno &&
	                engineno 	== selected [i] .engineno) 
	            {
	            	var inarray = true;
	            	if (inarray) {
	            		if (!$(this).is(':checked'))
	            			selected.splice(i,1);
	            	}
				}
            }
            if (!inarray) {
            	if ($(this).is(':checked')) 
            		selected.push({'itemcode':itemcode, 'linenum':linenum, 'serialno':serialno, 'chassisno':chassisno, 'engineno':engineno});
            }
		});
	}

	$('form').on('click', '#btnRelease', function(e) {
		e.preventDefault();
		var form_element = $(this).closest('form');
		var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();

		form_element.find('.form-group').find('input, textarea, select').trigger('blur_validate');
		if (form_element.find('.form-group.has-error').length == 0) {
			$('btnRelease').attr('disabled', false);
			var items = 0;

			$('#tableList tbody tr .item_checkbox:checked').each(function(){
				items += removeComma($(this).closest('tr').find('.qtytransferred').val());
			});

			if (items > 0) {
				var voucherno = $('#transaction_no').val();
				var data={
					'voucherno'	:[],
					'itemcode'	:[], 
					'linenum'	:[], 
					'serialno'	:[], 
					'chassisno'	:[], 
					'engineno'	:[]
				};

				for (var i = 0; i < selected.length; i++) {
					data.voucherno.push(voucherno);
					data.itemcode.push(selected[i].itemcode);
					data.linenum.push(selected[i].linenum);
					data.serialno.push(selected[i].serialno);
					data.chassisno.push(selected[i].chassisno);
					data.engineno.push(selected[i].engineno);
				}

				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.serialize()+'<?=$ajax_post?>'+submit_data+'&voucherno='+data.voucherno+'&serialitemcode='+data.itemcode+'&seriallinenum='+data.linenum+'&serialno='+data.serialno+'&chassisno='+data.chassisno+'&engineno='+data.engineno , function(data) {
					if (data.success) {
						$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = data.redirect;						
							}, 1000)
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
	$('#tableList tbody').on('ifUnchecked', '.check_task input[type="checkbox"]', function() {
		var n 			= $(this).closest('tr').find('.qtytransferred');
		var btnserial 	= $(this).closest('tr').find('.btnserial');
		var displayqty 	= $(this).closest('tr').find('.txtqtytransferred');

		n.attr('readonly', true).val(0).trigger('blur');
		btnserial.attr('disabled', true);
		displayqty.text('0');

		selected = [];
	});
	$('#tableList tbody').on('ifChecked', '.check_task input[type="checkbox"]', function() {
		var n 			= $(this).closest('tr').find('.qtytransferred');
		var btnserial 	= $(this).closest('tr').find('.btnserial');

		n.removeAttr('readonly', '').val(n.attr('data-value')).trigger('blur');
		btnserial.attr('disabled', false);
	});

	$('#serial_tableList tbody').on('ifUnchecked', '.chkitem', function() {
		var maxval = $(this).data('maxval');
		maxval += 1;
		$('.chkitem').data('maxval', maxval);
		if ($(this).data('maxval')<1) 
			$('.chkitem:not(:checked)').attr('disabled', true);
		
		else
			$('.chkitem').attr('disabled', false);

		$('#serial_tableList input[type="checkbox"]').iCheck('update');
	});
	$('#serial_tableList tbody').on('ifChecked', '.chkitem', function() {
		var maxval = $(this).data('maxval');
		maxval -= 1;
		$('.chkitem').data('maxval', maxval);
		console.log($(this).data('maxval'));
		if ($(this).data('maxval')<1) 
			$('.chkitem:not(:checked)').attr('disabled', true);
		
		else
			$('.chkitem').attr('disabled', false);

		$('#serial_tableList input[type="checkbox"]').iCheck('update');
	});
	
	$('#tableList tbody').on('click', '.btnserial', function(){
		var row = $(this).closest('tr');
		getList(row);
	});
	$('#btn_serial_select').on('click', function(){
		var checkbox 	= $('#serial_tableList tbody tr .chkitem');
		var linenum 	= $('#serial_tableList tbody .chkitem').data('linenum');
		var displayqty 	= $('tr#'+(linenum-1)+' .txtqtytransferred');
		var inputqty 	= $('tr#'+(linenum-1)+' .qtytransferred');
		
		saveSelectedSerial(checkbox);

		var qtycount 	= 0;
		for(var i=0; i<selected.length; i++){
			if (selected[i].linenum == linenum) {
				qtycount += 1;
			}
		}
		displayqty.text(qtycount);
		inputqty.val(qtycount);

		$('#serial_modalList').modal('hide');
	})
	$('#btn_modal_close').on('click', function(){
		$('#serial_modalList').modal('hide');
	})
</script>