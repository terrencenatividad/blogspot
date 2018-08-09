<section class="content">
	<div class="alert alert-danger alert-dismissable hidden" id="pageAlert">
		<button type="button" class="close" data-hide="alert">&times;</button>
		<strong>Error!</strong>
		<br/>
		<p></p>
	</div>
	<div class="box box-primary">
		<div class="box-body">
			<br>
			<form action="" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-5">
						<?php
							echo $ui->formField('text')
								->setLabel('Unit Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('uomcode')
								->setId('uomcode')
								->setValue($uomcode)
								->setMaxLength(15)
								->addHidden((isset($ajax_task) && $ajax_task == 'ajax_edit'))
								->setValidation('required code')
								->draw((isset($ajax_task) && $ajax_task == 'ajax_create'));
						?>
					</div>
					<div class="col-md-5">
						<?php
							echo $ui->formField('text')
								->setLabel('Unit Description')
								->setSplit('col-md-4', 'col-md-8')
								->setName('uomdesc')
								->setId('uomdesc')
								->setValue($uomdesc)
								->setAttribute(
									array(
										'maxlength' => 50
									)
								)
								->setValidation('required special')
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Unit Type')
								->setPlaceholder('None')
								->setSplit('col-md-4', 'col-md-8')
								->setName('uomtype')
								->setId('uomtype')
								->setList($type_list)
								->setValue($uomtype)
								->setValidation('required special')
								->draw($show_input);
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
<script>
	$(document).ready(function() 
	{
		$("[data-hide]").on("click", function(){
			$("." + $(this).attr("data-hide")).hide();
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
					},1000);
				}else{
					$("#pageAlert").removeClass('hidden');
					$("#pageAlert p").html('Unit of Measure already exist. Please specify another and try again.');
				}
			});
		} else {
			$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
		}
	});
</script>