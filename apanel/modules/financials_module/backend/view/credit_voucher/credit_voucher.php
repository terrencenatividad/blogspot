<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php if ($show_input): ?>
										<div class="form-group">
											<label for="voucherno" class="control-label col-md-4">Voucher No.</label>
											<div class="col-md-8">
												<?php if (substr($voucherno, 0, 3) == 'TMP'): ?>
													<input type="text" class="form-control" readonly value=" - Auto Generated - ">
													<?php else: ?> 
														<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
													<?php endif ?>
												</div>
											</div>
											<?php else: ?>
												<?php
												echo $ui->formField('text')
												->setLabel('Voucher No.')
												->setSplit('col-md-4', 'col-md-8')
												->setName('voucherno')
												->setId('voucherno')
												->setValue($voucherno)
												->setValidation('required')
												->draw($show_input);
												?>
											<?php endif ?>
										</div>
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Date')
											->setSplit('col-md-4', 'col-md-8')
											->setName('date')
											->setId('date')
											->setClass('datepicker-input')
											->setAddon('calendar')
											->setValue($date)
											->setValidation('required')
											//->setAttribute(array('readonly'=>'','data-date-start-date'=>$close_date))
											->draw($show_input);
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Reference ')
											->setSplit('col-md-4', 'col-md-8')
											->setName('reference')
											->setId('reference')
											->setValue($reference)
											->setValidation('required')
											->draw($show_input);
											?>
										</div>
										<div class="col-md-6">
											<?php
											echo $ui->formField('textarea')
											->setLabel('Notes')
											->setSplit('col-md-4', 'col-md-8')
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
							<table id="tableList" class="table table-hover table-sidepad">
								<thead>
									<tr class="info">
										<th class="col-xs-3">Item</th>
										<th>Description</th>
										<th class="col-xs-2 text-right">Amount</th>
										<th style="width: 50px;"></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
								<tfoot>
									<tr>
										<td>
											<?php if ($show_input): ?>
												<button type="button" class="btn btn-link" onClick="addRefurbishDetails()">Add a New Line</button>
											<?php endif ?>
										</td>
										<td>
											<p id="error_msg" class="help-block text-red text-right"></p>
										</td>
										<td class="text-right">
											<b id="total_debit" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
										</td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="box-body">
							<hr>
							<div class="row">
								<div class="col-md-12 text-center">
									<?php
									if($ajax_task == 'ajax_create' ){
										echo $ui->addSavePreview()
										->addSaveNew()
										->addSaveExit()
										->drawSaveOption();
									}

									if($ajax_task == 'ajax_view') {
										echo $ui->drawSubmit($show_input);
									} else if($ajax_task == 'ajax_edit') {
										echo $ui->drawSubmit(true);
									}

									echo $ui->drawCancel();
									?>
								</div>
							</div>
						</div>
					</form>
				</div>
			</section>
			<script>
				var delete_row		= {};
				var ajax_call		= '';
				var ajax_call2		= '';
				var min_row			= 2;
				function addRefurbishDetails(details, index) {
					var details = details || {item: '', description: '', detail_amount: ''};
					var row = `
					<tr>
					<td>
					<?php
					$value = ($show_input) ? '' : '<span id="temp_view_` + index + `"></span>';
					echo $ui->formField('text')
					->setPlaceholder('Item')
					->setSplit('', 'col-md-12')
					->setName('item[]')
					->setValidation('required')
					->setValue('` + details.item + `')
					->draw($show_input);
					?>
					</td>
					<td>
					<?php
					echo $ui->formField('text')
					->setPlaceholder('Description')
					->setSplit('', 'col-md-12')
					->setName('description[]')
					->setValue('` + details.description + `')
					->draw($show_input);
					?>
					</td>
					<td class="text-right amount_column">
					<?php
					echo $ui->formField('text')
					->setSplit('', 'col-md-12')
					->setId('detail_amount')
					->setName('detail_amount[]')
					->setClass('text-right')
					->setValidation('required decimal')
					->setValue('` + (parseFloat(details.detail_amount) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + `')
					->draw($show_input);
					?>
					</td>
					<td>
					<?php if ($show_input): ?>
						<button type="button" class="btn btn-danger delete_row" data-id="222" style="outline:none;">
						<span class="glyphicon glyphicon-trash"></span>
						</button>
					<?php endif ?>
					</td>
					</tr>
					`;
					$('#tableList tbody').append(row);
					}
					var refurbish_details = <?=$refurbish_details?>;
					function displayDetails(refurbish_details) {
						$('#tableList tbody').html('');
						if (refurbish_details.length > 0) {
							refurbish_details.forEach(function(refurbish_details, index) {
								addRefurbishDetails(refurbish_details, index);
							});
						} else if (min_row == 0) {
							$('#tableList tbody').append(`
								<tr>
								<td colspan="5" class="text-center"><b>Select Packing No.</b></td>
								</tr>
								`);
						}
						if (refurbish_details.length < min_row) {
							for (var x = refurbish_details.length; x < min_row; x++) {
								addRefurbishDetails('', x);
							}
						}
					}
					displayDetails(refurbish_details);
					<?php if ($show_input): ?>
						function deleteVoucherDetails(id) {
							delete_row.remove();
							if ($('#tableList tbody tr').length <= 1) {
								addRefurbishDetails();
							}
						}
						$('body').on('click', '.delete_row', function() {
							delete_row = $(this).closest('tr');
						});
						$(function() {
							linkDeleteToModal('.delete_row', 'deleteVoucherDetails');
						});
						$('form').on('click', '[id="save"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
								if (form_element.closest('form').find('.form-group.has-error').length == 0) {
									$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' + '&finalized=finalized' + submit_data, function(data) {
										if (data.success) {
											$('#delay_modal').modal('show');
											setTimeout(function() {							
												window.location = data.redirect;						
											}, 1000)
										}
									});
								} else {
									form_element.closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
								}
						});
						$('form').on('click', '[id="save_new"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (form_element.closest('form').find('.form-group.has-error').length == 0) {
								$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' + '&finalized=finalized' + submit_data, function(data) {
									if (data.success) {
										$('#delay_modal').modal('show');
										setTimeout(function() {							
											window.location = data.redirect;						
										}, 1000)
									}
								});
							} else {
								form_element.closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
							}
						});
						$('form').on('click', '[id="save_exit"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (form_element.closest('form').find('.form-group.has-error').length == 0) {
								$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' + '&finalized=finalized' + submit_data, function(data) {
									if (data.success) {
										$('#delay_modal').modal('show');
										setTimeout(function() {							
											window.location = data.redirect;						
										}, 1000)
									}
								});
							} else {
								form_element.closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
							}
						});
						$('form').on('click', '[type="submit"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (form_element.closest('form').find('.form-group.has-error').length == 0) {
								$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' + '&finalized=finalized' + submit_data, function(data) {
									if (data.success) {
										$('#delay_modal').modal('show');
										setTimeout(function() {							
											window.location = data.redirect;						
										}, 1000)
									}
								});
							} else {
								form_element.closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
							}
						});
						<?php endif ?>
					</script>