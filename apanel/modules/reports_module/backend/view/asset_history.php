<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
				<div class="col-md-2">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Asset Class')
								->setName('assetclass')
								->setId('assetclass')
								->setList($assetclass_list)
								->setNone('Filter: All')
								->draw();
						?>
					</div>
					<div class="col-md-2">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Asset Number')
								->setName('asset')
								->setId('asset')
								->setList($asset_list)
								->setNone('Filter: All')
								->draw();
						?>
					</div>
					<div class="col-md-2">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Department')
								->setName('department')
								->setId('department')
								->setList($dept_list)
								->setNone('Filter: All')
								->draw();
						?>
					</div>
					<div class="col-md-2">
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
					<div class="col-md-4 text-right">
						<a href="" id="export_csv" download="Asset_History.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>	
			</div>
			<div class="box-body table-responsive no-padding" id="report_content">
				<table id="tableList" class="table table-hover table-striped table-sidepad">
						<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Asset Class',array('class'=>'col-md-2'),'sort','assetclass')
									->addHeader("Asset Number",array('class'=>'col-md-2'),'sort','asset_number')
									->addHeader("Serial Number / Engine Number",array('class'=>'col-md-2'),'sort','serial_number')
									->addHeader('Transaction Date',array('class'=>'col-md-2'),'sort','transactiondate')
									->addHeader('Transaction Type',array('class'=>'col-md-1'),'sort','transactiontype')
									->addHeader('Amount',array('class'=>'col-md-1'),'sort','amount')
									->addHeader('Transfer To',array('class'=>'col-md-1'),'sort','transferto')
									->draw();
						?>
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

		tableSort('#tableList', function(value) {
			ajax.sort = value;
			ajax.page = 1;
			getList();
		});

		$('#asset').on('change', function() {
			ajax.page = 1;
			ajax.asset_number = $(this).val();
			ajax_call.abort();
			getList();
		});
			
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

		$("#assetclass").on("change",function(){
			ajax.assetclass = $(this).val();
			getList();
		});

		$("#department").on("change",function(){
			ajax.department = $(this).val();
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