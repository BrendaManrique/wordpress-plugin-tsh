<?php
	$datePicker = 'true';
	$jsFile = 'siteNotices';
	$pagPages = '10';
	$count = 0;

	// Edit Site Notification
    if (isset($_POST['submit']) && $_POST['submit'] == 'editNotice') {
        // Validation
		if($_POST['noticeTitle'] == "") {
            $msgBox = alertBox($noteTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['noticeText'] == "") {
            $msgBox = alertBox($noteTextReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$noticeId = $mysqli->real_escape_string($_POST['noticeId']);
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$noticeTitle = $mysqli->real_escape_string($_POST['noticeTitle']);
			$noticeText = htmlentities($_POST['noticeText']);
			$noticeStart = $mysqli->real_escape_string($_POST['noticeStart']).' 00:00:00';
			$noticeExpires = $mysqli->real_escape_string($_POST['noticeExpires']).' 00:00:00';

            $stmt = $mysqli->prepare("UPDATE
										notices
									SET
										isActive = ?,
										noticeTitle = ?,
										noticeText = ?,
										noticeStart = ?,
										noticeExpires = ?
									WHERE
										noticeId = ?"
			);
			$stmt->bind_param('ssssss',
									$isActive,
									$noticeTitle,
									$noticeText,
									$noticeStart,
									$noticeExpires,
									$noticeId
			);
			$stmt->execute();
			$msgBox = alertBox($siteNoticeUpdMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['noticeTitle'] = $_POST['noticeText'] = $_POST['noticeStart'] = $_POST['noticeExpires'] = '';
			$stmt->close();
		}
	}

	// Delete Site Notification
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteNotice') {
		$noticeId = $mysqli->real_escape_string($_POST['noticeId']);
		$stmt = $mysqli->prepare("DELETE FROM notices WHERE noticeId = ?");
		$stmt->bind_param('s', $noticeId);
		$stmt->execute();
		$msgBox = alertBox($siteNoticeDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM notices");
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$sqlStmt = "SELECT
					notices.noticeId,
					notices.createdBy,
					notices.isActive,
					notices.noticeTitle,
					notices.noticeText,
					notices.noticeDate,
					DATE_FORMAT(notices.noticeDate,'%M %d, %Y') AS createDate,
					notices.noticeStart,
					DATE_FORMAT(notices.noticeStart,'%M %d, %Y') AS startDate,
					DATE_FORMAT(notices.noticeStart,'%Y-%m-%d') AS showStart,
					notices.noticeExpires,
					DATE_FORMAT(notices.noticeExpires,'%M %d, %Y') AS endDate,
					DATE_FORMAT(notices.noticeExpires,'%Y-%m-%d') AS showEnd,
					CONCAT(employees.empFirst,' ',employees.empLast) AS createdBy
				FROM
					notices
					LEFT JOIN employees ON notices.createdBy = employees.empId ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($isMgr != '1')) {
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
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-bullhorn"></i> <?php echo $siteNoticesNavLink; ?></a></li>
			<li class="pull-right"><a href="index.php?page=newNotice" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newNoticeNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<?php if(mysqli_num_rows($res) < 1) { ?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noNoticesFound; ?>
					</div>
				<?php } else { ?>
					<table class="rwd-table">
						<tbody>
							<tr class="primary">
								<th><?php echo $noticeTitleField; ?></th>
								<th><?php echo $createdByField; ?></th>
								<th><?php echo $dateCreatedField; ?></th>
								<th><?php echo $activeText; ?></th>
								<th><?php echo $startDateField; ?></th>
								<th><?php echo $endDateField; ?></th>
								<th></th>
							</tr>
							<?php
								while ($row = mysqli_fetch_assoc($res)) {
								if ($row['isActive'] == '1') { $active = $yesBtn; $isActive = 'selected'; } else { $active = $noBtn; $isActive = ''; }
								if ($row['showStart'] != '0000-00-00') { $showStart = $row['showStart']; } else { $showStart = ''; }
								if ($row['showEnd'] != '0000-00-00') { $showEnd = $row['showEnd']; } else { $showEnd = ''; }
							?>
									<tr>
										<td data-th="<?php echo $noticeTitleField; ?>">
											<span data-toggle="tooltip" data-placement="right" title="<?php echo $editNoticeTooltip; ?>">
												<a data-toggle="modal" href="#editNotice<?php echo $row['noticeId']; ?>"><?php echo clean($row['noticeTitle']); ?></a>
											</span>
										</td>
										<td data-th="<?php echo $createdByField; ?>"><?php echo clean($row['createdBy']); ?></td>
										<td data-th="<?php echo $dateCreatedField; ?>"><?php echo $row['createDate']; ?></td>
										<td data-th="<?php echo $activeText; ?>"><?php echo $active; ?></td>
										<td data-th="<?php echo $startDateField; ?>"><?php echo $row['startDate']; ?></td>
										<td data-th="<?php echo $endDateField; ?>"><?php echo $row['endDate']; ?></td>
										<td data-th="<?php echo $actionText; ?>">
											<span data-toggle="tooltip" data-placement="left" title="<?php echo $editNoticeTooltip; ?>">
												<a data-toggle="modal" href="#editNotice<?php echo $row['noticeId']; ?>"><i class="fa fa-edit edit"></i></a>
											</span>
											<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteNoticeTooltip; ?>">
												<a data-toggle="modal" href="#deleteNotice<?php echo $row['noticeId']; ?>"><i class="fa fa-trash-o remove"></i></a>
											</span>
										</td>
									</tr>

									<div id="editNotice<?php echo $row['noticeId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
													<h4 class="modal-title"><?php echo $editNoticeModal; ?></h4>
												</div>
												<form action="" method="post">
													<div class="modal-body">
														<p><?php echo $siteNoticesQuip; ?></p>
														<div class="alertDates">
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="noticeStart"><?php echo $startDateField; ?></label>
																		<input type="text" class="form-control" name="noticeStart" id="noticeStart_<?php echo $count; ?>" value="<?php echo $showStart; ?>" />
																		<span class="help-block"><?php echo $noteStartDateHelp; ?></span>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="noticeExpires"><?php echo $endDateField; ?></label>
																		<input type="text" class="form-control" name="noticeExpires" id="noticeExpires_<?php echo $count; ?>" value="<?php echo $showEnd; ?>" />
																		<span class="help-block"><?php echo $noteEndDateHelp; ?></span>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="isActive"><?php echo $noteIsActiveField; ?></label>
																		<select class="form-control" name="isActive">
																			<option value="0"><?php echo $noBtn; ?></option>
																			<option value="1" <?php echo $isActive; ?>><?php echo $yesBtn; ?></option>
																		</select>
																		<span class="help-block"><?php echo $noteIsActiveFieldHelp; ?></span>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="noticeTitle"><?php echo $noteTitleField; ?></label>
															<input type="text" class="form-control" required="" name="noticeTitle" value="<?php echo clean($row['noticeTitle']); ?>" />
														</div>
														<div class="form-group">
															<label for="noticeText"><?php echo $siteNoteTextField; ?></label>
															<textarea class="form-control" required="" name="noticeText" rows="4"><?php echo clean($row['noticeText']); ?></textarea>
														</div>
													</div>
													<div class="modal-footer">
														<input type="hidden" name="noticeId" value="<?php echo $row['noticeId']; ?>" />
														<button type="input" name="submit" value="editNotice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>

									<div class="modal fade" id="deleteNotice<?php echo $row['noticeId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<form action="" method="post">
													<div class="modal-body">
														<p class="lead"><?php echo $deleteNoticeConf; ?></p>
													</div>
													<div class="modal-footer">
														<input name="noticeId" type="hidden" value="<?php echo $row['noticeId']; ?>" />
														<button type="input" name="submit" value="deleteNotice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
							<?php
									$count++;
								}
							?>
						</tbody>
					</table>
				<?php
					}
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
				?>
			</div>
		</div>
	</div>
<?php } ?>