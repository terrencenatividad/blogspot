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
						<?if($display_btn):?>
						<button type="button" id="close_book" class="btn btn-primary"><span class="glyphicon glyphicon-book"></span> Close Period<span></span></button>
						<?endif;?>
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

<div id="alert_modal" class="modal fade" tabindex="-1" role="dialog" style="z-index:10000;">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close " data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Warning!</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label col-md-12">Closing Period</label>
							</div>
						</div>
						<div class="col-md-8" style="margin:0px;">
						<?php
							echo $ui->formField('text')
									->setSplit('','col-md-12')
									->setName('datefrom')
									->setId('datefrom')
									// ->setClass('datefilter')
									// ->setAddon('calendar')
									->setAttribute(array('readonly'))
									->setValidation('required')
									->setValue($datafrom)
									->draw(true);
						?>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
								<label class="control-label col-md-12">Date To</label>
							</div>
						</div>
						<div class="col-md-8" style="margin:0px;">
						<?
							// echo $ui->formField('dropdown')
							// 		->setSplit('','col-md-12')
							// 		->setName('dateto')
							// 		->setId('dateto')
							// 		->setClass('datefilter')
							// 		// ->setAddon('calendar')
							// 		->setValidation('required')
							// 		->setList($openmonth_list)
							// 		->draw(true);
						?>
						</div> -->
					</div>
					<?php
						// echo $ui->formField('text')
						// 		->setLabel('Reference')
						// 		->setSplit('col-md-3','col-md-8')
						// 		->setName('reference')
						// 		->setId('reference')
						// 		->setValidation('required')
						// 		->draw(true);

						echo $ui->formField('textarea')
								->setLabel('Notes')
								->setSplit('col-md-3','col-md-8')
								->setName("notes")
								->setId("notes")
								// ->setValidation('required')
								->draw(true);
								
						echo $ui->formField('dropdown')
								->setLabel('Closing Account')
								->setPlaceholder('Select an Account')
								->setSplit('col-md-3', 'col-md-8')
								->setName('closing_account')
								->setId('closing_account')
								->setList($chart_account_list)
								->setValidation('required')
								->addHidden()
								->setValue($is_account)
								->draw(true);
					?>
					<br>
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

<div class="modal fade" id="previewModal" tabindex="-1" data-backdrop="static" data-keyboard="false" >
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
										->addHidden('voucherno')
										->setValidation('required')
										->setValue("--Auto Generated--")
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
										->addHidden('transactiondate')
										->setValidation('required')
										->draw();
								?>
								<?php
									echo $ui->formField('text')
										->setLabel('Reference')
										->setSplit('col-md-4', 'col-md-8')
										->setName('referenceno')
										->setId('referenceno')
										->addHidden('referenceno')
										->setValidation('required')
										->draw();
								?>
								<?php
									// echo $ui->formField('dropdown')
									// 	->setLabel('Proforma')
									// 	->setPlaceholder('Select Proforma')
									// 	->setSplit('col-md-4', 'col-md-8')
									// 	->setName('proformacode')
									// 	->setId('proformacode')
									// 	->addHidden('proformacode')
									// 	->setList($proforma_list)
									// 	->draw();
								?>
								<?php
									echo $ui->formField('textarea')
										->setLabel('Notes')
										->setSplit('col-md-4', 'col-md-8')
										->setName('remarks')
										->setId('remarks')
										->addHidden('remarks')
										->draw();
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body">	
				<div class="box-body table-responsive no-padding" id="content">
					<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-6">
							<div class="form-group">
								<div class="input-group">
									<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<table id="is_list" class="table table-hover table-sidepad" cellpadding="0" cellspacing="0" border="0" width="100%">
						<thead>
							<tr class="info">
								<th class="col-md-3 text-left">Account</th>
								<th class="col-md-3 text-left">Description</th>
								<th class="col-md-3 text-right">Debit</th>
								<th class="col-md-3 text-right">Credit</th>
							</tr>
						</thead>
						<tbody id="ic_rows"></tbody>
					</table>
					<div id="pagination"></div>	
					<div class="box-body">
						<hr>
						<div class="row">
							<div class="col-md-12 text-center">
								<button class="btn btn-primary" id="confirmbtn">Confirm</button>
								<button type="button" class="btn btn-default"  id="closing_cancel">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="redirectionModal" tabindex="-1" data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<strong>Confirmation</strong>
			</div>
			<div class="modal-header"></div>
			<div class="modal-body">	
				<p><b>Successfully Saved.</b> Would you like to view the Journal Voucher?</p>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-12 text-center">
						<button class="btn btn-success" id="btnYes">Yes</button>
						<button type="button" class="btn btn-default"  id="btnNo">No</button>
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
		ajax2.limit = 2;
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
				$('#alert_modal .modal-body').html("<p>You have not performed any closing activities for the Previous Month(s).</p>")
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

		// check_existing_jv(date_array[1]);
	}

	function preview_jv(voucherno){
		ajax2.voucherno 		=	voucherno;

		$.post('<?=MODULE_URL?>ajax/preview_listing', ajax2 , function(response) {
			//Header
			$('#previewModal #voucherno').val(response.voucherno);
			$("#previewModal #transactiondate_static").html(response.transactiondate);
			$('#previewModal #referenceno_static').html(response.reference);
			$("#previewModal #remarks_static").html(response.remarks);

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
		// enable_button($(this).val());
	}).trigger('change');
	$('#close_book').on('click',function(){
		var daterangefilter 	=	$('#daterangefilter').val();

		$('#reference').val("");
		$('#notes').val("");
		$("#jvModal").modal('show');
		$('#btnSaveDetails').prop('disabled',false);
	});
	
	//if current month = date
	function validate_date(current_month){
		var date_arr 	=	current_month.split(' ');
		var flag 		= 	0;
		
		var current = new Date(current_month);
			curr_m 	= current.getMonth() +1;
			curr_y 	= current.getFullYear();
			current_month = curr_m+"-"+curr_y;

		var d = new Date(),
			n = d.getMonth()+1,
			y = d.getFullYear();

			my 	=	n+"-"+y;

		if(my == current_month){
			$('#alert_modal .modal-body').html("<p>You cannot Close a Book within the current Month.</p>");
			$('#alert_modal').modal('show');
			$('#btnSaveDetails').prop('disabled',true);
			flag 	=	1;
		} else {
			$('#btnSaveDetails').prop('disabled',false);
		}

		return flag;
	}

	$('#jv_header').on('click',"#btnSaveDetails", function(){
		var current_date 		=	$('#jvModal #datefrom').val();

		ajax2.datefrom 			=	current_date;
		ajax2.reference 		=	$('#jvModal #reference').val();
		ajax2.notes 			=	$('#jvModal #notes').val();
		ajax2.closing_account 	=	$('#jvModal #closing_account').val();

		var has_error 	=	validate_date(current_date);

		if(!has_error){
			$.post('<?=MODULE_URL?>ajax/temporary_jv_save', ajax2 , function(response) {
				if( response.result ){
					$("#jvModal").modal('hide');
					preview_jv(response.voucherno);
				}
			});
		}
		
	});

	$('#previewModal').on('click','#confirmbtn',function(){
		ajax2.voucherno 	=	$('#previewModal #voucherno').val();
		$.post('<?=MODULE_URL?>ajax/update_jv_status', ajax2 , function(response) {
			if( response.result ){
				$('#previewModal').modal('hide');
				$('#redirectionModal').modal('show');
			}
		});
	});

	$('#redirectionModal').on('click','#btnYes',function(){
		window.location 	=	'<?=BASE_URL?>financials/journal_voucher/';
	});

	$('#redirectionModal').on('click','#btnNo',function(){
		window.location 	=	'<?=BASE_URL?>report/trial_balance';
	});

	$('#previewModal').on('input','#table_search', function () {
		var voucherno 	=	$('#previewModal #voucherno').val();
		ajax2.page = 1;
		ajax2.search = $(this).val();
		preview_jv(voucherno);
	});

	$('#previewModal #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var voucherno 	=	$('#previewModal #voucherno').val();
		ajax2.page = $(this).attr('data-page');
		preview_jv(voucherno);
	});
	
	$('.datefilter').datepicker({
		format: "MM yyyy",
		startView: 2,
		minViewMode: 1,
		maxViewMode: 2,
		autoclose: true
	});

	$('#closing_cancel').on('click',function(e){
		ajax2.voucherno 	=	$('#previewModal #voucherno').val();
		//delete temporary saved jv... 
		$.post('<?=MODULE_URL?>ajax/delete_temporary_jv', ajax2 , function(response) {
			if( response.result ){
				$('#previewModal').modal('hide');
				getTrialBalance();
			}
		});
		//close modal
	});

</script>