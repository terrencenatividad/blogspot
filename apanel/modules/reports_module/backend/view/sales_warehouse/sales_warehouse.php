<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="col-md-12">
						<!--<a href="" id="export_csv" download="Detailed Report per Warehouse.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>-->
					</div>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Warehouse Code')
											->setSplit('col-md-4', 'col-md-8')
											->setName('name')
											->setId('name')
											->setValue($warehousecode)
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Description')
											->setSplit('col-md-4', 'col-md-8')
											->setName('voucherno')
											->setId('voucherno')
											->setValue($description)
											->draw(false);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-body table-responsive no-padding">
					<table id="tableList" class="table table-hover table-sidepad">
						<thead>
							<tr class="info">
								<th>Itemcode</th>
								<th>Itemname</th>
								<th>Quantity</th>
								<th>UOM</th>
								<th>Unit Price</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div id="pagination"></div>
				</div>
				<div class="box-body">
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
						<a href="" id="export_csv" download="Detailed Report per Warehouse.csv" class="btn btn-primary "><span class="glyphicon glyphicon-export"></span> Export</a>
							<a href="<?=MODULE_URL?>" class="btn btn-default">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<script>
		var ajax = {}
		var ajax_call = {};
		$('#table_search').on('input', function () {
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
			ajax.page = $(this).attr('data-page');
			getList();
		})
		function getList() {
			ajax.warehouse =  '<?php echo $warehouse ?>';
			ajax.date	   =  '<?php echo $daterange ?>';
			ajax_call = $.post('<?=MODULE_URL?>ajax/getWarehouse', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}
		getList();
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			ajax_call.abort();
			getList();
		});
	</script>