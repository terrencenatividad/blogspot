<section class="content">
	<div class="box box-primary">
		<div class="box-header pb-none">
			<div class="row">
				<div class="col-md-3">
					<?php
					echo $ui->formField('dropdown')
					->setPlaceholder('Filter Budget Center')
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
				<div class="col-md-3">
					<div class="form-group">
						<div class="input-group">
							<input type="text" name="date" id="date" class="form-control" value = "" data-daterangefilter="month" autocomplete="off">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-calendar"></i>
							</span>
						</div>
					</div>
				</div>
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
						<th class="col-md-3">Code</th>
						<th class="col-md-3">Description</th>
						<th class="col-md-2">Budget</th>
						<th class="col-md-2">Actual</th>
						<th class="col-md-2">Variance</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
				<tfoot>
					<tr>
						<td colspan = "2"></td>
						<td class = "text-right"><strong>Total : </strong><span class = "total_amount"></span></td>
						<td class = "text-right"><b>Total : </b><span class = "total_actual"></span></td>
						<td class = "text-right"><b>Total : </b><span class="total_variance"></span></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div id="pagination"></div>	
</section>
<script type="text/javascript">
	var ajax = filterFromURL();
	var ajax_call = '';
	ajax.limit = 10;

	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#tableList tfoot').html(data.footer);
			$('#pagination').html(data.pagination);
			$('.total_amount').html(addComma(data.total_amount));
			$('.total_actual').html(addComma(data.total_actual));
			$('.total_variance').html(addComma(data.total_variance));
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	getList();

	tableSort('#tableList', function(value, getlist) {
		ajax.sort = value;
		ajax.page = 1;
		if (getlist) {
			getList();
		}
	});

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});

	$("#costcenter").on("change",function(){
		ajax.costcenter = $(this).val();
		ajax.page = "1";
		getList();
	});
	$("#budget_type").on("change",function(){
		ajax.budget_type = $(this).val();
		ajax.page = "1";
		getList();
	});
	$("#date").on("change",function(){
		ajax.date = $(this).val();
		ajax.page = "1";
		getList();
	});
</script>