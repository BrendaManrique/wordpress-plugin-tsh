<?php
	$datePicker = 'true';
	$jsFile = 'newNotice';

	// Add New Notification
    if (isset($_POST['submit']) && $_POST['submit'] == 'newNotice') {
        // Validation
		if($_POST['noticeTitle'] == "") {
            $msgBox = alertBox($noteTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['noticeText'] == "") {
            $msgBox = alertBox($noteTextReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$noticeTitle = $mysqli->real_escape_string($_POST['noticeTitle']);
			$noticeText = htmlentities($_POST['noticeText']);
			$noticeStart = $mysqli->real_escape_string($_POST['noticeStart']).' 00:00:00';
			$noticeExpires = $mysqli->real_escape_string($_POST['noticeExpires']).' 00:00:00';
			$noticeDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									notices(
										createdBy,
										isActive,
										noticeTitle,
										noticeText,
										noticeDate,
										noticeStart,
										noticeExpires
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssssss',
								$empId,
								$isActive,
								$noticeTitle,
								$noticeText,
								$noticeDate,
								$noticeStart,
								$noticeExpires
			);
			$stmt->execute();
			$msgBox = alertBox($siteNoteSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['noticeTitle'] = $_POST['noticeText'] = $_POST['noticeStart'] = $_POST['noticeExpires'] = '';
			$stmt->close();
		}
	}

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
			<li><a href="index.php?page=notices"><i class="fa fa-bullhorn"></i> <?php echo $siteNoticesNavLink; ?></a></li>
			<li class="pull-right"><a href="#home" data-toggle="tab" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newNoticeNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<p><?php echo $siteNoticesQuip; ?></p>

				<form action="" method="post">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="noticeStart"><?php echo $startDateField; ?></label>
								<input type="text" class="form-control" name="noticeStart" id="noticeStart" value="<?php echo isset($_POST['noticeStart']) ? $_POST['noticeStart'] : ''; ?>" />
								<span class="help-block"><?php echo $noteStartDateHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="noticeExpires"><?php echo $endDateField; ?></label>
								<input type="text" class="form-control" name="noticeExpires" id="noticeExpires" value="<?php echo isset($_POST['noticeExpires']) ? $_POST['noticeExpires'] : ''; ?>" />
								<span class="help-block"><?php echo $noteEndDateHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="isActive"><?php echo $noteIsActiveField; ?></label>
								<select class="form-control" name="isActive">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $noteIsActiveFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="noticeTitle"><?php echo $noteTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" required="" name="noticeTitle" value="<?php echo isset($_POST['noticeTitle']) ? $_POST['noticeTitle'] : ''; ?>" />
					</div>
					<div class="form-group">
						<label for="noticeText"><?php echo $siteNoteTextField; ?> <sup><?php echo $reqField; ?></sup></label>
						<textarea class="form-control" required="" name="noticeText" rows="4"><?php echo isset($_POST['noticeText']) ? $_POST['noticeText'] : ''; ?></textarea>
					</div>
					<button type="input" name="submit" value="newNotice" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i><?php echo $saveNoteBtn; ?></button>
				</form>
			</div>
		</div>
	</div>
<?php } ?>