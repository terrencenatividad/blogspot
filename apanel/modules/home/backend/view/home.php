
	<section class="content">
		<div class="row">
			<div class="col-md-3">
				<div class="small-box bg-aqua">
					<div class="inner">
						<h3><?php echo $invoices ?></h3>
						<p>Invoices</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-shopping-cart"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="<?php echo BASE_URL ?>sales/sales_invoice/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="<?php echo BASE_URL ?>sales/sales_invoice/create" class="small-box-footer">Create New Invoice <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo $purchases ?></h3>
						<p>Purchases</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-tags"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="<?php echo BASE_URL ?>purchase/purchase_receipt/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="<?php echo BASE_URL ?>purchase/purchase_receipt/create" class="small-box-footer">Create New Purchase <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-green">
					<div class="inner">
						<h3><?php echo $billings ?></h3>
						<p>Bills</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="<?php echo BASE_URL ?>billing/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="<?php echo BASE_URL ?>billing/create" class="small-box-footer">Create New Bill <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-red">
					<div class="inner">
						<h3><?php echo $journalvouchers ?></h3>
						<p>Journal Vouchers</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-briefcase"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="<?php echo BASE_URL ?>financials/journal_voucher/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="<?php echo BASE_URL ?>financials/journal_voucher/create" class="small-box-footer">Create New Journal <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs pull-right">
						<li class="active"><a href="#current_year" data-toggle="tab" aria-expanded="true">2017</a></li>
						<li class=""><a href="#previous_year" class="previous_year" data-toggle="tab" aria-expanded="false">2016</a></li>
						<li class="pull-left header">Revenue vs Expense</li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="current_year" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="previous_year" style="position: relative; height: 300px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="nav-tabs-custom">
					<ul id="aging_nav" class="nav nav-tabs pull-right">
						<li>
							<a href="<?php echo BASE_URL ?>accounts_payable" id="report_aging" aria-expanded="true">View Report</a>
						</li>
						<li class="active"><a href="#accounts_payable" data-toggle="tab" aria-expanded="true" data-url="<?php echo BASE_URL ?>agingpayable">Accounts Payable</a></li>
						<li class=""><a href="#accounts_receivable" class="accounts_receivable" data-toggle="tab" aria-expanded="false" data-url="<?php echo BASE_URL ?>agingreceivable">Accounts Receivable</a></li>
						<li class="pull-left header">Aging </li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="accounts_payable" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="accounts_receivable" style="position: relative; height: 300px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-solid bg-teal-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Sales Graph</h3>
					</div>
					<div class="box-body border-radius-none">
						<div class="chart" id="sales" style="height: 250px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-solid bg-yellow-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Purchases Graph</h3>
					</div>
					<div class="box-body border-radius-none">
						<div class="chart" id="purchases" style="height: 250px;"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
		var revenue_expense = <?php echo $revenue_expense ?>;
		var aging = <?php echo $aging ?>;
		var sales_purchases = <?php echo $sales_purchases ?>;

		$('#aging_nav').on('click', '[data-toggle="tab"]', function() {
			var url = $(this).attr('data-url');
			$('#report_aging').attr('href', url);
		});

		new Morris.Line({
			element: 'current_year',
			data: revenue_expense['<?php echo date('Y') ?>'],
			xkey: 'year',
			ykeys: ['expense', 'revenue'],
			labels: ['Expense', 'Revenue'],
			hideHover: true
		});
		$('.previous_year').one('click', function() {
			setTimeout(function(){
				new Morris.Line({
					element: 'previous_year',
					data: revenue_expense['<?php echo date('Y') - 1 ?>'],
					xkey: 'year',
					ykeys: ['expense', 'revenue'],
					labels: ['Expense', 'Revenue'],
					hideHover: true
				});
			}, 10);
		});
		new Morris.Donut({
			element: 'accounts_payable',
			data: aging.ap,
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			hideHover: true
		});
		$('.accounts_receivable').one('click', function() {
			setTimeout(function(){
				new Morris.Donut({
					element: 'accounts_receivable',
					data: aging.ar
				});
			}, 10);
		});

		new Morris.Line({
			element: 'sales',
			data: sales_purchases.sales,
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			gridTextColor: '#efefef',
			lineColors: ['#efefef'],
			gridLineColor: ['#efefef'],
			hideHover: true
		});
		new Morris.Line({
			element: 'purchases',
			data: sales_purchases.purchases,
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			gridTextColor: '#efefef',
			lineColors: ['#efefef'],
			gridLineColor: ['#efefef'],
			hideHover: true
		});
	</script>