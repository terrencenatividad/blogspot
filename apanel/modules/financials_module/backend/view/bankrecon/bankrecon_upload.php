	<section class="content">
		<div class="row">
			<div class="col-md-4 text-center col-md-offset-4" align="center">
				<div class="form-steps steps-2">
					<div class="step-line">
						<div class="step-progress" style="width: 25%"></div>
					</div>
					<div class="step active">
						<a class="step-icon">
							<i class="glyphicon glyphicon-import"></i>
						</a>
						<p>Import Bank Statement</p>
					</div>
					<div class="step">
						<a class="step-icon">
							<i class="glyphicon glyphicon-tags"></i>
						</a>
						<p>Tag and Match</p>
					</div>
				</div>
			</div>
		</div>
		<div class="box box-primary">
			<div class="box-body">
				<form method="post" id="bankrecon" class="form-horizontal">
					<div id="step1">
						<br>
						<div class="row">
							<div class="col-md-12">
								<h3 class="m-none">Step 1</h3>
							</div>
							<div class="col-md-10 col-md-offset-1">
								<div class="row">
									<div class="col-md-6">	
										<div class="form-group">
											<label class="control-label col-md-5 left">Select daterange</label>
											<div class="col-md-7">
												<div class="input-group monthrangefilter">
													<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value="" data-daterangefilter="month" data-validation="required">
													<span class="input-group-addon">
														<i class="glyphicon glyphicon-calendar"></i>
													</span>
												</div>
												<p class="help-block m-none"></p>
											</div>
										</div>
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Select Bank')
												->setPlaceholder('Filter Bank')
												->setSplit('col-md-5','col-md-7')
												->setName('accountcode')
												->setId('accountcode')
												->setList($bank_list)
												->setValidation('required')
												->draw($show_input);
										?>
										<div class="form-group">
											<label class="control-label col-md-5">Sample Template</label>
											<div class="col-md-7">
												<a href="<?php echo MODULE_URL ?>get_import" download="bankrecon_csv_template.csv" class="btn btn-primary btn-sm">Download Here</a>
											</div>
										</div>
										<div class="form-group">
											<label for="import_csv" class="control-label col-md-5">Select file to import</label>
											<div class="col-md-7">
												<?php
													echo $ui->setElement('file')
															->setId('import_csv')
															->setName('import_csv')
															->setAttribute(array('accept' => '.csv'))
															->setValidation('required')
															->draw();
												?>
											</div>
											<span class="help-block"></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="panel panel-info">
											<div class='panel-heading'><strong>On Screen Help</strong></div>
											<div class="panel-body">
												<ul>
														<li><strong>Select file to import.</strong>&nbsp;Click “Choose File” or "Browse".</li>
														<br/>
														<li><strong>Browse for your desired CSV file then click “Open”.</strong> &nbsp;The file to be selected must be in Comma-Separated Values (CSV) file.</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="step2" style="display: none">
						<hr/>
						<div class="row">
							<div class="col-md-12">
								<h3 class="m-none">Step 2</h3>
							</div>
							<div class="col-md-10 col-md-offset-1">
								<div id="startRow" class="row">
									<div class="col-md-6">
										<?php
											echo $ui->formField('text')
												->setLabel('Ending Balance')
												->setPlaceholder('0.00')
												->setSplit('col-md-5','col-md-7')
												->setName('endbalance')
												->setId('endbalance')
												->setValidation('required decimal')
												->draw($show_input);
										?>
									</div>
									<div class="col-md-6">
										<div class="panel panel-info">
											<div class="panel-heading"><strong>On Screen Help</strong></div>
											<div class="panel-body">
												<ul>
													<li><strong>Enter ending balance.</strong>&nbsp;The ending balance pertains to the ending bank balance from the statement provided by the bank.</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 text-center">
								<hr>
								<input type="hidden" name="import" value="csv">
								<input type="submit" name="submit" value="Import" class="btn btn-info">&nbsp;&nbsp;
								<button class="btn btn-primary" id="cancel">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
	<?php if ($has_recon && $has_recon->stat == 'open'): ?>
		<div class="modal fade" id="has_recon" tabindex="-1" data-backdrop="static">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						Bank Recon
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						Previous Bank Reconciliation is unfinished. Do you want to continue your Previous Bank Reconciliation?
					</div>
					<div class="modal-footer text-center">
						<a href="<?php echo MODULE_URL . 'listing/' . base64_encode($has_recon->id) ?>" class="btn btn-primary btn-flat">Yes</a>
						<button type="button" id="confirmation_no" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			$('#has_recon').modal('show');
		</script>
	<?php endif ?>
	<script>
		var ajax_call = '';
		$('#bankrecon').on('change', '#import_csv', function() {
			var filename = $(this).val().split("\\");
			$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
			$('#step2').show();
		});

		$('#bankrecon').submit(function(e) {
			e.preventDefault();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			$(this).closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if ($(this).closest('form').find('.form-group.has-error').length == 0) {
				var formData = new FormData($('#bankrecon')[0]);
				formData.append('file',$('#import_csv')[0].files[0]);

				ajax_call = $.ajax({
					url: '<?=MODULE_URL?>ajax/<?=$ajax_task?>',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: function(data) {
						if (data.success) {
							$('#success_modal').modal('show');
							show_success_msg("Your data has been successfully imported!");
							setTimeout(function() {
								window.location.href = data.redirect;					
							}, 1000)
							
						} else {
							addError('<p>' + data.error + '</p>', true);
							try {
								let invalid = data.invalid;
								for (var key in invalid) {
									if (invalid.hasOwnProperty(key)) {
										let invalid2 = invalid[key];
										for (var key2 in invalid2) {
											if (invalid2.hasOwnProperty(key2)) {
												addError('<p><b>Invalid ' + key + '</b>: ' + invalid2[key2] + '</p>');
											}
										}
									}
								}
							} catch(e) {}
							try {
								let validity = data.validity;
								for (var key in validity) {
									if (validity.hasOwnProperty(key)) {
										let validity2 = validity[key];
										for (var key2 in validity2) {
											if (validity2.hasOwnProperty(key2)) {
												addError('<p><b>' + key + ' Field</b>: ' + key2 + '</p>');
											}
										}
									}
								}
							} catch(e) {}
							$('#warning_modal').modal('show');
						}
					}
				});
			} else {
				$(this).closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
		function addError(error, clean) {
			if (clean) {
				$('#warning_modal .modal-body').html(error);
			} else {
				$('#warning_modal .modal-body').append(error);
			}
		}
		function show_success_msg(msg)
		{
		$('#success_modal #message').html(msg);
		$('#success_modal').modal('show');
		}
	</script>






