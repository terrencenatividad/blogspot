<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "bankForm" class="form-horizontal">
				<input type="hidden" name="id" id="id" value="<?=$id?>">	
				<input type="hidden" name="h_accountno" id="h_accountno" value="<?=$accountno?>">	
				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
									->setLabel('Bank Account GL Code:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('gl_code')
									->setId('gl_code')
									->setList($gllist)
									->setMaxLength(20)
									->setPlaceholder('Select GL Code')
									->setValue($gl_code)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Bank Account Name:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('shortname')
									->setId('shortname')
									->setValue($shortname)
									->setMaxLength(100)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Bank Account Code')
									->setSplit('col-md-3', 'col-md-6')
									->setName('bankcode')
									->setId('bankcode')
									->setValue($bankcode)
									->setValidation('required num')
									->setMaxLength(20)
									->draw($show_input);
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Bank Account Number:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('accountno')
									->setId('accountno')
									->setValue($accountno)
									->setMaxLength(20)
									->setValidation('required num')
									->draw($show_input);
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Currency')
								->setPlaceholder('Filter Currency')
								->setSplit('col-md-3', 'col-md-6')
								->setName('currency')
								->setId('currency')
								->setList($currencylist)
								->setMaxLength(20)
								// ->setValue('PHP')
								->setPlaceholder('Select Currency')
								->setValidation('required')
								->draw($show_input);
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('checkbox')
								->setLabel('Checking Account')
								->setSplit('col-md-3', 'col-md-6')
								->setName('checking_account')
								->setId('checking_account')
								->setDefault('1')
								->setValue($checking_account)
								->draw($show_input);
						?>
						
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
								->setLabel('Bank Address:')
								->setSplit('col-md-3', 'col-md-6')
								->setName('address1')
								->setId('address1')
								->setValue($address1)
								->setMaxLength(100)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>

					
				</div>

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
								<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/currency/edit/<?=$currencycode?>" style="outline:none;">Edit</a>
							</div>
						<?
							}
						?>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" id="btnCancel">Cancel</button>
						</div>
					</div>	
				</div>
			</form>
        </div>

    </div>
</section>

<script>
var ajax = {};

$('#bankForm #btnSave').on('click',function(){

	$('#bankForm #gl_code').trigger('blur');
	$('#bankForm #currency').trigger('blur');
	$('#bankForm #bankcode').trigger('blur');
	$('#bankForm #bankname').trigger('blur');
	$('#bankForm #accountcode').trigger('blur');
	$('#bankForm #acccountno').trigger('blur');

	if ($('#bankForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/bank/ajax/<?=$task?>', $('#bankForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				window.location = '<?php echo BASE_URL . 'maintenance/bank'; ?>';
			}
		});
	}
});

$('#bankForm #accountno').on('blur',function(){
	
	ajax.old_code 	= 	$('#h_accountno').val();
	
	ajax.curr_code 	=	$(this).val();

	var task 		=	'<?=$task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#accountno').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/bank/ajax/check_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Account Number you entered already exists!</b>";
			$('#bankForm #accountno').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});

$('#bankForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/bank'; ?>';
});

// $('#pricelist_table').on('click','.tag_customers',function(){
// 	var code	=	$(this).attr('data-id');
// 	window.location = '<?=MODULE_URL?>tag_customers/'+code;
// });


</script>