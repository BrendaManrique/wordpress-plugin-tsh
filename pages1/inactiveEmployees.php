<?php
	$pagPages = '20';

	// Reactivate Employee Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'reactivateEmp') {
		$reactivateId = $mysqli->real_escape_string($_POST['reactivateId']);
		$isActive = '1';
		$stmt = $mysqli->prepare("UPDATE employees SET isActive = ? WHERE empId = ?");
		$stmt->bind_param('ss', $isActive, $reactivateId);
		$stmt->execute();
		$msgBox = alertBox($empReactivedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Delete Employee Account
	if ($isAdmin == '1') {
		if (isset($_POST['submit']) && $_POST['submit'] == 'deleteEmp') {
			$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
			// Delete Employee's Account
			$stmt = $mysqli->prepare("DELETE FROM employees WHERE empId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();
			$msgBox = alertBox($empDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM employees WHERE isActive = 0");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				empId,
				isAdmin,
				isMgr,
				empEmail,
				CONCAT(empFirst,' ',empLast) AS theEmp,
				empPosition,
				DATE_FORMAT(empHireDate,'%M %e, %Y') AS empHireDate,
				DATE_FORMAT(empLastVisited,'%M %e, %Y at %l:%i %p') AS empLastVisited
			FROM
				employees
			WHERE
				isActive = 0
			ORDER BY
				empId ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

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
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<li><a href="index.php?page=activeEmployees"><i class="fa fa-group"></i> <?php echo $activeEmpNav; ?></a></li>
			<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-ban"></i> <?php echo $inactiveEmpNav; ?></a></li>
			<?php if ($isAdmin == '1') { ?>
				<li class="pull-right"><a href="index.php?page=newEmployee" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newEmpPage; ?></a></li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<?php if(mysqli_num_rows($res) < 1) { ?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noInactEmpFound; ?>
					</div>
				<?php } else { ?>
					<table class="rwd-table">
						<tbody>
							<tr class="primary">
								<th><?php echo $empName; ?></th>
								<th><?php echo $emailField; ?></th>
								<th><?php echo $positionField; ?></th>
								<th><?php echo $hireDateField; ?></th>
								<th><?php echo $accountTypeField; ?></th>
								<th><?php echo $lastLoginField; ?></th>
								<th></th>
							</tr>
							<?php
								while ($row = mysqli_fetch_assoc($res)) {
									if ($row['isAdmin'] == '1') { $admin = $administratorText.'/'; } else { $admin = ''; }
									if ($row['isMgr'] == '1') { $mgr = $managerText; } else { $mgr = $employeeText; }
							?>
									<tr>
										<td data-th="<?php echo $empName; ?>">
											<?php if (($row['isAdmin'] != '1') || ($isAdmin == '1')) {  ?>
												<a href="index.php?page=viewEmployee&eid=<?php echo $row['empId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewEmpTooltip; ?>">
													<?php echo clean($row['theEmp']); ?>
												</a>
											<?php } else { ?>
												<?php echo clean($row['theEmp']); ?>
											<?php } ?>
										</td>
										<td data-th="<?php echo $emailField; ?>"><?php echo clean($row['empEmail']); ?></td>
										<td data-th="<?php echo $positionField; ?>"><?php echo clean($row['empPosition']); ?></td>
										<td data-th="<?php echo $hireDateField; ?>"><?php echo $row['empHireDate']; ?></td>
										<td data-th="<?php echo $accountTypeField; ?>"><?php echo $admin.$mgr; ?></td>
										<td data-th="<?php echo $lastLoginField; ?>"><?php echo $row['empLastVisited']; ?></td>
										<td data-th="<?php echo $actionText; ?>">
											<?php if (($row['isAdmin'] != '1') || ($isAdmin == '1')) {  ?>
												<a href="index.php?page=viewEmployee&eid=<?php echo $row['empId']; ?>">
													<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewEmpTooltip; ?>"></i>
												</a>
											<?php } else { ?>
												<i class="fa fa-edit text-muted" data-toggle="tooltip" data-placement="left" title="<?php echo $notAvailTooltip; ?>"></i>
											<?php
												}
												if ($isAdmin == '1') {
											?>
												<a data-toggle="modal" href="#activate<?php echo $row['empId']; ?>">
													<i class="fa fa-check-circle text-success" data-toggle="tooltip" data-placement="left" title="<?php echo $reactEmpAcctTooltip; ?>"></i>
												</a>
												<a data-toggle="modal" href="#delete<?php echo $row['empId']; ?>">
													<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deactivateAccTooltip; ?>"></i>
												</a>
											<?php } ?>
										</td>
									</tr>

									<?php if ($isAdmin == '1') { ?>
										<div class="modal fade" id="activate<?php echo $row['empId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $reactivateEmpConf.' '.clean($row['theEmp']); ?>?</p>
														</div>
														<div class="modal-footer">
															<input name="reactivateId" type="hidden" value="<?php echo $row['empId']; ?>" />
															<button type="input" name="submit" value="reactivateEmp" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
															<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>

										<div class="modal fade" id="delete<?php echo $row['empId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $deleteAccountConf.' '.clean($row['theEmp']); ?>?</p>
														</div>
														<div class="modal-footer">
															<input name="deleteId" type="hidden" value="<?php echo $row['empId']; ?>" />
															<button type="input" name="submit" value="deleteEmp" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteAcctBtn; ?></button>
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
						</tbody>
					</table>
				<?php
					}
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
				?>
			</div>
		</div>
	</div>
<?php } ?>