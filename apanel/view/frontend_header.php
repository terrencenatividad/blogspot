<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $page_title ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/home/css/style.css">
	<script src="<?= BASE_URL ?>assets/js/jquery.min.js"></script>
	<script src="<?= BASE_URL ?>assets/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<div class="row"></div>
	</div>
	<div class="container">
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="col-md-12">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span> 
						</button>
						<a class="navbar-brand" href="#"><span class = "fa fa-home"></span></a>
					</div>
					<div class="collapse navbar-collapse" id="myNavbar">
						<ul class="nav navbar-nav">
							<li class="active"><a href="#">Home</a></li>
							<li><a href="#">Page 1</a></li>
							<li><a href="#">Page 2</a></li> 
							<li><a href="#">Page 3</a></li> 
						</ul>
					</div>
				</div>
			</div>
		</nav>
	</div>