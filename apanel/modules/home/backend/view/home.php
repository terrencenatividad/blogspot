
	<section class="content">
		<div class="row">
			<div class="col-md-3 col-sm-6">
				<div class="small-box bg-aqua">
					<div class="inner">
						<h3><?php echo $invoices ?></h3>
						<p>Invoices</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-shopping-cart"></i>
					</div>
					<div class="row">
						<div class="col-xs-5">
							<a href="<?php echo BASE_URL ?>sales/sales_invoice/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-xs-7">
							<a href="<?php echo BASE_URL ?>sales/sales_invoice/create" class="small-box-footer"><span class="hidden-md">Create </span>New Invoice <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo $purchases ?></h3>
						<p>Purchases</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-tags"></i>
					</div>
					<div class="row">
						<div class="col-xs-5">
							<a href="<?php echo BASE_URL ?>purchase/purchase_receipt/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-xs-7">
							<a href="<?php echo BASE_URL ?>purchase/purchase_receipt/create" class="small-box-footer"><span class="hidden-md">Create </span>New Purchase <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="small-box bg-green">
					<div class="inner">
						<h3><?php echo $items ?></h3>
						<p>Items</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-equalizer"></i>
					</div>
					<div class="row">
						<div class="col-xs-5">
							<a href="<?php echo BASE_URL ?>maintenance/item/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-xs-7">
							<a href="<?php echo BASE_URL ?>maintenance/item/create" class="small-box-footer"><span class="hidden-md">Create </span>New Item <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="small-box bg-red">
					<div class="inner">
						<h3><?php echo $journalvouchers ?></h3>
						<p>Journal Vouchers</p>
					</div>
					<div class="icon">
						<i class="glyphicon glyphicon-briefcase"></i>
					</div>
					<div class="row">
						<div class="col-xs-5">
							<a href="<?php echo BASE_URL ?>financials/journal_voucher/" class="small-box-footer">View List <i class="fa fa-arrow-circle-right"></i></a>
						</div>
						<div class="col-xs-7">
							<a href="<?php echo BASE_URL ?>financials/journal_voucher/create" class="small-box-footer"><span class="hidden-md">Create </span>New Journal <i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- <div class="col-md-6">
				<div class="box box-solid bg-teal-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Sales Graph <?php echo $year ?></h3>
					</div>
					<div class="box-body border-radius-none no-padding">
						<div class="chart" id="sales" style="height: 250px;"></div>
					</div>
				</div>
			</div> -->
			<div class="col-md-6">
				<div class="box box-solid bg-blue-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Gross Sales Graph <?php echo $year ?></h3>
					</div>
					<div class="box-body border-radius-none no-padding">
						<div class="chart" id="sales_summary" style="height: 250px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-solid bg-yellow-gradient">
					<div class="box-header">
						<i class="fa fa-th"></i>
						<h3 class="box-title">Purchases Graph <?php echo $year ?></h3>
					</div>
					<div class="box-body border-radius-none no-padding">
						<div class="chart" id="purchase_summary" style="height: 250px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs pull-right">
						<li class="active"><a href="#current_year" data-toggle="tab" aria-expanded="true"><?php echo $year ?></a></li>
						<li class=""><a href="#previous_year" class="previous_year" data-toggle="tab" aria-expanded="false"><?php echo $year - 1 ?></a></li>
						<li class="pull-left header">Revenue vs Cost of Sales</li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="current_year" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="previous_year" style="position: relative; height: 300px;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="nav-tabs-custom">
					<ul id="aging_nav" class="nav nav-tabs pull-right">
						<li>
							<a href="<?php echo BASE_URL ?>report/ap_aging" id="report_aging" aria-expanded="true">View Report</a>
						</li>
						<li class="active"><a href="#accounts_payable" data-toggle="tab" aria-expanded="true" data-url="<?php echo BASE_URL ?>report/ap_aging">Accounts Payable</a></li>
						<li class=""><a href="#accounts_receivable" class="accounts_receivable" data-toggle="tab" aria-expanded="false" data-url="<?php echo BASE_URL ?>report/ar_aging">Accounts Receivable</a></li>
						<li class="pull-left header">Aging </li>
					</ul>
					<div class="tab-content no-padding">
						<div class="chart tab-pane active" id="accounts_payable" style="position: relative; height: 300px;"></div>
						<div class="chart tab-pane" id="accounts_receivable" style="position: relative; height: 300px;"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
		var revenue_expense = '';
		var aging = '';
		var sales_purchases = '';
		var sales = '';
		var purchase = '';
		var shortMonth	= ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var fullMonth	= ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

		$.post('<?php echo MODULE_URL ?>ajax/get_chart', function(data) {
			revenue_expense = data.revenue_expense;
			aging = data.aging;
			sales_purchases = data.sales_purchases;
			sales = data.sales;
			purchase = data.purchase;
			showChart();
		});

		$('#aging_nav').on('click', '[data-toggle="tab"]', function() {
			var url = $(this).attr('data-url');
			$('#report_aging').attr('href', url);
		});
		
		function showChart() {
			new Morris.Bar({
				gridTextSize: 12,
				element: 'current_year',
				data: revenue_expense.current,
				xkey: 'month',
				ykeys: ['revenue', 'expense'],
				labels: ['Revenue', 'Cost of Sales'],
				barColors : ['#0071b3','#f8ac4b'],
				hideHover: true,
				xLabelFormat: function (x){
					var month = shortMonth[new Date(x.label).getMonth()];
					return month;
				},
				yLabelFormat: function(y) {
					let val = parseFloat(y);
					return val.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				}
			});
			$('.previous_year').one('click', function() {
				setTimeout(function(){
					new Morris.Bar({
						gridTextSize: 12,
						element: 'previous_year',
						data: revenue_expense.previous,
						xkey: 'month',
						ykeys: ['revenue', 'expense'],
						labels: ['Revenue', 'Cost of Sales'],
						barColors : ['#0071b3','#f8ac4b'],
						hideHover: true,
						xLabelFormat: function (x){
							var month = shortMonth[new Date(x.label).getMonth()];
							return month;
						},
						yLabelFormat: function(y) {
							let val = parseFloat(y);
							return val.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
						}
					});
				}, 10);
			});
			new Morris.Bar({
				element: 'accounts_payable',
				data: aging.ap,
				xkey: 'label',
				ykeys: ['value'],
				labels: ['Expense'],
				barColors: ['#f56954']
			});
			$('.accounts_receivable').one('click', function() {
				setTimeout(function(){
					new Morris.Bar({
						element: 'accounts_receivable',
						data: aging.ar,
						xkey: 'label',
						ykeys: ['value'],
						labels: ['Revenue'],
						barColors: ['#00c0ef']
					});
				}, 10);
			});
			
			// new Morris.Line({
			// 	gridTextSize: 8,
			// 	element: 'sales',
			// 	data: sales_purchases.sales,
			// 	xkey: 'month',
			// 	ykeys: ['value'],
			// 	labels: ['Value'],
			// 	gridTextColor: '#efefef',
			// 	lineColors: ['#efefef'],
			// 	gridLineColor: ['#efefef'],
			// 	hideHover: true,
			// 	xLabelFormat: function (x){
			// 		var month = shortMonth[new Date(x).getMonth()];
			// 		return month;
			// 	},
			// 	dateFormat: function (x) { 
			// 		var month = fullMonth[new Date(x).getMonth()];
			// 		return month;
			// 	},
			// 	yLabelFormat: function(y) {
			// 		let val = parseFloat(y);
			// 		return val.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
			// 	}
			// });
			/**	
			 * Cystiner
			 */
			new Morris.Line({
				gridTextSize: 8,
				element: 'sales_summary',
				data: sales.sales,
				xkey: 'month',
				ykeys: ['equipment','parts','service'],
				labels: ['Equipment','Parts','Service'],
				gridTextColor: '#efefef',
				lineColors: ['#f4415b','#efefef','yellow'],
				gridLineColor: ['#efefef'],
				hideHover: true,
				xLabelFormat: function (x){
					var month = shortMonth[new Date(x).getMonth()];
					return month;
				},
				dateFormat: function (x) { 
					var month = fullMonth[new Date(x).getMonth()];
					return month;
				},
				yLabelFormat: function(y) {
					let val = parseFloat(y);
					return val.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				}
			});
			new Morris.Line({
				gridTextSize: 8,
				element: 'purchase_summary',
				data: purchase.purchase,
				xkey: 'month',
				ykeys: ['equipment','parts'],
				labels: ['Equipment','Parts'],
				gridTextColor: '#efefef',
				lineColors: ['#f4415b','#efefef'],
				gridLineColor: ['#efefef'],
				hideHover: true,
				xLabelFormat: function (x){
					var month = shortMonth[new Date(x).getMonth()];
					return month;
				},
				dateFormat: function (x) { 
					var month = fullMonth[new Date(x).getMonth()];
					return month;
				},
				yLabelFormat: function(y) {
					let val = parseFloat(y);
					return val.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				}
			});
		}
	</script>