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
					<div class="col-md-8">

							<?php
								
							echo $ui->formField('dropdown')
					
								->setPlaceholder('Select Warehouse')
								->setSplit('', 'col-md-4 warehouse_input')
								->setName('warehouse')
								->setId('warehouse')
								->setList($warehouse_list)
								->setValue($warehouse)
								->setNone('Filter: All')
								->draw($show_input);
							?>

					</div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="Sales Report per Stock.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
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
			<!--<div class="box-body table-responsive no-padding" id="report_content">
				<table id="stock_tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
					<thead>
						<tr class="info">
							<th class="col-md-1 text-center">Item Code</th>
							<th class="col-md-1 text-center">Item Category</th> 
							<th class="col-md-1 text-center">Warehouse</th>
							<th class="col-md-1 text-center">Quantity</th>
							<th class="col-md-1 text-center">Stocks</th>
							<th class="col-md-1 text-center">Amount</th>
						</tr>
					</thead>
					<tbody id="stock_container">
						
					</tbody>
				</table>
				<div id="pagination"></div>	
			</div>-->
			<div class="box-body table-responsive no-padding">
				<table id="stock_tableList" class="table table-hover table-sidepad">
					<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Item Code',array('class' => 'col-md-2'),'sort','invdtl.itemcode')
									->addHeader('Item Category',array('class' => 'col-md-2'),'sort','label')
									->addHeader('Stocks', array('class' => 'col-md-2'),'sort','detailparticular')
									->addHeader('Warehouse', array('class' => 'col-md-2'),'sort','invdtl.warehouse')
									->addHeader('Quantity', array('class' => 'col-md-2'),'sort','issueqty')
									->addHeader('Unit Price', array('class' => 'col-md-2'),'sort','unitprice')
									->addHeader('Amount', array('class' => 'col-md-2'),'sort','inv.amount')
									->draw();
						?>
					</thead>
					<tbody id="stock_container">

					</tbody>
				</table>
				<div id="pagination"></div>	
			</div>
		</div>
	
	</section>

	<script>
		var ajax = {}
		var ajax_call = {};
			
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) {
				 	$('#stock_container').html(data.table);
					$('#pagination').html(data.pagination);
					$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}

		getList();
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

		$("#daterangefilter").on("change",function(){
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			ajax.warehouse = '';
			getList();
		}).trigger('change');

		$('#items').on('change', function(){
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});

		tableSort('#stock_tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});
	</script>