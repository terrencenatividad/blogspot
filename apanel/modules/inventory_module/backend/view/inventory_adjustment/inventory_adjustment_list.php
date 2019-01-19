<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-3">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Warehouse')
							->setName('warehouse')
							->setId('warehouse')
							->setList($warehouses)
							->setNone('Filter: None')
							->draw();
					?>
				</div>
				<div class="col-md-3 w_selected">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Item')
							->setName('itemcode')
							->setId('itemcode')
							->setList($item_list)
							->setNone('Filter: All')
							->draw();
					?>
				</div>
				<div class="col-md-3 w_selected">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Brand')
							->setName('brandcode')
							->setId('brandcode')
							->setList($brand_list)
							->setNone('Filter: All')
							->draw();
					?>
				</div>
				<div class="col-md-offset-6 col-md-3 hidden">
					<div>Time left = <span id="timer"></span></div>
				</div>
				<?if($display_import_btn):?>
					<div class="col-md-1 pull-right">
						<a href="javascript:void(0);" id="import" class="btn btn-info pull-right"><span class="glyphicon glyphicon-save"></span> Import Beginning Balance</a>
					</div>
				<?endif;?>
			</div>
		</div>

		<div class = "alert alert-warning alert-dismissable hidden">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<h4><strong>Warning!</strong></h4>
			<div id = "errmsg"></div>
			<div id = "warningmsg"></div>
		</div>

		<div class="box-body table-responsive no-padding w_selected">
			<table id="tableList" class="table table-hover">
				<thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Item Code',array('class'=>'col-md-1'),'sort','items.itemcode')
								->addHeader('Item Name', array('class'=>'col-md-2'),'sort','items.itemname')
								->addHeader('Brand', array('class'=>'col-md-1'),'sort','b.brandname')
								->addHeader('On Hand Qty', array('class'=>'col-md-1'),'sort','inv.onhandQty')
								->addHeader('Allocated Qty', array('class'=>'col-md-1'),'sort','inv.allocatedQty')
								->addHeader('Ordered Qty', array('class'=>'col-md-1'),'sort','inv.orderedQty')
								->addHeader('Available Qty', array('class'=>'col-md-1'),'sort','inv.availableQty')
								->addHeader('', array('class'=>'col-md-1'))
								->draw();
					?>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
		<div class="w_selected" id="pagination"></div>
	</div>
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

<div class="modal fade" id="adjModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">

		<div class="modal-content">
			<form method = "POST" id="adjustForm">
				<div class="modal-header ">
					<div class="row">
						<div class="col-md-11">
							<h4 class = 'bold'>Adjustment</h4>
						</div>
						<div class="col-md-1 right">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
					</div>
				</div>

				<div class="modal-body">
					<div class = 'row'>
						<input type='hidden' name='h_warehouse' id='h_warehouse' value=''>
						<input type='hidden' name='action' id='addminusbtn' value=''>
						<input type='hidden' name='item_ident_flag' id='item_ident_flag' value=''>

						<div class = 'panel panel-default'>
							<div class = 'panel-heading'>
								<h3 class="panel-title">Header Information:</h3>
							</div>
							<div class = 'panel-body'>
								<span class='col-md-4 bold'>Adjustment Ref. No.:</span> 
								<div class = 'col-md-4'>
									<input value = '<?=$voucherno?>' name = 'adjrefno' id = 'adjrefno' type = 'text' class = 'form-control' readonly="readonly" tabindex="-1">
										<!--<input type='hidden' name = 'voucherno' id = 'voucherno' value="<?=$generated_adj_id?>"> -->
								</div>
								<div class = 'col-md-4'></div>
								
								<div class = 'col-md-12'><br></div>	

								<span class = 'col-md-4 bold'>Date: </span>
								<div class = 'col-md-4'>
									<div class="input-group">
										<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
										</div>
										<input name='adjustdate' id='adjustdate' type="text" class="form-control" value = "<?=$adjustmentdate?>" readonly> 
									</div>
								</div>
								<div class = 'col-md-4'></div>

								<div class = 'col-md-12'><br></div>	

								<div class = 'col-md-12 no-padding'>
									<?php
										echo $ui->formField('textarea')
												->setLabel('Notes:')
												->setSplit('col-md-4', 'col-md-8')
												->setName('remarks')
												->setId('remarks')
												->setValidation('required')
												->setMaxLength(100)
												->setValue("")
												->draw();
									?>
								</div>

							</div>
							
							<hr/>
							
							<div class = 'panel-body'>
								<div class = 'col-md-12'></div>
								<span class='col-md-4 bold'>Part Code:</span> 
								<div class='col-md-8' id='modal_code'></div>
								<input id='itemcode' name='itemcode' type='hidden'>

								<span class='col-md-4 bold'>Part Name:</span> 
								<div class='col-md-8' id='modal_p_name'></div>
								<input id='itemname' name='itemname' type='hidden'>

								<div class='col-md-12'><br/></div>

								<?php
									echo $ui->formField('text')
											->setLabel("Qty:")
											->setSplit('col-md-4', 'col-md-8')
											->setName('issueqty')
											->setId('issueqty')
											->setValue('0')
											->setClass('notserialized')
											->setValidation('required integer')
											->draw();
								?>
								<?php
									echo $ui->setElement('button')
											->setId('issueqtybtn')
											->setSplit('col-md-4', 'col-md-8 text-left')
											->setName('issueqtybtn')
											->setClass('form-control serialized btn-warning hidden')
											->setValidation('required')
											->setPlaceholder('0')
											->setAttribute(array('style'=>'text-align:left'))
											->setValue('0')
											->draw();
									echo $ui->setElement('hidden')
											->setId('issueqty_serial')
											->setName('issueqty_serial')
											->setClass('form-control')
											->draw();
									// echo $ui->setElement('textarea')
											
											// <input name='issueqty_serial' id='issueqty_serial' class='form-control' type='hidden'>
				  							// <textarea class="form-control hidden" id="serialInputs" name="serials"></textarea>
								?>
								<div class='col-md-12'><hr/></div>
								<?php
									echo $ui->formField('dropdown')
											->setLabel('Inventory Account')
											->setPlaceholder('Select an Account')
											->setSplit('col-md-4', 'col-md-8')
											->setName('inventory_account')
											->setId('inventory_account')
											//->setList($chart_account_list)
											->setValidation('required')
											->setValue('')
											->draw();
								?>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 right">
							<div class="btn-group">
								<button type="button" class="btn btn-success" id="btnSave" >Save</button>	
								<button type="button" id="closeModal" class="btn btn-default">Cancel</button> 
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="lockerModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog ">

		<div class="modal-content">
			<div class="modal-header ">
				<div class="row">
					<div class="col-md-10">
						<h4 class = 'bold'> <span class="glyphicon glyphicon-warning-sign"></span> Notice!</h4>
					</div>
				</div>
			</div>

			<div class="modal-body">
				<div class = 'row'>
					<div class = 'col-md-12'>
						Proceeding with this transaction will prevent other users from logging in.<br><br>
						<strong>Currently Logged In Users are: </strong><br>
						<div id = "logged_users"></div>
						<br>
						Would you like to proceed with your transaction? 
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 right">
						<div class="form-group">
							<button type="button" class="btn btn-warning" id="btnProceed" >Proceed</button>	
							<button type="button" id="btnCancel" class="btn btn-default">Cancel</button> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="timerModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog ">

		<div class="modal-content">
			<div class="modal-header ">
				<div class="row">
					<div class="col-md-11">
						<h4 class = 'bold'> <span class="glyphicon glyphicon-warning-sign"> Notice!</h4>
					</div>
				</div>
			</div>

			<div class="modal-body">
				<div class = 'row'>
					<div class = 'col-md-12'>
						Time for adjusting is almost up. 
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 right">
						<div class="form-group">
							<button type="button" class="btn btn-warning" id="btnProceed" data-dismiss="modal">Ok</button>	 
						</div>
					</div>
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
					<h4 class="modal-title">Import Beginning Balance</h4>
				</div>
				<div class="modal-body">
					<div id = 'import-step1'>
						<div class = 'row'>
							<div class = 'col-md-1'></div>
							<?php
								echo $ui->formField('text')
										->setLabel('Date')
										->setSplit('col-md-3', 'col-md-8')
										->setName('importdate')
										->setId('importdate')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($importdate)
										->setValidation('required')
										->draw(true);
							?>
						</div>
						<div class="modal-footer text-center">
							<button type = 'button' class = 'btn btn-info btn-flat' name = 'import-proceed' id = 'import-proceed'><i id='loading' class="hidden fa fa-refresh fa-spin"></i> Proceed</button>
							<button type = 'button' class = 'btn btn-default btn-flat' name = 'import-skip' id = 'import-skip'><i id='loading' class="hidden fa fa-refresh fa-spin"></i> Skip</button>
						</div>	
					</div>

					<div id = 'import-step2'>
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import" id="download-link" download="Beginning Balance.csv" >here</a></label>
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
				</div>
				
			</form>
		</div>
	</div>
</div>

<!-- Import Serial Modal -->
<div class="modal fade" id="import-serial-modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="importSerialForm" ENCTYPE="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
					<h4 class="modal-title">Import Serial/Engine/Chassis Numbers</h4>
				</div>
				<div class="modal-body">
					<div id = 'import-step1'>
						<div class = 'row'>
							<div class = 'col-md-1'></div>
							<?php
								echo $ui->formField('text')
										->setLabel('Date')
										->setSplit('col-md-3', 'col-md-8')
										->setName('importdate')
										->setId('importdate')
										->setClass('datepicker-input')
										->setAttribute(array('readonly' => ''))
										->setAddon('calendar')
										->setValue($importdate)
										->setValidation('required')
										->draw(true);
							?>
						</div>
						<div class="modal-footer text-center">
							<button type = 'button' class = 'btn btn-info btn-flat' name = 'import-proceed' id = 'import-proceed'><i id='loading' class="hidden fa fa-refresh fa-spin"></i> Proceed</button>
							<button type = 'button' class = 'btn btn-default btn-flat' name = 'import-skip' id = 'import-skip'><i id='loading' class="hidden fa fa-refresh fa-spin"></i> Skip</button>
						</div>	
					</div>

					<div id = 'import-step2'>
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_serial_import" id="download-link" download="Import Serial Numbers.csv" >here</a></label>
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
				</div>
				
			</form>
		</div>
	</div>
</div>

<!-- Serial Modal --> 
<div class="modal fade" id="serialModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" id = "modal_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Items</h4>
				<h5 class="modal-title"><span class="col-md-2">Item Code:</span><div class="col-md-10"><input type = "text" id = "sec_itemcode" style="background:white; border:white;"></div></h5>
				<h5 class="modal-title"><span class="col-md-2">Description:</span><div class="col-md-10"><input type = "text" id = "sec_description" style="background:white; border:white;"></div></h5>
				<input type = "hidden" id  = "checkcount">
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6 col-md-offset-6">
						<div class="input-group">
							<input id="sec_search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-addon">
								<i class="fa fa-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body no-padding">
				<table id="tableSerialList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
					<thead>
						<tr class="info">
							<th class="col-xs-2 checkbox_header"></th>
							<th id = "serial_header">Serial No.</th>
							<th id = "engine_header">Engine No.</th>
							<th id = "chassis_header">Chassis No.</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					<tfoot>
						<tr class="newline">
							<td>
								<button type="button" class="btn btn-link" id="addnewserialline">Add a New Line</button>
							</td>
						</tr>
					</tfoot>
				</table>
				<div id="serial_pagination"></div>
			</div>
			<div class="modal-footer">
				<div class="col-md-12 col-sm-12 col-xs-12 text-center">
					<div class="btn-group">
						<button id = "btn_tag" type = "button" class = "btn btn-primary btn-sm btn-flat" disabled>Tag</button>
					</div>
					&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<button id = "btn_close" type="button" class="btn btn-default btn-sm btn-flat">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

	function show_error(msg, warning) {
		$(".delete-modal").modal("hide");
		$(".alert-warning").removeClass("hidden");
		$("#errmsg").html(msg);
		$("#warningmsg").html(warning);
	}

	function show_success_msg(msg) {
		$('#success_modal #message').html(msg);
		$('#success_modal').modal('show');
	}
	
	function hide_error() {
		$(".alert-warning").addClass("hidden");
		$("#errmsg").html('');
		$("#warningmsg").html('');
	}

	/**VALIDATE FIELD**/
	function validateField(form,id,help_block) {
		var field	= $("#"+form+" #"+id).val();

		if(id.indexOf('_chosen') != -1){
			var id2	= id.replace("_chosen","");
			field	= $("#"+form+" #"+id2).val();
		}

		if(field == '' || parseFloat(field) == 0)
		{
			$("#"+form+" #"+id)
				.closest('.field_col')
				.addClass('has-error');

			$("#"+form+" #"+help_block)
				// .next(".help-block")
				.removeClass('hidden');
				
			if($("#"+form+" #"+id).parent().next(".help-block")[0])
			{
				$("#"+form+" #"+id)
				.parent()
				.next(".help-block")
				.removeClass('hidden');
			}
			return 1;
		}
		else
		{
			$("#"+form+" #"+id)
				.closest('.field_col')
				.removeClass('has-error');

			$("#"+form+" #"+help_block) //$("#"+form+" #"+id)
				// .next(".help-block")
				.addClass('hidden');
				
			if($("#"+form+" #"+id).parent().next(".help-block")[0])
			{
				$("#"+form+" #"+id)
				.parent()
				.next(".help-block")
				.addClass('hidden');
			}
			return 0;
		}
	}	

	function getCOAList(current_code){
		$.post('<?=MODULE_URL?>ajax/get_code', "code="+current_code, function(data) {
			$('#inventory_account').html(data.list);
		});
	}

	function adjustment(partno, partname, qty, ident_flag, action){ 
		$('#item_ident_flag').val(ident_flag);
		var has_serial = ident_flag.substring(0, 1);
		var has_engine = ident_flag.substring(1, 2);
		var has_chassis= ident_flag.substring(2, 3);
		// console.log("111 "+ident_flag);
		if(has_serial == 1 || has_engine == 1 || has_chassis == 1) { 
			// $('#adjModal #issueqty').prop()
		// console.log("1 "+has_serial);
		// console.log("2 "+has_engine);
		// console.log("3 "+has_chassis);
			$('#adjModal .serialized').removeClass('hidden');
			$('#adjModal .notserialized').addClass('hidden');
			
			if(has_serial==0){$('#serialModal #serial_header').addClass('hidden');} else {$('#serialModal #serial_header').removeClass('hidden');}
			if(has_engine==0){$('#serialModal #engine_header').addClass('hidden');} else {$('#serialModal #engine_header').removeClass('hidden');}
			if(has_chassis==0){	$('#serialModal #chassis_header').addClass('hidden');} else {$('#serialModal #chassis_header').removeClass('hidden');}
		} else {
			// $('#adjModal #issueqty').val(0);
			$('#adjModal .serialized').addClass('hidden');
			$('#adjModal .notserialized').removeClass('hidden');
		}

		$('#adjModal #modal_code').html(partno);
		$('#adjModal #itemcode').val(partno);
		$('#adjModal #modal_p_name').html(partname);
		$('#adjModal #itemname').val(partname);
		$('#adjModal #addminusbtn').val(action);
		$('#adjModal #remarks').val('');

		if( action == "plus" ){
			$('#adjModal #inventory_account').closest('.form-group').find('label').html("Credit Account <span style='color:red;'>*</span>");
			$('.newline').removeClass('hidden');
		}
		else if( action == 'minus' ){
			$('#adjModal #inventory_account').closest('.form-group').find('label').html("Debit Account <span style='color:red;'>*</span>");
			$('.newline').addClass('hidden');
		}

		getCOAList(partno);

		$('#adjModal').modal('show');
	}

	var ajax = filterFromURL();
	var ajax_serials = {};
	var ajax_manual = {};
	var ajax_call = '';
	var serial_box	=	[];
	var temp_serial_box = [];
	var serial_manual_box = [];
	var temp_serial_manual_box = [];
	
	ajaxToFilter(ajax,{ search: '#table_search', itemcode: '#itemcode', warehouse: '#warehouse'});

	$('#table_search').on('input', function () {
		ajax.search = $(this).val();
		ajax.page 	= 1;
		getList();
	});

	$('#itemcode').on('change', function() {
		ajax.itemcode 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});

	$('#brandcode').on('change', function() {
		ajax.brandcode 	= $(this).val();
		ajax.page 		= 1;
		getList();
	});

	$('#warehouse').on('change',function(){
		var warehouse = $(this).val();
		$('#h_warehouse').val(warehouse);

		ajax.warehouse 	= warehouse;
		ajax.page 		= 1;

		if( warehouse != "none" ){
			if ($('.w_selected').is(':hidden')) {
				$('#tableList tbody').html('');
			}
			$('.w_selected').show();
		}else{
			$('.w_selected').hide();
		}

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

	function displayBtn(){
		$.post('<?=MODULE_URL?>ajax/view_import_button', ajax, function(data) {
			if(data.display == 0){
				$('#import').addClass('hidden');
			}
		});
	}

	//Hide the table by default
	$('.w_selected').hide();
	// var ajax_saving = {};
	/**ADJUSTMENT: SAVING**/
	$("#adjustForm #btnSave").click(function(){
		var valid		= 0;
		var btn_ident 	= $('#addminusbtn').val();

		$("#adjustForm").find('.form-group').find('input, textarea, select').trigger('blur');

		if($("#adjustForm").find('.form-group.has-error').length == 0){	
			$("#adjustForm #btnSave").addClass('disabled');
			$("#adjustForm #btnSave_toggle").addClass('disabled');
			
			$("#adjustForm #btnSave").html('Saving...');
			var serials 		= JSON.stringify(serial_box);
			var serials_manual  = JSON.stringify(serial_manual_box);
			$.post("<?=MODULE_URL?>ajax/update_inventory",$('#adjustForm').serialize()+"&serials="+serials+"&serials_manual="+serials_manual)
			.done(function(data){
				$("#adjustForm #btnSave").removeClass('disabled');
				$("#adjustForm #btnSave_toggle").removeClass('disabled');
			
				$("#adjustForm #btnSave").html('Save');
				
				if( data.msg == 'success' )	{
					$.post("<?=MODULE_URL?>ajax/create_jv",$("#adjustForm").serialize()+"&adjustment_voucher="+data.voucher)
					.done(function(data){
						$("#adjustForm #btnSave").removeClass('disabled');
						$("#adjustForm #btnSave_toggle").removeClass('disabled');
					
						$("#adjustForm #btnSave").html('Save');
						
						if( data.msg == 'success' ){
							$("#adjModal").modal('hide');
							if(btn_ident == "minus"){
								serial_box = [];
								temp_serial_box = [];
							} else {
								serial_manual_box = [];
								temp_serial_manual_box = [];
							}
							$('#issueqtybtn').val(0);
							$('#issueqtybtn').html(0);
							$('#issueqty_serial').val(0);
						}
					});
				}
			});
			
		}
		else
		{
			var quantity 	=	$('#adjustForm #issueqty').val();

			if( quantity <= 0 )
			{
				$('#adjustForm #issueqty').closest('.form-group').addClass('has-error').find('p.help-block').html("This field is required");
			}

			$('#warning_modal #warning_message').html("<b>Please fill in the required fields before proceeding!</b>");
			$('#warning_modal').modal('show');
		}
	});

	$('#adjustForm #closeModal').click(function(){
		$('#adjustForm #issueqty').val(0);
		$('#adjustForm #remarks').text('');
		$('#adjustForm #inventory_account').val('').trigger('change');
		$("#adjustForm").find('.form-group').removeClass('has-error').find('p.help-block').html('');
		$('#adjModal').modal('hide');
	});

	$.post('<?=MODULE_URL?>ajax/retrieve_users', ajax, function(data) {
		$('#lockerModal #logged_users').html(data.user_lists);
	});
	
	$('#lockerModal').modal('show');

	$('#lockerModal').on('click','#btnProceed',function(){
		$.post('<?=MODULE_URL?>ajax/update_locktime', ajax, function(data) {
			if( data.msg == 'success' )
			{
				$('#lockerModal').modal('hide');
				document.getElementById('timer').innerHTML = 05 + ":" + 01;
				startTimer();

				var warehouse 	=	$('#warehouse').val();

				if( warehouse != "" ){
					$('#warehouse').change();
				}
				
			}
		});
		$('#lockerModal').modal('hide');
	});

	$('#lockerModal').on('click','#btnCancel',function(){
		window.history.back();
	});

	function startTimer() {
		var presentTime = document.getElementById('timer').innerHTML;
		var timeArray = presentTime.split(/[:]+/);
		var m = timeArray[0];
		var s = checkSecond((timeArray[1] - 1));

		if(s == 59){
			m = m-1
		}
		
		if( m == 0 && s == 30 )
		{
			$('#timerModal').modal('show');
			window.location = '<?php echo MODULE_URL;?>';
		}

		document.getElementById('timer').innerHTML = m + ":" + s;
		setTimeout(startTimer, 1000);
	}

	/** For Import Modal **/
	$("#import").click(function() 
	{
		$('#import-modal #import-step2').hide();
		$('#import-modal').modal('show');
	});

	$('#import-modal #import-skip').click(function(){

		$('#import-modal #import-skip #loading').removeClass('hidden');

		var date 		=	$('#importForm #importdate').val();
		date 			=  	retrieveformatteddate(date);
		
		var link 	=	'<?=MODULE_URL?>get_import/'+date;
		
		$('#import-modal #download-link').attr('href',link);
		setTimeout(function() {
			$('#import-modal #import-step1').hide();
			$('#import-modal #import-step2').show();
			$('#import-modal #btnYes').prop('disabled',false);
		},1000);
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
	});

    $('#import-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
		$('#import_csv').closest('.form-group').html(form_csv);

		$('#import-modal #import-skip #loading').addClass('hidden');
		$('#import-modal #import-proceed #loading').addClass('hidden');
		$('#import-modal #import-step1').show();
	});

	function retrieveformatteddate(date)
	{
		date = new Date(date);
		year = date.getFullYear();
		month = date.getMonth()+1;
		dt = date.getDate();

		if (dt < 10) {
		dt = '0' + dt;
		}
		if (month < 10) {
		month = '0' + month;
		}

		return year+'-' + month + '-'+dt;
	}

	$('#import-modal #import-proceed').click(function(){
		
		$('#import-modal #import-proceed #loading').removeClass('hidden');

		var date 		=	$('#importForm #importdate').val();
		date 			=  	retrieveformatteddate(date);
		
		var link 	=	'<?=MODULE_URL?>get_import/'+date;
		
		$('#import-modal #download-link').attr('href',link);

		setTimeout(function() {
			$('#import-modal #import-step1').hide();
			$('#import-modal #import-step2').show();
		},1000);
	});

	$("#importForm #btnImport").click(function() 
	{
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
									$(".alert-warning").addClass("hidden");
									$("#errmsg").html('');
									hide_error();
									show_success_msg('Your Data has been imported successfully.');
								}else{
									$('#import-modal').modal('hide');
									show_error(response.errmsg, response.warning);
								}
							},
						});
	});

	$('#success_modal .btn-success').on('click', function(){
		$('#success_modal').modal('hide');
		getList();
	});

	$(document).on("shown.bs.modal","#success_modal", function () { 
		setTimeout(function() {
			$('#success_modal').modal('hide');
		},1500);
		getList();
	});

	$(document).on('hidden.bs.modal','#adjModal', function () {
		getList();
		displayBtn();
		hide_error();
	});
	
	function checkSecond(sec) {
		if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
		if (sec < 0) {sec = "59"};
		return sec;
	}
	// Sorting Script
	tableSort('#tableList', function(value, getlist) {
		ajax.sort = value;
		ajax.page = 1;
		if (getlist) {
			getList();
		}
	}, ajax);

	function getSerialList(button_ident){
		filterToURL();
		if (ajax_call != '') {
			ajax_call.abort();
		}
		var itemcode = $('#adjustForm #itemcode').val();
		var itemname = $('#adjustForm #itemname').val();
		$('#serialModal #sec_itemcode').prop('disabled',true);
		$('#serialModal #sec_description').prop('disabled',true);
		$('#serialModal #sec_itemcode').val(itemcode);
		$('#serialModal #sec_description').val(itemname);
		
		ajax_serials.itemcode	=	itemcode;
		ajax_serials.warehouse	=	$('#warehouse').val();
		ajax_serials.limit 		= 	5;

		if(button_ident=="minus"){
			$.post('<?=MODULE_URL?>ajax/retrieve_serialsforminus', ajax_serials, function(data) {
				$('#tableSerialList tbody').html(data.table);
				$('#serial_pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getSerialList(button_ident);
					setCheckedSerials();
				}
			}).done(function(){
				$('.checkbox_header').removeClass('hidden');
				$('#tableSerialList tfoot.newline').addClass('hidden')
				$('#serialModal').modal('show');
			});
		} else {
			$('#tableSerialList tbody').html('');
			$('#serial_pagination').html('');
			var count_lines = $('#tableSerialList tbody tr').length; 
			$('.checkbox_header').addClass('hidden');
			$('#serialModal').modal('show');
			if(temp_serial_manual_box!=''){
				// For setting previously entered values
				$.each(temp_serial_manual_box,function(key,object){
					addnewserial(object,count_lines);
				});
			} else {
				addnewserial('',count_lines);
			}
		}
	}

	$('#serialModal #btn_close').on('click',function(){
		$('#serialModal').modal('hide');
		$('#serialModal #sec_search').val('');
		ajax_serials.search = "";
		getList();
		var button_ident = $('#addminusbtn').val();
		if(button_ident == "plus"){
			serial_manual_box = [];
			temp_serial_manual_box = [];
		} else {
			serial_box = [];
			temp_serial_box = [];
		}
		
	});

	$(document).on('click','.serialized',function(){
		var button_ident = $('#addminusbtn').val();
		if(button_ident == "plus"){
			temp_serial_manual_box = serial_manual_box;
		}
		getSerialList(button_ident);
	});

	// Pagination for Serialized
	$('#serialModal #serial_pagination').on('click', 'a', function(e) {
		var button_ident = $('#addminusbtn').val();
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax_serials.page = $(this).attr('data-page');
			getSerialList(button_ident);
		}
	});

	$('#tableSerialList').on('ifChecked','.check_id',function(){
		var serial_id = $(this).val();
		if(jQuery.inArray(serial_id, temp_serial_box) == -1){
			temp_serial_box.push(serial_id);
		}
		checkhascontent();
	});

	$('#tableSerialList').on('ifUnchecked','.check_id',function(){
		var remove_this  = 	$(this).val(); 
		temp_serial_box = jQuery.grep(temp_serial_box, function(value) {
			return value != remove_this;
		});
		checkhascontent();
	});

	$('#serialModal').on('click','#btn_tag',function(){
		var button_ident 	= $('#addminusbtn').val();
		$('#serialModal').modal('hide');

		var count = 0;
		if(button_ident=='minus'){
			serial_box 			= temp_serial_box;
			temp_serial_box 	= [];
			$.each(serial_box,function(key,value){
				count++;
			});
		} else {
			clean_serial_manual_box();
			serial_manual_box 		=	temp_serial_manual_box;
			temp_serial_manual_box 	=	[];
			$.each(serial_manual_box,function(key,value){
				count++;
			});
		}
		$('#issueqtybtn').val(count);
		$('#issueqtybtn').html(count);
		$('#issueqty_serial').val(count);
	});

	function setCheckedSerials(){
		console.log("temp");
		console.log(temp_serial_box);
		console.log("actual");
		console.log(serial_box);
		temp_serial_box = serial_box;
		$.each(temp_serial_box,function(key,value){
			$('#check_id'+value).iCheck('check');
		});
	}

	function checkhascontent(){
		console.log("Serial Box 11");
		console.log(serial_box);
		if(serial_box != [] || temp_serial_box != []){
			$('#btn_tag').prop('disabled',false);
		} else {
			$('#btn_tag').prop('disabled',true);
		}
	}
	$('#serialModal').on('show.bs.modal',function(){
		setCheckedSerials();
		checkhascontent();
	});

	$('#serialModal').on('change','#sec_search',function(){
		var button_ident = $('#addminusbtn').val();
		ajax_serials.search = $(this).val();
		ajax_serials.page 	= 1;
		getSerialList(button_ident);
	});	

	$('#addnewserialline').on('click', function() {
		var count_lines = $('#tableSerialList tbody tr').length; // This is to count the initial rows on the table after clicking the add new line button
		addnewserial('',count_lines);
	});
	
	// Plus ( for Adjustment )
	function addnewserial(details, index) {
		var details = details || {serialno: '', engineno: '', chassisno: ''};
		var ident_flag = $('#item_ident_flag').val();
		var has_serial = ident_flag.substring(0, 1);
		var has_engine = ident_flag.substring(1, 2);
		var has_chassis= ident_flag.substring(2, 3);

		var display_serial = display_engine = display_chassis = "";
			display_serial = (has_serial == 0) ? "class='hidden'" : ""; 
			display_engine = (has_engine == 0) ? "class='hidden'" : ""; 
			display_chassis= (has_chassis == 0)? "class='hidden'" : ""; 
		var row = `
		<tr>
			<td `+display_serial+`>
				<?php
					echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('serialno[]')
							->setClass('serialno')
							->setAttribute(
								array(
									'data-linenum' => "`+ index +`"
								)
							)
							->setMaxLength('20')
							->setValidation('alpha_num')
							->setValue('` + details.serialno + `')
							->draw();
				?>
				<div class="has-error"><strong><small class="error_message"></small></strong></div>
				</td>
			<td `+display_engine+`>
				<?php
					echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('engineno[]')
							->setClass('engineno')
							->setAttribute(
								array(
									'data-linenum' => "`+ index +`"
								)
							)
							->setMaxLength('20')
							->setValidation('alpha_num')
							->setValue('` + details.engineno + `')
							->draw();
				?>
				<div class="has-error"><strong><small class="error_message"></small></strong></div>
			</td>
			<td `+display_chassis+`>
				<?php
					echo $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('chassisno[]')
							->setClass('chassisno')
							->setAttribute(
								array(
									'data-linenum' => "`+ index +`"
								)
							)
							->setMaxLength('20')
							->setValidation('alpha_num')
							->setValue('` + details.chassisno + `')
							->draw();
				?>
				<div class="has-error"><strong><small class="error_message"></small></strong></div>
			</td>
		</tr>
		`;
		$('#tableSerialList tbody').append(row);
	}
	function initialize_serial_manual_box(index){
		if(temp_serial_manual_box[index] == undefined){
			temp_serial_manual_box[index] = {};
			if(temp_serial_manual_box[index]['serialno'] == undefined){
				temp_serial_manual_box[index]['serialno'] = "";
			}
			if(temp_serial_manual_box[index]['engineno'] == undefined){
				temp_serial_manual_box[index]['engineno'] = "";
			}
			if(temp_serial_manual_box[index]['chassisno'] == undefined){
				temp_serial_manual_box[index]['chassisno'] = "";
			}
		} 
	}
	function check_if_has_errors(){
		var count = $('#tableSerialList tbody tr').find('input').closest('.has-error').length;
		if(count > 0) {
			$('#serialModal #btn_tag').prop('disabled', true);
		} else {
			$('#serialModal #btn_tag').prop('disabled', false);
		}
	}
	function clean_serial_manual_box(){
		temp_serial_manual_box = temp_serial_manual_box.filter(function(v){return (v.serialno!="" || v.engineno !=="" || v.chassisno !== "") });
	}
	function checkexistingrows(fieldindex, fieldtype, fieldvalue){
		var exists 	=	0;
		$.each(temp_serial_manual_box, function(key, object) {
			$.each(object, function(index, value){
				if(value!=""){
					if(fieldindex!=key && fieldvalue == value && fieldtype == index) {
						exists	+=	1;
					}
				}
			});
		});
		return exists;
	}
	$('#tableSerialList').on('change','.serialno',function(){
		var current_selection = $(this);
		var current_serial 	  = current_selection.val();
		var index 			  = $(this).data('linenum');

		ajax_manual.itemcode   = $('#sec_itemcode').val();
		ajax_manual.fieldvalue = current_serial;
		ajax_manual.fieldtype  = "serial";

		if(current_serial!=""){
			var existingrow 	=	jQuery.inArray(current_serial, temp_serial_manual_box);
			$('#btn_tag').prop('disabled',true);
			$.post('<?=MODULE_URL?>ajax/checkifexisting', ajax_manual, function(data) {
				initialize_serial_manual_box(index);
				temp_serial_manual_box[index]['serialno'] = "";
				if(data.count > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Serial Number already exists in the Database!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				} else {
					current_selection.closest('div').removeClass('has-error');
					current_selection.closest('td').find('.error_message').html('');
					temp_serial_manual_box[index]['serialno'] = current_serial;
				}
			}).done(function(){
				var exists = checkexistingrows(index, "serialno",current_serial);
				if(exists > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Serial Number already exists within the Modal!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				}
				$('#btn_tag').prop('disabled',false);
				check_if_has_errors();
			});
		} else {
			temp_serial_manual_box[index]['serialno'] = "";
		}
	});
	$('#tableSerialList').on('change','.engineno',function(){
		// checkifexisting
		var current_selection = $(this);
		var current_engine 	  = current_selection.val();
		var index 			  = $(this).data('linenum');

		ajax_manual.itemcode   = $('#sec_itemcode').val();
		ajax_manual.fieldvalue = current_engine;
		ajax_manual.fieldtype  = "engine";

		if(current_engine!=""){
			$('#btn_tag').prop('disabled',true);
			$.post('<?=MODULE_URL?>ajax/checkifexisting', ajax_manual, function(data) {
				initialize_serial_manual_box(index);
				temp_serial_manual_box[index]['engineno'] = "";
				if(data.count > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Engine Number already exists in the Database!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				} else {
					current_selection.closest('div').removeClass('has-error');
					current_selection.closest('td').find('.error_message').html('');
					temp_serial_manual_box[index]['engineno'] = current_engine;
				}
			}).done(function(){
				var exists = checkexistingrows(index, "engineno",current_engine);
				if(exists > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Engine Number already exists within the Modal!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				}
				$('#btn_tag').prop('disabled',false);
				check_if_has_errors();
			});
		} else {
			temp_serial_manual_box[index]['engineno'] = "";
		}
	});
	$('#tableSerialList').on('change','.chassisno',function(){
		var current_selection = $(this);
		var current_chassis   = current_selection.val();
		var index 			  = $(this).data('linenum');

		ajax_manual.itemcode   = $('#sec_itemcode').val();
		ajax_manual.fieldvalue = current_chassis;
		ajax_manual.fieldtype  = "chassis";

		if(current_chassis!=""){
			$('#btn_tag').prop('disabled',true);
			$.post('<?=MODULE_URL?>ajax/checkifexisting', ajax_manual, function(data) {
				initialize_serial_manual_box(index);
				temp_serial_manual_box[index]['chassisno'] = "";
				if(data.count > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Chassis Number already exists in the Database!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				} else {
					current_selection.closest('div').removeClass('has-error');
					current_selection.closest('td').find('.error_message').html('');
					temp_serial_manual_box[index]['chassisno'] = current_chassis;
				}
			}).done(function(){
				var exists = checkexistingrows(index, "chassisno",current_chassis);
				if(exists > 0){
					current_selection.closest('div').addClass('has-error');
					current_selection.closest('td').find('.error_message').html('<span style="padding-left:15px;" class="glyphicon glyphicon-exclamation-sign"></span> This Chassis Number already exists within the Modal!');
					current_selection.closest('td').find('.error_message').closest('div').attr('style','color:red');
				}
				$('#btn_tag').prop('disabled',false);
				check_if_has_errors();
			});
		} else {
			temp_serial_manual_box[index]['chassisno'] = "";
		}
	});
</script>