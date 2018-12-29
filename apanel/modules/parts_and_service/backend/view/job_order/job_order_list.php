<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<?php
							echo $ui->CreateNewButton('');
						?>
						<button type="button" id="item_multiple_cancel" class="btn btn-danger btn-flat">Cancel<span></span></button>
					</div>
					<div class="col-md-4">
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
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" readonly data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Filter Customer')
										->setName('customer')
										->setId('customer')
										->setList($customer_list)
										->setNone('Filter: All')
										->draw();
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="row">
							<div class="col-sm-8 col-xs-6 text-right">
								<label for="" class="padded">Items: </label>
							</div>
							<div class="col-sm-4 col-xs-6">
								<div class="form-group">
									<select id="items">
										<option value="10">10</option>
										<option value="20">20</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li class="active"><a href="all" data-toggle="tab">All</a></li>
				<li><a href="prepared" data-toggle="tab">Prepared</a></li>
				<li><a href="partial" data-toggle="tab">Partial</a></li>
				<li><a href="completed" data-toggle="tab">Completed</a></li>
				<li><a href="Cancelled" data-toggle="tab">Cancelled</a></li>
			</ul>
			<div class="table-responsive">
				<table id="tableList" class="table table-hover table-sidepad">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'text-center',
										'style' => 'width: 100px'
									)
								)
								->addHeader('JO No.', array('class' => 'col-md-3'), 'sort', 'job_order_no', 'desc')
								->addHeader('Transaction Date', array('class' => 'col-md-2'), 'sort', 'transactiondate')
								->addHeader('Customer', array('class' => 'col-md-2'), 'sort', 'customer')
								->addHeader('SQ No.', array('class' => 'col-md-3'), 'sort', 'service_quotation')
								->addHeader('Reference', array('class' => 'col-md-2'), 'sort', 'reference')
								->addHeader('Status', array('style' => 'width: 15px'), 'sort', 'stat')
								->draw();
					?>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
		<div id="attach_modal" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
			<form method = "post" id="attachments_form" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Attach File for <span id="modal-voucher"></span></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" name="voucherno" id='input_voucherno'>
						<?php
							echo $ui->setElement('file')
									->setId('files')
									->setName('files')
									->setAttribute(array('accept' => '.pdf, .jpg, .png'))
									->setValidation('required')
									->draw();
						?>
					</div>
					<p class="help-block">The file to be imported shall not exceed the size of <strong>3mb</strong> and must be a <strong>PDF, PNG or JPG</strong> file.</p>
				</div>
				<div class="modal-footer">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<div class="btn-group">
						<button type="button" class="btn btn-primary btn-sm btn-flat" id="attach_button">Attach</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</form>
			</div>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<script>
		var ajax = filterFromURL();
		var ajax_call = '';
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', customer : '#customer', daterangefilter : '#daterangefilter' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
	
		ajax.filter = $('#filter_tabs .active a').attr('href');
		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		}, ajax);
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});
		$('#customer').on('change', function() {
			ajax.page = 1;
			ajax.customer = $(this).val();
			getList();
		});
		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});
		$('#tableList').on('click','.issue_parts',function(){
			var id = $(this).data('id');
			window.location.href = '<?=BASE_URL?>parts_and_service/job_order/payment/'+id;
		});
		function getList() {
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();
		function ajaxCallback(id) {
			var ids = getDeleteId(id);
			$.post('<?=MODULE_URL?>ajax/ajax_delete', ids, function(data) {
				getList();
			});
		}
		function getIds(ids) {
			var x = ids.split(",");
			return "id[]=" + x.join("&id[]=");
		}
		$(function() {
			linkButtonToTable('#item_multiple_cancel', '#tableList');
			linkCancelToModal('#tableList .delete', 'ajaxCallback');
			linkCancelMultipleToModal('#item_multiple_cancel', '#tableList', 'ajaxCallback');
		});
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			getList();
		})
		$('#tableList').on('click','.tag_as_complete',function(){
			$('#attach_modal').modal('show');
			var job_order_no = $(this).data('id');
			$('#modal-voucher').html(job_order_no);
			$('#input_voucherno').val(job_order_no);
		});
		
	</script>

<script>
$(function () {
	'use strict';

	$('#attachments_form').fileupload({
		url: '<?= MODULE_URL ?>ajax/ajax_upload_file',
		maxFileSize: 3000000,
		disableExifThumbnail :true,
		previewThumbnail:false,
		autoUpload:false,
		add: function (e, data) {            
			$("#attach_button").off('click').on('click', function () {
				data.submit();
				//redirect
			});
		},
	});
	$('#attachments_form').addClass('fileupload-processing');
	$.ajax({
		url: $('#attachments_form').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#attachments_form')[0]
	}).always(function () {
		$(this).removeClass('fileupload-processing');
	}).done(function (result) {
		$(this).fileupload('option', 'done')
			.call(this, $.Event('done'), {
				result: result
			});
	});

	$('#attachments_form').bind('fileuploadadd', function (e, data) {
		var filename = data.files[0].name;
		$('#attachments_form #files').closest('.input-group').find('.form-control').html(filename);
	});
	$('#attachments_form').bind('fileuploadsubmit', function (e, data) {
		var voucherno 		=  $('#input_voucherno').val();
		console.log(voucherno);
		data.formData = {reference: voucherno};
	});
	$('#attachments_form').bind('fileuploadalways', function (e, data) {
		var error = data.result['files'][0]['error'];
		var form_group = $('#attachments_form #files').closest('.form-group');
		if(!error){
			$('#attach_modal').modal('hide');
			var msg = data.result['files'][0]['name'];
			form_group.removeClass('has-error');
			form_group.find('p.help-block.m-none').html('');

			$('#attachments_form #files').closest('.input-group').find('.form-control').html('');
			getList();
		}else{
			var msg = data.result['files'][0]['name'];
			form_group.addClass('has-error');
			form_group.find('p.help-block.m-none').html(msg);
		}
	});
});
</script>