	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<br>
				<form action="" id="form" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Position')
								->setSplit('col-md-3', 'col-md-8')
								->setName('position')
								->setId('position')
								->setValue($position)
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
								->setAttribute(array("maxlength" => "500"))
								->setValidation('required')
								->draw($show_input);
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