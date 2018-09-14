<section class="content">
	<div class="box box-primary">
		<div class="box-header">
			<!-- <form class="form-horizontal">
				<div class="row">
					<div class="col-md-3">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Switch To')
								->setSplit('col-md-4','col-md-8')
								->setPlaceholder('Select BIR Form')
								->setName('bir_form')
								->setId('bir_form')
								->setList($bir_forms)
								->setValue("")
								->draw(true);
						?>
					</div>
				</div>	
			</form> -->
		</div>
		
		<div class="box-body">
			<form method="post" id="birForm">
				<div class="col-md-10 col-md-offset-1">
					<p class="text-info">
						<em>Note : Make sure not to leave this page until you are satisfied with the generated PDF as data input on this page are not saved.</em>
					</p>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 text-center">
									<h3><strong>Annual Information Return of <br>
									Creditable Income Taxes Withheld (Expanded)/ <br>
									Income Payments Exempt from Withholding Tax</strong></h4>
								</div>
							</div>
							
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr>
										<td class="col-md-1">
											<p><strong>1</strong> For the year</p>
										</td>
										<td class="col-md-1">
											<?php
												echo $ui->formField('dropdown')
														->setName('yearfilter')
														->setId('yearfilter')
														->setList($years)
														->setValue($year)
														->setValidation('required')
														->draw(true);
											?>
										</td>
										<td class="col-md-2">
											<p><strong>2</strong> Amended Return?</p>
										</td>
										<td class="col-md-2">
										<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('amendreturn')
															->setValue("yes")
															->setDefault("yes")
															->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('amendreturn')
															->setValue("yes")
															->setDefault("no")
															->draw(true);
												?>
												No
											</label>
										</td>
										<td class="col-md-2">
											<p><strong>5</strong> No. of Sheet/s Attached</p>
										</td>
										<td class="col-md-1">
										<?php
												echo $ui->formField('text')
														->setName('attachments')
														->setId('attachments')
														->setMaxLength(2)
														->draw(true);
											?>
										</td>
									</tr>
									
								</table>
							</div>
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr class="hidden">
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td colspan="7" class="text-center">
											<h6><strong>Part I - Background Information</strong></h6>
										</td>
									</tr>
									<tr colspan="5">
										<td class="col-md-2">
											<p><strong>6</strong> Taxpayer Indentification Number (TIN)</p>
										</td>
										<td class="col-md-2">
											<?php
												echo $ui->formField('text')
														->setName('tin')
														->setId('tin')
														->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
														->setValue($tin)
														->setPlaceholder('000-000-000-000')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td class="col-md-1">
											<p><strong>7</strong> RDO Code</p>
										</td>
										<td class="col-md-1">
											<?php
												echo $ui->formField('text')
														->setName('rdo')
														->setId('rdo')
														->setMaxLength(3)
														->setValue($rdo_code)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td class="col-md-2">
											<p><strong>7</strong> Line of Business/Occupation</p>
										</td>
										<td class="col-md-2">
										<?php
												echo $ui->formField('text')
														->setName('businessline')
														->setId('businessline')
														->setValue($businessline)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td colspan="7">
										<div class="col-md-9">
											<p><strong>7</strong> Withholding Agent’s Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</small></p>
										</div>
										<div class="col-md-3">
										<p><strong>8</strong> Telephone No.
										</div>
										</td>
									</tr>
									<tr>
									<td colspan="7">
										<div class="col-md-9">
										<?php
												echo $ui->formField('text')
														->setName('agentname')
														->setId('agentname')
														->setValue($agentname)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
											</div>
											<div class="col-md-3">
											<?php
												echo $ui->formField('text')
														->setName('contact')
														->setId('contact')
														->setValue($contact)
														->setMaxLength(7)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="7">
										<div class="col-md-9">
											<p><strong>9</strong> Registered Address</p>
										</div>
										<div class="col-md-3">
										<p><strong>10</strong> Zip Code</p>
										</div>
										</td>
									</tr>
									<tr>
									<td colspan="7">
										<div class="col-md-9">
										<?php
												echo $ui->formField('text')
														->setName('firstaddress')
														->setId('firstaddress')
														->setMaxLength(40)
														->setValue($address)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
											</div>
											<div class="col-md-3">
											<?php
												echo $ui->formField('text')
														->setName('zipcode')
														->setId('zipcode')
														->setValue($zipcode)
														->setMaxLength(4)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
											</div>
										</td>
									
									</tr>
									<tr>
									
										<td colspan="7">
										<div class="col-md-4">
											<p><strong>11</strong> Category of Withholding Agent</p>
											</div>
											<div class="col-md-6">
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('category')
															->setValue("private")
															->setDefault("private")
															->draw(true);
												?>
												Private
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('category')
															->setValue("private")
															->setDefault("government")
															->draw(true);
												?>
												Government
											</label>
											</div>
										</td>
									
									</tr>
								</table>
							</div>
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr class="hidden">
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td colspan="5" class="text-center">
											<h6><strong>Part II – Summary of Remittances</strong></h6>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<h6><strong>Schedule I – Remittance per BIR Form No. 1601 - E</strong></h6>
										</td>
									</tr>
									<tr>
										<td class="col-md-1 text-center">
											MONTH
										</td>
										<td class="col-md-2 text-center">
										DATE OF REMITTANCE
										</td>
										<td class="col-md-3 text-center">
										NAME OF BANK/BANKCODE/ ROR NO., IF ANY
										</td>
										<td class="col-md-2 text-center">
											TAXES WITHHELD
										</td>
										<td class="col-md-2 text-center">
											PENALTIES
										</td>
										<td class="col-md-2 text-center">
											TOTAL AMOUNT REMITTED
										</td>
									</tr>
									<tbody id="tax_container">
									
									</tbody>
									<tr>
										<td><strong>Total</strong></td>
										<td></td>
										<td></td>
										<td><?php
												echo $ui->formField('text')
														->setName('totalwithheld')
														->setId('totalwithheld')
														->setClass('text-right amount')
														->setValue('')
														->setPlaceholder('0.00')
														->setAttribute(array('readOnly' => 'readOnly'))
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('totalpenalties')
														->setId('totalpenalties')
														->setClass('text-right amount')
														->setValue('')
														->setPlaceholder('0.00')
														->setAttribute(array('readOnly' => 'readOnly'))
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('total')
														->setId('total')
														->setClass('text-right amount')
														->setValue('')
														->setPlaceholder('0.00')
														->setAttribute(array('readOnly' => 'readOnly'))
														->draw(true);
											?>
										</td>
									</tr>
								
								</table>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-9">
							<p class="text-info">
								<em>Note : Make sure not to leave this page until you are satisfied with the generated PDF as data input on this page are not saved.</em>
							</p>
						</div>
						<div class="col-md-3 text-right">
							<button type="button" class="btn btn-primary" id="generate">Generate</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<script>
	$('#birForm #generate').on('click',function(){
		$.post("<?=MODULE_URL?>ajax/print_form/<?=$bir_form?>",$('#birForm').serialize())
		.done(function( data ) 
		{	
			var url = data.url;
			var win = window.open(url, '_blank');
			win.focus();
		});
		
	});

	var ajax = {}
	var ajax_call = '';
	ajax.year 		= $('#birForm #yearfilter').val();
	

	$('#birForm #yearfilter').on('change',function(){
		ajax.year = this.value;
		getList();	
	});

	$('body').on('blur', '[data-validation~="decimal"]', function(e) {
		computeTotal();
	});

	function getList() {
		if (ajax_call != '') {
			ajax_call.abort();
		}
		ajax_call = $.post("<?=MODULE_URL?>ajax/load_list/<?=$bir_form?>", ajax, function(data) {
			$('#birForm #tax_container').html(data.tax_table);

			$('.penalties').on('change', function(e){
				var penalties 	= 	$(this).val();
				var id 			= 	$(this).attr("id");
				var row 		=	id.replace(/[a-z]/g, '');
				
				var wtax 		= $('#taxwithheld'+row).val();
				var penalty 	= $('#penalties'+row).val();
				
				var totalamount			= parseFloat(wtax) + parseFloat(penalty);
				$('#totalamount'+row).val(addComma(totalamount));
				computeTotal();
			});
		});
	}
	getList();
	
	function computeTotal() {
		var sum = 0;
		$('.totalamount').each(function() {
			var value = $(this).val();
			sum += +value;
			$('#total').val(addComma(sum));
		});
	}
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
</script>