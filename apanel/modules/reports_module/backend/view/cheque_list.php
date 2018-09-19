<section class="content">
	<div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class = "col-md-11 form-group">
								<?php echo $ui->setElement("button")
										->setId("release")
										->setClass("btn btn-primary ")
										->setPlaceholder('<i class="glyphicon glyphicon-send"></i>  Release <span></span>')
										->draw();
								?>
								<?php echo $ui->setElement("button")
										->setId("void")
										->setClass("btn btn-warning ")
										->setPlaceholder('<i class="glyphicon glyphicon-remove"></i>  Void <span></span>')
										->draw();
								?>
								<?php echo $ui->setElement("button")
										->setId("cancel")
										->setClass("btn btn-danger ")
										->setPlaceholder('<i class="glyphicon glyphicon-ban-circle"></i>  Cancel <span></span>')
										->draw();
								?>
							</div>
							<div class = "col-md-9"></div>
							<div clss = "col-md-1">
								<a href="" id="export_csv" download="Check List.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
							</div>
						</div>
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
					<div class="col-md-6">
						<div class="row">
							<div class = "col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Partner')
										->setName('partner')
										->setId('partner')
										->setList($partner_list)
										->setNone('All')
										->setAttribute(array('multiple'))
										->draw();
								?>
							</div>
							<div class = "col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Bank')
										->setName('bank')
										->setId('bank')
										->setList($bank_list)
										->setNone('All')
										->setAttribute(array('multiple'))
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group" >
								<input name="search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
								<div class="input-group-btn" style = "height: 34px;">
									<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		
		<div class="nav-tabs-custom">
	
			<ul class="nav nav-tabs">
				<li class="active"><a href="#" data-toggle="tab" style="outline:none;" onClick="filterList('all');">All</a></li>
				
				<li><a href="#" data-toggle="tab" style="outline:none;" 
						onClick="filterList('uncleared');">Prepared</a></li>
				<li><a href="#" data-toggle="tab" style="outline:none;" 
						onClick="filterList('void');">Void</a></li>
				<li><a href="#" data-toggle="tab" style="outline:none;" 
						onClick="filterList('cancelled');">Cancelled</a></li>
				<li><a href="#" data-toggle="tab" style="outline:none;" 
						onClick="filterList('released');">Released</a></li>
						
			</ul>
			
			<table id="tableList" class="table table-hover">
				<thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
										'<input type = "checkbox" name = "selectall" id = "selectall"/>',
										array('class'=>'col-md-1 text-center'))
								->addHeader('Check Date',array('class'=>'col-md-1 center'),'sort','chq.chequedate')
								->addHeader('Check Number', array('class'=>'col-md-1 center'),'sort','chq.chequenumber')
								->addHeader('Invoice No.',array('class'=>'col-md-1 center'),'sort','ap.invoiceno')
								->addHeader('Voucher No.',array('class'=>'col-md-1 center'),'sort','chq.voucherno')
								->addHeader('Bank',array('class'=>'col-md-2 center'),'sort','coa.accountname')
								->addHeader('Partner',array('class'=>'col-md-1 center'),'sort','pt.partnername')
								->addHeader('Amount',array('class'=>'col-md-1 center'),'sort','chq.chequeamount')
								->addHeader('Release Date',array('class'=>'col-md-1 center'),'sort','chq.releasedate')
								->addHeader('Cleared Date',array('class'=>'col-md-1 center'),'sort','chq.cleardate')
								->addHeader('Check Status',array('class'=>'col-md-1 center'))
								->draw();
					?>
				</thead>
				<tbody></tbody>
				<tfoot></tfoot>
			</table>
			<div id="pagination"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="voidModal" tabindex="-1" data-backdrop="static">
<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			Confirmation
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		</div>
		<div class="modal-body">
			Are you sure you want to void this transaction?
		</div>
		<div class="modal-footer">
			<div class="row row-dense">
				<div class="col-md-12 center">
					<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnVoid">Yes</button>
					</div>
						&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<div class="modal fade" id="cancelmodal" tabindex="-1" data-backdrop="static">
<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			Confirmation
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		</div>
		<div class="modal-body">
			Are you sure you want to cancel this transaction?
		</div>
		<div class="modal-footer">
			<div class="row row-dense">
				<div class="col-md-12 center">
					<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnYes">Yes</button>
					</div>
						&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!-- Customer Modal -->
<div class="modal fade" id="release_modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4><b>Release Cheques</b><h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="releaseForm" autocomplete="off">
					<div class="alert alert-warning alert-dismissable hidden" id="customerAlert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<p>&nbsp;</p>
					</div>
					<div class="row">
						<label class="control-label col-md-3" for="release_date">Release Date:</label>
						<div class = "col-md-8" style = "padding-left: 8px; padding-right: 3px;">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input class="form-control pull-right datepicker-input" id="release_date" name="release_date" type="text" value="<?=$date?>" required>
								<span class="help-block hidden small req-color" id = "release_date_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<?php
							echo $ui->formField('textarea')
								->setLabel("Remarks")
								->setSplit("col-md-3","col-md-8")
								->setPlaceholder('Insert Remarks here..')
								->setName('release_remarks')
								->setId('release_remarks')
								->setValue('')
								->draw();
						?>
					</div>
					<div class="modal-footer">
							<div class="row row-dense">
								<div class="col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-info btn-flat" id="btnSave">Save</button>
									</div>
										&nbsp;&nbsp;&nbsp;
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
									</div>
								</div>
							</div>
						</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Vendor Modal -->

<script>
	var ajax = {}
	var ajax_call = {};

	$('#bank').on('change', function() {
		ajax.bank = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#partner').on('change', function() {
		ajax.partner = $(this).val();
		if (Array.isArray(ajax.partner) && ajax.partner.indexOf('none') != -1) {
			$(this).selectpicker('deselectAll');
		}
		ajax_call.abort();
		getList();
	});
	$( "#search" ).keyup(function() {
		var search = $( this ).val();
		ajax.search = search;
		ajax.page 	= 1;
		ajax_call.abort();
		getList();
	});
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		ajax.page = $(this).attr('data-page');
		getList();
	});
	function filterList(tab){
		ajax.filter = tab;
		ajax_call.abort();
		getList();
	}
	function getList() {
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList tbody').html(data.table);
			if (data.result_count == 0) {
				data.tabledetails = '';
			}
			$('#tableList tfoot').html(data.tabledetails);
			$('#pagination').html(data.pagination);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		});
	}
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		try {
			ajax_call.abort();
		} catch (e) {}
		getList();
	}).trigger('change');
	$('#tableList').on('ifChecked','#selectall',function(){
		$('.checkbox').iCheck('check');
	});
	$('#tableList').on('ifUnchecked','#selectall',function(){
		$('.checkbox').iCheck('uncheck');
	});
	function getSelectedIds(){
		id 	=	[];
		$('.checkbox:checked').each(function(){
			id.push($(this).val());
		});
		return id;
	}
	$("#release").click(function() 
	{
		$('#release_modal').modal('show');
	});
	$('#releaseForm #btnSave').on('click',function(){
		ids 	=	getSelectedIds();
		$.post('<?=MODULE_URL?>ajax/update_cheques', $('#releaseForm').serialize()+"&ids="+ids, function(data) {
			if( data.msg == 'success' )
			{
				getList();
				$('#release_modal').modal('hide');
			} 
		});
	});

	$("#void").click(function() 
	{
		$('#voidModal').modal('show');
	});
	$('#btnVoid').on('click',function(){
		ids 	=	getSelectedIds();
		$.post('<?=MODULE_URL?>ajax/update_void', "&ids="+ids ,function(data) {
			if( data.msg == 'success' )
			{
				getList();
				$('#voidModal').modal('hide');
			} 
		});
	});
	$("#cancel").click(function() 
	{
		$('#cancelmodal').modal('show');
	});
	$('#btnYes').on('click',function(){
		ids 	=	getSelectedIds();
		$.post('<?=MODULE_URL?>ajax/update_cancel', "&ids="+ids, function(data) {
			if( data.msg == 'success' )
			{
				getList();
				$('#cancelmodal').modal('hide');
			} 
		});
	});
	
	$(function() {
		linkButtonToTable('#release', '#tableList');
		linkButtonToTable('#void', '#tableList');
		linkButtonToTable('#cancel', '#tableList');
	});
	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});

</script>