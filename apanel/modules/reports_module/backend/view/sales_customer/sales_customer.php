<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="col-md-12 ">
						<!--<a href="" id="export_csv" download="Detailed Report per Customer.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span>Export</a>-->
					</div>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Customer')
											->setSplit('col-md-4', 'col-md-8')
											->setName('name')
											->setId('name')
											->setValue($name)
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Mobile')
											->setSplit('col-md-4', 'col-md-8')
											->setName('voucherno')
											->setId('voucherno')
											->setValue($mobile)
											->draw(false);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Email')
											->setSplit('col-md-4', 'col-md-8')
											->setName('voucherno')
											->setId('voucherno')
											->setValue($email)
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Address')
											->setSplit('col-md-4', 'col-md-8')
											->setName('voucherno')
											->setId('voucherno')
											->setValue($address1)
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
								<th></th>
								<th>Transaction Date</th>
								<th>SI No.</th>
								<th>Reference No.</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div id="header_values"></div>
				</div>
				<div class="box-body">
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
						<a href="" id="export_csv" download="Detailed Report per Customer.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span>Export</a>
							<a href="<?=MODULE_URL?>" class="btn btn-default">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<div id="packing_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Order List</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-8">
							<div class="input-group">
								<input type="text" id="order_list_search" class="form-control" placeholder="Search Order List">
								<div class="input-group-addon">
									<i class="glyphicon glyphicon-search"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body no-padding">
					<table id="tableList" class="table table-hover table-clickable table-sidepad">
						<thead>
							<tr class="info">
								<th>Transaction Date</th>
								<th>SI No.</th>
								<th>Amount</th>
								<th>Balance</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="5" class="text-center">Loading Items</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script>
		var ajax = {}
		var ajax_call = {};
		ajax.partnercode = '<?php echo $partnercode ?>';
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
			ajax.customer = '<?php echo $cust_code ?>';
			ajax.datefilter = '<?php echo $datefilter ?>';
			ajax_call = $.post('<?=MODULE_URL?>ajax/get_invoice', ajax, function(data) {
				$('#tableList tbody').html(data.table);
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