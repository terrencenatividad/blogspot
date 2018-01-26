	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Item')
										->setName('itemcode')
										->setId('itemcode')
										->setList($item_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4 text-right">
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
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
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
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th>Date</th>
							<th>Item Name</th>
							<th>Warehouse</th>
							<th>Reference No.</th>
							<th>Particulars</th>
							<th>In</th>
							<th>Out</th>
							<th>Current Onhand</th>
							<th>Activity</th>
							<th>User</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<script>
		var ajax = {}
		var ajax_call = '';
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});
		$('#itemcode').on('change', function() {
			ajax.page = 1;
			ajax.itemcode = $(this).val();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		})
		function getList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			getList();
		});
		$('#export').click(function() {
			var daterangefilter	= $('#daterangefilter').val();
			var itemcode		= $('#itemcode').val();
			window.location = '<?php echo MODULE_URL ?>list_export/' + btoa(daterangefilter) + '/' + btoa(itemcode);
		});
	</script>