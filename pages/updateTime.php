<?php
	$date = $_GET['date'];
	$week = $_GET['week'];
	$user_id = $_GET['user_id'];
	$datePicker = 'true';	
	$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
	$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
	$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
	$isAdmin = '1';
	$enableTimeEdits = '1';
	
	// Delete Time Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTime') {
		$entryId =  sanitize_text_field($_POST['entryId']);
		$wpdb->delete( $table_name_timeentry, array( 'entryId' => $entryId ) );
		$msgBox = alertBox($timeEntryDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }
	
    if ($date == ''){
    // Get Data
	$res =  $wpdb->get_results( $query=" SELECT 
				$table_name_timeentry.entryId,
				$table_name_timeentry.clockId,
				$table_name_timeentry.user_id,
				$table_name_timeentry.entryDate,
				DATE_FORMAT($table_name_timeentry.entryDate,'%M %d, %Y') AS eDate,
				DATE_FORMAT($table_name_timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT($table_name_timeentry.startTime,'%h:%i %p') AS hourStarted,
				$table_name_timeentry.startTime,
				$table_name_timeentry.endTime,
				DATE_FORMAT($table_name_timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT($table_name_timeentry.endTime,'%h:%i %p') AS hourEnded,
				$table_name_timeentry.entryType,
				$table_name_timeclock.clockId,
				$table_name_timeclock.weekNo,
				$table_name_timeclock.clockYear,
				$table_name_timeclock.running,
				CONCAT($table_name_employees.user_id,'user')  AS theEmp
			FROM
				$table_name_timeentry
				LEFT JOIN $table_name_timeclock ON $table_name_timeentry.clockId = $table_name_timeclock.clockId
				LEFT JOIN $table_name_employees ON $table_name_timeentry.user_id = $table_name_employees.user_id
			WHERE
				$table_name_timeentry.user_id = $user_id AND
				$table_name_timeclock.weekNo = $week",ARRAY_A);
	$resnum = $wpdb->num_rows; 
}else{
	// Get Data
	$res =  $wpdb->get_results( $query=" SELECT
				$table_name_timeentry.entryId,
				$table_name_timeentry.clockId,
				$table_name_timeentry.user_id,
				$table_name_timeentry.entryDate,
				DATE_FORMAT($table_name_timeentry.entryDate,'%M %d, %Y') AS eDate,
				DATE_FORMAT($table_name_timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT($table_name_timeentry.startTime,'%h:%i %p') AS hourStarted,
				$table_name_timeentry.startTime,
				$table_name_timeentry.endTime,
				DATE_FORMAT($table_name_timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT($table_name_timeentry.endTime,'%h:%i %p') AS hourEnded,
				$table_name_timeentry.entryType,
				$table_name_timeclock.clockId,
				$table_name_timeclock.weekNo,
				$table_name_timeclock.clockYear,
				$table_name_timeclock.running,
				CONCAT($table_name_employees.user_id,'user')  AS theEmp
			FROM
				$table_name_timeentry
				LEFT JOIN $table_name_timeclock ON $table_name_timeentry.clockId = $table_name_timeclock.clockId
				LEFT JOIN $table_name_employees ON $table_name_timeentry.user_id = $table_name_employees.user_id
			WHERE
				$table_name_timeentry.entryDate = '$date' AND
				$table_name_timeentry.user_id = $user_id",ARRAY_A);
}
	$resnum = $wpdb->num_rows; 
    
	// Get the Employee's Name
	//$emp =  $wpdb->get_row($query= "SELECT CONCAT('user',' ',user_id) AS theEmp FROM  $table_name_employees WHERE user_id = ".$user_id, ARRAY_A);

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
		<?php $user_info = get_userdata($user_id); ?>
		<h3><?php echo $pageName.' '.$forText.' '.$user_info->user_login; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>
		
		<?php if($resnum < 1) { ?>
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
						foreach ($res as $row) {
							// Get Total Time Worked for the Time Entry
							$entry = $row['entryId'];
							$times =  $wpdb->get_results( $query="SELECT TIMEDIFF(endTime,startTime) AS diff FROM $table_name_timeentry WHERE entryId = $entry");
							
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
								<?php if ($enableTimeEdits == '1') { ?>
									<td data-th="<?php echo $actionText; ?>">
										<a href="admin.php?page=viewtime&entryId=<?php echo $row['entryId']; ?>">
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

															<?php echo $deleteTimeConf.' '.$user_info->user_login; ?><br /><?php echo $row['dateStarted']; ?> &mdash; <?php echo $totalHoursField.': '.$totalTime; ?>?
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