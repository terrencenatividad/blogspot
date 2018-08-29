	<form method="POST" id="income_statement_form">
	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-3 col-sm-6 col-xs-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Filter Year')
								->setPlaceholder('Filter Year')
								->setName('year_filter')
								->setId('year_filter')
								->setList($year_list)
								->setValue($year)
								->draw();
						?>
					</div>
					<div class="col-md-9 col-sm-6 col-xs-6 text-right">
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
								<?php foreach ($header_monthly as $month): ?>
									<th class="text-right"><?php echo $month ?></th>
								<?php endforeach ?>
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
								<?php foreach ($header_quarterly as $quarter): ?>
									<th class="text-right"><?php echo $quarter ?></th>
								<?php endforeach ?>
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
								<?php foreach ($header_yearly as $year): ?>
									<th class="text-right"><?php echo $year ?></th>
								<?php endforeach ?>
							</tr>
						</thead>
						<?php echo $year_view ?>
					</table>
				</div>
			</div>
		</div>
	</section>
	</form>
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