<?php
	// Report Options
	if (!empty($_POST['fromDate'])) {
		$fromDate = $mysqli->real_escape_string($_POST['fromDate']);
	}
	if (!empty($_POST['toDate'])) {
		$toDate = $mysqli->real_escape_string($_POST['toDate']);
	}
	$employee = $mysqli->real_escape_string($_POST['employee']);

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportEmployeeTime.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		'Employee',
		'Year',
		'Week No.',
		'Date In',
		'Time In',
		'Date Out',
		'Time Out',
		'Total Hours'
	));

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

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();

		// Get the Time Total for each Time Entry
		$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$row['entryId'];
		$results = mysqli_query($mysqli, $tot) or die('-2'.mysqli_error());
		$rows = mysqli_fetch_assoc($results);

		// Convert it to HH:MM
		$from = new DateTime($rows['startTime']);
		$to = new DateTime($rows['endTime']);
		$lineTotal = $from->diff($to)->format('%h:%i');

		$items_array[] = clean($row['theEmp']);
		$items_array[] = $row['clockYear'];
		$items_array[] = $row['weekNo'];
		$items_array[] = $row['dateStarted'];
		$items_array[] = $row['hourStarted'];
		$items_array[] = $row['dateEnded'];
		$items_array[] = $row['hourEnded'];
		$items_array[] = $lineTotal;

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>