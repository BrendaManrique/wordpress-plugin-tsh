<?php
	// Report Options
	$showEmployees = $_POST['showEmployees'];
	if (isset($showEmployees) && $showEmployees == '0') {	// All Active
		$isActive = "'1'";
		$included = $allActiveEmpText;
	} else if ($showEmployees == '1') {						// All Inactive
		$isActive = "'0'";
		$included = $allInactiveEmpText;
	} else {												// Show All
		$isActive = "'0','1'";
		$included = $allEmpText;
	}

	$sql = "SELECT
				empId,
				isAdmin,
				isMgr,
				empEmail,
				CONCAT(empFirst,' ',empLast) AS theEmp,
				empPhone1,
				empPosition,
				DATE_FORMAT(empHireDate,'%M %d, %Y') AS empHireDate,
				DATE_FORMAT(empLastVisited,'%M %e, %Y') AS empLastVisited,
				isActive
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
	$totalRecs = mysqli_num_rows($res);

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
					<span class="label label-lg label-default"><strong><?php echo $included; ?></strong></span>
					<span class="label label-lg label-default"><?php echo '<strong>'.$totalRecordsText.':</strong> '.$totalRecs; ?></span>
				</p>
			</div>
			<div class="col-md-4">
				<form action="index.php?page=employeeExport" method="post" target="_blank" class="pull-right">
					<input type="hidden" name="showEmployees" value="<?php echo $showEmployees; ?>" />
					<button type="input" name="submit" value="export" class="btn btn-info btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $empReportExportBtn; ?></button>
				</form>
			</div>
		</div>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-warning"></i> <?php echo $noResultsFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table mt10">
				<tbody>
					<tr class="primary">
						<th><?php echo $empName; ?></th>
						<th><?php echo $positionField; ?></th>
						<th><?php echo $emailField; ?></th>
						<th><?php echo $phone1Field; ?></th>
						<th><?php echo $typeField; ?></th>
						<th><?php echo $hireDateField; ?></th>
						<th><?php echo $lastLoginField; ?></th>
						<th><?php echo $statusField; ?></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['empPhone1'] != '') { $empPhone1 = decryptIt($row['empPhone1']); } else { $empPhone1 = ''; }
							if ($row['isAdmin'] == '1') { $adm = '<strong class="text-primary">'.$administratorText.'</strong>'; } else { $adm = '<strong class="text-warning">'.$employeeText.'</strong>'; }
							if ($row['isMgr'] == '1') { $mgr = ' / <strong class="text-info">'.$managerText.'</strong>'; } else { $mgr = ''; }
							if ($row['isActive'] == '0') { $status = '<strong class="text-danger">'.$inactiveText.'</strong>'; } else { $status = '<strong class="text-success">'.$activeText.'</strong>'; }
					?>
							<tr>
								<td data-th="<?php echo $empName; ?>"><?php echo clean($row['theEmp']); ?></td>
								<td data-th="<?php echo $positionField; ?>"><?php echo clean($row['empPosition']); ?></td>
								<td data-th="<?php echo $emailField; ?>"><?php echo clean($row['empEmail']); ?></td>
								<td data-th="<?php echo $phone1Field; ?>"><?php echo $empPhone1; ?></td>
								<td data-th="<?php echo $typeField; ?>"><?php echo $adm.$mgr; ?></td>
								<td data-th="<?php echo $hireDateField; ?>"><?php echo $row['empHireDate']; ?></td>
								<td data-th="<?php echo $lastLoginField; ?>"><?php echo $row['empLastVisited']; ?></td>
								<td data-th="<?php echo $statusField; ?>"><?php echo $status; ?></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>
<?php } ?>