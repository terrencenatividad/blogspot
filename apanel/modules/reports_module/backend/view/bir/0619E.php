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
									<h3><strong>Monthly Remittance Return</strong></h3>
									<h4><strong>of Creditable Income Taxes Withheld (Expanded)</strong></h4>
								</div>
							</div>
							
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr>
										<td class="col-md-2" colspan="2">
											<p><strong>1</strong> For the month of (MM/YYYY)</p>
										</td>
										<td class="col-md-3">
											<p><strong>2</strong> Due Date (MM/DD/YYYY)</p>
										</td>
										<td class="col-md-2">
											<p><strong>3</strong> Amended Form?</p>
										</td>
										<td class="col-md-2">
											<p><strong>4</strong> Any Taxes Withheld?</p>
										</td>
										<td class="col-md-2">
											<p><strong>5</strong> ATC</p>
										</td>
										<td class="col-md-2">
											<p><strong>6</strong> Tax Type Code</p>
										</td>
									</tr>
									<tr>
										<td>
											<?php
												echo $ui->formField('dropdown')
														->setName('monthfilter')
														->setId('monthfilter')
														->setList($months)
														->setValue($month)
														->setValidation('required')
														->draw(true);
											?>
										</td>
										<td>
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
										<td>
												<?php
													echo $ui->formField('text')
															->setName('duedate')
															->setId('duedate')
															->setClass('datepicker-input')
															->setAttribute(array('readonly' => ''))
															->setValue($datefilter)
															->setAddon('calendar')
															->draw();
												?>
											
										</td>
										<td>
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
										<td>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('anytaxwithheld')
															->setValue("yes")
															->setDefault("yes")
															->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('anytaxwithheld')
															->setValue("yes")
															->setDefault("no")
															->draw(true);
												?>
												No
											</label>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('atc')
														->setId('atc')
														->setValue('WME10')
														->setAttribute(array("readonly" => "readonly"))
														// ->setMaxLength(2)
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxtype')
														->setId('taxtype')
														->setValue('WE')
														->setAttribute(array("readonly" => "readonly"))
														// ->setMaxLength(2)
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
										<td colspan="5" class="text-center">
											<h6><strong>Part I - Background Information</strong></h6>
										</td>
									</tr>
									<tr>
										<td class="col-md-3">
											<p><strong>7</strong> Taxpayer Indentification Number (TIN)</p>
										</td>
										<td class="col-md-3">
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
										<td></td>
										<td class="col-md-2">
											<p><strong>8</strong> RDO Code</p>
										</td>
										<td class="col-md-4">
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
									</tr>
									<tr>
										<td colspan="5">
											<p><strong>9</strong> Withholding Agent’s Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</small></p>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<?php
												echo $ui->formField('text')
														->setName('agentname')
														->setId('agentname')
														->setValue($agentname)
														->setMaxLength(40)
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
										<td colspan="5">
											<p><strong>10</strong> Registered Address <small>(Indicate complete address. If branch, indicate the branch address. If the registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small></p>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<?php
												echo $ui->formField('text')
														->setName('firstaddress')
														->setId('firstaddress')
														->setMaxLength(40)
														->setValue($firstaddress)
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
										<td colspan="3">
											<?php
												echo $ui->formField('text')
														->setName('secondaddress')
														->setId('secondaddress')
														->setValue($secondaddress)
														->setMaxLength(31)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td class="col-md-2">
											<p><strong>10A</strong> ZIP Code</p>
										</td>
										<td class="col-md-3">
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
										</td>
									</tr>
									<tr>
										<td class="col-md-1">
											<p><strong>11</strong> Contact Number</p>
										</td>
										<td class="col-md-2">
											<?php
												echo $ui->formField('text')
														->setName('contact')
														->setId('contact')
														->setMaxLength(12)
														->setValue($contact)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td colspan="2">
											<p><strong>12</strong> Cat. of Withholding Agent</p>
										</td>
										<td class="col-md-3">
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
										</td>
									</tr>
									<tr>
										<td>
											<p><strong>13</strong> Email Address</p>
										</td>
										<td colspan="4">
											<?php
												echo $ui->formField('text')
														->setName('email')
														->setId('email')
														->setMaxLength(36)
														->setValue($email)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
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
										<td colspan="5" class="text-center">
											<h6><strong>Part II – Tax Remittance</strong></h6>
										</td>
									</tr>
								
									<tr>
										<td><strong>14</strong></td>
										<td colspan="3">
											<p>Amount of Remittance</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('amountremittance')
														->setId('amountremittance')
														->setClass('text-right amount')
														->setValue('')
														->setValidation('decimal')
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
								
									<tr>
										<td><strong>15</strong></td>
										<td colspan="3">
											<p>Less : Amount Remitted from Previously Filed Form, if this is an amended form</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('amountremitted')
														->setId('amountremitted')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>16</strong></td>
										<td colspan="3">
											<p>Net Amount of Remittance <em><small>(Item 14 less Item 15)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('netamount')
														->setId('netamount')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>17</strong></td>
										<td><p>Add: Penalties</p></td>
										<td colspan="2">
											<p><strong>17A</strong> Surcharge</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('surcharge')
														->setId('surcharge')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>17B</strong> Interest</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('interest')
														->setId('interest')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>17C</strong> Compromise</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('compromise')
														->setId('compromise')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>17D</strong> Total Penalties <em><small>(Sum of Items 17A to 17C)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('penalties')
														->setId('penalties')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>18</strong></td>
										<td colspan="3">
											<p><strong>Total Amount of Remittance</strong> <em><small>(Sum of Items 16 and 17D)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('amountdue')
														->setId('amountdue')
														->setClass('text-right')
														->setValue('')
														->setPlaceholder('0.00')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
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

	$('#birForm #monthfilter').on('change',function(){
		ajax.month 		= $(this).val();
		getList();		
	});

	$('#birForm #yearfilter').on('change',function(){
		ajax.year = this.value;
		getList();	
	});

	$('body').on('ifChecked','.quarter', function() {
		ajax.quarter = this.value;
		getList();
	});

	$('body').on('blur', '[data-validation~="decimal"]', function(e) {
		compute();
	});

	function getList() {
		if (ajax_call != '') {
			ajax_call.abort();
		}
		ajax.month = $('#monthfilter').val();
		ajax.year = $('#yearfilter').val();
		ajax_call = $.post("<?=MODULE_URL?>ajax/getMonthYear/<?=$bir_form?>", ajax, function(data) {
			if(data.result == null){
				$('#birForm #amountremittance').val(addCommas('0.00'));
			}else{
				$('#birForm #amountremittance').val(addCommas(data.result));
			}
			$('#birForm #duedate').val(data.duedate);
			compute();
		});
	}
	getList();

	function compute(){
		var totalwithheld 		= 0;
		var firstremittance 	= 0;
		var secondremittance 	= 0;

		var previouslyfiled 	= 0;
		var overremittance 		= 0;
		var totalremittance 	= 0;

		var taxdue 		= 0;
		var surcharge 	= 0;
		var interest 	= 0;
		var compromise 	= 0;
		var penalties 	= 0;

		var amountdue 	= 0;

		amountremittance 	= $('#amountremittance').val() || '0';
		amountremittance 	= amountremittance.replace(/,/g,'');
		amountremitted 		= $('#amountremitted').val() || '0';
		amountremitted 		= amountremitted.replace(/,/g,'');
		// netamount 			= $('#netamount').val() || '0';
		// netamount 			= netamount.replace(/,/g,'');
		
		/**
		 * Compute Total Remittances Made
		 */
		var netamount	= (parseFloat(amountremittance) - parseFloat(amountremitted));
		$('#netamount').val(addCommas(netamount.toFixed(2)));

		surcharge 	= $('#surcharge').val() || '0';
		surcharge 	= surcharge.replace(/,/g,'');
		interest 	= $('#interest').val() || '0';
		interest 	= interest.replace(/,/g,'');
		compromise 	= $('#compromise').val() || '0';
		compromise 	= compromise.replace(/,/g,'');
		penalties 	= $('#penalties').val() || '0';
		penalties 	= penalties.replace(/,/g,'');
		
		
		/**
		 * Compute Total Amount Still Due
		 */
		var totalpenalties			= parseFloat(surcharge) + parseFloat(interest) + parseFloat(compromise);
		$('#penalties').val(addCommas(totalpenalties.toFixed(2)));
		var amountdue				= parseFloat(totalpenalties) + parseFloat(netamount);
		$('#amountdue').val(addCommas(amountdue.toFixed(2)));

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