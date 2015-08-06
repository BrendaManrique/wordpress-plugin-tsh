<?php
	$datePicker = 'true';
	$jsFile = 'tasks';

	// Add New Task
    if (isset($_POST['submit']) && $_POST['submit'] == 'addNewTask') {
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
			$taskStatus = $mysqli->real_escape_string($_POST['taskStatus']);
			$taskDue = $mysqli->real_escape_string($_POST['taskDue']);
			$taskStart = date("Y-m-d H:i:s");
			$taskDesc = htmlentities($_POST['taskDesc']);
			$assignedTo = $mysqli->real_escape_string($_POST['assignedTo']);

			// If Manager/Admin
			if (($isMgr == '1') || ($isAdmin == '1')) {
				// Check if the Task is assigned to someone other then the logged in Manager/Admin
				if ($assignedTo == 'self') {
					$assignTo = $empId;
				} else {
					$assignTo = $assignedTo;
				}
			} else {
				// Assign it to the logged in User
				$assignTo = $empId;
			}

			$stmt = $mysqli->prepare("
								INSERT INTO
									emptasks(
										assignedTo,
										createdBy,
										taskTitle,
										taskDesc,
										taskPriority,
										taskStatus,
										taskStart,
										taskDue
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
								$assignTo,
								$empId,
								$taskTitle,
								$taskDesc,
								$taskPriority,
								$taskStatus,
								$taskStart,
								$taskDue
			);
			$stmt->execute();
			$stmt->close();

			if (isset($_POST['addCal']) && $_POST['addCal'] == '1') {
				$startDate = $endDate = $taskDue.' 00:00:00';
				$eventTitle = 'Task: '.$taskTitle;
				$eventDesc = $mysqli->real_escape_string($_POST['taskDesc']);

				// Check if this is a Manager/Admin
				if (($isMgr == '1') || ($isAdmin == '1')) {
					$isAdmin = '1';
				} else {
					$isAdmin = '0';
				}

				$stmt = $mysqli->prepare("
									INSERT INTO
										calendarevents(
											empId,
											isAdmin,
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
											?
										)
				");
				$stmt->bind_param('ssssss',
									$empId,
									$isAdmin,
									$startDate,
									$endDate,
									$eventTitle,
									$eventDesc
				);
				$stmt->execute();
				$stmt->close();
				$msgBox = alertBox($taskAddedMsg1, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($taskAddedMsg2, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the Form of values
			$_POST['taskTitle'] = $_POST['taskDesc'] = $_POST['taskPriority'] = $_POST['taskStatus'] = $_POST['taskDue'] = '';
		}
	}


?>
<div class="content">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
		<li><a href="?page=tasks"><i class="fa fa-tasks"></i> <?php echo $openTasksNavLink; ?></a></li>
		<li><a href="?page=closedtasks"><i class="fa fa-check-square"></i> <?php echo $closedTasksNavLink; ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane in active" id="home">
			<form action="" method="post">
				<?php if (($isAdmin == '1') || ($isMgr == '1')) { ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="taskTitle"><?php echo $taskTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="taskTitle" required="" maxlength="50" value="<?php echo isset($_POST['taskTitle']) ? $_POST['taskTitle'] : ''; ?>" />
								<span class="help-block"><?php echo $max50Characs; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="assignedTo"><?php echo $assignTaskField; ?></label>
								<select class="form-control" name="assignedTo">
									<?php
										$empSql = "SELECT empId, CONCAT(empFirst,' ',empLast) AS employee FROM employees WHERE empId != ".$empId." AND isActive = 1";
										$empres = mysqli_query($mysqli, $empSql) or die('-1'.mysqli_error());
									?>
									<option value="self"><?php echo $assignTaskField1; ?></option>
									<?php while ($row = mysqli_fetch_assoc($empres)) { ?>
										<option value="<?php echo $row['empId']; ?>"><?php echo clean($row['employee']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $assignTaskFieldHelp; ?></span>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="form-group">
						<label for="taskTitle"><?php echo $taskTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" name="taskTitle" required="" maxlength="50" value="<?php echo isset($_POST['taskTitle']) ? $_POST['taskTitle'] : ''; ?>" />
						<span class="help-block"><?php echo $max50Characs; ?></span>
					</div>
				<?php } ?>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="taskPriority"><?php echo $priorityField; ?> <sup><?php echo $reqField; ?></sup></label>
							<input type="text" class="form-control" name="taskPriority" required="" value="<?php echo isset($_POST['taskPriority']) ? $_POST['taskPriority'] : ''; ?>" />
						</div>
						<div class="form-group">
							<label for="taskDue"><?php echo $taskDueField; ?> <sup><?php echo $reqField; ?></sup></label>
							<input type="text" class="form-control" name="taskDue" id="taskDue" required="" value="<?php echo isset($_POST['taskDue']) ? $_POST['taskDue'] : ''; ?>" />
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="addCal" value="1">
								<?php echo $addToCalField; ?>
							</label>
						</div>
						<span class="help-block"><?php echo $addToCalFieldHelp; ?></span>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="taskStatus"><?php echo $statusField; ?> <sup><?php echo $reqField; ?></sup></label>
							<input type="text" class="form-control" name="taskStatus" required="" value="<?php echo isset($_POST['taskStatus']) ? $_POST['taskStatus'] : ''; ?>" />
						</div>
						<div class="form-group">
							<label for="taskDesc"><?php echo $taskDescField; ?> <sup><?php echo $reqField; ?></sup></label>
							<textarea class="form-control" required="" name="taskDesc" rows="5"><?php echo isset($_POST['taskDesc']) ? $_POST['taskDesc'] : ''; ?></textarea>
						</div>
					</div>
				</div>
				<button type="input" name="submit" value="addNewTask" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveNewTaskBtn; ?></button>
			</form>
		</div>
	</div>
</div>