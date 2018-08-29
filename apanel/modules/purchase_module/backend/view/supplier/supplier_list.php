<section class="content">
    <div class="box box-primary">
        
        <div class="box-header">
            <div class="row">

				<!-- <div class = "col-md-4">
                    <a href="<?php echo BASE_URL; ?>maintenance/supplier/create" class = "btn btn-primary danger">Create</a>
              
			      	<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete <span></span> </button>
            
					<div class="btn btn-group" id="option_buttons">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
							Options <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href = "#" id="export" download="Supplier.csv" ><span class="glyphicon glyphicon-open"></span> Export Supplier(s)</a>
							</li>
							<li>
								<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Supplier(s)</a>
							</li>
						</ul>
					</div>
			    </div> -->
				<div class = "col-md-8">
				<?= 
					$ui->CreateNewButton('');
				?>

				<?= 
					$ui->OptionButton('');
				?>
				<input id = "item_multiple_delete" type = "button" name = "delete" 
						value = "Delete" class="btn btn-danger btn-flat ">
			
				</div>

                <div class="col-md-4 pull-right">
                    <div class="input-group input-group-sm">
                        <input id="search" name="table_search" class="form-control pull-right" placeholder="Search" type="text">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
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

		<div class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><strong>Error!</strong></h4>
			<div id = "errmsg"></div>
		</div>

       <div class="box-body table table-responsive">
            <table class="table table-hover" id="supplier_table">
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
								->addHeader('Supplier Code',array('class'=>'col-md-3'),'sort','p.partnercode')
								->addHeader('Company Name', array('class'=>'col-md-3'),'sort','p.partnername')
								->addHeader('Contact Person',array('class'=>'col-md-3'),'sort','p.first_name, p.last_name')
								->addHeader('E-mail Address',array('class'=>'col-md-3'),'sort','p.email')
								->addHeader('Status',array('class'=>'col-md-3'))
								->draw();
					?>
				</thead>
               
                <form method = "post">
                    <tbody id = "list_container">
                    </tbody>
                </form>

            </table>
			<div id = "pagination"></div>
        </div>   

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
						<h4 class="modal-title">Import Suppliers</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="Supplier_Template.csv">here</a></label>
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
	ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
	ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter' });
	ajaxToFilterTab(ajax, '#filter_tabs', 'filter');

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

$('#items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call  	=	$.post('<?=BASE_URL?>maintenance/supplier/ajax/supplier_list',ajax, function(data) {
						$('#supplier_table #list_container').html(data.table);
						$('#pagination').html(data.pagination);
						$("#export_id").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
						if (ajax.page > data.page_limit && data.page_limit > 0) {
							ajax.page = data.page_limit;
							showList();
						}
					});
};

tableSort('#supplier_table', function(value, getlist) {
  	ajax.sort = value;
  	ajax.page = 1;
  	if(getlist){
	  	showList();
	}
});

showList();

$(document).ready(function() 
{
	$( "#supplier_table" ).on('click' , '.delete', function() 
	{
		var id = $( this ).attr("data-id");
		
		if( id != "" )
		{
			$(".delete-modal > .modal").css("display", "inline");
			$(".delete-modal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/supplier/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == 'success' )	
					{
						$(".delete-modal").modal("hide");
						showList();
					}
					else
					{			
						$(".delete-modal").modal("hide");
						show_error("Unable to delete the Supplier(s).");
					}
				});
			});	
		}

	});

	/** For Import Modal **/
	$("#import_id").click(function() 
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

	$('#success_modal .btn-success').on('click', function(){
		$('#success_modal').modal('hide');
		showList();
	});
});

function ajaxCallback(id) {
	var ids = getDeleteId(id);
	$.post('<?=BASE_URL?>maintenance/supplier/ajax/delete', 'id=' + id, function(data) 
	{
		if( data.msg == 'success' )	
		{
			showList();
			$(".alert-warning").addClass("hidden");
		}
		else
		{
			$('#warning_modal #warning_message').html('<p>Unable to delete Supplier: Supplier in Use</p>');
			$('#warning_modal #warning_message').append('<p>Supplier Code: ' + id + '</p>');
			$('#warning_modal').modal('show');
		}	
	});
}

$(function() {
	linkButtonToTable('#item_multiple_delete', '#supplier_table');
	linkDeleteToModal('#supplier_table .delete', 'ajaxCallback');
	linkDeleteMultipleToModal('#item_multiple_delete', '#supplier_table', 'ajaxCallback');
});

		$('#supplier_table').on('click', '.activate', function() { 
					var code = $(this).attr('data-id');
					$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&partnercode='+code ,function(data) {
						showList();
					});
				});

		$('#supplier_table').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&partnercode='+id ,function(data) {
					showList();
				});
			});
		});

$('#export_id').prop('download','supplier.csv');

</script>

