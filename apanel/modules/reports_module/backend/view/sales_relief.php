<section class="content">
	<div class="box box-primary">
		<div class="box-header pb-none">
			<div class="row">
				<div class="col-md-3">
					<?php
						echo $ui->formField('text')
								->setName('daterangefilter')
								->setId('daterangefilter')
								->setAttribute(array('data-daterangefilter' => 'month'))
								->setAddon('calendar')
								->setValue($datefilter)
								->setValidation('required')
								->draw(true);
					?>
				</div>
				<div class = "col-md-3">
					<?php 
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Customer')
							->setName('customer')
							->setId('customer')
							->setNone('All')
							->setList($customer_list)
							->draw(true);
					?>
				</div>
				<div class ="col-md-4"></div>
				<div class="col-md-2">
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
			<div class = "row">
				<div style = "float:right; margin-right:15px">
					<a href="" id="downloadcsv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> CSV</a>
					<a href="" id="downloadDat" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> DAT</a>
				</div>
			</div>
			<div class = "row">
				<div class = "col-md-12">
					<h4><b>SUMMARY LIST OF SALES</b></h4>
				</div>
				<div class = "col-md-12">
					<h5><b>SALES TRANSACTION</b></h5>
					<h5><b>RECONCILIATION OF LISTING FOR ENFORCEMENT</b></h5>
				</div>
				<div class = "col-md-12">
					<h5><b>TIN : <span id="tin"><?=$companytin?></span></b></h5>
					<h5><b>OWNER'S NAME : <span id="ownername"><?=$companyname?></span></b></h5>
					<h5><b>OWNER'S TRADE NAME : <span id="tradename"><?=$companyname?></span></b></h5>
					<h5><b>OWNER'S ADDRESS : <span id="owneraddress"><?=$companyaddress?></span></b></h5>
				</div>
			</div>	
		</div>			
		<div class="nav-tabs-custom">
			<table id="tableList" class="table table-hover table-sidepad">
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('Taxable Month', array('class' => 'col-md-1'), 'sort', 'inv.transactiondate', ' asc')
							->addHeader('TIN', array('class' => 'col-md-1'), 'sort', 'p.tinno', ' asc')
							->addHeader('Customer', array('class' => 'col-md-2'), 'sort', 'p.partnername', ' asc')
							->addHeader('Gross Sales', array('class' => 'col-md-1'),'sort','inv.netamount',' asc')
							->addHeader('Exempt Sales', array('class' => 'col-md-1'),'sort','inv.vat_exempt',' asc')
							->addHeader('Zero Rated Sales', array('class' => 'col-md-1'),'sort','inv.vat_zerorated',' asc')
							->addHeader('Taxable Sales', array('class' => 'col-md-1'),'sort','inv.vat_sales',' sc')
							->addHeader('Output Tax', array('class' => 'col-md-1'),'sort','inv.taxamount',' asc')
							->addHeader('Gross Taxable Sales', array('class' => 'col-md-1'),'sort','inv.amount',' asc')
							->draw();
				?>
				<tbody></tbody>
				<tfoot></tfoot>
			</table>
		</div>
	</div>
	<div id="pagination"></div>
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
<script>
	var ajax = {}
	var ajax_call = '';
	var ajax = filterFromURL();
	ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
	// ajaxToFilter(ajax, { limit : '#items', datepicker : '#daterangefilter' });
	ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
	tableSort('#tableList', function(value, getlist) {
		ajax.sort = value;
		ajax.page = 1;
		if (getlist) {
			getList();
		}
	}, ajax);
	$('#items').on('change', function() {
		ajax.limit = $(this).val();
		ajax.page = 1;
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});
	$('#items').on('change', function(e) {
		e.preventDefault();
		ajax.limit = $(this).val();
		getList();
	});
	$('#customer').on('change', function(e) {
		e.preventDefault();
		ajax.customer = $(this).val();
		getList();
	});
	function getList() {
		filterToURL();
		if (ajax_call != '') {
			ajax_call.abort();
		}
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList tbody').html(data.table);
			if (data.result_count == 0) {
				data.tabledetails = '';
			}
			$('#tableList tfoot').html(data.tabledetails);
			$('#pagination').html(data.pagination);
			if (ajax.page > data.page_limit && data.page_limit > 0) {
				ajax.page = data.page_limit;
				getList();
			}
		});
	}
	// getList();
	$('#daterangefilter').on('change', function() {
		ajax.datefilter = $(this).val();
		ajax.page = 1;
		getList();
	}).trigger('change');
	
	$('#downloadcsv').click(function(){
		var datepicker 	= $('#daterangefilter').val();
		var sort 		= ajax.sort
		var customer 	= $('#customer').val();
		window.open('<?php echo MODULE_URL ?>get_csv?datefilter=' + encodeURIComponent(datepicker) + '&sort=' + encodeURIComponent(sort) + '&customer=' + encodeURIComponent(customer));
	});
	$('#downloadDat').click(function(){
		var datepicker = $('#daterangefilter').val();
		var sort 		= ajax.sort
		var customer 	= $('#customer').val();
		window.open('<?php echo MODULE_URL ?>get_dat?datefilter=' + encodeURIComponent(datepicker) + '&sort=' + encodeURIComponent(sort) + '&customer=' + encodeURIComponent(customer));
	});
</script>