<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post" id="arForm">
				<div class="col-md-3">
					<?php
						echo $ui->formField('dropdown')
							//->setPlaceholder('Filter Customer')
							->setName('customer')
							->setId('customer')
							->setList($customer_list)
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
					
						// echo $ui->formField('text')
						// 		->setName('daterangefilter')
						// 		->setId('daterangefilter')
						// 		->setAttribute(array('data-daterangefilter' => 'month'))
						// 		->setAddon('calendar')
						// 		->setValue($datefilter)
						// 		->setValidation('required')
						// 		->draw(true);
					?>
				</div>
				<div class="col-md-5"></div>
				<div class="col-md-1">
					<a href="" id="export_csv" download="AR_Aging_Report.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="info">
					<th class="col-md-2 text-center">Customer</th>
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
			<tbody id="ar_container">
				
			</tbody>
		</table>
		<div id="pagination"></div>	
	</div>
</section>
<!--DETAIL MODAL-->
	<div class="modal fade" id="listModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<?
			/**ITEM OPTIONS**/
			$itemArray	= array("10"=>"10","20"=>"20","50"=>"50","100"=>"100");
			?>
			<div class="modal-content">
				<div class="modal-header">
					List of Transactions
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="col-md-12">
						<?php 
								echo $ui->formField('dropdown')
									->setLabel('Display: ')
									->setSplit('col-md-10 text-right', 'col-md-2 pull-right')
									->setName('items')
									->setId('items')
									//->setAttribute(array("onChange" => "showList();"))
									->setList($itemArray)
									->setValue("10")
									->draw($show_input);
						?>
					</div>
					<br/>
					<br/>
					<table class="table table-condensed table-hover table-bordered">
						<thead>
							<tr class="info">
								<th class="col-md-2 text-center">Reference</th>
								<th class="col-md-2 text-center">Date</th>
								<th class="col-md-2 text-center">Debit</th>
								<th class="col-md-2 text-center">Credit</th>
							</tr>
						</thead>
						<tbody id="list_container">
							<tr>
								<td class="center" style="vertical-align:middle;" colspan="4">- No Records Found -</td>
							</tr>
						</tbody>
						<!--<tfoot>
							<tr class="">
								<td class="center" id="page_info">&nbsp;</td>
								<td class="center" id="page_links" colspan="3"></td>
							</tr>
						</tfoot>-->
					</table>
				</div>
				
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var ajax = {}
			ajax.limit = 10;
		var ajax_call = {};
			
		function getList() {
			ajax_call = $.post('<?=MODULE_URL?>ajax/list',ajax, function(data) {
				 	$('#ar_container').html(data.table);
					$('#pagination').html(data.pagination);
					$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			});
		}

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});

		$("#customer").on("change",function(){
			ajax.customer=$(this).val();
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