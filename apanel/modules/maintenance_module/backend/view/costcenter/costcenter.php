	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<br>
				<form action="" id="form" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Cost Center Code')
								->setSplit('col-md-3', 'col-md-8')
								->setName('costcenter_code')
								->setId('costcenter_code')
								->setValue($costcenter_code)
								->setAttribute(array("maxlength" => "50"))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
									->setLabel('Cost Center Account')
									->setPlaceholder('Select Account')
									->setSplit('col-md-3', 'col-md-8')
									->setName('costcenter_account')
									->setId('costcenter_account')
									->setList($coa_list)
									->setValue($costcenter_account)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Name')
								->setSplit('col-md-3', 'col-md-8')
								->setName('name')
								->setId('name')
								->setValue($name)
								->setAttribute(array("maxlength" => "100"))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
									->setLabel('Description')
									->setSplit('col-md-3', 'col-md-8')
									->setName('description')
									->setId('description')
									->setValue($description)
									->setAttribute(array("maxlength" => "1000"))
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Cost Center Approver ')
								->setPlaceholder('Select User')
								->setSplit('col-md-3', 'col-md-8')
								->setName('approver')
								->setId('approver')
								->setList($users_list)
								->setValue($approver)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							// echo $ui->formField('dropdown')
							// 	->setLabel('Budget Account ')
							// 	->setPlaceholder('Select Account')
							// 	->setSplit('col-md-3', 'col-md-8')
							// 	->setName('budget_account')
							// 	->setId('budget_account')
							// 	->setList($coa_list)
							// 	->setValue($budget_account)
							// 	->setValidation('required')
							// 	->draw($show_input);
						?>
					</div>
				</div>
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
							<?php echo $ui->drawSubmit($show_input); ?>
							<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
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
						$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = data.redirect;									
						}, 1000)	
					}
				});
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
	</script>
	<?php endif ?>