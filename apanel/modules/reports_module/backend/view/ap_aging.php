<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post" id="arForm">
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
									->setName('customer')
									->setId('customer')
									->setList($customer_list)
									->setAttribute(array("onChange" => "getList();"))
									->setNone('Filter: All')
									->draw();
						?>
					</div>
					<div class="col-md-3">
						<?php
							echo $ui->formField('text')
									->setSplit('', 'col-md-12 date')
									->setName('daterangefilter')
									->setId('daterangefilter')
									->setValue($datefilter)
									->setValidation('required')
									->setClass('date')
									->setAttribute(
										array(
											'readOnly' => 'readOnly'
										)
									)
									->setAddon('calendar')
									->draw($show_input);
						?>
					</div>
					<div class="col-md-5"></div>
					<div class="col-md-1">
						<a href="" id="export_csv" download="AP_Aging_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="info">
					<th class="col-md-2 text-center">Vendor</th>
					<th class="col-md-2 text-center">Reference</th>
					<th class="col-md-1 text-center">Terms</th>
					<th class="col-md-1 text-center">Due Date</th>
					<th class="col-md-1 text-center">Current</th>
					<th class="col-md-1 text-center">1 - 30 Days</th>
					<th class="col-md-1 text-center">31 -60 Days</th>
					<th class="col-md-1 text-center">Over 60 Days</th>
					<th class="col-md-1 text-center">Balance</th>
				</tr>
			</thead>
			<tbody id="ap_container">
				
			</tbody>
		</table>
		<div id="pagination"></div>	
	</div>
</section>

	<script type="text/javascript">
		var ajax = {}
			ajax.limit = 10;
		var ajax_call = {};
			
		function getList() 
		{
			ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) 
			{
				$('#ap_container').html(data.table);
				$('#pagination').html(data.pagination);
				$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}

		$('#pagination').on('click', 'a', function(e) 
		{
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});

		$("#customer").on("change",function(){
			ajax.customer= $(this).val();
			getList();
		});

		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			try {
				ajax_call.abort();
			} catch (e) {}
			getList();
		}).trigger('change');
		
		//Date picker
		$('.date').datepicker({
			autoclose: true,
			"setDate": today(),
			format: "M dd, yyyy",
			multidateSeparator: '|'
		});

		function today()
		{
			var m_names = new Array("Jan", "Feb", "Mar", 
			"Apr", "May", "Jun", "Jul", "Aug", "Sep", 
			"Oct", "Nov", "Dec");

			var d = new Date();
			var curr_date = d.getDate();
			var curr_month = d.getMonth();
			var curr_year = d.getFullYear();
			var today	= m_names[curr_month]+" "+curr_date+", "+ curr_year;
			
			return today;
		}
		
	</script>