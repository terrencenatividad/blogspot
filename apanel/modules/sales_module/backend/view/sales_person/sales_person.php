<section class="content">

    <div class="box box-primary">
        
        <div class="box-body">
            <form method = "post" id = "salespersonForm" class="form-horizontal">
				<input type="hidden" name="h_sp_code" id="h_sp_code" value="<?=$partnercode?>">	
				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Sales Person Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('partnercode')
								->setId('partnercode')
								->addHidden((isset($task) && $task == 'update'))
								->setValidation('required code')
								->setValue($partnercode)
								->draw((isset($task) && $task == 'add'));
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">	
						<label for="first_name" class="control-label" style="margin:15px;"><span style="font-size:15px;">Contact Person:</span></label>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('First Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('first_name')
								->setId('first_name')
								->setValue($first_name)
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Last Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('last_name')
								->setId('last_name')
								->setValue($last_name)
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
								->setLabel('Address')
								->setSplit('col-md-4', 'col-md-8')
								->setName('address1')
								->setId('address1')
								->setValidation('required')
								->setValue($address1)
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('E-mail')
								->setSplit('col-md-4', 'col-md-8')
								->setName('email')
								->setId('email')
								->setValue($email)
								->setAttribute(array('data-inputmask' => "'alias': 'email'"))
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<!--<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Business Type')
								->setPlaceholder('Filter Business Type')
								->setSplit('col-md-4', 'col-md-8')
								->setName('businesstype')
								->setId('businesstype')
								->setList($bt_select)
								->setValue($businesstype)
								->draw($show_input);
						?>
					</div>-->
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('TIN')
								->setSplit('col-md-4', 'col-md-8')
								->setName('tinno')
								->setId('tinno')
								->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
								->setPlaceholder('000-000-000-000')
								->setValue($tinno)
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Contact Number')
								->setSplit('col-md-4', 'col-md-8')
								->setName('mobile')
								->setId('mobile')
								->setValue($mobile)
								//->setAttribute(array('data-inputmask' => "'mask': '0999-9999-999'"))
								->draw($show_input);
						?>
					</div>
				</div>

				<hr/>

				<div class="row row-dense">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">

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
							<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/sales_person/edit/<?=$partnercode?>" style="outline:none;">Edit</a>
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
</section>

<script>

// $('form').submit(function(e) {
// 	e.preventDefault();
// 	$.post('<?=BASE_URL?>maintenance/sales_person/ajax/<?=$task?>', $(this).serialize()+ '<?=$ajax_post?>', function(data) {
// 		if (data.msg == 'success') {
// 			window.location = '<?php echo BASE_URL . 'maintenance/sales_person'; ?>';
// 		}
// 	});
// });

var ajax = {};

$('#btnSave').on('click',function(){

	$('#salespersonForm #partnercode').trigger('blur');
	$('#salespersonForm #first_name').trigger('blur');
	$('#salespersonForm #last_name').trigger('blur');
	$('#salespersonForm #address1').trigger('blur');

	if ($('#salespersonForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/sales_person/ajax/<?=$task?>', $('#salespersonForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				setTimeout(function() {
					window.location = '<?php echo BASE_URL . 'maintenance/sales_person'; ?>';
				},500);
			}
		});
	}
});

$('#partnercode').on('blur',function(){
	ajax.old_code 	= 	$('#h_sp_code').val();
	ajax.curr_code 	=	$(this).val();

	var error_message 	=	'';	
	var form_group	 	= 	$('#partnercode').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/sales_person/ajax/get_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Code you entered already exists!</b>";
			$('#partnercode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});

$('#btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/sales_person'; ?>';
});

</script>