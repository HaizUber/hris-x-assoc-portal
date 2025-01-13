<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title ?></title>

	<link rel="icon" type="image/png" href="<?php echo base_url('assets/FEU TECH Seal.png'); ?>" />
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Google Fonts -->
	<link href="https://fonts.gstatic.com" rel="preconnect">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/fontawesome-6.4.0/css/all.min.css'); ?>">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css'); ?>">
	<!-- Bootstrap 5 -->
	<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap5/css/bootstrap.min.css'); ?>">
	<!-- jQuery -->
	<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
	<!-- Own CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">

	<style>
		.card img {
			width: 11rem;
		}

		.card a {
			text-decoration: none;
			color: #000000;
		}

		.card {
			margin: 2px;
		}
	</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

	<noscript>
		<div role="alert" class="ic-flash-static ic-flash-error">
			<div class="ic-flash__icon" aria-hidden="true">
				<i class="icon-warning"></i>
			</div>
			<h1>You need to have JavaScript enabled in order to use the full functionalty of this site and avoid errors.</h1>
		</div>
	</noscript>

	<div class="wrapper">

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item">
					<img src="<?= base_url('assets/feutech.png'); ?>" class="feu-logo" alt="Branch-logo" />
				</li>
			</ul>

			<ul class="navbar-nav ml-auto">
			</ul>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<a href="<?php echo base_url('apps'); ?>" class="brand-link">
				<img src="<?php echo base_url('assets/FEU TECH Seal.png'); ?>" class="brand-image img-circle elevation-3" alt="FEU TECH Logo" style="opacity: .8">
				<span class="brand-text font-weight-light ml-1">DAS v2</span>
			</a>

			<div class="sidebar">
				<div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center ">
					<div class="info">
						<a href="#" class="d-block" style="overflow: hidden; text-overflow: ellipsis; pointer-events: none;"></a>
					</div>
				</div>
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<li class="nav-header">Menu</li>
						<li class="nav-item">
							<a href="<?php echo base_url('#'); ?>" class="nav-link">
								<i class="nav-icon fa-solid fa-grip"></i>
								<p>Applications<i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>APF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>CRF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>PCF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>PRF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>UAAPRF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>SSR</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>BPF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>LDRF</p>
									</a>
								</li>
							</ul>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>CUSI</p>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a href="" class="nav-link">
								<i class="nav-icon fa fa-sign-out"></i>
								<p>Logout</p>
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</aside>
