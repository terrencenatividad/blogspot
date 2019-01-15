	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<div class="input-group">
							<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "<?=$datefilter?>" data-daterangefilter="month">
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Item')
							->setName('itemcode')
							->setId('itemcode')
							->setList($item_list)
							->draw(true);
					?>
				</div>
				<div class="col-md-3">
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
				<div class="col-md-3">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Warehouse')
							->setName('warehouse')
							->setId('warehouse')
							->setNone('All')
							->setList($warehouse_list)
							->draw(true);
					?>
				</div>
			</div>
			<div class="row">
				<div class = "col-md-11"></div>
				<div class = "col-md-1 form-group">
					<a href="" id="export_csv" download="Stock Based Sales.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
			</div>
			<div class="alert alert-info alert-dismissible show" id="reminder" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<strong>Please select an Item first.</strong>
			</div>
			<div class="box-body">
				<div id="stock_based_table" class=" table-responsive no-padding hidden">
					<table id="tableList" class="table table-hover table-bordered">
						<thead>
							<tr class="info">
								<th class="col-md-1 center">Date</th>
								<th class="col-md-1 center">Invoice No.</th>
								<th class="col-md-3 center">Customer</th>
								<th class="col-md-2 center">Qty</th>
								<th class="col-md-2 center">Total</th>
								<th class="col-md-1 center">Unit Price</th>
								<th class="col-md-2 center">Amount</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
							<tr>
								<td colspan="3"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tfoot>
					</table>
					<div id="pagination" class="text-center">
				</div>
			</div>
		</div>
			
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
		ajax.itemcode 	= '';
		ajax.page 		= 1;

		$('#itemcode').on('change', function() {
			ajax.page = 1;
			ajax.itemcode = $(this).val();
			ajax_call.abort();

			getList();

			$('#stock_based_table').removeClass('hidden');
			$('#reminder').fadeOut('slow','linear',$('#reminder').addClass('hidden'));
		});
		$('#customer').on('change', function() {
			ajax.page = 1;
			ajax.customer = $(this).val();
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
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});

		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);

				if(data.result_count > 0){
					$("#export_csv").attr('href', 'data:text/csv;filename=stock_based_sales.csv;charset=utf-8,' + encodeURIComponent(data.csv));
					$("#export_csv").removeClass('hidden');
				}else{
					$("#export_csv").addClass('hidden');
				}
				
			});
		}
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			try {
				ajax_call.abort();
			} catch (e) {}
			getList();
		}).trigger('change');
	</script>