<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post">
                <div class="col-md-3">
                    <?php
                        echo $ui->formField('dropdown')
								->setPlaceholder('Filter IPO')
								->setName('import_purchase_order')
								->setId('import_purchase_order')
								->setList($import_purchase_order_list)
								->setNone('Filter: All')
								->draw();
                    ?>
                </div>
                <div class="col-md-3">
                    <?php
                        echo $ui->formField('dropdown')
								->setPlaceholder('Filter Supplier')
								->setName('supplier')
								->setId('supplier')
								->setList($supplier_list)
								->setNone('Filter: All')
								->draw();
                    ?>
                </div>
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
				<!-- <div class="col-md-3"></div> -->
				<div class="col-md-3">
					<div class="form-group text-right">
					<a href="<?php echo BASE_URL ?>purchase/job" id="job_link" class="btn btn-primary">Create Job</a>
						<a href="" id="export_csv" download="Landed_Cost.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
				</form>
			</div>	
		</div>
	</div>


    <div class="nav-tabs-custom">
        <ul id="filter_tabs" class="nav nav-tabs">
            <li class="active"><a href="#All" data-toggle="tab" data-id="All">All</a></li>
            <li><a href="#Partial" data-toggle="tab" data-id="Partial">Partial</a></li>
            <li><a href="#Completed" data-toggle="tab" data-id="Completed">Completed</a></li>
        </ul>

		<div class="tab-content no-padding">
			<div id="All" class="tab-pane active">
				<table id="landedCostListAll" class="landedCostList table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
					<thead>
						<tr class="info">
							<th class="col-md-1 text-center">Item</th>
							<th class="col-md-1 text-center">Description</th>
							<th class="col-md-1 text-center">IPO Number</th>
							<th class="col-md-1 text-center">IPO Date</th>
							<th class="col-md-1 text-center">Qty/Unit</th>
							<th class="col-md-1 text-center">IPO Receipt Date</th>
							<th class="col-md-1 text-center">Unit Cost Foreign Currency</th>
							<th class="col-md-1 text-center">Unit Cost Base Currency</th>
							<th class="col-md-1 text-center">Job Number</th>
							<th class="col-md-1 text-center">Importation Cost per Unit</th>
							<th class="col-md-1 text-center">Landed Cost per Unit</th>
							<th class="col-md-1 text-center">Total Landed Cost</th>
						</tr>
					</thead>
					<tbody id="landed_container">
						
					</tbody>
				</table>
				<div id="pagination" class="page"></div>	
			</div>

			<div id="Partial" class="tab-pane">
				<table id="landedCostListPartial" class="landedCostList table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
					<thead>
						<tr class="info">
							<th class="col-md-1 text-center">Item</th>
							<th class="col-md-1 text-center">Description</th>
							<th class="col-md-1 text-center">IPO Number</th>
							<th class="col-md-1 text-center">IPO Date</th>
							<th class="col-md-1 text-center">Qty/Unit</th>
							<th class="col-md-1 text-center">IPO Receipt Date</th>
							<th class="col-md-1 text-center">Unit Cost Foreign Currency</th>
							<th class="col-md-1 text-center">Unit Cost Base Currency</th>
							<th class="col-md-1 text-center">Job Number</th>
							<th class="col-md-1 text-center">Importation Cost per Unit</th>
							<th class="col-md-1 text-center">Landed Cost per Unit</th>
							<th class="col-md-1 text-center">Total Landed Cost</th>
						</tr>
					</thead>
					<tbody id="landed_container">
						
					</tbody>
					
				</table>
				<div id="pagination" class="page"></div>	
			</div>

			<div id="Completed" class="tab-pane">
				<table id="landedCostListCompleted" class="landedCostList table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
					<thead>
						<tr class="info">
							<th class="col-md-1 text-center">Item</th>
							<th class="col-md-1 text-center">Description</th>
							<th class="col-md-1 text-center">IPO Number</th>
							<th class="col-md-1 text-center">IPO Date</th>
							<th class="col-md-1 text-center">Qty/Unit</th>
							<th class="col-md-1 text-center">IPO Receipt Date</th>
							<th class="col-md-1 text-center">Unit Cost Foreign Currency</th>
							<th class="col-md-1 text-center">Unit Cost Base Currency</th>
							<th class="col-md-1 text-center">Job Number</th>
							<th class="col-md-1 text-center">Importation Cost per Unit</th>
							<th class="col-md-1 text-center">Landed Cost per Unit</th>
							<th class="col-md-1 text-center">Total Landed Cost</th>
						</tr>
					</thead>
					<tbody id="landed_container">
						
					</tbody>
					
				</table>
				<div id="pagination" class="page"></div>	
			</div>
		</div>

    </div>
</section>


<script type = 'text/javascript'>

	var ajax = {};
	var ajax2 = {};
	var ajax3 = {};
	var ajax_call = '';
	// var ajax = filterFromURL();
	ajax.limit = 10; 
	var tab = $('#filter_tabs li.active a').attr('data-id');
	$('#filter_tabs').on('click', 'li', function() {
		tab = $(this).find('a').attr('data-id');
		// alert(tab);
		ajax.tab = tab;
		getList();
	});


	function getList() {
		tab = $('#filter_tabs li.active a').attr('data-id');
		ajax.tab
		// alert(ajax.tab);
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('.landedCostList tbody').html(data.table);
			$('#landedCostList tbody').html(data.table);
			$('.page').html(data.pagination);
			if (ajax.page > data.page_limit && data.page_limit > 0) {
				ajax.page = data.page_limit;
				getList();
			}
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	getList();

	$('.page').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});

	$('#import_purchase_order').on('change', function() {
		ajax.import_purchase_order 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});

	$('#supplier').on('change', function() {
		ajax.supplier 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});

	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		getList();
		// enable_button($(this).val());
	}).trigger('change');
	
</script>