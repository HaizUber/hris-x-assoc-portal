<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title ?></title>

	<link rel="icon" type="image/png" href="<?php echo base_url('assets/FEU TECH Seal.png'); ?>" />
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/dist/css/adminlte.min.css">
	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

	<style>
		.caps {
			display: none;
		}

		#eye {
			cursor: pointer;
		}

		.card {
			width: 550px;
		}

		.form-control:focus {
			border-color: #28a745;
			box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, 0.25);
		}

		@media only screen and (min-width: 600px) {
			.card {
				width: 350px;
			}
		}
	</style>
</head>

<body class="hold-transition login-page">

	<noscript>
		<div role="alert" class="ic-flash-static ic-flash-error">
			<div class="ic-flash__icon" aria-hidden="true">
				<i class="icon-warning"></i>
			</div>
			<h1>You need to have JavaScript enabled in order to use the full functionalty of this site and avoid errors.</h1>
		</div>
	</noscript>

	<div class="login-box">
		<div class="card card-outline card-success" style="width: 450px;">
			<div class="card-header text-center p-4">
				<img src="<?php echo base_url('assets/feutech.png'); ?>" style="width:100%;">
			</div>
			<div class="card-body p-5">
				<h4 class="login-box-msg">Sign in</h4>
				<div class="input-group mb-3">
					<div class="form-floating mb-3">
						<input type="text" class="form-control" name="username" id="username" placeholder="Username">
						<label for="username">Employee ID</label>
					</div>
				</div>
				<div class="input-group mb-3">
					<div class="form-floating">
						<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						<label for="password">Password</label>
					</div>
				</div>
				<span class="caps btn btn-danger mt-1 mb-2" id="caps">Caps Lock is ON.</span>
				<input type="checkbox" onclick="togglePassword()" class="mb-3"> Show Password
				<div class="row">
					<div class="col-12">
						<a href="<?php echo base_url('apps') ?>">
							<button type="submit" onClick="this.disabled=true; this.innerText='Signing in...';" class="btn btn-success btn-block">Sign In</button>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

<script>
	var password = document.getElementById("password");
	var caps = document.getElementById("caps");
	password.addEventListener("keydown", function(event) {
		if (event.getModifierState("CapsLock")) {
			caps.style.display = "block";
		} else {
			caps.style.display = "none"
		}
	});

	function togglePassword() {
		var password = document.getElementById("password");
		if (password.type === "password") {
			password.type = "text";
		} else {
			password.type = "password";
		}
	}
</script>
