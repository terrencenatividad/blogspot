<section class="content">

    <div class="box box-primary">
	
        <div class="box-body">

			<form method = "post" id = "pricelist_template_form" class="form-horizontal">
				<input type="hidden" name="h_disc_code" id="h_disc_code" value="<?=$itemPriceCode?>">	
				<input type="hidden" name="task" id="task" value="<?=$task?>">	
				<div class = "col-md-12">&nbsp;</div>
				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Price List Code ')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistcode')
									->setId('pricelistcode')
									->setValue($itemPriceCode)
									->setMaxLength(20)
									->addHidden((isset($task) && $task == 'edit'))
									->setValidation('required code')
									->draw((isset($task) && $task == 'create'));
						?>
						<input type = "hidden" id = "h_price_code" name = "h_price_code" value = "<?= $itemPriceCode ?>">
					</div>

					<div class = "col-md-6">
						<?php
							echo $ui->formField('text')	
									->setLabel('Price List Name ')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistname')
									->setId('pricelistname')
									->setMaxLength('50')
									->setValue($itemPriceName)
									->setValidation('required special')
									->draw($show_input);
						?>
					
					</div>
				</div>

				<div class = "row">
					<div class = "col-md-6">
						<?php
							echo $ui->formField('textarea')	
									->setLabel('Description ')
									->setSplit('col-md-4', 'col-md-8')
									->setName('pricelistdesc')
									->setId('pricelistdesc')
									->setMaxLength(250)
									->setValue($itemPriceDesc)
									->setValidation('required')
									->draw($show_input);
						?>
					</div>
				</div>

				<hr/>

				<table id = "items_table" class="table table-hover">
					<thead>
						<tr class = "info">
							<th class = "col-md-2">Item Code</th>
							<th class = "col-md-3">Description</th>
							<th class = "col-md-2">Original Price</th>
							<th class = "col-md-2">Adjusted Price</th>
							<th class = "col-md-1">UOM</th>
							<?
							if( $task != 'view' ){
							?>
								<th class = "col-md-1 text-center"></th>
							<?
							}
							?>
						</tr>
					</thead>
				
					<tbody id = "list_container">
					<?php
					
					if($task == 'create')
					{
						$row 			   	= 1;
					
					?>
                        <tr class="clone" valign="middle">
                            <td class = "remove-margin">
                                <?php
                                    echo $ui->formField('dropdown')
                                        ->setPlaceholder('Select an Item')
                                        ->setSplit('	', 'col-md-12')
                                        ->setName("itemcode[".$row."]")
                                        ->setId("itemcode[".$row."]")
                                        ->setClass('itemcode')
                                        ->setList($itemcodes)
                                        ->setValidation('required')
                                        ->setValue("")
                                        ->draw($show_input);
                                ?>
                            </td>
							<td class = "remove-margin">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('detailparticulars['.$row.']')
											->setId('detailparticulars['.$row.']')
											->setMaxLength(250)
											->setAttribute(array("readonly"=>''))
											->setValue("")
											->draw($show_input);
								?>
							</td>
                            <td class = "remove-margin">
                                <?php
                                    echo $ui->formField('text')
                                            ->setSplit('', 'col-md-12')
                                            ->setName('original_price['.$row.']')
											->setId('original_price['.$row.']')
                                            ->setAttribute(array("readonly"=>''))
                                            ->setValue("")
                                            ->draw($show_input);
                                ?>
							</td>
                            <td class = "remove-margin">
                                <?php
                                    echo $ui->formField('text')
                                            ->setSplit('', 'col-md-12')
                                            ->setName('adjusted_price['.$row.']')
                                            ->setId('adjusted_price['.$row.']')
                                            ->setMaxLength(100)
                                        	->setValidation('decimal required')
                                            ->setValue("")
                                            ->draw($show_input);
                                ?>
							</td>
							<td class = "remove-margin">
							<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('uom['.$row.']')
											->setId('uom['.$row.']')
											->setAttribute(array("readonly"=>''))
											->setValue("")
											->draw($show_input);
								?>
							</td>
							<td class="text-center">
								<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
							</td>
                        </tr>
					<?php
					}	
					else if( !empty($sid) && $task!='create' )
					{
						$row 			= 1;
						
						for($i = 0; $i < count($details); $i++)
						{
							$itemcode 	 		= $details[$i]->itemcode;
							$detailparticular	= $details[$i]->description;
							$original 			= $details[$i]->original;
							$adjusted 			= $details[$i]->adjusted_price;
							$uom 				= $details[$i]->uomcode;
							
					?>	
						 <tr class="clone" valign="middle">
                            <td class = "remove-margin">
                                <?php
                                    echo $ui->formField('dropdown')
                                        ->setPlaceholder('Select an Item')
                                        ->setSplit('	', 'col-md-12')
                                        ->setName("itemcode[".$row."]")
                                        ->setId("itemcode[".$row."]")
                                        ->setClass('itemcode')
                                        ->setList($itemcodes)
                                        ->setValidation('required')
                                        ->setValue($itemcode)
                                        ->draw($show_input);
                                ?>
                            </td>
							<td class = "remove-margin">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('detailparticulars['.$row.']')
											->setId('detailparticulars['.$row.']')
											->setMaxLength(100)
											->setAttribute(array("readonly"=>''))
											->setValue($detailparticular)
											->draw($show_input);
								?>
							</td>
							<td class = "remove-margin">
                                <?php
                                    echo $ui->formField('text')
                                            ->setSplit('', 'col-md-12')
                                            ->setName('original_price['.$row.']')
                                            ->setId('original_price['.$row.']')
                                            ->setAttribute(array("readonly"=>''))
                                            ->setValue(number_format($original,2))
                                            ->draw($show_input);
                                ?>
							</td>
                            <td class = "remove-margin">
                                <?php
                                    echo $ui->formField('text')
                                            ->setSplit('', 'col-md-12')
                                            ->setName('adjusted_price['.$row.']')
                                            ->setId('adjusted_price['.$row.']')
                                            ->setMaxLength(20)
                                        	->setValidation('decimal')
                                            ->setValue(number_format($adjusted,2))
                                            ->draw($show_input);
                                ?>
							</td>
							<td class = "remove-margin">
								<?php
									echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setName('uom['.$row.']')
											->setId('uom['.$row.']')
											->setAttribute(array("readonly"=>''))
											->setValue($uom)
											->draw($show_input);
								?>
							</td>
							<?if($task!='view'){ ?>
								<td class="text-center">
									<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
								</td>
							<?}?>		
                        </tr>
					<?	
							$row++;	
						}
					}
					?>
                    </tbody>
                    <tfoot class="summary">
                        <tr>
                            <td>
                                <? if($task != 'view') { ?>
                                    <a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
                                <? } ?>
                            </td>	
                        </tr>	
                    </tfoot>
				</table>

				<hr/>

				<div class="row">
					<div class="col-md-12 text-center">
                    <?php
                        echo $ui->loadElement('check_task')
                            ->addOtherTask('Save','',($task != 'view'),'primary')
                            ->addEdit(($task == 'view'))
                            //->addCancel()
                            ->setValue($itemPriceCode)
                            ->draw_button($show_input);
					?>
						&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
						<!-- <button type="button" class="btn btn-default btn-flat" id="btnCancel" data-toggle="back_page">Cancel</button> -->
					</div>
					<input value = "" name = "save" id = "save" type = "hidden">
					</div>	
				</div>

			</form>

		</div>

	</div>

</div>

<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this record?
				<input type="hidden" id="recordId"/>
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-flat" id="btnYes">Yes</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-warning" id="sameItemCode" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Warning</h4>
			</div>
			<div class="modal-body">
				<p>This Item Code has already been selected.<br>
					Please select a different Item Code. </p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>

var ajax = {};

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

/**RESET IDS OF ROWS**/
function resetIds()
{
	var table 	= document.getElementById('items_table');
	var count	= table.tBodies[0].rows.length;

	x = 1;
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];

		row.cells[0].getElementsByTagName("select")[0].id 	= 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'original_price['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'adjusted_price['+x+']';
		row.cells[4].getElementsByTagName("input")[0].id 	= 'uom['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'itemcode['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'detailparticulars['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'original_price['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'adjusted_price['+x+']';
		row.cells[4].getElementsByTagName("input")[0].name 	= 'uom['+x+']';

		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[5].getElementsByTagName("button")[0].setAttribute('data-id',x);
		row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');

		x++;
	}
	
}

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero()
{
	resetIds();
	
	var table 		= document.getElementById('items_table');
	var newid 		= table.tBodies[0].rows.length;

	document.getElementById('itemcode['+newid+']').value 			= '';
	document.getElementById('detailparticulars['+newid+']').value 	= '';
	document.getElementById('original_price['+newid+']').value 		= '';
	document.getElementById('adjusted_price['+newid+']').value 		= '';
	document.getElementById('uom['+newid+']').value 				= '';
}

/**RETRIEVAL OF ITEM DETAILS**/
function getItemDetails(id)
{
	if( id != undefined )
	{
		var itemcode 	=	document.getElementById(id).value;
		var row 		=	id.replace(/[a-z]/g, '');

		$.post('<?=BASE_URL?>maintenance/pricelist/ajax/get_item_details',"itemcode="+itemcode, function(data) 
		{
			if( data != false )
			{
				document.getElementById('detailparticulars'+row).value 		=	data.itemdesc;
				document.getElementById('original_price'+row).value 		=	addComma(data.price);
				document.getElementById('uom'+row).value 			=	data.uomcode;

				$('#pricelist_template_form').trigger('change');
			}
			else
			{
				document.getElementById('detailparticulars'+row).value 		=	"";
				document.getElementById('original_price'+row).value 		=	"0.00";
				document.getElementById('uom'+row).value 			=	"";

				$('#pricelist_template_form').trigger('change');
			}
		});
	}
}

/** CONFIRMATION OF DELETION OF ROW DURING TRANSACTION**/
function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}

/** DELETION OF ROW DURING TRANSACTION **/
function deleteItem(row)
{
	var task		= '<?= $task ?>';
	var code		= document.getElementById('pricelistcode').value;
	var companycode	= '<?= COMPANYCODE ?>';
	var table 		= document.getElementById('items_table');
	var rowCount 	= table.tBodies[0].rows.length;
	var valid		= 1;

	var rowindex	= table.rows[row];

	if(rowindex.cells[0].childNodes[1] != null)
	{
		var index		= rowindex.cells[0].childNodes[1].value;
		var datatable	= 'price_list_details';

		if(rowCount > 1)
		{
			if(task != 'view')
			{
				ajax.table 		=	datatable;
				ajax.pl_code 	= 	code;
				ajax.itemcode 	=	$('#itemcode\\['+row+'\\]').val();
		
				$.post("<?=BASE_URL?>maintenance/pricelist/ajax/delete_row",ajax)
				.done(function( data ) 
				{
					if( data.msg == 'success' )
					{
						table.deleteRow(row);	
						resetIds();
					}
				});
			}
			else
			{
				table.deleteRow(row);	
				resetIds();
			}
		}
		else
		{	
			setZero();
			$('#itemcode\\['+row+'\\]').trigger('change');
		}
	}
	else
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetIds();
			$('#itemcode\\['+row+'\\]').trigger('change');
		}
		else
		{	
			setZero();
			$('#itemcode\\['+row+'\\]').trigger('change');
		}
	}
}

/**CANCEL TRANSACTIONS**/
function cancelTransaction()
{
	$.post("<?=BASE_URL?>maintenance/pricelist/ajax/cancel",'<?=$ajax_post?>')
	.done(function(data) 
	{
		if(data.msg == 'success')
		{
			window.location.href = '<?=BASE_URL?>maintenance/pricelist';
		}
	});
}

function checkItemCode() {
	var table 	= document.getElementById('items_table');
	var count	= table.tBodies[0].rows.length;
	var same 	= [];
	var same_cnt= 0;
	
	x = 1;
	for(var i = 1;i <= count;i++)
	{
		var row = table.rows[i];
		var itemcode_id = row.cells[0].getElementsByTagName("select")[0].id;
		var itemcode 	= document.getElementById(itemcode_id).value;
		
		if( jQuery.inArray(itemcode, same) > -1 ){
			$('#sameItemCode').modal('show');
			$('#itemcode\\['+i+'\\]').closest('div').parent().addClass('has-error');
			
			same_cnt++;
		}
		else
		{
			$('#itemcode\\['+i+'\\]').closest('div').parent().removeClass('has-error');
		}
		same.push(itemcode);
		x++;
	}
	return same_cnt;
}

$(document).ready(function(){

	var task 	=	'<?=$task?>';

    $('body').on('click', '.add-data', function() 
    {	
        $('#items_table tbody tr.clone select').select2('destroy');
        
        var clone = $("#items_table tbody tr.clone:first").clone(true); 

        var ParentRow = $("#items_table tbody tr.clone").last();
    
        clone.clone(true).insertAfter(ParentRow);
        
        setZero();
        
        $('#items_table tbody tr.clone select').select2({width: "100%"});
    });

	$('.itemcode').on('change', function(e) 
	{
		var id 		= 	$(this).attr("id");
		var result  = 	checkItemCode();
		
		if( result.length!=0 )
		{
			getItemDetails(id);
		}
	});

	$('#pricelistcode').on('blur',function(){
		ajax.old_code 	= 	$('#h_price_code').val();
		ajax.curr_code 	=	$(this).val();

		var task 			=	task;
		var error_message 	=	'';	
		var form_group	 	= 	$('#pricelistcode').closest('.form-group');

		$.post('<?=BASE_URL?>maintenance/pricelist/ajax/get_duplicate',ajax, function(data) {
			// console.log(data);
			if( data.msg == 'exists' ) {
				error_message 	=	"<b>The Code you entered already exists!</b>";
				$('#pricelistcode').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
			} else if( ( ajax.curr_code != "" && data.msg == "") || (data.msg == '' && task == 'edit')) {
				if (form_group.find('p.help-block').html() != "") {
					form_group.removeClass('has-error').find('p.help-block').html('');
				}
			}
		});
	});

	$('#deleteItemModal #btnYes').click(function() 
	{
		var id = $('#deleteItemModal').data('id');

		var table 		= document.getElementById('items_table');
		var rowCount 	= table.tBodies[0].rows.length;;

		deleteItem(id);
		
		$('#deleteItemModal').modal('hide');
	});

	$('#cancelbtn').on('click', function() 
	{
		if( task == 'create')
		{
			var record = document.getElementById('pricelistcode').value;
			cancelTransaction(record);
		}
		else
		{
			window.location =	"<?= BASE_URL ?>maintenance/pricelist/";
		}
	});

	if( task == 'create' )
	{
		$("#pricelist_template_form").change(function()
		{
			$('#pricelist_template_form #pricelistcode').trigger('blur');
			$('#pricelist_template_form #pricelistdesc').trigger('blur');
			$('#pricelist_template_form #pricelistname').trigger('blur');
			
			var parameter 	=	$("#pricelist_template_form").serialize();
			
			if( $("#pricelist_template_form").find('.form-group.has-error').length == 0 && $("#pricelist_template_form #itemcode\\[1\\]").val() != '' && $("#pricelist_template_form #adjusted_price\\[1\\]").val() > 0 ){
				$.post("<?=BASE_URL?>maintenance/pricelist/ajax/save_temp_data",parameter)
				.done(function(data){	
					
				});
			}
		});

		$('#pricelist_template_form').on('click','.save',function(){
			var result  = 	checkItemCode();
			$("#pricelist_template_form").find('.form-group').find('input, textarea, select').trigger('blur');

			if($("#pricelist_template_form").find('.form-group.has-error').length == 0 && result == 0 ){
				$('#save').val("final");
				$('#delay_modal').modal('show');
				setTimeout(function() {
					$('#pricelist_template_form').submit();
				},1000);
			}
		});
	}
	else
	{
		$('#pricelist_template_form').on('click','.save',function(){
			var result  = 	checkItemCode();
			$('#pricelist_template_form #pricelistcode').trigger('blur');
			$('#pricelist_template_form #pricelistdesc').trigger('blur');
			$('#pricelist_template_form #pricelistname').trigger('blur');

			$("#pricelist_template_form").find('.form-group').find('input, textarea, select').trigger('blur');

			var adj_price 	=	$("#pricelist_template_form #adjusted_price\\[1\\]").val();
			adj_price     = adj_price.replace(/\,/g,'');

			if( $("#pricelist_template_form").find('.form-group.has-error').length == 0 && $("#pricelist_template_form #itemcode\\[1\\]").val() != '' && adj_price > 0 && result == 0 ){
				setTimeout(function() {
					var parameter 	=	$("#pricelist_template_form").serialize()+'<?=$ajax_post?>';
				
					$.post("<?=BASE_URL?>maintenance/pricelist/ajax/save_temp_data",parameter,function(data)
					{	
						if(data.msg == 'success'){
							$('#delay_modal').modal('show');
							setTimeout(function() {									
								window.location.href = '<?=BASE_URL?>maintenance/pricelist';							
							}, 1000)
						}
					});
				},1000);
			}
		});
	}
});

</script>
