<?php
	$pagPages = '10';
	$table_name_emptasks = $wpdb->base_prefix . 'tsh_emptasks';
	$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
	//global $current_user;
	//get_currentuserinfo();
	//$user_id = $current_user->ID;

	// Complete Task
	if (isset($_POST['submit']) && $_POST['submit'] == 'completeTask') {
		$completeId = $mysql->real_escape_string($_POST['completeId']);
		$taskStatus = 'Closed';
		$isClosed = '1';
		$dateClosed = current_time("Y-m-d H:i:s");
		

		$stmt = $wpdb->update($table_name_emptasks, array('taskStatus' => $taskStatus, 'isClosed' => $isClosed,'dateClosed' => $dateClosed), array('empTaskId' => $completeId));
		//$stmt = $mysqli->prepare("UPDATE	emptasks SET taskStatus = ?,isClosed = ?,dateClosed = ?	WHERE	empTaskId = ?");
		//$stmt->bind_param('ssss', $taskStatus, $isClosed, $dateClosed, $completeId);
		//$stmt->execute();
		$msgBox = alertBox($taskMarkedCmpMsg, "<i class='fa fa-check-square'></i>", "success");
		//$stmt->close();
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
	//include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	//$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$wpdb->get_results($query = "SELECT * FROM $table_name_emptasks WHERE assignedTo = $user_id AND isClosed = 0");
	$total = $wpdb->num_rows; 

	// Pass the number of total records
	//$pages->set_total($total);

	// Get Data
	$res = $wpdb-> get_results( $query = "SELECT 
				$table_name_emptasks.empTaskId,
				$table_name_emptasks.createdBy,
				$table_name_emptasks.taskTitle,
				$table_name_emptasks.taskDesc,
				$table_name_emptasks.taskPriority,
				$table_name_emptasks.taskStatus,
				DATE_FORMAT($table_name_emptasks.taskStart,'%M %d, %Y') AS startDate,
				DATE_FORMAT($table_name_emptasks.taskDue,'%M %d, %Y') AS dueDate,
				UNIX_TIMESTAMP($table_name_emptasks.taskDue) AS orderDate,
				CONCAT($table_name_employees.empFirst,' ',$table_name_employees.empLast) AS postedBy
			FROM
				$table_name_emptasks
				LEFT JOIN $table_name_employees ON $table_name_emptasks.createdBy = $table_name_employees.user_id
			WHERE
				$table_name_emptasks.assignedTo = ".$user_id." AND emptasks.isClosed = 0
			ORDER BY
				orderDate ".$pagPages,ARRAY_A);
    $res_numrows = $wpdb->num_rows;

	
?>
<div class="content">
	<h3><?php //echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-tasks"></i> <?php echo $openTasksNavLink; ?></a></li>
		<li><a href="index.php?page=closedtasks"><i class="fa fa-check-square"></i> <?php echo $closedTasksNavLink; ?></a></li>
		<li class="pull-right"><a href="?page=newtask" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newTaskNavLink; ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane in active" id="home">
			<?php if($res_numrows < 1) { ?>
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
						<?php foreach ($res as $row) { ?>
							print_r($row);
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
					//if ($total > $pagPages) {
					//	echo $pages->page_links();
					//}
				}
			?>
		</div>
	</div>
</div>