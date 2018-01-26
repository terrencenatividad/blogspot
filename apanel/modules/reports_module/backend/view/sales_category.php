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
								->setPlaceholder('Filter Category')
								->setName('category')
								->setId('category')
								->setList($category_list)
								->setNone('All')
								->draw();
						?>
                           
					</div>
					<div class="col-md-3">
						
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Warehouse')
								->setName('warehouse')
								->setId('warehouse')
								->setList($warehouse_list)
								->setNone('All')
								->draw();
						?>
                           
					</div>

					<div class="col-md-1 col-md-offset-2">
						<a href="" id="export_csv" download="sales_per_category.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-bordered">
					<thead>
						<tr class="info">
							<th class="col-md-6 center">Item Category</th>
                            <th class="col-md-2 center">Sales Quantity</th>
							<th class="col-md-2 center">Base Quantity</th>
                            <th class="col-md-2 center">Amount</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination" class="text-center">
			
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
	<script>
		var ajax = {}
		var ajax_call = {};
		ajax.page 	= 1;
		ajax.limit 	= 20;
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			try {
				ajax_call.abort();
			} catch (e) {}
			getList();
		}).trigger('change');
		$('#category').on('change', function() {
			ajax.page = 1;
			ajax.category = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#warehouse').on('change', function() {
			ajax.page = 1;
			ajax.warehouse = $(this).val();
			ajax_call.abort();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				$("#export_csv").attr('href', 'data:text/csv;filename=sales_per_category.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}
		tableSort('#tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});
	</script>