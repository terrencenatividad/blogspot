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
				<div class="col-md-6"></div>
				<div class="col-md-3">
					<div class="form-group text-right">
						<button type="button" id="close_book" class="btn btn-primary" disabled><span class="glyphicon glyphicon-book"></span> Close Period<span></span></button>
						<a href="" id="export_csv" download="Trial_Balance.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
				</form>
			</div>	
		</div>
	</div>

	<div class="box-body table-responsive no-padding" id="report_content">
		<table id="balanceList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
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

<div id="alert_modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close " data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Warning!</h4>
			</div>
			<div class="modal-body">
				<p>You have not performed any closing activities for the Previous Month(s).</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="jvModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Information
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="jv_header">
					<!-- <div class="alert alert-warning alert-dismissable hidden" id="sequenceAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div> -->
					<div class="well well-md">
						<div class="row">
							<div class="col-md-12">
							<div class="row row-dense">
									<?php
										echo $ui->formField('text')
												->setLabel('Date')
												->setSplit('col-md-3','col-md-8')
												->setName('daterangefilter')
												->setId('daterangefilter')
												->setAttribute(array('data-daterangefilter' => 'month','disabled'))
												->setAddon('calendar')
												->setValidation('required')
												->draw(true);
									?>
								</div>
								<div class="row row-dense">
									<?php
										echo $ui->formField('text')
											->setLabel('Reference')
											->setSplit('col-md-3','col-md-8')
											->setName('reference')
											->setId('reference')
											->setValidation('required')
											->draw(true);
									?>
								</div>
								<div class="row row-dense">
									<?php
										echo $ui->formField('textarea')
											->setLabel('Notes')
											->setSplit('col-md-3','col-md-8')
											->setName("notes")
											->setId("notes")
											->setValidation('required')
											->draw(true);
									?>
								</div>
								<div class="row row-dense">
									<?php
									echo $ui->formField('dropdown')
											->setLabel('Retained Account')
											->setPlaceholder('Select an Account')
											->setSplit('col-md-3', 'col-md-8')
											->setName('retained_account')
											->setId('retained_account')
											->setList($chart_account_list)
											->setValidation('required')
											->setValue($retained_id)
											->draw(true);
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="row row-dense">
						<div class="col-md-12 text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-info" id="btnSaveDetails" >Save</button>
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<a href="javascript:void(0);" class="btn btn-small btn-default" role="button" data-dismiss="modal" style="outline:none;">
									Cancel
								</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<strong>Income Statement Entries</strong>
			</div>
			<div class="modal-header">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-11">
							<div class="row">
								<?php
									echo $ui->formField('text')
										->setLabel('Voucher No.')
										->setSplit('col-md-4', 'col-md-8')
										->setName('voucherno')
										->setId('voucherno')
										->setValidation('required')
										->draw();
								?>
								<?php
									echo $ui->formField('text')
										->setLabel('Transaction Date')
										->setSplit('col-md-4', 'col-md-8')
										->setName('transactiondate')
										->setId('transactiondate')
										->setClass('datepicker-input')
										->setAddon('calendar')
										->setValidation('required')
										->draw();
								?>
								<?php
									echo $ui->formField('text')
										->setLabel('Reference')
										->setSplit('col-md-4', 'col-md-8')
										->setName('referenceno')
										->setId('referenceno')
										->setValidation('required')
										->draw();
								?>
								<?php
									echo $ui->formField('dropdown')
										->setLabel('Proforma')
										->setPlaceholder('Select Proforma')
										->setSplit('col-md-4', 'col-md-8')
										->setName('proformacode')
										->setId('proformacode')
										->setList($proforma_list)
										->draw();
								?>
								<?php
									echo $ui->formField('textarea')
										->setLabel('Notes')
										->setSplit('col-md-4', 'col-md-8')
										->setName('remarks')
										->setId('remarks')
										->draw();
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body">	
				<div class="box-body table-responsive no-padding" id="content">
					<table id="is_list" class="table table-hover table-sidepad" cellpadding="0" cellspacing="0" border="0" width="100%">
						<thead>
							<tr class="info">
								<th class="col-md-3 text-left">Account</th>
								<th class="col-md-3 text-left">Description</th>
								<th class="col-md-3 text-right">Debit</th>
								<th class="col-md-3 text-right">Credit</th>
							</tr>
						</thead>
						<tbody id="ic_rows">
							
						</tbody>
					</table>
					<div id="pagination"></div>	
					<div class="box-body">
						<hr>
						<div class="row">
							<div class="col-md-12 text-center">
								<button class="btn btn-primary" id="confirmbtn">Confirm</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var ajax = {};
	var ajax2 = {};
		ajax.limit 	= 20; 
	var ajax_call 	= {};

	/**JSON : RETRIEVE TRANSACTIONS**/
	function openList(acct)
	{
		var x			= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
		var datefilter	= document.getElementById('daterangefilter').value;
		var items 		= document.getElementById('items').value;
		
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

	function check_existing_jv(end){
		ajax2.trans_date 	=	end;
		$.post('<?=MODULE_URL?>ajax/check_existing_jv', ajax2 , function(response) {
			if( response.existing == 0 ){
				$('#alert_modal').modal('show');
				// $('#close_book').prop('disabled',true);
			} else {
				// $('#close_book').prop('disabled',false);
			}
		});
	}

	function enable_button(daterange){
		var date_array 	= daterange.split('-');

		var start 	   	= date_array[0];
			start 		= new Date(Date.parse(start));

		var end 	   	= date_array[1];
			end 		= new Date(Date.parse(end));

		var n_start 	= start.getMonth()+1;
		var n_start_day = start.getDate();
		var n_start_yr	= start.getFullYear();	
		
		var n_end 		= end.getMonth()+1;
		var n_end_day 	= end.getDate();
		var n_end_yr	= end.getFullYear();
		
		var lastday = function(y,m){
			return  new Date(y, m, 0).getDate();
		}
		var m_lastday 	=	lastday(n_end_yr,n_end);
		
		if( n_start == n_end && m_lastday == n_end_day ){
			$('#close_book').prop('disabled',false);
		} else {
			$('#close_book').prop('disabled',true);
			// check_existing_jv(date_array[1]);
		}

		check_existing_jv(date_array[1]);
	}

	function preview_jv(voucherno){
		ajax2.voucherno 		=	voucherno;
		$("#jv_voucher") 
		$.post('<?=MODULE_URL?>ajax/preview_listing', ajax2 , function(response) {
			//Header
			$('#previewModal #voucherno').val(response.voucherno);
			$("#previewModal #transactiondate").val(response.transactiondate);
			$('#previewModal #referenceno').val(response.reference);
			$("#previewModal #proforma").val(response.proformacode);
			$("#previewModal #remarks").val(response.remarks);

			//Details
			$('#previewModal #ic_rows').html(response.table);
			$("#previewModal #pagination").html(response.pagination);

			$("#previewModal").modal('show');
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
		enable_button($(this).val());
	}).trigger('change');
	
	$('#close_book').on('click',function(){
		var daterangefilter 	=	$('#daterangefilter').val();
		$('#jvModal #daterangefilter').val(daterangefilter);
		$("#jvModal").modal('show');
	});
	
	$('#jv_header').on('click',"#btnSaveDetails", function(){
		ajax2.daterangefilter 	=	$('#daterangefilter').val();
		ajax2.reference 		=	$('#jvModal #reference').val();
		ajax2.notes 			=	$('#jvModal #notes').val();
		ajax2.retained_acct 	=	$('#jvModal #retained_account').val();

		$.post('<?=MODULE_URL?>ajax/temporary_jv_save', ajax2 , function(response) {
			if( response.result ){
				$("#jvModal").modal('hide');
				preview_jv(response.voucherno);
			}
		});
	});
</script>