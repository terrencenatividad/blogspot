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
				<li class="active"><a href="#Monthly" data-toggle="tab" data-id="Monthly">Monthly</a></li>
				<li><a href="#Quarterly" data-toggle="tab" data-id="Quarterly">Quarterly</a></li>
				<li><a href="#Yearly" data-toggle="tab" data-id="Yearly">Yearly</a></li>
			</ul>
			<div class="tab-content no-padding">
				<div id="Monthly" class="tab-pane active table-responsive scroll">
					<table id="tableListMonthly" class="table table-hover table-striped report_table text-right">
						<thead>
							<tr class="info">
								<th style="col-xs-9" >
									<?php
										echo $ui->formField('dropdown')
											->setSplit('col-md-1','col-md-3')
											->setName('month_filter')
											->setId('month_filter')
											->setList($month_list)
											->setValue($month)
											->draw();
									?>
								</th>
								<th style="col-xs-3" ></th>
							</tr>
						</thead>
						<?php echo $monthly_view ?>
					</table>
				</div>
				<div id="Quarterly" class="tab-pane">
					<table id="tableListQuarterly" class="table table-hover table-striped report_table text-right">
						<thead>
							<tr class="info">
								<th class="col-xs-3">&nbsp;</th>
								<th class="text-right">1st Quarter (Jan - Mar)</th>
								<th class="text-right">2nd Quarter (Apr - Jun)</th>
								<th class="text-right">3rd Quarter (Jul - Sep)</th>
								<th class="text-right">4th Quarter (Oct - Dec)</th>
							</tr>
						</thead>
						<?php echo $quarterly_view ?>
					</table>
				</div>
				<div id="Yearly" class="tab-pane">
					<table id="tableListYearly" class="table table-hover table-striped report_table text-right">
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
	</form>
	<script>
		var month 	= $('#month_filter').val();
		var year 	= $('#year_filter').val();
		
		$('body').on('change', '#year_filter, #month_filter', function() {
			$('#income_statement_form').submit();
		});
		$('#export').click(function() {
			var tab 		= $('#filter_tabs li.active a').attr('data-id');
			window.location = '<?php echo MODULE_URL ?>view_export/' + year + '/' + tab + '/' + month;
		});
	</script>