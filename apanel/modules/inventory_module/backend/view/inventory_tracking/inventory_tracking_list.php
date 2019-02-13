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
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-4">
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
							<div class="col-md-4">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Warehouse')
										->setName('warehouse')
										->setId('warehouse')
										->setList($warehouse_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
							<div class="col-md-4">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Brand')
										->setName('brandcode')
										->setId('brandcode')
										->setList($brand_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-3 text-right">
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
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Date', array('class' => 'col-md-2'), 'sort', 'il.entereddate', 'desc')
								->addHeader('Item', array('class' => 'col-md-2'), 'sort', 'itemname')
								->addHeader('Brand', array('class' => 'col-md-1'), 'sort', 'brandname')
								->addHeader('Warehouse', array('class' => 'col-md-1'), 'sort', 'description')
								->addHeader('Reference No.', array('class' => 'col-md-1'), 'sort', 'reference')
								->addHeader('Particulars', array('class' => 'col-md-1'), 'sort', 'partnername')
								->addHeader('In', array('class' => 'col-md-1'), 'sort', 'prevqty')
								->addHeader('Out', array('class' => 'col-md-1'), 'sort', 'quantity')
								->addHeader('Current Onhand', array('class' => 'col-md-1'), 'sort', 'currentqty')
								->addHeader('Activity', array('class' => 'col-md-1'), 'sort', 'activity')
								->addHeader('User', array('class' => 'col-md-2'), 'sort', 'name')
								->draw();
					?>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<script>
		var ajax = filterFromURL();
		var ajax_call = '';
		ajaxToFilter(ajax, { search : '#table_search', itemcode : '#itemcode' });
		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		}, ajax);
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
		$('#brandcode').on('change', function() {
			ajax.brandcode 	= $(this).val();
			ajax.page 		= 1;
			getList();
		});
		$('#warehouse').on('change', function() {
			ajax.page = 1;
			ajax.warehouse = $(this).val();
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
			var warehouse		= $('#warehouse').val();
			var brandcode		= $('#brandcode').val();
			window.location		= '<?php echo MODULE_URL ?>list_export/' + btoa(daterangefilter) + '/' + btoa(itemcode) + '/' + btoa(warehouse) + '/' + btoa(ajax.sort) + '/' + btoa(brandcode);
		});
	</script>