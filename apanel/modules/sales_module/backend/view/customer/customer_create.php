<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "customerForm" class="form-horizontal">
				<input type="hidden" name="h_customer_code" id="h_customer_code" value="<?=$partnercode?>">	
				<div class = "col-md-12">&nbsp;</div>
				<div class="col-md-11">
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('partnercode')
								->setId('partnercode')
								->setValidation('required code')
								->setMaxLength(20)
								->addHidden((isset($task) && $task == 'update'))
								->setAttribute(array('autocomplete' => 'off'))
								->setValue($partnercode)
								->draw((isset($task) && $task == 'add'));
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Company Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('partnername')
								->setId('partnername')
								->setMaxLength(255)
								->setValidation('required special')
								->setValue($partnername)
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
								->setMaxLength(105)
								->setValidation('required special')
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
								->setAttribute(array('data-inputmask' => "'alias': 'email'"))
								->setValue($email)
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Business Type')
								->setPlaceholder('Filter Business Type')
								->setSplit('col-md-4', 'col-md-8')
								->setName('businesstype')
								->setId('businesstype')
								->setList($bt_select)
								->setValidation('required')
								->setValue($businesstype)
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
								->setValidation('integer')
								->setMaxLength(20)
								// ->setAttribute(array('data-inputmask' => "'mask': '0999-999-9999'"))
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">	
						<!-- <label for="first_name" class="control-label" style="margin:15px;"><span style="font-size:15px;">Contact Person:</span></label> -->
						<h4>Contact Person</h4>
						
						<hr>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('First Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('first_name')
								->setId('first_name')
								->setValidation('special')
								->setMaxLength(50)
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
								->setValidation('special')
								->setMaxLength(50)
								->setValue($last_name)
								->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Payment Terms')
								->setSplit('col-md-4', 'col-md-8')
								->setName('terms')
								->setId('terms')
								->setPlaceholder('30')
								->setValue($terms)
								->setMaxLength(5)
								->setValidation('integer')
								->draw($show_input);
						?>
					</div>
					
				</div>

				<hr/>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('TIN')
								->setSplit('col-md-4', 'col-md-8')
								->setName('tinno')
								->setId('tinno')
								->setMaxLength(15)
								->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
								->setPlaceholder('000-000-000-000')
								->setValue($tinno)
								->draw($show_input);
						?>
					</div>
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
							<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/customer/edit/<?=$partnercode?>" style="outline:none;">Edit</a>
						</div>
					<?
						}
					?>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<?php if (MODAL): ?>
								<button type="button" class="btn btn-default btn-flat" id="btnCancel">Cancel</button>
							<?php else: ?>
								<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
							<?php endif ?>
						</div>
					</div>
				</div>
			</form>
        </div>

    </div>
</section>
<?php if ($show_input): ?>
<script>

var ajax = {};

$('#customerForm #btnSave').on('click',function(){

	$('#customerForm').find('.form-group').find('input, textarea, select').trigger('blur');

	if ($('#customerForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/customer/ajax/<?=$task?>', $('#customerForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				<?php if (MODAL): ?>
					addCustomerToDropdown();
				<?php else: ?>
					setTimeout(function() {
						window.location = '<?php echo BASE_URL . 'maintenance/customer'; ?>';
					},500);
				<?php endif ?>
			}
		});
	}
});

$('#partnercode').on('blur',function(){
	ajax.old_code 	= 	$('#h_customer_code').val();
	ajax.curr_code 	=	$(this).val();

	var task 			=	'<?=$task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#partnercode').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/customer/ajax/get_duplicate',ajax, function(data) {
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

$('#customerForm #btnCancel').on('click',function(){
	<?php if (MODAL): ?>
		closeModal('customer');
	<?php else: ?>
		window.location = '<?php echo BASE_URL . 'maintenance/customer'; ?>';
	<?php endif ?>
});

</script>	
<?php endif ?>