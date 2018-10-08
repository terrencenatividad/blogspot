<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<form autocomplete="off">
				<div class = "row">
					<div class="col-md-3 col-sm-5 col-xs-9">
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
					<div class="visible-xs">&nbsp;<br/></div>
					<div class = "col-md-4">
						<?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Select Customer')
								->setSplit('col-md-3', 'col-md-8')
								->setName('customer')
								->setId('customer')
								->setList($requestor_list)
								->draw($show_input);
						?>
					</div>
					
					<div class = "col-md-4"></div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="Statement of Account.csv" class="btn btn-primary pull-right hidden"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
					
				</div>
			</form>
			<em id="tip"><p class="text-info ">Here's a tip, select a customer to load a statement of account</p></em>
		</div>
		<div class="box-body">
			<div id="soa" class="table-responsive no-padding" style="display: none;">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<strong><h3 id="c_name"></h3></strong>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<em><p id="c_add"></p></em>
				</div>
				<table id="tableList" class="table table-hover table-sidepad table-bordered">
					<thead>
						<tr class = "info">
							<?php
								echo $ui->loadElement('table')
										->setHeaderClass('info')
										->addHeader('Transaction Date',array('class'=>'col-md-2'))
										->addHeader('Invoice No.', array('class'=>'col-md-1'))
										->addHeader('Type',array('class'=>'col-md-1'))
										->addHeader('Ref No.',array('class'=>'col-md-1'))
										->addHeader('Description',array('class'=>'col-md-5'))
										->addHeader('Amount',array('class'=>'col-md-1'))
										->addHeader('Balance',array('class'=>'col-md-1'))
										->draw();
							?>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div id="pagination" style="display: none"></div>
</section>

<script>
	var ajax = {};
	var ajax_call = {};
	ajax.custfilter 	= '';
	ajax.limit 	= 100;

	function showList(pg){
		ajax.daterangefilter = $('#daterangefilter').val();
		$.post('<?=MODULE_URL?>ajax/soa_listing',ajax, function(data) {
			$('#tableList tbody').html(data.table);
			$('#pagination').html(data.pagination);
			$('#c_name').html(data.c_name);
			$('#c_add').html(data.c_add);
			$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
			$('#tip').hide();
			$('#soa').show();
			$("#export_csv").removeClass('hidden');
			$('#pagination').show();
		});
	};
	tableSort('#tableList', function(value, getlist) {
		ajax.sort = value;
		ajax.page = 1;
		if (getlist) {
			showList();
		}
	}, ajax);
	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			showList();
		}
	});
	$('#daterangefilter').on('change', function() {
		ajax.daterangefilter = $(this).val();
		ajax.page = 1;
		showList();
	});
	$('#customer').on('change', function() {
		ajax.custfilter = $(this).val();
		showList();
	});
</script>