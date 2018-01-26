	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
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
								->setPlaceholder('Filter Customer')
								->setSplit('col-md-3', 'col-md-8')
								->setName('requestor')
								->setId('requestor')
								->setList($requestor_list)
								->draw($show_input);
						?>
					</div>
					
					<div class = "col-md-4"></div>

					<div class="col-md-1">
						<a href="" id="export_csv" download="Statement of Account.csv" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-export"></span> Export</a>
					</div>
					
				</div>
				<div class="alert alert-info alert-dismissible" id="reminder" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<strong>Please select a Customer first.</strong>
				</div>
			</div>
			<div id="soa" class="box-body table-responsive no-padding" style="display: none">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class = "info">
							<div class="pull-left" style = "margin-left : 10px;">
							<p class="form-control-static" style= "font-weight: bold;" >Company: <span id="c_name"></span></p>
							<p class="form-control-static" style= "font-weight: bold;" >Company Address: <span id="c_add"></span></p></div>
							<!--<th class = "col-md-1 text-center">Invoice Date</th>
							<th class = "col-md-1 text-center">Invoice Number</th>
							<th class = "col-md-1 text-center">Document Type</th>
							<th class = "col-md-1 text-center">Ref No.</th>
							<th class = "col-md-1 text-center">Description</th>
							<th class = "col-md-1 text-center">Amount</th>
							<th class = "col-md-1 text-center">Balance</th>-->
							<?php
								echo $ui->loadElement('table')
										->setHeaderClass('info')
										->addHeader('Invoice Date',array('class'=>'col-md-1'),'sort','invoicedate')
										->addHeader('Invoice No.', array('class'=>'col-md-1'),'sort','invoiceno')
										->addHeader('Document Type',array('class'=>'col-md-1'),'sort','transtype')
										->addHeader('Ref No.',array('class'=>'col-md-1'),'sort','referenceno')
										->addHeader('Description',array('class'=>'col-md-1'),'sort','particulars')
										->addHeader('Amount',array('class'=>'col-md-1'),'sort','invoiceamount')
										->addHeader('Balance',array('class'=>'col-md-1'),'sort','')
										->draw();
							?>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
		<div id="pagination" style="display: none"></div>
	</section>
<script>

var ajax = {};
var ajax_call = {};
ajax.requestor 	= '';

function showList(pg){
	ajax.daterangefilter = $("#daterangefilter").val();
	ajax.custfilter      = $("#requestor").val();
	$.post('<?=BASE_URL?>report/statement_account/ajax/soa_listing',ajax, function(data) {
		$('#tableList tbody').html(data.table);
		$('#pagination').html(data.pagination);
		$('#c_name').html(data.c_name);
		$('#c_add').html(data.c_add);
		$("#export_csv").attr('href', 'data:text/csv;filename=testing.csv;charset=utf-8,' + encodeURIComponent(data.csv));
		$('#reminder').hide();
		$('#soa').show();
		$('#pagination').show();
		console.log('sdflksdfkjlsdjlkfsdflkj');
	});
};
tableSort('#tableList', function(value, getlist) {
	ajax.sort = value;
	ajax.page = 1;
	if (getlist) {
		showList();
	}
}, ajax);
$( "#search" ).keyup(function() {
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
})

$('#daterangefilter').on('change', function() {
	ajax.daterangefilter = $(this).val();
	ajax.page = 1;
	showList();
});

$('#requestor').on('change', function() {
	ajax.cusfilter = $(this).val();
	showList();
});

$('#search').on('change', function() {
	ajax.search = $(this).val();
	showList();
});

</script>