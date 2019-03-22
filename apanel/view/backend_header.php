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
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/_all-skins.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/morris.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/nprogress.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
	<?php foreach ($include_css as $inc_css): ?>
		<link rel="stylesheet" href="<?= BASE_URL . 'assets/css/' . $inc_css ?>">
	<?php endforeach ?>

	<script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery-ui.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.slimscroll.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.sparkline.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.knob.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/select2.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/icheck.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootbox.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/moment.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/daterangepicker.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/fastclick.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.inputmask.bundle.js"></script>
	<script src="<?= BASE_URL ?>assets/js/raphael.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/morris.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap-select.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/global.js"></script>
	<script src="<?= BASE_URL ?>assets/js/adminlte.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/demo.js"></script>
	<script src="<?= BASE_URL ?>assets/js/nprogress.js"></script>
	<script src="<?= BASE_URL ?>assets/js/jquery.form.min.js"></script>
	<script src='https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=ch223xqxfn7t8v1kauqe1bf82k8ut75dwppo50p0d7l63eh7'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
	<script>
		$.widget.bridge('uibutton', $.ui.button);
	</script>
	<?php foreach ($include_js as $inc_js): ?>
		<script src="<?= BASE_URL . 'assets/js/' . $inc_js ?>"></script>
	<?php endforeach ?>
</head>
<?php $user_image = (!empty($user_image->image)) ?  str_replace('/apanel', '', BASE_URL) . "uploads/items/large/" . $user_image->image : BASE_URL . "assets/images/user_icon.png" ?>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<header class="main-header">
			<div id="nprogress_parent"></div>
			<a href="<?=BASE_URL?>" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>T</b>BS</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>Ter</b>BLOGSPOT</span>
			</a>
			<?php $this->session = new session(); ?>
			<?php $login = $this->session->get('login'); ?>
			<nav class="navbar navbar-static-top">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?=$user_image?>" class = "user-image" alt ="User Image">
								<span class="hidden-xs"><?= NAME ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?=$user_image?>" class = "img-circle" alt ="User Image">
									<p>
										<?= NAME ?>
										<small><?= GROUPNAME ?></small>
									</p>
								</li>
								<li class="user-body">
								</li>
								<li class="user-footer">
									<div class="pull-left">
										<a href="<?=BASE_URL?>maintenance/user/view/<?=$login['username']?>" class="btn btn-default btn-flat">Profile</a>
									</div>
									<div class="pull-right">
										<a href="<?=BASE_URL?>logout" class="btn btn-default btn-flat">Sign out</a>
									</div>
								</li>
							</ul>
						</li>
						<!-- Control Sidebar Toggle Button -->
					</ul>
				</div>
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
						<img src="<?=$user_image?>" class = "img-circle" alt ="User Image">
						<br><br>
					</div>
					<div class="pull-left info">
						<p><?=NAME?></p>
						<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
					</div>
				</div>
				<ul class="sidebar-menu" data-widget="tree">
					<li class="header">MAIN NAVIGATION</li> 
					<li>
						<a href="<?=BASE_URL?>">
							<i class="fa fa-dashboard"></i> <span>Dashboard</span>
						</a>
					</li>
					<?php foreach ($header_nav as $key => $header_nav2): ?>
						<?php foreach ($header_nav2 as $key2 => $header_nav3): ?>
							<?php if (is_array($header_nav3)): ?>
								<?php if (count($header_nav3) > 1): ?>
									<li class="treeview">
										<a href="#">
											<span><i class = "fa fa-th"></i> <?php echo $key2 ?></span>
											<span class="pull-right-container">
												<i class="fa fa-angle-left pull-right"></i>
											</span>
										</a>
										<ul class="treeview-menu">
											<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
												<li>
													<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
														<i class="fa fa-circle-o"></i><?php echo $key3 ?>
													</a>
												</li>
											<?php endforeach ?>
										</ul>
									</li>
									<?php else: ?>
										<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
											<li>
												<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
													<i class = "fa fa-star"></i> <span><?php echo $key3 ?></span>
												</a>
											</li>
										<?php endforeach ?>
									<?php endif ?>
									<?php else: ?>
										<li>
											<a href="<?php echo BASE_URL . trim($header_nav3, '%') ?>">
												<i class = "fa fa-star"></i> <span><?php echo $key2 ?></span>
											</a>
										</li>
									<?php endif ?>
								<?php endforeach ?>
							<?php endforeach ?>
						</ul>
					</section>
					<!-- /.sidebar -->
				</aside>
				<script>
					$('.main-sidebar [href="<?php echo $header_active ?>"]').parents('li').addClass('active');
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