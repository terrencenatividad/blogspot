<section class="content">
	<div class="box box-primary">
		<div class="box-header pb-none">
			<div class="row">
				<div class="col-md-3">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Cost Center')
					->setName('budgetcode')
					->setId('budgetcode')
					->setList($budgetcenter_list)
					->setNone('Filter: All')
					->draw();
					?>
				</div>
				<div class="col-md-6">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Year')
					->setSplit('', 'col-md-6')
					->setName('year')
					->setId('year')
					->setList($year_list)
					->setNone('Filter: All')
					->draw();
					?>
				</div>
				<div class="col-md-3 text-right">
					<a href="" id="export_csv" download="Budget_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
			</div>
		</div>
		<br><br><br>
		<div class="box-body table-responsive no-padding" id="report_content">
			<table id="tableList" class="table table-hover table-striped table-sidepad">
				<thead>
					<tr class="info">
						<th class="col-md-1">Account Name</th>
						<th class="col-md-1">Budget Code</th>
						<th class="col-md-1">Year</th>
						<th class="col-md-1">January</th>
						<th class="col-md-1">February</th>
						<th class="col-md-1">March</th>
						<th class="col-md-1">April</th>
						<th class="col-md-1">May</th>
						<th class="col-md-1">June</th>
						<th class="col-md-1">July</th>
						<th class="col-md-1">August</th>
						<th class="col-md-1">September</th>
						<th class="col-md-1">October</th>
						<th class="col-md-1">November</th>
						<th class="col-md-1">December</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
	<div id="pagination"></div>	
</section>
<script type="text/javascript">
	var ajax = {};
	var ajax_call = '';
	ajax.limit = 10;

	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#tableList tfoot').html(data.footer);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	getList();

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});

	$("#budgetcode").on("change",function(){
		ajax.budgetcode = $(this).val();
		getList();
	});

	$("#year").on("change",function(){
		ajax.year = $(this).val();
		getList();
	});
</script>