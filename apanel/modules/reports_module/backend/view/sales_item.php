	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value="<?php echo $datefilter ?>" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="row">
							<div class="col-md-4">
								<?php
									echo $ui->formField('dropdown')
											->setPlaceholder('Filter Item')
											->setName('itemcode')
											->setId('itemcode')
											->setNone('Filter: All')
											->setList($item_list)
											->draw();
								?>
							</div>
							<div class="col-md-4">
								<?php
									echo $ui->formField('dropdown')
											->setPlaceholder('Customer')
											->setName('customer')
											->setId('customer')
											->setNone('Filter: All')
											->setList($customer_list)
											->draw();
								?>
							</div>
							<div class="col-md-4">
								<?php
									echo $ui->formField('dropdown')
											->setPlaceholder('Warehouse')
											->setName('warehouse')
											->setId('warehouse')
											->setNone('Filter: All')
											->setList($warehouse_list)
											->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-2 text-right">
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
			<div class="box-body table-responsive no-padding" id="report_content">
				<table id="tableList" class="table table-hover table-sidepad" cellpadding="0" cellspacing="0" border="0" width="100%">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Item', array('class' => 'col-md-2'), 'sort', 'itemname', 'asc')
								->addHeader('Category', array('class' => 'col-md-1'), 'sort', 'label')
								->addHeader('UOM', array('class' => 'col-md-1 text-right'), 'sort', 'uom')
								->addHeader('Qty Sold', array('class' => 'col-md-2 text-right'), 'sort', 'sales')
								->addHeader('Qty Returned', array('class' => 'col-md-2 text-right'), 'sort', 'returns')
								->addHeader('Net Qty', array('class' => 'col-md-2 text-right'), 'sort', '(sales - returns)')
								->addHeader('Amount', array('class' => 'col-md-2 text-right'), 'sort', '(SUM(amount))')
								->draw();
					?>
					<tbody>
						
					</tbody>
					<tfoot>

					</tfoot>
				</table>
			</div>
		</div>
		<div id="pagination"></div>	
	</section>

	<script>
		var ajax = {}
		var ajax_call = '';

		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		});
			
		function getList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_view',ajax, function(data) {
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

		$('#itemcode').on('change', function() {
			ajax.page = 1;
			ajax.itemcode = $(this).val();
			getList();
		});

		$('#customer').on('change', function() {
			ajax.page = 1;
			ajax.customer = $(this).val();
			getList();
		});

		$('#warehouse').on('change', function() {
			ajax.page = 1;
			ajax.warehouse = $(this).val();
			getList();
		});

		$("#category").on("change",function(){
			ajax.page = 1;
			ajax.category = $(this).val();
			getList();
		});

		$("#daterangefilter").on("change",function(){
			ajax.page = 1;
			ajax.category = '';
			ajax.daterangefilter = $(this).val();
			$('#warehouse').val('');
			getList();
		}).trigger('change');

		$("#export").click(function() {
			window.location = '<?=MODULE_URL?>view_export?' + $.param(ajax);
		});
	</script>