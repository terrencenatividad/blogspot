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
									<h3><strong>Quarterly Percentage Tax Return</strong></h3>
									<!-- <h4><strong>of Creditable Income Taxes Withheld (Expanded)</strong></h4> -->
								</div>
							</div>
							
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr>
										<td class="col-md-3">
											<p><strong>1</strong> For the</p>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('calendar_fiscal')
															->setValue('')
															->setDefault(1)
															->draw(true);
												?>
												Calendar
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('calendar_fiscal')
															->setValue('')
															->setDefault(2)
															->draw(true);
												?>
												Fiscal
											</label>
											
										</td>

										<td class="col-md-4">
											<p><strong>3</strong> Quarter</p>
										</td>
										<td class="col-md-3">
											<p><strong>4</strong> Amended Return</p>
										</td>
										<td class="col-md-2">
											<p><strong>5</strong> No. of Sheet/s Attached</p>
										</td>
									</tr>
									<tr>
										<td>
										<p><strong>2</strong> Year Ended (MM/YYYY)</p>
											<?php
												echo $ui->formField('dropdown')
														->setSplit('col-md-4','col-md-5')
														->setName('month')
														->setId('month')
														->setList($months)
														->setValue($month)
														->draw(true);
											?>
											<?php
												echo $ui->formField('dropdown')
														->setSplit('col-md-4','col-md-6')
														->setName('year')
														->setId('year')
														->setList($years)
														->setValue($year)
														->draw(true);
											?>
										</td>
										<td>
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
															->setClass('quarter')
															->setValue($quarter)
															->setDefault(1)
															->draw(true);
												?>
												1st
											</label>
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
															->setClass('quarter')
															->setValue($quarter)
															->setDefault(2)
															->draw(true);
												?>
												2nd
											</label>
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
															->setClass('quarter')
															->setValue($quarter)
															->setDefault(3)
															->draw(true);
												?>
												3rd
											</label>
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
															->setClass('quarter')
															->setValue($quarter)
															->setDefault(4)
															->draw(true);
												?>
												4th
											</label>
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
										<td colspan="12" class="text-center">
											<h6><strong>Part I - Background Information</strong></h6>
										</td>
									</tr>
									<tr>
										<td class="col-md-2">
											<p><strong>6</strong> Taxpayer Indentification Number (TIN)</p>
										</td>
										<td colspan="5">
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
										<td class="col-md-2">
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
										<!-- <td class="col-md-1">
											<p><strong>8</strong> Line of Business/Occupation</p>
										</td>
										<td class="col-md-3">
											<?php
												// echo $ui->formField('text')
												// 		->setName('businessline')
												// 		->setId('businessline')
												// 		->setValue($businessline)
												// 		->setAttribute(
												// 			array(
												// 				'readOnly' => 'readOnly'
												// 			)
												// 		)
												// 		->draw(true);
											?>
										</td> -->
									</tr>
									<tr>
										<td colspan="8">
											<p><strong>8</strong> Taxpayer's Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</small></p>
										</td>
									</tr>
									<tr>
										<td colspan="8">
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
										<td colspan="7">
											<p><strong>9</strong> Registered Address </p>
										</td>
										<td></td>
									</tr>
									<tr>
										<td colspan="7">
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
											<td>
											<p><strong>9A</strong> Zip Code </p>
											<?php
												echo $ui->formField('text')
														->setName('zipcode')
														->setId('zipcode')
														->setValue($zipcode)
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
										<td colspan="2">
											<p><strong>10</strong> Contact Number </p>
										</td>
										<td colspan="7">
											<p><strong>11</strong> Email Address </p>
										</td>
										</tr>
										<tr>
										<td colspan="2">
									
												<?php
												echo $ui->formField('text')
														->setName('mobile')
														->setId('mobile')
														->setValue($mobile)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td colspan="7">
											<?php
												echo $ui->formField('text')
														->setName('email')
														->setId('email')
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
									<tr>
										<td colspan="2" class="col-md-3">
										<p><strong>12</strong> Are you availing of tax relief under Special Law or International Tax Treaty?</p>
										</td>
										<td class="col-md-2">
										<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('taxrelief')
															->setValue("yes")
															->setDefault("yes")
															->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('taxrelief')
															->setValue("no")
															->setDefault("no")
															->draw(true);
												?>
												No
											</label>
										</td>
										<td colspan="1" class="col-md-2">
											<strong>12A</strong> If yes, specify
											
										</td>
										<td colspan="4">
										<?php
												echo $ui->formField('text')
														->setName('specify')
														->setId('specify')
														->setValue('')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td colspan="8">
										<strong>13</strong> Only for individual taxpayers whose sales/receipts are subject to Percentage Tax under Section 116 of the Tax Code, as amended:
												What income tax rates are you availing? (choose one)
										</td>
									</tr>
									<tr>
										<td class="col-md-1">
										(To be filled out only on the initial quarter of the taxable year)
										</td>
										<td colspan="7">
										<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('incometax')
															->setValue("yes")
															->setDefault("yes")
															->draw(true);
												?>
												Graduated income tax rate on net taxable income
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('incometax')
															->setValue("no")
															->setDefault("no")
															->draw(true);
												?>
												8% income tax rate on gross sales/receipts/others
											</label>
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
										<td colspan="12" class="text-center">
											<h6><strong>Part II – Computation of Tax</strong></h6>
										</td>
									</tr>
							</table>
							</div>

								<div class="table">
								<table class="table table-bordered table-hover" id="table">
									<tr>
										<td><strong>14&nbsp;&nbsp;</strong></td>
										<td colspan="7" class="col-md-7">
											<p>Total Tax Due</p>
										</td>
										<!-- <td><strong>14</strong></td> -->
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('tax_due')
														->setId('tax_due')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong></strong></td>
										<td colspan="12"><p>Less: Tax Credits/Payments</p></td>
									</tr>
									<tr>
										<td></td>
										<td colspan="7"><strong>15</strong>&nbsp;
										Creditable Percentage Tax Withheld Per BIR FORM No. 2307
										</td>
										
										<!-- <td><strong>15</strong> -->
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('creditablepercentage')
														->setId('creditablepercentage')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setValidation('decimal')
														->setAttribute(array("onBlur" => "computeTaxPayments();"))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td></td>
										<td colspan="7"><strong>16</strong>&nbsp;
										Tax Paid in Return Previously Filed, if this is an Amended Return
										</td>
										
										<!-- <td><strong>20B</strong> -->
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('taxpaid')
														->setId('taxpaid')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setValidation('decimal')
														->setAttribute(array("onBlur" => "computeTaxPayments();"))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td></td>
										<td colspan="7"><strong>17</strong>&nbsp;
										Other Tax Credit/Payment <small>(specify)</small>
										<input type="text" name="other" id="other">
										</td>
										
										<!-- <td><strong>20B</strong> -->
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('othertax')
														->setId('othertax')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setValidation('decimal')
														->setAttribute(array("onBlur" => "computeTaxPayments();"))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>18</strong></td>
										<td colspan="7">
											<p>Total Tax Credits/Payments (Sum of Items 15 & 17)</p>
										</td>
										<!-- <td><strong>21</strong></td> -->
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('totaltax')
														->setId('totaltax')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>19</strong></td>
										<td colspan="7">
											<p>Tax Payable (Overpayment) (Item 14 less Item 18)</p>
										</td>
										<!-- <td><strong>22</strong></td> -->
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('totalpayable')
														->setId('totalpayable')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong></strong></td>
										<td colspan="2">
											<p>Add Penalties</p>
										</td>
									</tr>
									<tr>
										<td><strong></strong></td>
										<td colspan="7"><p><strong>20 </strong>Surcharge</p>
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('surcharge')
														->setId('surcharge')
														->setClass('text-right')
														->setValue("")														
														->setPlaceholder('0.00')
														->setAttribute(array("onBlur" => "computePenalties();"))
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong></strong></td>
										<td colspan="7">
										<p><strong>21 </strong>Interest</p>
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('interest')
														->setId('interest')
														->setClass('text-right')
														->setValue("")																				
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->setAttribute(array("onBlur" => "computePenalties();"))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong></strong></td>
										<td colspan="7">
										<p><strong>22 </strong>Compromise</p>
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('compromise')
														->setId('compromise')
														->setClass('text-right')
														->setValue("")														
														->setPlaceholder('0.00')
														->setValidation('decimal')
														->setAttribute(array("onBlur" => "computePenalties();"))
														->draw(true);
											?>
										</td>
									</tr>
										<td><strong>23</strong></td>
										<td colspan="7">
										<p>Total Penalties (Sum of Items 20 to 22)</p>
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('penalties')
														->setId('penalties')
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue("")														
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>24</strong></td>
										<td colspan="7">
											<p>Total Amount Payable(Overpayment)(Sum of Items 19 and 23)</p>
											<div class="col-md-4">If overpayment, mark one box only:</div>
											
											<div class="col-md-3">
											<label class="col-md-12">
												<?php
													echo $ui->setElement('radio')
															->setName('remittance')
															->setValue("")
															->setDefault("refunded")
															->draw(true);
												?>
												To be refunded
											</label>
											</div>
											<div class="col-md-5">
											<label class="col-md-12">
												<?php
													echo $ui->setElement('radio')
															->setName('remittance')
															->setValue("")
															->setDefault("taxcredit")
															->draw(true);
												?>
												To be issued a Tax Certificate
											</label>
											</div>
										</td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('totalamountpayable')
														->setId('totalamountpayable')
														->setClass('text-right')
														->setValue("")
														->setPlaceholder('0.00')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
								</table>
							</div>
						
						
					<div class="row">
						<div class="col-md-9">
							<p class="text-info">
								<!-- <em>Note : Make sure not to leave this page until you are satisfied with the generated PDF as data input on this page are not saved.</em> -->
							</p>
						</div>
						<div class="col-md-3 text-right">
							<!-- <button type="button" class="btn btn-primary" id="generate">Generate</button> -->
						</div>
					</div>
				</div>
			<!-- </form> -->
		</div>
	</div>
	<div class="box-body">
			<!-- <form method="post" id="birForm"> -->
				<div class="col-md-10 col-md-offset-1">
					<p class="text-info">
						<!-- <em>Note : Make sure not to leave this page until you are satisfied with the generated PDF as data input on this page are not saved.</em> -->
					</p>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 text-center">
									<h3><strong>Quarterly Percentage Tax Return</strong></h3>
									<!-- <h4><strong>of Creditable Income Taxes Withheld (Expanded)</strong></h4> -->
								</div>
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
										<td class="col-md-4">
											<p><strong></strong> TIN</p>
										</td>
										<td class="col-md-8">
											<p><strong></strong> Taxpayer's Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</p>
										</td>
									</tr>
									<tr>
									<td class="col-md-4">
											<?php
												echo $ui->formField('text')
														->setName('tin1')
														->setId('tin1')
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
										<td class="col-md-8">
											<?php
												echo $ui->formField('text')
														->setName('agentname1')
														->setId('agentname1')
														->setValue($agentname1)
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
										<td colspan="12" class="text-center">
											<h6><strong>Schedule I – Computation of Tax</strong> (Attach additional sheets, if necessary)</h6>
										</td>
									</tr>
									<tr>
										<td>
										</td>
										<td class="col-md-2 text-center">
											Alphanumeric Tax Code (ATC)
										</td>
										<td></td>
										<td class="col-md-2 text-center">
											Taxable Amount
										</td>
										<td></td>
										<td class="col-md-1 text-center">
											Tax Rate
										</td>
										<td></td>
										<td class="col-md-5 text-center">
											Tax Due
										</td>
									</tr>
									<?php
									$line = 1;
									for ($i=0; $i < 6; $i++):
									?>
									<tr>
										<td>
											<strong><?php echo $line;?></strong>
										</td>
										<td>
											<?php
												echo $ui->formField('dropdown')
												->setName('atc'.$i)
												->setId('atc'.$i)
												->setClass('atc')
												->setList($atc_list)
												->setNone(' ')
												->setValue('')
												->draw(true);
											?>
										</td>
										<td></td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxamount'.$i)
														->setId('taxamount'.$i)
														->setClass('text-right taxamount')
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
										<td></td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxrate'.$i)
														->setId('taxrate'.$i)
														->setClass('text-right')
														->setValue('')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td><p style="margin-top: 4px;">%</p></td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxdue'.$i)
														->setId('taxdue'.$i)
														->setClass('text-right tax_due')
														->setValue('')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									<?php
									$line++;
									endfor;
									?>
									<tr>
										<td><strong>7 </strong></td>
										<td colspan="6"><strong>Total Tax Due</strong> (Sum of Items 1 to 6)(To Part II Item 14)</td>
										
										<td>
										<?php
												echo $ui->formField('text')
														->setName('totaltaxdue'.$i)
														->setId('totaltaxdue'.$i)
														->setClass('text-right')
														->setValue('')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->setValidation('decimal')
														->draw(true);
											?>
										</td>
									</tr>
									</table>
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

$('.atc').on('change', function(e){
	var atc_code 		= 	$(this).val();
	var id 		= 	$(this).attr("id");
	var row 	=	id.replace(/[a-z]/g, '');
	var quarter = 	$('input[name=quarter]:checked').val()

var $radios = $('input[name=quarter]').change(function () {
    var value = $radios.filter(':checked').val();
    alert(value);
});

	$.post('<?=MODULE_URL?>ajax/get_atc_details',"atc_code="+atc_code+"&quarter="+quarter, function(data) 
	{	
		$('#taxrate'+row).val(data.tax_rate*100);
		$('#taxamount'+row).val(data.taxamount);
		$('#taxdue'+row).val(data.taxamount * data.tax_rate);
		taxdue();
	});
});

function taxdue() {
	var sum = 0;
	$('.tax_due').each(function() {
		var balyu = $(this).val();
		sum += +balyu;
		$('#tax_due').val(sum);
		$('#totalpayable').val(sum);
		$('#totalamountpayable').val(sum);
		$('#totaltaxdue6').val(sum);	
		formatNumber('tax_due');	
		formatNumber('totaltax');	
		formatNumber('totalpayable');	
		formatNumber('penalties');	
		formatNumber('totalamountpayable');	
	});
}


function computeTaxPayments(){
		var taxdue			= 0;
		var creditable 		= 0;
		var taxpaid 		= 0;
		var othertax 		= 0;
		var totaltax 		= 0;
		var totalpayable 	= 0;

		taxdue 		= $('#tax_due').val() || '0';
		taxdue 		= taxdue.replace(/,/g,'');
		creditable 	= $('#creditablepercentage').val() || '0';
		creditable 	= creditable.replace(/,/g,'');
		taxpaid 	= $('#taxpaid').val() || '0';
		taxpaid 	= taxpaid.replace(/,/g,'');
		othertax 	= $('#othertax').val() || '0';
		othertax 	= othertax.replace(/,/g,'');
		totaltax 	= $('#totaltax').val() || '0';
		totaltax 	= totaltax.replace(/,/g,'');
		totalpayable 	= $('#totaltax').val() || '0';
		totalpayable 	= totaltax.replace(/,/g,'');

		var totaltax				= parseFloat(creditable) + parseFloat(taxpaid) + parseFloat(othertax);
		$('#totaltax').val(totaltax);
		
		var totalpayable			= parseFloat(taxdue) - parseFloat(totaltax);
		$('#totalpayable').val(totalpayable);

		computePenalties();
}

function computePenalties()
	{

		var surcharge 	 = 0;
		var interest 	 = 0;
		var compromise 	 = 0;
		var penalties 	 = 0;
		var totalpayable = 0;
		var total		 = 0;

		surcharge 	= $('#surcharge').val() || '0';
		surcharge 	= surcharge.replace(/,/g,'');
		interest 	= $('#interest').val() || '0';
		interest 	= interest.replace(/,/g,'');
		compromise 	= $('#compromise').val() || '0';
		compromise 	= compromise.replace(/,/g,'');
		penalties 	= $('#penalties').val() || '0';
		penalties 	= penalties.replace(/,/g,'');
		totalpayable= $('#totalpayable').val() || '0';
		totalpayable= totalpayable.replace(/,/g,'');
		total 		= $('#totalamountpayable').val() || '0';
		total 		= total.replace(/,/g,'');
	
		var penalties				= parseFloat(surcharge) + parseFloat(interest) + parseFloat(compromise);
		$('#penalties').val(penalties);
		
		var total				= parseFloat(totalpayable) + parseFloat(penalties);
		$('#totalamountpayable').val(total);
		

	}

	/**FORMAT NUMBER**/
	function formatNumber(id){
		var amount = document.getElementById(id).value;
		if(amount != ''){
			amount     = amount.replace(/\,/g,'');
			var result = amount * 1;
			document.getElementById(id).value = addCommas(result.toFixed(2));
		}
	}

		/**ADD COMMAS**/
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