<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">

			<form method = "post" id = "tag_customer_form" class="form-horizontal">

				<div class = "col-md-12">&nbsp;</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Sales Person Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('partnercode')
									->setId('partnercode')
									->setAttribute(array("readonly" => "readonly"))
									->setValue($partnercode)
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Sales Person Name')
									->setSplit('col-md-4', 'col-md-8')
									->setName('salespersonname')
									->setId('salespersonname')
									->setAttribute(array("readonly" => "readonly"))
									->setValue($salespersonname)
									->draw($show_input);
						?>
					
					</div>
				</div>

				<hr/>

				<!-- Table Here -->
				<table id = "tag_customer_table" class="table table-hover">
					<thead>
						<tr class = "info">
							<th class = "col-md-1 hide_in_view" style="text-align:center;">
								<input type = "checkbox" name = "selectall" id = "selectall" />
							</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
							<th class = "col-md-3 text-center">Customer Code</th>
							<th class = "col-md-2 text-center">Customer Name</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
						</tr>
					</thead>
				
					<tbody id = "list_container">

					<?php
						$count 		=	1;
						if( !empty($customer_list) )
						{
							foreach ($customer_list as $key => $row) 
							{
								$customercode 	=	$row->partnercode;
								$customername 	=	$row->partnername;
								$tagged 		=	$row->tagged;
					?>
						<tr>
							<td class="text-center hide_in_view">
								<input id = "<?php echo $customercode; ?>" type = "checkbox" name = "taggedCustomers[]" value = "<?php echo $customercode; ?>" >
								<input type = "hidden" id = "<?=$count?>" class="h_checkboxes" value = "<?=$tagged?>"
							</td>

							<td class = 'show_in_view hidden'>&nbsp;</td>

							<td>
								<?php echo $customercode;?>
							</td>

							<td>
								<?php echo $customername;?>
							</td>

							<td class = 'show_in_view hidden' >&nbsp;</td>
						</tr>
					<?php
								$count++;
							}
						}
						else
						{
					?>
						<tr>
							<td colspan='3' class="text-center">
								No Records Found.
							</td>
						</tr>
					<?php
						}
					?>
					</tbody>

				</table>

				<hr/>

				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $ui->drawSubmit($show_input); ?>
						<a href="<?=BASE_URL?>maintenance/sales_person" class="btn btn-default">Delete</a>
					</div>	
				</div>

			</form>

		</div>

	</div>

</div>

<script>

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
	var field	= $("#"+form+" #"+id).val();

	if(id.indexOf('_chosen') != -1){
		var id2	= id.replace("_chosen","");
		field	= $("#"+form+" #"+id2).val();

	}

	if(field == '' || parseFloat(field) == 0)
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');

		$("#"+form+" #"+help_block)
			// .next(".help-block")
			.removeClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0])
		{
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.removeClass('hidden');
		}
		return 1;
	}
	else
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+help_block) //$("#"+form+" #"+id)
			// .next(".help-block")
			.addClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0])
		{
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.addClass('hidden');
		}
		return 0;
	}
}

$(document).ready(function(){

	//For Edit - Checks the input boxes with greater than 0 and triggers checkbox
	$( ".h_checkboxes" ).each(function( index ) {

		var code 		=	$(this).val();
		
		if( code != "" && code.length > 0 ) 
		{
			$('#'+code).iCheck('check');
		}
	});
	
	var task 	=	'<?=$task?>';

	if( task == 'view' )
	{
		$('.hide_in_view').addClass('hidden');
		$('.show_in_view').removeClass('hidden');
	}

	$('form').submit(function(e) {
		e.preventDefault();
		$.post('<?=BASE_URL?>maintenance/sales_person/ajax/<?=$task?>', $(this).serialize()+ '<?=$ajax_post?>', function(data) {
			if (data.msg == 'success') {
				window.location = '<?php echo BASE_URL . 'maintenance/sales_person'; ?>';
			}
		});
	});

});

</script>
