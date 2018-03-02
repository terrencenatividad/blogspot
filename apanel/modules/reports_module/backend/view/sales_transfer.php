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
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Requesting Warehouse')
								->setName('warehouse')
								->setId('warehouse1')
								->setNone('All')
								->setList($warehouse_list)
								->setValue($warehouse)
								->draw($show_input);
						?>
					</div>
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Source Warehouse')
								->setName('warehouse')
								->setId('warehouse2')
								->setNone('All')
								->setList($warehouse_list)
								->setValue($warehouse)
								->draw($show_input);
						?>
					</div> 
					<div class="col-md-2">
					</div>
					<div class="col-md-1">
						<a href="" id="export_csv" download="Sales Report per Stock Transfer.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
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
		</div>

		<div class="nav-tabs-custom">
			<!-- <ul id="filter_tabs" class="nav nav-tabs">
				<li><a href="open" data-toggle="tab">Pending</a></li>
				<li><a href="approved" data-toggle="tab">Approved</a></li>
				<li><a href="rejected" data-toggle="tab">Rejected</a></li>
				<li><a href="partial" data-toggle="tab">Partial</a></li>
				<li><a href="transferred" data-toggle="tab">Transferred</a></li>
				<li><a href="posted" data-toggle="tab">Ready for Tagging</a></li>
				<li><a href="closed" data-toggle="tab">Closed</a></li>
				<li class="active"><a href="all" data-toggle="tab">All</a></li>	
			</ul> -->
			<table id = "tableList" class="table table-hover">
				<thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('',array(),'','')
								->addHeader('Transfer No.',array(),'sort','sa.stocktransferno')
								->addHeader('Transaction Date',array(),'sort','sa.transactiondate')
								->addHeader('Requesting Warehouse',array(),'sort','w.description')
								->addHeader('Destination Warehouse',array(),'sort','wh.description')
								->addHeader('Request No',array(),'sort','source_no')
								->addHeader('Item Code',array(),'sort','itemcode')
								->addHeader('Item Desc',array(),'sort','detailparticular')
								->addHeader('Uom',array(),'sort','uom')
								->addHeader('No of Items',array(),'sort','qtytransferred')
								->addHeader('',array(),'','')
								->draw();
						?>
				</thead>
			<form method = "post">
				<tbody id = "list_container">
				</tbody>
			</form>	
			</table>
			<div id="pagination"></div>
		</div>
	</section>

	<script>
		var ajax_call = {};
		var ajax = filterFromURL();
		ajax.filter = 'all';
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter', warehouse1 : '#warehouse1', warehouse2 : '#warehouse2' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
			
		function getList() {
			filterToURL();
			ajax_call = $.post('<?=MODULE_URL?>ajax/sales_transferlist',ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}
		getList();
		
		$("#warehouse1").on("change",function(){
			ajax.page = 1;
			ajax.warehouse1 = $(this).val();
			ajax_call.abort();
			getList();
		});

		$("#warehouse2").on("change",function(){
			ajax.page = 1;
			ajax.warehouse2 = $(this).val();
			ajax_call.abort();
			getList();
		});

		$("#daterangefilter").on("change",function(){
			ajax.page = 1;
			ajax.daterangefilter = $(this).val();
			ajax_call.abort();
			getList();
		}).trigger('change');

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		})

		$('#filter_tabs li').on('click', function(){
			ajax.filter = $(this).find('a').attr('href');
			ajax.page = 1;
			getList();
		});

		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});

		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		},ajax);
	</script>