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
								->setPlaceholder('Filter Budget Center')
								->setName('department')
								->setId('department')
								->setList($dept_list)
								->setNone('Filter: All')
								->draw();
						?>
					</div>
					<div class="col-md-2 hidden">
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
						<a href="" id="export_csv" download="AssetMasterList.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>	
			</div>
<!-- 			

	</section>
	
<section class="content">
		 -->
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li class="active"><a href="#Asset" data-toggle="tab" data-id="Asset">Asset Details</a></li>
				<li><a href="#Depreciation" data-toggle="tab" data-id="Depreciation">Depreciation</a></li>
				<!-- <li><a href="#Accounting" data-toggle="tab" data-id="Accounting">Accounting</a></li> -->
			</ul>
			<div class="tab-content no-padding">
				<div id="Asset" class="tab-pane table-responsive scroll active">
					<table id="tableListAsset" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Asset Number - Sub-Number',array('class'=>'col-md-1'),'sort','asset_number')
									// ->addHeader('Sub-Number',array('class'=>'col-md-1'),'sort','sub_number')
									->addHeader('Serial Number/ Engine Number',array('class'=>'col-md-1'),'sort','serial_number')
									->addHeader("Asset Class",array('class'=>'col-md-1'),'sort','assetclass')
									->addHeader('Description',array('class'=>'col-md-1'),'sort','description')
									->addHeader('Asset Location',array('class'=>'col-md-1'),'sort','asset_location')
									->addHeader('Budget Center',array('class'=>'col-md-1'),'sort','department')
									->addHeader('Accountable Person',array('class'=>'col-md-1'),'sort','accountable_person')
									->draw();
						?>
						</thead>
						<tbody>
							<!-- <?php foreach($asd->result as $row):?>
							<tr>
								<td class="text-left"><?php echo $row->itemcode; ?></td>
								<td class="text-left"><?php echo $row->assetclass; ?></td>
								<td class="text-left"><?php echo $row->asset_name; ?></td>
								<td class="text-left"><?php echo $row->asset_number; ?></td>
								<td class="text-left"><?php echo $row->sub_number; ?></td>
								<td class="text-left"><?php echo $row->serial_number; ?></td>
								<td class="text-left"><?php echo $row->description; ?></td>
								<td class="text-left"><?php echo $row->asset_location; ?></td>
								<td class="text-left"><?php echo $row->name; ?></td>
								<td class="text-left"><?php echo $row->accountable_person; ?></td>
								<td class="text-left"><?php echo date('M d, Y', strtotime($row->retirement_date)); ?></td>
								<td class="text-left"><?php echo date('M d, Y', strtotime($row->commissioning_date)); ?></td>
							</tr>
							<?php endforeach ?> -->
						</tbody>
						<tfoot></tfoot>
					</table>
				</div>
				<div id="Depreciation" class="tab-pane">
					<table id="tableListDepreciation" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Asset Number',array('class'=>'col-md-2'),'sort','asset_number')
									
									->addHeader("Capitalized Cost",array('class'=>'col-md-2 text-center'),'sort','capitalized_cost')
									->addHeader('Commissioning<br> Date',array('class'=>'col-md-1'),'sort','commissioning_date')
									->addHeader('No. of Months<br> Useful Life',array('class'=>'col-md-1'),'sort','useful_life')
									->addHeader("Depreciation Amount /<br>Month ",array('class'=>'col-md-1'),'sort','depreciation_amount')
									->addHeader("Depreciation<br>Month<br>Start ",array('class'=>'col-md-1'),'sort','depreciation_month')
									->addHeader('Retirement Date',array('class'=>'col-md-1'),'sort','retirement_date')
									->addHeader('Status',array('class'=>'col-md-2 text-center'),'sort','status')
									->addHeader('Accumulated<br>Depreciation',array('class'=>'col-md-2 text-center'),'sort','balance_value')
									->addHeader('Book Value',array('class'=>'col-md-2 text-center'),'sort','salvage_value')
									->draw();
						?>
						</thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				</div>
				<div id="Accounting" class="tab-pane">
					<table id="tableListAccounting" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Item Code',array('class'=>'col-md-2'),'sort','itemcode')
									->addHeader('GL Account(Asset)',array('class'=>'col-md-2'),'sort','asset')
									->addHeader("GL Account(Accumulated Depreciation)",array('class'=>'col-md-2'),'sort','accdep')
									->addHeader("GL Account(Depreciation Expense)",array('class'=>'col-md-2'),'sort','depexp')
									->draw();
						?>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>

		
	<script type="text/javascript">
		var ajax = {};
		var ajax_call = '';
		ajax.limit = 10;

		tableSort('#tableListAsset', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
		});

		tableSort('#tableListDepreciation', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
		});

		tableSort('#tableListAccounting', function(value) {
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

		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.tab = $(this).find('a').attr('data-id');
			getList();
		});
			
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list',ajax, function(data) {
				if(ajax.tab == 'Depreciation'){
					table = '#tableListDepreciation';
				}else{
					table = '#tableListAsset';
				}
				$(table+' tbody').html(data.table);
				$(table+' tfoot').html(data.footer);
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