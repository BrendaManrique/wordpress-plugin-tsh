<?php
	global $wpdb;
	$datePicker = 'true';
	$jsFile = 'timeLogs';
	$isRecord = '';
	$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
	$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
	$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
	global $current_user;
get_currentuserinfo();
$user_id = $current_user->ID;
 $currentYear= current_time("Y");
 $weekNo = getWeekNo(current_time("Y-m-d"));
 	$enableTimeEdits = '0';


/*
	// Delete Time Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTime') {
		$entryId = $mysqli->real_escape_string($_POST['entryId']);
		$stmt = $wpdb->delete( $table_name_timeentry, array( 'entryId' => $entryId ) );     
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
			$clockYear	= current_time("Y", strtotime($dateIn));
			$weekNo	= current_time("W", strtotime($dateIn));
			$entryDate = current_time("Y-m-d");

			// Check if a Time Clock Record all ready exists
			$check = $mysqli->query("SELECT clockId FROM timeclock WHERE empId = ".$empId." AND weekNo = '".$weekNo."' AND clockYear = '".$clockYear."'");
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
									$empId,
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
										$empId,
										$weekNo,
										$clockYear
				);
				$stmt->execute();
				$stmt->close();

				// Get the new Time Clock clockId
				$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE empId = ".$empId." AND weekNo = '".$weekNo."' AND clockYear = ".$clockYear);
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
									$empId,
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
	}*/

	// Get a list of all the Years
	$u = $wpdb->get_results(
			$query="SELECT TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
			 FROM $table_name_timeclock
					LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
			WHERE  $table_name_timeclock.user_id = $user_id AND
					$table_name_timeclock.weekNo = $weekNo AND
					$table_name_timeclock.clockYear = $clockYear AND
					$table_name_timeentry.endTime != '0000-00-00 00:00:00'");
	$some = sumHours($u);
	$a = $wpdb->get_results( 
		$query=" SELECT clockYear FROM $table_name_timeclock WHERE user_id = $user_id GROUP BY clockYear;",ARRAY_A);
	// Set each Year in an array
	$yrs= array();
	$yrs = $a;
	/*while($year = $a) {
		$yrs[] = $year['clockYear'];
	}*/
	
?>

<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<ul class="nav nav-tabs">
		<?php
		
			// Create the Year Tabs
			foreach ($yrs as $years) {
				// Set the Current Year Tab Button as Active
				if ($years['clockYear'] == $currentYear) { $setActive = 'class="active"'; } else { $setActive = ''; }
		?>
				<li <?php echo $setActive; ?>><a href="#<?php echo $years['clockYear']; ?>" data-toggle="tab"><?php echo $years['clockYear']; ?></a></li>
		<?php
			}
			if ($enableTimeEdits == '1') {
				echo '<li class="pull-right"><a href="#addTime" data-toggle="modal" class="bg-success">'.$manTimeEntryBtn.'</a></li>';
			}
		?>
	</ul>

	<div class="tab-content">
	<?php
		if (empty($yrs)) {
			echo '
					<div class="alertMsg default no-margin">
						<i class="fa fa-warning"></i> '.$noTimeEntriesMsg.'
					</div>
				';
		}
		// Create the Tab Content
		foreach ($yrs as $year) {
			// Set the Current Year Tab as Active
			$yearclockYear = $year['clockYear'];
			if ($yearclockYear== $currentYear) { $inActive = ' in active'; } else { $inActive = ''; }
	?>
				<div class="tab-pane<?php echo $inActive; ?> no-padding" id="<?php echo $year; ?>">
				<?php
					// Get the Week Numbers
					$weeks = $wpdb->get_results( 
						$query=" SELECT weekNo FROM $table_name_timeclock WHERE user_id = $user_id AND clockYear = $yearclockYear GROUP BY weekNo ORDER BY weekNo DESC",ARRAY_A);
					
					// Set each year in an array
					/*$weeks = array();
					while($k = $i) {
						$weeks[] = $k['weekNo'];
					}*/
					if (empty($weeks)) {
						echo '
								<div class="alertMsg default no-margin">
									<i class="fa fa-warning"></i> '.$noTimeEntriesMsg.'
								</div>
							';
					} else {
						echo '<dl class="accordion">';
						foreach ($weeks as $weekTab) {
							$weekTabNo = $weekTab['weekNo'];
			?>
							<dt><a> <?php echo $weekLink.': '.$weekTabNo; ?> &mdash; <?php echo $yearField.': '.$yearclockYear; ?><span><i class="fa fa-angle-right"></i></span></a></dt>
							<dd class="hideIt">
								<?php
									// Get Data
									$res = $wpdb->get_results( 
										$query="SELECT
												$table_name_timeclock.clockId,
												$table_name_timeclock.user_id,
												$table_name_timeclock.weekNo,
												$table_name_timeclock.clockYear,
												$table_name_timeentry.entryId,
												$table_name_timeentry.startTime,
												DATE_FORMAT($table_name_timeentry.startTime,'%M %d, %Y') AS dateStarted,
												DATE_FORMAT($table_name_timeentry.startTime,'%h:%i %p') AS hourStarted,
												$table_name_timeentry.endTime,
												DATE_FORMAT($table_name_timeentry.endTime,'%M %d, %Y') AS dateEnded,
												DATE_FORMAT($table_name_timeentry.endTime,'%h:%i %p') AS hourEnded,
												UNIX_TIMESTAMP($table_name_timeentry.startTime) AS orderDate,
												$table_name_employees.user_id AS theEmp
											FROM
												$table_name_timeclock
												LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
												LEFT JOIN $table_name_employees ON $table_name_timeclock.user_id = $table_name_employees.user_id
											WHERE
												$table_name_timeclock.user_id = $user_id AND
												$table_name_timeclock.weekNo = $weekTabNo AND
												clockYear = $yearclockYear AND
												$table_name_timeentry.endTime != '0000-00-00 00:00:00'
											ORDER BY orderDate",ARRAY_A);
									// Get the Total Time Worked
									$result = $wpdb->get_results( 
										$query="SELECT
												TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
											FROM
												$table_name_timeclock
												LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
											WHERE
												$table_name_timeclock.user_id = $user_id AND
												$table_name_timeclock.weekNo = $weekTabNo AND
												clockYear = $yearclockYear AND
												$table_name_timeentry.endTime != '0000-00-00 00:00:00'");

									/*$times = array();
									while ($u = $result) {
										$times[] = $u['diff'];
									}*/
									$totalTime = sumHours($result);
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
											<?php if ($enableTimeEdits == '1') { ?>
												<th></th>
											<?php } ?>
										</tr>
										<?php
											foreach ( $res as $col) {
												// Get the Time Total for each Time Entry
												
												$col_user=$col['user_id'];
												//$rows = $wpdb->get_results( 
													//$query="SELECT $table_name_timeentry.startTime, $table_name_timeentry.endTime FROM $table_name_timeentry WHERE user_id = $col_user ",ARRAY_A);
										//print_r($rows);
												// Convert it to HH:MM
												$from = new DateTime($col['startTime']);
												$to = new DateTime($col['endTime']);
												$lineTotal = $from->diff($to)->format('%h:%i');

										
										?>
												<tr>
													<td data-th="<?php echo $yearField; ?>"><?php echo $col['clockYear']; ?></td>
													<td data-th="<?php echo $dateInField; ?>"><?php echo $col['dateStarted']; ?></td>
													<td data-th="<?php echo $timeInField; ?>"><?php echo $col['hourStarted']; ?></td>
													<td data-th="<?php echo $dateOutField; ?>"><?php echo $col['dateEnded']; ?></td>
													<td data-th="<?php echo $timeOutField; ?>"><?php echo $col['hourEnded']; ?></td>
													<td data-th="<?php echo $hoursText; ?>"><?php echo $lineTotal; ?></td>
													<?php if ($enableTimeEdits == '1') { ?>
														<td data-th="<?php echo $actionText; ?>">
															<a href="admin.php?page=viewtime&entryId=<?php echo $col['entryId']; ?>">
																<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editTimeTooltip; ?>"></i>
															</a>
															<a data-toggle="modal" href="#deleteTime<?php echo $col['entryId']; ?>">
																<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTimeTooltip; ?>"></i>
															</a>
														</td>
													<?php } ?>
												</tr>

												<?php if ($enableTimeEdits == '1') { ?>
													<div class="modal fade" id="deleteTime<?php echo $col['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<form action="" method="post">
																	<div class="modal-body">
																		<p class="lead">
																			<?php echo $deleteTimeConf; ?><br /><?php echo $col['dateStarted']; ?> &mdash; <?php echo $totalHoursField.': '.$lineTotal; ?>?
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
									sumHours($result);
									echo '
											<p class="mt20">
												<span class="label label-default preview-label" data-toggle="tooltip" data-placement="right" title="'.$timeFormatHelp.'">
													'.$totalText.': '.$totalTime.'
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

<?php if ($enableTimeEdits == '1') { ?>
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