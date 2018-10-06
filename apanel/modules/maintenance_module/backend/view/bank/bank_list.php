<section class="content">

    <div class="box box-primary">
        
        <div class="box-header">
            <div class="row">

				<div class = "col-md-8">
					<?=$ui->CreateNewButton('');?>
					<input type="button" id="item_multiple_delete" class="btn btn-danger  btn-flat"  value="Delete" >
				</div>
				<div class="input-group input-group-sm">
					<input id="search" name="table_search" class="form-control pull-right" placeholder="Search" type="text">
					<div class="input-group-btn">
						<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
					</div>
				</div>
            </div>
			
			<br>
			<div class="row">
				<div class="col-md-4 col-md-offset-8">
					<div class="row">
						<div class="col-sm-8 col-xs-6 text-right">
							<label for="" class="padded">Items: </label>
						</div>
						<div class="col-sm-4 col-xs-6">
							<div class="form-group">
								<select id="items">
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class='row'>
				<div class="col-md-12">
					<div class = "alert alert-warning alert-dismissable hidden">
						<button type="button" class="close" data-dismiss="alert">×</button>
						<h4><strong>Error!</strong></h4>
						<div id = "errmsg"></div>
					</div>
				</div>
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
								->addHeader('Bank Account Name',array('class'=>'col-md-3'),'sort','shortname')
								->addHeader('Bank Account Code',array('class'=>'col-md-3'),'sort','bankcode')
								->addHeader('Bank Account Number', array('class'=>'col-md-3'),'sort','accountno')
								->addHeader('Status', array('class'=>'col-md-3'),'','')
								->draw();
					?>
				</thead>
               
                <tbody id = "list_container">
				</tbody>

            </table>
			<div id="pagination"></div>
        </div>   

    </div>
</section>


<script>
var ajax = {};

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function showList(){
	$.post('<?=BASE_URL?>maintenance/bank/ajax/bank_list', ajax, function(data)
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
	var li = $(this).closest('li');
	if (li.not('.active').length && li.not('.disabled').length) {
		ajax.page = $(this).attr('data-page');
		showList();
	}
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
				$.post('<?=BASE_URL?>maintenance/bank/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == 'success' )	
					{
						$(".delete-modal").modal("hide");
						showList();
					}
					else
					{			
						$(".delete-modal").modal("hide");
						show_error("Unable to delete the Bank.");
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
	$.post('<?=BASE_URL?>maintenance/bank/ajax/delete', 'id=' + id, function(data) 
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

$('#list_container').on('click', '.activate', function(){
	var id = $(this).attr('data-id');
	$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+id ,function(data) {
		showList();
	});
});

$('#list_container').on('click', '.deactivate', function() { 
	$('#deactivate_modal').modal('show');
	var id = $(this).attr('data-id');
	
	$('#deactivate_modal').on('click', '#deactyes', function() {
		$('#deactivate_modal').modal('hide');
		
		$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+id ,function(data) {
			showList();
		});
	});
});



</script>
