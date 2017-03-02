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
		<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/AdminLTE.min.css">
		<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/skin.min.css">
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="<?= BASE_URL ?>assets/js/jquery-2.2.3.min.js"></script>
		<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
		<script src="<?= BASE_URL ?>assets/js/slimscroll.min.js"></script>
		<script src="<?= BASE_URL ?>assets/js/fastclick.min.js"></script>
		<script src="<?= BASE_URL ?>assets/js/app.min.js"></script>
	</head>
	<body class="hold-transition skin-blue layout-top-nav">
		<div class="wrapper">
			<header class="main-header">
				<nav class="navbar navbar-static-top">
					<div class="container">
						<div class="navbar-header">
							<a href="../../index2.html" class="navbar-brand"><b>Admin</b>LTE</a>
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
						</div>
						<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
							<ul class="nav navbar-nav">
								<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
								<li><a href="#">Link</a></li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Action</a></li>
										<li><a href="#">Another action</a></li>
										<li><a href="#">Something else here</a></li>
										<li class="divider"></li>
										<li><a href="#">Separated link</a></li>
										<li class="divider"></li>
										<li><a href="#">One more separated link</a></li>
									</ul>
								</li>
							</ul>
							<form class="navbar-form navbar-left" role="search">
								<div class="form-group">
									<input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
								</div>
							</form>
						</div>
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
															<img src="<?= BASE_URL ?>assets/img/user_icon.png" class="img-circle" alt="User Image">
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
										<img src="<?= BASE_URL ?>assets/img/user_icon.png" class="user-image" alt="User Image">
										<span class="hidden-xs">Alexander Pierce</span>
									</a>
									<ul class="dropdown-menu">
										<li class="user-header">
											<img src="<?= BASE_URL ?>assets/img/user_icon.png" class="img-circle" alt="User Image">
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