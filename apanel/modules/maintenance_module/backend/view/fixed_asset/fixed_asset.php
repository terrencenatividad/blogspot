<section class="content">
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

	<div class="box box-primary">
		<div class="panel panel-default">
			
		<form class="form-horizontal form-group" method="POST" id="fixed_asset_form" autocomplete="off">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
					<?
							echo $ui->formField('text')
									->setLabel('Item No ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('itemno')
									->setId('itemno')
									->setValue($itemno)
									->setValidation('required')
									->draw($show_input);	

					?>	
					</div>	
					<div class="col-md-6">		
					<?
						echo $ui->formField('text')
									->setLabel('Description ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('description')
									->setId('description')
									->setValue($description)
									->setValidation('required')
									->draw($show_input);
					?>
					</div>				
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
					<?php
								echo $ui->formField('dropdown')
									->setLabel('Category')
									->setSplit('col-md-3', 'col-md-8')
									->setName('category')
									->setId('category')
									->setList($category_list)
									->setValue($category)
									->draw($show_input);
					?>
					</div>
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Location ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('location')
									->setId('location')
									->setValue($location)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Amount: ')
									->setSplit('col-md-3', 'col-md-8')
									->setName('amount')
									->setId('amount')
									->setValue($amount)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
					
				
			</div>
			<div class="panel-footer">
				<div class="row center">
					<div class="col-md-5 col-sm-4 col-xs-4"></div>
					<?php echo $ui->drawSubmit($show_input); ?>
						<a href="<?=BASE_URL?>maintenance/fixed_asset" class="btn btn-default">Cancel</a>
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
		var form_group	 	= 	$('#fixed_asset_form #fixed_asset_form').closest('.form-group');
		$('#fixed_asset_form').find('.form-group').find('input, textarea, select').trigger('blur');

		if ($('#fixed_asset_form').find('.form-group.has-error').length == 0)
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
										data.itemno + " Tax account was already used : " + data.description  );
				}
			});
			
		}else{
				if (form_group.find('p.help-block').html() != "") {
					form_group.removeClass('has-error').find('p.help-block').html('');
				}
		}
});
</script>