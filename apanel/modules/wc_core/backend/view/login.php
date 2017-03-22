<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Login</title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
		<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/AdminLTE.min.css">
		<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/skin.css">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="<?= BASE_URL ?>assets/js/jquery-2.2.3.min.js"></script>
		<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<a href=""><b>Login</b>Page</a>
			</div>
			<div class="login-box-body">
				<?php if ( ! empty($error_msg)): ?>
					<p class="login-box-msg text-red"><?=$error_msg?></p>
				<?php else: ?>
					<p class="login-box-msg">Sign in to start your session</p>
				<?php endif ?>
				<form action="" method="post">
					<div class="form-group has-feedback">
						<input type="text" name="username" class="form-control" placeholder="Username">
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="password" class="form-control" placeholder="Password">
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
