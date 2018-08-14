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

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
									->setLabel('Book Number:')
                                    ->setSplit('col-md-3', 'col-md-6')
									->setName('booknumber')
									->setId('booknumber')
									->setValidation('required num')
									->setMaxLength(20)
									->setValue($booknumber)
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
									->setValidation('required num')
									->setMaxLength(20)
									->setValue($firstchequeno)
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
									->setValidation('required num')
									->setMaxLength(20)
									->setValue($lastchequeno)
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

	<div class="box-body table table-responsive">
		<table id = "currency_table" class="table table-hover">
			<thead>
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader(
								'<input type="checkbox" class="checkall">',
								array(
									'class' => 'col-md-1 text-center'
								)
							)
							->addHeader('Account Number.',array('class'=>'col-md-3'),'sort','currencycode')
							->addHeader('Book Number',array('class'=>'col-md-3'),'sort','currencycode')
							->addHeader('Check Batch', array('class'=>'col-md-3'),'sort','currency')
							->addHeader('Next Check No', array('class'=>'col-md-3'),'sort','currency')
							->draw();
				?>
			</thead>
			
			<tbody id = "list_container">
			</tbody>

		</table>
		<div id="pagination"></div>
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

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function showList(){
	ajax.id = $('#id').val();
	$.post('<?=BASE_URL?>maintenance/bank/ajax/check_list', ajax, function(data)
	{
		$('#currency_table #list_container').html(data.table);
        $('#pagination').html(data.pagination);
        //$("#export").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			getList();
		}
	});
};

$( "#search" ).keyup(function() {
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
});

$(document).ready(function() 
{
	showList();

	$( "#currency_table" ).on('click' , '.delete', function() 
	{
		var id = $( this ).attr("data-id");
		
		if( id != "" )
		{
			$(".delete-modal > .modal").css("display", "inline");
			$(".delete-modal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/currency/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == 'success' )	
					{
						$(".delete-modal").modal("hide");
						showList();
					}
					else
					{			
						$(".delete-modal").modal("hide");
						show_error("Unable to delete the Currency.");
					}
				});
			});	
		}

	});

	/** For Import Modal **/
	$("#import").click(function() 
	{
		$(".import-modal > .modal").css("display", "inline");
		$('.import-modal').modal();
	});

	$("#importForm #btnImport").click(function() 
	{
		var formData =	new FormData();
		formData.append('file',$('#import_csv')[0].files[0]);
		ajax_call 	=	$.ajax({
							url : '<?=MODULE_URL?>ajax/save_import',
							data:	formData,
							cache: 	false,
							processData: false, 
							contentType: false,
							type: 	'POST',
							success: function(response){
								if(response && response.errmsg == ""){
									$('#import-modal').modal('hide');
									$(".alert-warning").addClass("hidden");
									$("#errmsg").html('');
									showList();
								}else{
									$('#import-modal').modal('hide');
									show_error(response.errmsg);
								}
							},
						});
	});

	$('#importForm').on('change', '#import_csv', function() {
		var filename = $(this).val().split("\\");
		$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
	});

    $('#import-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
		$('#import_csv').closest('.form-group').html(form_csv);
	});

});

function ajaxCallback(id) {
	var ids = getDeleteId(id);
	$.post('<?=BASE_URL?>maintenance/currency/ajax/delete', 'id=' + id, function(data) 
	{
		if( data.msg == 'success' )	
		{
			showList();
			$(".alert-warning").addClass("hidden");
		}
		else
		{
			// Call function to display error_get_last
			show_error(data.msg);
		}
	});
}

$(function() {
	linkButtonToTable('#item_multiple_delete', '#currency_table');
	linkDeleteToModal('#currency_table .delete', 'ajaxCallback');
	linkDeleteMultipleToModal('#item_multiple_delete', '#currency_table', 'ajaxCallback');
});

// Sorting Script
tableSort('#currency_table', function(value) {
  ajax.sort = value;
  ajax.page = 1;
  showList();
});


// Added by Isabel

$('#items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

$('#list_container').on('click', '.manage_check', function(){
	var id = $(this).attr('data-id');
	window.location = '<?=MODULE_URL?>manage_check/' + id;
});


</script>