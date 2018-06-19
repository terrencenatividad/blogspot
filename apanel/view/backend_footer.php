
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

			<!-- two seconds delay modal : Added by Sabriella -->
			<div id="delay_modal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
						</div>
						<div class="modal-body">
							<p>Successfully Saved</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
						</div>
					</div>
				</div>
			</div>
			<!-- End -->

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
			<div class="control-sidebar-bg"></div>
		</div>				
		<div id="monthly_datefilter"></div>
		<script src="<?= BASE_URL ?>assets/js/site.js"></script>
	</body>
</html>