	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "<?=$datefilter?>" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
					<?php	
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Customer')
							->setName('customer')
							->setId('customer')
							->setList($customer_list)
							// ->setValue($warehouse)
							->setNone('Filter: All')
							->draw($show_input);
						?>
					</div>
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Warehouse')
								->setClass('hidden')
								->setName('warehouse')
								->setId('warehouse')
								->setList($warehouse_list)
								->setNone('All')
								->setAttribute(array('multiple'))
								->draw();
						?>
					</div>

					<div class="col-md-2">
					</div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="Sales Return per Customer.csv" class="btn btn-primary pull-right btn-flat"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				

				</div>
				<div class="alert alert-info alert-dismissible" id="reminder" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<strong>Please search for a Customer first.</strong>
				</div>
			</div>
			<div id="table_customer" class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class="info">
							<th class='col-md-3'></th>
							<th class='col-md-4'>Customer</th>
							<th class='col-md-4'>Amount</th>
						</tr>
					</thead>
					<tbody>

					</tbody>

					<tfoot>
						<tr>
							<td colspan="8" class="text-center" id="pagination"></td>
						</tr>
					</tfoot>

				</table>
			</div>
		</div>
		<!--<div id="pagination"></div>-->
	</section>

<script>
	var ajax = {}
	var ajax_call = {};

	$( document ).ready(function() {
		$('#reminder').show();
		$('#table_customer').hide();
	});
	
	$('#pagination').on('click', 'a', function(e) 
	{
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	})
	
	function getList() 
	{
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) 
		{
			$('#tableList tbody').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	
	getList();

	$('#tableList tbody').on('click', 'tr', function() 
	{
		var id = $(this).attr('data-id');
		window.location = '<?php echo MODULE_URL ?>view/' + id;
	});
	
	$('#daterangefilter').on('change', function() 
	{
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		ajax_call.abort();
		getList();
	}).trigger("change");
	
	// $('#customer').on('change',function()
	// {
	// 	ajax.customer = $(this).val();
		
	// 	if (Array.isArray(ajax.customer) && ajax.customer.indexOf('none') != -1) 
	// 	{
	// 		$(this).selectpicker('deselectAll');
	// 	}
	// 	ajax_call.abort();
	// 	getList();
	// });

	$('#warehouse').on('change',function()
	{
		ajax.warehouse = $(this).val();
		if (Array.isArray(ajax.warehouse) && ajax.warehouse.indexOf('none') != -1) 
		{
			$(this).selectpicker('deselectAll'); 
		}
		ajax_call.abort();
		getList();
	});

	$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			ajax_call.abort();
			getList();

			$('#reminder').hide();
			$('#table_customer').show();
	
	});
	$('#customer').on('change', function () {
		ajax.page = 1;
		ajax.customer = $(this).val();
		ajax_call.abort();
		getList();
		$('#reminder').hide();
		$('#table_customer').show();
	});
	
</script>