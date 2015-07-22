<?php
	// Compose New Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'newMessage') {
		// User Validations
		if ($_POST['toId'] == '...') {
			$msgBox = alertBox($recipientReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($msgSubjectReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($msgReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$toId = $mysqli->real_escape_string($_POST['toId']);
			$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
			$messageText = htmlentities($_POST['messageText']);
			$messageDate = date("Y-m-d H:i:s");

			// Get Employee's Email Address
			$getEmail = "SELECT empEmail AS theEmail FROM employees WHERE empId = ".$toId;
			$emailres = mysqli_query($mysqli, $getEmail) or die('-1'.mysqli_error());
			$col = mysqli_fetch_assoc($emailres);
			$theEmail = $col['theEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									privatemessages(
										fromId,
										toId,
										messageTitle,
										messageText,
										messageDate
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('sssss',
				$empId,
				$toId,
				$messageTitle,
				$messageText,
				$messageDate
			);
			$stmt->execute();

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $newMsgEmailSubject.' '.$empName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<p>'.$messageText.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$emailLoginLink.'</p>';
			$message .= '<p>'.$emailThankYou.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($msgSentMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($msgErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['messageTitle'] = $_POST['messageText'] = '';
			$stmt->close();
		}
	}

	// Get Total Inbox Message Count
	$inboxsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 0";
	$inboxtot = mysqli_query($mysqli, $inboxsql) or die('-2'.mysqli_error());
	$inboxtotal = mysqli_num_rows($inboxtot);

	// Get Total Sent Message Count
	$sentsql = "SELECT 'X' FROM privatemessages WHERE fromId = ".$empId;
	$senttot = mysqli_query($mysqli, $sentsql) or die('-3'.mysqli_error());
	$senttotal = mysqli_num_rows($senttot);

	// Get Total Archived Message Count
	$arcsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 1";
	$arctot = mysqli_query($mysqli, $arcsql) or die('-4'.mysqli_error());
	$arctotal = mysqli_num_rows($arctot);

	include 'includes/navigation.php';
?>
<div class="content">
	<h3><?php echo $pageName; ?></h3>

	<ul class="nav nav-tabs">
		<li><a href="index.php?page=inbox"><i class="fa fa-inbox"></i> <?php echo $inboxPage; ?> <span class="text-muted">(<?php echo $inboxtotal; ?>)</span></a></li>
		<li><a href="index.php?page=sent"><i class="fa fa-paper-plane-o"></i> <?php echo $sentNavLink; ?> <span class="text-muted">(<?php echo $senttotal; ?>)</span></a></li>
		<li><a href="index.php?page=archived"><i class="fa fa-archive"></i> <?php echo $archiveNavLink; ?> <span class="text-muted">(<?php echo $arctotal; ?>)</span></a></li>
	</ul>
</div>

<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<form action="" method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="toId"><?php echo $recipientField; ?> <sup><?php echo $reqField; ?></sup></label>
					<select class="form-control" name="toId">
						<?php
							$empSql = "SELECT empId, CONCAT(empFirst,' ',empLast) AS employee FROM employees WHERE empId != ".$empId." AND isActive = 1";
							$empres = mysqli_query($mysqli, $empSql) or die('-5'.mysqli_error());
						?>
						<option value="..."><?php echo $selectOption; ?></option>
						<?php while ($row = mysqli_fetch_assoc($empres)) { ?>
							<option value="<?php echo $row['empId']; ?>"><?php echo clean($row['employee']); ?></option>
						<?php } ?>
					</select>
					<span class="help-block"><?php echo $recipientFieldHelp; ?></span>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="messageTitle"><?php echo $subjectField; ?> <sup><?php echo $reqField; ?></sup></label>
					<input type="text" class="form-control" required="" name="messageTitle" value="<?php echo isset($_POST['messageTitle']) ? $_POST['messageTitle'] : ''; ?>" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="messageText"><?php echo $messageField; ?> <sup><?php echo $reqField; ?></sup></label>
			<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
		</div>
		<button type="input" name="submit" value="newMessage" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendMsgBtn; ?></button>
	</form>
</div>