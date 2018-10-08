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
					<!--<div class="col-md-3">
						<?php
							// echo $ui->formField('dropdown')
							// 	->setPlaceholder('Select Customer')
							// 	->setName('customer')
							// 	->setId('customer')
							// 	->setList($customer_list)
							// 	->setNone('All')
							// 	->setAttribute(array('multiple'))
							// 	->draw();
						?>
					</div>-->
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Warehouse')
								->setName('warehouse')
								->setId('warehouse')
								->setList($warehouse_list)
								->setNone('All')
								->setAttribute(array('multiple'))
								->draw();
						?>
					</div>

					<div class="col-md-5">
					</div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="Sales Report per Warehouse.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>

				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<!--<tr class="info">
							<th></th>
							<th class='col-md-4'>Warehouse</th>
							<th class='col-md-4'>Quantity</th>
						</tr>-->
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Warehouse Code',array('class'=>'col-md-4 text center'),'sort','warehousecode')
									->addHeader('Warehouse',array('class'=>'col-md-4 text center'),'sort','warehouse')
									->addHeader('Quantity', array('class'=>'col-md-4'),'sort','quantity')
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
		$('#tableList tbody').on('click', 'tr.clickable', function() {
			var id = $(this).attr('data-id');
			window.location = '<?php echo MODULE_URL ?>view/' + id;
		});
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			ajax_call.abort();
			getList();
		}).trigger('change');
		$('#customer').on('change',function(){
			ajax.customer = $(this).val();
			if (Array.isArray(ajax.customer) && ajax.customer.indexOf('none') != -1) {
				$(this).selectpicker('deselectAll');
			}
			ajax_call.abort();
			getList();
		});
		$('#warehouse').on('change',function(){
			ajax.warehouse = $(this).val();
			if (Array.isArray(ajax.warehouse) && ajax.warehouse.indexOf('none') != -1) {
				$(this).selectpicker('deselectAll'); 
			}
			ajax_call.abort();
			getList();
		});
		
	</script>