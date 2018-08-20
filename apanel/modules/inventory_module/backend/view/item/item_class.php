		<section class="content">
		<form action="" method="post" class="form-horizontal">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#Details" data-toggle="tab">Item Class Details</a></li>
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
												->setLabel('Item Class <span class = "asterisk">*</span>')
												->setAttribute(array('autocomplete' => 'off'))
												->setSplit('col-md-4', 'col-md-8')
												->setName('label')
												->setId('label')
												->setValue($label)
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Parent Class <span class = "asterisk">*</span>')
												->setSplit('col-md-4', 'col-md-8')
												->setName('parentid')
												->setId('parentid')
												->setList($parents)
												->setValue($parentid)
												->setValidation('required')
												->setPlaceholder('Select Parent')
												->draw($show_input);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="Accounting" class="tab-pane" style="padding: 15px">
						<div class="row">
							<div class="col-md-11">
								<h4>Sales Details</h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Debit Account <span class = "asterisk">*</span>')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('receivable_account')
												->setList($receivable_account_list)
												->setValidation('required')
												->setValue($receivable_account)
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Credit Account <span class = "asterisk">*</span>')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('revenue_account')
												->setList($revenue_account_list)
												->setValidation('required')
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
								<h4>Purchase Details</h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Debit Account <span class = "asterisk">*</span>')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('expense_account')
												->setList($expense_account_list)
												->setValidation('required')
												->setValue($expense_account)
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Credit Account <span class = "asterisk">*</span>')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('payable_account')
												->setList($payable_account_list)
												->setValidation('required')
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
								<h4>Inventory Details</h4>
								<div class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Inventory Account <span class = "asterisk">*</span>')
												->setPlaceholder('Select Account')
												->setSplit('col-md-4', 'col-md-8')
												->setName('inventory_account')
												->setList($chart_account_list)
												->setValidation('required')
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
		$('#label, #parentid').on('input change', function() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			var label = $('#label').val();
			var parentid = $('#parentid').val();
			$('#label').closest('form').find('[type="submit"]').addClass('disabled');
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_check_itemclass', 'label=' + label + '&parentid=' + parentid + '<?=$ajax_post?>', function(data) {
				var error_message = data.error_message;
				if (data.available) {
					var form_group = $('#label').closest('.form-group');
					form_group.removeClass('has-error').find('p.help-block').html('');
				} else {
					$('#label').closest('.form-group').addClass('has-error').find('p.help-block').html(error_message);
				}
				$('#label').closest('form').find('[type="submit"]').removeClass('disabled');
			});
		});
		$('form').submit(function(e) {
			e.preventDefault();
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
		});
	</script>
	<?php endif ?>