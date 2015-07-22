<?php
	$jsFile = 'privateMessages';
	$pagPages = '20';
	$pmPage = 'sent';

	// Mark Message as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMsg') {
		$messageId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET fromDeleted = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($sentMsgDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
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
			privatemessages.fromId = ".$empId." AND
			privatemessages.fromDeleted = 0
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
				CONCAT(employees.empFirst,' ',employees.empLast) AS recipient
			FROM
				privatemessages
				LEFT JOIN employees ON privatemessages.toId = employees.empId
			WHERE
				privatemessages.fromId = ".$empId." AND
				privatemessages.fromDeleted = 0
			ORDER BY
				orderDate DESC ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	// Get Total Inbox Message Count
	$inboxsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 0";
	$inboxtot = mysqli_query($mysqli, $inboxsql) or die('-2'.mysqli_error());
	$inboxtotal = mysqli_num_rows($inboxtot);

	// Get Total Sent Message Count
	$sentsql = "SELECT 'X' FROM privatemessages WHERE fromId = ".$empId." AND privatemessages.fromDeleted = 0";
	$senttot = mysqli_query($mysqli, $sentsql) or die('-3'.mysqli_error());
	$senttotal = mysqli_num_rows($senttot);

	// Get Total Archived Message Count
	$arcsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toDeleted = 0 AND toArchived = 1";
	$arctot = mysqli_query($mysqli, $arcsql) or die('-4'.mysqli_error());
	$arctotal = mysqli_num_rows($arctot);

	include 'includes/navigation.php';
?>
<div class="content">
	<input name="pmPage" id="pmPage" type="hidden" value="<?php echo $pmPage; ?>" />
	<h3><?php echo $pageName; ?></h3>

	<ul class="nav nav-tabs">
		<li><a href="index.php?page=inbox"><i class="fa fa-inbox"></i> <?php echo $inboxPage; ?> <span class="text-muted">(<?php echo $inboxtotal; ?>)</span></a></li>
		<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-paper-plane-o"></i> <?php echo $sentNavLink; ?> <span class="text-muted">(<?php echo $senttotal; ?>)</span></a></li>
		<li><a href="index.php?page=archived"><i class="fa fa-archive"></i> <?php echo $archiveNavLink; ?> <span class="text-muted">(<?php echo $arctotal; ?>)</span></a></li>
		<li class="pull-right"><a href="index.php?page=compose" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $composeNavLink; ?></a></li>
	</ul>
</div>

<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>
	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $emptySentMsg; ?>
		</div>
	<?php } else { ?>
		<div class="row">
			<div class="col-md-6">
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo $recipientField; ?></th>
							<th><?php echo $subjectField; ?></th>
							<th><?php echo $dateSentField; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php while ($row = mysqli_fetch_assoc($res)) { ?>
							<tr class="msgLink">
								<td class="name" data-th="<?php echo $recipientField; ?>"><?php echo clean($row['recipient']); ?></td>
								<td class="subject" data-th="<?php echo $subjectField; ?>"><?php echo clean($row['messageTitle']); ?></td>
								<input name="time" type="hidden" value="<?php echo $row['messageDate']; ?>" />
								<input name="msgTxt" type="hidden" value="<?php echo nl2br(htmlspecialchars($row['messageText'])); ?>" />
								<input name="messageId" type="hidden" value="<?php echo $row['messageId']; ?>" />
								<input name="toRead" type="hidden" value="<?php echo $row['toRead']; ?>" />
								<td data-th="<?php echo $dateSentField; ?>"><?php echo $row['listDate']; ?></td>
							</tr>

							<div class="modal fade" id="delete<?php echo $row['messageId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteMsgConf.' '.clean($row['messageTitle']); ?>?</p>
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
					You <i class="fa fa-long-arrow-right msgIcon"></i> <span class="whoTo"></span>
					<span class="pull-right theDate"></span>
				</h4>
				<span class="pull-right msgOptions"></span>
				<div class="clearfix"></div>
				<p class="msgQuip text-muted no-margin">
					<?php if(mysqli_num_rows($res) > 0) { ?>
						<?php echo $selectSentMsgQuip; ?>
					<?php } ?>
				</p>
				<div class="msgContent"></div>
			</div>
		</div>
	<?php } ?>
</div>