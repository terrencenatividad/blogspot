<section class="content">
	<div class="box">
		<div class="nav-tabs-custom">
	
		<ul id="filter_tabs" class="nav nav-tabs">
			<li class="active"><a href="#sales" data-toggle="tab">Selling Price</a></li>
			<li><a href="#purchase" data-toggle="tab">Purchase Price</a></li>
		</ul>

		<div class="tab-content">	
			<div id = "sales" class="tab-pane active">
				<form method = "post">
					<div class="box-header">
						<div class="row">
							<div class = "col-md-8">
								<a class="btn btn-primary" role="button" href="<?=MODULE_URL?>listing" style="outline:none;">Price List Template</a>
								<form class="navbar-form navbar-left">
									<div class="btn-group" id="option_buttons">
										<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
											Options <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li><a href="javascript:void(0);" download="export_masterlist.csv" id="export"><span class="glyphicon glyphicon-open"></span> Export Master List</a></li>
											<li><a href="javascript:void(0);" id="import"><span class="glyphicon glyphicon-save"></span> Import Master List</a></li>
										</ul>
									</div>
								</form>
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
											<select id="sales_items">
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
								<table id="masterlist_table" class="table table-striped table-condensed table-bordered">
									<thead>
										<?php
											echo $ui->loadElement('table')
													->setHeaderClass('info')
													->addHeader('Item Code',array('class'=>'col-md-3'),'sort','i.itemcode')
													->addHeader('Item Name', array('class'=>'col-md-3'),'sort','i.itemname')
													->addHeader('Selling Price',array('class'=>'col-md-3'),'sort','ip.itemprice')
													->addHeader('Selling UOM',array('class'=>'col-md-3'),'sort','u.uomdesc')
													->addHeader('',array('class'=>'col-md-1'))
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

			<div id = "purchase" class="tab-pane">
				<form method = "post">
					<div class="box-header">
						<div class="row">
							<div class = "col-md-8"></div>
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
							<div class="col-sm-10"></div>
							
							<div class="col-sm-1 col-xs- text-right">
								<label for="" class="padded">Items: </label>
							</div>

							<div class="col-sm-1 col-xs-6">
								<select id="purchase_items">
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
							</div>
						</div>
					</div>

					<div class="table-responsive" id="option_result" style="overflow-x: inherit;">
						<table id="purchaselist_table" class="table table-striped table-condensed table-bordered">
							<thead>
								<?php
									echo $ui->loadElement('table')
											->setHeaderClass('info')
											->addHeader('Item Code',array('class'=>'col-md-3'),'sort','i.itemcode')
											->addHeader('Item Name', array('class'=>'col-md-3'),'sort','i.itemname')
											->addHeader('Purchase Price',array('class'=>'col-md-3'),'sort','pa.purchase')
											->addHeader('Purchase UOM',array('class'=>'col-md-3'),'sort','u.uomdesc')
											->draw();
								?>
							</thead>
							<tbody id="list_container"></tbody>
						</table>
					</div>
					<div id="pagination"></div>
				</form>
			</div>
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
						<h4 class="modal-title">Import Price Master List</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import_masterlist" download="Master List_Template.csv">here</a></label>
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
	ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');

var ajax2 = filterFromURL();
	ajax2.filter = ajax2.filter || $('#filter_tabs .active a').attr('href');

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

$('#sales #table_search').on('input', function () {
	ajax.page = 1;
	ajax2.page = 1;
	ajax.search = $(this).val();
	ajax2.search = $(this).val();
	showList();
});

$('#purchase #table_search').on('input', function () {
	ajax.page = 1;
	ajax2.page = 1;
	ajax.search = $(this).val();
	ajax2.search = $(this).val();
	showList();
});

$('#sales_items').on('change', function(){
	ajax.page = 1;
	ajax.limit = $(this).val();
	showList();
});

$('#purchase_items').on('change', function(){
	ajax2.page = 1;
	ajax2.limit = $(this).val();
	showList();
});

ajaxToFilter(ajax,{ search: '#sales #table_search', limit: '#items'})
ajaxToFilterTab(ajax, '#filter_tabs','filter');

ajaxToFilter(ajax2,{ search: '#purchase #table_search', limit: '#items'})
ajaxToFilterTab(ajax2, '#filter_tabs','filter');


function showList(pg){
	filterToURL();
	if (ajax_call != '') {
		ajax_call.abort();
	}
	ajax_call 	=	$.post('<?=BASE_URL?>maintenance/pricelist/ajax/master_list',ajax, function(data) {
						$('#masterlist_table #list_container').html(data.table);
						$('#sales #pagination').html(data.pagination);
						$("#export").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
						if (ajax.page > data.page_limit && data.page_limit > 0) {
							ajax.page = data.page_limit;
							showList();
						}
					});
	
	$.post('<?=BASE_URL?>maintenance/pricelist/ajax/master_list_purchases',ajax2, function(data) {
		$('#purchaselist_table #list_container').html(data.table);
		$('#purchase #pagination').html(data.pagination);
		if (ajax2.page > data.page_limit && data.page_limit > 0 ) {
			ajax2.page = data.page_limit;
			showList();
		}
	});
};

showList();

$(document).ready(function() 
{
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
                            url : '<?=MODULE_URL?>ajax/save_import_masterlist',
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
	
    $('body').on('click', '.btn-primary.edit_row', function() {
        $(this).closest('tr').find('.form-control').attr('disabled', false);
        $(this).removeClass('btn-primary').addClass('btn-success').find('i').removeClass('glyphicon-pencil').addClass('glyphicon-floppy-disk');
    });

    $('body').on('click', '.btn-success.edit_row', function() {
    var x = {};
        x.id = $(this).closest('tr').find('.id').html();
    $(this).closest('tr').find('.form-control').each(function() {
        var id = $(this).attr('name');
        var val = $(this).val();
        if (val.replace(/\,\s/g,'') == '') {
        $(this).closest('td').addClass('has-error');
        } else {
        $(this).closest('td').removeClass('has-error');
        }
        x.itemcode  =   id;
        x.price     =   val;
    });
    if ($(this).closest('tr').find('td.has-error').length == 0) {
        $(this).closest('tr').find('.form-control').attr('disabled', true);
        $(this).removeClass('btn-success').addClass('btn-primary').find('i').removeClass('glyphicon-floppy-disk').addClass('glyphicon-pencil');
        $.post('<?=MODULE_URL?>ajax/save_sp', x, function(response) {
            if(response.msg == 'success'){
				show_success_msg('The price has been saved successfully.');
				// window.location = '<?//=BASE_URL?>maintenance/pricelist/master';
            }
        });
    }
    });

	$('#sales #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		showList();
	});

	$('#purchase #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax2.page = $(this).attr('data-page');
		showList();
	});

	$('.nav-tabs a').click(function(){
		$(this).tab('show');
	})

});

// Sorting Script
tableSort('#masterlist_table', function(value, getlist) {
	ajax.sort = value;
	ajax.page = 1;
	if (getlist) {
		showList();
	}
},ajax);

// Sorting Script
tableSort('#purchaselist_table', function(value, getlist) {
	ajax.sort = value;
	ajax.page = 1;
	if (getlist) {
		showList();
	}
},ajax);

</script>
