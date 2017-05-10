	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<br>
				<form action="" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('First Name')
								->setSplit('col-md-4', 'col-md-7')
								->setName('firstname')
								->setId('firstname')
								->setValue($firstname)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Middle Initial')
								->setSplit('col-md-4', 'col-md-2')
								->setName('middleinitial')
								->setId('middleinitial')
								->setValue($middleinitial)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Last Name')
								->setSplit('col-md-4', 'col-md-7')
								->setName('lastname')
								->setId('lastname')
								->setValue($lastname)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Group Access')
								->setPlaceholder('Select Group')
								->setSplit('col-md-4', 'col-md-7')
								->setName('groupname')
								->setId('groupname')
								->setList($group_list)
								->setValue($groupname)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('E-mail')
								->setSplit('col-md-4', 'col-md-7')
								->setName('email')
								->setId('email')
								->setValue($email)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Phone')
								->setSplit('col-md-4', 'col-md-7')
								->setName('phone')
								->setId('phone')
								->setValue($phone)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Mobile')
								->setSplit('col-md-4', 'col-md-7')
								->setName('mobile')
								->setId('mobile')
								->setValue($mobile)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Username')
								->setSplit('col-md-4', 'col-md-7')
								->setName('username')
								->setId('username')
								->setValidation('required')
								->setValue($username)
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							if ($show_input) {
								echo $ui->formField('text')
									->setLabel('Password')
									->setSplit('col-md-4', 'col-md-7')
									->setName('password')
									->setId('password')
									->setValidation((($ajax_task == 'ajax_edit') ? '' : 'required'))
									->draw($show_input);
							}
						?>
					</div>
				</div>
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
							<?php echo $ui->drawSubmit($show_input); ?>
							<a href="<?=MODULE_URL?>" class="btn btn-default">Cancel</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
	<?php if ($show_input): ?>
	<script>
		$('form').submit(function(e) {
			e.preventDefault();
			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0) {
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
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