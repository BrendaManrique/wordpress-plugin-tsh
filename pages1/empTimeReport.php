<?php
	$validReport = '';

	// Server Side validation
	if($_POST['employee'] == "...") {
		$msgBox = alertBox($selectEmpReq, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['fromDate'] == "") {
		$msgBox = alertBox($fromDateReq, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['toDate'] == "") {
		$msgBox = alertBox($toDateReq, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else {
		// Report Options
		if (!empty($_POST['fromDate'])) {
			$fromDate = $mysqli->real_escape_string($_POST['fromDate']);
			$fdate = date('F d, Y', strtotime($fromDate));
		}
		if (!empty($_POST['toDate'])) {
			$toDate = $mysqli->real_escape_string($_POST['toDate']);
			$tdate = date('F d, Y', strtotime($toDate));
		}
		$employee = $mysqli->real_escape_string($_POST['employee']);
		$empFullName = $mysqli->real_escape_string($_POST['empFullName']);

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
					timeclock.empId = ".$employee." AND
					timeentry.endTime != '0000-00-00 00:00:00' AND
					timeentry.startTime >= '".$fromDate."' AND timeentry.startTime <= '".$toDate."'
				ORDER BY orderDate";
		$res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
		$totalRecs = mysqli_num_rows($res);

		// Get the Total Time Worked
		$qry = "SELECT
					TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
				FROM
					timeclock
					LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
				WHERE
					timeclock.empId = ".$employee." AND
					timeentry.endTime != '0000-00-00 00:00:00' AND
					timeentry.startTime >= '".$fromDate."' AND timeentry.startTime <= '".$toDate."'";
		$result = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());
		$times = array();
		while ($u = mysqli_fetch_assoc($result)) {
			$times[] = $u['diff'];
		}
		$totalTime = sumHours($times);
	}

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($isMgr != '1')) {
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
		<div class="row">
			<div class="col-md-8">
				<p>
					<span class="label label-lg label-default"><?php echo '<strong>'.$employeeText.':</strong> '.$empFullName; ?></span>
					<span class="label label-lg label-default"><?php echo '<strong>'.$datesText.':</strong> '.$fdate.' &mdash; '.$tdate; ?></span>
					<span class="label label-lg label-default"><?php echo '<strong>'.$totalRecordsText.':</strong> '.$totalRecs; ?></span>
				</p>
			</div>
			<div class="col-md-4">
				<form action="index.php?page=empTimeExport" method="post" target="_blank" class="pull-right">
					<input type="hidden" name="fromDate" value="<?php echo $fromDate; ?>" />
					<input type="hidden" name="toDate" value="<?php echo $toDate; ?>" />
					<input type="hidden" name="employee" value="<?php echo $employee; ?>" />
					<button type="input" name="submit" value="export" class="btn btn-info btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $empReportExportBtn; ?></button>
				</form>
			</div>
		</div>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-warning"></i> <?php echo $noResultsFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table mt10">
				<tbody>
					<tr class="primary">
						<th><?php echo $yearField; ?></th>
						<th><?php echo $weekNoField; ?></th>
						<th><?php echo $dateInField; ?></th>
						<th><?php echo $timeInField; ?></th>
						<th><?php echo $dateOutField; ?></th>
						<th><?php echo $timeOutField; ?></th>
						<th><?php echo $totalHoursField; ?></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							// Get the Time Total for each Time Entry
							$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$row['entryId'];
							$results = mysqli_query($mysqli, $tot) or die('-3'.mysqli_error());
							$rows = mysqli_fetch_assoc($results);

							// Convert it to HH:MM
							$from = new DateTime($rows['startTime']);
							$to = new DateTime($rows['endTime']);
							$lineTotal = $from->diff($to)->format('%h:%i');
					?>
							<tr>
								<td data-th="<?php echo $yearField; ?>"><?php echo $row['clockYear']; ?></td>
								<td data-th="<?php echo $weekNoField; ?>"><?php echo $row['weekNo']; ?></td>
								<td data-th="<?php echo $dateInField; ?>"><?php echo $row['dateStarted']; ?></td>
								<td data-th="<?php echo $timeInField; ?>"><?php echo $row['hourStarted']; ?></td>
								<td data-th="<?php echo $dateOutField; ?>"><?php echo $row['dateEnded']; ?></td>
								<td data-th="<?php echo $timeOutField; ?>"><?php echo $row['hourEnded']; ?></td>
								<td data-th="<?php echo $totalHoursField; ?>"><?php echo $lineTotal; ?></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
			<span class="label label-lg label-default pull-right mt10"><strong><?php echo $totalText; ?></strong> <?php echo $totalTime; ?></strong></span>
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } ?>