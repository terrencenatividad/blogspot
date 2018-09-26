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
										<td class="col-md-4">
											<p><strong>2</strong> Quarter</p>
										</td>
										<td class="col-md-2">
											<p><strong>3</strong> Return Period (MM/DD/YYYY)</p>
										</td>
										<td class="col-md-2">
											<p><strong>4</strong> Amended Return?</p>
										</td>
										<td class="col-md-2">
											<p><strong>5</strong> Short period</p>
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
											<label class="col-md-3">
												<?php
												echo $ui->setElement('radio')
												->setName('quarter')
												->setValue($quarter)
												->setClass('quarter')
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
												->setClass('quarter')
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
												->setClass('quarter')
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
												->setClass('quarter')
												->setDefault(4)
												->draw(true);
												?>
												4th
											</label>
										</td>
										<td>
											<?php
											echo $ui->formField('text')
											->setLabel('From')
											->setName('from')
											->setId('from')
											->setClass('datepicker-input')
											->setAttribute(array('readonly' => ''))
											->setAddon('calendar')
											->draw();
											?>
											<?php
											echo $ui->formField('text')
											->setLabel('To')
											->setName('to')
											->setId('to')
											->setClass('datepicker-input')
											->setAttribute(array('readonly' => ''))
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
												->setName('shortperiod')
												->setValue("yes")
												->setDefault("yes")
												->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
												echo $ui->setElement('radio')
												->setName('shortperiod')
												->setValue("yes")
												->setDefault("no")
												->draw(true);
												?>
												No
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
										<td colspan="5" class="text-center">
											<h6><strong>Part I - Background Information</strong></h6>
										</td>
									</tr>
									<tr>
										<td class = "col-md-1">
											<p><strong>6</strong> TIN</p>
										</td>
										<td class="col-md-1">
											<p><strong>7</strong> RDO Code</p>
										</td>
										<td class="col-md-1">
											<p><strong>8</strong> No. of sheets attached</p>
										</td>
										<td class="col-md-1">
											<p><strong>9</strong> Line of Business</p>
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
											<p><strong>10</strong> Taxpayer's Name (For Individual)Last Name, First Name, Middle Name/(For Non-individual) Registered Name</small></p>
										</td>
										<td colspan="2">
											<p><strong>11</strong> Telephone Number</small></p>
										</td>
									</tr>
									<tr>
										<td colspan="3">
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
											<p><strong>12</strong> Registered Address <small>(Indicate complete address. If branch, indicate the branch address. If the registered address is different from the current address, go to the RDO to update registered address by using BIR Form No. 1905)</small></p>
										</td>
										<td colspan="2">
											<p><strong>13</strong> ZIP Code</p>
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
									<tr>
										<td class="col-md-4">
											<p><strong>14</strong> Are you availing of tax relief under Special Law or International Tax Treaty?</p>
										</td>
										<td class="col-md-2">
											<label class="col-md-6">
												<?php
												echo $ui->setElement('radio')
												->setName('tax_relief')
												->setValue("yes")
												->setDefault("yes")
												->draw(true);
												?>
												Yes
											</label>
											<label class="col-md-6">
												<?php
												echo $ui->setElement('radio')
												->setName('tax_relief')
												->setValue("yes")
												->setDefault("no")
												->draw(true);
												?>
												No
											</label>
										</td>
										<td class = "col-md-1">
											<p><strong>11</strong> If yes, specify</p>
										</td>
										<td class="col-md-5">
											<?php
											echo $ui->formField('text')
											->setName('specify')
											->setId('specify')
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
										<td><strong>15</strong></td>
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
										<td><strong>16</strong></td>
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
										<td><strong>17</strong></td>
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
										<td><strong>18</strong></td>
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
										<td><strong>19</strong></td>
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
										<td><strong>20</strong></td>
										<td colspan= "2">
											<p>Less: Allowable Input Tax</p>
										</td>
									</tr>
									<tr>
										<td colspan= "1"></td>
										<td colspan= "3">
											<p><strong>20A</strong> Input Tax Carried Over from Previous Quarter</p>
										</td>
										<td class = "text-right"><b>20A</b></td>
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
											<p><strong>20B</strong> Input Tax Deferred on Capital Goods Exceeding P1Million from Previous Quarter</p>
										</td>
										<td class = "text-right"><b>20B</b></td>
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
											<p><strong>20C</strong> Transitional Input Tax</p>
										</td>
										<td class = "text-right"><b>20C</b></td>
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
											<p><strong>20D</strong> Presumptive Input Tax</p>
										</td>
										<td class = "text-right"><b>20D</b></td>
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
											<p><strong>20E</strong> Others</p>
										</td>
										<td class = "text-right"><b>20E</b></td>
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
											<p><strong>20F</strong> Total (Sum of Item 20A, 20B, 20C, 20D & 20E)</p>
										</td>
										<td class = "text-right"><b>20F</b></td>
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
										<td><strong>21</strong></td>
										<td colspan= "2">
											<p>Current Transactions</p>
										</td>
									</tr>
									<tr>
										<td colspan= "1"></td>
										<td colspan= "2">
											<p><strong>21A/B</strong> Purchase of Capital Goods not exceeding P1Million <small>(see sch.2)</small></p>
										</td>
										<td class = "text-right"><b>21A/B</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('cgnotexceed21A')
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
											->setId('cgnotexceed21B')
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
											<p><strong>21C/D</strong> Purchase of Capital Goods exceeding P1Million <small>(see sch.2)</small></p>
										</td>
										<td class = "text-right"><b>21C/D</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('cgexceed21C')
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
											->setId('cgexceed21D')
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
											<p><strong>21E/F</strong> Domestic Purchases of Goods <small>other than capital goods</small></p>
										</td>
										<td class = "text-right"><b>21E/F</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('dompurchase21E')
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
											->setId('dompurchase21F')
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
											<p><strong>21G/H</strong> Importation of Goods Other than Capital Goods</p>
										</td>
										<td class = "text-right"><b>21G/H</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('importation21G')
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
											->setId('importation21H')
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
											<p><strong>21I/J</strong> Domestic Purchases of Services</p>
										</td>
										<td class = "text-right"><b>21I/J</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('dompurchaseserv21I')
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
											->setId('dompurchaseserv21J')
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
											<p><strong>21K/L</strong> Services rendered by Non-residents</p>
										</td>
										<td class = "text-right"><b>21K/L</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('servicerenderedK')
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
											->setId('servicerenderedL')
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
											<p><strong>21M</strong> Purchases Not Qualified for Input Tax</p>
										</td>
										<td class = "text-right"><b>21M</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('purchasenotqualified21M')
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
											<p><strong>21N/O</strong> Others</p>
										</td>
										<td class = "text-right"><b>21N/O</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('others21N')
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
											->setId('others21O')
											->setName('others21O')
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
											<p><strong>21P</strong> Total Current Purchases <small>(Sum of item 21A,21C,21E,21G,21I,21K,21M&21N)</small></p>
										</td>
										<td class = "text-right"><b>21P</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('totalpurchases21P')
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
										<td><strong>22</strong></td>
										<td colspan= "3">
											<p>Total Available Input Tax (Sum of Item 20F, 21B, 21D, 21F, 21H, 21J, 21L,&21O)</p>
										</td>
										<td class = "text-right"><b>22</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setId('total22')
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
										<td><strong>23</strong></td>
										<td colspan= "2">
											<p>Less: Deductions from Input Tax</p>
										</td>
									</tr>
									<tr>
										<td colspan= "1"></td>
										<td colspan= "3">
											<p><strong>23A</strong> Input Tax on Purchases of Capital Goods exceeding P1Million deferred for succeeding period (Sch.3)</p>
										</td>
										<td class = "text-right"><b>23A</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('totalavailableinputtax23A')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>23B</strong> Input Tax on Sale to Govt. closed to expense (Sch.4)</p>
										</td>
										<td class = "text-right"><b>23B</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('totalavailableinputtax23B')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>23C</strong> Input Tax allocable to Exempt Sales (Sch.5)</p>
										</td>
										<td class = "text-right"><b>23C</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('taxallocable23C')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>23D</strong> VAT Refund/TCC claimed</p>
										</td>
										<td class = "text-right"><b>23D</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('vatrefund23D')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>23E</strong> Others</p>
										</td>
										<td class = "text-right"><b>23E</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('other23E')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>23F</strong> Total (Sum of Item 23A, 23B,23C,23D & 23E)</p>
										</td>
										<td class = "text-right"><b>23F</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('total23F')
											->setClass('text-right')
											->setValue('0.00')
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
											<p>Total Allowable Input Tax (Item 22 less Item 23F)</p>
										</td>
										<td class = "text-right"><b>24</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('totalallowableinputtax24')
											->setClass('text-right')
											->setValue('0.00')
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
										<td colspan= "3">
											<p>Net VAT Payable (Item 19B less Item 24)</p>
										</td>
										<td class = "text-right"><b>25</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('netpayable25')
											->setClass('text-right')
											->setValue('0.00')
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
										<td><strong>26</strong></td>
										<td colspan= "2">
											<p>Less: Tax Credits/Payments</p>
										</td>
									</tr>
									<tr>
										<td colspan= "1"></td>
										<td colspan= "3">
											<p><strong>26A</strong> Monthly VAT Payments - previous two months</p>
										</td>
										<td class = "text-right"><b>26A</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('monthlyvat26A')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26B</strong> Creditable Value-Added Tax Withheld (Sch. 6)</p>
										</td>
										<td class = "text-right"><b>26B</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('creditablevat26B')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26C</strong> Advance Payments for Sugar and Flour Industries (Sch.7)</p>
										</td>
										<td class = "text-right"><b>26C</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('sugarandflour26C')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26D</strong> Input Tax on Sale to Govt. closed to expense (Sch.4)</p>
										</td>
										<td class = "text-right"><b>26D</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('inputtaxsale26D')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26E</strong> VAT paid in return previously filed, if this is an amended return</p>
										</td>
										<td class = "text-right"><b>26E</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('vatpaid26E')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26F</strong> Advance Payments made (please attach proof of payments - BIR Form No. 0605)</p>
										</td>
										<td class = "text-right"><b>26F</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('advpaymentsmade26F')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26G</strong> Others</p>
										</td>
										<td class = "text-right"><b>26G</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('otherstaxcredits26G')
											->setClass('text-right')
											->setValue('0.00')
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
											<p><strong>26H</strong> Total Tax Credits/Payments (Sum of Item 26A,26B,26C,26D,26E, 26F & 26G)</p>
										</td>
										<td class = "text-right"><b>26H</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('totaltaxcredits26H')
											->setClass('text-right')
											->setValue('0.00')
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
										<td><strong>27</strong></td>
										<td colspan= "3">
											<p>Tax Still Payable/(Overpayment)(Item 25 less Item 26H)</p>
										</td>
										<td class = "text-right"><b>27</b></td>
										<td>
											<?php
											echo $ui->formField('text')
											->setName('taxstillpayable27')
											->setClass('text-right')
											->setValue('0.00')
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
										<div class="col-md-3"><strong>29</strong> Add Penalties: </div>
										<div class="col-md-2 text-center">Surcharge(28A)</div>
										<div class="col-md-2 text-center">Interest(28B)</div>
										<div class="col-md-2 text-center">Compromise(28C)</div>
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
											->setMaxLength(15)
											->draw(true);
											?>
										</div>
										<div class="col-md-2">
											<?php
											echo $ui->formField('text')
											->setSplit('', 'col-md-12')
											->setPlaceholder('0.00')
											->setClass('text-right')
											->setMaxLength(15)
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
											->setMaxLength(15)
											->setId('compromise')
											->setName('compromise')
											->draw(true);
											?>
										</div>
										<div class="col-md-3">
											<div class="col-md-2"><strong>28D</strong></div>
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
										<div class="col-md-4"><strong>28</strong> Total Amount Payable/(Overpayment) (Sum of Item 27 & 28D)</div>
										<div class="col-md-5"></div>
										<div class="col-md-3">
											<div class="col-md-2"><strong>29</strong></div>
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
										<div class="col-md-1"><strong>30</strong></div>
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
										<div class="col-md-1"><strong>31</strong></div>
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
		$('#others21N').val('0.00');
		$('#others21O').val('0.00');
		makeZero();
		allowableInputTax20F();
		totalpurchases21P();
		var period = 0;
		$('.tableList tbody tr td .quarter').each(function() {
			if($(this).is(':checked')) {
				period = $(this).val();
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

				$.post("<?=MODULE_URL?>ajax/getZero", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#vat_exempt').val('0.00');
					} else {
						$('#vat_exempt').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getExempt", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#vat_zero').val('0.00');
					} else {
						$('#vat_zero').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getNotPurchasesExceeded", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#cgnotexceed21A').val('0.00');
						$('#cgnotexceed21B').val('0.00');
					} else {
						$('#cgnotexceed21A').val(sum);
						$('#cgnotexceed21B').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchasesExceeded", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#cgexceed21C').val('0.00');
						$('#cgexceed21D').val('0.00');
					} else {
						$('#cgexceed21C').val(sum);
						$('#cgexceed21D').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchaseGoods", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#dompurchase21E').val('0.00');
						$('#dompurchase21F').val('0.00');
					} else {
						$('#dompurchase21E').val(sum);
						$('#dompurchase21F').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchaseImport", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#importation21G').val('0.00');
						$('#importation21H').val('0.00');
					} else {
						$('#importation21G').val(sum);
						$('#importation21H').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchaseServices", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#dompurchaseserv21I').val('0.00');
						$('#dompurchaseserv21J').val('0.00');
					} else {
						$('#dompurchaseserv21I').val(sum);
						$('#dompurchaseserv21J').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchaseNonResident", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#servicerenderedK').val('0.00');
						$('#servicerenderedL').val('0.00');
					} else {
						$('#servicerenderedK').val(sum);
						$('#servicerenderedL').val(sum);
					}
				});

				$.post("<?=MODULE_URL?>ajax/getPurchaseNotTax", { period : period }, function(data) {
					var sum = data.sum_amount;
					var taxamount = data.sum_taxamount;
					if(sum == null || taxamount == null) {
						$('#purchasenotqualified21M').val('0.00');
					} else {
						$('#purchasenotqualified21M').val(sum);
					}
				});
			}
		});
});

$('.tableList').on('ifToggled', '.quarter', function() {
	var period = 0;
	makeZero();
	allowableInputTax20F();
	totalpurchases21P();
	if($(this).is(':checked')) {
		period = $(this).val();
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

		$.post("<?=MODULE_URL?>ajax/getZero", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_zero').val('0.00');
			} else {
				$('#vat_zero').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getExempt", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#vat_exempt').val('0.00');
			} else {
				$('#vat_exempt').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getNotPurchasesExceeded", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#cgnotexceed21A').val('0.00');
				$('#cgnotexceed21B').val('0.00');
			} else {
				$('#cgnotexceed21A').val(sum);
				$('#cgnotexceed21B').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchasesExceeded", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#cgexceed21C').val('0.00');
				$('#cgexceed21D').val('0.00');
			} else {
				$('#cgexceed21C').val(sum);
				$('#cgexceed21D').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchaseGoods", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#dompurchase21E').val('0.00');
				$('#dompurchase21F').val('0.00');
			} else {
				$('#dompurchase21E').val(sum);
				$('#dompurchase21F').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchaseImport", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#importation21G').val('0.00');
				$('#importation21H').val('0.00');
			} else {
				$('#importation21G').val(sum);
				$('#importation21H').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchaseServices", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#dompurchaseserv21I').val('0.00');
				$('#dompurchaseserv21J').val('0.00');
			} else {
				$('#dompurchaseserv21I').val(sum);
				$('#dompurchaseserv21J').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchaseNonResident", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#servicerenderedK').val('0.00');
				$('#servicerenderedL').val('0.00');
			} else {
				$('#servicerenderedK').val(sum);
				$('#servicerenderedL').val(sum);
			}
		});

		$.post("<?=MODULE_URL?>ajax/getPurchaseNotTax", { period : period }, function(data) {
			var sum = data.sum_amount;
			var taxamount = data.sum_taxamount;
			if(sum == null || taxamount == null) {
				$('#purchasenotqualified21M').val('0.00');
			} else {
				$('#purchasenotqualified21M').val(sum);
			}
		});
	}
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

function allowableInputTax20F() {
	var allowableInputTax20A = Math.round($('#vat_privateA').val());
	var allowableInputTax20B = Math.round($('#vat_privateB').val());
	var allowableInputTax20C = Math.round($('#vat_govA').val());
	var allowableInputTax20D = Math.round($('#vat_govB').val());
	var allowableInputTax20E = Math.round($('#vat_exempt').val());
	var zero = Math.round($('#vat_zero').val());
	var allowableInputTax20F = allowableInputTax20A + allowableInputTax20B + allowableInputTax20C + allowableInputTax20D + allowableInputTax20E;
	$('#allowableInputTax20F').val(allowableInputTax20F.toFixed(2));
}

function totalpurchases21P() {
	var cgnotexceed21A = Math.round($('#cgnotexceed21A').val());
	var cgnotexceed21B = Math.round($('#cgnotexceed21B').val());
	var cgexceed21C = Math.round($('#cgexceed21C').val());
	var cgexceed21D = Math.round($('#cgexceed21D').val());
	var dompurchase21E = Math.round($('#dompurchase21E').val());
	var dompurchase21F = Math.round($('#dompurchase21F').val());
	var importation21G = Math.round($('#importation21G').val());
	var importation21H = Math.round($('#importation21H').val());
	var dompurchaseserv21I = Math.round($('#dompurchaseserv21I').val());
	var dompurchaseserv21J = Math.round($('#dompurchaseserv21J').val());
	var servicerenderedK = Math.round($('#servicerenderedK').val());
	var servicerenderedL = Math.round($('#servicerenderedL').val());
	var purchasenotqualified21M = Math.round($('#purchasenotqualified21M').val());
	var others21O = Math.round($('#others21O').val());
	var totalpurchases21P = cgnotexceed21A + cgnotexceed21B + cgexceed21C + cgexceed21D + dompurchase21E + dompurchase21F + importation21G + importation21H + dompurchaseserv21I + dompurchaseserv21J + servicerenderedK + servicerenderedL + purchasenotqualified21M;
	$('#totalpurchases21P').val(totalpurchases21P.toFixed(2));
	var total22  = totalpurchases21P + cgnotexceed21B + cgexceed21D + dompurchase21F + importation21H + dompurchaseserv21J + servicerenderedL + others21O;
	$('#total22').val(total22.toFixed(2));
}

function makeZero() {
	$('.penalties .row').find('input[type=text]').val('0.00');
}
</script>