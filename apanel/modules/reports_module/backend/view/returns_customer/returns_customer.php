<section class="content">
		<div class="box box-primary">
			<form action="" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
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
								<th class = "col-md-2 text-center">Transaction Date</th>
								<th class = "col-md-2 text-center">SR No.</th>
								<th class = "col-md-2 text-center">Item</th>
								<th class = "col-md-1 text-center">Quantity</th>
								<th class = "col-md-1 text-center">UOM</th>
								<th class = "col-md-2 text-center">Unit Price</th>
								<th class = "col-md-2 text-center">Amount</th>
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
							<a href="" id="export_csv" download="Detailed Sales Return per Customer.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
							<a href="<?=MODULE_URL?>" class="btn btn-default btn-flat">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>

<script>
var ajax = {}
var ajax_call = {};
ajax.partnercode = '<?php echo $partnercode ?>';

$('#pagination').on('click', 'a', function(e) 
{
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	getList();
})

function getList() 
{
	ajax.customer = '<?php echo $cust_code ?>';
	ajax.datefilter = '<?php echo $datefilter ?>';
	ajax.warehouse = '<?php echo $warehouse ?>';
	ajax.data_type = '<?php echo $data_type ?>';
	ajax_call = $.post('<?=MODULE_URL?>ajax/get_invoice', ajax, function(data) 
	{
		$('#tableList tbody').html(data.table);
		$('#pagination').html(data.pagination);
		$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
	});
}

getList();

$('#daterangefilter').on('change', function() 
{
	ajax.daterangefilter = $(this).val();
	ajax.page = 1;
	ajax_call.abort();
	getList();
});
</script>