<?php
	$eid = $_GET['eid'];
	$datePicker = 'true';
	$jsFile = 'timeLogs';
	$isRecord = '';

	// Delete Time Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTime') {
		$entryId = $mysqli->real_escape_string($_POST['entryId']);
		$stmt = $mysqli->prepare("DELETE FROM timeentry WHERE entryId = ?");
		$stmt->bind_param('s', $entryId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($timeEntryDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Add New Time Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'newEntry') {
        // Validation
		if($_POST['dateIn'] == "") {
            $msgBox = alertBox($dateInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeIn'] == "") {
            $msgBox = alertBox($timeInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateOut'] == "") {
            $msgBox = alertBox($dateOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeOut'] == "") {
            $msgBox = alertBox($timeOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dateIn = $mysqli->real_escape_string($_POST['dateIn']);
			$timeIn = $mysqli->real_escape_string($_POST['timeIn']);
			$dateOut = $mysqli->real_escape_string($_POST['dateOut']);
			$timeOut = $mysqli->real_escape_string($_POST['timeOut']);
			$clockYear	= date("Y", strtotime($dateIn));
			$weekNo	= date("W", strtotime($dateIn));
			$entryDate = date("Y-m-d");

			// Check if a Time Clock Record all ready exists
			$check = $mysqli->query("SELECT clockId FROM timeclock WHERE empId = ".$eid." AND weekNo = '".$weekNo."' AND clockYear = '".$clockYear."'");
			$rows = mysqli_fetch_assoc($check);
			if ($check->num_rows) {
				$isRecord = 'true';
				$clockId = $rows['clockId'];
			}

			$entryType = '2';
			$startTime = $dateIn.' '.$timeIn.':00';
			$endTime = $dateOut.' '.$timeOut.':00';

			if ($isRecord == 'true') {
				// Time Clock Record exists, Add the Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											empId,
											entryDate,
											startTime,
											endTime,
											entryType
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
									$clockId,
									$eid,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($manualTimeEntrySaved, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['dateIn'] = $_POST['timeIn'] = $_POST['dateOut'] = $_POST['timeOut'] = '';
				$stmt->close();
			} else {
				// Time Clock Record does NOT exists, Create a new Time Clock record and Add the Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeclock(
											empId,
											weekNo,
											clockYear
										) VALUES (
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sss',
										$eid,
										$weekNo,
										$clockYear
				);
				$stmt->execute();
				$stmt->close();

				// Get the new Time Clock clockId
				$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE empId = ".$eid." AND weekNo = '".$weekNo."' AND clockYear = ".$clockYear);
				$id = mysqli_fetch_assoc($track_id);
				$newId = $id['clockId'];

				// Add the New Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											empId,
											entryDate,
											startTime,
											endTime,
											entryType
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
									$newId,
									$eid,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($manualTimeEntrySaved, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['dateIn'] = $_POST['timeIn'] = $_POST['dateOut'] = $_POST['timeOut'] = '';
				$stmt->close();
			}
		}
	}

	// Get a list of all the Years
	$a = "SELECT clockYear FROM timeclock WHERE empId = ".$eid." GROUP BY clockYear";
	$b = mysqli_query($mysqli, $a) or die('-1'.mysqli_error());
	$yrs = array();
	// Set each Year in an array
	while($year = mysqli_fetch_assoc($b)) {
		$yrs[] = $year['clockYear'];
	}

	// Get the Employee's name
	$c = "SELECT CONCAT(employees.empFirst,' ',employees.empLast) AS empFullName FROM employees WHERE empId = ".$eid;
	$d = mysqli_query($mysqli, $c) or die('-2'.mysqli_error());
	$name = mysqli_fetch_assoc($d);
	$eName = $name['empFullName'];

	include 'includes/navigation.php';
?>
<div class="content">
	<h3><?php echo $pageName.': '.$eName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<ul class="nav nav-tabs">
		<?php
			// Create the Year Tabs
			foreach ($yrs as $years) {
				// Set the Current Year Tab Button as Active
				if ($years == $currentYear) { $setActive = 'class="active"'; } else { $setActive = ''; }
		?>
				<li <?php echo $setActive; ?>><a href="#<?php echo $years; ?>" data-toggle="tab"><?php echo $years; ?></a></li>
		<?php
			}
			if ($set['enableTimeEdits'] == '1') {
				echo '<li class="pull-right"><a href="#addTime" data-toggle="modal" class="bg-success">'.$manTimeEntryBtn.'</a></li>';
			}
		?>
	</ul>

	<div class="tab-content">
	<?php
		if (empty($yrs)) {
			echo '
					<div class="alertMsg default no-margin mt10">
						<i class="fa fa-warning"></i> '.$noTimeEntriesMsg.'
					</div>
				';
		}
		// Create the Tab Content
		foreach ($yrs as $year) {
			// Set the Current Year Tab as Active
			if ($year == $currentYear) { $inActive = ' in active'; } else { $inActive = ''; }
	?>
				<div class="tab-pane<?php echo $inActive; ?> no-padding" id="<?php echo $year; ?>">
				<?php
					// Get the Week Numbers
					$i = "SELECT weekNo FROM timeclock WHERE empId = ".$eid." AND clockYear = ".$year." GROUP BY weekNo ORDER BY weekNo DESC";
					$j = mysqli_query($mysqli, $i) or die('-3' . mysqli_error());

					// Set each year in an array
					$weeks = array();
					while($k = mysqli_fetch_assoc($j)) {
						$weeks[] = $k['weekNo'];
					}
					if (empty($weeks)) {
						echo '
								<div class="alertMsg default no-margin">
									<i class="fa fa-warning"></i> '.$noTimeEntriesMsg.'
								</div>
							';
					} else {
						echo '<dl class="accordion">';
						foreach ($weeks as $weekTab) {
			?>
							<dt><a> <?php echo $weekLink.': '.$weekTab; ?> &mdash; <?php echo $yearField.': '.$year; ?><span><i class="fa fa-angle-right"></i></span></a></dt>
							<dd class="hideIt">
								<?php
									// Get Data
									$sql = "SELECT
												timeclock.clockId,
												timeclock.empId,
												timeclock.weekNo,
												timeclock.clockYear,
												timeentry.entryId,
												timeentry.startTime,
												DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
												DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
												timeentry.endTime,
												DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
												DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
												UNIX_TIMESTAMP(timeentry.startTime) AS orderDate,
												CONCAT(employees.empFirst,' ',employees.empLast) AS theEmp
											FROM
												timeclock
												LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
												LEFT JOIN employees ON timeclock.empId = employees.empId
											WHERE
												timeclock.empId = ".$eid." AND
												timeclock.weekNo = ".$weekTab." AND
												clockYear = ".$year." AND
												timeentry.endTime != '0000-00-00 00:00:00'
											ORDER BY orderDate";
									$res = mysqli_query($mysqli, $sql) or die('-4'.mysqli_error());

									// Get the Total Time Worked
									$qry = "SELECT
												TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
											FROM
												timeclock
												LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
											WHERE
												timeclock.empId = ".$eid." AND
												timeclock.weekNo = ".$weekTab." AND
												clockYear = ".$year." AND
												timeentry.endTime != '0000-00-00 00:00:00'";
									$result = mysqli_query($mysqli, $qry) or die('-5'.mysqli_error());
									$times = array();
									while ($u = mysqli_fetch_assoc($result)) {
										$times[] = $u['diff'];
									}
									$totalTime = sumHours($times);
								?>
								<table class="rwd-table no-margin">
									<tbody>
										<tr>
											<th><?php echo $yearField; ?></th>
											<th><?php echo $dateInField; ?></th>
											<th><?php echo $timeInField; ?></th>
											<th><?php echo $dateOutField; ?></th>
											<th><?php echo $timeOutField; ?></th>
											<th><?php echo $hoursText; ?></th>
											<?php if (($set['enableTimeEdits'] == '1') && ($isAdmin == '1')) { ?>
												<th></th>
											<?php } ?>
										</tr>
										<?php
											while ($col = mysqli_fetch_assoc($res)) {
												// Get the Time Total for each Time Entry
												$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$col['entryId'];
												$results = mysqli_query($mysqli, $tot) or die('-6'.mysqli_error());
												$rows = mysqli_fetch_assoc($results);

												// Convert it to HH:MM
												$from = new DateTime($rows['startTime']);
												$to = new DateTime($rows['endTime']);
												$lineTotal = $from->diff($to)->format('%h:%i');
										?>
												<tr>
													<td data-th="<?php echo $yearField; ?>"><?php echo $col['clockYear']; ?></td>
													<td data-th="<?php echo $dateInField; ?>"><?php echo $col['dateStarted']; ?></td>
													<td data-th="<?php echo $timeInField; ?>"><?php echo $col['hourStarted']; ?></td>
													<td data-th="<?php echo $dateOutField; ?>"><?php echo $col['dateEnded']; ?></td>
													<td data-th="<?php echo $timeOutField; ?>"><?php echo $col['hourEnded']; ?></td>
													<td data-th="<?php echo $hoursText; ?>"><?php echo $lineTotal; ?></td>
													<?php if (($set['enableTimeEdits'] == '1') && ($isAdmin == '1')) { ?>
														<td data-th="<?php echo $actionText; ?>">
															<a href="index.php?page=viewTime&entryId=<?php echo $col['entryId']; ?>">
																<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editTimeTooltip; ?>"></i>
															</a>
															<a data-toggle="modal" href="#deleteTime<?php echo $col['entryId']; ?>">
																<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTimeTooltip; ?>"></i>
															</a>
														</td>
													<?php } ?>
												</tr>

												<?php if (($set['enableTimeEdits'] == '1') && ($isAdmin == '1')) { ?>
													<div class="modal fade" id="deleteTime<?php echo $col['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<form action="" method="post">
																	<div class="modal-body">
																		<p class="lead">
																			<?php echo $deleteTimeConf; ?><br /><?php echo $col['dateStarted']; ?> &mdash; <?php echo $totalHoursField; ?>: <?php echo $lineTotal; ?>?
																		</p>
																	</div>
																	<div class="modal-footer">
																		<input name="entryId" type="hidden" value="<?php echo $col['entryId']; ?>" />
																		<button type="input" name="submit" value="deleteTime" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
																		<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
																	</div>
																</form>
															</div>
														</div>
													</div>
												<?php } ?>
										<?php } ?>
									</tbody>
								</table>
								<?php
									sumHours($times);
									echo '
											<p class="mt20">
												<span class="label label-default preview-label" data-toggle="tooltip" data-placement="right" title="'.$timeFormatHelp.'">
													'.$totalText.' '.$totalTime.'
												</span>
											</p>
										';
								?>
							</dd>
		<?php
						}
						echo '</dl><div class="clearfix"></div>';
					}
		?>
				</div>
	<?php
		}
	?>
	</div>
</div>

<?php if ($set['enableTimeEdits'] == '1') { ?>
	<div id="addTime" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $manTimeEntryBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateIn"><?php echo $dateInField; ?> <sup><?php echo $reqField; ?></sup></label>
									<input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="<?php echo isset($_POST['dateIn']) ? $_POST['dateIn'] : ''; ?>" />
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeIn"><?php echo $timeInField; ?> <sup><?php echo $reqField; ?></sup></label>
									<input type="text" class="form-control" name="timeIn" id="timeIn" required="" value="<?php echo isset($_POST['timeIn']) ? $_POST['timeIn'] : ''; ?>" />
									<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateOut"><?php echo $dateOutField; ?> <sup><?php echo $reqField; ?></sup></label>
									<input type="text" class="form-control" name="dateOut" id="dateOut" required="" value="<?php echo isset($_POST['dateOut']) ? $_POST['dateOut'] : ''; ?>" />
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeOut"><?php echo $timeOutField; ?> <sup><?php echo $reqField; ?></sup></label>
									<input type="text" class="form-control" name="timeOut" id="timeOut" required="" value="<?php echo isset($_POST['timeOut']) ? $_POST['timeOut'] : ''; ?>" />
									<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="newEntry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveTimeEntryBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>