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
								<table class="table table-bordered table-hover tableList">
									<tr>
										<td class="col-md-2">
											<p><strong>1</strong> For the year (MM / YYYY)</p>
										</td>
										<td class="col-md-2">
											<p><strong>2</strong> Amended Return?</p>
										</td>
										<td class="col-md-2">
											<p><strong>3</strong> Number of sheets</p>
										</td>
									</tr>
									<tr>
										<td>
											<?php
											echo $ui->formField('dropdown')
											->setName('monthfilter')
											->setId('monthfilter')
											->setSplit('col-md-5', 'col-md-6')
											->setList($months)
											->setValue($months)
											->setValidation('required')
											->draw(true);
											?>
											<?php
											echo $ui->formField('dropdown')
											->setName('yearfilter')
											->setId('yearfilter')
											->setSplit('col-md-5', 'col-md-6')
											->setList($years)
											->setValue($year)
											->setValidation('required')
											->draw(true);
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
										<td class="col-md-2">
											<?php
											echo $ui->formField('text')
											->setSplit('','col-md-10')
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
										<td class = "col-md-1">
											<p><strong>4</strong> TIN</p>
										</td>
										<td class="col-md-1">
											<p><strong>5</strong> RDO Code</p>
										</td>
										<td class="col-md-1">
											<p><strong>6</strong> Line of Business</p>
										</td>
									</tr>
									<tr>
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
										<td class="col-md-3">
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
										<td class="col-md-4">
											<?php
											echo $ui->formField('text')
											->setName('line')
											->setId('line')
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
										<td colspan="3">
											<p><strong>7</strong> Taxpayer's Name (For Individual)Last Name, First Name, Middle Name/(For Non-individual) Registered Name</small></p>
										</td>
										<td colspan="2">
											<p><strong>8</strong> Telephone Number</small></p>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<?php
											echo $ui->formField('text')
											->setName('contact')
											->setId('contact')
											->setValue($contact)
											->setMaxLength(11)
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
											<p><strong>9</strong> Registered Address</p>
										</td>
										<td colspan="2">
											<p><strong>10</strong> ZIP Code</p>
										</td>
									</tr>
									<tr>
										<td colspan="3">
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
									</tr>
								</table>
								<div class="row">
									<div class="col-md-4">
										<p><strong>11</strong> Are you availing of tax relief under Special Law or International Tax Treaty?</p>
									</div>
									<div class="col-md-2">
										<label class="col-md-2">
											<?php
											echo $ui->setElement('radio')
											->setName('tax_relief')
											->setValue("yes")
											->setDefault("yes")
											->draw(true);
											?>
											Yes
										</label>
										<label class="col-md-2">
											<?php
											echo $ui->setElement('radio')
											->setName('tax_relief')
											->setValue("yes")
											->setDefault("no")
											->draw(true);
											?>
											No
										</label>
									</div>
									<div class = "col-md-1">
										<p>If yes, specify</p>
									</div>
									<div class="col-md-5">
										<?php
										echo $ui->formField('text')
										->setName('specify')
										->setId('specify')
										->draw(true);
										?>
									</div>
								</tr>
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
									<td colspan="5" class="text-center">
										<h6><strong>Part II – Computation of Tax</strong></h6>
									</td>
								</tr>
								<tr>
									<td>
									</td>
									<td class="col-md-2 text-center">
									</td>
									<td>
									</td>
									<td class="col-md-2 text-center">
									</td>
									<td class="col-md-4 text-center">
										Sales Receipts for the Quarter (Exclusive of VAT)
									</td>
									<td class="col-md-5 text-center">
										Output Tax Due for the Quarter
									</td>
								</tr>
								<tr>
									<td><strong>12</strong></td>
									<td colspan="2">
										<p>Vatable Sales/Receipt-Private (Sch.1)</p>
									</td>
									<td class = "text-right"><b>15A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setId('vat_privateA')
										->setName('vat_privateA')
										->setClass('text-right')
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
										->setId('vat_privateB')
										->setName('vat_privateB')
										->setClass('text-right')
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
									<td><strong>13</strong></td>
									<td colspan="2">
										<p>Sale to Government</p>
									</td>
									<td class = "text-right"><b>16A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setId('vat_govA')
										->setName('vat_govA')
										->setClass('text-right')
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
										->setId('vat_govB')
										->setName('vat_govB')
										->setClass('text-right')
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
									<td><strong>14</strong></td>
									<td colspan="2">
										<p>Zero Rated Sales/Receipts</p>
									</td>
									<td class = "text-right"><b>17</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setId('vat_zero')
										->setName('vat_zero')
										->setClass('text-right')
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
									<td><strong>15</strong></td>
									<td colspan="2">
										<p>Exempt Sales/Receipts</p>
									</td>
									<td class = "text-right"><b>18</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setId('vat_exempt')
										->setName('vat_exempt')
										->setClass('text-right')
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
									<td><strong>16</strong></td>
									<td colspan="2">
										<p>Total Sales/Receipts and Output Tax Due</p>
									</td>
									<td class = "text-right"><b>19A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setId('totalsales19A')
										->setName('totalsales19A')
										->setClass('text-right')
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
										->setId('totalsales19B')
										->setName('totalsales19B')
										->setClass('text-right')
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
							<table class = "table table-bordered makezero">
								<tr>
									<td><strong>17</strong></td>
									<td colspan= "2">
										<p>Less: Allowable Input Tax</p>
									</td>
								</tr>
								<tr>
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17A</strong> Input Tax Carried Over from Previous Quarter</p>
									</td>
									<td class = "text-right"><b>17A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('carriedover20A')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17B</strong> Input Tax Deferred on Capital Goods Exceeding P1Million from Previous Quarter</p>
									</td>
									<td class = "text-right"><b>17B</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('deferred20B')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17C</strong> Transitional Input Tax</p>
									</td>
									<td class = "text-right"><b>17C</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('transitionalinputtax20C')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17D</strong> Presumptive Input Tax</p>
									</td>
									<td class = "text-right"><b>17D</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('presumptiveinputtax26D')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17E</strong> Others</p>
									</td>
									<td class = "text-right"><b>17E</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('others20E')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>17F</strong> Total (Sum of Item 17A, 17B, 17C, 17D & 17E)</p>
									</td>
									<td class = "text-right"><b>17F</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totalsum20F')
										->setClass('text-right')
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
									<td><strong>18</strong></td>
									<td colspan= "2">
										<p>Current Transactions</p>
									</td>
								</tr>
								<tr>
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18A/B</strong> Purchase of Capital Goods not exceeding P1Million <small>(see sch.2)</small></p>
									</td>
									<td class = "text-right"><b>18A/B</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('cgnotexceed21A')
										->setClass('text-right')
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
										->setName('cgnotexceed21B')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18C/D</strong> Purchase of Capital Goods exceeding P1Million <small>(see sch.2)</small></p>
									</td>
									<td class = "text-right"><b>18C/D</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('cgexceed21C')
										->setClass('text-right')
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
										->setName('cgexceed21D')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18E/F</strong> Domestic Purchases of Goods <small>other than capital goods</small></p>
									</td>
									<td class = "text-right"><b>18E/F</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('dompurchase21E')
										->setClass('text-right')
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
										->setName('dompurchase21F')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18G/H</strong> Importation of Goods Other than Capital Goods</p>
									</td>
									<td class = "text-right"><b>18G/H</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('importation21G')
										->setClass('text-right')
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
										->setName('importation21H')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18I/J</strong> Domestic Purchases of Services</p>
									</td>
									<td class = "text-right"><b>18I/J</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('dompurchaseserv21I')
										->setClass('text-right')
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
										->setName('dompurchaseserv21J')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18K/L</strong> Services rendered by Non-residents</p>
									</td>
									<td class = "text-right"><b>18K/L</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('servicerenderedK')
										->setClass('text-right')
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
										->setName('servicerenderedL')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18M</strong> Purchases Not Qualified for Input Tax</p>
									</td>
									<td class = "text-right"><b>18M</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('purchasenotqualified21M')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18N/O</strong> Others</p>
									</td>
									<td class = "text-right"><b>18N/O</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('others21N')
										->setClass('text-right')
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
										->setName('others2O')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "2">
										<p><strong>18P</strong> Total Current Purchases <small>(Sum of item 18A,18C,18E,18G,18I,18K,18M&18N)</small></p>
									</td>
									<td class = "text-right"><b>18P</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totalpurchases21P')
										->setClass('text-right')
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
									<td><strong>19</strong></td>
									<td colspan= "3">
										<p>Total Available Input Tax (Sum of Item 20F, 21B, 21D, 21F, 21H, 21J, 21L,&21O)</p>
									</td>
									<td class = "text-right"><b>19</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('total22')
										->setClass('text-right')
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
									<td colspan= "2">
										<p>Less: Deductions from Input Tax</p>
									</td>
								</tr>
								<tr>
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20A</strong> Input Tax on Purchases of Capital Goods exceeding P1Million deferred for succeeding period (Sch.3)</p>
									</td>
									<td class = "text-right"><b>23A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totalavailableinputtax23A')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20B</strong> Input Tax on Sale to Govt. closed to expense (Sch.4)</p>
									</td>
									<td class = "text-right"><b>20B</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totalavailableinputtax23B')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20C</strong> Input Tax allocable to Exempt Sales (Sch.5)</p>
									</td>
									<td class = "text-right"><b>20C</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('taxallocable23C')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20D</strong> VAT Refund/TCC claimed</p>
									</td>
									<td class = "text-right"><b>20D</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('vatrefund23D')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20E</strong> Others</p>
									</td>
									<td class = "text-right"><b>20E</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('other23E')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>20F</strong> Total (Sum of Item 20A, 20B,20C,20D & 20E)</p>
									</td>
									<td class = "text-right"><b>20F</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('total23F')
										->setClass('text-right')
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
									<td><strong>21</strong></td>
									<td colspan= "3">
										<p>Total Allowable Input Tax (Item 22 less Item 23F)</p>
									</td>
									<td class = "text-right"><b>21</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totalallowableinputtax24')
										->setClass('text-right')
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
									<td><strong>22</strong></td>
									<td colspan= "3">
										<p>Net VAT Payable (Item 19B less Item 24)</p>
									</td>
									<td class = "text-right"><b>22</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('netpayable25')
										->setClass('text-right')
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
									<td><strong>23</strong></td>
									<td colspan= "2">
										<p>Less: Tax Credits/Payments</p>
									</td>
								</tr>
								<tr>
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23A</strong> Monthly VAT Payments - previous two months</p>
									</td>
									<td class = "text-right"><b>23A</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('monthlyvat26A')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23B</strong> Creditable Value-Added Tax Withheld (Sch. 6)</p>
									</td>
									<td class = "text-right"><b>23B</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('creditablevat26B')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23C</strong> Advance Payments for Sugar and Flour Industries (Sch.7)</p>
									</td>
									<td class = "text-right"><b>23C</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('sugarandflour26C')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23D</strong> Input Tax on Sale to Govt. closed to expense (Sch.4)</p>
									</td>
									<td class = "text-right"><b>23D</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('inputtaxsale26D')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23E</strong> VAT paid in return previously filed, if this is an amended return</p>
									</td>
									<td class = "text-right"><b>23E</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('vatpaid26E')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23F</strong> Advance Payments made (please attach proof of payments - BIR Form No. 0605)</p>
									</td>
									<td class = "text-right"><b>23F</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('advpaymentsmade26F')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23G</strong> Others</p>
									</td>
									<td class = "text-right"><b>23G</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('otherstaxcredits26G')
										->setClass('text-right')
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
									<td colspan= "1"></td>
									<td colspan= "3">
										<p><strong>23H</strong> Total Tax Credits/Payments (Sum of Item 23A,23B,23C,23D,23E, 23F & 23G)</p>
									</td>
									<td class = "text-right"><b>23H</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('totaltaxcredits26H')
										->setClass('text-right')
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
									<td><strong>24</strong></td>
									<td colspan= "3">
										<p>Tax Still Payable/(Overpayment)(Item 22 less Item 23G)</p>
									</td>
									<td class = "text-right"><b>24</b></td>
									<td>
										<?php
										echo $ui->formField('text')
										->setName('taxstillpayable27')
										->setClass('text-right')
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
							<div class="penalties">
								<div class="row">
									<div class="col-md-3"><strong>25</strong> Add Penalties: </div>
									<div class="col-md-2 text-center">Surcharge(25A)</div>
									<div class="col-md-2 text-center">Interest(25B)</div>
									<div class="col-md-2 text-center">Compromise(25C)</div>
									<div class="">&nbsp;</div>
								</div>
								<div class="row">
									<div class="col-md-3"></div>
									<div class="col-md-2">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setPlaceholder('0.00')
										->setClass('text-right')
										->setId('surcharge')
										->setName('surcharge')
										->setMaxLength(5)
										->draw(true);
										?>
									</div>
									<div class="col-md-2">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setPlaceholder('0.00')
										->setClass('text-right')
										->setMaxLength(10)
										->setId('interest')
										->setName('interest')
										->draw(true);
										?>
									</div>
									<div class="col-md-2">
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-md-12')
										->setPlaceholder('0.00')
										->setClass('text-right')
										->setMaxLength(10)
										->setId('compromise')
										->setName('compromise')
										->draw(true);
										?>
									</div>
									<div class="col-md-3">
										<div class="col-md-2"><strong>25D</strong></div>
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-md-10')
										->setPlaceholder('0.00')
										->setClass('text-right')
										->setAttribute(array('readonly' => ''))
										->setId('penalties')
										->setName('penalties')
										->setMaxLength(10)
										->draw(true);
										?>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-4"><strong>26</strong> Total Amount Payable/(Overpayment) (Sum of Item 24 & 25D)</div>
									<div class="col-md-5"></div>
									<div class="col-md-3">
										<div class="col-md-2"><strong>26</strong></div>
										<?php
										echo $ui->formField('text')
										->setSplit('', 'col-md-10')
										->setPlaceholder('0.00')
										->setClass('text-right')
										->setAttribute(array('readonly' => ''))
										->setId('total_payable')
										->setName('total_payable')
										->setMaxLength(10)
										->draw(true);
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 text-center">
									<h5>
									I declare, under the penalties of perjury, that this return has been made in good faith, verified by me, and to the best of my knowledge, and belief, is true and correct, pursuant to the provisions of the National Internal Revenue Code, as amended, and the regulations issued under authority thereof.</h5>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-5">
									<div class="col-md-1"><strong>27</strong></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setId('agentname')
									->setName('agentname')
									->setValue($agentname)
									->setMaxLength(50)
									->draw(true);
									?>
								</div>
								<div class="col-md-1"></div>
								<div class="col-md-5">
									<div class="col-md-1"><strong>28</strong></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-8')
									->setMaxLength(30)
									->setId('signature')
									->setName('signature')
									->draw(true);
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-3 text-center">
									President/Vice President/Principal Officer/Accredited Tax Agent/Authorized Representative/Taxpayer.
									(Signature Over Printed Name)
								</div>
								<div class="col-md-3"></div>
								<div class="col-md-2 text-center">
									Treasurer/Assistant Treasurer
									(Signature Over Printed Name)
								</div>
								<div class="col-md-2"></div>
							</div>
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-3">
									<div class="col-md-1"></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setMaxLength(20)
									->setId('position1')
									->setName('position1')
									->draw(true);
									?>
								</div>
								<div class="col-md-3">
									<div class="col-md-1"></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setPlaceholder('000-000-000-000')
									->setId('tin_signatory1')
									->setName('tin_signatory1')
									->setMaxLength(15)
									->setValue($tin)
									->draw(true);
									?>
								</div>
								<div class="col-md-4">
									<div class="col-md-2"></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setMaxLength(20)
									->setId('position2')
									->setName('position2')
									->draw(true);
									?>
								</div>
								<div class="col-md-1"></div>
							</div>
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-3 text-center">
									Title/Position of Signatory
								</div>
								<div class="col-md-3 text-center">
									Tin of Signatory
								</div>
								<div class="col-md-3 text-center">
									Title/Position of Signatory
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setMaxLength(30)
									->setId('taxagent')
									->setName('taxagent')
									->draw(true);
									?>
								</div>
								<div class="col-md-2">
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setPlaceholder('MM/DD/YYYY')
									->setMaxLength(10)
									->setId('dateissuance')
									->setName('dateissuance')
									->draw(true);
									?>
								</div>
								<div class="col-md-2">
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setPlaceholder('MM/DD/YYYY')
									->setMaxLength(10)
									->setId('expiry')
									->setName('expiry')
									->draw(true);
									?>
								</div>
								<div class="col-md-4">
									<div class="col-md-2"></div>
									<?php
									echo $ui->formField('text')
									->setSplit('', 'col-md-10')
									->setPlaceholder('000-000-000-000')
									->setMaxLength(15)
									->setId('tin_signatory2')
									->setName('tin_signatory2')
									->draw(true);
									?>
								</div>
								<div class="col-md-1"></div>
							</div>
							<div class="row">
								<div class="col-md-3 text-center">
									Tax Agent Acc#/Atty's Roll #. (if applicable)
								</div>
								<div class="col-md-2 text-center">
									Date of Issue
								</div>
								<div class="col-md-2 text-center">
									Date of Expiry
								</div>
								<div class="col-md-1"></div>
								<div class="col-md-3 text-center">
									TIN of Signatory
								</div>
							</div>
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

	$(document).ready(function() {
		$('#vat_privateA').val('0.00');
		$('#vat_privateB').val('0.00');
		$('#vat_govA').val('0.00');
		$('#vat_govB').val('0.00');
		$('#vat_zero').val('0.00');
		$('#vat_exempt').val('0.00');
		$('#totalsales19A').val('0.00');
		$('#totalsales19B').val('0.00');
		makeZero();
	});

	$('#monthfilter').on('change', function() {
		var period = $(this).val();
		makeZero();
		
		$.post("<?=MODULE_URL?>ajax/getPrivate", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_privateA').val('0.00');
				$('#vat_privateB').val('0.00');
			} else {
				$('#vat_privateA').val(sum);
				$('#vat_privateB').val(taxamount);
			}
			sumSales();
		});

		$.post("<?=MODULE_URL?>ajax/getGov", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_govA').val('0.00');
				$('#vat_govB').val('0.00');
			} else {
				$('#vat_govA').val(sum);
				$('#vat_govB').val(taxamount);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getGov", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_zero').val('0.00');
			} else {
				$('#vat_zero').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getGov", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_exempt').val('0.00');
			} else {
				$('#vat_exempt').val(sum);
			}
		});
	});

	function sumSales() {
		var privateA = Math.round($('#vat_privateA').val());
		var privateB = Math.round($('#vat_privateB').val());
		var govA = Math.round($('#vat_govA').val());
		var govB = Math.round($('#vat_govB').val());
		var exempt = Math.round($('#vat_exempt').val());
		var zero = Math.round($('#vat_zero').val());
		var sumA = privateA + govA + exempt + zero;
		var sumB =  privateB + govB;
		$('#totalsales19A').val(sumA.toFixed(2));
		$('#totalsales19B').val(sumB.toFixed(2));
	}

	function makeZero() {
		$('.makezero tbody tr td').find('input[type=text]').val('0.00');
		$('.penalties .row').find('input[type=text]').val('0.00');
	}
</script>
