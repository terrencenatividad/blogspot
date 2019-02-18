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
			<table id="tableList" class="table table-hover table-striped table-sidepad table-bordered">
				<thead>
					<tr class="info">
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
						<td class = "text-right budget_total"></td>
						<td class = "text-right jan"></td>
						<td class = "text-right feb"></td>
						<td class = "text-right mar"></td>
						<td class = "text-right april"></td>
						<td class = "text-right may"></td>
						<td class = "text-right june"></td>
						<td class = "text-right july"></td>
						<td class = "text-right aug"></td>
						<td class = "text-right sept"></td>
						<td class = "text-right oct"></td>
						<td class = "text-right nov"></td>
						<td class = "text-right dec"></td>
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
			$('.budget_total').html(addComma(data.budget_total));
			$('.jan').html(addComma(data.jan));
			$('.feb').html(addComma(data.feb));
			$('.mar').html(addComma(data.mar));
			$('.april').html(addComma(data.april));
			$('.may').html(addComma(data.may));
			$('.june').html(addComma(data.june));
			$('.july').html(addComma(data.july));
			$('.aug').html(addComma(data.aug));
			$('.sept').html(addComma(data.sept));
			$('.oct').html(addComma(data.oct));
			$('.nov').html(addComma(data.nov));
			$('.dec').html(addComma(data.dec));
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
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