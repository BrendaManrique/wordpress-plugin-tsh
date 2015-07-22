<?php
	$fullcalendar = 'true';
	$calinclude = 'true';
	$datePicker = 'true';
	$jsFile = 'calendar';

	// Add New Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'newEvent') {
		// Validations
		if($_POST['startDate'] == '') {
			$msgBox = alertBox($startDateReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['endDate'] == '') {
			$msgBox = alertBox($endDateReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			if (($isAdmin == '1') || ($isMgr == '1')) {
				$isPublic = $mysqli->real_escape_string($_POST['isPublic']);
				$admin = '1';
			} else {
				$isPublic = $admin = '0';
			}
			$isShared = $mysqli->real_escape_string($_POST['isShared']);
			$dateOfEvent = $mysqli->real_escape_string($_POST['startDate']);
			$timeOfEvent = $mysqli->real_escape_string($_POST['eventTime']);
			$startDate = $dateOfEvent.' '.$timeOfEvent.':00';
			$endOfEvent = $mysqli->real_escape_string($_POST['endDate']);
			$endTimeOfEvent = $mysqli->real_escape_string($_POST['endTime']);
			$endDate = $endOfEvent.' '.$endTimeOfEvent.':00';
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventDesc = $mysqli->real_escape_string($_POST['eventDesc']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									calendarevents(
										empId,
										isAdmin,
										isShared,
										isPublic,
										startDate,
										endDate,
										eventTitle,
										eventDesc
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssssss',
								$empId,
								$admin,
								$isShared,
								$isPublic,
								$startDate,
								$endDate,
								$eventTitle,
								$eventDesc
			);
			$stmt->execute();
			$msgBox = alertBox($newEventSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['startDate'] = $_POST['eventTime'] = $_POST['endDate'] = $_POST['endTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = '';
			$stmt->close();
		}
	}

	// Edit Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'editEvent') {
		if($_POST['startDate'] == '') {
			$msgBox = alertBox($startDateReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['endDate'] == '') {
			$msgBox = alertBox($endDateReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			$eventId = $mysqli->real_escape_string($_POST['eventId']);
			$dateOfEvent = $mysqli->real_escape_string($_POST['startDate']);
			$timeOfEvent = $mysqli->real_escape_string($_POST['eventTime']);
			$startDate = $dateOfEvent.' '.$timeOfEvent.':00';
			$endOfEvent = $mysqli->real_escape_string($_POST['endDate']);
			$endTimeOfEvent = $mysqli->real_escape_string($_POST['endTime']);
			$endDate = $endOfEvent.' '.$endTimeOfEvent.':00';
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventDesc = $mysqli->real_escape_string($_POST['eventDesc']);

			$stmt = $mysqli->prepare("
								UPDATE
									calendarevents
								SET
									startDate = ?,
									endDate = ?,
									eventTitle = ?,
									eventDesc = ?
								WHERE
									eventId = ?
			");
			$stmt->bind_param('sssss',
								$startDate,
								$endDate,
								$eventTitle,
								$eventDesc,
								$eventId

			);
			$stmt->execute();
			$msgBox = alertBox($eventUpdMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['startDate'] = $_POST['eventTime'] = $_POST['endDate'] = $_POST['endTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = '';
			$stmt->close();
		}
	}

	// Delete Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteEvent') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$isValid = '';

		if ($isMgr != '1') {
			// Check if the Event Belongs to the logged in User
			$check = $mysqli->query("SELECT 'X' FROM calendarevents WHERE eventId = '".$deleteId."' AND empId = ".$empId);
			if ($check->num_rows) {
				$isValid = 'true';
			}

			if ($isValid != '') {
				// Yup, Allow the Delete
				$stmt = $mysqli->prepare("DELETE FROM calendarevents WHERE eventId = ?");
				$stmt->bind_param('s', $deleteId);
				$stmt->execute();
				$stmt->close();
				$msgBox = alertBox($eventDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				// Nope, show an error
				$msgBox = alertBox($eventDeleteError, "<i class='fa fa-times-circle'></i>", "danger");
			}
		} else if ($isMgr == '1') {
			// Manager/Admin - Allow the Delete
			$stmt = $mysqli->prepare("DELETE FROM calendarevents WHERE eventId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$stmt->close();
			$msgBox = alertBox($eventDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox($eventDeleteError, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }

	include 'includes/navigation.php';
?>
<div class="content">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div id="calendar"></div>
	<p class="text-muted no-margin mt10"><?php echo $calendarQuip; ?></p>
</div>

<div id="" class="modal fade viewEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><span class="event-title"></span></h4>
			</div>
			<div class="modal-body event-padding">
				<p class="event-desc"></p>
			</div>
			<div class="modal-footer">
				<div class="event-actions"></div>
			</div>
		</div>
	</div>
</div>

<div id="" class="modal fade editEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $editEventModal; ?> <span class="event-modal-title"></span></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="startDate"><?php echo $startDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="startDate" id="editstartDate" required="" value="" />
								<span class="help-block"><?php echo $dateFormatHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTime"><?php echo $startTimeField; ?></label>
								<input type="text" class="form-control" name="eventTime" id="editeventTime" value="" />
								<span class="help-block"><?php echo $startTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="endDate"><?php echo $endDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="endDate" id="editendDate" required="" value="" />
								<span class="help-block"><?php echo $dateFormatHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="endTime"><?php echo $endTimeField; ?></label>
								<input type="text" class="form-control" name="endTime" id="editendTime" value="" />
								<span class="help-block"><?php echo $startTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="eventTitle"><?php echo $eventTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control titleField" name="eventTitle" required="" maxlength="50" value="" />
						<span class="help-block"><?php echo $max50Characs; ?></span>
					</div>
					<div class="form-group">
						<label for="eventDesc"><?php echo $eventDescField; ?></label>
						<textarea class="form-control descField" name="eventDesc" rows="4"></textarea>
						<span class="help-block"><?php echo $eventDescFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="eventId" class="event-id" value="" />
					<button type="input" name="submit" value="editEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="" class="modal fade deleteEvent" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $deleteEventConf; ?> <span class="event-modal-title"></span>?</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="deleteId" class="event-id" value="" />
					<button type="input" name="submit" value="deleteEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="newEvent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newEvent" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $newEventModal; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<?php if (($isAdmin == '1') || ($isMgr == '1')) { ?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="isPublic"><?php echo $publicEventField; ?></label>
									<select class="form-control" name="isPublic">
										<option value="0"><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
									<span class="help-block"><?php echo $publicEventFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="isShared"><?php echo $shareEventField; ?></label>
									<select class="form-control" name="isShared">
										<option value="0"><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
									<span class="help-block"><?php echo $shareEventFieldHelp; ?></span>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="form-group">
							<label for="isShared"><?php echo $shareEventField; ?></label>
							<select class="form-control" name="isShared">
								<option value="0"><?php echo $noBtn; ?></option>
								<option value="1"><?php echo $yesBtn; ?></option>
							</select>
							<span class="help-block"><?php echo $shareEventFieldHelp; ?></span>
						</div>
					<?php } ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="startDate"><?php echo $startDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="startDate" id="newstartDate" required="" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>" />
								<span class="help-block"><?php echo $dateFormatHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTime"><?php echo $startTimeField; ?></label>
								<input type="text" class="form-control" name="eventTime" id="neweventTime" value="<?php echo isset($_POST['eventTime']) ? $_POST['eventTime'] : ''; ?>" />
								<span class="help-block"><?php echo $startTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="endDate"><?php echo $endDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="endDate" id="newendDate" required="" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>" />
								<span class="help-block"><?php echo $dateFormatHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="endTime"><?php echo $endTimeField; ?></label>
								<input type="text" class="form-control" name="endTime" id="newendTime" value="<?php echo isset($_POST['endTime']) ? $_POST['endTime'] : ''; ?>" />
								<span class="help-block"><?php echo $startTimeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="eventTitle"><?php echo $eventTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" name="eventTitle" required="" maxlength="50" value="<?php echo isset($_POST['eventTitle']) ? $_POST['eventTitle'] : ''; ?>" />
						<span class="help-block"><?php echo $max50Characs; ?></span>
					</div>
					<div class="form-group">
						<label for="eventDesc"><?php echo $eventDescField; ?></label>
						<textarea class="form-control" name="eventDesc" rows="4"><?php echo isset($_POST['eventDesc']) ? $_POST['eventDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $eventDescFieldHelp; ?></span>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="newEvent" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveNewEvtBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>