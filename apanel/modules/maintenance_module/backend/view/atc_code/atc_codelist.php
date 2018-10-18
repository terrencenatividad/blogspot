<section class="content">
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!</strong></h4>
		<div id = "errmsg"></div>
	</div>
	<!-- Success Message for File Import -->
	<?php
		// $file_import_msg = ($file_import_result) ? "<strong>Success!</strong> CSV file has been uploaded." : "Selected file was not uploaded successfully.";

		// if($file_import_result)
		// {
		// 	echo '<div class="alert alert-success alert-dismissable" id="success_alert">
		// 			<button type="button" class="close" data-dismiss="alert" >&times;</button>';
		// 	echo 	'"'.$file_import_msg.'"';
		// 	echo '</div>';
		// 	header("Refresh:0");
		// }
	?>

	<!-- Error Message for File Import -->
	<?php
		// $errmsg		= array_filter($import_error_messages);
		// $errorcount	= count($errmsg);

		// if($errorcount > 0)
		// {
		// 	echo '<div class="alert alert-warning alert-dismissable">
		// 			<button type="button" class="close" data-dismiss="alert" >&times;</button>';
		// 	echo 	"<strong>The system encountered the following error(s) in processing 
		// 				the file you've imported:</strong><hr/>";
		// 	echo	"<ul>";
		// 	foreach($errmsg as $errmsgIndex => $errmsgVal)
		// 	{
		// 		echo '<li>'.$errmsgVal.'</li>';
		// 	}		
		// 	echo	"</ul>";
		// 	echo '</div>';
		// }
	?>

	<div class="box box-primary">
		<form method = "post">
		<div class="box-header">
			<!--<textarea id="export_json" class="hidden"></textarea>-->
			<div class="row">
				<div class = "col-md-8">
					<!-- <a class="btn btn-primary btn-flat" role="button" 
						href="<?=MODULE_URL?>create" style="outline:none;">Add New ATC Code</a> -->
						<?= 
							$ui->CreateNewButton('');
						?>
					<!-- <form class="navbar-form navbar-left">
						<div class="btn-group" id="option_buttons">
							<button type="button" 
							class="btn btn-primary btn-flat dropdown-toggle" 
								data-toggle="dropdown">
								Options <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="" id="export_csv" download="atccode.csv"><span class="glyphicon glyphicon-export"></span>Export ATC Code</a></li>
								<li><a href="javascript:void(0);" id="import">
								<span class="glyphicon glyphicon-save"></span> Import ATC Code</a></li>
							</ul>
						</div>
					</form> -->
						<?= 
							$ui->OptionButton('');
						?>
					<input id = "deleteMultipleBtn" type = "button" name = "delete" 
						value = "Delete" class="btn btn-danger btn-flat ">
					<input id = "activateMultipleBtn" type = "button" name = "activate" 
					value = "Activate" class="btn btn-success btn-flat ">
					<input id = "deactivateMultipleBtn" type = "button" name = "deactivate" 
					value = "Deactivate" class="btn btn-warning btn-flat ">
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<div class="input-group">
							<input id="table_search" name="table_search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
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
			<!-- <div class="panel-heading" id="option_filter">
				<div class="row">
					<div class="control-label col-md-9 col-sm-9 col-xs-9">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('all');">All Accounts</a></li>
						<li ><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('asset');">Assets</a></li>
						<li ><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('liability');">Liabilities</a></li>
						<li ><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('equity');">Equity</a></li>
						<li ><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('revenue');">Revenue</a></li>
						<li ><a href="#" data-toggle="tab" 
							style="outline:none;" onClick="filterList('expense');">Expenses</a></li>
					</ul>
					</div>
				</div>
			</div>		 -->
			<div class="box-body table table-responsive">
					<table id="tableList" class="table table-striped table-condensed table-bordered table-hover">
						<!--<thead>
							<tr class="info">
								<th class="col-md-1" style="text-align:center;">
									<input type = "checkbox" class = "checkall" name = "select_all" 
									id = "select_all"/>
								</th>
								<th class="col-md-2 center"><a href="javascript:void(0);" 
									onClick="sortList('chart.accountcode');" class="link" 
									id="sort_chart_accountcode" style="outline:none;">Account Code 
									<span class="glyphicon glyphicon-chevron-up"></span></a></th>
								<th class="col-md-5 center"><a href="javascript:void(0);" 
										onClick="sortList('chart.accountname');" class="link" 
										id="sort_chart_accountname" style="outline:none;">Account Name 
										<span class=""></span></a></th>
								<th class="col-md-4 center"><a href="javascript:void(0);" 
								onClick="sortList('acct.accountclass');" class="link" 
								id="sort_acct_accountclass" style="outline:none;">Account Class 
								<span class=""></span></a></th>
							</tr>
						</thead>-->
						<?
							echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall" id="checkkk" value="1">',
									array(
										'class' => 'col-md-1 text-center'
									)
								)
								->addHeader('ATC Code', array('class' => 'col-md-1 text-center'), 
											'sort', 'atcId', 'DESC')
								->addHeader('Tax Rate', array('class' => 'col-md-1 text-center'), 
											'sort', 'tax_rate')
								->addHeader('Tax Code', array('class'=> 'col-md-1 text-center'),
											'sort', 'wtaxcode')
								->addHeader('Description', array('class'=> 'col-md-3 text-center'),
								'sort', 'short_desc')
								->addHeader('EWT', array('class'=> 'col-md-3 text-center'),
								'sort', 'tax_account')
								->addHeader('CWT', array('class'=> 'col-md-3 text-center'),
								'sort', 'cwt')
								->addHeader('Status', array('class'=> 'col-md-2 text-center'),
								'sort', 'status')
								->draw();
						?>		
						<tbody id="list_container">

						</tbody>
					</table>
					<div id="pagination"></div>
				
			</div>
		</div>
		</form>
	</div>
</section>
<!--DELETE RECORD CONFIRMATION MODAL-->
<div class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this ATC Code?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
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

<!--DELETE RECORDS CONFIRMATION MODAL-->
<div class="modal fade" id="multipleDeleteModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete selected atc code(s)?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
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

<!-- Import Modal -->
<div class="import-modal" id="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import ATC Code</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template 
						<a href="<?=BASE_URL?>modules/maintenance_module/backend/view/
											pdf/import_atccode.csv">here</a>
						</label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.
						</label>
						<hr/>
						<div class="form-group field_col">
							<label for="import_csv">
								Step 3. Select the updated file and click 'Import' to proceed.
							</label>
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
						<p class="help-block">The file to be imported must be in CSV 
											  (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id="btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" 
							data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var ajax = {};
	function show_error(msg)
	{
		$(".delete-modal").modal("hide");
		$(".alert-warning").removeClass("hidden");
		$("#errmsg").html(msg);
	}
	function showList() 
	{
		$.post('<?=MODULE_URL?>ajax/load_list', ajax, function(data)
		{
			$('.checkall').iCheck('uncheck');
			$('#list_container').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_id").attr('href', 'data:text/csv;filename=chart_of_accounts.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			
			if (ajax.page > data.page_limit && data.page_limit > 0) 
			{
				ajax.page = data.page_limit;
				showList();
			}
			
		});
	}
	tableSort('#tableList', function(value, x) 
	{
		ajax.sort = value;
		ajax.page = 1;
		if (x) 
		{
			showList();
		}
	});

	$( "#table_search" ).keyup(function() 
	{
		var search = $( this ).val();
		ajax.search = search;
		showList();
	});

	/**IMPORT**/
	$('#import-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_csv').val('').closest('.form-group').
					find('.form-control').html('').closest('.form-group').html();
		$('#import_csv').closest('.form-group').html(form_csv);
	});
	
	$('#importForm').on('change', '#import_csv', function() {
		var filename = $(this).val().split("\\");
		$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
	});

	$(function() {
		showList();

		// $("#selectall").click(function() 
		// {
		// 	$('input:checkbox').not(this).prop('checked', this.checked);
		// });
		/*
		* For Single Delete
		*/
		$(document.body).on("click", ".delete", function() 
		{   
			var id = [];
				id.push($( this ).attr("data-id"));
			
			 if( id != "" )
			 {
			 	$("#deleteModal").modal("show");

				$( "#btnYes" ).click(function() 
				{
						$.post('<?=MODULE_URL?>ajax/delete', 'id=' + id, function(data) 
						{
							if( data.msg == "" )
								window.location.href = "<?=MODULE_URL?>";
							else
							{
								// Call function to display error_get_last
								show_error(data.msg);
							}
						});
				});	
			}
		});

		/*
		* For Delete All
		*/
		$( "#deleteMultipleBtn" ).click(function() 
		{	
			var id = [];

			$('input:checkbox.item_checkbox:checked').each(function()
			{
				id.push($(this).val());
			});
			//alert(id);
			if( id != "" )
			{
			 	$("#multipleDeleteModal").modal("show");

				$( "#multipleDeleteModal #btnYes" ).click(function() 
				{
					$.post('<?=MODULE_URL?>ajax/delete', 'id=' + id, function(data) 
					{
						if( data.msg == "" )
							window.location.href = "<?=MODULE_URL?>";
						else
						{
							// Call function to display error_get_last
							show_error(data.msg);
						}
					});
				});	
			}
		});

$("#deactivateMultipleBtn").click(function() 
	{
	var id = [];

		$('input:checkbox.item_checkbox:checked').each(function()
		{
			id.push($(this).val());
		});
		
		if( id != "" )
		{
			$('#multipleDeactivateModal').modal('show');
			$( "#multipleDeactivateModal #btnDeac" ).click(function() {
			ids 	=	getSelectedIds();
			$.post('<?=MODULE_URL?>ajax/update_multiple_deactivate', "&ids="+ids ,function(data) {
				
				if( data.msg == 'success' )
				{
					$('.checked').iCheck('uncheck');
					showList();
					$('#multipleDeactivateModal').modal('hide');
				} 
			});
		});
		}
	});

$("#activateMultipleBtn").click(function() 
	{
		var id = [];

		$('input:checkbox.item_checkbox:checked').each(function()
		{
			id.push($(this).val());
		});

		if( id != "" )
		{
			$('#multipleActivateModal').modal('show');
			$( "#multipleActivateModal #btnYes" ).click(function() {
			ids 	=	getSelectedIds();
			$.post('<?=MODULE_URL?>ajax/update_multiple_activate', "&ids="+ids ,function(data) {
				if( data.msg == 'success' )
				{
					$('.checked').iCheck('uncheck');
					showList();
					$('#multipleActivateModal').modal('hide');
				} 
			});
		});
		}
	});

	function getSelectedIds(){
		id 	=	[];
		$('.checkbox:checked').each(function(){
			id.push($(this).val());
		});
		return id;
	}
		/*
		* For Import Modal
		*/
		$("#import_id").click(function() 
		{
			$(".import-modal > .modal").css("display", "inline");
			$('.import-modal').modal();
		});


		// $("#importForm #btnImport").click(function() 
		// {
		// 	$("#importForm").submit();
		// });

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
										show_success_msg("Your data has been successfully imported!");										
										$(".alert-warning").addClass("hidden");
										$("#errmsg").html('');
									}else{
										$('#import-modal').modal('hide');
										show_error(response.errmsg);
									}
								},
							});
		});

		// $("#export").click(function() 
		// {
		// 	ajax.addCond 		 = $("#addCond").val();
		// 	ajax.search			 = $("#table_search").val() 

		// 	window.location = '<?//=MODULE_URL?>ajax/export?' + $.param(ajax);
		// });

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			$('.checked').iCheck('uncheck');
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				showList();
			}
		});

		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			showList();
		});

		$('#export_id').prop('download','atccode.csv');
		// $('#export_id').prop('href','');

	});

	function show_success_msg(msg)
	{
		$('#success_modal #message').html(msg);
		$('#success_modal').modal('show');
		setTimeout(function() {												
			window.location = '<?= MODULE_URL ?>';		
		}, 1000)
	}

	$('#tableList').on('click', '.activate', function() { 
			var id = $(this).attr('data-id');
			$.post('<?=MODULE_URL?>ajax/ajax_edit_activate', '&id='+id ,function(data) {
				showList();
			});
		});

		$('#tableList').on('click', '.deactivate', function() { 
			$('#deactivate_modal').modal('show');
			var id = $(this).attr('data-id');
			
			$('#deactivate_modal').on('click', '#deactyes', function() {
				$('#deactivate_modal').modal('hide');
				
				$.post('<?=MODULE_URL?>ajax/ajax_edit_deactivate', '&id='+id ,function(data) {
					showList();
				});
			});
		});

		$('#tableList').on('ifToggled', 'input[type=checkbox]:not(.checkall)', function() {
			var b = $('input[type=checkbox]:not(.checkall)');
			var row = $('#tableList >tbody >tr').length;
			var c =	b.filter(':checked').length;
			if(c == row){
				$('#tableList thead tr th').find('.checkall').prop('checked', true).iCheck('update');
			}else{
				$('#tableList thead tr th').find('.checkall').prop('checked', false).iCheck('update');
			}
		});

</script>