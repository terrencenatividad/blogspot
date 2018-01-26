<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post">
				<div class="col-md-3">
					<?php
						echo $ui->formField('text')
								->setName('daterangefilter')
								->setId('daterangefilter')
								->setAttribute(array('data-daterangefilter' => 'month'))
								->setAddon('calendar')
								->setValue($datefilter)
								->setValidation('required')
								->draw(true);
					?>
				</div>
				<div class="col-md-8"></div>
				<div class="col-md-1">
					<a href="" id="export_csv" download="Trial_Balance.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
				</div>
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="tableList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="info">
					<th class="col-md-1 text-center">Item Code</th>
					<th class="col-md-2 text-center">Account Name</th>
					<th class="col-md-1 text-center">Prev Carryforward</th>
					<th class="col-md-1 text-center">Balance Carryforward</th>
					<th class="col-md-1 text-center">Total Debit</th>
					<th class="col-md-1 text-center">Total Credit</th>
					<th class="col-md-1 text-center">Balance for the Period</th>
					<th class="col-md-1 text-center">Accumulated Balance</th>
				</tr>
			</thead>
			<tbody id="trial_container">
				
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
			ajax.limit = 20; 
		var ajax_call = {};

		/**JSON : RETRIEVE TRANSACTIONS**/
		function openList(acct)
		{
			var x			= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
			var datefilter	= document.getElementById('daterangefilter').value;
			var items 		= document.getElementById('items').value;
			//console.log(datefilter);
			//console.log(items);
			var sortCol		= '';
			var sortBy		= '';
		
			var data		= "daterangefilter="+datefilter+"&accountcode="+acct+"&items="+items;
			$.post('<?=MODULE_URL?>ajax/load_account_transactions',data, function(response) {
			var jsondata	= response;
			 	$("#listModal .modal-header").html('<strong>'+jsondata.title+'</strong>'+x);
			 	$('#list_container').html(jsondata.table);
			 	$('#acct').val(acct);
			
			 	$("#listModal").modal('show');
			});
		}

		function getTrialBalance(){
			ajax.daterangefilter = $("#daterangefilter").val();
			ajax_call = $.post('<?=MODULE_URL?>ajax/list', ajax , function(data) {
					$('#trial_container').html(data.table);
					$("#pagination").html(data.pagination);
					$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
				});
		}

		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getTrialBalance();
		});

		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			ajax.page = 1;
			getTrialBalance();
		}).trigger('change');

	</script>