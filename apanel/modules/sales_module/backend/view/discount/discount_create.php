<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">

			<form method = "post" id = "discount_template_form" class="form-horizontal">
				<input type="hidden" name="h_disc_code" id="h_disc_code" value="<?=$discountcode?>">	
				<div class = "col-md-12">&nbsp;</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Discount Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('discountcode')
									->setId('discountcode')
									->setValue($discountcode)
									->setValidation('required code')
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Discount Name')
									->setSplit('col-md-4', 'col-md-8')
									->setName('discountname')
									->setId('discountname')
									->setValue($discountname)
									->setValidation('required')
									->draw($show_input);
						?>
					
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('dropdown')	
									->setLabel('Discount Type')
									->setSplit('col-md-4', 'col-md-8')
									->setName('discounttype')
									->setId('discounttype')
									->setAttribute(array("disabled" => "disabled"))
									->setList($discountchoice)
									->setValue($discounttype)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('textarea')	
									->setLabel('Discount Description')
									->setSplit('col-md-4', 'col-md-8')
									->setName('discountdesc')
									->setId('discountdesc')
									->setValue($discountdesc)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>

				<hr/>

				<table id = "discount_value_table" class="table table-hover">
					<thead>
						<tr class = "info">
							<th class = "col-md-1 hide_in_view" style="text-align:center;">
								<input type = "checkbox" name = "selectall" id = "selectall" />
							</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
							<th class = "col-md-3 text-center">Discount Name</th>
							<th class = "col-md-2 text-center">Percentage</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
						</tr>
					</thead>
				
					<tbody id = "list_container">

					<?php
						$count 		=	1;

						foreach ($discount_list as $key => $row) 
						{
							$discount_name 	=	$row->ind;
							$discount_id 	=	'discount_value['.$count.']';
					?>
						<tr>
							<td class="text-center hide_in_view">
								<input id = "<?php echo $count; ?>" type = "checkbox" name = "checkbox[]" value = "<?php echo $$discount_name; ?>"  class = "singleboxes">
							</td>

							<td class = 'show_in_view hidden'>&nbsp;</td>

							<td>
								<?php echo $row->val;?>
							</td>

							<td>
								<?php
									echo $ui->formField('text')	
											->setSplit('', 'col-md-12')
											->setName($discount_name)
											->setId($discount_id)
											->setValue($$discount_name)
											->setClass('discount_values')
											->setAttribute(array("disabled" => "disabled"))
											->draw($show_input);
								?>
							</td>

							<td class = 'show_in_view hidden' >&nbsp;</td>
						</tr>
					<?php
							$count++;
						}
					?>

					</tbody>

				</table>

				<hr/>

				<div class="row">
					<div class="col-md-12 text-center">
						<? 	if( $show_input )
						{
					?>
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnSave">Save</button>
						</div>
					<? 	
						}else{
					?>
						<div class="btn-group">
							<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/discount/edit/<?=$discountcode?>" style="outline:none;">Edit</a>
						</div>
					<?
						}
					?>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
							<!-- <button type="button" class="btn btn-default btn-flat" id="btnCancel">Cancel</button> -->
						</div>
					</div>	
				</div>

			</form>

		</div>

	</div>

</div>

<script>

var ajax = {};

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

$(document).ready(function(){

	$('#discount_value_table').on('blur','.discount_values',function(){
		var id = $(this).attr("id");
		formatNumber(id);
	});

	$('#discount_value_table').on('ifChecked','.singleboxes',function(){
		var id = $(this).attr("id");

		console.log("TEST = "+id);

		var discount_value 	=	document.getElementById("discount_value["+id+"]");
		discount_value.removeAttribute("disabled"); 
	});

	$('#discount_value_table').on('ifUnchecked','.singleboxes',function(){
		var id = $(this).attr("id");

		var discount_value 		=	document.getElementById("discount_value["+id+"]");
		discount_value.value 	=	"0.00";
		discount_value.setAttribute("disabled","disabled");
	});

	//For Edit - Checks the input boxes with greater than 0 and triggers checkbox
	$( "#discount_value_table .discount_values" ).each(function( index ) {
		var discount_value 		=	$(this).val();
		var id 					= 	index+1;

		if( discount_value > 0.00 && discount_value > 0 ){
			$('#'+id).iCheck('check');
		}
	});

	$('#discount_value_table').on('ifClicked','#selectall',function(){
		$('.singleboxes').iCheck('check');
	});

	$('#discount_value_table').on('ifUnchecked','#selectall',function(){
		$('.singleboxes').iCheck('uncheck');
	});

	var task 	=	'<?=$task?>';

	if( task == 'view' )
	{
		$('.hide_in_view').addClass('hidden');
		$('.show_in_view').removeClass('hidden');
	}

	$('#discount_template_form #btnSave').on('click',function(){

		$('#discount_template_form #discountcode').trigger('blur');
		$('#discount_template_form #discountname').trigger('blur');
		$('#discount_template_form #discounttype').trigger('blur');
		$('#discount_template_form #discountdesc').trigger('blur');

		var discount 	=	$('#discount_value_table input[type="checkbox"]:checked').length;
		var value 		=	0;

		if( discount <= 0 )
		{
			$('#warning_modal #warning_message').html("<b>Please add a discount to the template.</b>");
			$('#warning_modal').modal('show');
		}

		$( "#discount_value_table .discount_values" ).each(function( index ) {
			var discount_value 		=	$(this).val();
			var id 					= 	index+1;

			if( discount_value > 0.00 && discount_value > 0 ){
				value 				+=	1;
			}

			var checked =	document.getElementById(id).checked;
			
			if( checked )
			{
				var disc_val = document.getElementById("discount_value["+id+"]").value;
				
				if( disc_val == 0 || disc_val == 0.00 )
				{
					$("#discount_value\\["+id+"\\]").parent().addClass('has-error');
				}
			}
		});

		if ($('#discount_template_form').find('.form-group.has-error').length == 0 && discount > 0 && value > 0 )
		{	
			$.post('<?=BASE_URL?>maintenance/discount/ajax/<?=$task?>', $('#discount_template_form').serialize()+ '<?=$ajax_post?>', function(data) {
				if( data.msg == 'success' )
				{
					window.location = '<?php echo BASE_URL . 'maintenance/discount'; ?>';
				}
			});
		}
	});

	$('#discountcode').on('blur',function(){
		ajax.old_code 	= 	$('#h_disc_code').val();
		ajax.curr_code 	=	$(this).val();
		
		var error_message 	=	'';	
		var form_group	 	= 	$('#discountcode').closest('.form-group');

		$.post('<?=BASE_URL?>maintenance/discount/ajax/get_duplicate',ajax, function(data) {
			if( data.msg == 'exists' )
			{
				error_message 	=	"<b>The Code you entered already exists!</b>";
				$('#discountcode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
			}
			else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
			{
				if (form_group.find('p.help-block').html() != "") {
					form_group.removeClass('has-error').find('p.help-block').html('');
				}
			}
		});
	});

	// $('#discount_template_form #btnCancel').on('click',function(){
	// 	window.location = '<?php //echo BASE_URL . 'maintenance/discount'; ?>';
	// });


});

</script>
