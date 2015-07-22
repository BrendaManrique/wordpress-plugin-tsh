<?php
	$searchTerm = (isset($_POST['searchTerm'])) ? $mysqli->real_escape_string($_POST['searchTerm']) : '';

	// Search Data
	$qry = "SELECT
				empId,
				isAdmin,
				isMgr,
				empEmail,
				empFirst,
				empLast,
				CONCAT(empFirst,' ',empLast) AS theEmp,
				empAvatar,
				empPhone1,
				empAddress1,
				empPosition,
				DATE_FORMAT(empHireDate,'%M %d, %Y') AS empHireDate
			FROM
				employees
			WHERE
				isActive = 1 AND
				(empFirst LIKE '%".$searchTerm."%' OR empFirst LIKE UPPER('%".$searchTerm."%') OR empFirst LIKE LOWER('%".$searchTerm."%') OR
				empLast LIKE '%".$searchTerm."%' OR empLast LIKE UPPER('%".$searchTerm."%') OR empLast LIKE LOWER('%".$searchTerm."%') OR
				empPosition LIKE '%".$searchTerm."%' OR empPosition LIKE UPPER('%".$searchTerm."%') OR empPosition LIKE LOWER('%".$searchTerm."%'))
			GROUP BY empId
			ORDER BY empId";
	$res = mysqli_query($mysqli, $qry) or die('-1'.mysqli_error());
	$rowstot = mysqli_num_rows($res);

	if ($rowstot == 1) { $qty = $rowstot.' '.$resultFoundText; } else { $qty = $rowstot.' '.$resultsFoundText; }
	if ($rowstot < 1) {
		$msgBox = alertBox($noResultsFoundMsg, "<i class='fa fa-warning'></i>", "default no-margin");
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
		<h3>
			<?php echo $pageName; ?>
			<span class="pull-right"><?php echo $qty; ?></span>
		</h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php
		while ($row = mysqli_fetch_assoc($res)) {
			if ($row['empPhone1'] != '') { $empPhone1 = decryptIt($row['empPhone1']); } else { $empPhone1 = '';  }
			if ($row['empAddress1'] != '') { $empAddress1 = decryptIt($row['empAddress1']); } else { $empAddress1 = '';  }
	?>
			<div class="search-box">
				<div class="row">
					<div class="col-md-1">
						<img src="<?php echo $avatarDir.$row['empAvatar']; ?>" class="avatarSearch" />
					</div>
					<div class="col-md-11 section-box">
						<div class="row">
							<div class="col-md-5">
								<h4>
									<?php if($row['isAdmin'] != '1') {  ?>
									<a href="index.php?page=viewEmployee&eid=<?php echo $row['empId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewEmpTooltip; ?>">
										<?php echo clean($row['theEmp']); ?>
									</a>
									<?php } else { ?>
										<?php echo clean($row['theEmp']); ?>
									<?php } ?>
								</h4>
								<p>
									<i class="fa fa-envelope-o"></i> <?php echo clean($row['empEmail']); ?><br />
									<i class="fa fa-phone"></i> <?php echo $empPhone1; ?>
								</p>
							</div>
							<div class="col-md-4">
								<p><?php echo nl2br(clean($empAddress1)); ?></p>
							</div>
							<div class="col-md-3">
								<p class="text-right">
									<strong><?php echo clean($row['empPosition']); ?></strong><br />
									<?php echo $hireDateField.': '.$row['empHireDate']; ?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>