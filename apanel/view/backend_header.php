<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $page_title ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/select2.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datepicker3.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/skin.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/icheck.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<?php foreach ($include_css as $inc_css): ?>
	<link rel="stylesheet" href="<?= BASE_URL . $inc_css ?>">
	<?php endforeach ?>

	<script src="<?= BASE_URL ?>assets/js/jquery-2.2.3.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/select2.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap-datepicker.js"></script>
	<script src="<?= BASE_URL ?>assets/js/icheck.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/slimscroll.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/fastclick.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/app.min.js"></script>
</head>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
	<header class="main-header">
		<nav class="navbar navbar-static-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="" class="navbar-brand"><b>Admin</b>LTE</a>
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
						<i class="fa fa-bars"></i>
					</button>
				</div>
				<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
					<ul class="nav navbar-nav">
						<li>
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
						<li class="dropdown notifications-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-bell-o"></i>
								<span class="label label-warning">10</span>
							</a>
							<ul class="dropdown-menu">
								<li class="header">You have 10 notifications</li>
								<li>
									<ul class="menu">
										<li>
										<a href="#">
											<i class="fa fa-users text-aqua"></i> 5 new members joined today
										</a>
										</li>
									</ul>
								</li>
								<li class="footer"><a href="#">View all</a></li>
							</ul>
						</li>
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?= BASE_URL ?>assets/images/user_icon.png" class="user-image" alt="User Image">
								<span class="hidden-xs">Alexander Pierce</span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?= BASE_URL ?>assets/images/user_icon.png" class="img-circle" alt="User Image">
									<p>
										Alexander Pierce - Web Developer
										<small>Member since Nov. 2012</small>
									</p>
								</li>
								<li class="user-body">
									<div class="row">
										<div class="col-xs-4 text-center">
											<a href="#">Followers</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Sales</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Friends</a>
										</div>
									</div>
								</li>
								<li class="user-footer">
									<div class="pull-left">
										<a href="#" class="btn btn-default btn-flat">Profile</a>
									</div>
									<div class="pull-right">
										<a href="#" class="btn btn-default btn-flat">Sign out</a>
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
		$('.navbar-static-top [href="<?php echo BASE_URL . $header_active ?>"]').parents('li').addClass('active');
	</script>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				<?php echo $page_title ?>
				<?php echo $page_subtitle ?>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Examples</a></li>
				<li class="active">Blank page</li>
			</ol>
		</section>