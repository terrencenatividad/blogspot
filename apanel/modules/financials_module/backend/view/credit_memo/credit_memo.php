	<form id = "VendorDetailForm">
			<input class = "form_iput" value = "" name = "h_terms" id="h_terms" type="hidden">
			<input class = "form_iput" value = "" name = "h_tinno" id="h_tinno" type="hidden">
			<input class = "form_iput" value = "" name = "h_address1" id="h_address1" type="hidden">
			<input class = "form_iput" value = "update" name = "h_querytype" id="h_querytype" type="hidden">
			<input class = "form_iput" value = "" name = "h_condition" id = "h_condition" type="hidden">
			<input class = "form_iput" value = "<?=$h_disctype?>" name = "h_disctype" id = "h_disctype" type="hidden">
	</form>
	
	<section class="content">
		<div class="box box-primary">
			<form action=""  id= "cm" method="post" class="form-horizontal">
				<div class="box-body">
					<br>
					<div class="row">
						<div class="col-md-6">
							<?php if ($show_input): ?>
								<div class="form-group">
									<label for="voucherno" class="control-label col-md-4">Voucher No.</label>
									<div class="col-md-8">
										<?php if (substr($voucherno, 0, 3) == 'TMP'): ?>
											<input type="text" class="form-control" readonly value=" - Auto Generated - ">
										<?php else: ?> 
											<input type="text" class="form-control" readonly value="<?= (empty($voucherno)) ? ' - Auto Generated -' : $voucherno ?>">
										<?php endif ?>
									</div>
								</div>
							<?php else: ?>
								<?php
									echo $ui->formField('text')
										->setLabel('Voucher No.')
										->setSplit('col-md-4', 'col-md-8')
										->setName('voucherno')
										->setId('voucherno')
										->setValue($voucherno)
										->setValidation('required')
										->draw($show_input);
								?>
							<?php endif ?>
						</div>
						<div class="col-md-6">
							<?php
								echo $ui->formField('text')
									->setLabel('Transaction Date')
									->setSplit('col-md-4', 'col-md-8')
									->setName('transactiondate')
									->setId('transactiondate')
									->setClass('datepicker-input')
									->setAddon('calendar')
									->setValue($transactiondate)
									->setAttribute(array('readonly'=>'','data-date-start-date'=>$close_date))
									->setValidation('required')
									->draw($show_input);
							?>
						</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<!--<?php
									echo $ui->formField('dropdown')
										->setLabel('Vendor')
										->setPlaceholder('Select Vendor')
										->setSplit('col-md-4', 'col-md-8')
										->setName('vendor')
										->setId('vendor')
										->setList($partner_list)
										->setValue($vendor)
										->setValidation('required')
										->draw($show_input);
								?>-->
								<?php
										echo $ui->formField('dropdown')
											->setLabel('Partner')
											->setPlaceholder('None')
											->setSplit('col-md-4', 'col-md-8')
											->setName('partner')
											->setId('partner')
											->setList($partner_list)
											->setValue($partner)
											->setValidation('required')
											->draw($show_input);
								
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('text')
											->setLabel('Reference No.')
											->setSplit('col-md-4', 'col-md-8')
											->setName('referenceno')
											->setName('referenceno')
											->setId('referenceno')
											->setAttribute(array('readonly'))
											->setAddon('search')
											->setValue($referenceno)
											// ->setValidation('required')
											->draw($show_input);
								?>
							</div>
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setLabel('Proforma')
										->setPlaceholder('Select Proforma')
										->setSplit('col-md-4', 'col-md-8')
										->setName('proformacode')
										->setId('proformacode')
										->setList($proforma_list)
										->setValue($proformacode)
										->draw($show_input);
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('text')
										->setLabel('<i>Source No. : </i>')
										->setSplit('col-md-4', 'col-md-7')
										->setName('si_no')
										->setId('si_no')
										->setAttribute( 	
											array(
												"readonly" => "", 
												"maxlength" => "15",
												"tabindex" => -1
											)
										)
										->setClass("input_label")
										->setValue($si_no)
										->draw($show_input);
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('text')
										->setLabel('<i> Amount : </i>')
										->setSplit('col-md-4', 'col-md-7')
										->setName('sr_amount')
										->setId('sr_amount')
										->setAttribute( 	
											array(
												"readonly" => "", 
												"maxlength" => "15",
												"tabindex" => -1
											)
										)
										->setClass("input_label")
										->setValue(($sr_amount) ? number_format($sr_amount, 2) : '0.00')
										->draw($show_input);
								?>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<?php
									echo $ui->formField('textarea')
										->setLabel('Notes')
										->setSplit('col-md-2', 'col-md-10')
										->setName('remarks')
										->setId('remarks')
										->setValue($remarks)
										// ->setValidation('required')
										->draw($show_input);
								?>
							</div>
						</div>
					</div>
					<div class="box-body table-responsive no-padding">
						<table id="tableList" class="table table-hover table-sidepad">
							<thead>
								<tr class="info">
									<th class="col-xs-3">Account</th>
									<th>Description</th>
									<th class="col-xs-2 text-right">Debit</th>
									<th class="col-xs-2 text-right">Credit</th>
									<th style="width: 50px;"></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
							<tfoot>
								<tr>
									<td>
										<?php if ($show_input): ?>
											<button type="button" class="btn btn-link" onClick="addVoucherDetails()">Add a New Line</button>
										<?php endif ?>
									</td>
									<td>
										<p id="error_msg" class="help-block text-red text-right"></p>
									</td>
									<td class="text-right">
										<b id="total_debit" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
									</td>
									<td class="text-right">
										<b id="total_credit" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
									</td>
									<td></td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="box-body">
					<hr>
					<div class="row">
						<div class="col-md-12 text-center">
							<?php 
								if($restrict_cm && $status != 'cancelled'){
									echo $ui->drawSubmit($show_input); 
								}
							?>
							<!-- <a href="<?=MODULE_URL?>"  data-toggle="back_page" class="btn btn-default">Cancel</a> -->
							<?php echo $ui->drawCancel(); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>

	
	<div id="ordered_list_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Return List</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-8">
							<div class="input-group">
								<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-addon">
									<i class="fa fa-search"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body no-padding">
					<table id="ordered_tableList" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Return No.</th>
								<th class="col-xs-3">Transaction Date</th>
								<th class="col-xs-4">Reference No.</th>
								<th class="col-xs-2 text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="4" class="text-center">Loading Items</td>
							</tr>
						</tbody>
					</table>
					<div id="pagination"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="return_details" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Return List</h4>
				</div>
				<div class="modal-body">
					<div class="box-body">
			<br>
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group"><label for="voucherno" class="control-label col-md-4">Return No.</label><div class="col-md-8 voucherno"><p class="form-control-static"></p><p class="help-block m-none"></p></div></div>													</div>
						<div class="col-md-6">
							<div class="form-group"><label for="partnername" class="control-label col-md-4">Partner</label><div class="col-md-8 partnername"><p class="form-control-static "></p><p class="help-block m-none"></p></div></div>						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group"><label for="source_no" class="control-label col-md-4">Reference No.</label><div class="col-md-8 source_no"><p class="form-control-static "></p><p class="help-block m-none"></p></div></div>													</div>
						<div class="col-md-6">
							<div class="form-group"><label for="transactiondate" class="control-label col-md-4">Document Date</label><div class="col-md-8 transactiondate"><p class="form-control-static "></p><p class="help-block m-none"></p></div></div></div>
					</div>
					
				</div>
			</div>
		</div>
				<div class="modal-body no-padding">
					<table id="return_table" class="table table-hover table-clickable table-sidepad">
						<thead>
							<tr class="info">
								<th class="col-xs-3">Item Code</th>
								<th class="col-xs-4">Description</th>
								<th class="col-xs-1">Unit Cost</th>
								<th class="col-xs-1">Qty</th>
								<th class="col-xs-1">UOM</th>
								<th class="col-xs-2 text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="5" class="text-center">Loading Items</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer text-center">
					<button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<script>
	function addVendorToDropdown() {

		var optionvalue = $("#vendor_modal #supplierForm #partnercode").val();
		var optiondesc 	= $("#vendor_modal #supplierForm #partnername").val();

		$('<option value="'+optionvalue+'">'+optiondesc+'</option>').insertAfter("#cm #vendor option:nth-child(4)");
		$('#cm #vendor').val(optionvalue);
		
		// getPartnerInfo(optionvalue);

		$('#vendor_modal').modal('hide');
		$('#vendor_modal').find("input[type=text], textarea, select").val("");
	}
	function closeModal(){
		$('#vendor_modal').modal('hide');
	}
</script>
<?php
	echo $ui->loadElement('modal')
		->setId('vendor_modal')
		->setContent('maintenance/supplier/create')
		->setHeader('Add a Vendor')
		->draw();
?>
	
	<script>
		var delete_row		= {};
		var ajax_call		= '';
		var ajax_call2		= '';
		var ajax_call3		= '';
		var ajax_call4		= '';
		var ajax			= {};
		var proformacode	= '<?php echo $proformacode ?>';
		var min_row			= 2;
		function addVoucherDetails(details, index) {
			var details = details || {accountcode: '', detailparticulars: '', debit: '', credit: ''};
			var row = `
				<tr>
					<td>
						<?php
							$value = ($show_input) ? '' : '<span id="temp_view_` + index + `"></span>';
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Account')
								->setSplit('', 'col-md-12')
								->setName('accountcode[]')
								->setList($chartofaccounts)
								->setValidation('required')
								->setValue($value)
								->draw($show_input);
						?>
					</td>
					<td>
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('detailparticulars[]')
								->setValue('` + details.detailparticulars + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right debit_column">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('debit[]')
								->setClass('text-right')
								->setValidation('required decimal')
								->setValue('` + (parseFloat(details.debit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + `')
								->draw($show_input);
						?>
					</td>
					<td class="text-right credit_column">
						<?php
							echo $ui->formField('text')
								->setSplit('', 'col-md-12')
								->setName('credit[]')
								->setClass('text-right')
								->setValidation('required decimal')
								->setValue('` + (parseFloat(details.credit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + `')
								->draw($show_input);
						?>
					</td>
					<td>
						<?php if ($show_input): ?>
						<button type="button" class="btn btn-danger delete_row" data-id="222" style="outline:none;">
							<span class="glyphicon glyphicon-trash"></span>
						</button>
						<?php endif ?>
					</td>
				</tr>
			`;
			$('#tableList tbody').append(row);
			<?php if ($show_input): ?>
				if (details.accountcode != '') {
					$('#tableList tbody').find('tr:last select').val(details.accountcode);
				}
				try {
					drawTemplate();
				} catch(e) {};
			<?php else: ?>
				var accountlist = <?= json_encode($chartofaccounts) ?>;
				accountlist.forEach(function(account) {
					if (account.ind == details.accountcode) {
						$('#temp_view_' + index).html(account.val);
					}
				});
			<?php endif ?>
			addTotal('#total_credit', details.credit);
			addTotal('#total_debit', details.debit);
		}
		function recomputeTotal() {
			$('#total_debit').html('0.00');
			$('#total_credit').html('0.00');
			$('[name="debit[]"]').each(function() {
				addTotal('#total_debit', $(this).val());
			});
			$('[name="credit[]"]').each(function() {
				addTotal('#total_credit', $(this).val());
			});
		}
		var voucher_details = <?=$voucher_details?>;
		function displayDetails(voucher_details) {
			$('#tableList tbody').html('');
			if (voucher_details.length > 0) {
				voucher_details.forEach(function(voucher_details, index) {
					addVoucherDetails(voucher_details, index);
				});
			} else if (min_row == 0) {
				$('#tableList tbody').append(`
					<tr>
						<td colspan="5" class="text-center"><b>Select Packing No.</b></td>
					</tr>
				`);
			}
			if (voucher_details.length < min_row) {
				for (var x = voucher_details.length; x < min_row; x++) {
					addVoucherDetails('', x);
				}
			}
			recomputeTotal();
		}
		displayDetails(voucher_details);
		function addTotal(id, amount) {
			var old = parseFloat($(id).html().replace(/\,/g,'') || 0);
			$(id).html((old + parseFloat(amount.replace(/\,/g,'') || 0)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
		}
		var proforma = [];
		function revertProforma(code) {
			proformacode = code;
			$('#proformacode').val(code).trigger('change');
		}
		function changeProforma() {
			displayDetails(proforma);
			$('#proformacode').trigger('change');
		}
		$('#proformacode').on('change', function() {
			if (proformacode != $(this).val()) {
				var temp = proformacode;
				proformacode = $(this).val();
				if (ajax_call2 != '') {
					ajax_call2.abort();
				} 
				ajax_call2 = $.post('<?=MODULE_URL?>ajax/ajax_get_proforma', 'proformacode=' + proformacode, function(data) {
					proforma = data.proforma;
					if (temp !== '') {
						showConfirmationLink('changeProforma()', `revertProforma('` + temp + `')`, `Are you sure you want to apply this proforma? <br> Applying this would overwrite the existing entries you've added.`);
					} else {
						displayDetails(proforma);
					}
				});
			}
		});
		<?php if ($show_input): ?>
		$('body').on('input blur', '[name="debit[]"], [name="credit[]"]', function() {
			recomputeTotal();
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
			if (total_debit > 0 && total_credit > 0 && total_debit != total_credit) {
				$('#error_msg').html('Total Debit and Total Credit must match');
			} else {
				$('#error_msg').html('');
			}
		});
		$('body').on('blur', '[name="debit[]"]', function() {
			var val = parseFloat($(this).val().replace(/\,/g,'') || 0);
			if (val > 0) {
				$(this).closest('tr').find('[name="credit[]"]').val('0.00');
			}
		});
		$('body').on('blur', '[name="credit[]"]', function() {
			var val = parseFloat($(this).val().replace(/\,/g,'') || 0);
			if (val > 0) {
				$(this).closest('tr').find('[name="debit[]"]').val('0.00').trigger('input');
			}
		});
		function deleteVoucherDetails(id) {
			delete_row.remove();
			if ($('#tableList tbody tr').length <= 1) {
				addVoucherDetails();
			}
		}
		$('body').on('click', '.delete_row', function() {
			delete_row = $(this).closest('tr');
		});
		$(function() {
			linkDeleteToModal('.delete_row', 'deleteVoucherDetails');
		});
	
		$('form').on('click', '[type="submit"]', function(e) {
			e.preventDefault();
			var form_element = $(this).closest('form');
			var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g,'') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g,'') || 0);
			form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
				if (form_element.closest('form').find('.form-group.has-error').length == 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' + '&finalized=finalized' + submit_data, function(data) {
						if (data.success) {
							window.location = data.redirect;
						}
					});
				} else {
					form_element.closest('form').find('.form-group.has-error').first().find('input, textarea, select').focus();
				}
			} else if (total_debit <= 0 || total_credit <= 0) {
				$('#error_msg').html('Total Debit and Total Credit must have a value');
			} else {
				$('#error_msg').html('Total Debit and Total Credit must match');
			}
		});
		<?php else: ?>
			$('#total_debit').html('0.00');
			$('#total_credit').html('0.00');
			$('#tableList tbody td.debit_column .form-control-static').each(function() {
				addTotal('#total_debit', $(this).html());
			});
			$('#tableList tbody td.credit_column .form-control-static').each(function() {
				addTotal('#total_credit', $(this).html());
			});
		<?php endif ?>

		$('#vendor_button').click(function()
		{
			$('#vendor_modal').modal('show');
		});

		/**FOR ADD NEW VENDOR TRANSACTION**/
		function addNewModal(type,val,row)
		{
			row 		= row.replace(/[a-z]/g, '');
			if(val == 'add')
			{
				if(type == 'Vendor')
				{
					$('#vendor_modal').modal();
					$('#Vendor').val('');
				}
			}
		}

		$('#referenceno').on('click', function() {
			var customer = $('#partner').val();
			ajax.customer = customer;
			if (customer == '') {
				$('#warning_modal').modal('show').find('#warning_message').html('Please Select a Partner');
				$('#customer').trigger('blur');
			} else {
				$('#ordered_list_modal').modal('show');
				getList();
			}
		});

		$('#ordered_tableList').on('click', 'tr[data-id]', function() {
			var so = $(this).attr('data-id');
			var result = so.split(',');
			var a = result[0];
			var b = result[1];
			var c = result[2];
			$('#referenceno').val(a).trigger('blur');
			$('#si_no').val(b).trigger('blur');
			$('#sr_amount').val(addCommas(c)).trigger('blur');
			$('#ordered_list_modal').modal('hide');
		});

		$('#partner').on('change',function(){
			var emp = '';
			$('#referenceno').val(emp);
			$('#si_no').val(emp);
			$('#sr_amount').val(emp);
		});

		$('#ordered_tableList').on('click', 'a[data-id]', function(e){
			e.stopPropagation();
			var sr_no = $(this).html();
			ajax.sr_no = sr_no;
			$('#return_details').modal('show');
			if (ajax_call3 != '') {
				ajax_call3.abort();
			}
			ajax_call3 = $.post('<?=MODULE_URL?>ajax/ajax_load_return_details', ajax, function(data){
				$('#return_table tbody').html(data.table);
				$('.voucherno').html(data.voucherno);
				$('.source_no').html(data.source_no);
				$('.partnername').html(data.partnername);
				$('.transactiondate').html(data.transactiondate);
			});
		});

		function addCommas(nStr) {
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}

		function getList() {
			ajax.limit = 5;
			$('#ordered_list_modal').modal('show');
			if (ajax_call4 != '') {
				ajax_call4.abort();
			}
			console.log(ajax);
			ajax_call4 = $.post('<?=MODULE_URL?>ajax/ajax_load_ordered_list', ajax, function(data) {
				$('#ordered_tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		$('#table_search').on('input', function() {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		$('.cancel').click(function() 
		{
			$('#cancelModal').modal('show');
			$('#cancelModal').on('click', '#btnYes', function() {
				$('#cancelModal').modal('hide');
				window.location =	"<?= MODULE_URL ?>";
				});
		});

	</script>