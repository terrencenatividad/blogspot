<section class="content">
		<div class="box box-primary">
			<div class="box-header">
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
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Vendor')
								->setName('vendor')
								->setId('vendor')
								->setList($vendor_list)
								->setNone('All')
								->setAttribute(array('multiple'))
								->draw();
						?>
					</div>

					<div class="col-md-5">
					</div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="purchase_journal.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>

				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Date',array('class'=>'col-md-2'),'sort','pr.transactiondate')
									->addHeader("Supplier's TIN",array('class'=>'col-md-2'),'sort','pt.tinno')
									->addHeader("Supplier's Name",array('class'=>'col-md-2'),'sort','pt.partnername')
									->addHeader('Description',array('class'=>'col-md-2'),'sort','pr.remarks')
									->addHeader('Reference No.',array('class'=>'col-md-1'),'sort','pr.voucherno')
									->addHeader('Amount',array('class'=>'col-md-1'),'sort','pr.taxamount + pr.amount')
									->addHeader('Discount',array('class'=>'col-md-1'),'sort','pr.discountamount')
									->addHeader('VAT amount',array('class'=>'col-md-1'),'sort','pr.taxamount')
									->addHeader('Net Purchases',array('class'=>'col-md-1'),'sort','pr.amount')
									->draw();
						?>
					</thead>
					<tbody>

					</tbody>
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
		var ajax_call = {};
		tableSort('#tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#itemcode').on('change', function() {
			ajax.page = 1;
			ajax.itemcode = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		})
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}
		getList();
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			ajax_call.abort();
			getList();
		}).trigger('change');
		$('#vendor').on('change',function(){
			ajax.customer = $(this).val();
			if (Array.isArray(ajax.vendor) && ajax.vendor.indexOf('none') != -1) {
				$(this).selectpicker('deselectAll');
			}
			ajax_call.abort();
			getList();
		});
		$('#vendor').on('change',function(){
			ajax.vendor = $(this).val();
			if (Array.isArray(ajax.vendor) && ajax.vendor.indexOf('none') != -1) {
				$(this).selectpicker('deselectAll'); 
			}
			ajax_call.abort();
			getList();
		});
		
	</script>