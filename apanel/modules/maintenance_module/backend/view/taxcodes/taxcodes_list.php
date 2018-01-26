<section class="content">
	
	<!-- Success Message for File Import -->
	<?php
		$file_import_msg = ($file_import_result) ? "<strong>Success!</strong> CSV file has been uploaded." : "Selected file was not uploaded successfully.";

		if($file_import_result)
		{
			echo '<div class="alert alert-success alert-dismissable" id="success_alert">
					<button type="button" class="close" data-dismiss="alert" >&times;</button>';
			// echo 	'<strong>Success!</strong> CSV file has been uploaded.';
			echo 	'"'.$file_import_msg.'"';
			echo '</div>';
		}
	?>

	<!-- Error Message for File Import -->
	<?php
		$errmsg		= array_filter($import_error_messages);
		$errorcount	= count($errmsg);

		if($errorcount > 0)
		{
			echo '<div class="alert alert-warning alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" >&times;</button>';
			echo 	"<strong>The system encountered the following error(s) in processing the file you've imported:</strong><hr/>";
			echo	"<ul>";
			foreach($errmsg as $errmsgIndex => $errmsgVal)
			{
				echo '<li>'.$errmsgVal.'</li>';
			}		
			echo	"</ul>";
			echo '</div>';
		}
	?>

	<!-- Error Message -->
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!</strong></h4>
		<div id = "errmsg"></div>
	</div>

    <div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">

					<div class = "col-md-1">
						<form class="navbar-form navbar-left">
							<div class="btn-group" id="option_buttons">
								<button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
									Options <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li>
										<a href="<?= BASE_URL ?>maintenance/taxcodes/create"><span class="glyphicon glyphicon-plus"></span> Add New Tax</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href = "#" id="export"><span class="glyphicon glyphicon-open"></span> Export Taxes</a>
									</li>
									<li>
										<a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Taxes</a>
									</li>
								</ul>
							</div>
						</form>
					</div>

					<div class = "col-md-2 pull-left">
						<input id = "deletelistBtn" type = "button" name = "delete" value = "Delete" class="btn btn-danger btn-sm btn-flat width100">
					</div>

					<div class="col-md-4 pull-right">
						<div class="input-group input-group-sm">
							<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
							</div>
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

			</div>
			<div class="box-body table-responsive" style = "overflow-x: inherit;">
				<table id="tableList" class="table table-hover">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'col-md-1 text-center'
									)
								)
								->addHeader('Tax Code', array('class' => 'col-md-3 text-center'), 'sort', 'tax.fstaxcode', 'asc')
								->addHeader('Tax Description', array('class' => 'col-md-3 text-center'), 'sort', 'tax.longname')
								->addHeader('Tax Type', array('class' => 'col-md-3 text-center'), 'sort', 'code.value')
								->addHeader('Tax Rate', array('class' => 'col-md-3 text-center'), 'sort', 'tax.taxrate')
								->draw();
					?>

					<tbody id = "list_container">
					</tbody>

					<tfoot>
						<tr>
							<td colspan="5" class="text-center" id="page_links"></td>
						</tr>
					</tfoot>

				</table>
			</div>
		</form>
    </div>
    
</section>

<!-- Delete Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="delete-yes">Yes</button>
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
<div class="import-modal">
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Taxes</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=BASE_URL?>modules/maintenance_module/backend/view/pdf/import_taxes.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group field_col">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<input class = "form_iput" value = "" name = "import_csv" id = "import_csv" type = "file">
							<span class="help-block hidden small" id = "import_csv_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
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
var ajax = {};

tableSort('#tableList', function(value, x) 
{
	ajax.sort = value;
	ajax.page = 1;
	if (x) 
	{
		showList();
	}
});

function show_error(msg)
{
	$(".delete-modal").modal("hide");
	$(".alert-warning").removeClass("hidden");
	$("#errmsg").html(msg);
}

function showList(pg) 
{
	$.post('<?=BASE_URL?>maintenance/taxcodes/ajax/load_list', ajax, function(data)
	{
		$('#list_container').html(data.list);
		$('#page_links').html(data.pagination);
	});
}

/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
	var field	= $("#"+form+" #"+id).val();

	if(id.indexOf('_chosen') != -1)
	{
		var id2	= id.replace("_chosen","");
		field	= $("#"+form+" #"+id2).val();
	}

	if((field == '' || parseFloat(field) == 0))
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');
		
		$("#"+form+" #"+help_block)
			.removeClass('hidden');

		return 1;
	}
	else
	{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+help_block)
			.addClass('hidden');

		return 0;
	}
}

$( "#search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});


$('#items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

$(document).ready(function() 
{
	// Load data
	showList();

	/*
	* For Single Delete
	*/
	$(document.body).on("click", ".delete", function() 
	{
		var id = [];
			id.push($( this ).attr("data-id"));

		if( id != "" )
		{
			// $(".delete-modal > .modal").css("display", "inline");
			$("#deleteItemModal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/taxcodes/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == "" )
						window.location.href = "<?=BASE_URL?>maintenance/taxcodes";
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
	$( "#deletelistBtn" ).click(function() 
	{	
		var id = [];

		$('input:checkbox.item_checkbox:checked').each(function()
		{
			id.push($(this).val());
		});

		if( id != "" )
		{
			$("#deleteItemModal").modal("show");

			$( "#delete-yes" ).click(function() 
			{
				$.post('<?=BASE_URL?>maintenance/taxcodes/ajax/delete', 'id=' + id, function(data) 
				{
					if( data.msg == "" )
						window.location.href = "<?=BASE_URL?>maintenance/taxcodes";
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
	* For Import Modal
	*/
	$("#import").click(function() 
	{
		$(".import-modal > .modal").css("display", "inline");
		$('.import-modal').modal();
	});

	$("#importForm #btnImport").click(function() 
	{
		var valid	= 0;
		
		valid	+= validateField('importForm','import_csv', "import_csv_help");

		if(valid == 0)
		{
			$("#importForm").submit();
		}
	});


	/*
	* For Export
	*/
	$("#export").click(function() 
	{
		window.location = '<?=BASE_URL?>maintenance/taxcodes/ajax/export';
	});

	// For Pagination
	$('#page_links').on('click', 'a', function(e) 
	{
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		showList();
	});

});

</script>