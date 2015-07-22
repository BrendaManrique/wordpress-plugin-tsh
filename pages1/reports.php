<?php
	$datePicker = 'true';
	$jsFile = 'reports';

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
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $employeeReportsTitle; ?></h3>
					</div>
					<div class="panel-body setHeight">
						<form action="index.php?page=empReport" method="post">
							<div class="form-group">
								<label for="showEmployees"><?php echo $includeEmpField; ?></label>
								<select class="form-control" id="showEmployees" name="showEmployees">
									<option value="0"><?php echo $includeEmpField0; ?></option>
									<option value="1"><?php echo $includeEmpField1; ?></option>
									<option value="2"><?php echo $includeEmpField2; ?></option>
								</select>
								<span class="help-block"><?php echo $includeEmpFieldHelp; ?></span>
							</div>
							<button type="input" name="submit" value="report1" class="btn btn-default btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $empTimeRptTitle; ?></h3>
					</div>
					<div class="panel-body setHeight">
						<form action="index.php?page=empTimeReport" method="post">
							<?php if ($isAdmin == '1') { ?>
								<div class="form-group">
									<label for="employee"><?php echo $selectEmpField; ?> <sup><?php echo $reqField; ?></sup></label>
									<select class="form-control" name="employee" id="employee">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get the Client List
											$qry1 = "SELECT
														empId,
														CONCAT(empFirst,' ',empLast) AS theEmp,
														isActive
													FROM
														employees";
											$res1 = mysqli_query($mysqli, $qry1) or die('-1'.mysqli_error());
											while ($a = mysqli_fetch_assoc($res1)) {
												if ($a['isActive'] == '0') { $mark = ' *'; } else { $mark = ''; }
										?>
												<option value="<?php echo $a['empId']; ?>"><?php echo clean($a['theEmp']).$mark; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="empFullName" id="empFullName" value="" />
									<span class="help-block"><?php echo $selectEmpFieldHelp; ?></span>
								</div>
							<?php } else { ?>
								<div class="form-group">
									<label for="employee"><?php echo $selectEmpField; ?> <sup><?php echo $reqField; ?></sup></label>
									<select class="form-control" name="employee" id="employee">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get the Client List
											$qry1 = "SELECT
														empId,
														CONCAT(empFirst,' ',empLast) AS theEmp,
														isActive
													FROM
														employees
													WHERE isAdmin != 1";
											$res1 = mysqli_query($mysqli, $qry1) or die('-2'.mysqli_error());
											while ($a = mysqli_fetch_assoc($res1)) {
												if ($a['isActive'] == '0') { $mark = '*'; } else { $mark = ''; }
										?>
												<option value="<?php echo $a['empId']; ?>"><?php echo clean($a['theEmp']).' '.$mark; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="empFullName" id="empFullName" value="" />
									<span class="help-block"><?php echo $selectEmpFieldHelp; ?></span>
								</div>
							<?php } ?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="fromDate"><?php echo $selectFromDateField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" required="" name="fromDate" id="fromDate" value="">
										<span class="help-block"><?php echo $selectFromDateHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="toDate"><?php echo $selectToDateField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" required="" name="toDate" id="toDate" value="">
										<span class="help-block"><?php echo $selectToDateHelp; ?></span>
									</div>
								</div>
							</div>
							<button type="input" name="submit" value="clientReport2" class="btn btn-default btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>