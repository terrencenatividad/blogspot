<section class="content">
    <div class="box box-primary">
        
        <div class="box-header">
            <div class="row">

				<!-- <div class = "col-md-4">
					<a href="<?php echo BASE_URL; ?>maintenance/discount/create" class="btn btn-primary danger">Create</a>
					<div class="btn btn-group" id="option_buttons">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
							Options <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href = "#" id="export" download="Discounts.csv" ><span class="glyphicon glyphicon-open"></span> Export Discount(s)</a>
							</li>
							<li>
								<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Discount(s)</a>
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
				</div>

				<!--<div class = "col-md-1">
					<input id = "deletelistBtn" type = "submit" value = "Delete" class = "btn btn-danger btn-sm btn-flat width100">
				</div>-->

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

       	<div class="box-body table table-responsive" style = "overflow-x: inherit;">
            <table id = "discount_table" class="table table-hover">
                <thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('',array('class' => 'col-md-1 text-center'))
								->addHeader('Discount Code',array('class'=>'col-md-3'),'sort','discountcode')
								->addHeader('Discount Name', array('class'=>'col-md-3'),'sort','discountname')
								->addHeader('Description',array('class'=>'col-md-3'),'sort','discountdesc')
								->addHeader('Status',array('class'=>'col-md-3'),'sort','stat')
								->draw();
					?>
				</thead>
               
                <form method = "post">
                    <tbody id = "list_container">
                    </tbody>
                </form>
            </table>
			<div id="pagination"></div>
        </div>

	</div>
</div>

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
				<p>Are you sure you want to cancel this record?</p>
				</div>
				<div class="modal-footer text-center">
					<button type="button" class="btn btn-outline btn-flat" id = "delete-yes">Yes</button>
					<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
				</div>
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
						<h4 class="modal-title">Import Discount</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" download="Discount_Template.csv">here</a></label>
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
					<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import_customers" class="download_button" download="Discount - Customers.csv">here</a></label>
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
	showList();
}

tableSort('#discount_table', function(value, getlist) {
  	ajax.sort = value;
 	ajax.page = 1;
	if(getlist){
	  	showList();
	}
}, ajax);

$('#items').on('change', function() {
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

$( "#search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

ajaxToFilter(ajax, { search : '#search', limit : '#items'});

function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call = $.post('<?=BASE_URL?>maintenance/discount/ajax/discount_list',ajax, function(data) {
					$('#discount_table #list_container').html(data.table);
					$('#pagination').html(data.pagination);
					$("#export_id").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
					if (ajax.page > data.page_limit && data.page_limit > 0) {
						ajax.page = data.page_limit;
						showList();
					}
				});
};

showList();

$(document).ready(function() 
{
	$( "#discount_table" ).on('click' , '.delete', function() 
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
				$.post('<?=BASE_URL?>maintenance/discount/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.success )	
					{
						window.location.href = "<?=BASE_URL?>maintenance/discount";
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

	/** -- FOR TAGGING AS COMPLETE -- **/
	$('#discount_table').on('click','.tag_customers',function(){
		var code	=	$(this).attr('data-id');
		window.location = '<?=MODULE_URL?>tag_customers/'+code;
	});
	/** -- FOR TAGGING AS COMPLETE -- end **/

	/** For Import Modal **/
	$("#import_id").click(function() 
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
									show_success_msg("Your data has been successfully imported!");
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

	/** For Import Modal **/

	$("#importCustForm #btnImport").click(function() 
	{
		var formData =	new FormData();
		formData.append('file',$('#import_cust_csv')[0].files[0]);
		formData.append('discountcode',$('#hidden_id').val());
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

	$('#discount_table').on('click','.import_customers',function(){
		var code	=	$(this).attr('data-id');
		$('#hidden_id').val(code);
		$('.download_button').attr('download','Discount [ '+code+' ] - Customers.csv');
		$('#import-tagcust-modal').modal('show');
	});
	
	$('#discount_table').on('click','.import_customers',function(){
		$(".import-modal > .modal").css("display", "inline");
		$('.import-modal').modal();
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


		$('#discount_table').on('click', '.activate', function() { 
			var id = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+id ,function(data) {
				showList();
			});
		});

		$('#discount_table').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+id ,function(data) {
					showList();
				});
			});
		});

$('#export_id').prop('download','discount.csv');

</script>
