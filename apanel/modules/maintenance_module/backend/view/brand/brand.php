<section class="content">

	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
	
        <div class="box-body">
            <form method = "post" id = "brandForm" class="form-horizontal">
				<input type="hidden" name="h_brand_code" id="h_brand_code" value="<?=$brandcode?>">	
				<div class = "col-md-12">&nbsp;</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							echo $ui->formField('text')
									->setLabel('Brand Code: ')
									->setSplit('col-md-3', 'col-md-6')
									->setName('brandcode')
									->setId('brandcode')
									->setMaxLength(12)
									->setValue($brandcode)
									->setValidation('required') //required code
									->draw($show_input);
						?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<?php
							echo $ui->formField('text')
									->setLabel('Brand Name: ')
									->setSplit('col-md-3', 'col-md-6')
									->setName('brandname')
									->setId('brandname')
									->setMaxLength(30)
									->setValue($brandname)
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
							<?php if($access == 1):  ?>
							<div class="btn-group">
								<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/brand/edit/<?=$brandcode?>" style="outline:none;">Edit</a>
							</div>
							<?php endif; ?>
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

$('#brandForm #btnSave').on('click',function(){

	$('#brandForm #brandcode').trigger('blur');
	$('#brandForm #brandname').trigger('blur');

	if ($('#brandForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/brand/ajax/<?=$task?>', $('#brandForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				$('#delay_modal').modal('show');
					setTimeout(function() {							
						window.location = '<?php echo BASE_URL . 'maintenance/brand'; ?>';
					}, 1000)
				
			}
		});
	}
});

$('#brandcode').on('blur',function(){
	ajax.old_code 	= 	$('#h_brand_code').val();
	ajax.curr_code 	=	$(this).val();

	var task 		=	'<?=$task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#brandcode').closest('.form-group');
	var hasSpace = $(this).val().indexOf(' ')>=0;
	var regex = /^[0-9a-zA-Z\_]+$/;

	$.post('<?=BASE_URL?>maintenance/brand/ajax/get_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Code you entered already exists!</b>";
			$('#brandcode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if(hasSpace == true) {
			error_message 	=	"<b>Space not allowed</b>";
			$('#brandcode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if(!regex.test(ajax.curr_code))
		{
			console.log(ajax.curr_code);
			error_message 	=	"<b>Special character not allowed</b>";
			$('#brandcode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}

	});
});

$('#brandForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/brand'; ?>';
});


</script>