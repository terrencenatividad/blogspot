	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<!--<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>-->
					
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-5">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Item')
										->setName('itemcode')
										->setId('itemcode')
										->setList($item_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
							<div class="col-md-5">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Warehouse')
										->setName('warehouse')
										->setId('warehouse')
										->setList($warehouse_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
						</div>
					</div>
					<!--<div class = "col-md-4">
						<div class="form-group">
							<div class="input-group" >
								<input name="table_search" id = "search" class="form-control" placeholder="Search" type="text" style = "height: 34px;">
								<div class="input-group-btn" style = "height: 34px;">
									<button type="button" class="btn btn-default" id="" style = "height: 34px;"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div> -->
					<div class="col-md-6 text-right">
						<div class="form-group">
							<a href="" id="export_csv" download="Inventory Inquiry.csv" class="btn btn-info btn-flat"><span class="glyphicon glyphicon-export"></span> CSV</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="row">
							<div class="col-sm-9 col-xs-6 text-right">
								<label for="" class="padded">Items: </label>
							</div>
							<div class="col-sm-3 col-xs-6">
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
			<!--<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th></th>
							<th>Item Name</th>
							<th>Warehouse</th>
							<th>Onhand Quantity</th>
							<th>Ordered Quantity</th>
							<th>Allocated Quantity</th>
							<th>Available Quantity</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>-->
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Item Name',array('class' => 'col-md-2'),'sort','itemname')
									->addHeader('Warehouse',array('class' => 'col-md-2'),'sort','w.description')
									->addHeader('Onhand Qty', array('class' => 'col-md-2'),'sort','SUM(inv.onhandQty)')
									->addHeader('Ordered Qty', array('class' => 'col-md-2'),'sort','SUM(inv.orderedQty)')
									->addHeader('Allocated Qty', array('class' => 'col-md-2'),'sort',' SUM(inv.allocatedQty)')
									->addHeader('Available Qty', array('class' => 'col-md-2'),'sort','SUM(inv.availableQty)')
									->draw();
						?>
					</thead>
					<tbody id="stock_container">

					</tbody>
				</table>
				<div id="pagination"></div>	
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<!--ON HAND QUANTITY MODAL-->
	<div class="modal fade" id="listModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					On Hand Qty
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<section class="content">
						<div class="box box-primary">
							<div class="box-body table-responsive no-padding">
								<table id="tableModalList" class="table table-hover table-sidepad">
									<thead>
										<tr class="info">
											<th></th>
											<th>Beginning Balance </th>
											<th>Purchase Receipt </th>
											<th>Delivered </th>
											<th>Sales Return </th>
											<th>Purchase Return </th>
											<th>Transfered </th>
											<th>Adjusted </th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
						<div id="pagination"></div>
					</section>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--ORDER QUANTITY MODAL-->
	<div class="modal fade" id="orderModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					Order Qty
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<section class="content">
						<div class="box box-primary">
							<div class="box-body table-responsive no-padding">
								<table id="orderList" class="table table-hover table-sidepad">
									<thead>
										<tr class="info">
											<th></th>
											<th>Purchase Order </th>
											<th>Purchase Return </th>
											<th>Purchase Receipt </th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
						<div id="pagination"></div>
					</section>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--ALLOCATED QTY-->
	<div class="modal fade" id="allocatedModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					Allocated Qty
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<section class="content">
						<div class="box box-primary">
							<div class="box-body table-responsive no-padding">
								<table id="allocatedModalList" class="table table-hover table-sidepad">
									<thead>
										<tr class="info">
											<th></th>
											<th>Sales Order </th>
											<th>Delivered </th>
											<!-- <th>Sales Return </th> -->
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
						<div id="pagination"></div>
					</section>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--AVAILABLE QUANTITY MODAL-->
	<!-- <div class="modal fade" id="availModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					Available Quantity
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<section class="content">
						<div class="box box-primary">
							<div class="box-body table-responsive no-padding">
								<table id="availModalList" class="table table-hover table-sidepad">
									<thead>
										<tr class="info">
											<th></th>
											<th>Purchase Receipt </th>
											<th>Delivered </th>
											<th>Sales Return </th>
											<th>Purchase Return </th>
											<th>Transfered </th>
											<th>Adjusted </th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
						<div id="pagination"></div>
					</section>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<script>
		var ajax = {}
		var ajax2 = {}
		var ajax_call = {};
		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});
		$('#search').on('input', function () {
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
			ajax_call.abort();
			getList();
		});
		$('#tableList tbody').on('click', 'a', function() {
			var path = $(this).attr('data-id');
			var arr = path.split('/');
			var table 		= arr[0];
			var itemcode 	= arr[1];
			var warehouse 	= arr[2];
			ajax2.table 	= table;
			ajax2.itemcode 	= itemcode;
			ajax2.warehouse = warehouse;
			ajax_call = $.post('<?=MODULE_URL?>ajax/' + ajax2.table + '_listing', ajax2, function(data) {
				if (ajax2.table == 'onhand'){
				$('#tableModalList tbody').html(data.table);
				$('#listModal').modal('show');
				} else if (ajax2.table == 'order'){
				$('#orderList tbody').html(data.table);
				$('#orderModal').modal('show');
				} else if (ajax2.table == 'allocated'){
				$('#allocatedModalList tbody').html(data.table);
				$('#allocatedModal').modal('show');
				} 
			});
		});
		tableSort('#tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});
		$('#warehouse').on('change',function(){
			ajax.warehouse = $(this).val();
			ajax.page = 1;
			getList();
		});
	</script>