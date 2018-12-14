		<section class="content">
			<div class = "alert alert-warning alert-dismissable hidden">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4><strong>Error!<strong></h4>
					<div id = "errmsg"></div>
				</div>

				<div class="box box-primary">
					<div class="panel panel-default">
						
						<form class="form-horizontal form-group" method="POST" id="coaForm" autocomplete="off">
							<div class="panel-body">
								<div class="row">
									<?php if($ajax_task == 'ajax_edit') : ?>
										<div class="col-md-6 hidden">
											<?
											echo $ui->formField('text')
											->setLabel('Budget Code ')
											->setSplit('col-md-3', 'col-md-8')
											->setName('budget_code')
											->setId('budget_code')
											->setValue($budget_code)
											->draw($show_input);	
											?>	
										</div>
									<?php endif; ?>
									<div class="col-md-6">
										<?
										echo $ui->formField('dropdown')
										->setLabel('Budget Center Code')
										->setPlaceholder('Select one')
										->setSplit('col-md-3', 'col-md-8')
										->setName('budget_center_code')
										->setId('budget_center_code')
										->setList($budget_center)
										->setNone('none')
										->setValue($budget_center_code)
										->setValidation('required')
										->draw($show_input);	
										?>	
									</div>	
									<div class="col-md-6">
										<?php
										echo $ui->formField('text')
										->setLabel('Date')
										->setSplit('col-md-3', 'col-md-8')
										->setName('transactiondate')
										->setId('transactiondate')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($transactiondate)
										->setValidation('required')
										->draw($show_input);
										?>
									</div>
									
								</div>
								<div class="row">
									<div class="col-md-6">
										<?
										echo $ui->formField('textarea')
										->setLabel('Budget Description')
										->setSplit('col-md-3', 'col-md-8')
										->setName('budgetdesc')
										->setId('budgetdesc')
										->setMaxLength(250)
										->setValue($budgetdesc)
										->draw($show_input);	
										?>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?
										echo $ui->formField('dropdown')
										->setLabel('Budget Type')
										->setPlaceholder('Select one')
										->setSplit('col-md-3', 'col-md-8')
										->setName('budget_type')
										->setId('budget_type')
										->setList(array('BS' => 'Balance Sheet', 'IS' => 'Income Statement'))
										->setValue($budget_type)
										->setValidation('required')
										->draw($show_input);	
										?>	
									</div>
									<div class="col-md-6">
										<?
										echo $ui->formField('dropdown')
										->setLabel('Budget Check')
										->setPlaceholder('Select one')
										->setSplit('col-md-3', 'col-md-8')
										->setName('budget_check')
										->setId('budget_check')
										->setList(array('Monitored' => 'Monitored', 'Controlled' => 'Controlled'))
										->setValue($budget_check)
										->setValidation('required')
										->draw($show_input);	
										?>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?
										echo $ui->formField('dropdown')
										->setPlaceholder('Select one')
										->setLabel('Owner')
										->setSplit('col-md-3', 'col-md-8')
										->setName('owner')
										->setId('owner')
										->setList($user_list)
										->setValue($owner)
										->draw($show_input);	
										?>	
									</div>
									<div class="col-md-6">
										<?
										echo $ui->formField('text')
										->setLabel('Prepared By')
										->setSplit('col-md-3', 'col-md-8')
										->setName('prepared_by')
										->setId('prepared_by')
										->setAttribute(array('readonly'))
										->setValue($prepared_by)
										->draw($show_input);	
										?>	
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?
										echo $ui->formField('text')
										->setPlaceholder('Select one')
										->setLabel('Approved by')
										->setSplit('col-md-3', 'col-md-8')
										->setName('approved_by')
										->setId('approved_by')
										->setList($user_list)
										->setValue($approved_by)
										->draw($show_input);	
										?>	
									</div>
								</div>
								<div class="container">
									<div class="panel panel-default">
										<div class="table-responsive">
											<fieldset>
												<table class="table table-hover table-condensed " id="itemsTable">
													<thead>
														<tr class="info">
															<th class="col-md-4 text-center">Account</th>
															<th class="col-md-4 text-center">Description</th>
															<th class="col-md-4 text-center">Amount</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
													<tfoot>
														<tr>
															<td></td>
															<td></td>
															<td><?
															echo $ui->formField('text')
															->setLabel('Total')
															->setSplit('col-md-3', 'col-md-8')
															->setName('total')
															->setId('total')
															->setValue($total)
															->setClass('text-right')
															->setAttribute(array('readonly'))
															->draw($show_input);	
															?></td>
														</tr>
													</tfoot>
												</table>
											</fieldset>
										</div>
									</div>
								</div>
								<div class="panel-footer">
									<div class="row center">
										<div class="col-md-5 col-sm-4 col-xs-4"></div>
										<div class="col-md-2 col-sm-3 col-xs-3" id="task_buttons" style="padding:3px;">
											<?php echo $ui->drawSubmit($show_input); ?>
											<a href="<?=MODULE_URL?>" class="btn btn-default btn-flat">Cancel</a>
											<div class="col-md-5 col-sm-4 col-xs-4"></div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</section>
				<script>
					$('#budget_type').on('change', function() {
						var type = $(this).val();
						if(type != 'none') {
							$.post('<?=MODULE_URL?>ajax/ajax_get_accounts','&type=' + type, function(data) {
								$('#itemsTable tbody').html(data.table);
							});
						} else {
							$('#itemsTable tbody').html();
						}
					});
					<?php if($ajax_task != 'ajax_create') : ?>
						$(document).ready(function() {
							$.post('<?=MODULE_URL?>ajax/ajax_get_accounts_edit','&budgetcode=' + '<?= $budget_code ?>' + '&ajax_task=' + '<?=$ajax_task?>', function(data) {
								$('#itemsTable tbody').html(data.table);
							});
						});
					<?php endif; ?>

					$('#itemsTable').on('blur', '#amount', function() {
						sum();
					});

					function sum() {
						var total = 0;
						$('#itemsTable tbody tr #amount').each(function() {
							var amount = $(this).val();
							total += +removeComma(amount);
							$('#total').val(addComma(total));
						});
					}

					<?php if ($show_input): ?>
						$('form').submit(function(e) {
							e.preventDefault();
							$(this).find('.form-group').find('input, textarea, select').trigger('blur');
							if ($(this).find('.form-group.has-error').length == 0) {
								$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post ?>', function(data) {
									if (data.success) {
										$('#delay_modal').modal('show');
										setTimeout(function() {
											window.location = data.redirect;
										},500);
									}
								});
							} else {
								$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
							}
						});
					<?php endif ?>
				</script>