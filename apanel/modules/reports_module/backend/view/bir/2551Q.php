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
															->setName('quarter')
															->setValue($quarter)
															->setDefault(1)
															->draw(true);
												?>
												Calendar
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('quarter')
															->setValue($quarter)
															->setDefault(1)
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
														->draw();
											?>
											<?php
												echo $ui->formField('dropdown')
														->setSplit('col-md-4','col-md-6')
														->setName('month')
														->setId('month')
														->setList($years)
														->setValue($year)
														->draw();
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
										<td class="col-md-1">
											<p><strong>6</strong> Taxpayer Indentification Number (TIN)</p>
										</td>
										<td colspan="1">
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
										<td class="col-md-1">
											<p><strong>8</strong> Line of Business/Occupation</p>
										</td>
										<td class="col-md-3">
											<?php
												echo $ui->formField('text')
														->setName('businessline')
														->setId('businessline')
														->setValue($businesstype)
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
											<p><strong>9</strong> Taxpayer's Name <small>(Last Name, First Name, Middle Name for Individual OR Registered Name for Non-Individual)</small></p>
										</td>
										<td colspan="5">
										<p><strong>10</strong> Telephone Number</p>
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
										<td colspan="2">
											<?php
												echo $ui->formField('text')
														->setName('telephone')
														->setId('telephone')
														->setValue('')
														->setPlaceholder('0000000')
														->setMaxLength(7)														
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<p><strong>11</strong> Registered Address </p>
										</td>
										<td colspan="2">
											<p><strong>12</strong> Zip Code </p>
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
										<td colspan="2">
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
										<p><strong>13</strong> Are you availing of tax relief under Special Law or International Tax Treaty?</p>
										</td>
										<td class="col-md-2">
										<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('tax_yes')
															->setValue("yes")
															->setDefault("yes")
															->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
													echo $ui->setElement('radio')
															->setName('tax_yes')
															->setValue("yes")
															->setDefault("no")
															->draw(true);
												?>
												No
											</label>
										</td>
										<td colspan="1">
											If yes, specify
											
										</td>
										<td colspan="2">
										<?php
												echo $ui->formField('text')
														->setName('specify')
														->setId('specify')
														->setValue('')
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
											<h6><strong>Part II – Computation of Tax</strong></h6>
										</td>
									</tr>
									<tr>
										<td>
										</td>
										<td class="col-md-2 text-center">
											Taxable Transaction/Industry Classification
										</td>
										<td></td>
										<td class="col-md-2 text-center">
											ATC
										</td>
										<td></td>
										<td class="col-md-3 text-center">
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
									$line = 14;
									for ($i=0; $i < 5; $i++):
									?>
									<tr>
										<td>
											<strong><?php echo $line.'A';?></strong>
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
											<strong><?php echo $line.'B';?></strong>
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
											<strong><?php echo $line.'C';?></strong>
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
											<strong><?php echo $line.'D';?></strong>
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
										<td>
											<strong><?php echo $line.'E';?></strong>
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
										<td colspan="7">
											<p>Total Tax Due</p>
										</td>
										<td><strong>19</strong></td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
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
									<tr>
										<td><strong>20</strong></td>
										<td colspan="12"><p>Less: Tax Credits/Payments</p></td>
									</tr>
									<tr>
										<td></td>
										<td colspan="7"><strong>20A</strong>&nbsp;
										Creditable Percentage Tax Withheld Per BIR FORM No. 2307
										</td>
										
										<td><strong>20A</strong>
										</td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
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
									<tr>
										<td></td>
										<td colspan="7"><strong>20B</strong>&nbsp;
										Tax Paid in Return Previously Filed, if this is an Amended Return
										</td>
										
										<td><strong>20B</strong>
										</td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue(number_format(1000+$line,2))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>21</strong></td>
										<td colspan="7">
											<p>Total Tax Credits/Payments (Sum of Items 20A & 20B)</p>
										</td>
										<td><strong>21</strong></td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue(number_format(1000+$line,2))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>22</strong></td>
										<td colspan="7">
											<p>Tax Payable (Overpayment) (Item 19 less Item 21)</p>
										</td>
										<td><strong>22</strong></td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue(number_format(1000+$line,2))
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>23</strong></td>
										<td colspan="2">
											<p>Add Penalties</p>
										<p align="center">Surcharge</p>
										</td>
										<td colspan="2">
											<p>&nbsp;</p>
										<p align="center">Interest</p>
										</td>
										<td colspan="2">
											<p>&nbsp;</p>
										<p align="center">Compromise</p>
										</td>
										<td colspan="4">
										</td>
									</tr>
									<tr>
										<td></td>
										<td colspan="2"><strong>23A</strong>&nbsp;
										<?php
												echo $ui->formField('text')
														->setName('surcharge')
														->setClass('text-right')
														->setValue(number_format('2200.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
										<td colspan="2"><strong>23B</strong>&nbsp;
										<?php
												echo $ui->formField('text')
														->setName('interest')
														->setClass('text-right')
														->setValue(number_format('2200.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
										<td colspan="2"><strong>23C</strong>&nbsp;
										<?php
												echo $ui->formField('text')
														->setName('penalties')
														->setClass('text-right')
														->setValue(number_format('2500.50',2))
														->setPlaceholder('0.00')
														->draw(true);
										?>
										</td>
										<td></td>
										<td><strong>23D</strong>&nbsp;
										</td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('taxdue')
														->setClass('text-right')
														->setValue(number_format('2200.50',2))
														->setPlaceholder('0.00')
														->draw(true);
											?>
										</td>
									</tr>
									<tr>
										<td><strong>24</strong></td>
										<td colspan="7">
											<p>Total Amount Payable(Overpayment)(Sum of Items 22 and 23D)</p>
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
															->setDefault("refunded")
															->draw(true);
												?>
												To be issued a Tax Certificate
											</label>
											</div>
										</td>
										<td><strong>24</strong></td>
										<td>
										<?php
												echo $ui->formField('text')
														->setName('tax_due'.$i)
														->setClass('text-right')
														->setPlaceholder('0.00')
														->setValue(number_format(1000+$line,2))
														->draw(true);
											?>
										</td>
									</tr>
								</table>
							</div>
							<div class="table">
								<table class="table table-bordered table-hover">
									<tr>
										<td colspan="12">
											<p><small>I declare, under the penalties of perjury, that this return has been made in good faith, verified by me, and to the best of my knowledge, and belief,
														is true and correct, pursuant to the provisions of the National Internal Revenue Code, as amended, and the regulations issued under authority thereof. </small></p>
										</td>
									</tr>
									
									<tr>
									<td></td>
										<td colspan="5">
											<div class="col-md-12">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-1">
															<strong>25</strong>
														</div>
															<div class="col-md-8"><p>&nbsp;</p>
																<small><p align="center" style="border-top: 1px solid black;">Signature over Printed Name of Taxpayer/ <br>
																Taxpayer Authorized Representative</p></small>
															</div>
													</div>
													<div class="row">
														<div class="col-md-1">
															<p></p>
														</div>
														<div class="col-md-8"><p>&nbsp;</p>
															<small><p align="center" style="border-top: 1px solid black;">TIN of Tax Agent (if applicable)</p></small>
														</div>
													</div>
												</div>
											</div>
										</td>
										<td colspan="6">
										<div class="col-md-12">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-1">
															<strong>26</strong>
														</div>
														<div class="col-md-10"><p>&nbsp;</p>
															<small><p align="center" style="border-top: 1px solid black;">
															Title/Position of Signatory</p></small>
														</div>
													</div>
													<div class="row">
														<div class="col-md-1">
															<p></p>
														</div>
														<div class="col-md-10"><p>&nbsp;</p>
															<small><p align="center" style="border-top: 1px solid black;">Tax Agent Accreditation No. (if applicable)</p></small>
														</div>
													</div>
												</div>
											</div>
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
</script>
