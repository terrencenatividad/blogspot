<section class = 'content'>
	<div class="box box-primary">
		<!--<form method="post" class="form-horizontal" id="stockTransferForm">-->
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
									->setValidation('required')
									->draw($show_input);
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
							<th class="col-md-1">Item Code</th>
							<th>Item Name</th>
							<th>On Hand Qty</th>
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
					<?php if ($task == "release"): ?>
						<button type="button" id="btnRelease" class="btn btn-info btn-sm btn-flat">Release</button>
					<?php elseif ($task == "received"): ?>	
							<button type="button" id="btnReceive" class="btn btn-success btn-sm btn-flat">Receive</button>
					<? endif; ?>	
						<a href="<?=MODULE_URL?>" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</div>
		<!--</form>-->
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
			var details = details || {itemcode: '', itemname: '', source: '',destination: '',
						ohqty: '', qtytoapply: '', price: '', amount: ''};
						if(details.ohqty == null || details.ohqty == ''){ details.ohqty = '0.00';}
						if(details.itemname == ""){ details.itemname = "";}
						if(details.source == ""){ details.source = "";}
						if(details.destination == ""){ details.destination = "";}
						if(details.ohqty == null || details.ohqty == ""){ details.ohqty = '0';}
						if(details.qtytoapply == null || details.qtyapply == ""){ details.qtyapply = '0';}
						if(details.price == null || details.price == ""){ details.price = '0.00';}
						if(details.amount == null || details.amount == ""){ details.amount = '0.00';}
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
								->setName('itemname[]')
								->setClass('itemname')
								->setAttribute(array("readonly"=>""))
								->setValue('` + details.itemname + `')
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
					<td>
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
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('qtytotransfer[]')
								->setClass('qtytotransfer')
								->setValidation('required integer')
								->setValue('` + (parseInt(details.qtytoapply) || 0) + `')
								->draw($show_input);
						?>
					</td>
					<td >
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('uom[]')
								->setClass('uomcode')
								//->setValidation('required integer')
								->setAttribute(array("readonly"=>""))
								->setValue('` + details.uom + `')
								->draw($show_input);
								
						?>
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

	//Added by Jasmine
	//modified: Darryl
	$(document.body).on('click','#btnRelease',function(){
		ajax.transaction_no 	=	$('#transaction_no').val();
		$.post('<?=MODULE_URL?>ajax/set_release', ajax, 
		function(data) {
			if (data.msg =='success') {
				window.location = '<?=MODULE_URL?>';
			}
		 });
	});
	//Added: Darryl
	$(document.body).on('click','#btnReceive',function(){
		ajax.transaction_no 	=	$('#transaction_no').val();
		$.post('<?=MODULE_URL?>ajax/set_received', ajax, 
		function(data) {
			if (data.msg =='success') {
				window.location = '<?=MODULE_URL?>';
			}
		 });
	});

</script>