<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
		<form method = "post" class="form-horizontal">
		
			<div class = "clearfix">
                <div class = "col-md-12">&nbsp;</div>
			
				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Tax Code:')
									->setSplit('col-md-3', 'col-md-8')
									->setName('fstaxcode')
									->setId('fstaxcode')
									->setValue($fstaxcode)
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Tax Name:')
									->setSplit('col-md-3', 'col-md-8')
									->setName('shortname')
									->setId('shortname')
									->setValue($shortname)
									->draw($show_input);
						?>
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Tax Description:')
									->setSplit('col-md-3', 'col-md-8')
									->setName('longname')
									->setId('longname')
									->setValue($longname)
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Tax Rate:')
									->setSplit('col-md-3', 'col-md-8')
									->setName('taxrate')
									->setId('taxrate')
									->setValue($taxrate)
									->draw($show_input);
						?>
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">

						<?php
							echo $ui->formField('dropdown')
								->setLabel('Tax Type:')
								->setPlaceholder('Filter Tax Type')
								->setSplit('col-md-3', 'col-md-8')
								->setName('tax_type')
								->setId('tax_type')
								->setList($tax_type_list)
								->setValue($taxtype)
								->draw($show_input);
						?>
					</div>
				</div>

				
				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Sales Account:')
								->setPlaceholder('Filter Sales Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('sales_account')
								->setId('sales_account')
								->setList($account_list)
								->setValue($salesAccount)
								->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">

						<?php
							echo $ui->formField('dropdown')
								->setLabel('Purchase Account:')
								->setPlaceholder('Filter Purchase Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('purchase_account')
								->setId('purchase_account')
								->setList($account_list)
								->setValue($purchaseAccount)
								->draw($show_input);
						?>
					</div>
				</div>
                
                <div class = "col-md-5">&nbsp;</div>

				<?php if($task == "view") {?>
						<input type = "button" name = "add" value = "<?= $button_name ?>" class = "btn btn-info btn-flat" onClick = "document.location = '<?= BASE_URL . 'maintenance/taxcodes/edit/'.$fstaxcode.'' ?>'">	
				<?php } else { ?>
					<input type = "submit" name = "add" value = "<?= $button_name ?>" class = "btn btn-info btn-flat">	
				<?php } ?>	

				<input type = "button" name = "cancel" value = "Cancel" class = "btn btn-default btn-flat" onClick = "document.location = '<?= BASE_URL . 'maintenance/taxcodes' ?>'">	
                
                <div class = "col-md-12">&nbsp;</div>
                <div class = "col-md-12">&nbsp;</div>
            </div>	

		</form>
    </div>
    
</section>

<script>

$(document).ready(function() 
{
	$('form').submit(function(e) 
	{
		e.preventDefault();

		$.post('<?=BASE_URL?>maintenance/taxcodes/ajax/<?=$task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) 
		{
			if( data.msg == "success" )
				window.location.href = "<?=BASE_URL?>maintenance/taxcodes";
			if(data.msg == "error_add")
			{
				$(".alert-warning").removeClass("hidden");
				$("#errmsg").html("Duplicate Entry Found for Tax Code: " + data.tax_codes);
			}
			if(data.msg == "error_update")
			{
				$(".alert-warning").removeClass("hidden");
				$("#errmsg").html("Encountered Error Updating Data");
			}
		});
	});
});

</script>