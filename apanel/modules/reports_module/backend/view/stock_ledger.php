<section class="content">
	<div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class = "row">
					<div class="col-md-12">
						<div class="row">
							<div class = "col-md-1">
								<?php echo $ui->formField("button")
										->setId("export_csv")
										->setClass("btn btn-info")
										->setPlaceholder('<span class="glyphicon glyphicon-export"></span> CSV')
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Account')
										->setName('accountcodefilter')
										->setId('accountcodefilter')
										->setList($accountcodes)
										->setNone('All')
										->setAttribute(array('multiple'))
										->draw();
								?>
							</div>
							<!-- Filter Button Here / Display Items dopdown + CSV-->
						</div>
					</div>
					<div class="col-md-4">
						<div class="col-md-12">
							<div class="form-group">
								<div class="input-group" >
									<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
									<div class="input-group-btn" style = "height: 34px;">
										<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		
		<table id="tableList" class="table table-striped table-hover  report_table text-right">
			<thead>
				<tr class="info">
					<th class="col-md-1">Date</th>
					<th class="col-md-1">Reference No.</th>
					<th class="col-md-3">Particulars</th>
					<th class="col-md-1">In</th>
					<th class="col-md-1">Out</th>
					<th class="col-md-1">Balance</th>
				</tr>
			</thead>
			<tbody id = "list_container" style = "border-top:0;">
			</tbody>
		</table>
	</div>
	<div id="pagination"></div>
</section>
<script>
	var ajax = {}
	var ajax_call = {};
	$('#search').on('input', function () {
		ajax.search = $(this).val();
		ajax.page = 1;
		ajax_call.abort();
		getList();
	});
	$('#accountcodefilter').on('change', function() {
		ajax.accountcodefilter = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#customer').on('change', function() {
		ajax.customer = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	});
	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList #list_container').html(data.table);
			$('#pagination').html(data.pagination);
		});
	}
	getList();
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		ajax_call.abort();
		getList();
	});
	$("#export_csv").click(function() {
		window.location = '<?=MODULE_URL?>ajax/export?' + $.param(ajax);
	});
</script>