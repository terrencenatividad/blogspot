<style>
	.nopadding{
		padding:0;
	}
</style>
<section class="content">
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
					<div class = "col-md-8">
						<?
							echo $ui->CreateNewButton('');
						?>
						<button type="button" id="item_multiple_cancel" class="btn btn-danger btn-flat">Cancel<span></span></button>
						<!--<a href="" id="export_csv" download="Purchase Order.csv" class="btn btn-info btn-flat"><span class="glyphicon glyphicon-export"></span> CSV</a>-->
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
					<div class="col-sm-3 col-xs-6">
						<?php
							echo $ui->formField('text')
									->setName('daterangefilter')
									->setId('daterangefilter')
									->setAttribute(array('data-daterangefilter' => 'month'))
									->setAddon('calendar')
									->setValue("")
									->setValidation('required')
									->draw(true);
						?>
					</div>
					<div class="col-sm-3 col-xs-6">
						<?php
							echo $ui->formField('dropdown')
									->setPlaceholder('Filter Supplier')
									->setName('vendor')
									->setId('vendor')
									->setList($vendor_list)
									->setNone('Filter: All')
									->draw();
						?>
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

		</form>
    </div>

	<div class="nav-tabs-custom">	

		<ul id="filter_tabs" class="nav nav-tabs">
			<li class="active"><a href="all" data-toggle="tab">All</a></li>
			<li><a href="open" data-toggle="tab">Pending</a></li>
			<li><a href="partial" data-toggle="tab">Partial</a></li>
			<li><a href="posted" data-toggle="tab">Completed</a></li>
			<li><a href="cancelled" data-toggle="tab">Cancelled</a></li>
		</ul>

		<table id = "po_table" class="table table-hover">
			<thead>
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('<input type="checkbox" class="checkall">',
										array(
											'class' => 'text-center col-md-1',
											'style' => 'width:100px'
											)
										)
							->addHeader('Date',array('class'=>'col-md-2'),'sort','po.transactiondate')
							->addHeader('PO No.', array('class'=>'col-md-1'),'sort','po.voucherno')
							->addHeader('Reference No.',array('class'=>'col-md-1'),'sort','po.referenceno')
							->addHeader('Supplier',array('class'=>'col-md-2'),'sort','p.partnername')
							->addHeader('Amount',array('class'=>'col-md-2'),'sort','po.netamount')
							->addHeader('Balance',array('class'=>'col-md-2'),'sort','(po.netamount - (pr.netamount + pr.discountamount + pr.wtaxamount))')
							->addHeader('Status',array('class'=>'col-md-1'))
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

</section>

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
							<span class="help-block hidden small"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="submit" class="btn btn-info btn-flat" id = "btnImport">Import</button>
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

	/** -- FOR SEARCH -- **/	
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			showList();
		});
	/** -- FOR SEARCH -- end **/

	/** -- FOR ITEM DISPLAY -- **/
		$('#items').on('change', function() {
			ajax.page = 1;
			ajax.limit = $(this).val();
			showList();
		});
	/** -- FOR ITEM DISPLAY -- end **/

	/** -- FOR PAGINATION **/
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			showList();
		});
	/** -- FOR PAGINATION -- end **/

	/** -- FOR VENDOR **/
		$('#vendor').on('change', function(e) 
		{
			ajax.vendor = $(this).val();
			ajax.page 	= 1;
			showList();
		});
	/** -- FOR VENDOR -- end **/

	/** -- FOR TAB FILTERS -- **/	
		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			showList();
		});
	/** -- FOR TAB FILTERS -- end **/	
	
	/** -- FOR SORTING -- **/
		tableSort('#po_table', function(value,getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				showList();
			}
		},ajax);
	/** -- FOR SORTING -- end **/

	/** -- FOR DATE -- **/
		$('#daterangefilter').on('change', function() 
		{
			ajax.daterangefilter = $(this).val();
			ajax.page 	= 1;
			showList();
		});
	/** -- FOR DATE -- end **/

	/** -- FOR DISPLAY -- end **/	
		function showList(){
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call  	=	$.post('<?=BASE_URL?>purchase/purchase_order/ajax/po_listing',ajax, function(data) {
								$('#po_table #list_container').html(data.table);
								$('#pagination').html(data.pagination);
								$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
								if (ajax.page > data.page_limit && data.page_limit > 0) {
									ajax.page = data.page_limit;
									showList();
								}
							});
		};

		showList();
		
		function show_error(msg){
			$(".delete-modal").modal("hide");
			$(".alert-warning").removeClass("hidden");
			$("#errmsg").html(msg);
		}
	/** -- FOR DISPLAY -- end **/	

	/** -- FOR DELETING DATA -- **/

		function ajaxCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/delete_po', ids, function(data) {
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
	
		$(function() {
			linkCancelToModal('#po_table .delete', 'ajaxCallback');
			linkButtonToTable('#item_multiple_cancel', '#po_table');
			linkCancelMultipleToModal('#item_multiple_cancel', '#po_table', 'ajaxCallback');
		});
	/** -- FOR DELETING DATA -- end **/

	/** -- FOR IMPORTING DATA -- **/
		$("#import").click(function() 
		{
			$(".import-modal > .modal").css("display", "inline");
			$('.import-modal').modal();
		});
	/** -- FOR IMPORTING DATA -- end **/

	/** -- FOR PRINTING -- **/
		$('#po_table').on('click','.print_preview',function(){
			var voucher	=	$(this).attr('data-id');
			window.location = '<?=MODULE_URL?>print_preview/'+voucher;
		});
	/** -- FOR PRINTING -- end **/

	/** -- FOR TAGGING AS COMPLETE -- **/
		$('#po_table').on('click','.tag_as_complete',function(){
			var voucher	=	$(this).attr('data-id');
			window.location = '<?=MODULE_URL?>tag_as_complete/'+voucher;
		});
	/** -- FOR TAGGING AS COMPLETE -- end **/

</script>