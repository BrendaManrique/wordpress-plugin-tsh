<?php
	$date = $_GET['date'];
	$empId = $_GET['empId'];
	$datePicker = 'true';
	
	// Delete Time Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTime') {
		$entryId = $mysqli->real_escape_string($_POST['entryId']);
		$stmt = $mysqli->prepare("DELETE FROM timeentry WHERE entryId = ?");
		$stmt->bind_param('s', $entryId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($timeEntryDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }
	
	// Get Data
	$query = "SELECT
				timeentry.entryId,
				timeentry.clockId,
				timeentry.empId,
				timeentry.entryDate,
				DATE_FORMAT(timeentry.entryDate,'%M %d, %Y') AS eDate,
				DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
				timeentry.startTime,
				timeentry.endTime,
				DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
				timeentry.entryType,
				timeclock.clockId,
				timeclock.weekNo,
				timeclock.clockYear,
				timeclock.running,
				CONCAT(employees.empFirst,' ',employees.empLast) AS theEmp
			FROM
				timeentry
				LEFT JOIN timeclock ON timeentry.clockId = timeclock.clockId
				LEFT JOIN employees ON timeentry.empId = employees.empId
			WHERE
				timeentry.entryDate = '".$date."' AND
				timeentry.empId = ".$empId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	
	// Get the Employee's Name
	$qry = "SELECT CONCAT(empFirst,' ',empLast) AS theEmp FROM employees WHERE empId = ".$empId;
    $result = mysqli_query($mysqli, $qry) or die('-1'.mysqli_error());
	$emp = mysqli_fetch_assoc($result);
	
	include 'includes/navigation.php';

	if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content">
		<h3><?php echo $pageName.' '.$forText.' '.$emp['theEmp']; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>
		
		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noTimeEntriesMsg; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $weekLink; ?></th>
						<th><?php echo $recordDateField; ?></th>
						<th><?php echo $dateInField; ?></th>
						<th><?php echo $timeInField; ?></th>
						<th><?php echo $dateOutField; ?></th>
						<th><?php echo $timeOutField; ?></th>
						<th><?php echo $totalHoursField; ?></th>
						<?php if ($set['enableTimeEdits'] == '1') { ?>
							<th></th>
						<?php } ?>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							// Get Total Time Worked for the Time Entry
							$qry = "SELECT TIMEDIFF(endTime,startTime) AS diff FROM timeentry WHERE entryId = ".$row['entryId'];
							$results = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());
							$times = array();
							while ($u = mysqli_fetch_assoc($results)) {
								$times[] = $u['diff'];
							}
							$totalTime = sumHours($times);
					?>
							<tr>
								<td data-th="<?php echo $weekLink; ?>"><?php echo $row['weekNo']; ?></td>
								<td data-th="<?php echo $recordDateField; ?>"><?php echo $row['eDate']; ?></td>
								<td data-th="<?php echo $dateInField; ?>"><?php echo $row['dateStarted']; ?></td>
								<td data-th="<?php echo $timeInField; ?>"><?php echo $row['hourStarted']; ?></td>
								<td data-th="<?php echo $dateOutField; ?>"><?php echo $row['dateEnded']; ?></td>
								<td data-th="<?php echo $timeOutField; ?>"><?php echo $row['hourEnded']; ?></td>
								<td data-th="<?php echo $totalHoursField; ?>"><?php echo $totalTime; ?></td>
								<?php if ($set['enableTimeEdits'] == '1') { ?>
									<td data-th="<?php echo $actionText; ?>">
										<a href="index.php?page=viewTime&entryId=<?php echo $row['entryId']; ?>">
											<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editTimeTooltip; ?>"></i>
										</a>
										<a data-toggle="modal" href="#deleteTime<?php echo $row['entryId']; ?>">
											<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTimeTooltip; ?>"></i>
										</a>
									</td>
								
									<div class="modal fade" id="deleteTime<?php echo $row['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<form action="" method="post">
													<div class="modal-body">
														<p class="lead">
															<?php echo $deleteTimeConf; ?><br /><?php echo $row['dateStarted']; ?> &mdash; <?php echo $totalHoursField.': '.$totalTime; ?>?
														</p>
													</div>
													<div class="modal-footer">
														<input name="entryId" type="hidden" value="<?php echo $row['entryId']; ?>" />
														<button type="input" name="submit" value="deleteTime" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
								<?php } ?>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>
<?php } ?>