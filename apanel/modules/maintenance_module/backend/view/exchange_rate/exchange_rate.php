<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "rateForm" class="form-horizontal">

				<div class = "row">
					<div class = "col-md-12">
					<?
						echo $ui->setElement('hidden')
								->setName('code')
								->setId('code')
								->setValue($code)
								->draw();
					?>
					</div>
				</div>

				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Effectivity Date')
								->setSplit('col-md-3', 'col-md-8')
								->setName('effectivedate')
								->setId('effectivedate')
								->setClass('datepicker-input')
								->setAttribute(array('readonly' => ''))
								->setAddon('calendar')
								->setValue($effectivedate)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Exchange Rate')
									->setSplit('col-md-3', 'col-md-8')
									->setName('exchangerate')
									->setId('exchangerate')
									->setValue($exchangerate)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Base Currency')
								->setPlaceholder('Filter Currency')
								->setSplit('col-md-3', 'col-md-8')
								->setName('basecurrencycode')
								->setId('basecurrencycode')
								->setList($currencylist)
								->setValue($basecurrencycode)
								->setValidation('required')
								->draw($show_input);
						?>
					</div>
                    <div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Exchange Currency')
								->setPlaceholder('Filter Currency')
								->setSplit('col-md-3', 'col-md-8')
								->setName('exchangecurrencycode')
								->setId('exchangecurrencycode')
								->setList($currencylist)
								->setValue($exchangecurrencycode)
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
								<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/exchange_rate/edit/<?=$code?>" style="outline:none;">Edit</a>
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

$('#rateForm #btnSave').on('click',function(){

	$('#rateForm #effectivedate').trigger('blur');
	$('#rateForm #exchangerate').trigger('blur');
	$('#rateForm #basecurrencycode').trigger('blur');
	$('#rateForm #exchangecurrencycode').trigger('blur');

	if ($('#rateForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/exchange_rate/ajax/<?=$task?>', $('#rateForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				window.location = '<?php echo BASE_URL . 'maintenance/exchange_rate'; ?>';
			}
		});
	}
});

// $('#partnercode').on('blur',function(){
// 	ajax.old_code 	= 	$('#h_customer_code').val();
// 	ajax.curr_code 	=	$(this).val();

// 	var task 		=	'<?=$task?>';

// 	$.post('<?=BASE_URL?>maintenance/exchange_rate/ajax/get_duplicate',ajax, function(data) {
// 		if( data.msg == 'exists' )
// 		{
// 			$('#partnercode').closest('.form-group').addClass("has-error");
// 			$('#warning_modal #warning_message').html("<b>The Code you entered already exists!</b>");
// 			$('#warning_modal').modal('show');
// 		}
// 		else if( data.msg == '' && task == 'edit')
// 		{
// 			$('#partnercode').closest('.form-group').removeClass("has-error");
// 		}
// 	});
// });

$('#rateForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/exchange_rate'; ?>';
});


</script>