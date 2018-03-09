<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">

			<form method = "post" id = "tag_customer_form" class="form-horizontal">

				<div class = "col-md-12">&nbsp;</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Sales Person Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('partnercode')
									->setId('partnercode')
									->setAttribute(array("readonly" => "readonly"))
									->setValue($partnercode)
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Sales Person Name')
									->setSplit('col-md-4', 'col-md-8')
									->setName('salespersonname')
									->setId('salespersonname')
									->setAttribute(array("readonly" => "readonly"))
									->setValue($salespersonname)
									->draw($show_input);
						?>
					
					</div>
				</div>

				<hr/>
				<div class="col-md-4">
					<button type="button" id="open_customer_modal" class="btn btn-info btn-sm" data-toggle="modal"><i class="glyphicon glyphicon-new-window"></i> Tag Customer</button>
				</div>

				<div class="col-md-4 pull-right">
					<div class="input-group input-group-sm">
						<input id="tag_search" name="table_search" class="form-control pull-right" placeholder="Search" type="text">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
						</div>
					</div>
					<br>
				</div>

				<!-- Table Here -->
				
				<table id = "tag_customer_table" class="table table-hover">
					<thead>
						<tr class = "info">
							<th class = "col-md-1 hide_in_view" style="text-align:center;">
								<input type = "checkbox" name = "selectall" id = "selectall" />
							</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
							<th class = "col-md-3 text-center">Customer Code</th>
							<th class = "col-md-2 text-center">Customer Name</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
						</tr>
					</thead>
				
					<tbody id = "list_container"></tbody>

				</table>
				<input id="retrieved_tag" name="retrieved_tag" class="hidden">
				<div id="pagination"></div>

				<hr/>

				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $ui->drawSubmit($show_input); ?>
						<a href="<?=BASE_URL?>maintenance/sales_person" class="btn btn-default">Delete</a>
					</div>	
				</div>

			</form>

		</div>

	</div>

</div>

<!-- Import Customers Modal -->
<div class="import-modal" id="import-tagcust-modal" tabindex="-1" data-backdrop="static">>
	<div class="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="POST" id="importForm" ENCTYPE="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span></button>
						<h4 class="modal-title">Import Customers</h4>
					</div>
					<div class="modal-body">
						<label>Step 1. Download the sample template <a href="<?=MODULE_URL?>get_import_customers" download="Sales Person [<?php echo $partnercode ." - ". $salespersonname ?>] - Customers.csv">here</a></label>
						<hr/>
						<label>Step 2. Fill up the information needed for each columns of the template.</label>
						<hr/>
						<div class="form-group">
							<label for="import_cust_csv">Step 3. Select the updated file and click 'Import' to proceed.</label>
							<?php
								echo $ui->setElement('file')
										->setId('import_cust_csv')
										->setName('import_cust_csv')
										->setAttribute(array('accept' => '.csv'))
										->setValidation('required')
										->draw();
							?>
							<span class="help-block"></span>
						</div>
						<p class="help-block">The file to be imported must be in CSV (Comma Separated Values) file.</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-info btn-flat" id = "btnImport">Import</button>
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<script>
var ajax = {};
var tagged 		 = new Array();
var ret_tagged 	 = new Array();

function addCommas(nStr)
{
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

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

/**VALIDATE FIELD**/
function validateField(form,id,help_block)
{
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

function show_success_msg(msg)
{
	$('#success_modal #message').html(msg);
	$('#success_modal').modal('show');
}

var set_tagged = true;
function showList(){

	ajax.code 	=	$('#partnercode').val();
	ajax.tagged = 	tagged;	
	$.post('<?=BASE_URL?>maintenance/sales_person/ajax/tagging_list',ajax, function(data) {

		$('#tag_customer_table #list_container').html(data.table);
		$('#pagination').html(data.pagination);

		var ret_tagged 	=	data.tagged; 
		if( ret_tagged.length != 0 && set_tagged )
		{
			tagged 	=	ret_tagged;
			set_tagged = false;
		}

		for ( var i = 0, l = tagged.length; i < l; i++ ) {
			var checked = tagged[i];

			$('#'+checked).iCheck('check');
		}

		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			showList();
		}
	});
};

$( "#tag_search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
});

showList();

$(document).ready(function(){
	
	var task 	=	'<?=$task?>';

	if( task == 'view' )
	{
		$('.hide_in_view').addClass('hidden');
		$('.show_in_view').removeClass('hidden');
	}

	$('form').submit(function(e) {
		e.preventDefault();
		$.post('<?=BASE_URL?>maintenance/sales_person/ajax/<?=$task?>', $(this).serialize()+ '<?=$ajax_post?>'+"&tagged="+tagged, function(data) {
			if (data.msg == 'success') {
				window.location = '<?php echo BASE_URL . 'maintenance/sales_person'; ?>';
			}
		});
	});

	$('#tag_customer_table').on('ifChecked', 'input[type="checkbox"]', function() {
		var code = 	$(this).val(); 
		if(  jQuery.inArray( code, tagged ) == -1 )
		{
			tagged.push(code);
		}
		console.log(JSON.stringify(tagged));
	});

	$('#tag_customer_table').on('ifUnchecked', 'input[type="checkbox"]', function() {
		var remove_this  = 	$(this).val(); 
		tagged = jQuery.grep(tagged, function(value) {
			return value != remove_this;
		});
		console.log(JSON.stringify(tagged));
	});

	/** For Import Modal **/
	
	$('#open_customer_modal').on('click',function(){
		$('#import-tagcust-modal').modal('show');
	});

	$("#open_customer_modal").click(function() 
	{
		$(".import-modal > .modal").css("display", "inline");
		$('.import-modal').modal();
	});

	$("#importForm #btnImport").click(function() 
	{
		var formData =	new FormData();
		formData.append('file',$('#import_cust_csv')[0].files[0]);
		formData.append('partnercode',$('#partnercode').val());
		ajax_call 	=	$.ajax({
							url : '<?=MODULE_URL?>ajax/save_import_customers',
							data:	formData,
							cache: 	false,
							processData: false, 
							contentType: false,
							type: 	'POST',
							success: function(response){
								if(response && response.errmsg == ""){
									$('#import-tagcust-modal').modal('hide');
									$(".alert-warning").addClass("hidden");
									$("#errmsg").html('');
									show_success_msg("Your Data has been successfully imported!");
								}else{
									$('#import-tagcust-modal').modal('hide');
									show_error(response.errmsg);
								}
							},
						});
	});
	
	$('#importForm').on('change', '#import_cust_csv', function() {
		var filename = $(this).val().split("\\");
		$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
	});

    $('#import-tagcust-modal').on('show.bs.modal', function() {
		var form_csv = $('#import_cust_csv').val('').closest('.form-group').find('.form-control').html('').closest('.form-group').html();
		$('#import_cust_csv').closest('.form-group').html(form_csv);
	});

	$('#success_modal .btn-success').on('click', function(){
		$('#success_modal').modal('hide');
		showList();
	});
});

</script>
