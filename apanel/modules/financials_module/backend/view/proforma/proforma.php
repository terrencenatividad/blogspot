<section class="content">
	<div class = "alert alert-warning alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<h4><strong>Error!<strong></h4>
		<div id = "errmsg"></div>
	</div>
	<div class="box box-primary">
        <div class = "col-md-12">&nbsp;</div>

		<form class="form-horizontal" method="POST" id="proformaForm" autocomplete="off">
			<div class="panel panel-default">	
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
							<?
								if($task=='create'){
									echo $ui->formField('text')
											->setLabel('Proforma Code')
											->setSplit('col-md-4', 'col-md-8')
											->setName('proformacode')
											->setId('proformacode')
											->setValue($proformacode)
											->draw($task != "view");
								}else{
									echo $ui->formField('text')
											->setLabel('Proforma Code')
											->setSplit('col-md-4', 'col-md-8')
											->setName('proformacode')
											->setId('proformacode')
											->setValue($proformacode_)
											->setAttribute(array("disabled"=>""))
											->draw($task != "view");
									echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
									}
												

							?>	
							</div>	
							<div class="col-md-6">		
							<?
									echo $ui->formField('dropdown')
												->setLabel('Transaction Type')
												->setPlaceholder('Select Transaction Type')
												->setSplit('col-md-4', 'col-md-8')
												->setName('financialtype')
												->setId('financialtype')
												->setList($financialtype_list)
												->setValue($transactiontype)
												->draw($task != "view");
							?>
							</div>				
						</div>
						<br>
						<div class="row">
							<div class = "col-md-6">
							<?php
										
									echo $ui->formField('text')
											->setLabel('Proforma Description')
											->setSplit('col-md-4', 'col-md-8')
											->setName('proformadesc')
											->setId('proformadesc')
											->setValue($proformadesc)
											->draw($task != "view");	

							?>
							</div>
							
						</div>
						<div class="panel panel-info">
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-hover table-condensed " id="itemsTable">
									<thead>
										<tr class="info">
											<th class="col-md-3 left">Account code</th>
											<th class="col-md-8 left"> &nbsp; </th>
											<?if($task!='view'){?>
											<th class="col-md-1 center"></th>
											<?}?>
										</tr>
									</thead>
							<tbody>
							<?	
						
								if($task=='create'){
									$accountcodeid 	= '';	
									$row 			= 1;
							?>
								<tr class="clone" valign="middle">
									<td style="width: 50%;">
										<?
										echo $ui->formField('dropdown')
												->setPlaceholder('Select Account Code')
												->setSplit('col-md-4', 'col-md-12')
												->setName('accountcodeid['.$row.']')
												->setId('accountcodeid['.$row.']')
												->setList($accountcodeoption_list)
												->draw($task != "view");
										?>
									</td>
									<td> &nbsp; 

									</td>
									 <?if($task!='view'){?>
                                    <td class="center">
                                    	<button type="button" class="btn btn-danger confirm-delete" id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
                                    </td>
                                    <?}?>
									
								</tr>
								<?
									$row++;
								?>
								<!--EDIT AND VIEW -->

								<?
								}else if(!empty($sid) && ($task=='edit' || $task == 'view')){
									
								$row 			= 1;
								
								if(!empty($detailList))
								{
									
									for($i=0;$i < count($detailList);$i++)
									{	
									
										$accountname = $detailList[$i]->accountname;
										$accountcodeid = $detailList[$i]->accountcodeid;
									
								?>
							
								<tr class="clone" valign="middle">
									<td style="width: 50%;">
										<!--?=$objUiClass->selectBox("accountcodeid[".$row."]", $accountcode, $task, $accountCode,$accountName,"select", "form-control chosen-select");?>-->
									<?	
										echo $ui->formField('dropdown')
										->setSplit('', 'col-md-12')
										->setName('accountcodeid['.$row.']')
										->setId('accountcodeid['.$row.']')
										->setList($accountcodeoption_list)
										->setValue($accountcodeid)
										->setValidation('required')
										->draw($task != "view");
									?>		
									</td>
									<td> &nbsp; 

									</td>
									 <?if($task!='view' && !empty($sid)){?>
                                    <td class="center">
                                    	<button type="button" class="btn btn-danger confirm-delete" id="<?=$row?>" name="chk[]" style="outline:none;" 
										onClick="confirmDelete(<?=$row?>);"><span class="glyphicon glyphicon-trash"></span></button>
                                    </td>
                                    <?}?>
									
								</tr>
								<?
									$row++;
								?>
									
									
								</tr>

									
								<? } ?> <!-- end foreach -->
							<?	}?>

							<? } ?> <!-- end if !empty -->

							</tbody>
							<tfoot>
								<tr>
									<td>
										<?if($task!='view'){?>
										<!--<a type="button" class="btn btn-link add"  style="text-decoration:none; outline:none;"  onClick="setZero();" onMouseOut="setZero();" onKeyUp="setZero();" rel=".clone" >Add New Line</a>-->
										<a type="button" class="btn btn-link add-data"  style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
										<?}?>
									</td>	
								</tr>
							</tfoot>		
						</table>
					</div> <!-- end class table responsive -->
							</div>
								
						</div>

					</div>
					<div class="panel-footer">
						<div class="row center">
							<div class="col-md-5 col-sm-4 col-xs-4"></div>
							<div class="col-md-2 col-sm-3 col-xs-3" id="task_buttons" style="padding:3px;">
							<?if($task != "view"):?>
							<input type = "submit" name = "update" value = "Save" 
							class = "btn btn-primary btn-flat">	
						
							&nbsp;&nbsp;&nbsp;
							<a href="<?=MODULE_URL?>" class="btn btn-default">Cancel</a>
							<?else:?>
							<a href="<?=MODULE_URL?>edit/<?=$sid?>" 
									role="button" class="btn btn-primary btn-flat">Edit</a>
							<a href="<?=MODULE_URL?>" class="btn btn-default">Exit</a>
							<?  
							endif;
							?>
							<div class="col-md-5 col-sm-4 col-xs-4"></div>
						</div>
					</div>
			</div>
		</form>
	</div>
</section>
<!-- CANCEL MODAL-->
<div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to cancel?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-info" id="btnYes">Yes</button>
						</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--DELETE RECORD CONFIRMATION MODAL-->
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
							<button type="button" class="btn btn-info" id="btnYes">Yes</button>
						</div>
						&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var base_url = '<?=BASE_URL?>';


/**MODAL DELETE ROW**/
function confirmDelete(id)
{
	$('#deleteItemModal').data('id', id).modal('show');
}

/**DELETE ROW**/
function deleteItem(row)
{
	
	//var task		= document.getElementById('task').value;
	//var voucher		= document.getElementById('proformacode').value;
	//var companycode	= document.getElementById('cmp').value;
	var table 		= document.getElementById('itemsTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;
	var rowindex	= table.rows[row];
		
	if(rowCount > 1){
		table.deleteRow(row);	
	}

}

/**SET TABLE ROWS TO DEFAULT VALUES**/
function setZero()
{
	
	resetIds();
	
	var table 		= document.getElementById('itemsTable');
	var newid 		= table.rows.length - 2;
	var account		= document.getElementById('accountname['+newid+']');

	if(document.getElementById('accountname['+newid+']')!=null)
	{
		document.getElementById('accountname['+newid+']').value 		= '';
		$('#accountname\\['+newid+'\\]').trigger('change');
		
	}
}
	  
/**RESET IDS OF ROWS**/
function resetIds()
{
	var table 	= document.getElementById('itemsTable');
	var count	= table.rows.length - 2;
	//alert(count);
	x = 1;
	for(var i = 1;i<=count;i++){
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'accountcodeid['+x+']';

		
		
		row.cells[0].getElementsByTagName("select")[0].name = 'accountcodeid['+x+']';
	
		row.cells[0].getElementsByTagName("select")[0].setAttribute('data-id',x);
		row.cells[2].getElementsByTagName("button")[0].setAttribute('onClick','confirmDelete('+x+')');
		x++;
	}
	
}

/**VALIDATE FIELD**/
function validateField(form,id)
{
	var field	= $("#"+form+" #"+id).val();

	if(field == ''){
		$("#"+form+" #"+id)
			.closest('.field_col')
			.addClass('has-error');

		$("#"+form+" #"+id)
			.next(".help-block")
			.removeClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0]){
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.removeClass('hidden');
		}
		return 1;
	}else{
		$("#"+form+" #"+id)
			.closest('.field_col')
			.removeClass('has-error');

		$("#"+form+" #"+id)
			.next(".help-block")
			.addClass('hidden');
			
		if($("#"+form+" #"+id).parent().next(".help-block")[0]){
			$("#"+form+" #"+id)
			.parent()
			.next(".help-block")
			.addClass('hidden');
		}
		return 0;
	}
}

function validateCode(form,field,val)
{	
	var valid		= document.getElementById('valid');
	var oldcode		= document.getElementById('oldcode').value;
	
	var cmp			= document.getElementById('companycode').value;
	var table		= 'proforma';
	var condition	= " (proformacode = '"+val+"' AND proformacode != '"+oldcode+"') AND companycode = '"+cmp+"' AND stat='active' ";
	$.post("./ajax/getValue.php",{fields:field,"table":table,"condition":condition})
	.done(function( data ) {
		var resp			= data.split('|');
		
		if(val != ''){
			if(resp[0] != ''){
				$("#"+form+" #"+field)
					.closest('.field_col')
					.addClass('has-error');
		
				$("#"+form+" #"+field)
					.next(".help-block")
					.html('<i class="glyphicon glyphicon-exclamation-sign"></i> Proforma code <b>[ '+val+' ]</b> already exist.');
			
				$("#"+form+" #"+field)
					.next(".help-block")
					.removeClass('hidden');
			
				SelectAll(field);
			
				valid.value = 1;
			}else{
				$("#"+form+" #"+field)
					.closest('.field_col')
					.removeClass('has-error');
		
				$("#"+form+" #"+field)
					.next(".help-block")
					.html('<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.');
			
				$("#"+form+" #"+field)
					.next(".help-block")
					.addClass('hidden');
			
				valid.value = 0;
			}
			
		}else{
		
			$("#"+form+" #"+field)
				.next(".help-block")
				.html('<i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.');
		
			valid.value = 0;
		}
	});
}

/**HIGHLIGHT INPUT CONTENT**/
function SelectAll(id)
{
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

$(function(){
	$('#proformaForm').submit(function(e) 
	{
		e.preventDefault();
		var ajax_post = "<?=$ajax_post?>";
		$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + ajax_post, function(response) 
		{
			if( response.msg == "success" )
			$('#delay_modal').modal('show');
							setTimeout(function() {							
								window.location = "<?= MODULE_URL ?>";
						}, 1000)	
			if(response.msg == "error_add")
			{
				$(".alert-warning").removeClass("hidden");
				$("#errmsg").html("Error Inserting " + 
									response.proform_code + " " + 
									response.proforma_desc);
			}
		});
	});

	$('#btnExit').click(function() {
		window.location = '<?=MODULE_URL?>';
	});

	$('#btnCancel').on('click', function(e) {
		e.preventDefault();
	
		$('#cancelModal').modal('show');
	});

	$('#cancelModal #btnYes').click(function() {
		var id 		= $('#cancelModal').data('id');
		// var type	= document.getElementById('type').value;
		// var mod		= document.getElementById('mod').value;
		
		$('#cancelModal').modal('hide');
		//alert(id);
		// location.href	= 'index.php?mod='+mod+'&type='+type;
	});

			
	/**CONFIRM DELETE ROW**/

	// Deletion of Row
	$('#deleteItemModal #btnYes').click(function() 
	{
		// handle deletion here
		var id = $('#deleteItemModal').data('id');

		var table 		= document.getElementById('itemsTable');
		var rowCount 	= table.rows.length - 2;
		
		 deleteItem(id);
		
		$('#deleteItemModal').modal('hide');
	});

	$('body').on('click', '.add-data', function() {

		$('#itemsTable tbody tr.clone select').select2('destroy');
		
		/**ADD NEW ROW**/
		var clone = $("#itemsTable tbody tr.clone:first").clone(true); 

		var ParentRow = $("#itemsTable tbody tr.clone").last();
	
		clone.clone(true).insertAfter(ParentRow);
		
		setZero();
		
		$('#itemsTable tbody tr.clone select').select2({width: "100%"});

	});
});


</script>
