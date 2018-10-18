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
											->setLabel('Transaction Date')
											->setSplit('col-md-4', 'col-md-8')
											->setClass('datepicker-input')
											->setAttribute(array('readonly' => ''))
											->setAddon('calendar')
											->setName('transactiondate')
											->setId('transactiondate')
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
											->setLabel('Customer')
											->setSplit('col-md-4', 'col-md-8')
											->setPlaceholder('Select Customer')
											->setName('customer')
											->setId('customer')
											->setValue($partnername)
											->setList($customer_list)
											->setValidation('required')
											->draw($show_input);
											?>
										</div>
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Reference')
											->setSplit('col-md-4', 'col-md-8')
											->setName('referenceno')
											->setId('referenceno')
											->setMaxLength(30)
											->setValue($referenceno)
											->draw($show_input);
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Receivable No.')
											->setSplit('col-md-4', 'col-md-8')
											->setPlaceholder('Select Account Receivable')
											->setName('receivableno')
											->setId('receivableno')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($receivableno)
											->draw($show_input);
											?>
										</div>
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Amount')
											->setPlaceholder('0.00')
											->setSplit('col-md-4', 'col-md-8')
											->setName('amount')
											->setId('amount')
											->setValue($amount)
											->setValidation('required')
											->draw($show_input);
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?php
											echo $ui->formField('text')
											->setLabel('Invoice No.')
											->setPlaceholder('Sales Invoice')
											->setSplit('col-md-4', 'col-md-8')
											->setName('invoiceno')
											->setId('invoiceno')
											->setAttribute(array('readonly'))
											->setValue($invoiceno)
											->draw($show_input);
											?>
										</div>
									</div>
								</div>
							</div>
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
			<div id="ar_list_modal" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Accounts Receivable List</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-4 col-md-offset-8">
									<div class="input-group">
										<input id="ar_search" class="form-control pull-right" placeholder="Search" type="text">
										<div class="input-group-addon">
											<i class="fa fa-search"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-body no-padding">
							<table id="ar_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
								<thead>
									<tr class="info">
										<th class="col-xs-4">AR No.</th>
										<th class="col-xs-4">Transaction Date</th>
										<th class="col-xs-4 text-right">Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="4" class="text-center">Loading Items</td>
									</tr>
								</tbody>
							</table>
							<div id="pagination"></div>
						</div>
					</div>
				</div>
			</div>
			<script>
				<?php if ($show_input): ?>
					var ajax		= {};
					var ajax_call	= '';
					$(document).ready(function() {
						$("#amount").keydown(function (e) {
							if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
								(e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
								(e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
								(e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
								(e.keyCode >= 35 && e.keyCode <= 39)) {
									return;
							}
							if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
								e.preventDefault();
							}
						});
					});
					$('#customer').on('change', function() {
						$('#invoiceno').val('');
						$('#receivableno').val('');
					});

					$('#receivableno').on('focus', function() {
						var customer = $('#customer').val();
						ajax.customer = customer;
						if (customer == '') {
							$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Customer');
							$('#customer').trigger('blur');
						} else {
							$('#ar_tableList tbody').html(`<tr>
								<td colspan="4" class="text-center">Loading Items</td>
							</tr>`);
							$('#pagination').html('');
							getARList();
						}
					});
					$('#ar_list_modal #ar_search').on('input', function() {
						ajax.page = 1;
						ajax.search = $(this).val();
						getARList();
					});
					function getARList() {
						ajax.limit = 5;
						$('#ar_list_modal').modal('show');
						$('#invoice_list_modal').modal('hide');
						if (ajax_call != '') {
							ajax_call.abort();
						}
						ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_ar_list', ajax, function(data) {
							$('#ar_tableList tbody').html(data.table);
							$('#pagination').html(data.pagination);
							if (ajax.page > data.page_limit && data.page_limit > 0) {
								ajax.page = data.page_limit;
								getList();
							}
						});
					}
					$('#ar_tableList').on('click', 'tr[data-id]', function() {
						var rno = $(this).attr('data-id');
						var ino = $(this).attr('data-invno');
						$('#receivableno').val(rno).trigger('blur');
						$('#invoiceno').val(ino).trigger('blur');
						$('#ar_list_modal').modal('hide');
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