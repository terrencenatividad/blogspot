<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "currencyForm" class="form-horizontal">
				<input type="hidden" name="h_currency_code" id="h_currency_code" value="<?=$currencycode?>">	
				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							echo $ui->formField('text')
									->setLabel('Currency Code:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('currencycode')
									->setId('currencycode')
									->setValue($currencycode)
									->setValidation('required code')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<?php
							echo $ui->formField('text')
									->setLabel('Currency Name:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('currency')
									->setId('currency')
									->setValue($currency)
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

$('#currencyForm #btnSave').on('click',function(){

	$('#currencyForm #currencycode').trigger('blur');
	$('#currencyForm #currency').trigger('blur');

	if ($('#currencyForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/currency/ajax/<?=$task?>', $('#currencyForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				window.location = '<?php echo BASE_URL . 'maintenance/currency'; ?>';
			}
		});
	}
});

$('#currencycode').on('blur',function(){
	ajax.old_code 	= 	$('#h_currency_code').val();
	ajax.curr_code 	=	$(this).val();

	var task 		=	'<?=$task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#currencycode').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/currency/ajax/get_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Code you entered already exists!</b>";
			$('#currencycode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});

$('#currencyForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/currency'; ?>';
});


</script>