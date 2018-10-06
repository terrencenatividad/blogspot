	<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-3">
						<?php
							echo $ui->formField('text')
									->setLabel('(Filter Result) As of')
									->setClass('datepicker-input')
									->setId('date_filter')
									->setAttribute(array('readonly' => ''))
									->setAddon('calendar')
									->setValidation('required')
									->setValue($date)
									->draw();
						?>
					</div>
					<div class="col-md-9">
						<div class="form-group text-right">
							<?php
								echo $ui->setElement('button')
										->setId('export')
										->setPlaceholder('<i class="glyphicon glyphicon-export"></i> Export')
										->draw();
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<table id="tableList" class="table table-hover table-sidepad report_table">
					<thead>
						<tr class="info">
							<th>As of (<span id="date_filter_asof"></span>)</th>
							<th class="col-xs-3"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<p class="form-control-static">Bank Balance</p>
							</td>
							<td>
								<?php
									echo $ui->setElement('text')
											->setName('bank_balance')
											->setID('bank_balance')
											->setClass('text-right')
											->setValidation('decimal')
											->setValue('0.00')
											->draw();
								?>
							</td>
						</tr>
						<tr data-id="released">
							<td>
								<p class="form-control-static">Less: Outstanding Checks</p>
							</td>
							<td class="text-right">
								<p id="outstanding_checks" class="form-padding mb-none">0.00</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="form-control-static">Add: For Deposit</p>
							</td>
							<td>
								<?php
									echo $ui->setElement('text')
											->setName('for_deposit')
											->setID('for_deposit')
											->setClass('text-right')
											->setValidation('decimal')
											->setValue('0.00')
											->draw();
								?>
							</td>
						</tr>
						<tr class="warning bold">
							<td>
								<p class="form-control-static">Available Cash</p>
							</td>
							<td class="text-right">
								<p id="available_cash" class="form-padding mb-none">0.00</p>
							</td>
						</tr>
						<tr data-id="uncleared">
							<td>
								<p class="form-control-static">Less: Check for release</p>
							</td>
							<td class="text-right">
								<p id="check_for_release" class="form-padding mb-none">0.00</p>
							</td>
						</tr>
						<tr data-id="postdated">
							<td>
								<p class="form-control-static">Post Dated Checks</p>
							</td>
							<td class="text-right">
								<p id="post_dated_checks" class="form-padding mb-none">0.00</p>
							</td>
						</tr>
						<tr class="warning bold">
							<td>
								<p class="form-control-static">Cash Balance</p>
							</td>
							<td class="text-right">
								<p id="cash_balance" class="form-padding mb-none">0.00</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div id="cash_position_details" style="display: none;">
			<div class="box box-primary">
				<table id="tableDetails" class="table table-hover report_table">
					<thead>
						<tr class="info">
							<th>Voucher Number</th>
							<th>Check Date</th>
							<th>Check Number</th>
							<th>Payee</th>
							<th class="text-right">Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Test</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Test</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Test</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>Test</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="pagination"></div>
		</div>
	</section>
	<script>
		var ajax = {}
		var ajax_call = '';
		var ajax_call2 = '';
		function getCashPosition() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_get_data', ajax, function(data) {
				for (var key in data) {
					if (data.hasOwnProperty(key)) {
						$('#' + key).html(data[key]);
					}
				}
				recomputeCashPosition();
			});
		}
		function recomputeCashPosition() {
			var bank_balance		= parseFloat($('#bank_balance').val().replace(/\,/g,'')) || 0;
			var for_deposit			= parseFloat($('#for_deposit').val().replace(/\,/g,'')) || 0;
			var outstanding_checks	= parseFloat($('#outstanding_checks').html().replace(/\,/g,'')) || 0;
			var check_for_release	= parseFloat($('#check_for_release').html().replace(/\,/g,'')) || 0;
			var post_dated_checks	= parseFloat($('#post_dated_checks').html().replace(/\,/g,'')) || 0;

			$('#outstanding_checks, #check_for_release, #post_dated_checks').each(function() {
				if ((parseFloat($(this).html().replace(/\,/g,'')) || 0) == 0) {
					$(this).closest('tr').removeClass('clickable');
				} else {
					$(this).closest('tr').addClass('clickable');
				}
			});

			var available_cash		= bank_balance + for_deposit - outstanding_checks;
			var cash_balance		= available_cash - check_for_release - post_dated_checks;
			$('#available_cash').html(available_cash.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
			$('#cash_balance').html(cash_balance.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
		}
		function displayCashPositionDetails() {
			if (ajax_call2 != '') {
				ajax_call2.abort();
			}
			ajax_call2 = $.post('<?=MODULE_URL?>ajax/ajax_get_details', ajax, function(data) {
				$('#tableDetails tbody').html(data.table);
				$('#pagination').html(data.pagination);
				$('#cash_position_details').show();
				recomputeCashPosition();
			});
		}
		$('#bank_balance, #for_deposit').on('blur', function() {
			recomputeCashPosition();
		});
		$('#date_filter').on('change', function() {
			ajax.datefilter = $(this).val();
			$('#date_filter_asof').html($(this).val());
			getCashPosition();
			$('#cash_position_details').hide();
		});
		$('#tableList').on('click', 'tr.clickable', function() {
			ajax.stat = $(this).attr('data-id');
			displayCashPositionDetails();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				displayCashPositionDetails();
			}
		});
		$('#for_deposit').on('input change', function() {
			ajax.for_deposit = $(this).val();
		});
		$('#bank_balance').on('input change', function() {
			ajax.bank_balance = $(this).val();
		});
		$("#export").click(function() {
			window.location = '<?=MODULE_URL?>view_export?' + $.param(ajax);
		});
	</script>