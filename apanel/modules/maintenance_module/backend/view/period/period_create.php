<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "customerForm" class="form-horizontal">
				
				<div class = "col-md-12">&nbsp;</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Period')
								->setSplit('col-md-4', 'col-md-8')
								->setName('period')
								->setId('period')
								->setValue($period)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Fiscal Year')
								->setSplit('col-md-4', 'col-md-8')
								->setName('fiscalyear')
								->setId('fiscalyear')
								->setValidation('required')
								->setValue($fiscalyear)
								->draw($show_input);
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Start Date')
								->setSplit('col-md-4', 'col-md-8')
								->setName('startdate')
								->setId('startdate')
								->setAddon('calendar')
								->setClass('datepicker-input')
								->setValue($startdate)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('End Date')
								->setSplit('col-md-4', 'col-md-8')
								->setName('enddate')
								->setId('enddate')
								->setAddon('calendar')
								->setClass('datepicker-input')
								->setValue($enddate)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
								->setLabel('Description')
								->setSplit('col-md-4', 'col-md-8')
								->setName('description')
								->setId('description')
								->setValue($description)
								// ->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $ui->drawSubmit($show_input); ?>
						<a href="<?=BASE_URL?>maintenance/period" class="btn btn-default">Cancel</a>
					</div>	
				</div>
			</form>
        </div>

    </div>
</section>
<div class="modal modal-warning" id="error_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Warning</h4>
				</div>
				<div class="modal-body">
					<p>Start date and end date is within existing period.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
<script>
		$('form').submit(function(e) {
			e.preventDefault();
			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0) {
				$.post('<?=BASE_URL?>maintenance/period/ajax/<?=$task?>', $(this).serialize()+ '<?=$ajax_post?>', function(data) {
				if (data.success) {
					window.location.href = data.redirect;
				}
				else {
					$('#error_modal').modal('show');
				}
			});
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
	</script>