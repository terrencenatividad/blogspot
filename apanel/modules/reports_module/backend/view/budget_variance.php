<section class="content">
	<div class="box box-primary">
		<div class="box-header pb-none">
			<div class="row">
				<div class="col-md-3">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Cost Center')
					->setName('costcenter')
					->setId('costcenter')
					->setList($costcenter_list)
					->setNone('Filter: All')
					->draw();
					?>
				</div>
				<div class="col-md-3">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Budget Type')
					->setName('budget_type')
					->setId('budget_type')
					->setList(array('BS' => 'Balance Sheet', 'IS' => 'Income Statement'))
					->setNone('Filter: All')
					->draw();
					?>
				</div>
				<div class="col-md-3"></div>
				<div class="col-md-3 text-right">
					<a href="" id="export_csv" download="Budget_Variance.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
			</div>
		</div>
		<br><br><br>
		<div class="box-body table-responsive no-padding" id="report_content">
			<table id="tableList" class="table table-hover table-striped table-sidepad">
				<thead>
					<tr class="info">
						<th class="col-md-1">Code</th>
						<th class="col-md-2">Description</th>
						<th class="col-md-2">Budget</th>
						<th class="col-md-2">Actual</th>
						<th class="col-md-1">Variance</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
				<tfoot>
					<tr>
						<td colspan = "2"></td>
						<td><b>Total : </b><span class = "total_budget"></span></td>
						<td><b>Total : </b><span class = "total_actual"></span></td>
						<td><b>Total : </b><span class="total_variance"></span></td>
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

	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#tableList tfoot').html(data.footer);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			totalBudget();
			totalActual();
			totalVariance();
		});
	}
	getList();

	function totalBudget() {
		var total = 0;
		var amount = 0;
		$('#tableList tbody tr td.amount').each(function(index,value) {
			amount = removeComma($(this).html());
			total += amount;
			$('td .total_budget').html(addComma(total));
		});
	}

	function totalActual() {
		var total = 0;
		var amount = 0;
		$('#tableList tbody tr td.actual').each(function(index,value) {
			amount = removeComma($(this).html());
			total += amount;
			$('td .total_actual').html(addComma(total));
		});
	}

	function totalVariance() {
		var total = 0;
		var amount = 0;
		$('#tableList tbody tr td.variance').each(function(index,value) {
			amount = removeComma($(this).html());
			total += amount;
			$('td .total_variance').html(addComma(total));
		});
	}

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
			totalBudget();
			totalActual();
			totalVariance();
		}
	});

	$("#costcenter").on("change",function(){
		ajax.costcenter = $(this).val();
		getList();
		totalBudget();
		totalActual();
		totalVariance();
	});
	$("#budget_type").on("change",function(){
		ajax.budget_type = $(this).val();
		getList();
		totalBudget();
		totalActual();
		totalVariance();
	});
</script>