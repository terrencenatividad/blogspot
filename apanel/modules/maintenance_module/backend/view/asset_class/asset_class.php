<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<br>
				<form action="" id="form" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Code')
								->setSplit('col-md-3', 'col-md-8')
								->setName('code')
								->setId('code')
								->setValue($code)
								->setAttribute(array("maxlength" => "4"))
								->addHidden((isset($ajax_task) && $ajax_task == 'ajax_edit'))
								->setValidation('code required')
								->draw((isset($ajax_task) && $ajax_task == 'ajax_create'));
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Asset Class')
								->setSplit('col-md-3', 'col-md-8')
								->setName('assetclass')
								->setId('assetclass')
								->setValue($assetclass)
								->setAttribute(array("maxlength" => "35"))
								->setValidation('required special')
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('checkbox')
								->setLabel('Depreciate')
								->setSplit('col-md-3', 'col-md-8')
								->setName('depreciate')
								->setId('depreciate')
								->setDefault(1)
								->setValue($depreciate)
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('No. of Months Useful Life')
									->setSplit('col-md-3', 'col-md-8')
									->setName('useful_life')
									->setClass('useful_life')
									->setId('useful_life')
									->setValue($useful_life)
									->setAttribute(array("maxlength" => "3", "disabled" => "disabled"))
									->setValidation('required integer')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Salvage Value')
								->setSplit('col-md-3', 'col-md-8')
								->setName('salvage_value')
								->setId('salvage_value')
								->setValue($salvage_value)
								->setAttribute(array("maxlength" => "20", "disabled" => "disabled"))
								->setValidation('required decimal')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('GL Account (Asset) ')
								->setPlaceholder('Select Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('gl_asset')
								->setId('gl_asset')
								->setList($coa_list)
								->setValue($gl_asset)
								// ->setAttribute(array("disabled" => "disabled"))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('GL Account (Acc Dep) ')
								->setPlaceholder('Select Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('gl_accdep')
								->setId('gl_accdep')
								->setList($coa_list)
								->setValue($gl_accdep)
								->setAttribute(array("disabled" => "disabled"))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('GL Account(Depreciate Expense)')
								->setPlaceholder('Select Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('gl_depexpense')
								->setId('gl_depexpense')
								->setList($coa_list)
								->setValue($gl_depexpense)
								->setAttribute(array("disabled" => "disabled"))
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
				</div>
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
var ajax = {};
	$('form').submit(function(e) {
		e.preventDefault();
		$(this).find('.form-group').find('input, textarea, select').trigger('blur');
		if ($(this).find('.form-group.has-error').length == 0) {
			$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
				if (data.success) {
					$('#delay_modal').modal('show');
						setTimeout(function() {							
							window.location = data.redirect;									
					}, 1000)	
				}
			});
		} else {
			$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
		}
	});

	$('#costcenter_code').on('blur',function(){
	ajax.old_code 	= 	$('#h_costcenter_code').val();
	ajax.curr_code 	=	$(this).val();

	var task 		=	'<?=$ajax_task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#costcenter_code').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/costcenter/ajax/get_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Code you entered already exists!</b>";
			$('#costcenter_code').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}else if( ( ajax.curr_code != "" && data.msg == "donut") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
		else if( ( ajax.curr_code != "" && data.msg == "donut") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});

$('form').on('ifChecked','#depreciate', function(e){
	$("#useful_life").prop('disabled',false);
	$("#salvage_value").prop('disabled',false);
	// $("#gl_asset").prop('disabled',false);
	$("#gl_accdep").prop('disabled',false);
	$("#gl_depexpense").prop('disabled',false);
	$('#useful_life').closest('.form-group').find('#useful_life').attr("data-validation","required integer");
	$('#salvage_value').closest('.form-group').find('#salvage_value').attr("data-validation","required decimal");
	// $('#gl_asset').closest('.form-group').find('#gl_asset').attr("data-validation","required");
	$('#gl_accdep').closest('.form-group').find('#gl_accdep').attr("data-validation","required");
	$('#gl_depexpense').closest('.form-group').find('#gl_depexpense').attr("data-validation","required");
});
$('form').on('ifUnchecked','#depreciate', function(e){
	$("#useful_life").prop('disabled',true);
	$("#salvage_value").prop('disabled',true);
	// $("#gl_asset").prop('disabled',true);
	$("#gl_accdep").prop('disabled',true);
	$("#gl_depexpense").prop('disabled',true);
	$('#useful_life').closest('.form-group').find('#useful_life').removeAttr("data-validation");
	$('#salvage_value').closest('.form-group').find('#salvage_value').removeAttr("data-validation");
	// $('#gl_asset').closest('.form-group').find('#gl_asset').removeAttr("data-validation");
	$('#gl_accdep').closest('.form-group').find('#gl_accdep').removeAttr("data-validation");
	$('#gl_depexpense').closest('.form-group').find('#gl_depexpense').removeAttr("data-validation");

	$('#useful_life').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
	$('#salvage_value').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
	// $('#gl_asset').closest('.form-group').removeCslass('has-error').find('p.help-block').html('');
	$('#gl_accdep').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
	$('#gl_depexpense').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
	

});
var task = '<?php echo $ajax_task; ?>';
var dep  = $('#depreciate').is(':checked');

if(dep != false){
	$("#useful_life").prop('disabled',false);
	$("#salvage_value").prop('disabled',false);
	// $("#gl_asset").prop('disabled',false);
	$("#gl_accdep").prop('disabled',false);
	$("#gl_depexpense").prop('disabled',false);
	$('#useful_life').closest('.form-group').find('#useful_life').attr("data-validation","required integer");
	$('#salvage_value').closest('.form-group').find('#salvage_value').attr("data-validation","required decimal");
	// $('#gl_asset').closest('.form-group').find('#gl_asset').attr("data-validation","required");
	$('#gl_accdep').closest('.form-group').find('#gl_accdep').attr("data-validation","required");
	$('#gl_depexpense').closest('.form-group').find('#gl_depexpense').attr("data-validation","required");
}else if(dep == false){
	$("#useful_life").prop('disabled',true);
	$("#salvage_value").prop('disabled',true);
	// $("#gl_asset").	prop('disabled',true);
	$("#gl_accdep").prop('disabled',true);
	$("#gl_depexpense").prop('disabled',true);
	$('#useful_life').closest('.form-group').find('#useful_life').removeAttr("data-validation");
	$('#salvage_value').closest('.form-group').find('#salvage_value').removeAttr("data-validation");
	// $('#gl_asset').closest('.form-group').find('#gl_asset').removeAttr("data-validation");
	$('#gl_accdep').closest('.form-group').find('#gl_accdep').removeAttr("data-validation");
	$('#gl_depexpense').closest('.form-group').find('#gl_depexpense').removeAttr("data-validation");
}

$('#name').on('blur',function(){
	ajax.old_name 	= 	$('#h_name').val();
	ajax.curr_name 	=	$(this).val();

	var task 		=	'<?=$ajax_task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#name').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/costcenter/ajax/get_duplicate_name',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Name you entered already exists!</b>";
			$('#name').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}else if( ( ajax.curr_name != "" && data.msg == "donut") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
		else if( ( ajax.curr_name != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});



$('body').on('blur blur_validate keyup keydown', '[data-validation~="alpha_num_special"]', function(e) {
    var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z 0-9</p><p>Letters and Numbers Only</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if (! (/^[a-zA-Z0-9., &()\[\]_\-':;]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
</script>
<?php endif ?>