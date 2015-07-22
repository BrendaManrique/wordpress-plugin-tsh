<?php
	$datePicker = 'true';
	$jsFile = 'newEmployee';

	// Add New Employee Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'newEmployee') {
        // Validation
        if($_POST['empFirst'] == "") {
            $msgBox = alertBox($empFirstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empLast'] == "") {
            $msgBox = alertBox($empLastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empHireDate'] == "") {
            $msgBox = alertBox($dateOfHireReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empEmail'] == "") {
            $msgBox = alertBox($empEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password1'] == "") {
            $msgBox = alertBox($empAccPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password1'] != $_POST['password2']) {
			$msgBox = alertBox($empAccPassReq, "<i class='fa fa-warning'></i>", "warning");
        } else if($_POST['empPhone1'] == "") {
            $msgBox = alertBox($empPhoneReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empAddress1'] == "") {
            $msgBox = alertBox($empMailingAddyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$empFirst = $mysqli->real_escape_string($_POST['empFirst']);
			if (isset($_POST['empMiddleInt'])) {
				$empMiddleInt = $mysqli->real_escape_string($_POST['empMiddleInt']);
			} else {
				$empMiddleInt = '';
			}
			$empLast = $mysqli->real_escape_string($_POST['empLast']);
			$empHireDate = $mysqli->real_escape_string($_POST['empHireDate']).' 00:00:00';
			$empEmail = $mysqli->real_escape_string($_POST['empEmail']);
			if (isset($_POST['empMiddleInt'])) {
				$empMiddleInt = $mysqli->real_escape_string($_POST['empMiddleInt']);
			} else {
				$empMiddleInt = '';
			}
			$empPhone1 = encryptIt($_POST['empPhone1']);
			if (isset($_POST['empPhone2'])) {
				$empPhone2 = encryptIt($_POST['empPhone2']);
			} else {
				$empPhone2 = '';
			}
			if (isset($_POST['empPhone3'])) {
				$empPhone3 = encryptIt($_POST['empPhone3']);
			} else {
				$empPhone3 = '';
			}
			$empAddress1 = encryptIt($_POST['empAddress1']);
			if (isset($_POST['empAddress2'])) {
				$empAddress2 = encryptIt($_POST['empAddress2']);
			} else {
				$empAddress2 = '';
			}
			$setAdmin = $mysqli->real_escape_string($_POST['setAdmin']);
			$setManager = $mysqli->real_escape_string($_POST['setManager']);
			$dupEmail = '';

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM employees WHERE empEmail = '".$empEmail."'");
			if ($check->num_rows) {
				$dupEmail = 'true';
			}

			// If duplicates are found
			if ($dupEmail != '') {
				$msgBox = alertBox($acctExistsMsg, "<i class='fa fa-warning'></i>", "warning");
			} else {
				// Create the new account and set it to Active
				$isActive = '1';
				$password = encryptIt($_POST['password1']);

				$stmt = $mysqli->prepare("
									INSERT INTO
										employees(
											isAdmin,
											isMgr,
											empEmail,
											password,
											empFirst,
											empMiddleInt,
											empLast,
											empPhone1,
											empPhone2,
											empPhone3,
											empAddress1,
											empAddress2,
											empHireDate,
											isActive
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('ssssssssssssss',
					$setAdmin,
					$setManager,
					$empEmail,
					$password,
					$empFirst,
					$empMiddleInt,
					$empLast,
					$empPhone1,
					$empPhone2,
					$empPhone3,
					$empAddress1,
					$empAddress2,
					$empHireDate,
					$isActive
				);
				$stmt->execute();
				$msgBox = alertBox($empAcctCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
				// Clear the form of Values
				$_POST['empFirst'] = $_POST['empMiddleInt'] = $_POST['empLast'] = $_POST['empHireDate'] = $_POST['empEmail'] = '';
				$_POST['empPhone1'] = $_POST['empPhone2'] = $_POST['empPhone3'] = $_POST['empAddress1'] = $_POST['empAddress2'] = $_POST['empPosition'] = '';
				$stmt->close();
			}
		}
	}

	include 'includes/navigation.php';

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
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<li><a href="index.php?page=activeEmployees"><i class="fa fa-group"></i> <?php echo $activeEmpNav; ?></a></li>
			<li><a href="index.php?page=inactiveEmployees"><i class="fa fa-ban"></i> <?php echo $inactiveEmpNav; ?></a></li>
			<li class="pull-right"><a href="#home" data-toggle="tab" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newEmpPage; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<form action="" method="post">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="empFirst"><?php echo $firstNameField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empFirst" value="<?php echo isset($_POST['empFirst']) ? $_POST['empFirst'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="empMiddleInt"><?php echo $miField; ?></label>
								<input type="text" class="form-control" name="empMiddleInt" value="<?php echo isset($_POST['empMiddleInt']) ? $_POST['empMiddleInt'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label for="empLast"><?php echo $lastNameField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empLast" value="<?php echo isset($_POST['empLast']) ? $_POST['empLast'] : ''; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="empHireDate"><?php echo $hireDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empHireDate" id="empHireDate" value="<?php echo isset($_POST['empHireDate']) ? $_POST['empHireDate'] : ''; ?>" />
								<span class="help-block"><?php echo $hireDateFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="empEmail"><?php echo $accountEmailAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empEmail" value="<?php echo isset($_POST['empEmail']) ? $_POST['empEmail'] : ''; ?>" />
								<span class="help-block"><?php echo $accountEmailAddyFieldHelp2; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password1"><?php echo $passwordField; ?> <sup><?php echo $reqField; ?></sup></label>
								<div class="input-group">
									<input type="password" class="form-control" required="" name="password1" id="password1" value="" />
									<span class="input-group-addon"><a href="" id="generate" data-toggle="tooltip" data-placement="top" title="<?php echo $generatePassTooltip; ?>"><i class="fa fa-key"></i></a></span>
								</div>
								<span class="help-block">
									<a href="" id="showIt" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
									<a href="" id="hideIt" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
								</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password2"><?php echo $repeatAccPassField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="password" class="form-control" required="" name="password2" id="password2" value="" />
								<span class="help-block"><?php echo $repeatAccPassFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone1"><?php echo $primPhoneField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empPhone1" value="<?php echo isset($_POST['empPhone1']) ? $_POST['empPhone1'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone2"><?php echo $altPhone1; ?></label>
								<input type="text" class="form-control" name="empPhone2" id="empPhone2" value="<?php echo isset($_POST['empPhone2']) ? $_POST['empPhone2'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone3"><?php echo $altPhone1; ?></label>
								<input type="text" class="form-control" name="empPhone3" id="empPhone3" value="<?php echo isset($_POST['empPhone3']) ? $_POST['empPhone3'] : ''; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="empAddress1"><?php echo $mailingAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
								<textarea class="form-control" name="empAddress1" required="" rows="3"><?php echo isset($_POST['empAddress1']) ? $_POST['empAddress1'] : ''; ?></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="empAddress2"><?php echo $altAddyField; ?></label>
								<textarea class="form-control" name="empAddress2" rows="3"><?php echo isset($_POST['empAddress2']) ? $_POST['empAddress2'] : ''; ?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="setAdmin"><?php echo $adminAccField; ?></label>
								<select class="form-control" name="setAdmin">
									<option value="0" selected><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $adminAccFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="setManager"><?php echo $accountTypeField; ?></label>
								<select class="form-control" name="setManager">
									<option value="0" selected><?php echo $employeeText; ?></option>
									<option value="1"><?php echo $managerText; ?></option>
								</select>
							</div>
						</div>
					</div>
					<button type="input" name="submit" value="newEmployee" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addNewEmpBtn; ?></button>
				</form>
			</div>
		</div>
	</div>
<?php } ?>