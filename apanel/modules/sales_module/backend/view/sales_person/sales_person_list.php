<section class="content">
    <div class="box box-primary">
        
        <div class="box-header">
            <div class="row">

				<div class = "col-md-4">
                    <a href="<?php echo BASE_URL; ?>maintenance/sales_person/create" class = "btn btn-primary danger">Create</a>
					<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete <span></span> </button>
					
					<div class="btn btn-group" id="option_buttons">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
							Options <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href = "#" id="export" download="Sales Person.csv" ><span class="glyphicon glyphicon-open"></span> Export Sales Person</a>
							</li>
							<li>
								<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Sales Person</a>
							</li>
						</ul>
					</div>
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
				<div class="col-sm-3 col-xs-6">
				</div>
				<div class="col-sm-3 col-xs-6">
				</div>
				<div class="col-sm-4 col-xs-6"></div>
				
				<div class="col-sm-1 col-xs-6 text-right">
					<label for="" class="padded">Items: </label>
				</div>

				<div class="col-sm-1 col-xs-6">
					<select id="items">
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</div>
			</div>
        </div>

		<div class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><strong>Error!</strong></h4>
			<div id = "errmsg"></div>
		</div>

       <div class="box-body table table-responsive">
            <table class="table table-hover" id="sales_person_table">
                <thead>
					<?php

						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'text-center col-md-1'
									)
								)
								->addHeader('Sales Person Code',array('class'=>'col-md-2'),'sort','p.partnercode')
								->addHeader('Sales Person Name', array('class'=>'col-md-3'),'sort','p.first_name, p.last_name')
								->addHeader('E-mail Address',array('class'=>'col-md-3'),'sort','p.email')
								->addHeader('Status',array('class'=>'col-md-3'))
								->draw();
					?>
				</thead>
               
             	<tbody id = "list_container">
                </tbody>
            </table>
			<div id = "pagintation"></div>
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
						<h4 class="modal-title">Import Sales Person</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="SalesPerson_Template.csv">here</a></label>
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

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

$( "#search" ).keyup(function() {
	ajax.search = $( this ).val();
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
});

tableSort('#sales_person_table', function(value, getlist) {
  	ajax.sort = value;
  	ajax.page = 1;
	if(getlist){
		showList();
	}
},ajax);

$('#items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

ajaxToFilter(ajax, { search : '#search', limit : '#items'});

function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call = $.post('<?=BASE_URL?>maintenance/sales_person/ajax/sales_person_list',ajax, function(data) {
					$('#sales_person_table #list_container').html(data.table);
					$('#pagination').html(data.pagination);
					$("#export").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
					if (ajax.page > data.page_limit && data.page_limit > 0) {
						ajax.page = data.page_limit;
						showList();
					}
				});
};

showList();

$(document).ready(function() 
{
	//alert('test');
	$( "#sales_person_table" ).on('click' , '.delete', function() 
	{
		//alert('test');
		var id = $( this ).attr("data-id");
		
		if( id != "" )
		{
			$(".delete-modal > .modal").css("display", "inline");
			$(".delete-modal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/sales_person/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == "success" )	
					{
						$(".delete-modal").modal("hide");
						showList();
					}
					else
					{
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
	$.post('<?=BASE_URL?>maintenance/sales_person/ajax/delete', 'id=' + id, function(data) 
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
	linkButtonToTable('#item_multiple_delete', '#sales_person_table');
	linkDeleteToModal('#sales_person_table .delete', 'ajaxCallback');
	linkDeleteMultipleToModal('#item_multiple_delete', '#sales_person_table', 'ajaxCallback');
});

</script>

