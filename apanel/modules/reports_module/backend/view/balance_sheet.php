	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-2 col-sm-4 col-xs-6">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Year')
								->setName('year_filter')
								->setId('year_filter')
								->setList($year_list)
								->setValue($year)
								->draw();
						?>
					</div>
					<div class="col-md-10 col-sm-8 col-xs-6 text-right">
						<div class="form-group">
							<?php
								echo $ui->setElement('button')
										->setId('export')
										->setPlaceholder('<i class="glyphicon glyphicon-export"></i> Export')
										->draw();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li><a href="#Monthly" data-toggle="tab" data-id="Monthly">Monthly</a></li>
				<li><a href="#Quarterly" data-toggle="tab" data-id="Quarterly">Quarterly</a></li>
				<li class="active"><a href="#Yearly" data-toggle="tab" data-id="Yearly">Yearly</a></li>
			</ul>
			<div class="tab-content no-padding">
				<div id="Monthly" class="tab-pane table-responsive scroll">
					<table id="tableListMonthly" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
							<tr class="info">
								<th style="col-xs-4">Account</th>
								<th class="text-right">Jan</th>
								<th class="text-right">Feb</th>
								<th class="text-right">Mar</th>
								<th class="text-right">Apr</th>
								<th class="text-right">May</th>
								<th class="text-right">Jun</th>
								<th class="text-right">Jul</th>
								<th class="text-right">Aug</th>
								<th class="text-right">Sep</th>
								<th class="text-right">Oct</th>
								<th class="text-right">Nov</th>
								<th class="text-right">Dec</th>
							</tr>
						</thead>
						<?php echo $monthly_view ?>
					</table>
				</div>
				<div id="Quarterly" class="tab-pane">
					<table id="tableListQuarterly" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Account</th>
								<th class="text-right">1st Quarter (Jan - Mar)</th>
								<th class="text-right">2nd Quarter (Apr - Jun)</th>
								<th class="text-right">3rd Quarter (Jul - Sep)</th>
								<th class="text-right">4th Quarter (Oct - Dec)</th>
							</tr>
						</thead>
						<?php echo $quarterly_view ?>
					</table>
				</div>
				<div id="Yearly" class="tab-pane active">
					<table id="tableListYearly" class="table table-hover table-striped table-sidepad report_table text-right">
						<thead>
							<tr class="info">
								<th class="col-xs-4">Account</th>
								<th class="text-right"><?php echo $year ?></th>
								<th class="text-right"><?php echo $year - 1 ?></th>
							</tr>
						</thead>
						<?php echo $year_view ?>
					</table>
				</div>
			</div>
		</div>
	</section>
	<script>
		var year_filter = $('#year_filter').val();
		var tab = $('#filter_tabs li.active a').attr('data-id');
		$('#year_filter').on('change', function() {
			year_filter = $(this).val();
			window.location = '<?php echo MODULE_URL ?>view/' + year_filter;
		});
		$('#filter_tabs').on('click', 'li', function() {
			tab = $(this).find('a').attr('data-id');
		});
		$('#export').click(function() {
			window.location = '<?php echo MODULE_URL ?>view_export/' + year_filter + '/' + tab;
		});
	</script>