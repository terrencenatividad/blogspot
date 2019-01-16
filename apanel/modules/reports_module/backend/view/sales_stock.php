<section class="content">
		<div class="box box-primary">
			<div class="box-header">
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
											->setPlaceholder('Item Class')
											->setName('category')
											->setId('category')
											->setNone('Filter: All')
											->setList($category_list)
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
				<div class="box-body table-responsive no-padding">
					<table id="stock_tableList" class="table table-hover table-sidepad">
						<thead>
							<?php
								echo $ui->loadElement('table')
										->setHeaderClass('info')
										->addHeader('Item Code',array('class' => 'col-md-2'),'sort','invdtl.itemcode')
										->addHeader('Item Category',array('class' => 'col-md-2'),'sort','label')
										->addHeader('Stocks', array('class' => 'col-md-2'),'sort','detailparticular')
										// ->addHeader('Warehouse', array('class' => 'col-md-2'),'sort','invdtl.warehouse')
										->addHeader('Qty', array('class' => 'col-md-2'),'sort','issueqty')
										->addHeader('Unit Price', array('class' => 'col-md-2'),'sort','unitprice')
										->addHeader('Amount', array('class' => 'col-md-2'),'sort','inv.amount')
										->draw();
							?>
						</thead>
						<tbody id="stock_container">

						</tbody>
						<tfoot>
	
						</tfoot>
					</table>
					<div id="pagination"></div>	
				</div>
		</div>
	</section>

	<script>
		var ajax = {}
		var ajax_call = '';
			
		tableSort('#stock_tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});

		function getList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) {
				$('#stock_container').html(data.table);
				if (data.result_count == 0) {
					data.tabledetails = '';
				}
				$('#stock_tableList tfoot').html(data.tabledetails);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		})

		$("#warehouse").on("change",function(){
			ajax.warehouse	=	$(this).val();
			ajax.page = 1;
			getList();
		});

		$("#category").on("change",function(){
			ajax.category	=	$(this).val();
			ajax.page = 1;
			getList();
		});

		$("#daterangefilter").on("change",function(){
			ajax.page = 1;
			ajax.category = '';
			ajax.daterangefilter = $(this).val();
			$('#warehouse').val('');
			getList();
		}).trigger('change');

		$('#items').on('change', function(){
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});

		$("#export").click(function() {
			window.location = '<?=MODULE_URL?>export?' + $.param(ajax);
		});
	</script>