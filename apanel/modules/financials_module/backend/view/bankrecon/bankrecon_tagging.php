	<section class="content">
		<div class="row">
			<div class="col-md-4 text-center col-md-offset-4" align="center">
				<div class="form-steps steps-2">
					<div class="step-line">
						<div class="step-progress" style="width: 75%"></div>
					</div>
					<div class="step prev">
						<a href="#" class="step-icon import-again">
							<i class="glyphicon glyphicon-import"></i>
						</a>
						<p>Import Bank Statement</p>
					</div>
					<div class="step active">
						<a class="step-icon">
							<i class="glyphicon glyphicon-tags"></i>
						</a>
						<p>Tag and Match</p> 
					</div>
				</div>
			</div>
		</div>
		<div class="box box-primary">
			<div class="box-header with-border text-center bg-aqua">
				<h4 class="m-none">Reconciliation Summary</h4>
			</div>
			<div class="box-body">
				<div id="header_values" class="form-horizontal">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Balance per Bank')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('balance_bank')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Balance per Book')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('balance_book')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Add: Deposit in Transit')
											->setSplit('col-md-7 col-md-offset-1 force-left', 'col-md-4 text-right')
											->setId('transit')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Add: Unrecorded Deposit')
											->setSplit('col-md-7 col-md-offset-1 force-left', 'col-md-4 text-right')
											->setId('unrecorded_deposit')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Total')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('total_bank')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Total')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('total_book')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Less: Outstanding Cheques')
											->setSplit('col-md-7 col-md-offset-1 force-left', 'col-md-4 text-right')
											->setId('outstanding_check')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Less: Unrecorded Withdrawal')
											->setSplit('col-md-7 col-md-offset-1 force-left', 'col-md-4 text-right')
											->setId('unrecorded_withdrawal')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Adjusted Bank Balance')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('adjusted_bank')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
								<div class="col-md-6">
									<?php
										echo $ui->formField('text')
											->setLabel('Adjusted Book Balance')
											->setSplit('col-md-8 force-left', 'col-md-4 text-right')
											->setId('adjusted_book')
											->setValue('0.00')
											->addHidden()
											->draw(false);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer with-border text-center">
				<button type="button" id="bankrecon_finalize" class="btn btn-primary" disabled>Finalize Bank Reconciliation</button>
			</div>
		</div>

		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<li class="active"><a href="#bank_list" data-toggle="tab">Bank Statement List</a></li>
				<li><a href="#system_list" data-toggle="tab">Oojeema Transaction</a></li>
				<li><a href="#confirmed_list" data-toggle="tab">Confirmed Matched Items</a></li>
			</ul>
			<div class="tab-content no-padding">
				<div id="bank_list" class="tab-pane active table-responsive">
					<table id="bankTable" class="table table-hover table-sidepad">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Check Date', array('class' => 'col-md-4'))
									->addHeader('Account Nature', array('class' => 'col-md-4'))
									->addHeader('Check Number', array('class' => 'col-md-2'))
									->addHeader('Amount', array('class' => 'col-md-2 text-right'))
									->addHeader('', array('style' => 'width: 15px'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
				<div id="system_list" class="tab-pane table-responsive">
					<table id="systemTable" class="table table-hover table-sidepad">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Document Date', array('class' => 'col-md-3'))
									->addHeader('Voucher Number', array('class' => 'col-md-3'))
									->addHeader('Account Nature', array('class' => 'col-md-3'))
									->addHeader('Check Number', array('class' => 'col-md-2'))
									->addHeader('Amount', array('class' => 'col-md-2 text-right'))
									->addHeader('', array('style' => 'width: 15px'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
				<div id="confirmed_list" class="tab-pane table-responsive">
					<table id="confirmedTable" class="table table-hover table-sidepad">
						<thead>
							<tr>
								<th colspan="4" class="text-center bg-teal" style="color: #000 !important">Bank Statement</th>
								<th colspan="4" class="text-center bg-gray">Oojeema Transaction</th>
							</tr>
						</thead>
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Transaction Date', array('class' => 'col-md-2 bg-teal-active'))
									->addHeader('Check Number', array('class' => 'col-md-2 bg-teal-active'))
									->addHeader('Amount', array('class' => 'col-md-2 bg-teal-active text-right'))
									->addHeader('', array('class' => 'bg-teal-active'))
									->addHeader('', array('class' => 'bg-gray-active'))
									->addHeader('Transaction Date', array('class' => 'col-md-2 bg-gray-active'))
									->addHeader('Check Number', array('class' => 'col-md-2 bg-gray-active'))
									->addHeader('Amount', array('class' => 'col-md-2 bg-gray-active text-right'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div id="pagination" data-ajax="ajax_bank"></div>

		<div class="box box-primary">
			<div class="box-header with-border text-center bg-aqua">
				<h4 class="m-none">Matched Items</h4>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="matchedItems" class="table table-hover table-sidepad">
					<thead>
						<tr>
							<th colspan="4" class="text-center bg-teal" style="color: #000 !important">Bank Statement</th>
							<th colspan="4" class="text-center bg-gray">Oojeema Transaction</th>
						</tr>
					</thead>
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Transaction Date', array('class' => 'col-md-2 bg-teal-active'))
								->addHeader('Check Number', array('class' => 'col-md-2 bg-teal-active'))
								->addHeader('Amount', array('class' => 'col-md-2 bg-teal-active text-right'))
								->addHeader('', array('class' => 'bg-teal-active'))
								->addHeader('', array('class' => 'bg-gray-active'))
								->addHeader('Transaction Date', array('class' => 'col-md-2 bg-gray-active'))
								->addHeader('Check Number', array('class' => 'col-md-2 bg-gray-active'))
								->addHeader('Amount', array('class' => 'col-md-2 bg-gray-active text-right'))
								->draw();
					?>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination_bottom" data-ajax="ajax_matched"></div>
	</section>
	<div class="modal fade" id="bank_match_modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					Find Match
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body table-reponsive no-padding">
					<table id="matchingTable" class="table table-hover table-sidepad mb-none">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Document Date', array('class' => 'col-md-3'))
									->addHeader('Voucher Number', array('class' => 'col-md-4'))
									->addHeader('Check Number', array('class' => 'col-md-3'))
									->addHeader('Amount', array('class' => 'col-md-2 text-right'))
									->addHeader('', array('style' => 'width: 15px'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
				<div class="modal-footer force-left">
					<span id="tag_button"></span>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="system_match_modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					Find Match
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body table-reponsive no-padding">
					<table id="matchingTable" class="table table-hover table-sidepad mb-none">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Document Date', array('class' => 'col-md-3'))
									->addHeader('Voucher Number', array('class' => 'col-md-4'))
									->addHeader('Check Number', array('class' => 'col-md-3'))
									->addHeader('Amount', array('class' => 'col-md-2 text-right'))
									->addHeader('', array('style' => 'width: 15px'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
				<div class="modal-footer force-left">
					<span id="tag_button"></span>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="tagged_modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					Remove Tag
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body table-reponsive no-padding">
					<table id="taggedTable" class="table table-hover table-sidepad mb-none">
						<?php
							echo $ui->loadElement('table')
									->setHeaderClass('info')
									->addHeader('Transaction Date', array('class' => 'col-md-4'))
									->addHeader('Check Number', array('class' => 'col-md-3'))
									->addHeader('Voucherno', array('class' => 'col-md-2'))
									->addHeader('Amount', array('class' => 'col-md-3 text-right'))
									->addHeader('', array('style' => 'width: 15px'))
									->draw();
						?>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script>
		var ajax_bank = {};
		var ajax_system = {};
		var ajax_matched = {};
		var ajax_confirmed = {};
		var confirm_id = '';
		ajax_bank.recon_id = '<?php echo $recon_id ?>';
		ajax_system.recon_id = '<?php echo $recon_id ?>';
		ajax_matched.recon_id = '<?php echo $recon_id ?>';
		ajax_confirmed.recon_id = '<?php echo $recon_id ?>';
		var ajax_call = {};
		ajax_call.bank = '';
		ajax_call.system = '';
		ajax_call.matched = '';
		ajax_call.confirmed = '';
		ajax_call.headers = '';
		ajax_call.modal = '';
		function getBankList() {
			if (ajax_call.bank != '') {
				ajax_call.bank.abort();
			}
			ajax_call.bank = $.post('<?php echo MODULE_URL . 'ajax/ajax_get_bank' ?>', ajax_bank, function(data) {
				$('#bankTable tbody').html(data.table);
				$('#pagination').attr('data-ajax', 'ajax_bank');
				$('#pagination').html(data.pagination);
				if (ajax_bank.page > data.page_limit && data.page_limit > 0) {
					ajax_bank.page = data.page_limit;
					getList();
				}
			});
		}

		function getSystemList() {
			if (ajax_call.system != '') {
				ajax_call.system.abort();
			}
			ajax_call.system = $.post('<?php echo MODULE_URL . 'ajax/ajax_get_system' ?>', ajax_system, function(data) {
				$('#systemTable tbody').html(data.table);
				$('#pagination').attr('data-ajax', 'ajax_system');
				$('#pagination').html(data.pagination);
				if (ajax_system.page > data.page_limit && data.page_limit > 0) {
					ajax_system.page = data.page_limit;
					getList();
				}
			});
		}

		function getMatchedList() {
			if (ajax_call.matched != '') {
				ajax_call.matched.abort();
			}
			ajax_call.matched = $.post('<?php echo MODULE_URL . 'ajax/ajax_get_matched' ?>', ajax_matched, function(data) {
				$('#matchedItems tbody').html(data.table);
				$('#pagination_bottom').attr('data-ajax', 'ajax_matched');
				$('#pagination_bottom').html(data.pagination);
				if (ajax_matched.page > data.page_limit && data.page_limit > 0) {
					ajax_matched.page = data.page_limit;
					getList();
				}
			});
		}

		function getConfirmedList() {
			if (ajax_call.confirmed != '') {
				ajax_call.confirmed.abort();
			}
			ajax_call.confirmed = $.post('<?php echo MODULE_URL . 'ajax/ajax_get_confirmed' ?>', ajax_confirmed, function(data) {
				$('#confirmedTable tbody').html(data.table);
				$('#pagination').attr('data-ajax', 'ajax_confirmed');
				$('#pagination ').html(data.pagination);
				if (ajax_confirmed.page > data.page_limit && data.page_limit > 0) {
					ajax_confirmed.page = data.page_limit;
					getList();
				}
			});
		}

		function getHeaders() {
			if (ajax_call.headers != '') {
				ajax_call.headers.abort();
			}
			$('#bankrecon_finalize').attr('disabled', true);
			ajax_call.header = $.post('<?php echo MODULE_URL . 'ajax/ajax_get_headers' ?>', { recon_id: '<?php echo $recon_id ?>' }, function(data) {
				var balance_bank = parseFloat(data.balance_bank.toString()).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				var balance_book = parseFloat(data.balance_book.toString()).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				var deposit_transit = '<a href="#" class="open_tagged" data-type="deposit_in_transit">' + data.deposit_transit.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</a>';
				var unrecorded_deposit = '<a href="#" class="open_tagged" data-type="unrecorded_deposit">' + data.unrecorded_deposit.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</a>';
				var outstanding_cheques = '<a href="#" class="open_tagged" data-type="outstanding_cheque">' + data.outstanding_cheques.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</a>';
				var unrecorded_withdrawal = '<a href="#" class="open_tagged" data-type="unrecorded_withdrawal">' + data.unrecorded_withdrawal.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</a>';

				var total_bank = (parseFloat(data.balance_bank) + parseFloat(data.deposit_transit)).toFixed(2);
				var total_book = (parseFloat(data.balance_book) + parseFloat(data.unrecorded_deposit)).toFixed(2);
				var adjusted_bank = (parseFloat(total_bank) - parseFloat(data.outstanding_cheques)).toFixed(2);
				var adjusted_book = (parseFloat(total_book) - parseFloat(data.unrecorded_withdrawal)).toFixed(2);
				
				if (data.deposit_transit == '0.00') {
					deposit_transit = data.deposit_transit;
				}
				if (data.unrecorded_deposit == '0.00') {
					unrecorded_deposit = data.unrecorded_deposit;
				}
				if (data.outstanding_cheques == '0.00') {
					outstanding_cheques = data.outstanding_cheques;
				}
				if (data.unrecorded_withdrawal == '0.00') {
					unrecorded_withdrawal = data.unrecorded_withdrawal;
				}

				$('#balance_bank').closest('.form-group').find('.form-control-static').html(balance_bank);
				$('#balance_book').closest('.form-group').find('.form-control-static').html(balance_book);
				$('#transit').closest('.form-group').find('.form-control-static').html(deposit_transit);
				$('#unrecorded_deposit').closest('.form-group').find('.form-control-static').html(unrecorded_deposit);
				$('#total_bank').closest('.form-group').find('.form-control-static').html(total_bank.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
				$('#total_book').closest('.form-group').find('.form-control-static').html(total_book.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
				$('#outstanding_check').closest('.form-group').find('.form-control-static').html(outstanding_cheques);
				$('#unrecorded_withdrawal').closest('.form-group').find('.form-control-static').html(unrecorded_withdrawal);
				$('#adjusted_bank').closest('.form-group').find('.form-control-static').html(adjusted_bank.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
				$('#adjusted_book').closest('.form-group').find('.form-control-static').html(adjusted_book.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
				if (data.finalize && adjusted_bank == adjusted_book) {
					$('#bankrecon_finalize').attr('disabled', false);
				} else {
					$('#bankrecon_finalize').attr('disabled', true);
				}
			});
		}

		function getListing(filter) {
			getHeaders();
			if (filter == '#bank_list') {
				getBankList();
			} else if (filter == '#system_list') {
				getSystemList();
			} else if (filter == '#confirmed_list') {
				getConfirmedList();
			} else if (filter ==  '#match_list') {
				getMatchedList();
			}
		}
		getBankList();
		getSystemList();
		getMatchedList();
		getHeaders();
		$('#pagination, #pagination_bottom').on('click', 'a', function(e) {
			e.preventDefault();
			var name = $(this).closest('[id*="pagination"]').attr('data-ajax');
			eval(name).page = $(this).attr('data-page');
			if (name == 'ajax_bank') {
				getBankList();
			} else if (name == 'ajax_system') {
				getSystemList();
			} else if (name == 'ajax_confirmed') {
				getConfirmedList();
			} else if (name ==  'ajax_matched') {
				getMatchedList();
			}
		});
		$('#bankrecon_finalize').click(function() {
			showConfirmationLink('finalizeBankRecon()', '', 'Are you sure you want to finalize?');
		});
		function finalizeBankRecon() {
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_finalized', { recon_id: '<?php echo $recon_id ?>' }, function(data) {
				if (data.success) {
					window.location = '<?php echo MODULE_URL ?>';
				}
			});
		}
		$('#header_values').on('click', '.open_tagged', function(e) {
			e.preventDefault();
			if (ajax_call.modal != '') {
				ajax_call.modal.abort();
			}
			var type = $(this).attr('data-type');
			ajax_call.modal = $.post('<?php echo MODULE_URL ?>ajax/ajax_get_tagged', { type: type, recon_id: '<?php echo $recon_id ?>' }, function(data) {
				$('#taggedTable tbody').html(data.table);
				$('#tagged_modal').modal('show');
			});
		});
		$('#bankTable tbody').on('click', '.tag-match', function() {
			var recdet_id = $(this).attr('data-id');
			var nature = $(this).attr('data-nature');
			var button = '';
			if (nature == 'Income') {
				button = '<button type="button" class="btn btn-primary btn-sm bank_income" data-id="' + recdet_id + '">Tag as Unrecorded Deposit</button>';
			} else if (nature == 'Expense') {
				button = '<button type="button" class="btn btn-primary btn-sm bank_expense" data-id="' + recdet_id + '">Tag as Unrecorded Withdrawal</button>';
			}
			$('#bank_match_modal #tag_button').html('');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_get_formatching', { recdet_id: recdet_id }, function(data) {
				$('#bank_match_modal #matchingTable tbody').html(data.table);
				$('#bank_match_modal').modal('show');
				if (data.transaction_type == 'Current') {
					$('#bank_match_modal #tag_button').html(button);
				}
			});
		});
		$('#systemTable tbody').on('click', '.tag-match', function() {
			var voucherno = $(this).attr('data-id');
			var nature = $(this).attr('data-nature');
			var button = '';
			if (nature == 'Income') {
				button = '<button type="button" class="btn btn-primary btn-sm system_income" data-id="' + voucherno + '">Tag as Deposit in Transit</button>';
			} else if (nature == 'Expense') {
				button = '<button type="button" class="btn btn-primary btn-sm system_expense" data-id="' + voucherno + '">Tag as Outstanding Check</button>';
			}
			$('#system_match_modal #tag_button').html('');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_get_formatching2', { voucherno: voucherno, recon_id: '<?php echo $recon_id ?>' }, function(data) {
				$('#system_match_modal #matchingTable tbody').html(data.table);
				$('#system_match_modal').modal('show');
				if (data.transaction_type == 'Current') {
					$('#system_match_modal #tag_button').html(button);
				}
			});
		});
		$('#bank_match_modal #tag_button').on('click', '.bank_income', function() {
			$('#bank_match_modal').modal('hide');
			var recdet_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_bankdeposit', { recdet_id: recdet_id }, function() {
				getBankList();
				getHeaders();
			});
		});
		$('#bank_match_modal #tag_button').on('click', '.bank_expense', function() {
			$('#bank_match_modal').modal('hide');
			var recdet_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_bankwithdrawal', { recdet_id: recdet_id }, function() {
				getBankList();
				getHeaders();
			});
		});
		$('#system_match_modal #tag_button').on('click', '.system_income', function() {
			$('#system_match_modal').modal('hide');
			var voucherno = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_systemdeposit', { voucherno: voucherno, recon_id: '<?php echo $recon_id ?>' }, function() {
				getSystemList();
				getHeaders();
			});
		});
		$('#system_match_modal #tag_button').on('click', '.system_expense', function() {
			$('#system_match_modal').modal('hide');
			var voucherno = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_systemwithdrawal', { voucherno: voucherno, recon_id: '<?php echo $recon_id ?>' }, function() {
				getSystemList();
				getHeaders();
			});
		});
		$('#bank_match_modal #matchingTable tbody').on('click', '.tag-match', function() {
			$('#bank_match_modal').modal('hide');
			var voucherno = $(this).attr('data-id');
			var recdet_id = $(this).attr('data-match');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_match', { voucherno: voucherno, recdet_id: recdet_id }, function() {
				getBankList();
				getMatchedList();
				getHeaders();
			});
		});
		$('#system_match_modal #matchingTable tbody').on('click', '.tag-match', function() {
			$('#system_match_modal').modal('hide');
			var recdet_id = $(this).attr('data-id');
			var voucherno = $(this).attr('data-match');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_match', { voucherno: voucherno, recdet_id: recdet_id }, function() {
				getSystemList();
				getMatchedList();
				getHeaders();
			});
		});
		$('#matchedItems tbody').on('click', '.tag-match', function() {
			var tagged_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_set_confirm', { tagged_id: tagged_id }, function() {
				getConfirmedList();
				getMatchedList();
				getHeaders();
			});
		});
		$('#matchedItems tbody').on('click', '.remove-match', function() {
			var tagged_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_remove_match', { tagged_id: tagged_id }, function() {
				getBankList();
				getSystemList();
				getMatchedList();
				getHeaders();
			});
		});
		$('#confirmedTable tbody').on('click', '.remove-match', function() {
			var tagged_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_remove_match', { tagged_id: tagged_id }, function() {
				getConfirmedList();
				getMatchedList();
				getHeaders();
			});
		});
		$('#taggedTable tbody').on('click', '.remove-match', function() {
			var tagged_id = $(this).attr('data-id');
			$.post('<?php echo MODULE_URL ?>ajax/ajax_remove_match', { tagged_id: tagged_id }, function() {
				getBankList();
				getSystemList();
				getHeaders();
				$('#tagged_modal').modal('hide');
			});
		});
		$('#filter_tabs').on('click', 'li a', function(data) {
			var filter = $(this).attr('href');
			getListing(filter);
		});
		$('.import-again').click(function() {
			showConfirmationLink('newBankRecon()', '', 'Are you sure you want to cancel current Bank Reconciliation?');
		});
		function newBankRecon() {
			$.post('<?php echo MODULE_URL ?>ajax/ajax_delete_current', { recon_id: '<?php echo $recon_id ?>' }, function() {
				window.location = '<?php echo MODULE_URL ?>';
			});
		}
	</script>



