<section class="content">
	<div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-11"></div>
							<div clss="col-md-1">
								<a href="" id="export_csv" download="Cheque List.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
							</div>
							<div class="col-md-12">&nbsp;</div>
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
			<table id="tableList" class="table table-hover">
				<thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Check Date',array('class'=>'col-md-2 center'),'','chq.chequedate')
								->addHeader('Customer',array('class'=>'col-md-3 center'),'','pt.partnername')
								->addHeader('Bank',array('class'=>'col-md-2 center'),'','coa.accountname')
								->addHeader('Check Number', array('class'=>'col-md-3 center'),'','chq.chequenumber')
								// ->addHeader('Invoice No.',array('class'=>'col-md-1 center'),'','ar.invoiceno')
								->addHeader('Amount',array('class'=>'col-md-2 center'),'','chq.chequeamount')
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
	$("#release").click(function() {
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
	$(function() {
		linkButtonToTable('#release', '#tableList');
	});
	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});
</script>