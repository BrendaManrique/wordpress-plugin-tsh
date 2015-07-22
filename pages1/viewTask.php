<?php
	$taskId = $_GET['taskId'];
	$datePicker = 'true';
	$jsFile = 'tasks';

	// Edit Task
    if (isset($_POST['submit']) && $_POST['submit'] == 'editTask') {
        // Validation
		if($_POST['taskTitle'] == "") {
            $msgBox = alertBox($taskTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskDesc'] == "") {
            $msgBox = alertBox($taskDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskDue'] == "") {
            $msgBox = alertBox($taskDueDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$taskTitle = $mysqli->real_escape_string($_POST['taskTitle']);
			$taskPriority = $mysqli->real_escape_string($_POST['taskPriority']);
			$taskDue = $mysqli->real_escape_string($_POST['taskDue']);
			$taskDesc = htmlentities($_POST['taskDesc']);
			$taskNotes = htmlentities($_POST['taskNotes']);
			$isClosed = $mysqli->real_escape_string($_POST['isClosed']);
			if ($isClosed == '1') {
				$taskStatus = 'Closed';
				$dateClosed = date("Y-m-d H:i:s");
			} else {
				$taskStatus = $mysqli->real_escape_string($_POST['taskStatus']);
				$dateClosed = '0000-00-00 00:00:00';
			}

            $stmt = $mysqli->prepare("UPDATE
										emptasks
									SET
										taskTitle = ?,
										taskDesc = ?,
										taskNotes = ?,
										taskPriority = ?,
										taskStatus = ?,
										taskDue = ?,
										isClosed = ?,
										dateClosed = ?
									WHERE
										empTaskId = ?"
			);
			$stmt->bind_param('sssssssss',
									$taskTitle,
									$taskDesc,
									$taskNotes,
									$taskPriority,
									$taskStatus,
									$taskDue,
									$isClosed,
									$dateClosed,
									$taskId
			);
			$stmt->execute();
			$msgBox = alertBox($taskUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				emptasks.empTaskId,
				emptasks.assignedTo,
				emptasks.createdBy,
				emptasks.taskTitle,
				emptasks.taskDesc,
				emptasks.taskNotes,
				emptasks.taskPriority,
				emptasks.taskStatus,
				DATE_FORMAT(emptasks.taskStart,'%M %d, %Y') AS startDate,
				DATE_FORMAT(emptasks.taskDue,'%Y-%m-%d') AS showDue,
				DATE_FORMAT(emptasks.taskDue,'%M %d, %Y') AS dueDate,
				emptasks.isClosed,
				DATE_FORMAT(emptasks.dateClosed,'%M %d, %Y') AS dateClosed,
				CONCAT(employees.empFirst,' ',employees.empLast) AS postedBy
			FROM
				emptasks
				LEFT JOIN employees ON emptasks.createdBy = employees.empId
			WHERE
				emptasks.empTaskId = ".$taskId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['isClosed'] == '1') {
		$curStatus = '<strong class="text-success">'.clean($row['taskStatus']).' on '.$row['dateClosed'].'</strong>';
	} else {
		$curStatus = clean($row['taskStatus']);
	}

	include 'includes/navigation.php';

	if ($row['assignedTo'] != $empId) {
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
			<li><a href="index.php?page=tasks"><i class="fa fa-tasks"></i> <?php echo $openTasksNavLink; ?></a></li>
			<li><a href="index.php?page=closedTasks"><i class="fa fa-check-square"></i> <?php echo $closedTasksNavLink; ?></a></li>
			<li class="pull-right"><a href="index.php?page=newTask" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newTaskNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<div class="row">
					<div class="col-md-6">
						<table class="infoTable no-margin">
							<tr>
								<td class="infoKey"><?php echo $taskTitleField; ?>:</td>
								<td class="infoVal"><?php echo clean($row['taskTitle']); ?></td>
							</tr>
							<tr>
								<td class="infoKey"><?php echo $dateCreatedField; ?>:</td>
								<td class="infoVal"><?php echo $row['startDate']; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><?php echo $priorityField; ?>:</td>
								<td class="infoVal"><?php echo clean($row['taskPriority']); ?></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="infoTable no-margin">
							<tr>
								<td class="infoKey"><?php echo $createdByField; ?>:</td>
								<td class="infoVal"><?php echo clean($row['postedBy']); ?></td>
							</tr>
							<tr>
								<td class="infoKey"><?php echo $dateDueField; ?>:</td>
								<td class="infoVal"><?php echo $row['dueDate']; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><?php echo $statusField; ?>:</td>
								<td class="infoVal"><?php echo $curStatus; ?></td>
							</tr>
						</table>
					</div>
				</div>

				<div class="well well-sm mt20">
					<strong><?php echo $taskDescField; ?>:</strong> <?php echo nl2br(clean($row['taskDesc'])); ?>
				</div>

				<?php if (!empty($row['taskNotes'])) { ?>
					<div class="well well-sm mt20">
						<strong><?php echo $taskNotesField; ?>:</strong> <?php echo nl2br(clean($row['taskNotes'])); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="content">
		<h3><?php echo $editTaskForm; ?></h3>

		<form action="" method="post">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="taskTitle"><?php echo $taskTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" required="" name="taskTitle" value="<?php echo clean($row['taskTitle']); ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="taskDue"><?php echo $dateDueField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" required="" name="taskDue" id="taskDue" value="<?php echo $row['showDue']; ?>" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="taskPriority"><?php echo $priorityField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" required="" name="taskPriority" value="<?php echo clean($row['taskPriority']); ?>" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="taskStatus"><?php echo $statusField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" required="" name="taskStatus" value="<?php echo clean($row['taskStatus']); ?>" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="taskDesc"><?php echo $taskDescField; ?> <sup><?php echo $reqField; ?></sup></label>
				<textarea class="form-control" required="" name="taskDesc" rows="4"><?php echo clean($row['taskDesc']); ?></textarea>
			</div>
			<div class="form-group">
				<label for="taskNotes"><?php echo $taskNotesField; ?></label>
				<textarea class="form-control" name="taskNotes" rows="4"><?php echo clean($row['taskNotes']); ?></textarea>
			</div>
			<?php if ($row['isClosed'] == '0') { ?>
				<div class="form-group">
					<label for="isClosed"><?php echo $markTaskCmpTooltip; ?>?</label>
					<select class="form-control" name="isClosed">
						<option value="0"><?php echo $noBtn; ?></option>
						<option value="1"><?php echo $yesBtn; ?></option>
					</select>
				</div>
			<?php } else { ?>
				<div class="form-group">
					<label for="isClosed"><?php echo $reopenTaskTooltip; ?>?</label>
					<select class="form-control" name="isClosed">
						<option value="1"><?php echo $noBtn; ?></option>
						<option value="0"><?php echo $yesBtn; ?></option>
					</select>
				</div>
			<?php } ?>
			<button type="input" name="submit" value="editTask" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateTaskBtn; ?></button>
		</form>
	</div>
<?php } ?>