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
									->setLabel('Code ')
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
									->setLabel('Company Name ')
									->setSplit('col-md-4', 'col-md-8')
									->setName('partnername')
									->setId('partnername')
									->setMaxLength(30)
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
									->setLabel('Address ')
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
									->setMaxLength(150)
									->setAttribute(array('data-inputmask' => "'alias': 'email'"))
									->setValue($email)
									->setValidation('email')
									->draw($show_input);
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<?php
								echo $ui->formField('dropdown')
									->setLabel('Business Type ')
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
									//->setValidation('integer')
									->setMaxLength(20)
									->setAttribute(array('data-inputmask' => "'mask': '0999-999-9999'"))
									->draw($show_input);
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">	
							<!-- <label for="first_name" class="control-label" style="margin:15px;"><span style="font-size:15px;">Contact Person:</span></label> -->
							<?php if (MODAL): ?>
								<h4>Contact Person</h4>
							<?php else: ?>
								<h3>Contact Person</h3>
							<?php endif ?>
							
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
									->setMaxLength(20)
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
									->setMaxLength(20)
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

					<hr/>

					<div class="row">
						<div class="col-md-12">	
							<!-- <label for="first_name" class="control-label" style="margin:15px;"><span style="font-size:15px;">Contact Person:</span></label> -->
							<?php if (MODAL): ?>
								<h4>Credit Limit</h4>
							<?php else: ?>
								<h3>Credit Limit</h3>
							<?php endif ?>
							
							<hr>
						</div>
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('Credit Limit')
									->setSplit('col-md-4', 'col-md-8')
									->setName('credit_limit')
									->setId('credit_limit')
									->setValidation('decimal')
									->setMaxLength(20)
									->setValue($credit_limit)
									->draw($show_input);
							?>
						</div>
						<?if($task == 'view'):?>
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('Incurred Receivables')
									->setSplit('col-md-4', 'col-md-8')
									->setName('incurred_receivables')
									->setId('incurred_receivables')
									// ->setValidation('special')
									->setMaxLength(20)
									->setValue($incurred_receivables)
									->draw($show_input);
							?>
						</div>
						<div class="col-md-6">
						</div> 
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('Outstanding Receivables')
									->setSplit('col-md-4', 'col-md-8')
									->setName('outstanding_receivables')
									->setId('outstanding_receivables')
									// ->setValidation('special')
									->setMaxLength(20)
									->setValue($outstanding_receivables)
									->draw($show_input);
							?>
						</div> 
						<?endif;?>
					</div>
					<hr/>

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
					$('#delay_modal').modal('show');
						setTimeout(function() {							
							window.location = '<?php echo BASE_URL . 'maintenance/customer'; ?>';
						}, 1000)
				<?php endif ?>
			}
		});
	}
});

var ajax_call = '';
$('#partnercode').on('input', function() {
	if (ajax_call != '') {
		ajax_call.abort();
	}
	var partnercode = $(this).val();
	$('#partnercode').closest('form').find('[type="submit"]').addClass('disabled');
	ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_check_code', 'partnercode=' + partnercode + '<?=$ajax_post?>', function(data) {
		var error_message = 'The Code you entered already exists';
		if (data.available) {
			var form_group = $('#partnercode').closest('.form-group');
			if (form_group.find('p.help-block').html() == error_message) {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		} else {
			$('#partnercode').closest('.form-group').addClass('has-error').find('p.help-block').html(error_message);
		}
		$('#partnercode').closest('form').find('[type="submit"]').removeClass('disabled');
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