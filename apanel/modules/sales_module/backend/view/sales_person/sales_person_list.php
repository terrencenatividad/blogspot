<section class="content">
    <div class="box box-primary">
        
        <div class="box-header">
            <div class="row">

				<div class = "col-md-4">
                    <a href="<?php echo BASE_URL; ?>maintenance/sales_person/create" class = "btn btn-primary danger">Create</a>
					<!-- <button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Delete <span></span> </button> -->
					
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
									'',
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

<div class="delete-modal">
	<div class="modal modal-danger">
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
					<button type="button" class="btn btn-outline btn-flat" id = "delete-yes">Yes</button>
					<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="transferSPModal" tabindex="-1" data-backdrop="static" data-keyboard='false'>
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Transfer Customers
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="transferForm" autocomplete="off">
					<div class = "well well-md">
						<div class="row row-dense remove-margin">
							<?php
								echo $ui->formField('text')
										->setLabel('Current <br> Sales Person:')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('curr_sp')
										->setId('curr_sp')
										->setValue("")
										->addHidden(true)
										->draw($show_input);

								//echo '<input type="hidden" id="curr_sp_code" name="curr_sp_code" value="">';
							?>
							<?php
								echo $ui->formField('dropdown')
										->setLabel('Transfer to: <span class="asterisk"> * </span>')
										->setSplit('col-md-3', 'col-md-8 field_col')
										->setName('transferto')
										->setId('transferto')
										->setValidation('required')
										->setValue("")
										->draw($show_input);
							?>
						</div>

						<div class="row row-dense">
						</div>

						<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-info btn-flat" id="btnTransfer">Transfer</button>
									</div>
										&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="box-body table table-responsive hidden">
						<table id = "current_tagged_customers" class="table table-hover">
							<thead>
								<tr class = "info">
									<th class = "col-md-1" style="text-align:center;">
										<input type="checkbox" class="checkall">
									</th>
									<th class = "col-md-2">Customer Code</th>
									<th class = "col-md-3">Customer Name</th>
								</tr>
							</thead>
						
							<form method = "post">
								<tbody id = "list_container">
								</tbody>
							</form>

						</table>
						<div id="pagination"></div>
					</div>   
				</form>
			</div>
		</div>
	</div>
</div>

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

<!-- Import Customers Modal -->
<div class="import-modal" id="import-tagcust-modal" tabindex="-1" data-backdrop="static">>
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importCustForm" ENCTYPE="multipart/form-data">
					<input id="hidden_id" value="" class="hidden">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Customers</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import_customers" class="download_button" download="Sales Person - Customers.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group">
							<label for="import_cust_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<?php
								echo $ui->setElement('file')
										->setId('import_cust_csv')
										->setName('import_cust_csv')
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

function show_success_msg(msg)
{
	$('#success_modal #message').html(msg);
	$('#success_modal').modal('show');
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
	// $( "#sales_person_table" ).on('click' , '.delete', function() 
	// {
	// 	//alert('test');
	// 	var id = $( this ).attr("data-id");
		
	// 	if( id != "" )
	// 	{
	// 		$(".delete-modal > .modal").css("display", "inline");
	// 		$(".delete-modal").modal("show");

	// 		$( "#delete-yes" ).click(function() 
	// 		{
	// 			$.post('<?=BASE_URL?>maintenance/sales_person/ajax/delete', 'id=' + id, function(data) 
	// 			{
	// 				if( data.msg == "success" )	
	// 				{
	// 					$(".delete-modal").modal("hide");
	// 					showList();
	// 				}
	// 				else
	// 				{
	// 					// show_error(data.msg);
	// 					$('#warning_modal #warning_message').html(data.msg);
	// 					$('#warning_modal #warning_message').append('<p>Customer Code: ' + id + '</p>');
	// 					$('#warning_modal').modal('show');
	// 				}	
	// 			});
	// 		});	
	// 	}

	// });

	$('#sales_person_table').on('click','.tag_customers',function(){

		var code 	=	$(this).attr('data-id');

		window.location = '<?php echo BASE_URL.'maintenance/sales_person/tag_customers/'; ?>'+code
	});

	$('#sales_person_table').on('click','.transfer_customers',function(){

		var code 	=	$(this).attr('data-id');
		//curr_sp

		$.post('<?=BASE_URL?>maintenance/sales_person/ajax/retrieve_currsp_details', 'code=' + code, function(data) 
		{
			//get current sp code
			$('#curr_sp').val(data.code);
			//get current sp name 
			$('#curr_sp_static').text(data.curr_name);
			//get dropwodn list
			$('#transferto').html(data.options);
			//Table Content
			//$('#current_tagged_customers #list_container').html(data.table);
			//Pagination
			$('#pagination').html(data.pagination);
		});

		$('#transferSPModal').modal('show');
	});

	$('#transferForm').on('click','#btnTransfer',function(){
		$.post('<?=BASE_URL?>maintenance/sales_person/ajax/apply_transfer', $('#transferForm').serialize(), function(data) 
		{
			if( data.msg == 'success' )
			{
				$('#transferSPModal').modal('hide');
			}
		});
	});

		/** For Import Modal **/
	$("#import").click(function() 
	{
		$("#import-modal > .modal").css("display", "inline");
		$('#import-modal').modal();
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

	// Import Customers

	$("#importCustForm #btnImport").click(function() 
	{
		var formData =	new FormData();
		formData.append('file',$('#import_cust_csv')[0].files[0]);
		formData.append('partnercode',$('#hidden_id').val());
		ajax_call 	=	$.ajax({
							url : '<?=MODULE_URL?>ajax/save_import_customers',
							data:	formData,
							cache: 	false,
							processData: false, 
							contentType: false,
							type: 	'POST',
							success: function(response){
								if(response && response.errmsg == ""){
									$('#import-tagcust-modal').modal('hide');
									$(".alert-warning").addClass("hidden");
									$("#errmsg").html('');
									show_success_msg("Your Data has been succesfully imported!");
								}else{
									$('#import-tagcust-modal').modal('hide');
									show_error(response.errmsg);
								}
							},
						});
	});
	
	$('#importCustForm').on('change', '#import_cust_csv', function() {
		var filename = $(this).val().split("\\");
		$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
	});

    $('#import-tagcust-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_cust_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
		$('#import_cust_csv').closest('.form-group').html(form_csv);
	});

	$('#sales_person_table').on('click','.import_customers',function(){
		var code	=	$(this).attr('data-id');
		$('#hidden_id').val(code);
		$('.download_button').attr('download','Sales Person [ '+code+' ] - Customers.csv');
		$('#import-tagcust-modal').modal('show');
	});
	
	$('#sales_person_table').on('click','.import_customers',function(){
		$("#import-tagcust-modal > .modal").css("display", "inline");
		$('#import-tagcust-modal').modal();
	});

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		showList();
	});
	
	$('#success_modal .btn-success').on('click', function(){
		$('#success_modal').modal('hide');
		$('#import-tagcust-modal').modal('hide');
		showList();
	});
});

function ajaxCallback(id) {
	var ids = getDeleteId(id);
	$.post('<?=BASE_URL?>maintenance/sales_person/ajax/delete', 'id=' + id, function(data) 
	{
		if( data.msg == "success" )	
		{
			$(".delete-modal").modal("hide");
			showList();
		}
		else
		{
			$('#warning_modal #warning_message').html(data.msg);
			$('#warning_modal #warning_message').append('<p>Sales Person Code: ' + id + '</p>');
			$('#warning_modal').modal('show');
		}	
	});
}

$(function() {
	linkButtonToTable('#item_multiple_delete', '#sales_person_table');
	linkDeleteToModal('#sales_person_table .delete', 'ajaxCallback');
	linkDeleteMultipleToModal('#item_multiple_delete', '#sales_person_table', 'ajaxCallback');
});

</script>

