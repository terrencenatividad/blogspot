<section class="content">
		<form action="" method="post" class="form-horizontal">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#Details" data-toggle="tab">Item Details</a></li>
					<li><a href="#UOM" data-toggle="tab">Unit of Measure</a></li>
					<li><a href="#Accounting" data-toggle="tab">Accounting Details</a></li>
				</ul>
				<div class="tab-content no-padding">
					<div id="Details" class="tab-pane active" style="padding: 15px">
						<div class="row">
							<div class="col-md-11">
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Item Code ')
												->setAttribute(array('autocomplete' => 'off'))
												->setSplit('col-md-4', 'col-md-8')
												->setName('itemcode')
												->setId('itemcode')
												->setValue($itemcode)
												->addHidden((isset($ajax_task) && $ajax_task == 'ajax_edit'))
												->setValidation('required code')
												->draw((isset($ajax_task) && $ajax_task == 'ajax_create'));
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Item Name ')
												->setSplit('col-md-4', 'col-md-8')
												->setName('itemname')
												->setId('itemname')
												->setValue($itemname)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Barcode')
												->setSplit('col-md-4', 'col-md-8')
												->setName('barcode')
												->setId('barcode')
												->setValue($barcode)
												->setMaxLength(50)
												->setValidation('alpha_num')
												->draw($show_input);
										?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('textarea')
												->setLabel('Item Desc ')
												->setSplit('col-md-4', 'col-md-8')
												->setName('itemdesc')
												->setId('itemdesc')
												->setValue($itemdesc)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Item Class ')
												->setPlaceholder('Select Item Class')
												->setSplit('col-md-4', 'col-md-8')
												->setName('classid')
												->setId('classid')
												->setList($itemclass_list)
												->setValue($classid)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Item Group ')
												->setPlaceholder('Select Item Group')
												->setSplit('col-md-4', 'col-md-8')
												->setName('itemgroup')
												->setId('itemgroup')
												->setList($groups_list)
												->setValue($itemgroup)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Item Type ')
												->setPlaceholder('Select Item Type')
												->setSplit('col-md-4', 'col-md-8')
												->setName('typeid')
												->setId('typeid')
												->setList($itemtype_list)
												->setValue($typeid)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Weight')
												->setSplit('col-md-4', 'col-md-8')
												->setName('weight')
												->setId('weight')
												->setValue($weight)
												->setValidation('decimal')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Weight Type')
												->setPlaceholder('Select Weight Type')
												->setSplit('col-md-4', 'col-md-8')
												->setName('weight_type')
												->setId('weight_type')
												->setList($weight_type_list)
												->setValue($weight_type)
												->draw($show_input);
										?>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="bundle" class="control-label col-md-4">Bundle</label>
											<div class="col-md-1">
												<?
													echo $ui->setElement('checkbox')
															->setName('bundle')
															->setId('bundle')
															->setDefault(1)
															->setValue($bundle)
															->setAttribute(array('style'=>"position:absolute; opacity:0;"))
															->draw($show_input);
												?>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="serialized" class="control-label col-md-4">Serial #</label>
											<div class="col-md-1">
												<?
													echo $ui->setElement('checkbox')
															->setName('serialized')
															->setId('serialized')
															->setDefault(1)
															->setValue($serialized)
															->setAttribute(array('style'=>"position:absolute; opacity:0;"))
															->draw($show_input);
												?>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="engine" class="control-label col-md-4">Engine #</label>
											<div class="col-md-1">
												<?
													echo $ui->setElement('checkbox')
															->setName('engine')
															->setId('engine')
															->setDefault(1)
															->setValue($engine)
															->setAttribute(array('style'=>"position:absolute; opacity:1;"))
															->draw($show_input);
												?>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="chassis" class="control-label col-md-4">Chassis #</label>
											<div class="col-md-1">
												<?
													echo $ui->setElement('checkbox')
															->setName('chassis')
															->setId('chassis')
															->setDefault(1)
															->setValue($chassis)
															->setAttribute(array('style'=>"position:absolute; opacity:0;"))
															->draw($show_input);
												?>
											</div>	
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Brand')
												->setPlaceholder('Select Brand')
												->setSplit('col-md-4', 'col-md-8')
												->setName('brand')
												->setId('brand')
												->setList($brand_list)
												->setValue($brandcode)
												->setNone('none')
												->draw($show_input);
										?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="replacement_part" class="control-label col-md-4">Replacement Part</label>
											<div class="col-md-1">
												<?
													echo $ui->setElement('checkbox')
															->setName('replacement_part')
															->setId('replacement_part')
															->setDefault(1)
															->setValue($replacement)
															->setAttribute(array('style'=>"position:absolute; opacity:0;"))
															->draw($show_input);
												?>
											</div>
										</div>
									</div>	
									<div class="col-md-6" id="replacement_selection">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Replacement For')
												->setPlaceholder('Select Replacement Item')
												->setSplit('col-md-4', 'col-md-8')
												->setName('replacement_for')
												->setId('replacement_for')
												->setList($existing_item_list)
												->setAttribute(array('disabled'=>true))
												->setValue($replacementcode)
												->setNone('none')
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="UOM" class="tab-pane">
						<table class="table table-hover table-sidepad">
							<thead>
								<tr class="info">
									<th class="col-xs-2"></th>
									<th class="col-xs-5 text-center">Unit of Measure</th>
									<th class="col-xs-2 text-center">Base UOM Conversion</th>
									<th class="col-xs-3"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-right">
										<label class="form-control-static">Base UOM </label>
									</td>
									<td class="text-center uom_base_td">
										<?php
											echo $ui->formField('dropdown')
												->setPlaceholder('Select UOM')
												->setSplit('', 'col-md-12')
												->setId('uom_base')
												->setName('uom_base')
												->setList($uom_list)
												->setValidation('required')
												->setValue($uom_base)
												->draw($show_input);
										?>
									</td>
									<td class="text-center">
										<p class="form-control-static">1</p>
									</td>
									<td>
									
									</td>
								</tr>
								<tr>
									<td class="text-right">
										<label class="form-control-static">Selling UOM </label>
									</td>
									<td class="text-center">
										<?php
											echo $ui->formField('dropdown')
												->setPlaceholder('Select UOM')
												->setSplit('', 'col-md-12')
												->setName('uom_selling')
												->setList($uom_list)
												->setValidation('required')
												->setValue($uom_selling)
												->draw($show_input);
										?>
									</td>
									<td class="text-center">
										<?php
											echo $ui->formField('text')
												->setPlaceholder('Quantity')
												->setSplit('', 'col-md-12')
												->setClass('text-center')
												->setName('selling_conv')
												->setValidation('required integer')
												->setValue($selling_conv)
												->draw($show_input);
										?>
									</td>
									<td>
										<label class="form-control-static uom_in">in <span>Base Unit of Measure</span></label>
									</td>
								</tr>
								<tr>
									<td class="text-right">
										<label class="form-control-static">Purchasing UOM </label>
									</td>
									<td class="text-center">
										<?php
											echo $ui->formField('dropdown')
												->setPlaceholder('Select UOM')
												->setSplit('', 'col-md-12')
												->setName('uom_purchasing')
												->setList($uom_list)
												->setValidation('required')
												->setValue($uom_purchasing)
												->draw($show_input);
										?>
									</td>
									<td class="text-center">
										<?php
											echo $ui->formField('text')
												->setPlaceholder('Quantity')
												->setSplit('', 'col-md-12')
												->setClass('text-center')
												->setName('purchasing_conv')
												->setValidation('required integer')
												->setValue($purchasing_conv)
												->draw($show_input);
										?>
									</td>
									<td>
										<label class="form-control-static uom_in">in <span>Base Unit of Measure</span></label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="Accounting" class="tab-pane" style="padding: 15px">
						<div class="row">
							<div class="col-md-11">
								<h4>Sales Details <small>(optional)</small></h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Debit Account')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('receivable_account')
												->setList($receivable_account_list)
												->setValue($receivable_account)
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Credit Account')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('revenue_account')
												->setList($revenue_account_list)
												->setValue($revenue_account)
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-11">
								<h4>Purchase Details <small>(optional)</small></h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Debit Account')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('expense_account')
												->setList($expense_account_list)
												->setValue($expense_account)
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Credit Account')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('payable_account')
												->setList($payable_account_list)
												->setValue($payable_account)
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-11">
								<h4>Inventory Details <small>(optional)</small></h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Inventory Account')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('inventory_account')
												->setList($chart_account_list)
												->setValue($inventory_account)
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-11">
								<h4>Tax Information <small>(optional)</small></h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Revenue Type')
												->setPlaceholder('Select Revenue Type')
												->setSplit('col-md-4', 'col-md-8')
												->setName('revenuetype')
												->setList($revenuetype_list)
												->setValue($revenuetype)
												->setNone('None')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Expense Type')
												->setPlaceholder('Select Expense Type')
												->setSplit('col-md-4', 'col-md-8')
												->setName('expensetype')
												->setList($expensetype_list)
												->setValue($expensetype)
												->setNone('None')
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12 text-center" style="padding-bottom: 15px">
							<?php echo $ui->drawSubmit($show_input); ?>
							<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
	<?php if ($show_input): ?>
	<script>
		var ajax_call = '';
		var task 	  = '<?=$ajax_task?>';
		var uom = $('#uom_base option:selected').html();
		if (uom != '') {
			$('.uom_in span').html(uom);
		}
		$('#itemcode').on('input', function() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			var itemcode = $(this).val();
			$('#itemcode').closest('form').find('[type="submit"]').addClass('disabled');
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_check_itemcode', 'itemcode=' + itemcode + '<?=$ajax_post?>', function(data) {
				var error_message = 'Item Code already Exist';
				if (data.available) {
					var form_group = $('#itemcode').closest('.form-group');
					if (form_group.find('p.help-block').html() == error_message) {
						form_group.removeClass('has-error').find('p.help-block').html('');
					}
				} else {
					$('#itemcode').closest('.form-group').addClass('has-error').find('p.help-block').html(error_message);
				}
				$('#itemcode').closest('form').find('[type="submit"]').removeClass('disabled');
			});
		});
		$('#uom_base').change(function() {
			var uom = $(this).find('option:selected').html();
			$('.uom_in span').html(uom);
		});

		function uncheck_checkboxes(){
			$("#chassis").iCheck('uncheck');
			$("#serialized").iCheck('uncheck');
			$("#engine").iCheck('uncheck');
			$('#replacement_part').iCheck('uncheck');
		}

		function disable_checkboxes($status){
			uncheck_checkboxes();
			$("#chassis").prop('disabled',$status).iCheck('update');
			$("#serialized").prop('disabled',$status).iCheck('update');
			$("#engine").prop('disabled',$status).iCheck('update');
			$('#replacement_part').prop('disabled',$status).iCheck('update');
		}
		
		if($('#bundle').attr('checked') && task == 'ajax_edit'){
			disable_checkboxes(1);
		}
		// For Replacement For Dropdown
		if($('#replacement_part').attr('checked') && task == 'ajax_edit'){
			$('#replacement_for').prop('disabled',false);
		}

		$('form').on('ifChecked','#bundle',function(e){
			disable_checkboxes(1);
			$('#replacement_for').val('none');
			drawTemplate();
		});

		$('form').on('ifUnchecked','#bundle',function(e){
			disable_checkboxes(0);
		});

		$('form').on('ifChecked','#replacement_part', function(e){
			$("#replacement_for").prop('disabled',false);
		});

		$('form').on('ifUnchecked','#replacement_part', function(e){
			$("#replacement_for").prop('disabled',true);
			$("#replacement_for").val('');
			drawTemplate();
		});

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
			$(this).find('.form-group').find("select option:selected").prop('disabled',true);
		});
		
			
	</script>
	<?php else: ?>
	<script>
		var uom = $('.uom_base_td .form-control-static').html();
		if (uom != '') {
			$('.uom_in span').html(uom);
		}
	</script>
	<?php endif ?>