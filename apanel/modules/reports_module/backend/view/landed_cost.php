<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<form method="post">
                <div class="col-md-3">
                    <?php
                        echo $ui->formField('dropdown')
								->setPlaceholder('Filter IPO')
								->setName('import_purchase_order')
								->setId('import_purchase_order')
								->setList($import_purchase_order_list)
								->setNone('Filter: All')
								->draw();
                    ?>
                </div>
                <div class="col-md-3">
                    <?php
                        echo $ui->formField('dropdown')
								->setPlaceholder('Filter Supplier')
								->setName('supplier')
								->setId('supplier')
								->setList($supplier_list)
								->setNone('Filter: All')
								->draw();
                    ?>
                </div>
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
				<!-- <div class="col-md-3"></div> -->
				<div class="col-md-3">
					<div class="form-group text-right">
						
						<a href="" id="export_csv" download="Landed_Cost.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
				</div>
				</form>
			</div>	
		</div>
	</div>


    <div class="nav-tabs-custom">
        <ul id="filter_tabs" class="nav nav-tabs">
            <li class="active"><a href="#All" data-toggle="tab" data-id="All">All</a></li>
            <li><a href="#Partial" data-toggle="tab" data-id="Partial">Partial</a></li>
            <li><a href="#Completed" data-toggle="tab" data-id="Completed">Completed</a></li>
        </ul>


        <div class="box-body table-responsive no-padding" id="report_content">
            <table id="landedCostList" class="table table-hover table-striped table-condensed table-bordered" cellpadding="0" cellspacing="0" border="0" width="100%">
                <thead>
                    <tr class="info">
                        <th class="col-md-1 text-center">Item</th>
                        <th class="col-md-1 text-center">Description</th>
                        <th class="col-md-1 text-center">IPO Number</th>
                        <th class="col-md-1 text-center">IPO Date</th>
                        <th class="col-md-1 text-center">Qty/Unit</th>
                        <!-- <th class="col-md-1 text-center">UOM</th> -->
                        <th class="col-md-1 text-center">IPO Receipt Date</th>
                        <th class="col-md-1 text-center">Unit Cost Foreign Currency</th>
                        <th class="col-md-1 text-center">Unit Cost Base Currency</th>
                        <th class="col-md-1 text-center">Importation Cost per Unit</th>
                        <th class="col-md-1 text-center">Landed Cost per Unit</th>
                        <th class="col-md-2 text-center">Total Landed Cost</th>
                    </tr>
                </thead>
                <tbody id="landed_container">
                    
                </tbody>
            </table>
            <div id="pagination"></div>	
        </div>
    </div>
</section>


<script type = 'text/javascript'>
console.log("a");
	var ajax = {}
	var ajax_call = '';
	var ajax = filterFromURL();
	// ajax.limit = 10; 

	function getList() {
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#landedCostList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			getList();
		}
	});

	$('#import_purchase_order').on('change', function() {
		ajax.import_purchase_order 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});

	$('#supplier').on('change', function() {
		ajax.supplier 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});
	/**JSON : RETRIEVE TRANSACTIONS**/
	function openList(acct)
	{
		var x					= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
		var datefilter			= document.getElementById('daterangefilter').value;
		var items 				= document.getElementById('items').value;
		
		ajax3.daterangefilter 	= datefilter;
		ajax3.accountcode 		= acct;
		ajax3.items 			= items;
		
		$.post('<?=MODULE_URL?>ajax/load_account_transactions',ajax3, function(response) {
		var jsondata	= response;
			$("#listModal .modal-header").html('<strong>'+jsondata.title+'</strong>'+x);
			$('#listModal #list_container').html(jsondata.table);
			$('#listModal #pagination').html(jsondata.pagination);
			$('#acct').val(acct);
			if(!($("listModal").data('bs.modal') || {}).isShown){
				$("#listModal").modal('show');
			}
		});
	}

	// $('#report_content #pagination').on('click', 'a', function(e) {
	// 	e.preventDefault();
	// 	var li = $(this).closest('li');
	// 	if (li.not('.active').length && li.not('.disabled').length) {
	// 		ajax.page = $(this).attr('data-page');
	// 		getTrialBalance();
	// 	}
	// });
	$('#listModal #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax3.page = $(this).attr('data-page');
			openList(ajax3.accountcode);
		}
	});
	$('#listModal #items').on('change', function(e) {
		e.preventDefault();
		ajax3.limit = $(this).val();
		openList(ajax3.accountcode);
	});
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		getList();
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
			$.post('<?=MODULE_URL?>ajax/temporary_jv_close', ajax2 , function(response) {
				if( response.result ){
					$("#jvModal").modal('hide');
					preview_jv(response.voucherno);
				}
			});
		}
		
	});

	$('#previewModal').on('click','#confirmbtn',function(){
		ajax2.voucherno 	=	$('#previewModal #voucherno').val();
		$.post('<?=MODULE_URL?>ajax/close_jv_status', ajax2 , function(response) {
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

</script>