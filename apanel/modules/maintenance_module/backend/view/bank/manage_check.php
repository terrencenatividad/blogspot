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
				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<!-- <div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
									->setLabel('Bank Account GL Code:')
									->setSplit('col-md-3', 'col-md-6')
									->setName('gl_code')
									->setId('gl_code')
									->setList($gllist)
									->setNone('')
									->setAttribute(array("disabled" => "disabled"))
									// ->setValue($gl_code)
									->setValidation('required')
									->draw($show_input);
						?>
					</div> -->

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Book Number:')
                                    ->setSplit('col-md-3', 'col-md-6')
                                    // ->setAttribute(array("disabled" => "disabled"))
									->setName('booknumber')
									->setId('booknumber')
									->setValue($booknumber)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Start Number')
									->setSplit('col-md-3', 'col-md-6')
									->setName('firstchequeno')
									->setId('firstchequeno')
									->setValue($firstchequeno)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('End Number')
									->setSplit('col-md-3', 'col-md-6')
									->setName('lastchequeno')
									->setId('lastchequeno')
									->setValue($lastchequeno)
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

	// $('#bankForm #bankcode').trigger('blur');
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
	
	ajax.old_code 	= 	$('#accountno').val();
	
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


</script>