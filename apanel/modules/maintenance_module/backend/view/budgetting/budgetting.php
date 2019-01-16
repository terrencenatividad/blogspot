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
											->setSplit('col-md-3', 'col-md-8')
											->setName('id')
											->setId('id')
											->setValue($ajax_post)
											->draw($show_input);	
											?>	
										</div>
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
										->setLabel('Budget Center')
										->setPlaceholder('Select one')
										->setSplit('col-md-3', 'col-md-8')
										->setName('budget_center_code')
										->setId('budget_center_code')
										->setList($budget_center)
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
									<div class="col-md-6">
										<?php
										echo $ui->formField('text')
										->setLabel('Effectivity Date')
										->setSplit('col-md-3', 'col-md-8')
										->setName('effectivity_date')
										->setId('effectivity_date')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($effectivity_date)
										->setValidation('required')
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
								<div class="nav-tabs-custom">
									<?php if($ajax_task == 'ajax_view' && $status == 'approved' && count($budget_supplement) != 0) : ?>
										<ul id="filter_tabs" class="nav nav-tabs">
											<li class="active"><a href="#Budgets" data-toggle="tab" data-id="Quarterly">Budgets</a></li>
											<li><a href="#Supplements" data-toggle="tab" data-id="Monthly">Budget Supplements</a></li>
										</ul>
									<?php endif; ?>
									<?php if($ajax_task != 'ajax_view') : ?>
										<i>Make sure to choose a Budget Type</i>
									<?php endif; ?>
									<div class="panel panel-default">
										<div class="table-responsive">
											<fieldset>
												<div class="tab-content no-padding">
													<div id="Budgets" class="tab-pane table-responsive active">
														<table class="table table-hover table-condensed " id="itemsTable">
															<thead>
																<tr class="info">
																	<th class="col-md-4 text-center">Account</th>
																	<th class="col-md-4 text-center">Description</th>
																	<th class="col-md-3 text-center">Amount</th>
																	<th class="col-md-4 text-center"></th>
																</tr>
															</thead>
															<tbody>
																<?php if($ajax_task == 'ajax_create') { ?>
																	<tr>
																		<td><?
																		echo $ui->formField('dropdown')
																		->setPlaceholder('~Select One~')
																		->setSplit('', 'col-md-12')
																		->setName('accountcode[]')
																		->setId('accountcode')
																		->setClass('accountname')
																		->setValidation('required')
																		->draw($show_input);	
																		?></td>
																		<td><?
																		echo $ui->formField('text')
																		->setSplit('', 'col-md-12')
																		->setName('description[]')
																		->setId('description')
																		->setClass('description')
																		->draw($show_input);	
																		?></td>
																		<td><?
																		echo $ui->formField('text')
																		->setPlaceholder('0.00')
																		->setSplit('', 'col-md-12')
																		->setName('amount[]')
																		->setId('amount')
																		->setClass('text-right amount')
																		->setValidation('decimal required')
																		->draw($show_input);	
																		?></td>
																		<td class="text-center">
																			<button type="button" class="btn btn-danger btn-flat confirm-delete" style="outline:none;"><span class="glyphicon glyphicon-trash"></span></button>
																		</td>
																	</tr>
																<?php } else { ?>
																	<?php if(isset($budget_details)) : ?>
																		<?php foreach($budget_details as $row) : ?>
																			<tr>
																				<td><?
																				echo $ui->formField('dropdown')
																				->setPlaceholder('~Select One~')
																				->setSplit('', 'col-md-12')
																				->setName('accountcode[]')
																				->setId('accountcode')
																				->setClass('accountname')
																				->setList($get_accounts)
																				->setValue($row->accountcode)
																				->setValidation('required')
																				->draw($show_input);	
																				?></td>
																				<td><?
																				echo $ui->formField('text')
																				->setSplit('', 'col-md-12')
																				->setName('description[]')
																				->setId('description')
																				->setClass('description')
																				->setValue($row->description)
																				->draw($show_input);	
																				?></td>
																				<td><?
																				echo $ui->formField('text')
																				->setPlaceholder('0.00')
																				->setSplit('', 'col-md-12')
																				->setName('amount[]')
																				->setId('amount')
																				->setClass('text-right amount')
																				->setValue($row->amount)
																				->setValidation('decimal required')
																				->draw($show_input);	
																				?></td>
																				<?php if($ajax_task != 'ajax_view') : ?>
																					<td class="text-center">
																						<button type="button" class="btn btn-danger btn-flat confirm-delete" style="outline:none;"><span class="glyphicon glyphicon-trash"></span></button>
																					<?php endif; ?>
																				</td>
																			</tr>
																		<?php endforeach; ?>
																	<?php endif; ?>
																<?php } ?>
															</tbody>
															<tfoot>
																<tr>
																	<?php if($ajax_task != 'ajax_view') : ?>
																		<td><a href="javascript:void(0);" class = "add-data">Add A New Line</a></td>
																	<?php endif; ?>
																</tr>
															</tfoot>
														</table>
													</div>
													<div id="Supplements" class="tab-pane table-responsive">
														<table class="table table-hover table-condensed " id="tableSupplement">
															<thead>
																<tr class="info">
																	<th class="col-md-4 text-center">Account</th>
																	<th class="col-md-4 text-center">Description</th>
																	<th class="col-md-4 text-center">Amount</th>
																</tr>
															</thead>
															<tbody>
																<?php if(isset($budget_supplement)) : ?>
																	<?php foreach($budget_supplement as $row) : ?>
																		<tr>
																			<td><?
																			echo $ui->formField('dropdown')
																			->setPlaceholder('~Select One~')
																			->setSplit('', 'col-md-12')
																			->setName('accountcode[]')
																			->setId('accountcode')
																			->setClass('accountname')
																			->setList($get_accounts)
																			->setValue($row->accountcode)
																			->setValidation('required')
																			->draw($show_input);	
																			?></td>
																			<td><?
																			echo $ui->formField('text')
																			->setSplit('', 'col-md-12')
																			->setName('description[]')
																			->setId('description')
																			->setClass('description')
																			->setValue($row->description)
																			->draw($show_input);	
																			?></td>
																			<td><?
																			echo $ui->formField('text')
																			->setPlaceholder('0.00')
																			->setSplit('', 'col-md-12')
																			->setName('amount[]')
																			->setId('amount')
																			->setClass('text-right amount')
																			->setValue($row->amount)
																			->setValidation('decimal required')
																			->draw($show_input);	
																			?></td>
																		</tr>
																	<?php endforeach; ?>
																<?php endif; ?>
															</tbody>
														</table>
													</div>
												</div>
											</fieldset>
										</div>
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

			<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							Confirmation
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							Are you sure you want to delete this line?
							<input type="hidden" id="recordId"/>
						</div>
						<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 center">
									<div class="btn-group">
										<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
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

						var deleterow = '';
						$('.confirm-delete').on('click', function() {
							var one = 0;
							$('#itemsTable tbody tr td .confirm-delete').each(function() {
								one++;
							});

							if(one > 1) {
								$('#deleteItemModal').modal('show');
								deleterow = $(this).closest('tr');
							}
						});

						$('#btnYes').on('click', function() {
							deleterow.remove();
							$('#deleteItemModal').modal('hide');
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
									$('#itemsTable tbody tr .accountname').html(data.ret).val('').trigger('');
									$('#itemsTable tbody tr .description').val('');
									$('#itemsTable tbody tr .amount').val('');
								});
							} else {
								$('#itemsTable tbody tr .accountname').html().val('').trigger('');
								$('#itemsTable tbody tr .description').val('');
								$('#itemsTable tbody tr .accountname').val('');
							}
						});

						$('.add-data').on('click', function() {
							$('#itemsTable tbody tr select').select2('destroy');

							var clone = $("#itemsTable tbody tr:first").clone(true); 

							var ParentRow = $("#itemsTable tbody tr").last();

							clone.clone(true).insertAfter(ParentRow);

							$('#itemsTable tbody tr select').select2({width: "100%"});
							$('#itemsTable tbody tr .accountname').last().val('').trigger('change');
							$('#itemsTable tbody tr .accountname').find('.form-group').removeClass('has-error');
							$('#itemsTable tbody tr .accountname').closest('.form-group').find('.help-block').html('');
							$('#itemsTable tbody tr .description').last().val('');
							$('#itemsTable tbody tr .amount').last().val('');
						});

						$('.accountname').on('change', function() {
							$(this).closest('td').find('.form-group').removeClass('has-error');
						});

						<?php if ($show_input): ?>
							$('form').submit(function(e) {
								e.preventDefault();
								$(this).find('.form-group').find('input, textarea, select').trigger('blur');
								if ($(this).find('.form-group.has-error').length == 0) {
									$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
										if (data.success) {
											$('#delay_modal').modal('show');
											setTimeout(function() {
												window.location = data.redirect;
											},1000);
										}
									});
								} else {
									$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
								}
							});
						<?php endif ?>
					</script>