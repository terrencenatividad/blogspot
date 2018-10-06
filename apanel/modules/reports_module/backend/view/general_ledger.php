<section class="content">
	<div class="box box-primary">
		<form method = "post">
			<div class="box-header">
				<div class="row">
					<div class = "col-md-11"></div>
					<div class = "col-md-1">
						<div class="form-group">
							<a href="" id="export_csv" download="General Ledger.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export</a>
						</div>
					</div>
				</div>
				<div class = "row">
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
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php
									echo $ui->formField('dropdown')
										->setPlaceholder('Select Account')
										->setName('accountcodefilter')
										->setId('accountcodefilter')
										->setList($accountcodes)
										->setNone('All')
										->draw();
								?>
							</div>
							<!-- Filter Button Here / Display Items dopdown + CSV-->
						</div>
					</div>
					<div class="col-md-4">
						<div class="col-md-12">
							<div class="form-group">
								<div class="input-group" >
									<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
									<div class="input-group-btn" style = "height: 34px;">
										<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		
		<table id="tableList" class="table table-striped table-hover  report_table text-right">
			<thead>
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('Account Code',array('class'=>'col-md-1'),'sort','ca.segment5')
							->addHeader('Account Name', array('class'=>'col-md-1'),'sort','ca.accountname')
							->addHeader('',array(
											'class'=>'col-md-9',
											'colspan'=>'6'))
							->draw();
				?>
			</thead>
			<tbody>
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('danger')
							->addHeader('Transaction Date',array('class'=>'col-md-1'),'sort','bal.transactiondate')
							->addHeader('Voucher No.', array('class'=>'col-md-1'),'sort','bal.voucherno')
							->addHeader('Partner',array('class'=>'col-md-1'),'sort','p.partnername')
							->addHeader('Description',array('class'=>'col-md-1'))
							->addHeader('Status',array('class'=>'col-md-1'))
							->addHeader('Total Debit',array('class'=>'col-md-1'),'sort','SUM(bal.debit)')
							->addHeader('Total Credit',array('class'=>'col-md-1'),'sort','SUM(bal.credit)')
							->draw();
				?>
			</tbody>
			<tbody id = "list_container" style = "border-top:0;">
			</tbody>
		</table>
	</div>
	<div id="pagination"></div>
</section>
<script>
	var ajax = {}
	var ajax_call = {};
	$('#search').on('input', function () {
		ajax.search = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#accountcodefilter').on('change', function() {
		ajax.accountcodefilter = $(this).val();
		ajax_call.abort();
		getList();
	});
	$('#customer').on('change', function() {
		ajax.customer = $(this).val();
		ajax_call.abort();
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
		ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
			$('#tableList #list_container').html(data.table);
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

	tableSort('#tableList', function(value) {
		ajax.sort = value;
		ajax.page = 1;
		getList();
	});
</script>