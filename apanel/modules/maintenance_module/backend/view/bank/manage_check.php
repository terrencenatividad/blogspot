<section class="content">

	<!-- <div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">Ã—</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div> -->

    <div class="box box-primary">
        <div class="box-body">
            <form method = "post" id = "checkForm" class="form-horizontal">
				<input type="hidden" name="bank_id" id="id" value="<?=$id?>">
				<input type="hidden" name="oldbooknumber" id="booknumber" value="<?=$booknumber?>">		
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
									->setLabel('First Check Number')
									->setSplit('col-md-3', 'col-md-6')
									->setName('firstchequeno')
									->setClass('firstchequeno')
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
									->setLabel('Last Check Number')
									->setSplit('col-md-3', 'col-md-6')
									->setName('lastchequeno')
									->setClass('lastchequeno')
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
						
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnSave">Save</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-flat" id="btnEdit">Save</button>
							</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" id="btnCancel">Cancel</button>
						</div>
					</div>	
				</div>
			</form>
        </div>
    </div>

	<div class="table table-condensed table-bordered table-hover">
		<table id = "bank_check" class="table table-hover">
			<thead>
				<thead>
					<tr class="info">
						<th ></th>
						<th >Bank Name</th>
						<th >Account Number</th>
						<th >Book Number</th>
						<th >Check Number</th>
						<th >Next Check No</th>
						<th >Status</th>
					</tr>
				</thead>
			</thead>
			
			<tbody id = "check_container">

			</tbody>

		</table>
		<div id="pagination"></div>
	</div>   
</section>

<div id="delete_modal" class="modal modal-danger">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this record?</p>
			</div>
			<div class="modal-footer text-center">
				<button type="button" id="delete_yess" class="btn btn-outline btn-flat" onclick="">Yes</button>
				<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="set_modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to set this as your default check book?

				<input type="hidden" id=""/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="set_yes">Yes</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal_checker" class="modal">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Number entered is within the series of existing checks</p>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<!-- <div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="set_yes">Yes</button>
						</div> -->
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group text-center">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Ok</button>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>

<div id="modal_checker_on_range" class="modal">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>First check number cannot be greater than last check number!</p>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="clear_checks" data-dismiss="modal">Ok</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<!-- <div class="btn-group text-center">
							<button type="button"  class="btn btn-default btn-flat" data-dismiss="modal">Ok</button>
						</div> -->
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>

<div class="modal fade" id="cancel_checks" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">

		<div class="modal-content">
			<form method = "POST" id="cancelled_checks">
				<div class="modal-header ">
					<div class="row">
						<div class="col-md-11">
							<h4 class = 'bold'>Cancel Check</h4>
						</div>
						<div class="col-md-1 right">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
					</div>
				</div>

				<div class="modal-body">
					<div class = 'row'>
						<input type='hidden' name='bank' id='bank' value=''>

						<div class = 'panel panel-default'>
							<div class = 'panel-heading'>
								<h3 class="panel-title">Please enter a number within <span id="range"></span></h3>
							</div>
							<div class = 'panel-body'>
								<div class = 'col-md-12 no-padding'>
									<?php
										echo $ui->formField('text')
												->setLabel('First Number')
												->setSplit('col-md-4', 'col-md-8')
												->setName('firstcancelled')
												->setId('firstcancelled')
												->setValidation('required num')
												->setValue("")
												->draw();
									?>
								</div>
								<br><br><br>
								<div class = 'col-md-12 no-padding'>
									<?php
										echo $ui->formField('text')
												->setLabel('Last Number')
												->setSplit('col-md-4', 'col-md-8')
												->setName('lastcancelled')
												->setId('lastcancelled')
												->setValidation('required num')
												->setValue("")
												->draw();
									?>
								</div>
								<br><br><br>
								<div class = 'col-md-12 no-padding'>
									<?php
										echo $ui->formField('textarea')
												->setLabel('Reason')
												->setSplit('col-md-4', 'col-md-8')
												->setName('remarks')
												->setId('remarks')
												->setValidation('required')
												->setValue("")
												->draw();
									?>
								</div>

							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-success" id="save_cancelled" >Save</button>
								<button type="button" data-dismiss="modal" class="btn btn-default" >Cancel</button> 
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
var ajax = {};

$('#checkForm #btnSave').on('click',function(){
	$('#checkForm #booknumber').trigger('blur');
	$('#checkForm #firstchequeno').trigger('blur');
	$('#checkForm #lastchequeno').trigger('blur');
	var bank_id = $('#id').val();

	if ($('#checkForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/bank/ajax/<?=$task?>', $('#checkForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' )
			{
				 window.location = self.location;
			}
		});
	}
});

// $('#checkForm #booknumber').on('blur',function(){
	
// 	ajax.old_code 	= 	$('#checkForm #booknumber').val();
	
// 	ajax.curr_code 	=	$(this).val();

// 	var task 		=	'<?=$task?>';
// 	var error_message 	=	'';	
// 	var form_group	 	= 	$('#checkForm #booknumber').closest('.form-group');

// 	$.post('<?=BASE_URL?>maintenance/bank/ajax/check_duplicate_booknumber',ajax, function(data) {
// 		if( data.msg == 'exists' )
// 		{
// 			error_message 	=	"<b>The Book Number you entered already exists!</b>";
// 			$('#checkForm #booknumber').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
// 		}
// 		else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit'))
// 		{
// 			if (form_group.find('p.help-block').html() != "") {
// 				form_group.removeClass('has-error').find('p.help-block').html('');
// 			}
// 		}
// 	});
// });

$('#checkForm #btnCancel').on('click',function(){
	window.location = '<?php echo BASE_URL . 'maintenance/bank'; ?>';
});

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

var bank_checks = [];

function showList(){
	ajax.id = $('#id').val();
	$.post('<?=BASE_URL?>maintenance/bank/ajax/check_list', ajax, function(data)
	{
		$('#bank_check #check_container').html(data.table);
        $('#pagination').html(data.pagination);
		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			getList();
		}
		$('#bank_check tbody tr').each(function() {
			var start_check 	= $(this).find('.start_check').html();
			if (start_check) {
				bank_checks.push(start_check);
			}
		});

		

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

	$( "#bank_check" ).on('click' , '.delete', function() 
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
			show_error(data.msg);
		}
	});
}

$(function() {
	linkButtonToTable('#item_multiple_delete', '#bank_check');
	linkDeleteToModal('#bank_check .delete_check_series', 'ajaxCallback');
	linkDeleteMultipleToModal('#item_multiple_delete', '#bank_check', 'ajaxCallback');
});

// Sorting Script
tableSort('#bank_check', function(value) {
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

$('#check_container').on('click', '.manage_check', function(){
	var id = $(this).attr('data-id');
	window.location = '<?=MODULE_URL?>manage_check/' + id;
});

$('#btnEdit').hide();
$('#check_container').on('click', '.edit_check_series', function(){
	ajax.id     =  $('#id').val();
	bookno =  $(this).closest('tr').find('#start_check').html();
	var result = bookno.split('-');
	ajax.booknumber = result[0];
		$.post('<?=BASE_URL?>maintenance/bank/ajax/edit_check', ajax ,  function(data){
			if (data){
				$('#checkForm #booknumber').val(data.booknumber);
				$('#firstchequeno').val(data.firstchequeno);
				$('#lastchequeno').val(data.lastchequeno);
				var task = data.task;
				if (task == 'update_check'){
					$('#btnSave').hide();
					$('#btnEdit').show();
				}
			}
		});
});

$('#checkForm #btnEdit').on('click',function(){
	if ($('#checkForm').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/bank/ajax/update_check', $('#checkForm').serialize()+ '<?=$ajax_post?>', function(data) {
			if( data.msg == 'success' || data.msg == true)
			{
				window.location = self.location;
			}
		});
	}
});

$('#check_container').on('click', '.delete_check_series', function(){
	ajax.id     =  $('#id').val();
	bookno =  $(this).closest('tr').find('#start_check').html();
	var result = bookno.split('-');
	ajax.booknumber = result[0];
		if( id != "" )
		{
			$(".delete-modal").modal("show");
			$( "#delete_yess" ).click(function() {
				$.post('<?=BASE_URL?>maintenance/bank/ajax/delete_check', ajax ,  function(data){
						window.location = self.location;
				});
			});
		}
});

$('#check_container').on('click', '.set_as_default_check', function(){
	ajax.id     =  $('#id').val();
	bookno =  $(this).closest('tr').find('#start_check').html();
	var result = bookno.split('-');
	ajax.booknumber = result[0];
		if( id != "" )
		{
			$("#set_modal").modal("show");
			$( "#set_yes" ).click(function() {
				$.post('<?=BASE_URL?>maintenance/bank/ajax/set_check', ajax ,  function(data){
						window.location = self.location;
				});
			});
		}
});

$('#checkForm #firstchequeno, #lastchequeno').on('blur' ,function(){
	var first_number = parseFloat($('#firstchequeno').val());
	var end_number = parseFloat($('#lastchequeno').val());
	if (first_number != "" && end_number !="" ){
		if (end_number < first_number){
			$('#modal_checker_on_range').modal('show');
		}
	}

	jQuery.each(bank_checks,function(ind,val){
		var result = val.split('-');
		var start = parseFloat(result[0]);
		var end = parseFloat(result[1]);
		if ( (start <= first_number && end >= first_number) || (start <= end_number && end >= end_number) ){
			$('#modal_checker').modal('show');
		}  
    })
	
})

$('#clear_checks').on('click', function(){
	$('#checkForm').trigger("reset");
}) 

$('#check_container').on('click', '.cancel_check_range', function(){
	ajax.id     =  $('#id').val();
	check_range =  $(this).closest('tr').find('#start_check').html();
	var result = check_range.split('-');
	ajax.start = result[0];
	ajax.end = result[1];
	$('#range').html(check_range);
	if( id != "" )
	{
		$("#cancel_checks").modal("show");
		$( "#set_yes" ).click(function() {
			$.post('<?=BASE_URL?>maintenance/bank/ajax/set_check', ajax ,  function(data){
					window.location = self.location;
			});
		});
	}
});

$('#save_cancelled').on('click',function(){
	$('#cancelled_checks #firstcancelled').trigger('blur');
	$('#cancelled_checks #lastcancelled').trigger('blur');
	$('#cancelled_checks #remarks').trigger('blur');
	ajax.firstcancelled = $('#firstcancelled').val();
	ajax.lastcancelled = $('#lastcancelled').val();
	ajax.remarks = $('#remarks').val();
	if ($('#cancelled_checks').find('.form-group.has-error').length == 0)
	{	
		$.post('<?=BASE_URL?>maintenance/bank/ajax/save_cancelled', ajax, function(data) {
			if( data.msg == 'yes' )
			{
				window.location = self.location;
			}
		});
	}
})

$('#cancelled_checks #firstcancelled, #lastcancelled').on('blur',function(){
	var first_number= $('#firstcancelled').val();
	var end_number 	= $('#lastcancelled').val();
	var range 		= $('#range').html();
	var range = range.split('-');
	var start = parseFloat(range[0]);
	var end = parseFloat(range[1]);
	if (first_number){
		if ( (start > first_number && first_number < end) ){
			error_message 	=	"<b>The number you entered is not within the check range</b>";
			$('#cancel_checks #firstcancelled').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}  else {
			$('#cancel_checks #firstcancelled').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
		}
	}

	if (end_number){
		if (start > end_number && end_number < end){
			error_message 	=	"<b>The number you entered is not within the check range</b>";
			$('#cancel_checks #lastcancelled').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
		}  else {
			$('#cancel_checks #lastcancelled').closest('.form-group').removeClass('has-error').find('p.help-block').html('');
		}
	}

})


</script>