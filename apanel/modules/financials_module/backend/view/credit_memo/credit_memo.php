<form id="VendorDetailForm">
	<input class="form_iput" value="" name="h_terms" id="h_terms" type="hidden">
	<input class="form_iput" value="" name="h_tinno" id="h_tinno" type="hidden">
	<input class="form_iput" value="" name="h_address1" id="h_address1" type="hidden">
	<input class="form_iput" value="update" name="h_querytype" id="h_querytype" type="hidden">
	<input class="form_iput" value="" name="h_condition" id="h_condition" type="hidden">
	<input class="form_iput" value="<?=$h_disctype?>" name="h_disctype" id="h_disctype" type="hidden">
</form>

<section class="content">
	<div class="box box-primary">
		<form action="" id="cm" method="post" class="form-horizontal">
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
										->setLabel('Partner ')
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
						<div class="form-group">
							<label class="control-label col-md-4">Job Items </label>
							<div class="col-md-8">
								<?php if($ajax_task != 'ajax_view') {?>
								<input type="hidden" name="jobs_tagged" id="jobs_tagged" value="<?php echo $job_no; ?>">
								<button type="button" id="job" class="btn btn-block btn-success btn-flat" <?php //echo $val ?>>
									<em class="pull-left"><small>Click to tag job items</small></em>
									<strong id="job_text" class="pull-right"> 
									<?php if($ajax_task == 'ajax_edit') {?>
										<?php $tags = explode(',', $job_no); ?>
										<?php $tags = ($tags[0] == '') ? 0 : count($tags); ?>
									<?php echo $tags; ?>
									<?php } else { ?>
									<?php } ?>
									</strong>
								</button>
								<?php } else { ?>
								<span>
								<?php echo substr($job_no, 0, 20) . '...'; ?></span>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
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
					<div class="col-md-6">
						<?php
									echo $ui->formField('dropdown')
									->setLabel('Currency')
									->setSplit('col-md-4', 'col-md-8')
									->setName('currencycode')
									->setId('currencycode')
									->setDefault('PHP')
									->setValue($currency)
									->setList($currencycodes)
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
					<div class="col-md-6">
					<?php
									echo $ui->formField('text')
									->setLabel('Exchange Rate')
									->setPlaceholder('0.00')
									->setSplit('col-md-4', 'col-md-8')
									->setName('exchangerate')
									->setId('exchangerate')
									->setValue($exchangerate)
									->setClass('text-right')
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
							<th class="col-xs-2 text-right">Currency Amount</th>	
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
							<td class="text-right">
								<b id="total_currency" class="form-<?= ($show_input) ? 'padding' : 'control-static' ?>"></b>
							</td>
							<td></td> 
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="box-body">
				<hr>
				<div class="row">
					<div class="col-md-12 text-center" id="saving_buttons">
						<?php 
										if($restrict_cm && $status != 'cancelled'){
											if($ajax_task == 'ajax_create' ){
												echo $ui->addSavePreview()
												->addSaveNew()
												->addSaveExit()
												->drawSaveOption();
											}
											if($ajax_task == 'ajax_edit') {
												echo $ui->drawSubmit(true);
											}
										}
										if($ajax_task == 'ajax_view' && $display_edit) {
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

<div class="modal fade" id="jobModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				Choose Job to tag
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body table-reponsive no-padding">
				<form id="jobform" class="form-horizontal" method="post">
					<table id="jobsTable" class="table table-hover table-sidepad mb-none">
						<?php
							echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('', array('class' => 'col-md-1'))
							->addHeader('Job Number', array('class' => 'col-md-3'))
							->draw();
							?>
						<tbody>
						</tbody>
						<textarea hidden class="job_append"></textarea>
					</table>
					<div id="paginate"></div>
			</div>
			<div class="modal-footer force-right">
				<input type="submit" class="btn btn-primary btn-flat" id="confirmJob" value="Tag">
				<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
				</form>
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
									<div class="form-group"><label for="voucherno" class="control-label col-md-4">Return No.</label>
										<div class="col-md-8 voucherno">
											<p class="form-control-static"></p>
											<p class="help-block m-none"></p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group"><label for="partnername" class="control-label col-md-4">Partner</label>
										<div class="col-md-8 partnername">
											<p class="form-control-static "></p>
											<p class="help-block m-none"></p>
										</div>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group"><label for="source_no" class="control-label col-md-4">Reference No.</label>
										<div class="col-md-8 source_no">
											<p class="form-control-static "></p>
											<p class="help-block m-none"></p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group"><label for="transactiondate" class="control-label col-md-4">Document Date</label>
										<div class="col-md-8 transactiondate">
											<p class="form-control-static "></p>
											<p class="help-block m-none"></p>
										</div>
									</div>
								</div>
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
			var optiondesc = $("#vendor_modal #supplierForm #partnername").val();

			$('<option value="' + optionvalue + '">' + optiondesc + '</option>').insertAfter("#cm #vendor option:nth-child(4)");
			$('#cm #vendor').val(optionvalue);

			// getPartnerInfo(optionvalue);

			$('#vendor_modal').modal('hide');
			$('#vendor_modal').find("input[type=text], textarea, select").val("");
		}

		function closeModal() {
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
		var delete_row = {};
		var ajax_call = '';
		var ajax_call2 = '';
		var ajax_call3 = '';
		var ajax_call4 = '';
		var ajax = {};
		var proformacode = '<?php echo $proformacode ?>';
		var min_row = 2;

		function addVoucherDetails(details, index) {
			var details = details || {
				accountcode: '',
				detailparticulars: '',
				debit: '0.00',
				credit: '0.00',
				currency: '0.00'
			};
			var row = `
				<tr>
				<td>
				<?php
				$value = ($show_input) ? '' : '<span id="temp_view_` + index +
						`"></span>';
				echo $ui->formField('dropdown')
				->setPlaceholder('Select Account')
				->setSplit('', 'col-md-12')
				->setName('accountcode[]')
				->setClass('accountcode')
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
				->setValue('` +
						details.detailparticulars +
						`')
				->draw($show_input);
				?>
				</td>
				<td class="text-right debit_column">
				<?php
				echo $ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('debit[]')
				->setId('debit')
				->setClass('debit text-right')
				->setValidation('required decimal')
				->setValue('` +
						(parseFloat(details.debit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") +
						`')
				->draw($show_input);
				?>
				</td>
				<td class="text-right credit_column">
				<?php
				echo $ui->formField('text')
				->setSplit('', 'col-md-12')
				->setName('credit[]')
				->setId('credit')
				->setClass('credit text-right')
				->setValidation('required decimal')
				->setValue('` +
						(parseFloat(details.credit) || 0).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") +
						`')
				->draw($show_input);
				?>
				</td>
				<td class="text-right credit_column">
				<?php
				echo $ui->formField('text')
					->setPlaceholder('0.00')
					->setSplit('col-md-2', 'col-md-10')
					->setLabel('<span class="label label-default currency_symbol">PHP</span>')
					->setName('currencyamount[]')
					->setId('currencyamount')
					->setAttribute(array("maxlength" => "20", 'readonly'))
					->setClass("currencyamount text-right")
					->setValidation('decimal')
					->setValue('` +
					(parseFloat(details.currency) || 0.00).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") +
					`')
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
			} catch (e) {};
			<?php else: ?>
			var accountlist = <?= json_encode($chartofaccounts) ?>;
			accountlist.forEach(function (account) {
				if (account.ind == details.accountcode) {
					$('#temp_view_' + index).html(account.val);
				}
			});
			<?php endif ?>
			addTotal('#total_credit', details.credit);
			addTotal('#total_debit', details.debit);
			addTotal('#total_currency', details.currency);
		}

		function recomputeTotal() {
			$('#total_debit').html('0.00');
			$('#total_credit').html('0.00');
			$('#total_currency').html('0.00');
			$('[name="debit[]"]').each(function () {
				addTotal('#total_debit', $(this).val());
			});
			$('[name="credit[]"]').each(function () {
				addTotal('#total_credit', $(this).val());
			});
			$('[name="currencyamount[]"]').each(function () {
				addTotal('#total_currency', $(this).val());
			});

		}
		var voucher_details = <?=$voucher_details?>;

		function displayDetails(voucher_details) {
			$('#tableList tbody').html('');
			if (voucher_details.length > 0) {
				voucher_details.forEach(function (voucher_details, index) {
					addVoucherDetails(voucher_details, index);
				});
			} else if (min_row == 0) {
				$('#tableList tbody').append(
					`
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
			var old = parseFloat($(id).html().replace(/\,/g, '') || 0);
			$(id).html((old + parseFloat(removeComma(amount) || 0)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
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
		$('#proformacode').on('change', function () {
			if (proformacode != $(this).val()) {
				var temp = proformacode;
				proformacode = $(this).val();
				if (ajax_call2 != '') {
					ajax_call2.abort();
				}
				ajax_call2 = $.post('<?=MODULE_URL?>ajax/ajax_get_proforma', 'proformacode=' + proformacode, function (data) {
					proforma = data.proforma;
					if (temp !== '') {
						showConfirmationLink('changeProforma()', `revertProforma('` + temp + `')`,
							`Are you sure you want to apply this proforma? <br> Applying this would overwrite the existing entries you've added.`
						);
					} else {
						displayDetails(proforma);
					}
				});
			}
		});
		<?php if ($show_input): ?>
		$('body').on('input blur', '[name="debit[]"], [name="credit[]"]', function () {
			recomputeTotal();
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g, '') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g, '') || 0);
			if (total_debit > 0 && total_credit > 0 && total_debit != total_credit) {
				$('#error_msg').html('Total Debit and Total Credit must match');
			} else {
				$('#error_msg').html('');
			}
		});
		var debit_currency = 0;
		var credit_currency = 0;
		$('body').on('blur', '[name="debit[]"]', function () {
			var val = parseFloat($(this).val().replace(/\,/g, '') || 0);
			var rate = removeComma($('#exchangerate').val());
			if (val > 0) {
				$(this).closest('tr').find('[name="credit[]"]').val('0.00');
			}
			if ($(this).val() != '' && $(this).val() != '0.00') {
				debit_currency = $(this).val() * rate;
				$(this).closest('tr').find('.currencyamount').val(addComma(debit_currency)).attr('name', 'currencydebit');
				//$(this).closest('tr').find('.credit').attr('readonly', 'readonly');
			}
			recomputeTotal();
			sumCurrencyAmount();
		});
		$('body').on('blur', '[name="credit[]"]', function () {
			var val = parseFloat($(this).val().replace(/\,/g, '') || 0);
			var rate = removeComma($('#exchangerate').val());
			if (val > 0) {
				$(this).closest('tr').find('[name="debit[]"]').val('0.00').trigger('input');
			}
			if ($(this).val() != '' && $(this).val() != '0.00') {
				credit_currency = $(this).val() * rate;
				$(this).closest('tr').find('.currencyamount').val(addComma(credit_currency)).attr('name', 'currencycredit');
				//$(this).closest('tr').find('.credit').attr('readonly', 'readonly');
			}
			recomputeTotal();
			sumCurrencyAmount();
		});

		function deleteVoucherDetails(id) {
			delete_row.remove();
			if ($('#tableList tbody tr').length <= 1) {
				addVoucherDetails();
			}
		}
		$('body').on('click', '.delete_row', function () {
			delete_row = $(this).closest('tr');
		});
		$(function () {
			linkDeleteToModal('.delete_row', 'deleteVoucherDetails');
		});

		$('form').on('click', '[id="save"]', function (e) {
			e.preventDefault();
			$('#tableList tbody tr td').find('.accountcode').find('option[disabled]').prop('disabled', false)
			var form_element = $(this).closest('form');
			var submit_data = '&submit=' + $(this).attr('id');
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g, '') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g, '') || 0);
			form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
				$('#error_msg').html('');
				console.log('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' +
						'&finalized=finalized' + submit_data + '&job=' + job);
				if (form_element.closest('form').find('.form-group.has-error').length == 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' +
						'&finalized=finalized' + submit_data + '&job=' + job, 
						function (data) {
							if (data.success) {
								$('#save').prop('disabled', true);
								$('#save_options').find('button').prop('disabled', true);
								$('#saving_buttons').find('.cancel').addClass('disabled', true);
								$('#delay_modal').modal('show');
								setTimeout(function () {
									window.location = data.redirect;
								}, 1000)
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
		$('form').on('click', '[id="save_new"]', function (e) {
			e.preventDefault();
			$('#tableList tbody tr td').find('.accountcode').find('option[disabled]').prop('disabled', false)
			var form_element = $(this).closest('form');
			var submit_data = '&submit=' + $(this).attr('id');
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g, '') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g, '') || 0);
			form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
				$('#error_msg').html('');
				if (form_element.closest('form').find('.form-group.has-error').length == 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' +
						'&finalized=finalized' + submit_data + '&job=' + job,
						function (data) {
							if (data.success) {
								$('#save').prop('disabled', true);
								$('#save_options').find('button').prop('disabled', true);
								$('#saving_buttons').find('.cancel').addClass('disabled', true);
								setTimeout(function () {
									window.location = data.redirect;
								}, 1000)
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
		$('form').on('click', '[id="save_exit"]', function (e) {
			e.preventDefault();
			$('#tableList tbody tr td').find('.accountcode').find('option[disabled]').prop('disabled', false)
			var form_element = $(this).closest('form');
			var submit_data = '&submit=' + $(this).attr('id');
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g, '') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g, '') || 0);
			form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
				$('#error_msg').html('');
				if (form_element.closest('form').find('.form-group.has-error').length == 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' +
						'&finalized=finalized' + submit_data + '&job=' + job,
						function (data) {
							if (data.success) {
								$('#save').prop('disabled', true);
								$('#save_options').find('button').prop('disabled', true);
								$('#saving_buttons').find('.cancel').addClass('disabled', true);
								$('#delay_modal').modal('show');
								setTimeout(function () {
									window.location = data.redirect;
								}, 1000)
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

		$('form').on('click', '[type="submit"]', function (e) {
			e.preventDefault();
			$('#tableList tbody tr td').find('.accountcode').find('option[disabled]').prop('disabled', false)
			var form_element = $(this).closest('form');
			var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
			var total_debit = parseFloat($('#total_debit').html().replace(/\,/g, '') || 0);
			var total_credit = parseFloat($('#total_credit').html().replace(/\,/g, '') || 0);
			form_element.closest('form').find('.form-group').find('input, textarea, select').trigger('blur_validate');
			if (total_debit == total_credit && (total_debit > 0 || total_credit > 0)) {
				$('#error_msg').html('');
				if (form_element.closest('form').find('.form-group.has-error').length == 0) {
					$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', form_element.closest('form').serialize() + '<?=$ajax_post?>' +
						'&finalized=finalized' + submit_data + '&job=' + job,
						function (data) {
							if (data.success) {
								$('#saving_buttons').find('button').prop('disabled', true);
								$('#saving_buttons').find('.cancel').prop('disabled', true);
								$('#delay_modal').modal('show');
								setTimeout(function () {
									window.location = data.redirect;
								}, 1000)
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
		$('#tableList tbody td.debit_column .form-control-static').each(function () {
			addTotal('#total_debit', $(this).html());
		});
		$('#tableList tbody td.credit_column .form-control-static').each(function () {
			addTotal('#total_credit', $(this).html());
		});
		<?php endif ?>

		$('#vendor_button').click(function () {
			$('#vendor_modal').modal('show');
		});

		/**FOR ADD NEW VENDOR TRANSACTION**/
		function addNewModal(type, val, row) {
			row = row.replace(/[a-z]/g, '');
			if (val == 'add') {
				if (type == 'Vendor') {
					$('#vendor_modal').modal();
					$('#Vendor').val('');
				}
			}
		}

		$('#referenceno').on('click', function () {
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

		$('#ordered_tableList').on('click', 'tr[data-id]', function () {
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

		$('#partner').on('change', function () {
			var emp = '';
			$('#referenceno').val(emp);
			$('#si_no').val(emp);
			$('#sr_amount').val(emp);
		});

		$('#ordered_tableList').on('click', 'a[data-id]', function (e) {
			e.stopPropagation();
			var sr_no = $(this).html();
			ajax.sr_no = sr_no;
			$('#return_details').modal('show');
			if (ajax_call3 != '') {
				ajax_call3.abort();
			}
			ajax_call3 = $.post('<?=MODULE_URL?>ajax/ajax_load_return_details', ajax, function (data) {
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
			ajax_call4 = $.post('<?=MODULE_URL?>ajax/ajax_load_ordered_list', ajax, function (data) {
				$('#ordered_tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			getList();
		});
		$('#pagination').on('click', 'a', function (e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		
		<?php if($ajax_task == 'ajax_create') : ?>
		var job = [];
		$('#job').on('click', function() {
			$.post('<?=MODULE_URL?>ajax/ajax_list_jobs', '&jobs_tagged=' + $('#jobs_tagged').val(), function(data) {
				if(data) {
					$('#jobModal').modal('show');
					$('#jobsTable tbody').html(data.table);
					$('#paginate').html(data.pagination);
				}
			});
		});
		<?php endif ?>

		<?php if($ajax_task == 'ajax_edit') : ?>
		var job = [];
		$('#job').on('click', function() {
			$.post('<?=MODULE_URL?>ajax/ajax_list_jobs', '&jobs_tagged=' + job, function(data) {
				if(data) {
					$('#jobModal').modal('show');
					$('#jobsTable tbody').html(data.table);
					$('#paginate').html(data.pagination);
				}
			});
		});
		<?php endif ?>

		<?php if($ajax_task == 'ajax_create') : ?>
		$('#paginate').on('click', 'a', function(e) {
			e.preventDefault();
			$('#jobsTable tbody tr td input[type="checkbox"]:checked').each(function() {
				var get = $(this).val();
				if($.inArray(get, job) == -1) {
					job.push(get);
				}
			});
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				page = $(this).attr('data-page');
				$.post('<?=MODULE_URL?>ajax/ajax_list_jobs', '&jobs_tagged=' + $('#jobs_tagged').val() + '&page=' + page, function(data) {
					if(data) {
						$('#jobsTable tbody').html(data.table);
						$('#paginate').html(data.pagination);
						$('#jobsTable tbody tr td input[type="checkbox"]').each(function() {
							if(jQuery.inArray($(this).val(), job) != -1) {
								$(this).closest('tr').iCheck('check');
							}
						});
					}
				});
			}
		});
		<?php endif ?>

		<?php if($ajax_task == 'ajax_edit') : ?>
		$('#paginate').on('click', 'a', function(e) {
			e.preventDefault();
			$('#jobsTable tbody tr td input[type="checkbox"]:checked').each(function() {
				var get = $(this).val();
				if($.inArray(get, job) == -1) {
					job.push(get);
				}
			});
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				page = $(this).attr('data-page');
				$.post('<?=MODULE_URL?>ajax/ajax_list_jobs', '&jobs_tagged=' + job + '&page=' + page, function(data) {
					if(data) {
						$('#jobsTable tbody').html(data.table);
						$('#paginate').html(data.pagination);
						$('#jobsTable tbody tr td input[type="checkbox"]').each(function() {
							if(jQuery.inArray($(this).val(), job) != -1) {
								$(this).closest('tr').iCheck('check');
							}
						});
					}
				});
			}
		});
		<?php endif ?>

		<?php if($ajax_task == 'ajax_create') : ?>
		$('#jobModal').on('shown.bs.modal', function () {
			$('#jobsTable tbody tr td input[type="checkbox"]').each(function () {
				if (jQuery.inArray($(this).val(), job) != -1) {
					$(this).closest('tr').iCheck('check');
				}
			});
		});
		<?php endif ?>

		$('#jobsTable').on('ifToggled', 'input[type="checkbox"]', function() {
			if(!$(this).is(':checked')) {
				job.splice( $.inArray($(this).val(),job) ,1 );
			}
		});
		
		<?php if($ajax_task == 'ajax_create') : ?>
		var ctr = 0;
		$('#confirmJob').on('click',function(e) {
			e.preventDefault();
			$('#jobsTable tbody tr td input[type="checkbox"]').each(function() {
				if($(this).is(':checked')) {
					var get = $(this).val();
					ctr++;
					if($.inArray(get, job) == -1) {
						job.push(get);
					}
					$('#job_text').html(job.length);
					$('#assetid').attr('disabled', 'disabled');
				} else {
					$('#job_text').html(job.length);
				}
			});
			if(ctr == 0) {
				$('#job_text').html('0');
			}
			$('#jobModal').modal('hide');
		});
		<?php endif ?>
		
		<?php if($ajax_task == 'ajax_edit') : ?>
		var ctr = 0;
		$('#confirmJob').on('click',function(e) {
			e.preventDefault();
			$('#jobsTable tbody tr td input[type="checkbox"]').each(function() {
				if($(this).is(':checked')) {
					ctr++;
					var get = $(this).val();
					if($.inArray(get, job) == -1) {
						job.push(get);
					}
					$('#job_text').html(job.length);
					$('#assetid').attr('disabled', 'disabled');
				} else {
					$('#job_text').html(job.length);
				}

				if($(this).is(':checked') == 'false') {
					$('#job_text').html('0');
				}
			});
			if(ctr == 0) {
				$('#job_text').html('0');
			}
			$('#jobModal').modal('hide');
		});
		<?php endif ?>

		$('#currencycode').on('change', function() {
			var currencycode = $(this).val();
			$('#tableList tbody tr td .form-group').find('.currency_symbol').html(currencycode);
			$.post('<?=MODULE_URL?>ajax/ajax_get_currency_val', { currencycode : currencycode }, function(data) {
				if(data) {
					$('#exchangerate').val(data.exchangerate);	
					$('.debit').each(function() {
						if($(this).val() != '0.00') {
							console.log($('#exchangerate').val());
							console.log($(this).val());
							$(this).closest('tr').find('.currencyamount').val(addComma(data.exchangerate * removeComma($(this).val())));
						} else {
							$(this).closest('tr').find('.currencyamount').val(addComma(data.exchangerate * removeComma($(this).closest('tr').find('.credit').val())));
						}
					});
					sumCurrencyAmount();
				}
			});
		});

		// $('#exchangerate').on('change', function() {
		// 	var exchangerate = $(this).val();
		// 	$('.currencyamount').each(function() {
		// 		currency = removeComma($(this).val());
		// 		var convertedcurrency = currency * exchangerate;
		// 		$('.currencyamount').val(addComma(convertedcurrency));
		// 		$('.currencyamount').html(addComma(convertedcurrency));	
		// 	});
		// 	sumCurrencyAmount();
		// });

		function sumDebit() {
			var total_debit = 0;
			var debit = 0;
			var curr_val = 0;
			$('.debit').each(function() {
				debit = removeComma($(this).val());
				total_debit += +debit;
				$('#total_debit').val(addComma(total_debit));
			});
		}

		function sumCredit() {
			var total_credit = 0;
			var credit = 0;
			var curr_val = 0;
			$('.credit').each(function() {
				credit = removeComma($(this).val());
				total_credit += +credit;
				$('#total_credit').val(addComma(total_credit));
			});
		}

		function sumCurrencyAmount() {
			var total_currency = 0;
			var currency = 0;
			$('.currencyamount').each(function() {
				//console.log($(this).val());
				currency = removeComma($(this).val());
				total_currency += +currency;
				console.log(total_currency);
				$('#total_currency').val(addComma(total_currency));
				$('#total_currency').html(addComma(total_currency));
			});
		}

		function getCurrencyAmountView() {
			var rate = +removeComma($('#exchangerate').html());
			$('[name="debit[]"]').each(function() {
				if ($(this).html() != '' && $(this).html() != '0.00') {
					var debits = removeComma($(this).html());
					var debit_currency = debits * rate;
					$(this).closest('tr').find('#currencyamount').html(addComma(debit_currency));
					console.log(debits);
				}
			});
			$('[name="credit[]"]').each(function() {
				if ($(this).html() != '' && $(this).html() != '0.00') {
					var credits = removeComma($(this).html());
					var credit_currency = credits * rate;
					$(this).closest('tr').find('#currencyamount').html(addComma(credit_currency));
				}
			});
			SumCurrencyonView();
		}

		function getCurrencyAmountEdit() {
			var rate = +removeComma($('#exchangerate').val());
			$('[name="debit[]"]').each(function() {
				if ($(this).val() != '' && $(this).val() != '0.00') {
					console.log(rate);
					var debits = removeComma($(this).val());
					var debit_currency = debits * rate;
					$(this).closest('tr').find('.currencyamount').val(addComma(debit_currency));
				}
			});
			$('[name="credit[]"]').each(function() {
				if ($(this).val() != '' && $(this).val() != '0.00') {
					var credits = removeComma($(this).val());
					var credit_currency = credits * rate;
					$(this).closest('tr').find('.currencyamount').val(addComma(credit_currency));
				}
			});
			SumCurrencyonEdit();
		}
		
		function SumCurrencyonView() {
			var total_currency = 0;
			$('[name="currencyamount[]"]').each(function () {
				var currency = +removeComma($(this).html());
				total_currency += currency;
				//console.log(total_currency);
				$(this).closest('tr').find('.currency_symbol').html('<?=$currency ?>');

			});
			$('#total_currency').html(addComma(total_currency));
		}

		function SumCurrencyonEdit() {
			var total_currency = 0;
			$('[name="currencyamount[]"]').each(function () {
				var currency = +removeComma($(this).html());
				total_currency += currency;
				//console.log(total_currency);
				$(this).closest('tr').find('.currency_symbol').html('<?=$currency ?>');

			});
			$('#total_currency').html(addComma(total_currency));
		}

		String.prototype.toNum = function(){
			return parseInt(this, 10);
		}
		
		<?php if($ajax_task == 'ajax_view') : ?>
			$(document).ready(function() {
				getCurrencyAmountView();
				sumCurrencyAmount();
			});
		<?php endif; ?>

		<?php if($ajax_task == 'ajax_edit') : ?>
			$(document).ready(function() {
				getCurrencyAmountEdit();
				sumCurrencyAmount();
				job = $('#jobs_tagged').val().split(',');	
			});
		<?php endif; ?>

		var row = '';
		$('#exchangerate').on('blur', function() {
			var total = 0;
			var rate = $(this).val();
			$('.currencyamount').each(function() {
				var debit = removeComma($(this).closest('tr').find('.debit').val());
				var credit = removeComma($(this).closest('tr').find('.credit').val());
				console.log('debit: ' + debit);
				console.log('credit: ' + credit);

				if(debit != '0.00') {
					row = $(this).closest('tr').find('.debit');
					total = debit * rate;
					console.log('debit on row: ' + debit);
					console.log('rate on row: ' + rate);
					console.log('total on row: ' + total);
				} 

				if(credit != '0.00') {
					row = $(this).closest('tr').find('.credit');
					total = credit * rate;
					console.log('credit on row: ' + credit);
					console.log('rate on row: ' + rate);
					console.log('total on row: ' + total);
				}
				console.log('-----');
				row.closest('tr').find('.currencyamount').val(addComma(total));
				sumCurrencyAmount();
			});
		});

		$(document).ready(function() {
			var currencycode = $('#currencycode').val();
			$.post('<?=MODULE_URL?>ajax/ajax_get_currency_val', { currencycode : currencycode }, function(data) {
				if(data) {
					$('#exchangerate').val(data.exchangerate);
				}
			});
		});
		
	</script>