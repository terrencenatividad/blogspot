<section class="content">
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

	<div class="box box-primary">
		<div class="panel panel-default">
			
		<form class="form-horizontal form-group" method="POST" id="coaForm" autocomplete="off">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
					<?
							echo $ui->formField('text')
									->setLabel('ATC Code ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('atc_code')
									->setId('atc_code')
									->setValue($atc_code)
									->setValidation('required')
									->draw($task != "view");	

					?>	
					</div>	
					<div class="col-md-6">		
					<?
						echo $ui->formField('text')
									->setLabel('Tax Rate(%) ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('tax_rate')
									->setId('tax_rate')
									->setValue($tax_rate)
									->setValidation('required')
									->draw($task != "view");
					?>
					</div>				
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
					<?php
								echo $ui->formField('text')
									->setLabel('Tax Code')
									->setSplit('col-md-3', 'col-md-8')
									->setName('wtaxcode')
									->setId('wtaxcode')
									->setValidation('required')
									->setValue($wtaxcode)
									->draw($task != "view");
					?>
					</div>
					<div class = "col-md-6">
						<?php
							echo $ui->formField('dropdown')
									->setLabel('EWT: ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('tax_account')
									->setId('tax_account')
									->setList($account_list)
									->setValue($tax_account)
									->setValidation('required')
									->draw($task != "view");
						?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('textarea')
									->setLabel('Description: ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('short_desc')
									->setId('short_desc')
									->setValue($short_desc)
									->setValidation('required')
									->draw($task != "view");
						?>
					</div>
					<div class = "col-md-6">
						<?php
							echo $ui->formField('dropdown')
									->setLabel('CWT: ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('cwt')
									->setId('cwt')
									->setList($s_account_list)
									->setValue(($task == 'create' ? '' : $cwt))
									->setValidation('required')
									->draw($task != "view");
						?>
					</div>
				</div>
					
				
			</div>
			<div class="panel-footer">
				<div class="row center">
					<div class="col-md-5 col-sm-4 col-xs-4"></div>
					<div class="col-md-2 col-sm-3 col-xs-3" id="task_buttons" style="padding:3px;">
					<?php if($task == "view") {?>
						<input type = "button" name = "add" value = "<?= $button_name ?>" 
							class = "btn btn-primary btn-flat" 
							onClick = "document.location = '<?=MODULE_URL?>edit/<?=$sid?>'">	
					<?php } else { ?>
						<input type = "submit" name = "update" value = "<?= $button_name ?>" 
							class = "btn btn-primary btn-flat">	
					<?php } ?>	
					<a href="<?=MODULE_URL?>" class="btn btn-default btn-flat">Cancel</a>
					

					<div class="col-md-5 col-sm-4 col-xs-4"></div>
				</div>
			</div>
		</form>
	</div>
	</div>
</section>
<script>

$('form').submit(function(e) 
{
		e.preventDefault();
		var form_group	 	= 	$('#coaForm #coaForm').closest('.form-group');
		$('#coaForm').find('.form-group').find('input, textarea, select').trigger('blur');

		if ($('#coaForm').find('.form-group.has-error').length == 0)
		{	
			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
				if( data.msg == 'success' )
				{
					$('#delay_modal').modal('show');
						setTimeout(function() {							
						window.location =  "<?=MODULE_URL?>";					
					}, 1000)
				}
				if(data.msg == "error_add")
				{
					$(".alert-warning").removeClass("hidden");
					$("#errmsg").html("Duplicate Entry for ATC Code : " + 
										data.atc_code + " Tax account was already used : " + data.tax_account  );
				}
			});
			
		}else{
				if (form_group.find('p.help-block').html() != "") {
					form_group.removeClass('has-error').find('p.help-block').html('');
				}
		}
});
</script>