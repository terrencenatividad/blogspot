
				<section class="footer-padding"></section>
			</div>
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
			<div id="success_modal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
						</div>
						<div class="modal-body">
							<p id = "message"></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
						</div>
					</div>
				</div>
			</div>
			<div id="invalid_characters" class="modal fade" role="dialog">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Invalid Characters</h4>
						</div>
						<div class="modal-body">
							<p><b>Allowed Characters:</b> a-z A-Z 0-9 - _</p>
							<p>Letters, Numbers, Dash, and Underscore</p>
							<p><b>Note:</b> Space is an Invalid Character</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
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
			<div id="cancel_modal" class="modal modal-warning">
				<div class="modal-dialog" style = "width: 300px;">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Confirmation</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to cancel this record?</p>
						</div>
						<div class="modal-footer text-center">
							<button type="button" id="cancel_yes" class="btn btn-outline btn-flat" onclick="">Yes</button>
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
			<div class="modal" id="locked_popup_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title text-center">System is Locked for the Moment</h4>
						</div>
						<div class="modal-body">
							<p class="text-red text-center">Locked Time: <span id="locktime"></span></p>
						</div>
					</div>
				</div>
			</div>
			<div class="modal" id="login_popup_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="form-group has-feedback">
							<input type="text" id="login_form_username" name="login_form_username" class="form-control" placeholder="Username" value="<?php echo USERNAME ?>" readonly>
							<span class="glyphicon glyphicon-user form-control-feedback"></span>
						</div>
						<div class="form-group has-feedback">
							<input type="password" id="login_form_password" name="login_form_password" class="form-control" placeholder="Password">
							<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<button type="button" id="login_form_button" class="btn btn-primary btn-block btn-flat">Sign In</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>Version</b> 0.9.1
				</div>
				<?php
					$startyear		= 2017;
					$currentyear	= '';
					if ($startyear < date('Y')) {
						$currentyear = ' - ' . date('Y');
					}
				?>
				<strong>Copyright &copy; <?=$startyear?><?=$currentyear?> <a href="http://cid-systems.com">Cid Systems</a>.</strong> All rights reserved.
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
		<div id="monthly_datefilter"></div>
		<script src="<?= BASE_URL ?>assets/js/site.js"></script>
	</body>
</html>