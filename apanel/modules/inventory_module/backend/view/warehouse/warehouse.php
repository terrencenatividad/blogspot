<section class="content">
	<div class="alert alert-danger alert-dismissable hidden" id="pageAlert">
		<button type="button" class="close" data-hide="alert">&times;</button>
		<strong>Error!</strong>
		<br/>
		<p></p>
	</div>
	<div class="box box-primary">
		<div class="box-body">
			<br>
			<form action="" method="post" class="form-horizontal" id = "warehouseForm">
				<input type="hidden" name="h_warehouse_code" id="h_warehouse_code" value="<?=$warehousecode?>">	
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Warehouse Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('warehousecode')
								->setId('warehousecode')
								->setValue($warehousecode)
								->setValidation('required code')
								->draw($show_input);
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Warehouse Description')
								->setSplit('col-md-4', 'col-md-8')
								->setName('description')
								->setId('description')
								->setValue($description)
								->setValidation('required')
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
							<a class="btn btn-primary btn-flat" role="button" href="<?=BASE_URL?>maintenance/warehouse/edit/<?=$warehousecode?>" style="outline:none;">Edit</a>
						</div>
					<?
						}
					?>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" id="btnCancel" data-toggle="back_page">Cancel</button>
							<!-- <a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a> -->
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<script>
var ajax = {};

$('#warehouseForm #btnSave').on('click',function(){

	$('#warehouseForm #warehousecode').trigger('blur');
	$('#warehouseForm #description').trigger('blur');

	if ($('#warehouseForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/warehouse/ajax/<?=$ajax_task?>', $('#warehouseForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				setTimeout(function() {
					window.location = '<?php echo BASE_URL . 'maintenance/warehouse'; ?>';
				},500);
			}
		});
	}
});

$('#warehousecode').on('blur',function(){
	ajax.old_code 	= 	$('#h_warehouse_code').val();
	ajax.curr_code 	=	$(this).val();

	var task 			=	'<?=$ajax_task?>';
	var error_message 	=	'';	
	var form_group	 	= 	$('#warehousecode').closest('.form-group');

	$.post('<?=BASE_URL?>maintenance/warehouse/ajax/get_duplicate',ajax, function(data) {
		if( data.msg == 'exists' )
		{
			error_message 	=	"<b>The Code you entered already exists!</b>";
			$('#warehousecode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}
		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
		{
			if (form_group.find('p.help-block').html() != "") {
				form_group.removeClass('has-error').find('p.help-block').html('');
			}
		}
	});
});

$('#warehouseForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/warehouse'; ?>';
});

</script>