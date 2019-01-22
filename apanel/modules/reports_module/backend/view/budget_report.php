<section class="content">
	<div class="box box-primary">
		<div class="box-header pb-none">
			<div class="row">
				<div class="col-md-3">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Budget Center')
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
		<!-- <h5>Note : <i>This list is for the Budget Check "Monitored" only.</i></h5> -->
		<div class="box-body table-responsive no-padding" id="report_content">
			<table id="tableList" class="table table-hover table-striped table-sidepad">
				<thead>
					<tr class="info">
						<!-- <th class="col-md-1">Account Name</th>
						<th class="col-md-1">Budget Code</th> -->
						<th class="col-md-1">Budget Code</th>
						<th class="col-md-1">Budget Description</th>
						<th class="col-md-1">Total Budget</th>
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
				<tfoot>
					<tr>
						<td colspan="2" class="text-right"><strong>Total</strong></td>
						<td class = "text-right year_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
						<td class = "text-right all_total"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div id="pagination"></div>	
</section>
<script type="text/javascript">
	var ajax = {};
	var ajax_call = '';
	ajax.limit = 10;
	var total = 0;
	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#tableList tfoot').html(data.footer);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			$('#tableList tbody tr input.monthly_total').each(function() {
				var val = parseInt($(this).val());
				total += +val;
				$('.all_total').html('<strong>'+addComma(total)+'</strong>');
			});
			total = 0;
			$('#tableList tbody tr input.year_total').each(function() {
				var val = parseInt($(this).val());
				total += +val;
				$('.year_total').html('<strong>'+addComma(total)+'</strong>');
			});
		});
	}
	getList();

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			total = 0;
			getList();
		}
	});

	$("#budgetcode").on("change",function(){
		total = 0;
		ajax.budgetcode = $(this).val();
		getList();
	});

	$("#year").on("change",function(){
		total = 0;
		ajax.year = $(this).val();
		getList();
	});
</script>