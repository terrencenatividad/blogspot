<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">

			<form method = "post" id = "tag_customer_form" class="form-horizontal">

				<div class = "col-md-12">&nbsp;</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Price List Code')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistcode')
									->setId('pricelistcode')
                                    ->setAttribute(array('readonly'=>""))
									->setValue($itemPriceCode)
									->draw($show_input);
						?>
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Price List Name')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistname')
									->setId('pricelistname')
                                    ->setAttribute(array('readonly'=>""))
									->setValue($itemPriceName)
									->draw($show_input);
						?>
					
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('textarea')	
									->setLabel('Description')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistdesc')
									->setId('pricelistdesc')
                                    ->setAttribute(array('readonly'=>""))
									->setValue($itemPriceDesc)
									->draw($show_input);
						?>
					</div>
				</div>

				<hr/>

				<!--<table id = "items_table" class="table table-hover">
					<thead>
						<tr class = "warning">
							<th class = "col-md-2 text-center">Item Code</th>
							<th class = "col-md-3 text-center">Description</th>
							<th class = "col-md-2 text-center">Selling Price</th>
						</tr>
					</thead>
					<tbody id = "list_container">
                    </tbody>
                </table>-->

				<div class="row">
					<div class="col-md-4">
						<button type="button" id="open_item_modal" class="btn btn-info btn-sm" data-toggle="modal"><i class="glyphicon glyphicon-new-window"></i> View Items</button>
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
				</div>
	
				<br/>

				<table id = "tag_customer_table" class="table table-hover">
					<thead>
						<tr class = "info">
							<th class = "col-md-1 hide_in_view" style="text-align:center;">
								<input type = "checkbox" name = "selectall" id = "selectall" />
							</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
							<th class = "col-md-2">Customer Code</th>
							<th class = "col-md-2">Customer Name</th>
							<th class = 'col-md-1 show_in_view hidden'></th>
						</tr>
					</thead>
				
					<tbody id = "list_container"></tbody>

				</table>
				
				<div id="pagination"></div>

				<hr/>

				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $ui->drawSubmit($show_input); ?>
						<a href="<?=BASE_URL?>maintenance/pricelist" class="btn btn-default">Cancel</a>
					</div>	
				</div>

			</form>

		</div>

	</div>

</div>

<div class="modal fade" id="item_modal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4><strong>List of Items</strong></h4>
				<!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
			</div>
			<div class="modal-body">
				<table id = "items_table" class="table table-hover">
					<thead>
						<tr class = "warning">
							<th class = "col-md-2 text-center">Item Code</th>
							<th class = "col-md-3 text-center">Description</th>
							<th class = "col-md-3 text-center">Original Price</th>
							<th class = "col-md-2 text-center">Adjusted Price</th>
						</tr>
					</thead>
					<tbody id = "list_container">
                    </tbody>
                </table>
				<div id="pagination"></div>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var ajax = {};
var ajax2 = {};
var ajax_call = {};
	ajax.filter = $('#filter_tabs .active a').attr('href');
	ajax.limit 	= $('#items').val();

var to_be_tagged = new Array();
var to_be_untag  = new Array();
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

function list_items(){
	ajax.plcode 	=	'<?=$itemPriceCode?>';
    $.post('<?=BASE_URL?>maintenance/pricelist/ajax/item_list',ajax, function(data) {
        $('#items_table #list_container').html(data.table);
        $('#item_modal #pagination').html(data.pagination);
    });
}

var set_tagged = true;
function list_customers(){
	ajax2.plcode 	=	'<?=$itemPriceCode?>';
	ajax2.tagged 	= 	tagged;

	$.post('<?=BASE_URL?>maintenance/pricelist/ajax/customer_list/', ajax2, function(data) {
        $('#tag_customer_table #list_container').html(data.table);
        $('#pagination').html(data.pagination);
		
		var ret_tagged 	=	data.tagged; 
		if( ret_tagged.length != 0 && set_tagged ) {
			tagged 	=	ret_tagged;
			set_tagged = false;
		}
		
		for ( var i = 0, l = tagged.length; i < l; i++ ) {
			var checked = tagged[i];

			$('#'+checked).iCheck('check');
		}

		if (ajax.page > data.page_limit && data.page_limit > 0) {
			ajax.page = data.page_limit;
			list_customers();
		}
    });
}

$(document).ready(function(){
	
	list_customers();
	
	var task 	=	'<?=$task?>';

	if( task == 'view' )
	{
		$('.hide_in_view').addClass('hidden');
		$('.show_in_view').removeClass('hidden');
	}

	$('form').submit(function(e) {
		e.preventDefault();
		$.post('<?=BASE_URL?>maintenance/pricelist/ajax/<?=$task?>', $(this).serialize()+ '<?=$ajax_post?>'+"&tagged="+tagged, function(data) {
			if (data.msg == 'success') {
				window.location = '<?php echo BASE_URL . 'maintenance/pricelist'; ?>';
			}
		});
	});

	$('#open_item_modal').on('click',function(){
		list_items();
		$('#item_modal').modal('show');
	});	

	$('#pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax2.page = $(this).attr('data-page');
			list_customers();
		}
	});

	$( "#tag_search" ).keyup(function() 
	{
		var search = $( this ).val();
		ajax2.search = search;
		list_customers();
	});

	$('#item_modal #pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			list_items();
		}
	});

	$('#tag_customer_table').on('ifChecked', 'input[type="checkbox"]', function() {
		var code = 	$(this).val(); 
		if(  jQuery.inArray( code, tagged ) == -1 )
		{
			tagged.push(code);
		}
		// console.log(JSON.stringify(tagged));
	});

	$('#tag_customer_table').on('ifUnchecked', 'input[type="checkbox"]', function() {
		var remove_this  = 	$(this).val(); 
		tagged = jQuery.grep(tagged, function(value) {
			return value != remove_this;
		});
		// console.log(JSON.stringify(tagged));
	});
});

</script>
