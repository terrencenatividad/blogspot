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
								<?php if($ajax_task == 'ajax_edit') : ?>
									<div class="row">
										<div class="col-md-6">
											<a class="btn btn-info" data-target = '#import-modal' data-toggle = "modal">Import</a>
										</div>
									</div>
								<?php endif; ?>
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
										->setLabel('Budget Cost Center')
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
										->setLabel('Approver')
										->setSplit('col-md-3', 'col-md-8')
										->setName('approver')
										->setId('approver')
										->setAttribute(array('readonly' => 'readonly'))
										->setValue($approver)
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
											<?php if($status == 'for approval') { 
												echo $ui->drawSubmit($show_input);
											} else if($ajax_task == 'ajax_create' AND $status == ''){
												echo $ui->drawSubmit($show_input);
											}
											?>
											<a class="btn btn-default btn-flat" id = "btnCancel">Cancel</a>
											<div class="col-md-5 col-sm-4 col-xs-4"></div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</section>

				<div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								Confirmation
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								Are you sure you want to cancel this transaction?
							</div>
							<div class="modal-footer">
								<div class="row row-dense">
									<div class="col-md-12 center">
										<div class="btn-group">
											<button type="button" class="btn btn-primary btn-flat" id="btnCancelYes">Yes</button>
										</div>
										&nbsp;&nbsp;&nbsp;
										<div class="btn-group">
											<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="modal fade" id="import-modal" tabindex="-1" data-backdrop="static">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span></button>
										<h4 class="modal-title">Import Budget Accounts</h4>
									</div>
									<div class="modal-body">
										<label>Step 1. Download the sample template 
											<a href="<?=BASE_URL?>modules/maintenance_module/backend/view/
												pdf/import_budget.csv">here</a>
											</label>
											<hr/>
											<label>Step 2. Fill up the information needed for each columns of the template.
											</label>
											<hr/>
											<div class="form-group field_col">
												<label for="import_csv">
													Step 3. Select the updated file and click 'Import' to proceed.
												</label>
												<?php
												echo $ui->setElement('file')
												->setId('import_csv')
												->setName('import_csv')
												->setAttribute(array('accept' => '.csv'))
												->setValidation('required')
												->draw();
												?>
												<span class="help-block"></span>
											</div>
											<p class="help-block">The file to be imported must be in CSV 
											(Comma Separated Values) file.</p>
										</div>
										<div class="modal-footer text-center">
											<button type="button" class="btn btn-info btn-flat" id="btnImport">Import</button>
											<button type="button" class="btn btn-default btn-flat" 
											data-dismiss="modal">Close</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<script>
							$('#budget_center_code').on('change', function() {
								$.post('<?=MODULE_URL?>ajax/ajax_get_approver', '&budget_code=' + $(this).val(), function(data) {
									if(data) {
										$('#approver').val(data.approver);
									}
								});
							});

							$('#btnCancel').click(function() 
							{
								$('#cancelModal').modal('show');
							});

							$('#btnCancelYes').on('click', function() {
								window.location = '<?= MODULE_URL ?>';
							});

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