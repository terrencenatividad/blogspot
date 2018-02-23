<section class="content">
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

	<div class="box box-primary">
		<div class="panel panel-default">
			
		<form class="form-horizontal" method="POST" id="coaForm" autocomplete="off">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">

					<?
							echo $ui->formField('text')
									->setLabel('Account Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('accountcode')
									->setId('accountcode')
									->setValue($accountcode)
									->draw($task != "view");	

					?>	
					</div>	
					<div class="col-md-6">		

					
					<?
						echo $ui->formField('text')
									->setLabel('Account Name')
									->setSplit('col-md-4', 'col-md-8')
									->setName('accountname')
									->setId('accountname')
									->setValue($accountname)
									->draw($task != "view");
					?>
					</div>				
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
					<?php
								echo $ui->formField('dropdown')
									->setLabel('Account Class')
									->setPlaceholder('Select Account Class Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('accountclasscode')
									->setId('accountclasscode')
									->setList($accountclasscode_list)
									->setValue($accountclasscode)
									->draw($task != "view");
					?>
					</div>
					<div class = "col-md-6">
					<?php
								echo $ui->formField('dropdown')
									->setLabel('FS Presentation')
									->setPlaceholder('Select FS Presentation')
									->setSplit('col-md-4', 'col-md-8')
									->setName('fspresentation')
									->setId('fspresentation')
									->setList($fspresentation_list)
									->setValue($fspresentation)
									->draw($task != "view");
					?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
					<?
								echo $ui->formField('dropdown')
											->setLabel('Account Type')
											->setPlaceholder('Select Account Type')
											->setSplit('col-md-4', 'col-md-8')
											->setName('accounttype')
											->setId('accounttype')
											->setList($accounttype_list)
											->setValue($accounttype)
											->draw($task != "view");
					?>
					</div>
					<div class = "col-md-6"> 
					
					<?
								echo $ui->formField('dropdown')
										->setLabel('Parent Account Title')
										->setPlaceholder('Select Parent Account')
										->setSplit('col-md-4','col-md-8')
										->setName('parentaccountcode')
										->setId('parentaccountcode')
										->setList($parentaccountcode_list)
										->setValue($parentaccountcode)
										->draw($task != "view");
					?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class = "col-md-6">
					<?
								echo $ui->formField('dropdown')
												->setLabel('Account Nature')
												->setPlaceholder('Select Account Nature')
												->setSplit('col-md-4','col-md-8')
												->setName('accountnature')
												->setId('accountnature')
												->setList($accountnature_list)
												->setValue($accountnature)
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

		$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', 
			  $(this).serialize() + '<?=$ajax_post?>', 
			  function(data) 
		{
			if( data.msg == "success" )
				window.location.href = "<?=MODULE_URL?>";
			if(data.msg == "error_add")
			{
				$(".alert-warning").removeClass("hidden");
				$("#errmsg").html("Duplicate Entry for " + 
									data.account_code + " " + 
									data.account_name);
			}
		});
});

</script>