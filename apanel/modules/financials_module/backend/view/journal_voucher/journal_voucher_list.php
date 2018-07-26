<section class="content">
	<!-- Error Message for File Import -->
	<div class="alert alert-danger hidden" id="import_error">
		<button type="button" class="link btn-sm close" >&times;</button>
		<p>Ok, just a few more things we need to adjust for us to proceed :) </p><hr/>
		<ul>

		</ul>
	</div>
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-8">
						<!-- <div class="form-group">
							<a href="<?//= MODULE_URL ?>create" class="btn btn-primary">Create New Journal Voucher</a>
							<button type="button" id="import_jv" class="btn btn-info delete_button">Import<span></span></button>
							<button type="button" id="item_multiple_delete" class="btn btn-danger delete_button">Cancel<span></span></button>
						</div> -->
						<?
							echo $ui->CreateNewButton('');
							echo $ui->OptionButton('');
						?>
						<input type="button" id="item_multiple_delete" class="btn btn-danger btn-flat " value="Cancel">
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
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-5">
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
			<div class = "alert alert-warning alert-dismissable hidden">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4><strong>Warning!</strong></h4>
				<div id = "errmsg"></div>
				<div id = "warningmsg"></div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader(
									'<input type="checkbox" class="checkall">',
									array(
										'class' => 'text-center',
										'style' => 'width: 15px'
									)
								)
								->addHeader('Transaction Date', array('class' => 'col-md-2'), 'sort', 'transactiondate')
								->addHeader('Imported', array('class' => 'col-md-2'), 'sort', 'source')
								->addHeader('Voucher Number', array('class' => 'col-md-2'), 'sort', 'voucherno', 'desc')
								->addHeader('Reference', array('class' => 'col-md-3'), 'sort', 'referenceno')
								->addHeader('Total Amount', array('class' => 'col-md-3'), 'sort', 'amount')
								->addHeader('Status', array('class' => 'col-md-3'), 'sort', 'status')
								->draw();
					?>
					<tbody>

					</tbody>
					<!--<tfoot>
						<tr>
							<td colspan="9">Showing 1 to 25 of 57 entries</td>
						</tr>
					</tfoot>-->
				</table>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<div class="delete-modal">
		<div class="modal modal-danger">
			<div class="modal-dialog" style = "width: 300px;">
				<div class="modal-content">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
					</div>
					<div class="modal-body">
					<p>Are you sure you want to delete this record?</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-outline btn-flat" id = "delete-yes">Yes</button>
						<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Import Modal -->
	<div class="modal fade" id="import-modal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Journal Vouchers</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" id="download-link" download="Journal Voucher Template.csv" >here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group">
							<label for="import_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<?php
								echo $ui->setElement('file')
										->setId('import_csv')
										->setName('import_csv')
										->setAttribute(array('accept' => '.csv'))
										->setValidation('required')
										->draw();
							?>
							<span class="help-block"></span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
						<div class="modal-footer text-center">
							<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
							<button type="button" class="btn btn-default btn-flat" id="btnClose">Close</button>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	<script>
		var ajax = {}
		var ajax_call = '';
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { search : '#table_search', limit : '#items', daterangefilter : '#daterangefilter' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
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
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
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
		$(function() {
			linkButtonToTable('#item_multiple_delete', '#tableList');
			linkCancelToModal('#tableList .delete', 'ajaxCallback');
			linkCancelMultipleToModal('#item_multiple_delete', '#tableList', 'ajaxCallback');
		});
		$('#daterangefilter').on('change', function() {
			ajax.page = 1;
			ajax.daterangefilter = $(this).val();
			getList();
		});
		// NO Export Yet
		$('#export_id').addClass('hidden')
		$('#import_id').prop('href','#import-modal');
		$("#import_id").click(function() 
		{
			$("#import-modal > .modal").css("display", "inline");
			$('#import-modal').modal();
		});
		$('#import-modal').on('show.bs.modal', function() {
			var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
			$('#import_csv').closest('.form-group').html(form_csv);
		});
		$('#import-modal #btnClose').click(function(){
			$('#import-modal #import-skip #loading').addClass('hidden');
			$('#import-modal #import-proceed #loading').addClass('hidden');
			$('#import-modal #import-step1').show();
			$('#import-modal #import-step2').hide();
			$('#import-modal').modal('hide');	
		});
	
		$('#importForm').on('change', '#import_csv', function() {
			var filename = $(this).val().split("\\");
			$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
			console.log("Filename = "+filename[filename.length - 1]);
		});
		$(".close").click(function() 
		{
			location.reload();
		});
		$('#btnImport').on('click',function(e){
			var formData =	new FormData();
			formData.append('file',$('#import_csv')[0].files[0]);
			ajax_call 	=	$.ajax({
				url : '<?=MODULE_URL?>ajax/save_import',
				data:	formData,
				cache: 	false,
				processData: false, 
				contentType: false,
				type: 	'POST',
				success: function(response){
					if(response && response.errmsg == ""){
						$('#import-modal').modal('hide');
						// $(".alert-warning").addClass("hidden");
						// $("#errmsg").html('');
						show_success_msg('Your data has been imported successfully.');
					}else{
						$('#import-modal').modal('hide');
						show_error(response.errmsg, response.warning);
					}
				},
			});
		});
		function show_error(msg, warning){
			if(msg != ''){
				var newmsg 	= msg.split("<br/>");
				var errcnt	= newmsg.length;
				var list 	= '';
				for (let index = 0; index < errcnt; index++) {
					if(newmsg[index] != ''){
						list += '<li>'+newmsg[index]+'</li>';
					}
				}
			}
			$("#import_error").removeClass('hidden');
			$("#import_error ul").html(list);

			$('html,body').animate({ scrollTop: (0) }, 'slow');
		}
		function show_success_msg(msg){
			$('#success_modal #message').html(msg);
			$('#success_modal').modal('show');
		}
		$('body').on('click','#success_modal .btn-success', function(){
			$('#success_modal').modal('hide');
			getList();
		});
	</script>