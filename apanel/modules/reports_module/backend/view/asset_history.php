<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Asset')
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
								->setName('datefilter')
								->setId('datefilter')
								->setClass('datepicker-input')
								->setAttribute(array('readonly'))
								->setAddon('calendar')
								->setValue($datefilter)
								->draw();
						?>
					</div>
					<div class="col-md-6 text-right">
						<a href="" id="export_csv" download="AP_Aging_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>	
			</div>
			<div class="box-body table-responsive no-padding" id="report_content">
				<table id="tableList" class="table table-hover table-striped table-sidepad">
						<thead>
						<tr class="info">
							<th class="col-md-2">Asset Class</th>
							<th class="col-md-2">Asset Number</th>
							<th class="col-md-2">Serial Number / Engine Number</th>
							<th class="col-md-2">Transaction Date</th>
							<th class="col-md-2">Transaction Type</th>
							<th class="col-md-2">Amount</th>
							<th class="col-md-2 text-right">Transfer To</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					<tfoot>
					
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
			});
		}

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});

		$("#supplier").on("change",function(){
			ajax.supplier = $(this).val();
			getList();
		});

		$('#datefilter').on('change', function() {
			ajax.datefilter = $(this).val();
			ajax.page = 1;
			if (ajax_call != '') {
				ajax_call.abort();
			}
			getList();
		});
	</script>