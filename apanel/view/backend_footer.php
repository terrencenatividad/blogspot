				<script src="<?= BASE_URL ?>assets/js/site.js"></script>
				<section class="footer-padding"></section>
			</div>
			<!-- Vendor Modal -->
			<div class="modal fade" id="vendor_modal" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							Add a Vendor
						</div>
						<div class="modal-body">
							<form class="form-horizontal" id="newVendor" autocomplete="off">
								<input class = "form_iput" value = "newVendor" name = "h_form" id = "h_form" type="hidden">
								<div class="alert alert-warning alert-dismissable hidden" id="customerAlert">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<p>&nbsp;</p>
								</div>
								<div class = "well well-md">
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('text')
													->setLabel('Vendor Code: <span class="asterisk"> * </span>')
													->setSplit('col-md-3', 'col-md-8 field_col')
													->setName('partnercode')
													->setId('partnercode')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('text')
													->setLabel('Company Name: <span class="asterisk"> * </span>')
													->setSplit('col-md-3', 'col-md-8 field_col')
													->setName('partnername')
													->setId('partnername')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<div class="col-md-12">
											<div class="form-group">
												<label class="col-md-4">Contact Person:</label>
											</div>
										</div>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
												->setLabel('First Name')
												->setSplit('col-md-3', 'col-md-8 field_col')
												->setName('first_name')
												->setId('first_name')
												->setValidation('required')
												->setValue("")
												->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('text')
												->setLabel('Last Name')
												->setSplit('col-md-3', 'col-md-8 field_col')
												->setName('last_name')
												->setId('last_name')
												->setValidation('required')
												->setValue("")
												->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('Email:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('email')
													->setId('email')
													->setAttribute(array("maxlength" => "150"))
													->setPlaceHolder("email@oojeema.com")
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('textarea')
													->setLabel('Address: <span class = "asterisk">*</span>')
													->setSplit('col-md-3', 'col-md-8 field_col')
													->setName('address1')
													->setId('address1')
													->setAttribute(array("rows" => "1"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Business Type: <span class="asterisk"> * </span>')
												->setPlaceholder('Filter Business Type')
												->setSplit('col-md-3', 'col-md-8 field_col')
												->setName('businesstype')
												->setId('businesstype')
												->setList($business_type_list)
												->setValidation('required')
												->setValue("")
												->draw($show_input);

										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('TIN:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('tinno')
													->setId('tinno')
													->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
													->setPlaceholder('000-000-000-000')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('Terms:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('terms')
													->setId('terms')
													->setAttribute(array("maxlength" => "5"))
													->setValue("30")
													->draw($show_input);
										?>
									</div>
								</div>
								<div class="modal-footer">
									<div class="row row-dense">
										<div class="col-md-12 col-sm-12 col-xs-12 text-center">
											<div class="btn-group">
												<button type="button" class="btn btn-info btn-flat" id="vendorBtnSave">Save</button>
											</div>
												&nbsp;&nbsp;&nbsp;
											<div class="btn-group">
												<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- End Vendor Modal -->

			<!-- Customer Modal -->
			<div class="modal fade" id="customer_modal" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							Add a Customer
						</div>
						<div class="modal-body">
							<form class="form-horizontal" id="newCustomer" autocomplete="off">
								<input class = "form_iput" value = "newCustomer" name = "h_form" id = "h_form" type="hidden">
								<div class="alert alert-warning alert-dismissable hidden" id="customerAlert">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<p>&nbsp;</p>
								</div>
								<div class = "well well-md">
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('text')
													->setLabel('Customer Code: <span class="asterisk"> * </span>')
													->setSplit('col-md-3', 'col-md-8 field_col')
													->setName('partnercode')
													->setId('partnercode')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('text')
													->setLabel('Company Name: <span class="asterisk"> * </span>')
													->setSplit('col-md-3', 'col-md-8 field_col')
													->setName('partnername')
													->setId('partnername')
													->setAttribute(array("maxlength" => "20"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<div class="col-md-12">
											<div class="form-group">
												<label class="col-md-4">Contact Person:</label>
											</div>
										</div>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
												->setLabel('First Name <span class="asterisk"> * </span>')
												->setSplit('col-md-3', 'col-md-8')
												->setName('first_name')
												->setId('first_name')
												->setValue("")
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin <span class='asterisk'> * </span>">
										<?php
											echo $ui->formField('text')
												->setLabel('Last Name <span class="asterisk"> * </span>')
												->setSplit('col-md-3', 'col-md-8 field_col')
												->setName('last_name')
												->setId('last_name')
												->setValue("")
												->setValidation('required')
												->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('Email:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('email')
													->setId('email')
													->setAttribute(array("maxlength" => "150"))
													->setPlaceHolder("email@oojeema.com")
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('textarea')
													->setLabel('Address: <span class = "asterisk">*</span>')
													->setSplit('col-md-3', 'col-md-8')
													->setName('address1')
													->setId('address1')
													->setAttribute(array("rows" => "1"))
													->setValidation('required')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense remove-margin">
										<?php
											echo $ui->formField('dropdown')
												->setLabel('Business Type: <span class="asterisk"> * </span>')
												->setPlaceholder('Filter Business Type')
												->setSplit('col-md-3', 'col-md-8')
												->setName('businesstype')
												->setId('businesstype')
												->setList($business_type_list)
												->setValidation('required')
												->setValue("")
												->draw($show_input);

										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('TIN:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('tinno')
													->setId('tinno')
													->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
													->setPlaceholder('000-000-000-000')
													->setValue("")
													->draw($show_input);
										?>
									</div>
									<div class="row row-dense">
										<?php
											echo $ui->formField('text')
													->setLabel('Terms:')
													->setSplit('col-md-3', 'col-md-8')
													->setName('terms')
													->setId('terms')
													->setAttribute(array("maxlength" => "5"))
													->setValue("30")
													->draw($show_input);
										?>
									</div>
								</div>
								<div class="modal-footer">
									<div class="row row-dense">
										<div class="col-md-12 col-sm-12 col-xs-12 text-center">
											<div class="btn-group">
												<button type="button" class="btn btn-info btn-flat" id="customerBtnSave">Save</button>
											</div>
												&nbsp;&nbsp;&nbsp;
											<div class="btn-group">
												<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- End Customer Modal -->
			<div id="warning_modal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title modal-danger"><span class="glyphicon glyphicon-warning-sign"></span> Oops!</h4>
						</div>
						<div class="modal-body">
							<p id = "warning_message"></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Ok</button>
						</div>
					</div>
				</div>
			</div>
			<div id="delete_modal" class="modal modal-danger">
				<div class="modal-dialog" style = "width: 300px;">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Confirmation</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to delete this record?</p>
						</div>
						<div class="modal-footer text-center">
							<button type="button" id="delete_yes" class="btn btn-outline btn-flat" onclick="">Yes</button>
							<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
			<div id="confimation_modal" class="modal">
				<div class="modal-dialog" style = "width: 300px;">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Confirmation</h4>
						</div>
						<div class="modal-body">
							<p id="confimation_question">Are you sure you want to delete this record?</p>
						</div>
						<div class="modal-footer text-center">
							<button type="button" id="confirmation_yes" class="btn btn-primary btn-flat" onclick="">Yes</button>
							<button type="button" id="confirmation_no" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>Version</b> 2.0.1
				</div>
				<strong>Copyright &copy; 2017 <a href="http://cid-systems.com">Cid Systems</a>.</strong> All rights reserved.
			</footer>
			<aside class="control-sidebar control-sidebar-dark">
				<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
					<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
					<li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane" id="control-sidebar-home-tab">
						<h3 class="control-sidebar-heading">Recent Activity</h3>
						<ul class="control-sidebar-menu">
							<li>
								<a href="javascript:void(0)">
									<i class="menu-icon fa fa-birthday-cake bg-red"></i>
									<div class="menu-info">
										<h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
										<p>Will be 23 on April 24th</p>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<i class="menu-icon fa fa-user bg-yellow"></i>
									<div class="menu-info">
										<h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
										<p>New phone +1(800)555-1234</p>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<i class="menu-icon fa fa-envelope-o bg-light-blue"></i>
									<div class="menu-info">
										<h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>
										<p>nora@example.com</p>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<i class="menu-icon fa fa-file-code-o bg-green"></i>
									<div class="menu-info">
										<h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
										<p>Execution time 5 seconds</p>
									</div>
								</a>
							</li>
						</ul>
						<h3 class="control-sidebar-heading">Tasks Progress</h3>
						<ul class="control-sidebar-menu">
							<li>
								<a href="javascript:void(0)">
									<h4 class="control-sidebar-subheading">
										Custom Template Design
										<span class="label label-danger pull-right">70%</span>
									</h4>
									<div class="progress progress-xxs">
										<div class="progress-bar progress-bar-danger" style="width: 70%"></div>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<h4 class="control-sidebar-subheading">
										Update Resume
										<span class="label label-success pull-right">95%</span>
									</h4>
									<div class="progress progress-xxs">
										<div class="progress-bar progress-bar-success" style="width: 95%"></div>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<h4 class="control-sidebar-subheading">
										Laravel Integration
										<span class="label label-warning pull-right">50%</span>
									</h4>
									<div class="progress progress-xxs">
										div class="progress-bar progress-bar-warning" style="width: 50%"></div>
									</div>
								</a>
							</li>
							<li>
								<a href="javascript:void(0)">
									<h4 class="control-sidebar-subheading">
										Back End Framework
										<span class="label label-primary pull-right">68%</span>
									</h4>
									<div class="progress progress-xxs">
										<div class="progress-bar progress-bar-primary" style="width: 68%"></div>
									</div>
								</a>
							</li>
						</ul>
					</div>
					<div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
					<div class="tab-pane" id="control-sidebar-settings-tab">
						<form method="post">
							<h3 class="control-sidebar-heading">General Settings</h3>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Report panel usage
									<input type="checkbox" class="pull-right" checked>
								</label>
								<p>
									Some information about this general settings option
								</p>
							</div>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Allow mail redirect
									<input type="checkbox" class="pull-right" checked>
								</label>
								<p>
									Other sets of options are available
								</p>
							</div>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Expose author name in posts
									<input type="checkbox" class="pull-right" checked>
								</label>
								<p>
									Allow the user to show his name in blog posts
								</p>
							</div>
							<h3 class="control-sidebar-heading">Chat Settings</h3>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Show me as online
									<input type="checkbox" class="pull-right" checked>
								</label>
							</div>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Turn off notifications
									<input type="checkbox" class="pull-right">
								</label>
							</div>
							<div class="form-group">
								<label class="control-sidebar-subheading">
									Delete chat history
									<a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
								</label>
							</div>
						</form>
					</div>
				</div>
			</aside>
			<div class="control-sidebar-bg"></div>
		</div>
	</body>
</html>