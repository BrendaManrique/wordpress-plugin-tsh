<?php
	$msgBox = '';
	$avatarDir = $set['avatarFolder'];
	
	// Get the Current Year & Month Name
	$currentYear = date('Y');
	$thisMonth = date('F');
	// Get the Current Week number
	$theDate = date('Y-m-d');
	$currentMonth = date('m');
	$weekNo = date('W', strtotime($theDate) + 60 * 60 * 24 );
	if ($currentMonth == '12' && $weekNo == '01') { $weekNum = '52'; } else { $weekNum = $weekNo; }
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $set['siteName']; ?> &middot; <?php echo $pageName; ?></title>
	<meta name="description" content="">
	<meta name="keywords" content="">

	<link rel="stylesheet" type="text/css" href="css/googlefonts.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/custom.css" />
	<?php if (isset($addCss)) { echo $addCss; } ?>
	<link rel="stylesheet" type="text/css" href="css/timezone.css" />
	<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />

	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<section class="header">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<a href=""><img src="images/logo.png" alt="TimeZone"></a>
				</div>

				<div class="col-md-6 text-right">
					<?php echo $todayIsQuip.' '.date('l'). " the " .date('jS \of F, Y'); ?> <span class="clock">0:00:00 AM</span>
				</div>
			</div>
		</div>
	</section>