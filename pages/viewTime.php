<?php
	$entryId = $_GET['entryId'];
	$datePicker = 'true';
	$jsFile = 'viewTime';
	$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
	$table_name_timeedits = $wpdb->base_prefix . 'tsh_timeedits';
	$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
	$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
	$table_name_timeedits = $wpdb->base_prefix . 'tsh_timeedits';
	$isAdmin = '1';
	

	// Edit Time Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'editRecord') {
        // Validation
		if($_POST['editReason'] == "") {
            $msgBox = alertBox($editReasonReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateIn'] == "") {
            $msgBox = alertBox($dateInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeIn'] == "") {
            $msgBox = alertBox($timeInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateOut'] == "") {
            $msgBox = alertBox($dateOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeOut'] == "") {
            $msgBox = alertBox($timeOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dateIn = sanitize_text_field($_POST['dateIn']);
			$timeIn = sanitize_text_field($_POST['timeIn']);
			$dateOut =sanitize_text_field($_POST['dateOut']);
			$timeOut = sanitize_text_field($_POST['timeOut']);
			$startTime = $dateIn.' '.$timeIn.':00';
			$endTime = $dateOut.' '.$timeOut.':00';
			$entryType = '3';
			// Edit the Record
			$wpdb->update( $table_name_timeentry,
						array( 'startTime' => $startTime,	'endTime' => $endTime,'entryType' =>$entryType),
						array( 'entryId' => $entryId) 
						);

			$editReason =sanitize_text_field($_POST['editReason']);
			$editedDate = date("Y-m-d H:i:s");
			$origStartTime = sanitize_text_field($_POST['origStartTime']);
			$origEndTime = sanitize_text_field($_POST['origEndTime']);

			// Add a record of the Edit
			$wpdb->insert( 	$table_name_timeedits, 
					array( 
						'entryId' => $entryId, 
						'editedBy' => $user_id,
						'editedDate' => $editedDate,
						'origStartTime' => $origStartTime,
						'origEndTime' => $origEndTime,
						'editedStartTime' => $startTime,
						'editedEndTime' => $endTime,
						'editReason' => $editReason

					)	);


			$msgBox = alertBox($timeRecUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['editReason'] = '';
			
		}
	}

	// Get Data
	$row =  $wpdb->get_row( $query="SELECT
				$table_name_timeentry.entryId,
				$table_name_timeentry.clockId,
				$table_name_timeentry.user_id,
				DATE_FORMAT($table_name_timeentry.entryDate,'%M %d, %Y') AS entryDate,
				$table_name_timeentry.startTime,
				DATE_FORMAT($table_name_timeentry.startTime,'%Y-%m-%d') AS startDate,
				DATE_FORMAT($table_name_timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT($table_name_timeentry.startTime,'%h:%i %p') AS hourStarted,
				DATE_FORMAT($table_name_timeentry.startTime,'%H:%i') AS hourIn,
				$table_name_timeentry.endTime,
				DATE_FORMAT($table_name_timeentry.endTime,'%Y-%m-%d') AS endDate,
				DATE_FORMAT($table_name_timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT($table_name_timeentry.endTime,'%h:%i %p') AS hourEnded,
				DATE_FORMAT($table_name_timeentry.endTime,'%H:%i') AS hourOut,
				$table_name_timeentry.entryType,
				$table_name_timeclock.weekNo,
				$table_name_timeclock.clockYear,
				$table_name_timeclock.running,
				CONCAT($table_name_employees.user_id,' ','user') AS theEmp
			FROM
				$table_name_timeentry
				LEFT JOIN $table_name_timeclock ON $table_name_timeentry.clockId = $table_name_timeclock.clockId
				LEFT JOIN $table_name_employees ON $table_name_timeentry.user_id = $table_name_employees.user_id
			WHERE
				$table_name_timeentry.entryId = $entryId", ARRAY_A);

	if ($row['entryType'] == '1') { $entryType = $entryType1; } else if ($row['entryType'] == '2') { $entryType = $entryType2; } else if ($row['entryType'] == '3') { $entryType = $entryType3; }
	if ($row['running'] == '1') { $isRunning = $yesBtn; } else { $isRunning = $noBtn; }

	// Get any Previous Edit data
	$results = $wpdb->get_results( $query="SELECT
					$table_name_timeedits.editedBy,
					DATE_FORMAT($table_name_timeedits.editedDate,'%M %d, %Y at %h:%i %p') AS editedDate,
					$table_name_timeedits.editReason,
					CONCAT($table_name_employees.user_id,' ','user') AS editedBy
				FROM
					$table_name_timeedits
					LEFT JOIN $table_name_employees ON $table_name_timeedits.editedBy = $table_name_employees.user_id
				WHERE $table_name_timeedits.entryId = $entryId", ARRAY_A);
	$resultsnum = $wpdb->num_rows;
	
	// Get the Total Time Worked
	$times = $wpdb->get_results( $query="SELECT
				TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
			FROM
				$table_name_timeclock
				LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
			WHERE
				$table_name_timeentry.entryId = $entryId");

	if ($row['endTime'] != '0000-00-00 00:00:00') {
		$totalTime = sumHours($times);
		$dateEnded = $row['dateEnded'];
		$hourEnded = $row['hourEnded'];
	} else {
		$totalTime = $dateEnded = $hourEnded = '';
	}



	if (($row['user_id'] != $user_id) && ($isAdmin != '1')) {
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

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><?php echo $recordDateField; ?>:</td>
						<td class="infoVal"><?php echo $row['entryDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $clockYearField; ?>:</td>
						<td class="infoVal"><?php echo $row['clockYear']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $dateInField; ?>:</td>
						<td class="infoVal"><?php echo $row['dateStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $dateOutField; ?>:</td>
						<td class="infoVal"><?php echo $dateEnded; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $entryTypeField; ?>:</td>
						<td class="infoVal"><?php echo $entryType; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><?php echo $clockRunningField; ?>:</td>
						<td class="infoVal"><?php echo $isRunning; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $weekNoField; ?>:</td>
						<td class="infoVal"><?php echo $row['weekNo']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $timeInField; ?>:</td>
						<td class="infoVal"><?php echo $row['hourStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $timeOutField; ?>:</td>
						<td class="infoVal"><?php echo $hourEnded; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $totalHoursField; ?>:</td>
						<td class="infoVal"><strong><?php echo $totalTime; ?></strong></td>
					</tr>
				</table>
			</div>
		</div>
		<?php if ($row['endTime'] != '0000-00-00 00:00:00') { ?>
			<a data-toggle="modal" data-target="#editEntry" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editTimeRecBtn; ?></a>
		<?php } else { ?>
			<div class="alertMsg info no-margin mt10">
				<i class="fa fa-info-circle"></i> <?php echo $editTimeRecQuip; ?>
			</div>
		<?php } ?>
	</div>

	<div class="content last">
		<h4><?php echo $prevTimeEditsTitle; ?></h4>
		<?php if($resultsnum < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noTimeEditsFoundMsg; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $updateDateField; ?></th>
						<th><?php echo $updatedByField; ?></th>
						<th><?php echo $reasonForEditField; ?></th>
					</tr>
					<?php foreach ($results as $rows) { ?>
						<tr>
							<td data-th="<?php echo $updateDateField; ?>"><?php echo $rows['editedDate']; ?></td>
							<td data-th="<?php echo $updatedByField; ?>"><?php echo clean($rows['editedBy']); ?></td>
							<td data-th="<?php echo $reasonForEditField; ?>"><?php echo clean($rows['editReason']); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>

	<?php if ($row['endTime'] != '0000-00-00 00:00:00') { ?>
		<div id="editEntry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $updateTimeRecModal; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="editReason"><?php echo $reasonForEditField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="editReason" value="" />
								<span class="help-block"><?php echo $reasonForEditFieldHelp; ?></span>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateIn"><?php echo $dateInField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="<?php echo $row['startDate']; ?>" />
										<span class="help-block"><?php echo $dateFormatHelp; ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeIn"><?php echo $timeInField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="timeIn" id="timeIn" required="" value="<?php echo $row['hourIn']; ?>" />
										<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateOut"><?php echo $dateOutField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="dateOut" id="dateOut" required="" value="<?php echo $row['endDate']; ?>" />
										<span class="help-block"><?php echo $dateFormatHelp; ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeOut"><?php echo $timeOutField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="timeOut" id="timeOut" required="" value="<?php echo $row['hourOut']; ?>" />
										<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<input type="hidden" name="origStartTime" value="<?php echo $row['startTime']; ?>" />
							<input type="hidden" name="origEndTime" value="<?php echo $row['endTime']; ?>" />
							<button type="input" name="submit" value="editRecord" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
<?php
		}
	}
?>