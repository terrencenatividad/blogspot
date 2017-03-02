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
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/skin.min.css">
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
	<script src="<?= BASE_URL ?>assets/js/slimscroll.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/fastclick.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/app.min.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<header class="main-header">
	<a href="../../index2.html" class="logo">
		<span class="logo-mini"><b>A</b>LT</span>
		<span class="logo-lg"><b>Admin</b>LTE</span>
	</a>
	<nav class="navbar navbar-static-top">
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</a>
		<div class="navbar-custom-menu">
		<ul class="nav navbar-nav">
			<li class="dropdown messages-menu">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<i class="fa fa-envelope-o"></i>
				<span class="label label-success">4</span>
			</a>
			<ul class="dropdown-menu">
				<li class="header">You have 4 messages</li>
				<li>
				<ul class="menu">
					<li>
					<a href="#">
						<div class="pull-left">
						<img src="<?= BASE_URL ?>/assets/images/user2-160x160.jpg" class="img-circle" alt="User Image">
						</div>
						<h4>
						Support Team
						<small><i class="fa fa-clock-o"></i> 5 mins</small>
						</h4>
						<p>Why not buy a new awesome theme?</p>
					</a>
					</li>
				</ul>
				</li>
				<li class="footer"><a href="#">See All Messages</a></li>
			</ul>
			</li>
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
			<li class="dropdown tasks-menu">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<i class="fa fa-flag-o"></i>
				<span class="label label-danger">9</span>
			</a>
			<ul class="dropdown-menu">
				<li class="header">You have 9 tasks</li>
				<li>
				<ul class="menu">
					<li>
					<a href="#">
						<h3>
						Design some buttons
						<small class="pull-right">20%</small>
						</h3>
						<div class="progress xs">
						<div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
							<span class="sr-only">20% Complete</span>
						</div>
						</div>
					</a>
					</li>
				</ul>
				</li>
				<li class="footer">
				<a href="#">View all tasks</a>
				</li>
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
			<li>
			<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
			</li>
		</ul>
		</div>
	</nav>
	</header>
	<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
		<div class="pull-left image">
			<img src="<?= BASE_URL ?>assets/images/user_icon.png" class="img-circle" alt="User Image">
		</div>
		<div class="pull-left info">
			<p>Alexander Pierce</p>
			<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		</div>
		</div>
		<form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
				<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
				</button>
				</span>
			</div>
		</form>
		<ul class="sidebar-menu">
			<li>
				<a href="<?php echo BASE_URL ?>">
					<i class="fa fa-dashboard"></i> <span>Dashboard</span>
				</a>
			</li>
			<?php foreach ($header_nav as $key => $header_nav2): ?>
				<li class="header"><?php echo strtoupper($key) ?></li>
				<?php foreach ($header_nav2 as $key2 => $header_nav3): ?>
					<?php if (is_array($header_nav3)): ?>
						<?php if (count($header_nav3) > 1): ?>
							<li class="treeview">
								<a href="#">
									<i class="fa fa-dashboard"></i> <span><?php echo $key2 ?></span>
									<span class="pull-right-container">
										<i class="fa fa-angle-left pull-right"></i>
									</span>
								</a>
								<ul class="treeview-menu">
									<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
										<li>
											<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
												<i class="fa fa-circle-o"></i> <?php echo $key3 ?>
											</a>
										</li>
									<?php endforeach ?>
								</ul>
							</li>
						<?php else: ?>
							<?php foreach ($header_nav3 as $key3 => $header_nav4): ?>
								<li>
									<a href="<?php echo BASE_URL . trim($header_nav4, '%') ?>">
										<i class="fa fa-dashboard"></i> <span><?php echo $key3 ?></span>
									</a>
								</li>
							<?php endforeach ?>
						<?php endif ?>
					<?php else: ?>
						<li>
							<a href="<?php echo BASE_URL . trim($header_nav3, '%') ?>">
								<i class="fa fa-dashboard"></i> <span><?php echo $key2 ?></span>
							</a>
						</li>
					<?php endif ?>
				<?php endforeach ?>
			<?php endforeach ?>
		</ul>
	</section>
	</aside>
	<script>
		$('.sidebar-menu [href="<?php echo BASE_URL . $header_active ?>"]').parents('li').addClass('active');
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