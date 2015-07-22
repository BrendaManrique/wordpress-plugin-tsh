<?php
	$messageId = $_GET['messageId'];

	// Mark Message as Read
	if (isset($_POST['submit']) && $_POST['submit'] == 'markRead') {
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgReadMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Archived
	if (isset($_POST['submit']) && $_POST['submit'] == 'archive') {
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toArchived = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgArchivedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMsg') {
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toDeleted = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Reply to Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'replyToMessage') {
		$toId = $mysqli->real_escape_string($_POST['toId']);
		$origId = $mysqli->real_escape_string($_POST['origId']);
		$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
		$messageText = htmlentities($_POST['messageText']);
		$messageDate = date("Y-m-d H:i:s");

		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($msgSubjectReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($msgReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
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
										origId,
										messageTitle,
										messageText,
										messageDate
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssssss',
				$empId,
				$toId,
				$origId,
				$messageTitle,
				$messageText,
				$messageDate
			);
			$stmt->execute();

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $replyMsgEmailSubject.' '.$empName;

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
				$msgBox = alertBox($replyMsgSent, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($msgErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['messageText'] = '';
			$stmt->close();
		}
	}

    $query = "SELECT
				privatemessages.messageId,
				privatemessages.fromId,
				privatemessages.toId,
				privatemessages.origId,
				privatemessages.messageTitle,
				privatemessages.messageText,
				DATE_FORMAT(privatemessages.messageDate,'%b %d %Y at %h:%i %p') AS messageDate,
				UNIX_TIMESTAMP(privatemessages.messageDate) AS orderDate,
				privatemessages.toRead,
				privatemessages.toArchived,
				privatemessages.toDeleted,
				CONCAT(employees.empFirst,' ',employees.empLast) AS receivedFrom
			FROM
				privatemessages
				LEFT JOIN employees ON privatemessages.fromId = employees.empId
			WHERE
				privatemessages.messageId = ".$messageId;
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Get Total Inbox Message Count
	$inboxsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 0";
	$inboxtot = mysqli_query($mysqli, $inboxsql) or die('-3'.mysqli_error());
	$inboxtotal = mysqli_num_rows($inboxtot);

	// Get Total Sent Message Count
	$sentsql = "SELECT 'X' FROM privatemessages WHERE fromId = ".$empId." AND privatemessages.fromDeleted = 0";
	$senttot = mysqli_query($mysqli, $sentsql) or die('-4'.mysqli_error());
	$senttotal = mysqli_num_rows($senttot);

	// Get Total Archived Message Count
	$arcsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 1";
	$arctot = mysqli_query($mysqli, $arcsql) or die('-5'.mysqli_error());
	$arctotal = mysqli_num_rows($arctot);

	include 'includes/navigation.php';

	if ($row['toId'] != $empId) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content">
		<h3><?php echo $pageName; ?></h3>

		<ul class="nav nav-tabs">
			<li><a href="index.php?page=inbox"><i class="fa fa-inbox"></i> <?php echo $inboxPage; ?> <span class="text-muted">(<?php echo $inboxtotal; ?>)</span></a></li>
			<li><a href="index.php?page=sent"><i class="fa fa-paper-plane-o"></i> <?php echo $sentNavLink; ?> <span class="text-muted">(<?php echo $senttotal; ?>)</span></a></li>
			<li><a href="index.php?page=archived"><i class="fa fa-archive"></i> <?php echo $archiveNavLink; ?> <span class="text-muted">(<?php echo $arctotal; ?>)</span></a></li>
			<li class="pull-right"><a href="index.php?page=compose" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $composeNavLink; ?></a></li>
		</ul>
	</div>

	<div class="content">
		<?php if ($msgBox) { echo $msgBox; } ?>
		<h4 class="msgHeading">
			<?php echo clean($row['receivedFrom']); ?> <i class="fa fa-long-arrow-right msgIcon"></i> <?php echo $youText; ?>
			<span class="pull-right"><?php echo $row['messageDate']; ?></span>
		</h4>
		<div class="msgOptions">
			<div class="row">
				<div class="col-md-8">
					<strong class="lead"><?php echo clean($row['messageTitle']); ?></strong>
				</div>
				<div class="col-md-4">
					<span class="pull-right">
						<form action="" method="post">
							<a data-toggle="modal" href="#reply" class="btn btn-info btn-sm btn-icon"><i class="fa fa-reply"></i> <?php echo $sendReplyBtn; ?></a>
							<?php if ($row['toRead'] == '0') { ?>
								<button type="input" name="submit" value="markRead" class="btn btn-primary btn-sm btn-icon"><i class="fa fa-check-square"></i> <?php echo $markAsReadBtn; ?></button>
							<?php } else { ?>
								<button type="input" name="submit" value="archive" class="btn btn-warning btn-sm btn-icon"><i class="fa fa-archive"></i> <?php echo $archiveNavLink; ?></button>
							<?php } ?>
							<a data-toggle="modal" href="#delete" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash-o"></i> <?php echo $deleteBtn; ?></a>
						</form>
					</span>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="msgContent"><?php echo nl2br(clean($row['messageText'])); ?></div>
	</div>

	<div id="reply" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $sendReplyMsgModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="messageTitle"><?php echo $subjectField; ?></label>
							<input type="text" class="form-control" required="" name="messageTitle" value="re: <?php echo clean($row['messageTitle']); ?>" />
						</div>
						<div class="form-group">
							<label for="messageText"><?php echo $messageField; ?></label>
							<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
						</div>
					</div>

					<div class="modal-footer">
						<input type="hidden" name="toId" value="<?php echo $row['fromId']; ?>" />
						<input type="hidden" name="origId" value="<?php echo $row['origId']; ?>" />
						<button type="input" name="submit" value="replyToMessage" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendReplyBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $deleteMsgConf.' '.clean($row['messageTitle']); ?>?</p>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="deleteMsg" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>