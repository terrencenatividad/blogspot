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
									<h3><strong>Quarterly Remittance Return</strong></h3>
									<h4><strong>of Creditable Income Taxes Withheld (Expanded)</strong></h4>
								</div>
							</div>
							
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr>
										<td class="col-md-2">
											<p><strong>1</strong> For the year</p>
										</td>
										<td class="col-md-4">
											<p><strong>2</strong> Quarter</p>
										</td>
										<td class="col-md-2">
											<p><strong>3</strong> Amended Return?</p>
										</td>
										<td class="col-md-2">
											<p><strong>4</strong> Any Taxes Withheld?</p>
										</td>
										<td class="col-md-2">
											<p><strong>5</strong> No. of Sheet/s Attached</p>
										</td>
									</tr>
									<tr>
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
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
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
										<td colspan="5" class="text-center">
											<h6><strong>Part I - Background Information</strong></h6>
										</td>
									</tr>
									<tr>
										<td class="col-md-3">
											<p><strong>6</strong> Taxpayer Indentification Number (TIN)</p>
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
											<p><strong>7</strong> RDO Code</p>
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
											<p><strong>8</strong> Withholding Agent’s Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</small></p>
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
											<p><strong>9</strong> Registered Address <small>(Indicate complete address. If branch, indicate the branch address. If the registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small></p>
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
											<p><strong>9A</strong> ZIP Code</p>
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
											<p><strong>10</strong> Contact Number</p>
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
											<p><strong>11</strong> Cat. of Withholding Agent</p>
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
															->setName('government')
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
											<p><strong>12</strong> Email Address</p>
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
											<h6><strong>Part II – Computation of Tax</strong></h6>
										</td>
									</tr>
									<tr>
										<td>
										</td>
										<td class="col-md-2 text-center">
											ATC
										</td>
										<td class="col-md-4 text-center">
											Tax Base (Consolidated for the Quarter) 
										</td>
										<td class="col-md-1 text-center">
											Tax Rate
										</td>
										<td class="col-md-5 text-center">
											Tax Withheld (Consolidated for the Quarter)
										</td>
									</tr>
									<?php
									$line = 13;
									for ($i=0; $i < 6; $i++):
									?>
									<tr>
										<td>
											<strong><?php echo $line;?></strong>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('atc'.$i)
														->setClass('text-right')
														->setValue('ATC'.$line)
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxbase'.$i)
														->setClass('text-right')
														->setValue(number_format(1000+$line+.55,2))
														->setPlaceholder('0.00')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxrate'.$i)
														->setClass('text-right')
														->setPlaceholder('0%')
														->setValue($line.'%')
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxwithheld'.$i)
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue(number_format(1000+$line,2))
														->setAttribute(
															array(
																'readOnly' => 'readOnly'
															)
														)
														->draw(true);
											?>
										</td>
									</tr>
									<?php
									$line++;
									endfor;
									?>
									<tr>
										<td><strong>19</strong></td>
										<td colspan="3">
											<p>Total Taxes Withheld for the Quarter <small>(Sum of Items 13 to 18)</small></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('totalwithheld')
														->setClass('text-right')
														->setValue(number_format('1500.50',2))
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
									<tr>
										<td><strong>20</strong></td>
										<td><p>Less: Remittances Made :</p></td>
										<td colspan="2">
											<p>1st Month of the Quarter</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('firstremittance')
														->setClass('text-right')
														->setValue(number_format('1600.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>21</strong></td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p>2nd Month of the Quarter</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('secondremittance')
														->setClass('text-right')
														->setValue(number_format('1700.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>22</strong></td>
										<td colspan="3">
											<p>Less : Tax Remitted in Return Previously Filed, if this is an amended return</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('previouslyfiled')
														->setClass('text-right')
														->setValue(number_format('1800.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>23</strong></td>
										<td colspan="3">
											<p>Less : Over-remittance from Previous Quarter of the same taxable year</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('overremittance')
														->setClass('text-right')
														->setValue(number_format('1900.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>24</strong></td>
										<td colspan="3">
											<p>Total Remittances Made <small>(Sum of Items 20 to 23)</small></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('totalremittance')
														->setClass('text-right')
														->setValue(number_format('2000.50',2))
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
									<tr>
										<td><strong>25</strong></td>
										<td colspan="3">
											<p><strong>Tax Still Due</strong>/(Over-remittance) <em><small>(Item 19 Less Item 24)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('taxdue')
														->setClass('text-right')
														->setValue(number_format('2100.50',2))
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
									<tr>
										<td>&nbsp;</td>
										<td><p>Add: Penalties</p></td>
										<td colspan="2">
											<p><strong>26</strong> Surcharge</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('surcharge')
														->setClass('text-right')
														->setValue(number_format('2200.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>27</strong> Interest</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('interest')
														->setClass('text-right')
														->setValue(number_format('2300.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>28</strong> Compromise</p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('compromise')
														->setClass('text-right')
														->setValue(number_format('2400.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2">
											<p><strong>29</strong> Total Penalties <em><small>(Sum of Items 26 to 28)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('penalties')
														->setClass('text-right')
														->setValue(number_format('2500.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>30</strong></td>
										<td colspan="3">
											<p><strong>TOTAL AMOUNT STILL DUE</strong>/(Over-remittance) <em><small>(Sum of Items 25 and 29)</small></em></p>
										</td>
										<td>
											<?php
												echo $ui->formField('text')
														->setName('amountdue')
														->setClass('text-right')
														->setValue(number_format('2600.50',2))
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
									<tr>
										<td>&nbsp;</td>
										<td>
											<p>If over-remittance</p>
										</td>
										<td colspan="3">
											<label class="col-md-3">
												<?php
													echo $ui->setElement('radio')
															->setName('remittance')
															->setValue("")
															->setDefault("refunded")
															->draw(true);
												?>
												To be refunded
											</label>
											<label class="col-md-4">
												<?php
													echo $ui->setElement('radio')
															->setName('remittance')
															->setValue("")
															->setDefault("taxcredit")
															->draw(true);
												?>
												To be issued Tax Credit Certificate
											</label>
											<label class="col-md-5">
												<?php
													echo $ui->setElement('radio')
															->setName('remittance')
															->setValue("")
															->setDefault("carriedover")
															->draw(true);
												?>
												To be carried over to the next quarter within the same calendar year (not applicable for succeeding year)
											</label>
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
</script>
