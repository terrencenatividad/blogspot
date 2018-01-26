	<section class="content">
		<div class="box box-primary">
			<form action="" method="post">
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-sidepad">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Account Code', array('class' => 'col-md-3'))
									->addHeader('Account Name', array('class' => 'col-md-3'))
									->addHeader('Sales Account', array('class' => 'col-md-3'))
									->addHeader('Purchase Account', array('class' => 'col-md-3'))
									->draw();
						?>
						<tbody>
							<?php foreach ($accounts as $row): ?>
								<tr>
									<td><p class="form-control-static"><?php echo $row->fstaxcode ?></p></td>
									<td><p class="form-control-static"><?php echo $row->longname ?></p></td>
									<td>
										<?php 
											echo $ui->formField('dropdown')
													->setPlaceholder('Please Select an Account')
													->setName('salesAccount[]')
													->setList($account_list)
													->setValidation('required')
													->setValue($row->salesAccount)
													->draw($show_input);

											echo $ui->setElement('hidden')
													->setName('fstaxcode[]')
													->setValue($row->fstaxcode)
													->draw();
										?>
									</td>
									<td>
										<?php 
											echo $ui->formField('dropdown')
													->setPlaceholder('Please Select an Account')
													->setName('purchaseAccount[]')
													->setList($account_list)
													->setValidation('required')
													->setValue($row->purchaseAccount)
													->draw($show_input);
										?>
									</td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
				<div class="box-body">
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
							<?php
								echo $ui->drawSubmitDropdown($show_input, isset($ajax_task) ? $ajax_task : '');
								if ($show_input) {
									echo $ui->drawCancel();
								}
							?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<?php if ($show_input): ?>
		<script>
			$('form').on('submit', function(e) {
				e.preventDefault();
				$(this).find('input, textarea, select').trigger('blur_validate');
				if ($(this).find('.form-group.has-error').length == 0) {
					console.log($(this).serialize());
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize(), function(data) {
						if (data.success) {
							window.location = data.redirect;
						}
					});
				} else {
					$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
				}
			});
		</script>
	<?php endif ?>