	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<br>
				<form action="" method="post" class="form-horizontal">
					<div class="row">
						<div class="col-md-12">
							<?php
								echo $ui->formField('text')
									->setLabel('Group Name ')
									->setAttribute(array('autocomplete' => 'off'))
									->setSplit('col-md-2', 'col-md-8')
									->setName('groupname')
									->setId('groupname')
									->setValue($groupname)
									->setValidation('required')
									->setMaxLength(25)
									->draw($show_input);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?php
								echo $ui->formField('textarea')
									->setLabel('Group Desc ')
									->setSplit('col-md-2', 'col-md-8')
									->setName('description')
									->setId('description')
									->setValue($description)
									->setValidation('required')
									->draw($show_input);
							?>
						</div>
					</div>
					<input type="hidden" name="status" value="<?php echo $status; ?>" >
					<!-- <div class="row">
						<div class="col-md-12">
							<?php
								// echo $ui->formField('checkbox')
								// 	->setLabel("Admin Access")
								// 	->setSplit('col-md-2', 'col-md-8')
								// 	->setName("admin")
								// 	->setId("admin")
								// 	->setDefault('1')
								// 	->setValue('')
								// 	->draw($show_input); 
							?>
						</div>
					</div> -->
					<?php
					$prev_group	= '';
					$next_group	= '';
					?>
					<?php foreach ($moduleaccess_list as $key => $moduleaccess): ?>
						<hr class="form-hr">
						<?php
							$prev_group = $moduleaccess->module_group;
							if($prev_group != $next_group):
						?>
						<div class="row">
							<h4 class="module_label col-md-12">
								<strong><?php echo $moduleaccess->module_group ?></strong>
							</h4>
						</div>
						<hr class="form-hr">
						<?php
							endif;
						?>
						<div class="row">
							<label class="control-label col-md-2 module_label" data-id="<?php echo "module_access_{$key}" ?>">
								<?php echo $moduleaccess->module_name ?>:
							</label>
							<div class="col-md-8">
								<div class="row">
									<?php foreach ($access_list as $access_type => $access): ?>
										<div class="col-sm-2">
											<?php 
												if ($moduleaccess->{str_replace('mod', 'has', $access_type)}): ?>
												<?php
													echo $ui->formField('checkbox')
														->setLabel($access)
														->setSplit('col-xs-6 force-left', 'col-xs-6 no-padding force-right')
														->setName("module_access[{$moduleaccess->module_name}][{$access_type}]")
														->setId("module_access_{$key}_{$access_type}")
														->setSwitch()
														->setDefault('1')
														->setValue($moduleaccess->{$access_type})
														->draw($show_input);
												?>
											<?php else: ?>
												<input type="hidden" name="<?="module_access[{$moduleaccess->module_name}][{$access_type}]"?>" value="0">
											<?php endif ?>
										</div>
									<?php endforeach ?>
								</div>
							</div>
						</div>
						<?php
							$next_group = $prev_group;
						?>
					<?php endforeach ?>
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
		var ajax_call = '';
		$('.module_label').on('click', function() {
			var module_label = $(this).attr('data-id');
			var check_status = 'uncheck';
			$('[id*="' + module_label + '"]').each(function() {
				if ( ! $(this).is(':checked')) {
					check_status = 'check';
				}
			});
			$('[id*="' + module_label + '"]').iCheck(check_status);
		});
		$('.module_label').on('mouseenter mouseout', function(e) {
			$(this).next().find('label').trigger(e.type);
		});
		$('#groupname').on('blur', function() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			var groupname = $(this).val();
			$('#groupname').closest('form').find('[type="submit"]').addClass('disabled');
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_check_groupname', 'groupname=' + groupname + '<?=$ajax_post?>', function(data) {
				var error_message = 'Group Name already Exist';
				if (data.available) {
					var form_group = $('#groupname').closest('.form-group');
					if (form_group.find('p.help-block').html() == error_message) {
						form_group.removeClass('has-error').find('p.help-block').html('');
					}
				} else {
					$('#groupname').closest('.form-group').addClass('has-error').find('p.help-block').html(error_message);
				}
				$('#groupname').closest('form').find('[type="submit"]').removeClass('disabled');
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
					}
				});
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
	</script>
	<?php endif ?>