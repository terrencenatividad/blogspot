	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="form-group">
							<div class="input-group">
								<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-btn">
									<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-md-offset-8 text-right">
							<div class="form-group">
								<?php
									echo $ui->setElement('button')
											->setId('export')
											->setPlaceholder('<i class="glyphicon glyphicon-export"></i> Export')
											->draw();
								?>
							</div>
						</div>
				</div>
			</div>
			<div class="table-responsive">
				<table id="tableList" class="table table-hover table-striped table-sidepad report_table">
					<thead>
						<tr class="info">
							<th class="col-xs-2">Item Code</th>
							<th class="col-xs-3">Item Name</th>
							<th class="col-xs-4">Description</th>
							<th class="col-xs-1 text-right">Stock</th>
							<th>UOM</th>
							<th class="col-xs-1 text-right">Average Cost</th>
							<th class="col-xs-1 text-right">Total</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<div id="breakdown_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Average Cost Breakdown</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">
							<p><label>Item Code: </label> <span id="itemcode_label"></span></p>
							<p><label>Item Name: </label> <span id="itemname_label"></span></p>
							<p><label>Description: </label> <span id="itemdesc_label"></span></p>
						</div>
						<div class="col-md-4 text-right">
							<?php
								echo $ui->setElement('button')
										->setId('breakdown_export')
										->setPlaceholder('<i class="glyphicon glyphicon-export"></i> Export')
										->draw();
							?>
						</div>
					</div>
				</div>
				<div class="modal-body no-padding">
					<table id="breakdown_tableList" class="table table-hover table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Movement Date</th>
								<th class="col-xs-2 text-right">Document</th>
								<th class="col-xs-2 text-right">Movement Qty</th>
								<th class="col-xs-2 text-right">Stock Qty</th>
								<th class="col-xs-2 text-right">Cost</th>
								<th class="col-xs-3 text-right">Average Cost</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="6" class="text-center">Loading Items</td>
							</tr>
						</tbody>
					</table>
					<div id="pagination_modal"></div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var ajax = {}
		var ajax2 = {}
		var ajax_call = '';
		var ajax_call2 = '';
		
		$('#tableList').on('click', '.show_breakdown', function() {
			var itemcode = $(this).closest('tr').attr('data-itemcode');
			var itemname = $(this).closest('tr').attr('data-itemname');
			var itemdesc = $(this).closest('tr').attr('data-itemdesc');
			$('#breakdown_modal').modal('show');
			$('#itemcode_label').html(itemcode);
			$('#itemname_label').html(itemname);
			$('#itemdesc_label').html(itemdesc);
			$('#breakdown_tableList tbody').html(`<tr>
							<td colspan="6" class="text-center">Loading Items</td>
						</tr>`);
			ajax2.itemcode = itemcode;
			getBreakdownList();
		});
		
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});

		$('#pagination_modal').on('click', 'a', function(e) {
			e.preventDefault();
			ajax2.page = $(this).attr('data-page');
			getBreakdownList();
		});

		$('#table_search').on('input', function() {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
			
		function getList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_view',ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();
			
		function getBreakdownList() {
			if (ajax_call2 != '') {
				ajax_call2.abort();
			}
			ajax_call2 = $.post('<?=MODULE_URL?>ajax/ajax_view_breakdown', ajax2, function(data) {
				$('#breakdown_tableList tbody').html(data.table);
				$('#pagination_modal').html(data.pagination);
				if (ajax2.page > data.page_limit && data.page_limit > 0) {
					ajax2.page = data.page_limit;
					getList();
				}
			});
		}

		$("#export").click(function() {
			window.location = '<?=MODULE_URL?>view_export?' + $.param(ajax);
		});

		$("#breakdown_export").click(function() {
			window.location = '<?=MODULE_URL?>view_breakdown_export?' + $.param(ajax2);
		});
	</script>