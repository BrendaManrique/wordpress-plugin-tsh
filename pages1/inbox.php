<?php
	$jsFile = 'privateMessages';
	$pagPages = '20';
	$pmPage = 'inbox';

	// Mark Message as Read
	if (isset($_POST['submit']) && $_POST['submit'] == 'markRead') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgReadMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Archived
	if (isset($_POST['submit']) && $_POST['submit'] == 'archive') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toArchived = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgArchivedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMsg') {
		$messageId = $mysqli->real_escape_string($_POST['deleteId']);
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

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("
		SELECT
			*
		FROM
			privatemessages
			LEFT JOIN employees ON privatemessages.fromId = employees.empId
		WHERE
			privatemessages.toId = ".$empId." AND
			privatemessages.toDeleted = 0 AND
			privatemessages.toArchived = 0
	");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

    $query = "SELECT
				privatemessages.messageId,
				privatemessages.fromId,
				privatemessages.toId,
				privatemessages.origId,
				privatemessages.messageTitle,
				privatemessages.messageText,
				DATE_FORMAT(privatemessages.messageDate,'%b %d %Y') AS listDate,
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
				privatemessages.toId = ".$empId." AND
				privatemessages.toDeleted = 0 AND
				privatemessages.toArchived = 0
			ORDER BY
				orderDate DESC ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());

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
?>
<div class="content">
	<input name="pmPage" id="pmPage" type="hidden" value="<?php echo $pmPage; ?>" />
	<h3><?php echo $pageName; ?></h3>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-inbox"></i> <?php echo $inboxPage; ?> <span class="text-muted">(<?php echo $inboxtotal; ?>)</span></a></li>
		<li><a href="index.php?page=sent"><i class="fa fa-paper-plane-o"></i> <?php echo $sentNavLink; ?> <span class="text-muted">(<?php echo $senttotal; ?>)</span></a></li>
		<li><a href="index.php?page=archived"><i class="fa fa-archive"></i> <?php echo $archiveNavLink; ?> <span class="text-muted">(<?php echo $arctotal; ?>)</span></a></li>
		<li class="pull-right"><a href="index.php?page=compose" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $composeNavLink; ?></a></li>
	</ul>
</div>

<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>
	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $inboxEmptyMsg; ?>
		</div>
	<?php } else { ?>
		<div class="row">
			<div class="col-md-6">
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo $fromField; ?></th>
							<th><?php echo $subjectField; ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								if ($row['toRead'] == '0') { $isUnread = 'isUnread'; } else { $isUnread = ''; }
						?>
								<tr class="msgLink <?php echo $isUnread; ?>">
									<td class="name" data-th="<?php echo $fromField; ?>"><?php echo clean($row['receivedFrom']); ?></td>
									<td class="subject" data-th="<?php echo $subjectField; ?>"><?php echo clean($row['messageTitle']); ?></td>
									<input name="time" type="hidden" value="<?php echo $row['messageDate']; ?>" />
									<input name="msgTxt" type="hidden" value="<?php echo nl2br(htmlspecialchars($row['messageText'])); ?>" />
									<input name="messageId" type="hidden" value="<?php echo $row['messageId']; ?>" />
									<input name="toRead" type="hidden" value="<?php echo $row['toRead']; ?>" />
									<td data-th="<?php echo $dateRcvdField; ?>"><?php echo $row['listDate']; ?></td>
								</tr>

								<div id="reply<?php echo $row['messageId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header modal-primary">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
												<h4 class="modal-title"><?php echo $sendReplyMsgModal; ?></h4>
											</div>
											<form action="" method="post">
												<div class="modal-body">
													<div class="form-group">
														<label for="messageTitle"><?php echo $subjectField; ?> <sup><?php echo $reqField; ?></sup></label>
														<input type="text" class="form-control" required="" name="messageTitle" value="re: <?php echo clean($row['messageTitle']); ?>" />
													</div>
													<div class="form-group">
														<label for="messageText"><?php echo $messageField; ?> <sup><?php echo $reqField; ?></sup></label>
														<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
													</div>
												</div>

												<div class="modal-footer">
													<input type="hidden" name="toId" value="<?php echo $row['fromId']; ?>" />
													<input type="hidden" name="origId" value="<?php echo $row['messageId']; ?>" />
													<button type="input" name="submit" value="replyToMessage" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendReplyBtn; ?></button>
													<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
												</div>
											</form>

										</div>
									</div>
								</div>

								<div class="modal fade" id="delete<?php echo $row['messageId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deleteMsgConf.': '.clean($row['messageTitle']); ?>?</p>
												</div>
												<div class="modal-footer">
													<input name="deleteId" type="hidden" value="<?php echo $row['messageId']; ?>" />
													<button type="input" name="submit" value="deleteMsg" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
													<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
												</div>
											</form>
										</div>
									</div>
								</div>
						<?php } ?>
					</tbody>
				</table>
			<?php
				if ($total > $pagPages) {
					echo $pages->page_links();
				}
			?>
			</div>
			<div class="col-md-6">
				<h4 class="msgHeading">
					<span class="whoFrom"></span> <i class="fa fa-long-arrow-right msgIcon"></i> <?php echo $youText; ?>
					<span class="pull-right theDate"></span>
				</h4>
				<span class="pull-right msgOptions"></span>
				<div class="clearfix"></div>
				<p class="msgQuip text-muted no-margin">
					<?php if(mysqli_num_rows($res) > 0) { ?>
						<?php echo $selectPrivMsgQuip; ?>
					<?php } ?>
				</p>
				<div class="msgContent"></div>
			</div>
		</div>
	<?php } ?>
</div>