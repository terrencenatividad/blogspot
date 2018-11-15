		<section class="content">
			<div class = "alert alert-warning alert-dismissable hidden">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4><strong>Error!<strong></h4>
					<div id = "errmsg"></div>
				</div>

				<div class="box box-primary">
					<div class="panel panel-default">

						<form class="form-horizontal form-group" method="POST" id="coaForm" autocomplete="off">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6">
										<?
										echo $ui->formField('dropdown')
										->setLabel('Bundle Item Code ')
										->setPlaceholder('Select One')
										->setSplit('col-md-3', 'col-md-8')
										->setName('bundle_item_code')
										->setId('bundle_item_code')
										->setList(array('1' => 'Bundle Code 1', '2' => 'Bundle Code 2'))
										->setValue($bundle_item_code)
										->setValidation('required')
										->draw($show_input);	
										?>	
									</div>	
									<div class="col-md-6">
										<?
										echo $ui->formField('textarea')
										->setLabel('Description')
										->setSplit('col-md-3', 'col-md-8')
										->setName('description')
										->setId('description')
										->setValue($description)
										->setValidation('required')
										->draw($show_input);	
										?>	
									</div>			
								</div>
							</div>
							<div class="panel panel-default">
								<div class="table-responsive">
									<fieldset>
										<table class="table table-hover table-condensed " id="itemsTable">
											<thead>
												<tr class="info">
													<th class="col-md-3 text-center">Item Code</th>
													<th class="col-md-3 text-center">Item Name</th>
													<th class="col-md-2">Description</th>
													<th class="col-md-2">Percentage</th>
													<th class="col-md-2 center">UOM</th>
													<th class="col-md-1 center"></th>
												</tr>
											</thead>
											<tbody>
												<tr class="clone" valign="middle">
													<td class = "remove-margin">
														<?php
														echo $ui->formField('dropdown')
														->setPlaceholder('Select One')
														->setSplit('col-md-3', 'col-md-12')
														->setName("item_code")
														->setId("item_code")
														->setList(array('1' => 'Code 1', '2' => 'Code 2'))
														->setNone('Select One')
														->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
														echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('item_name')
														->setId('item_name')
														->setAttribute(array('readonly'))
														->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
														echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setName('detailsdesc')
														->setId('detailsdesc')
														->setAttribute(array('readonly'))
														->draw($show_input);
														?>
													</td>
													<td class = "remove-margin">
														<?php
														echo $ui->formField('text')
														->setPlaceholder('0.00')
														->setSplit('', 'col-md-12')
														->setName('quantity')
														->setId('quantity')
														->setClass('text-right')
														->draw($show_input);
														?>
													</td>

													<td class = "remove-margin">
														<?php
														echo $ui->formField('text')
														->setSplit('', 'col-md-12')
														->setAttribute(array('readonly'))
														->setName('uom')
														->setId('uom')
														->draw($show_input);
														?>
													</td>	

													<td class="text-center">
														<button type="button" class="btn btn-danger btn-flat confirm-delete"><span class="glyphicon glyphicon-trash"></span></button>
													</td>			
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td>
														<a type="button" class="btn btn-link add-data" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
													</td>	
												</tr>
											</tfoot>
										</table>
									</fieldset>
								</div>
							</div>
							<div class="panel-footer">
								<div class="row center">
									<div class="col-md-5 col-sm-4 col-xs-4"></div>
									<div class="col-md-2 col-sm-3 col-xs-3" id="task_buttons" style="padding:3px;">
										<?php if($ajax_task == "ajax_view") {?>
											<input type = "button" name = "add" value = "<?= $button_name ?>" 
											class = "btn btn-primary btn-flat" 
											onClick = "document.location = '<?=MODULE_URL?>edit/<?=$sid?>'">	
										<?php } else { ?>
											<input type = "submit" name = "update" value = "Save" 
											class = "btn btn-primary btn-flat">	
										<?php } ?>	
										<a href="<?=MODULE_URL?>" class="btn btn-default btn-flat">Cancel</a>


										<div class="col-md-5 col-sm-4 col-xs-4"></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</section>

				<div class="modal fade" id="deleteItemModal" tabindex="-1" data-backdrop="static">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								Confirmation
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								Are you sure you want to cancel this record?
							</div>
							<div class="modal-footer">
								<div class="row row-dense">
									<div class="col-md-12 center">
										<div class="btn-group">
											<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
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

				<script>
					var clone = $("#itemsTable tbody tr.clone:first").clone(true);
					$('#itemsTable').on('click', '.add-data' ,function() {
						var parent = $("#itemsTable tbody tr.clone").last();
						clone.clone().insertAfter(parent);
						$('#itemsTable tbody tr.clone #proj_name').last().val('');
						$('#itemsTable tbody tr.clone #proj_code').last().val('');
						$('#itemsTable tbody tr.clone #approved_budget').last().val('');
						$('#itemsTable tbody tr.clone #available_balance').last().val('');
						drawTemplate();
					});
					var remove = '';
					$('#itemsTable').on('click', '.confirm-delete', function() {
						var len = $('tr.clone').length;
						if(len == 1) {
							$('#itemsTable tbody tr.clone #proj_name').val('');
							$('#itemsTable tbody tr.clone #proj_code').val('');
							$('#itemsTable tbody tr.clone #approved_budget').val('');
							$('#itemsTable tbody tr.clone #available_balance').val('');
							$('#total_cost').val('');
						} else {
							$('#deleteItemModal').modal('show');
							remove = $(this).closest('tr');
						}
					});

					$('#btnYes').on('click', function() {
						remove.remove();
						$('#deleteItemModal').modal('hide');
					});


					<?php if ($show_input): ?>
						$('form').submit(function(e) {
							e.preventDefault();
							$('#delay_modal').modal('show');
							return false;
							var status = $(document.activeElement).attr('id');
							$(this).find('.form-group').find('input, textarea, select').trigger('blur');
							if ($(this).find('.form-group.has-error').length == 0) {
								$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>' + '&status=' + status, function(data) {
									if (data.success) {
										window.location = data.redirect;
									}
								});
							} else {
								$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
							}
						});
					<?php endif ?>
				</script>