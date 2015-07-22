<?php
	// Report Options
	$showEmployees = $_POST['showEmployees'];
	if (isset($showEmployees) && $showEmployees == '0') {	// All Active
		$isActive = "'1'";
		$included = 'All Active';
	} else if ($showEmployees == '1') {						// All Inactive
		$isActive = "'0'";
		$included = 'All Inactive';
	} else {												// Show All
		$isActive = "'0','1'";
		$included = 'All';
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=employeeExport.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		'Employee Name',
		'Position',
		'Email',
		'Primary Phone',
		'Alternate Phone',
		'Alternate Phone',
		'Type',
		'Date of Hire',
		'Last Login',
		'Status',
		'Termination Date',
		'Termination Reason'
	));

	// Get Data
	$sql = "SELECT
				empId,
				isAdmin,
				isMgr,
				empEmail,
				empFirst, IFNULL(empMiddleInt,'') AS empMiddleInt, empLast,
				empPhone1,
				empPhone2,
				empPhone3,
				empPosition,
				DATE_FORMAT(empHireDate,'%M %d, %Y') AS empHireDate,
				DATE_FORMAT(empLastVisited,'%M %e, %Y') AS empLastVisited,
				isActive,
				DATE_FORMAT(empTerminationDate,'%M %e, %Y') AS empTerminationDate,
				terminationReason
			FROM
				employees
			WHERE
				isActive IN (".$isActive.")
			ORDER BY
				isActive DESC,
				isAdmin DESC,
				isMgr DESC,
				empId";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();

		// Decrypt Data
		if ($row['empPhone1'] != '') { $empPhone1 = decryptIt($row['empPhone1']); } else { $empPhone1 = ''; }
		if ($row['empPhone2'] != '') { $empPhone2 = decryptIt($row['empPhone2']); } else { $empPhone2 = ''; }
		if ($row['empPhone3'] != '') { $empPhone3 = decryptIt($row['empPhone3']); } else { $empPhone3 = ''; }

		if ($row['isActive'] == '0') { $isActive = 'Inactive'; } else { $isActive = 'Active'; }
		if ($row['isAdmin'] == '1') { $adm = 'Administrator'; } else { $adm = 'Employee'; }
		if ($row['isMgr'] == '1') { $mgr = ' / Manager'; } else { $mgr = ''; }
		$empFullname = clean($row['empFirst']).' '.clean($row['empMiddleInt']).' '.clean($row['empLast']);
		$type = $adm.$mgr;

		$items_array[] = $empFullname;
		$items_array[] = clean($row['empPosition']);
		$items_array[] = clean($row['empEmail']);
		$items_array[] = $empPhone1;
		$items_array[] = $empPhone2;
		$items_array[] = $empPhone3;
		$items_array[] = $type;
		$items_array[] = $row['empHireDate'];
		$items_array[] = $row['empLastVisited'];
		$items_array[] = $isActive;
		$items_array[] = $row['empTerminationDate'];
		$items_array[] = clean($row['terminationReason']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>