<section class="content">
	<div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">
					<div class = "col-md-8">
						<!-- <a class="btn btn-primary" role="button" href="<?=MODULE_URL?>create" style="outline:none;">Create</a>
						<form class="navbar-form navbar-left">
							<div class="btn-group" id="option_buttons">
								<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
									Options <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="javascript:void(0);" download="export_pricelist.csv" id="export"><span class="glyphicon glyphicon-open"></span> Export Price List</a></li>
									<li><a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Price List</a></li>
								</ul>
							</div>
						</form> -->
						<?= 
							$ui->CreateNewButton('');
						?>
						<?= 
							$ui->OptionButton('');
						?>
						<a class="btn btn-info btn-flat" role="button" href="<?=MODULE_URL?>master" style="outline:none;">Master Price List</a>
						<input id = "deactivateMultipleBtn" type = "button" name = "deactivate" value = "Deactivate" class="btn btn-warning btn-flat ">
					</div>
					<div class = "col-md-4">
						<div class="form-group">
							<div class="input-group" >
								<input name="table_search" id = "table_search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
								<div class="input-group-btn" style = "height: 34px;">
									<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
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
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<div class = "alert alert-warning alert-dismissable hidden">
						<button type="button" class="close" data-dismiss="alert">×</button>
						<h4><strong>Warning!</strong></h4>
						<div id = "errmsg"></div>
						<div id = "warningmsg"></div>
					</div>
					<div class="table-responsive" id="option_result" style="overflow-x: inherit;">
						<table id="pricelist_table" class="table table-striped table-condensed table-bordered">
							<thead>
								<?php
									echo $ui->loadElement('table')
											->setHeaderClass('info')
											->addHeader('',array('class'=>'col-md-1'))
											->addHeader('Price List Code',array('class'=>'col-md-3'),'sort','pl.itemPriceCode')
											->addHeader('Price List Name', array('class'=>'col-md-3'),'sort','pl.itemPriceName')
											->addHeader('Description',array('class'=>'col-md-3'),'sort','pl.itemPriceDesc')
											->addHeader('Status',array('class'=>'col-md-3'),'sort','pl.status')
											->draw();
								?>
							</thead>
							<tbody id="list_container"></tbody>
						</table>
					</div>
					<div id="pagination"></div>
				</div>
			</div>
		</form>
	</div>	
</section> 

<!-- Import Modal -->
<div class="import-modal" id="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Price List</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="PriceList_Template.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<?php
								echo $ui->setElement('file')
										->setId('import_csv')
										->setName('import_csv')
										->setAttribute(array('accept' => '.csv'))
										->setValidation('required')
										->draw();
							?>
							<span class="help-block"></span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var ajax = filterFromURL();
var ajax_call = '';

$('#table_search').on('input', function () {
	ajax.page = 1;
	ajax.search = $(this).val();
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

$('#items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

ajaxToFilter(ajax,{ search: '#table_search',  limit: '#items'});

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function show_success_msg(msg)
{
	$('#success_modal #message').html(msg);
	$('#success_modal').modal('show');
	setTimeout(function() {												
		window.location = '<?= MODULE_URL ?>';		
	}, 1000)
}

function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call 	=	$.post('<?=BASE_URL?>maintenance/pricelist/ajax/price_list',ajax, function(data) {
						$('#pricelist_table #list_container').html(data.table);
						$('#pagination').html(data.pagination);
						$("#export_id").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
						if (ajax.page > data.page_limit && data.page_limit > 0) {
							ajax.page = data.page_limit;
							showList();
						}
					});
};

function ajaxCallback(id) {
	//var ids = getDeleteId(id);
	ajax.code 	=	id;
	$.post('<?=MODULE_URL?>ajax/delete_template', ajax, function(data) {
		if(data.msg == "success")
		{
			showList();
		}
	});
}

function getIds(ids) {
	var x = ids.split(",");
	return "id[]=" + x.join("&id[]=");
}

$(document).ready(function() 
{
	showList();

	$( "#pricelist_table" ).on('click' , '.delete', function() 
	{
		var id = $( this ).attr("data-id");
		
		if( id != "" )
		{
			$(".delete-modal > .modal").css("display", "inline");
			$(".delete-modal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/discount/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == 'success' )	
					{
						$(".delete-modal").modal("hide");
						showList();
					}
					else
					{			
						$(".delete-modal").modal("hide");
						show_error(data.msg);
					}
				});
			});	
		}

	});

	$( "#deletelistBtn" ).click(function() 
	{	
		var id = [];

		$('input:checkbox[name="checkbox[]"]:checked').each(function()
		{
			id.push($(this).val());
		});

		if( id != "" )
		{
			$(".delete-modal > .modal").css("display", "inline");
			$(".delete-modal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/pricelist/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.success )	
					{
						window.location.href = "<?=BASE_URL?>maintenance/pricelist";
					}
					else
					{
						// Call function to display error_get_last
						show_error(data.msg);
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
                                    show_success_msg('Your Data has been imported successfully.');
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
	
	$('#success_modal .btn-success').on('click', function(){
		$('#success_modal').modal('hide');
		showList();
	});
	
	/** -- FOR DELETING DATA -- **/
	$(function() {
		linkDeleteToModal('#pricelist_table .delete', 'ajaxCallback');
	});
	/** -- FOR DELETING DATA -- end **/

	/** -- FOR TAGGING AS COMPLETE -- **/
	$('#pricelist_table').on('click','.tag_customers',function(){
		var code	=	$(this).attr('data-id');
		window.location = '<?=MODULE_URL?>tag_customers/'+code;
	});
	/** -- FOR TAGGING AS COMPLETE -- end **/
});

// Sorting Script
tableSort('#pricelist_table', function(value, getlist) {
	ajax.sort = value;
	ajax.page = 1;
	if (getlist) {
		showList();
	}
},ajax);

		$('#export_id').prop('download','price_list.csv');
		// $('#export_id').prop('href','<?= MODULE_URL ?>get_export');
		$('#import_id').prop('href','#import-modal');
		// $('#import_id').prop('data-toggle','modal');
		$("#import_id").click(function() 
		{
			$("#import-modal > .modal").css("display", "inline");
			$('#import-modal').modal();
		});

		$('#pricelist_table').on('click', '.activate', function() { 
			var id = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+id ,function(data) {
				showList();
			});
		});

		$('#pricelist_table').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+id ,function(data) {
					showList();
				});
			});
		});

		$("#deactivateMultipleBtn").click(function() 
		{
			$('#multipleDeactivateModal').modal('show');
			$( "#multipleDeactivateModal #btnDeac" ).click(function() {
			ids 	=	getSelectedIds();
			$.post('<?=MODULE_URL?>ajax/update_multiple_deactivate', "&ids="+ids ,function(data) {
				
				if( data.msg == 'success' )
				{
					showList();
					$('#multipleDeactivateModal').modal('hide');
				} 
			});
		});
		});

		function getSelectedIds(){
			id 	=	[];
			$('.checkbox:checked').each(function(){
				id.push($(this).val());
			});
			return id;
		}
</script>
