<?php
	$pagPages = '10';

	// Complete Task
	if (isset($_POST['submit']) && $_POST['submit'] == 'completeTask') {
		$completeId = $mysqli->real_escape_string($_POST['completeId']);
		$taskStatus = 'Closed';
		$isClosed = '1';
		$dateClosed = current_time("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("UPDATE
									emptasks
								SET
									taskStatus = ?,
									isClosed = ?,
									dateClosed = ?
								WHERE
									empTaskId = ?"
		);
		$stmt->bind_param('ssss', $taskStatus, $isClosed, $dateClosed, $completeId);
		$stmt->execute();
		$msgBox = alertBox($taskMarkedCmpMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Delete Task
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTask') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM emptasks WHERE empTaskId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$msgBox = alertBox($taskDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM emptasks WHERE assignedTo = ".$empId." AND isClosed = 0");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				emptasks.empTaskId,
				emptasks.createdBy,
				emptasks.taskTitle,
				emptasks.taskDesc,
				emptasks.taskPriority,
				emptasks.taskStatus,
				DATE_FORMAT(emptasks.taskStart,'%M %d, %Y') AS startDate,
				DATE_FORMAT(emptasks.taskDue,'%M %d, %Y') AS dueDate,
				UNIX_TIMESTAMP(emptasks.taskDue) AS orderDate,
				CONCAT(employees.empFirst,' ',employees.empLast) AS postedBy
			FROM
				emptasks
				LEFT JOIN employees ON emptasks.createdBy = employees.empId
			WHERE
				emptasks.assignedTo = ".$empId." AND emptasks.isClosed = 0
			ORDER BY
				orderDate ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-tasks"></i> <?php echo $openTasksNavLink; ?></a></li>
		<li><a href="index.php?page=closedTasks"><i class="fa fa-check-square"></i> <?php echo $closedTasksNavLink; ?></a></li>
		<li class="pull-right"><a href="index.php?page=newTask" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newTaskNavLink; ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane in active" id="home">
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default">
					<i class="fa fa-minus-square-o"></i> <?php echo $noOpenTasksFound; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table">
					<tbody>
						<tr class="primary">
							<th><?php echo $taskTitleField; ?></th>
							<th><?php echo $createdByField; ?></th>
							<th><?php echo $priorityField; ?></th>
							<th><?php echo $statusField; ?></th>
							<th><?php echo $dateCreatedField; ?></th>
							<th><?php echo $dateDueField; ?></th>
							<th></th>
						</tr>
						<?php while ($row = mysqli_fetch_assoc($res)) { ?>
							<tr>
								<td data-th="<?php echo $taskTitleField; ?>">
									<a href="index.php?page=viewTask&taskId=<?php echo $row['empTaskId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewTaskTooltip; ?>">
										<?php echo clean($row['taskTitle']); ?>
									</a>
								</td>
								<td data-th="<?php echo $createdByField; ?>"><?php echo clean($row['postedBy']); ?></td>
								<td data-th="<?php echo $priorityField; ?>"><?php echo clean($row['taskPriority']); ?></td>
								<td data-th="<?php echo $statusField; ?>"><?php echo clean($row['taskStatus']); ?></td>
								<td data-th="<?php echo $dateCreatedField; ?>"><?php echo $row['startDate']; ?></td>
								<td data-th="<?php echo $dateDueField; ?>"><?php echo $row['dueDate']; ?></td>
								<td data-th="<?php echo $actionText; ?>">
									<a href="index.php?page=viewTask&taskId=<?php echo $row['empTaskId']; ?>">
										<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewTaskTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#completeTask<?php echo $row['empTaskId']; ?>">
										<i class="fa fa-check-square-o text-success" data-toggle="tooltip" data-placement="left" title="<?php echo $markTaskCmpTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#deleteTask<?php echo $row['empTaskId']; ?>">
										<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTaskTooltip; ?>"></i>
									</a>
								</td>
							</tr>

							<div class="modal fade" id="completeTask<?php echo $row['empTaskId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $completeTaskText.' '.clean($row['taskTitle']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="completeId" type="hidden" value="<?php echo $row['empTaskId']; ?>" />
												<button type="input" name="submit" value="completeTask" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>

							<div class="modal fade" id="deleteTask<?php echo $row['empTaskId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteTaskConf.' '.clean($row['taskTitle']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="deleteId" type="hidden" value="<?php echo $row['empTaskId']; ?>" />
												<button type="input" name="submit" value="deleteTask" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
				}
			?>
		</div>
	</div>
</div>