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
											->setName('transactiondate')
											->setId('transactiondate')
											->setClass('datepicker-input')
											->setAddon('calendar')
											->setValue($transactiondate)
											->setValidation('required')
											->setAttribute(array('readonly'=>'','data-date-start-date'=>$close_date))
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
											->setName('referenceno')
											->setId('referenceno')
											->setValue($referenceno)
											->setValidation('required')
											->draw($show_input);
											?>
										</div>
										<div class="col-md-6">
											<?php
											echo $ui->formField('dropdown')
											->setLabel('Proforma')
											->setPlaceholder('Select Proforma')
											->setSplit('col-md-4', 'col-md-8')
											->setName('proformacode')
											->setId('proformacode')
											->setList($proforma_list)
											->setValue($proformacode)
											// ->setValidation('required')
											->draw($show_input);
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<?php
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
							</div>
						</div>
						<div class="box-body table-responsive no-padding">
							<table id="tableList" class="table table-hover table-sidepad">
								<thead>
									<tr class="info">
										<th class="col-xs-3">Account</th>
										<th>Description</th>
										<th class="col-xs-2 text-right">Debit</th>
										<th class="col-xs-2 text-right">Credit</th>
										<th style="width: 50px;"></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
								<tfoot>
									<tr>
										<td>
											<?php if ($show_input): ?>
												<button type="button" class="btn btn-link" onClick="addVoucherDetails()">Add a New Line</button>
											<?php endif ?>
										</td>
										<td>
											<p id="error_msg" class="help-block text-red text-right"></p>
										</td>
										<td class="text-right">
											<b id="total_debit" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
										</td>
										<td class="text-right">
											<b id="total_credit" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
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
									if( $display_edit && $restrict_jv && $ajax_task == 'ajax_create' ){
										echo $ui->addSavePreview()
										->addSaveNew()
										->addSaveExit()
										->drawSaveOption();
									}

									if($ajax_task == 'ajax_view' && isset($stat) &&  $stat != 'cancelled') {
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
				var proformacode	= '<?php echo $proformacode ?>';
				var min_row			= 2;
				function addVoucherDetails(details, index) {
					var details = details || {accountcode: '', detailparticulars: '', debit: '', credit: ''};
					var row = `
					<tr>
					<td>
					<?php
					$value = ($show_input) ? '' : '<span id="temp_view_` + index + `"></span>';
					echo $ui->formField('dropdown')
					->setPlaceholder('Select Account')
					->setSplit('', 'col-md-12')
					->setName('accountcode[]')
					->setList($chartofaccounts)
					->setValidation('required')
					->setValue($value)
					->draw($show_input);
					?>
					</td>
					<td>
					<?php
					echo $ui->formField('text')
					->setSplit('', 'col-md-12')
					->setName('detailparticulars[]')
					->setValue('` + details.detailparticulars + `')
					->draw($show_input);
					?>
					</td>
					<td class="text-right debit_column">
					<?php
					echo $ui->formField('text')
					->setSplit('', 'col-md-12')
					->setName('debit[]')
					->setClass('text-right')
					->setValidation('required decimal')
					->setValue('` + (parseFloat(details.debit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + `')
					->draw($show_input);
					?>
					</td>
					<td class="text-right credit_column">
					<?php
					echo $ui->formField('text')
					->setSplit('', 'col-md-12')
					->setName('credit[]')
					->setClass('text-right')
					->setValidation('required decimal')
					->setValue('` + (parseFloat(details.credit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + `')
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
					<?php if ($show_input): ?>
						if (details.accountcode != '') {
							$('#tableList tbody').find('tr:last select').val(details.accountcode);
						}
						try {
							drawTemplate();
						} catch(e) {};
						<?php else: ?>
							var accountlist = <?= json_encode($chartofaccounts) ?>;
							accountlist.forEach(function(account) {
								if (account.ind == details.accountcode) {
									$('#temp_view_' + index).html(account.val);
								}
							});
						<?php endif ?>
						addTotal('#total_credit', details.credit);
						addTotal('#total_debit', details.debit);
					}
					function recomputeTotal() {
						$('#total_debit').html('0.00');
						$('#total_credit').html('0.00');
						$('[name="debit[]"]').each(function() {
							addTotal('#total_debit', $(this).val());
						});
						$('[name="credit[]"]').each(function() {
							addTotal('#total_credit', $(this).val());
						});
					}
					var voucher_details = <?=$voucher_details?>;
					function displayDetails(voucher_details) {
						$('#tableList tbody').html('');
						if (voucher_details.length > 0) {
							voucher_details.forEach(function(voucher_details, index) {
								addVoucherDetails(voucher_details, index);
							});
						} else if (min_row == 0) {
							$('#tableList tbody').append(`
								<tr>
								<td colspan="5" class="text-center"><b>Select Packing No.</b></td>
								</tr>
								`);
						}
						if (voucher_details.length < min_row) {
							for (var x = voucher_details.length; x < min_row; x++) {
								addVoucherDetails('', x);
							}
						}
						recomputeTotal();
					}
					if (<?php echo ($amount) ? $amount : 0 ?> > 0 || '<?php echo $stat?>' == 'cancelled' || '<?php echo $checker ?>' != 'closing') {
						displayDetails(voucher_details);
					} else {
						$('#tableList tbody').append(`<tr><th colspan="4" class="text-center">No Entries for this Period</th></tr>`);
						$('#tableList tfoot').html('');
					}
					function addTotal(id, amount) {
						var old = parseFloat($(id).html().replace(/\,/g,'') || 0);
						$(id).html((old + parseFloat(amount.replace(/\,/g,'') || 0)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
					}
					var proforma = [];
					function revertProforma(code) {
						proformacode = code;
						$('#proformacode').val(code).trigger('change');
					}
					function changeProforma() {
						displayDetails(proforma);
						$('#proformacode').trigger('change');
					}
					$('#proformacode').on('change', function() {
						if (proformacode != $(this).val()) {
							var temp = proformacode;
							proformacode = $(this).val();
							if (ajax_call2 != '') {
								ajax_call2.abort();
							} 
							ajax_call2 = $.post('<?=MODULE_URL?>ajax/ajax_get_proforma', 'proformacode=' + proformacode, function(data) {
								proforma = data.proforma;
								if (temp !== '') {
									showConfirmationLink('changeProforma()', `revertProforma('` + temp + `')`, `Are you sure you want to apply this proforma? <br> Applying this would overwrite the existing entries you've added.`);
								} else {
									displayDetails(proforma);
								}
							});
						}
					});
					<?php if ($show_input): ?>
						$('body').on('input blur', '[name="debit[]"], [name="credit[]"]', function() {
							recomputeTotal();
							var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
							var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
							if (total_debit > 0 && total_credit > 0 && total_debit != total_credit) {
								$('#error_msg').html('Total Debit and Total Credit must match');
							} else {
								$('#error_msg').html('');
							}
						});
						$('body').on('blur', '[name="debit[]"]', function() {
							var val = parseFloat($(this).val().replace(/\,/g,'') || 0);
							if (val > 0) {
								$(this).closest('tr').find('[name="credit[]"]').val('0.00');
							}
						});
						$('body').on('blur', '[name="credit[]"]', function() {
							var val = parseFloat($(this).val().replace(/\,/g,'') || 0);
							if (val > 0) {
								$(this).closest('tr').find('[name="debit[]"]').val('0.00').trigger('input');
							}
						});
						function deleteVoucherDetails(id) {
							delete_row.remove();
							if ($('#tableList tbody tr').length <= 1) {
								addVoucherDetails();
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
							var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
							var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
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
							} else if (total_debit <= 0 || total_credit <= 0) {
								$('#error_msg').html('Total Debit and Total Credit must have a value');
							} else {
								$('#error_msg').html('Total Debit and Total Credit must match');
							}
						});
						$('form').on('click', '[id="save_new"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
							var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
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
							} else if (total_debit <= 0 || total_credit <= 0) {
								$('#error_msg').html('Total Debit and Total Credit must have a value');
							} else {
								$('#error_msg').html('Total Debit and Total Credit must match');
							}
						});
						$('form').on('click', '[id="save_exit"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
							var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
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
							} else if (total_debit <= 0 || total_credit <= 0) {
								$('#error_msg').html('Total Debit and Total Credit must have a value');
							} else {
								$('#error_msg').html('Total Debit and Total Credit must match');
							}
						});
						$('form').on('click', '[type="submit"]', function(e) {
							e.preventDefault();
							var form_element = $(this).closest('form');
							var submit_data = '&submit=' + $(this).attr('id');
							var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
							var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
							form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
							if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
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
							} else if (total_debit <= 0 || total_credit <= 0) {
								$('#error_msg').html('Total Debit and Total Credit must have a value');
							} else {
								$('#error_msg').html('Total Debit and Total Credit must match');
							}
						});
						<?php else: ?>
							$('#total_debit').html('0.00');
							$('#total_credit').html('0.00');
							$('#tableList tbody td.debit_column .form-control-static').each(function() {
								addTotal('#total_debit', $(this).html());
							});
							$('#tableList tbody td.credit_column .form-control-static').each(function() {
								addTotal('#total_credit', $(this).html());
							});
						<?php endif ?>
					</script>