
	<section class="content">
		<div class="row">
			<div class="col-md-3">
				<div class="small-box bg-aqua">
					<div class="inner">
						<h3>150</h3>
						<p>Invoices</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-shopping-cart"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="#" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="#" class="small-box-footer">Create New Invoice <i class="fa fa-arrow-circle-right"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3>150</h3>
						<p>Purchases</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-tags"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="#" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="#" class="small-box-footer">Create New Purchase <i class="fa fa-arrow-circle-right"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-green">
					<div class="inner">
						<h3>150</h3>
						<p>Bills</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="#" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="#" class="small-box-footer">Create New Bill <i class="fa fa-arrow-circle-right"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="small-box bg-red">
					<div class="inner">
						<h3>150</h3>
						<p>Journal Vouchers</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-briefcase"></i>
					</div>
					<div class="row">
						<div class="col-md-5">
							<a href="#" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-md-7">
							<a href="#" class="small-box-footer">Create New Journal <i class="fa fa-arrow-circle-right"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs pull-right">
						<li class="active"><a href="#revenue-chart" data-toggle="tab" aria-expanded="true">2017</a></li>
						<li class=""><a href="#sales-chart" data-toggle="tab" aria-expanded="false">2016</a></li>
						<li class="pull-left header">Revenue vs Expense</li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs pull-right">
						<li>
							<a href="report" aria-expanded="true">View Report</a>
						</li>
						<li class="active"><a href="#revenue-chart2" data-toggle="tab" aria-expanded="true">Accounts Payable</a></li>
						<li class=""><a href="#sales-chart2" data-toggle="tab" aria-expanded="false">Accounts Receivable</a></li>
						<li class="pull-left header">Aging </li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="revenue-chart2" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="sales-chart2" style="position: relative; height: 300px;"></div>
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
						<div class="chart" id="line-chart" style="height: 250px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-solid bg-yellow-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Sales Graph</h3>
					</div>
					<div class="box-body border-radius-none">
						<div class="chart" id="line-chart2" style="height: 250px;"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
		new Morris.Line({
			element: 'revenue-chart',
			data: [
				{ year: '2008', value: 20 },
				{ year: '2009', value: 10 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			hideHover: true
		});
		new Morris.Line({
			element: 'sales-chart',
			data: [
				{ year: '2008', value: 30 },
				{ year: '2009', value: 1 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			hideHover: true
		});
		new Morris.Line({
			element: 'revenue-chart2',
			data: [
				{ year: '2008', value: 20 },
				{ year: '2009', value: 10 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			hideHover: true
		});
		new Morris.Line({
			element: 'sales-chart2',
			data: [
				{ year: '2008', value: 30 },
				{ year: '2009', value: 1 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			hideHover: true
		});

		new Morris.Line({
			element: 'line-chart',
			data: [
				{ year: '2008', value: 30 },
				{ year: '2009', value: 1 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			gridTextColor: '#efefef',
			lineColors: ['#efefef'],
			gridLineColor: ['#efefef'],
			hideHover: true
		});
		new Morris.Line({
			element: 'line-chart2',
			data: [
				{ year: '2008', value: 30 },
				{ year: '2009', value: 1 },
				{ year: '2010', value: 5 },
				{ year: '2011', value: 5 },
				{ year: '2012', value: 20 }
			],
			xkey: 'year',
			ykeys: ['value'],
			labels: ['Value'],
			gridTextColor: '#efefef',
			lineColors: ['#efefef'],
			gridLineColor: ['#efefef'],
			hideHover: true
		});
	</script>