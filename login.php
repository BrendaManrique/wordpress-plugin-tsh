<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header("Location: install/install.php");
	} else {
		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'ar':		include ('language/ar.php');		break;
			case 'bg':		include ('language/bg.php');		break;
			case 'ce':		include ('language/ce.php');		break;
			case 'cs':		include ('language/cs.php');		break;
			case 'da':		include ('language/da.php');		break;
			case 'en':		include ('language/en.php');		break;
			case 'en-ca':	include ('language/en-ca.php');		break;
			case 'en-gb':	include ('language/en-gb.php');		break;
			case 'es':		include ('language/es.php');		break;
			case 'fr':		include ('language/fr.php');		break;
			case 'hr':		include ('language/hr.php');		break;
			case 'hu':		include ('language/hu.php');		break;
			case 'hy':		include ('language/hy.php');		break;
			case 'id':		include ('language/id.php');		break;
			case 'it':		include ('language/it.php');		break;
			case 'ja':		include ('language/ja.php');		break;
			case 'ko':		include ('language/ko.php');		break;
			case 'nl':		include ('language/nl.php');		break;
			case 'pt':		include ('language/pt.php');		break;
			case 'ro':		include ('language/ro.php');		break;
			case 'sv':		include ('language/sv.php');		break;
			case 'th':		include ('language/th.php');		break;
			case 'vi':		include ('language/vi.php');		break;
			case 'yue':		include ('language/yue.php');		break;
		}

		// Include Functions
		include('includes/functions.php');

		$msgBox = '';
		$isReset = '';

		// User Log In Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'signIn') {
			if($_POST['empEmail'] == '') {
				$msgBox = alertBox($accountEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] == '') {
				$msgBox = alertBox($accountPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Check if the User account has been activated
				$empEmail = (isset($_POST['empEmail'])) ? $mysqli->real_escape_string($_POST['empEmail']) : '';
				$check = $mysqli->query("SELECT isActive FROM employees WHERE empEmail = '".$empEmail."'");
				$row = mysqli_fetch_assoc($check);

				// If the account is active - allow the login
				if ($row['isActive'] == '1') {
					$password = encryptIt($_POST['password']);

					if($stmt = $mysqli -> prepare("
											SELECT
												empId,
												isAdmin,
												isMgr,
												empEmail,
												empFirst,
												empLast,
												empPosition
											FROM
												employees
											WHERE
												empEmail = ? AND
												password = ?
					"))	{
						$stmt -> bind_param("ss",
											$empEmail,
											$password
						);
						$stmt -> execute();
						$stmt -> bind_result(
									$empId,
									$isAdmin,
									$isMgr,
									$empEmail,
									$empFirst,
									$empLast,
									$empPosition
						);
						$stmt -> fetch();
						$stmt -> close();

						if (!empty($empId)) {
							if (session_id() == '') {
								session_start();
							}
							$_SESSION["empId"] 			= $empId;
							$_SESSION["isAdmin"] 		= $isAdmin;
							$_SESSION["isMgr"] 			= $isMgr;
							$_SESSION["empEmail"] 		= $empEmail;
							$_SESSION["empName"] 		= $empFirst.' '.$empLast;
							$_SESSION["empPosition"]	= $empPosition;
							header('Location: index.php');
						} else {
							$msgBox = alertBox($loginFailedMsg, "<i class='fa fa-times-circle'></i>", "danger");
						}
					}

					// Update Last Visited Date for User
					$empLastVisited = date("Y-m-d H:i:s");
					$sqlStmt = $mysqli->prepare("
											UPDATE
												employees
											SET
												empLastVisited = ?
											WHERE
												empId = ?
					");
					$sqlStmt->bind_param('ss',
									   $empLastVisited,
									   $empId
					);
					$sqlStmt->execute();
					$sqlStmt->close();

				} else if ($row['isActive'] == '0') {
					// If the account is not active, show a message
					$msgBox = alertBox($inactiveAccountMsg, "<i class='fa fa-warning'></i>", "warning");
				} else {
					// No account found
					$msgBox = alertBox($noAccountFoundMsg, "<i class='fa fa-times-circle'></i>", "danger");
				}
			}
		}

		// Reset Account Password Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'resetPass') {
			// Set the email address
			$theEmail = (isset($_POST['theEmail'])) ? $mysqli->real_escape_string($_POST['theEmail']) : '';

			// Validation
			if ($_POST['theEmail'] == "") {
				$msgBox = alertBox($accountEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$query = "SELECT empEmail FROM employees WHERE empEmail = ?";
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s",$theEmail);
				$stmt->execute();
				$stmt->bind_result($empEmail);
				$stmt->store_result();
				$numrows = $stmt->num_rows();

				if ($numrows == 1){
					// Generate a RANDOM Hash for a password
					$randomPassword = uniqid(rand());

					// Take the first 8 digits and use them as the password we intend to email the Employee
					$emailPassword = substr($randomPassword, 0, 8);

					// Encrypt $emailPassword for the database
					$newpassword = encryptIt($emailPassword);

					//update password in db
					$updatesql = "UPDATE employees SET password = ? WHERE empEmail = ?";
					$update = $mysqli->prepare($updatesql);
					$update->bind_param("ss",
											$newpassword,
											$theEmail
										);
					$update->execute();

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];

					$subject = $resetPassEmailSubject;

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>'.$resetPassEmail1.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$emailPassword.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$resetPassEmail2.'</p>';
					$message .= '<p>'.$resetPassEmail3.'</p>';
					$message .= '<p>'.$emailThankYou.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($theEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($passwordResetMsg, "<i class='fa fa-check-square-o'></i>", "success");
						$isReset = 'true';
						$stmt->close();
					}
				} else {
					// No account found
					$msgBox = alertBox($noAccountFoundMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			}
		}
?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $set['siteName']; ?> &middot; <?php echo $loginPageTitle; ?></title>
		<meta name="description" content="">
		<meta name="keywords" content="">

		<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,300italic,400italic,600italic'>
		<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Raleway:300,400,600,300italic,400italic,600italic'>

		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="css/custom.css" />
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
					<div class="col-md-8">
						<a href=""><img src="images/logo.png" alt="TimeZone"></a>
					</div>
				</div>
			</div>
		</section>
		
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="loginCont">
						<div class="login">
							<h2 class="text-center"><?php echo $loginPageTitle; ?></h2>
							<?php if ($msgBox) { echo $msgBox; } ?>
							<form action="" method="post" class="mt20">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
									<input type="email" class="form-control" required="" placeholder="<?php echo $emailAddyField; ?>" name="empEmail" />
								</div>
								<br>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock icon-lock"></i></span>
									<input type="password" class="form-control" required="" placeholder="<?php echo $passwordField; ?>" name="password" />
								</div>
								<small class="pull-right"><a data-toggle="modal" href="#resetPassword"><i class="fa fa-unlock"></i> <?php echo $resetPasswordLink; ?></a></small>
								<button type="input" name="submit" value="signIn" class="btn btn-login btn-icon"><i class="fa fa-sign-in"></i> <?php echo $signInNav; ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-labelledby="resetPassword" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $resetPasswordModal; ?></h4>
					</div>
					<?php if ($isReset == '') { ?>
						<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="theEmail"><?php echo $accountEmailAddyField; ?></label>
									<input type="email" class="form-control" required="" name="theEmail" id="theEmail" value="" />
									<span class="help-block"><?php echo $accountEmailAddyFieldHelp; ?></span>
								</div>
							</div>
							<div class="modal-footer">
								<button type="input" name="submit" value="resetPass" class="btn btn-success btn-icon"><i class="fa fa-unlock"></i> <?php echo $resetPasswordLink; ?></button>
								<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					<?php } else { ?>
						<div class="modal-body">
							<p class="lead"><?php echo $passwordReset1; ?></p>
							<p><?php echo $passwordReset2; ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		
		<section id="footer-default">
			<div class="container">
				<div class="row">
					<div class="col-md-12 mt30 mb20">
						<a href=""><img src="images/logo.png"></a>
					</div>
					<div class="col-md-12">
						<p>
							<?php echo $footerText1; ?> <a href="http://codecanyon.net/item/timezone-employee-management-time-clock/6682629?ref=Luminary"><?php echo $set['siteName']; ?></a>
							<?php echo $footerText2; ?> <i class="fa fa-circle-thin"></i>
							<?php echo $footerText3; ?>
						</p>
					</div>
				</div>
			</div>
		</section>
		
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/custom.js" type="text/javascript"></script>
	</body>
	</html>
<?php } ?>