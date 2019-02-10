<section class="content">
<div class="box box-primary">
	<div class="box-header pb-none">
		<div class="row">
		<div class="col-md-4">
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
			<div class="col-md-4">
				<?php
						echo $ui->formField('dropdown')
								->setPlaceholder('Filter Customer')
								->setName('customer')
								->setId('customer')
								->setNone('All')
								->setList($customer_list)
								->draw(true);	
				?>
			</div>
			<div class="col-md-4 text-right">
				<a href="" id="export_csv" download="PartsandServiceReport.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
			</div>
		</div>	
	</div>
	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-sidepad">
				<thead>
				<?php
				echo $ui->loadElement('table')
						->setHeaderClass('info')
						->addHeader('Customer',array('class'=>'col-md-2'),'sort','transactiontype')
						->addHeader('Transaction Date',array('class'=>'col-md-1'),'sort','amount')
						->addHeader("Service Quotation No.",array('class'=>'col-md-2'),'sort','assetclass')
						->addHeader("Customer Purchase Order No.",array('class'=>'col-md-2'),'sort','asset_number')
						->addHeader('Sales Invoice No.',array('class'=>'col-md-2'),'sort','sub_number')
						->addHeader('Parts Total Amount',array('class'=>'col-md-1'),'sort','serial_number')
						->addHeader('Service Total Amount',array('class'=>'col-md-1'),'sort','transactiondate')
						->addHeader('Total Sales',array('class'=>'col-md-1'),'sort','amount')
						->draw();
				?>
			</thead>
			<tbody>
				
			</tbody>
			<tfoot>
			
			</tfoot>
		</table>
	</div>
</div>
<div id="pagination"></div>	

<div id="asd" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Transactions</h4>
		</div>
		<div class="modal-body" id="badeh">
			
		</div>
	
	</div>
</div>
</div>

</section>
<script type="text/javascript">
	var ajax = {}
	var ajax_call = {};
	var ajax = filterFromURL();
	ajax.limit 	= 10;
	ajax.page 	= 1;
	ajaxToFilter(ajax, { customer : '#customer', daterangefilter : '#daterangefilter' });

	function getList() {
		filterToURL();
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}

	$(function(){
		getList();
	});
	$("#daterangefilter").on("change",function(){
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		getList();
	}).trigger('change');
	$("#customer").on("change",function(){
		ajax.customer = $(this).val();
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
	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});
</script>