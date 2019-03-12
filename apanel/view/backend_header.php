<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $page_title ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/ionicons.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/select2.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/icheck.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/daterangepicker.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datepicker3.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/skin.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/morris.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/nprogress.css">
<?php foreach ($include_css as $inc_css): ?>
	<link rel="stylesheet" href="<?= BASE_URL . 'assets/css/' . $inc_css ?>">
<?php endforeach ?>
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script src="<?= BASE_URL ?>assets/js/jquery-2.2.3.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/select2.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/icheck.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootbox.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/moment.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/daterangepicker.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap-datepicker.js"></script>
	<script src="<?= BASE_URL ?>assets/js/slimscroll.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/fastclick.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/app.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.inputmask.bundle.js"></script>
	<script src="<?= BASE_URL ?>assets/js/raphael.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/morris.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap-select.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/global.js"></script>
	<script src="<?= BASE_URL ?>assets/js/nprogress.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.form.min.js"></script>
<?php foreach ($include_js as $inc_js): ?>
	<script src="<?= BASE_URL . 'assets/js/' . $inc_js ?>"></script>
<?php endforeach ?>
</head>
<body class="hold-transition skin-red fixed layout-top-nav">
<div class="wrapper">
	<header class="main-header">
		<nav class="navbar navbar-static-top">
			<div id="nprogress_parent"></div>
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="<?php echo BASE_URL ?>" class="navbar-brand navbar-logo">
						<img src="<?php echo BASE_URL ?>assets/images/oojeema_PRIME_white_small.png" height="100%">
					</a>
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
						<i class="fa fa-bars"></i>
					</button>
				</div>
				<p class="navbar-text navbar-text-maintenance marquee"><strong>Oojeema Prime</strong> will be undergoing Maintenance from <strong>12:00pm</strong> to <strong>1:00pm</strong>.  You will not be able to login during this time.  Sorry for the inconvenience.</p>
				<!-- <p class="navbar-text navbar-text-maintenance marquee"><strong>Hey!</strong> Just a friendly reminder. Remember to have your lunch @ exactly <strong>12:00 pm</strong> to <strong>1:00 pm</strong>. :)</p> -->
				<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="hidden-sm">
							<a href="<?php echo BASE_URL ?>">
								<span>Dashboard</span>
							</a>
						</li>
						<?php foreach ($header_nav as $key => $header_nav2): ?>
							<?php foreach ($header_nav2 as $key2 => $header_nav3): ?>
								<?php if (is_array($header_nav3)): ?>
									<?php if (count($header_nav3) > 1): ?>
										<li class="treeview">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">
												<span><?php echo $key2 ?></span>
												 <span class="caret"></span>
											</a>
											<ul class="dropdown-menu">
												<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
													<li>
														<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
															<?php echo $key3 ?>
														</a>
													</li>
												<?php endforeach ?>
											</ul>
										</li>
									<?php else: ?>
										<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
											<li>
												<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
													<span><?php echo $key3 ?></span>
												</a>
											</li>
										<?php endforeach ?>
									<?php endif ?>
								<?php else: ?>
									<li>
										<a href="<?php echo BASE_URL . trim($header_nav3, '%') ?>">
											<span><?php echo $key2 ?></span>
										</a>
									</li>
								<?php endif ?>
							<?php endforeach ?>
						<?php endforeach ?>
					</ul>
				</div>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?= BASE_URL ?>assets/images/user_icon.png" class="user-image" alt="User Image">
								<span class="hidden-xs"><?= NAME ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?= BASE_URL ?>assets/images/user_icon.png" class="img-circle" alt="User Image">
									<p>
										<?= NAME ?>
										<small><?= GROUPNAME ?></small>
									</p>
								</li>
								<li class="user-footer">
									<div class="pull-right">
										<a href="<?=BASE_URL?>logout" class="btn btn-default btn-flat">Sign out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</header>
	<script>
		$('.navbar-static-top [href="<?php echo $header_active ?>"]').parents('li').addClass('active');
	</script>
	<div class="modal" id="locked_popup" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
	<div class="modal" id="no_access_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center">Page Locked</h4>
				</div>
				<div class="modal-body">
					<p class="text-red text-center">Page is currently being access by someone</p>
				</div>
				<div class="modal-footer">
					<a href="<?php echo BASE_URL ?>" class="btn btn-primary" data-toggle="back_page">Close</a>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="login_popup" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Login</h4>
				</div>
				<div class="modal-body">
					<div id="error_box"></div>
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
	</div>
	<script>
		function getLockAccess(task, spec_data) {
			window.loading_indicator = false;
			$.post('<?php echo MODULE_URL ?>ajax/ajax_get_lock_access', { task: task, spec_data: spec_data }, function(data) {
				if (data.no_access_modal === true) {
					$('#no_access_modal').modal('show');
				} else if (data.no_access_modal === false) {
					$('#no_access_modal').modal('hide');
				}
				setTimeout(function() {
					getLockAccess(task, spec_data);
				}, 10000);
			});
		}
		<?php if (isset($no_access_modal) && $no_access_modal): ?>
		$('#no_access_modal').modal('show');
		<?php endif ?>
	</script>
	<?php if (defined('LOCKED')): ?>
	<script>
		$('#locked_popup').modal('show');
		$('#locked_popup #locktime').html('<?php echo LOCKED ?>');
		setTimeout(function() {
			$.post('<?php echo BASE_URL ?>', function() {});
		}, <?php echo LOCKED_SEC ?> * 1000);
	</script>
	<?php endif ?>
	<script>
		$(function() {
			$('#login_form_password').keypress(function(event){
				var keycode = (event.keyCode ? event.keyCode : event.which);
				if(keycode == '13'){
					$('#login_form_button').trigger('click').focus();
				}
			});
			$('#login_form_button').on('click', function() {
				var login_form = $(this).closest('.modal');
				var username = login_form.find('#login_form_username').val();
				var password = login_form.find('#login_form_password').val();
				$.post('<?php echo BASE_URL ?>login', { username: username, password: password, ajax: 'ajax_access' }, function(data) {
					var error_msg = data.error_msg || '';
					login_form.find('#error_box').html('<p class="login-box-msg text-red">' + error_msg + '</p>');
					login_form.find('#login_form_password').val('');
				})
			});
		});
	</script>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				<?php echo $page_title ?>
				<?php echo $page_subtitle ?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo BASE_URL ?>"><i class="fa fa-dashboard"></i> Home</a></li>
				<?php if (defined('MODULE_NAME')): ?>
					<?php if (MODULE_NAME != MODULE_GROUP): ?>
						<li><a href="<?php echo MODULE_URL ?>"><?php echo MODULE_GROUP ?></a></li>
					<?php endif ?>
					<li class="active"><?php echo MODULE_NAME ?></li>
				<?php else: ?>
					<li class="active">Dashboard</li>
				<?php endif ?>
			</ol>
		</section>