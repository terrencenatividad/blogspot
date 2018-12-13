<section class="content">
		<form action="" method="post" id="form" class="form-horizontal">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#Details" data-toggle="tab">Asset Details</a></li>
					<li><a href="#Depreciate" data-toggle="tab">Depreciation</a></li>
					<li><a href="#Accounting" data-toggle="tab">Accounting Details</a></li>
				</ul>
				<div class="tab-content no-padding">
					<div id="Details" class="tab-pane active" style="padding: 15px">
					<div class="row">
							<div class="col-md-11">
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
													->setLabel('Item Code')
													->setPlaceholder('Select Item Code')
													->setSplit('col-md-3', 'col-md-8')
													->setName('itemcode')
													->setId('itemcode')
													->setList($item_list)
													->setValue($itemcode)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
													->setLabel('Asset Class')
													->setPlaceholder('Select Asset Class')
													->setSplit('col-md-3', 'col-md-8')
													->setName('asset_class')
													->setId('asset_class')
													->setList($assetclass_list)
													->setValue($asset_class)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>
								
								<div class="row">
								
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Asset Name')
												->setSplit('col-md-3', 'col-md-8')
												->setName('asset_name')
												->setId('asset_name')
												->setValue($asset_name)
												->setAttribute(array("maxlength" => "50"))
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Asset Number(Bar Code)')
												->setSplit('col-md-3', 'col-md-8')
												->setName('asset_number')
												->setId('asset_number')
												->setValue($asset_number)
												->setAttribute(array("maxlength" => "10"))
												->setValidation('alpha_num required')
												->draw($show_input);
										?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Sub-Number')
												->setSplit('col-md-3', 'col-md-8')
												->setName('sub_number')
												->setId('sub_number')
												->setValue($sub_number)
												->setAttribute(array("maxlength" => "3"))
												->setValidation('alpha_num')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Serial Number / Engine Number')
												->setSplit('col-md-3', 'col-md-8')
												->setName('serial_number')
												->setId('serial_number')
												->setValue($serial_number)
												->setAttribute(array("maxlength" => "15"))
												->setValidation('alpha_num')
												->draw($show_input);
										?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('textarea')
													->setLabel('Description')
													->setSplit('col-md-3', 'col-md-8')
													->setName('description')
													->setId('description')
													->setValue($description)
													->setAttribute(array("maxlength" => "1000"))
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Asset Location')
												->setSplit('col-md-3', 'col-md-8')
												->setName('asset_location')
												->setId('asset_location')
												->setValue($asset_location)
												->setAttribute(array("maxlength" => "30"))
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('textarea')
													->setLabel('Department')
													->setSplit('col-md-3', 'col-md-8')
													->setName('department')
													->setId('department')
													->setValue($department)
													->setAttribute(array("maxlength" => "30"))
													->setValidation('required')
													->draw($show_input);
										?>
									</div>	
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Accountable Person')
													// ->setPlaceholder('Select User')
													->setSplit('col-md-3', 'col-md-8')
													->setName('accountable_person')
													->setId('accountable_person')
													// ->setList($users_list)
													->setValue($accountable_person)
													->setAttribute(array("maxlength" => "30"))
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Retirement Date')
													->setSplit('col-md-3', 'col-md-8')
													->setClass('datepicker-input')
													->setAttribute(array('readonly' => ''))
													->setAddon('calendar')
													->setName('retirement_date')
													->setId('retirement_date')
													->setValue($retirement_date)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Commissioning Date')
													->setSplit('col-md-3', 'col-md-8')
													->setClass('datepicker-input')
													->setAttribute(array('readonly' => ''))
													->setAddon('calendar')
													->setName('commissioning_date')
													->setId('commissioning_date')
													->setValue($commissioning_date)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>

								
							</div>
						</div>
					</div>


				

				<div id="Depreciate" class="tab-pane" style="padding-top:15px">
				<div class="col-md-12">
					<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('No. of Months Useful Life')
													->setSplit('col-md-3', 'col-md-8')
													->setName('useful_life')
													->setId('useful_life')
													->setValue($useful_life)
													->setAttribute(array("maxlength" => "3"))
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Depreciation Month Start')
													->setSplit('col-md-3', 'col-md-8')
													->setClass('datepicker-input')
													->setAttribute(array('readonly' => ''))
													->setAddon('calendar')
													->setName('depreciation_month')
													->setId('depreciation_month')
													->setValue($depreciation_month)
													->setValidation('required')
													->draw($show_input);
										?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Capitalized Cost')
													->setSplit('col-md-3', 'col-md-8')
													->setName('capitalized_cost')
													->setId('capitalized_cost')
													->setValue($capitalized_cost)
													->setValidation('required decimal')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Purchase Value')
													->setSplit('col-md-3', 'col-md-8')
													->setName('purchase_value')
													->setId('purchase_value')
													->setValue($purchase_value)
													->setValidation('required decimal')
													->draw($show_input);
										?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Book Value')
													->setSplit('col-md-3', 'col-md-8')
													->setName('balance_value')
													->setId('balance_value')
													->setValue($balance_value)
													->setValidation('required decimal')
													->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Salvage Value')
													->setSplit('col-md-3', 'col-md-8')
													->setName('salvage_value')
													->setId('salvage_value')
													->setValue($salvage_value)
													->setValidation('required decimal')
													->draw($show_input);
										?>
									</div>
								</div>

								<div class="row hidden">
									<div class="col-md-6">
									<?php
										echo $ui->formField('text')
												->setLabel('Total Number of Depreciation')
												->setSplit('col-md-3', 'col-md-8')
												->setName('number_of_dep')
												->setId('number_of_dep')
												->setValue($number_of_dep)
												->setValidation('integer')
												->draw($show_input);
									?>
								</div>
								<div class="col-md-6">
										<?php
											echo $ui->formField('text')
													->setLabel('Depreciation Amount / Month')
													->setSplit('col-md-3', 'col-md-8')
													->setName('depreciation_amount')
													->setId('depreciation_amount')
													->setValue($depreciation_amount)
													->setAttribute(array("readonly" => "readonly"))
													->setValidation('decimal')
													->draw($show_input);
										?>
									</div>
								</div>
				
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text hidden')
												->setSplit('col-md-3', 'col-md-8')
												->setName('frequency_of_dep')
												->setClass('hidden')
												->setId('frequency_of_dep')
												->setValue($frequency_of_dep)
												->setValidation('required integer')
												->draw($show_input);
									?>
								</div>
						
						</div>
					</div>
					<?php if($show_input):?>
					<div class="row text-center">
					<input type="button" id="compute" value="Compute Depreciation" class="btn btn-info">
					</div>
					<?php endif?>
					<br>
					<?php if($ajax_task == "ajax_create"){?>

					<table class="table table-hover table-sidepad" id="schedule" hidden>
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Date</th>
									<th class="col-md-3 text-center">Depreciation Amount</th>
									<th class="col-md-3 text-center">Accumulated Depreciation Amount</th>
									<th class="col-md-3 text-center">GL Account(Asset)</th>
									<th class="col-md-3 text-center">GL Account(Acc Dep)</th>
									<th class="col-md-3 text-center">GL Account(Depreciation Expense)</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
					</table>
					<?php }else{?>
					<table class="table table-hover table-sidepad" id="schedule">
							<thead>
								<tr class="info">
									<th class="col-md-2 text-center">Date</th>
									<th class="col-md-3 text-center">Depreciation Amount</th>
									<th class="col-md-3 text-center">Accumulated Depreciation Amount</th>
									<th class="col-md-3 text-center">GL Account(Asset)</th>
									<th class="col-md-3 text-center">GL Account(Acc Dep)</th>
									<th class="col-md-3 text-center">GL Account(Depreciation Expense)</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($schedule as $row){ ?>
											<tr>
											<td class="col-md-2 text-center"><?php echo $row->depreciation_date; ?></td>
											<td class="col-md-3 text-center"><?php echo number_format($row->depreciation_amount, 2); ?></td>
											<td class="col-md-3 text-center"><?php echo number_format($row->accumulated_dep, 2); ?></td>
											<td class="col-md-3 text-center"><?php echo $row->asset; ?></td>
											<td class="col-md-3 text-center"><?php echo $row->accdep; ?></td>
											<td class="col-md-3 text-center"><?php echo $row->depexpense; ?></td>
											</tr>



									<?php
									}
										?>
							</tbody>
					</table>
					<?php }?>

					

				</div>
				


				<div id="Accounting" class="tab-pane">
				<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
											->setLabel('GL Account (Asset)')
											->setSplit('col-md-3', 'col-md-8')
											->setName('gl_asset')
											->setId('gl_asset')
											->setList($coa_list)
											->setValue($gl_asset)
											->setValidation('required')
											->draw($show_input);
								?>
							</div>
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
											->setLabel('GL Account (Acc Dep)')
											->setSplit('col-md-3', 'col-md-8')
											->setName('gl_accdep')
											->setId('gl_accdep')
											->setList($coa_list)
											->setValue($gl_accdep)
											->setValidation('required')
											->draw($show_input);
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
											->setLabel('GL Account(Depreciation Expense)')
											->setSplit('col-md-3', 'col-md-8')
											->setName('gl_depexpense')
											->setId('gl_depexpense')
											->setList($coa_list)
											->setValue($gl_depexpense)
											->setAttribute(array("maxlength" => "1000"))
											->setValidation('required')
											->draw($show_input);
								?>
								</div>
						</div>
					</div>
					</div>
					<hr>
					<div class="row" style="padding-bottom: 15px">
						<div class="col-md-12 text-center">
							<?php echo $ui->drawSubmit($show_input); ?>
							<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
						</div>
					</div>
		</form>
	</section>
	<div class="modal fade" id="itemsModal" tabindex="-1" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
					<h4 class="modal-title">Items</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="paymentForm">
						<br/>
					
						<div class="table-responsive">
							<table class="table table-condensed table-bordered table-hover" id="itemlists">
								<thead>
									<tr class="info">
										<th class="col-md-2 text-center">Date</th>
										<th class="col-md-2 text-center">PO</th>
										<th class="col-md-2 text-center">Item Code</th>
										<th class="col-md-2 text-center">Unit Price</th>
									</tr>
								</thead>
								<tbody id="itemcode_list">
									
								</tbody>
								<tfoot>
									<tr>
										<td class="center" colspan = "7" id="app_page_links"></td>
									</tr>
								</tfoot>
							</table>
						</div>
						<div id="pagination"></div>
						<div class="modal-footer text-center">
						<button type="button" class="btn btn-default btn-flat" id="cancelprice" data-dismiss='modal'>Cancel</button>
						</div>
					
					</form>
				</div>
			</div>
		</div>
	</div>
<?php if ($show_input): ?>
<script>
$(document).ready(function(){
	if('<?=$ajax_task?>' == 'ajax_edit'){
		$('#capitalized_cost').prop('readonly',true);
		$('#purchase_value').prop('readonly',true);
		$('#balance_value').prop('readonly',true);
	}
});


var ajax = {};
	$('form').submit(function(e) {
			e.preventDefault();
			$(this).find('.form-group').find("select option:selected").prop('disabled',false);
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
				var first = $(this).find('.form-group.has-error').first().find('input, textarea, select');
				$('.nav.nav-tabs').find('a[href="#' + first.closest('.tab-pane').attr('id') + '"]').trigger('click');
				first.focus();
			}
			// $(this).find('.form-group').find("select option:selected").prop('disabled',true);
		});

$('#pagination').on('click', 'a', function(e) {
      e.preventDefault();
      ajax.page = $(this).attr('data-page');
      getItemList();
});

function getItemList() {
	var assname = $('#asset_name').val();
	var salval = $('#salvage_value').val();
	var bal = $('#balance_value').val();
	var number = $('#number_of_dep').val();
	var itemcode = $('#itemcode').val();
	
	
	$.post('<?=MODULE_URL?>ajax/ajax_get_itemcode', 'itemcode='+itemcode , function(data) {
		$('#itemcode_list').html(data.table);
		$('#pagination').html(data.pagination);
			if (ajax.page > data.page_limit && data.page_limit > 0) {
				ajax.page = data.page_limit;
				getItemList();
			}
		$('#description').val(data.description);
		$('#asset_name').val(data.itemname);
		$('#serial_number').val(data.barcode);
		});
}

$('#itemcode').on('change', function(){
	$('#itemsModal').modal('show');
	getItemList();
});

$('#itemlists').on('click', 'tr[data-id]', function() {
	var purchaseprice = $(this).attr('data-id');
	$('#purchase_value').val(purchaseprice);
		$('#itemsModal').modal('hide');
	});

// $('#itemlists').on('ifChecked','.pono',function(event){
// 	var purchaseprice = $(this).iCheck('checked').val();
		
// 	$('#itemsModal').on('click','#TagPP',function(event){
// 		$('#purchase_value').val(purchaseprice);
// 		$('#itemsModal').modal('hide');
// 	});
// });

$('#asset_class').on('change', function(){
	var assetclass = $('#asset_class').val();
	$.post('<?=MODULE_URL?>ajax/ajax_get_assetclass', 'assetclass='+assetclass , function(data) {
		$('#useful_life').val(data.useful_life).trigger('change');
		$('#salvage_value').val(data.salvage_value).trigger('change');
		$('#gl_asset').val(data.gl_asset).trigger('change');
		$('#gl_accdep').val(data.gl_accdep).trigger('change');
		$('#gl_depexpense').val(data.gl_depexpense).trigger('change');
		});
});

function getList() {
	var assname = $('#asset_name').val();
	var salval = $('#salvage_value').val();
	var bal = $('#balance_value').val();
	var number = $('#number_of_dep').val();
	
	$.post('<?=MODULE_URL?>ajax/ajax_view_schedule', $('#form').serialize() , function(data) {
		$('#schedule tbody').html(data.table);
		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			getList();
		}
	});
}

$('#compute').on('click', function(){
	var salval = $('#salvage_value').val();
	salval     = salval.replace(/\,/g,'');
	var bal = $('#balance_value').val();
	bal     = bal.replace(/\,/g,'');
	var number = $('#number_of_dep').val();
	number     = number.replace(/\,/g,'');

	$('#schedule').removeAttr('hidden');
	var dep_amount = (parseFloat(bal) - parseFloat(salval)) / parseInt(number);
	$('#depreciation_amount').val(dep_amount);
	getList();
	
});

$('#cancelprice').on('click', function(){
	$('#itemcode').val('').trigger('change');
});

$('body').on('blur blur_validate keyup keydown', '[data-validation~="alpha_num_special"]', function(e) {
    var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z 0-9</p><p>Letters and Numbers Only</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if (! (/^[a-zA-Z0-9., &()\[\]_\-':;]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
</script>
<?php endif ?>