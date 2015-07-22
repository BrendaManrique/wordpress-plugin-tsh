<?php
	$eid = $_GET['eid'];
	$datePicker = 'true';
	$jsFile = 'viewEmployee';
	$pagPages = '10';

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		// Get the Employee's avatar url
		$sql = "SELECT empAvatar FROM employees WHERE empId = ".$eid;
		$result = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['empAvatar'];

		$filePath = $avatarDir.$avatarName;
		// Delete the Employee's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Employee record
			$empAvatar = 'empAvatar.png';
			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									empAvatar = ?
								WHERE
									empId = ?");
			$stmt->bind_param('ss',
							   $empAvatar,
							   $eid);
			$stmt->execute();
			$msgBox = alertBox($empAvatarRemMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($empAvatarRemError, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Update Employee Data
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAccount') {
		// Validation
		if($_POST['empFirst'] == "") {
            $msgBox = alertBox($firstNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empLast'] == "") {
            $msgBox = alertBox($lastNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empPhone1'] == "") {
            $msgBox = alertBox($primaryPhoneReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empAddress1'] == "") {
            $msgBox = alertBox($mailingAddyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$empFirst = $mysqli->real_escape_string($_POST['empFirst']);
			if (isset($_POST['empMiddleInt'])) {
				$empMiddleInt = $mysqli->real_escape_string($_POST['empMiddleInt']);
			} else {
				$empMiddleInt = '';
			}
			$empLast = $mysqli->real_escape_string($_POST['empLast']);
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
			if (isset($_POST['empDob'])) {
				$empDob = $mysqli->real_escape_string($_POST['empDob']).' 00:00:00';
			} else {
				$empDob = '';
			}
			if (isset($_POST['empSsn'])) {
				$empSsn = encryptIt($_POST['empSsn']);
			} else {
				$empSsn = '';
			}

			$stmt = $mysqli->prepare("UPDATE
										employees
									SET
										empFirst = ?,
										empMiddleInt = ?,
										empLast = ?,
										empPhone1 = ?,
										empPhone2 = ?,
										empPhone3 = ?,
										empAddress1 = ?,
										empAddress2 = ?,
										empDob = ?,
										empSsn = ?
									WHERE
										empId = ?"
			);
			$stmt->bind_param('sssssssssss',
									$empFirst,
									$empMiddleInt,
									$empLast,
									$empPhone1,
									$empPhone2,
									$empPhone3,
									$empAddress1,
									$empAddress2,
									$empDob,
									$empSsn,
									$eid
			);
			$stmt->execute();
			$msgBox = alertBox($empAccUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Employee Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		// Validation
		if($_POST['empEmail'] == "") {
            $msgBox = alertBox($validEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$empEmail = $mysqli->real_escape_string($_POST['empEmail']);

			$stmt = $mysqli->prepare("UPDATE
										employees
									SET
										empEmail = ?
									WHERE
										empId = ?"
			);
			$stmt->bind_param('ss', $empEmail, $eid);
			$stmt->execute();
			$msgBox = alertBox($empEmailAddyUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Employee's Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'changePassword') {
		// Validation
		if($_POST['password'] == '') {
			$msgBox = alertBox($newPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($retypePassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($empAccPassReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = encryptIt($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}

			$stmt = $mysqli->prepare("UPDATE
										employees
									SET
										password = ?
									WHERE
										empId = ?"
			);
			$stmt->bind_param('ss', $password, $eid);
			$stmt->execute();
			$msgBox = alertBox($empPassUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Employee Data
	if (isset($_POST['submit']) && $_POST['submit'] == 'position') {
		// Validation
		if($_POST['empPosition'] == "") {
            $msgBox = alertBox($empPositionReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empHireDate'] == "") {
            $msgBox = alertBox($empDateOfHireReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empStartSalery'] == "") {
            $msgBox = alertBox($empStartSalaryReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empCurrSalery'] == "") {
            $msgBox = alertBox($empCurrSalarayReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['empSalaryTerm'] == "") {
            $msgBox = alertBox($salaryTermReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['leaveHours'] == "") {
            $msgBox = alertBox($leavePerWeekReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$empPosition = $mysqli->real_escape_string($_POST['empPosition']);
			$empHireDate = $mysqli->real_escape_string($_POST['empHireDate']).' 00:00:00';
			$empPayGrade = $mysqli->real_escape_string($_POST['empPayGrade']);
			$empStartSalery = $mysqli->real_escape_string($_POST['empStartSalery']);
			$empCurrSalery = $mysqli->real_escape_string($_POST['empCurrSalery']);
			$empSalaryTerm = $mysqli->real_escape_string($_POST['empSalaryTerm']);
			$leaveHours = $mysqli->real_escape_string($_POST['leaveHours']);
			$setMgr = $mysqli->real_escape_string($_POST['isMgr']);
			$setAdmin = $mysqli->real_escape_string($_POST['isAdmin']);

			$stmt = $mysqli->prepare("UPDATE
										employees
									SET
										empPosition = ?,
										empHireDate = ?,
										empPayGrade = ?,
										empStartSalery = ?,
										empCurrSalery = ?,
										empSalaryTerm = ?,
										leaveHours = ?,
										isMgr = ?,
										isAdmin = ?
									WHERE
										empId = ?"
			);
			$stmt->bind_param('ssssssssss',
									$empPosition,
									$empHireDate,
									$empPayGrade,
									$empStartSalery,
									$empCurrSalery,
									$empSalaryTerm,
									$leaveHours,
									$setMgr,
									$setAdmin,
									$eid
			);
			$stmt->execute();
			$msgBox = alertBox($empPosPayUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Terminate Employee
	if (isset($_POST['submit']) && $_POST['submit'] == 'terminate') {
		if ($eid == '1') {
			// Block Terminating the Primary Admin Account
			$msgBox = alertBox($empTermError, "<i class='icon-warning'></i>", "warning");
		} else {
			$setTermenated = $mysqli->real_escape_string($_POST['setTermenated']);
			// Validations
			if(($setTermenated == '1') && ($_POST['empTerminationDate'] == '')) {
				$msgBox = alertBox($termDateReq, "<i class='icon-remove-sign'></i>", "danger");
			} else if(($setTermenated == '1') && ($_POST['terminationReason'] == '')) {
				$msgBox = alertBox($termReasonReq, "<i class='icon-remove-sign'></i>", "danger");
			} else {
				if ($setTermenated == '1') {
					$isActive = '0';
					$empTerminationDate = $mysqli->real_escape_string($_POST['empTerminationDate']);
					$terminationReason = $mysqli->real_escape_string($_POST['terminationReason']);
				} else {
					$isActive = '1';
					$empTerminationDate = '0000-00-00 00:00:00';
					$terminationReason = '';
				}

				$stmt = $mysqli->prepare("UPDATE
											employees
										SET
											isActive = ?,
											empTerminationDate = ?,
											terminationReason = ?
										WHERE
											empId = ?"
				);
				$stmt->bind_param('ssss',
										$isActive,
										$empTerminationDate,
										$terminationReason,
										$eid
				);
				$stmt->execute();
				$msgBox = alertBox($empTermStatusUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
				$stmt->close();
			}
		}
	}

	// Start/Stop the Time Clock
	if (isset($_POST['submit']) && $_POST['submit'] == 'toggleTime') {
		$isRecord = $mysqli->real_escape_string($_POST['isRecord']);

		if ($isRecord != '0') {
			// Record All Ready Exists
			$clockId = $mysqli->real_escape_string($_POST['clockId']);
			$entryId = $mysqli->real_escape_string($_POST['entryId']);
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = $mysqli->real_escape_string($_POST['running']);
			$entryDate = $endTime = date("Y-m-d");
			$startTime = $endTime = date("Y-m-d H:i:s");

			if ($running == '0') {
				// Start Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 1
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Start Clock - Add a new time entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											empId,
											entryDate,
											startTime
										) VALUES (
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssss',
									$clockId,
									$eid,
									$entryDate,
									$startTime
				);
				$stmt->execute();
				$stmt->close();
			} else {
				// Stop Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 0
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Stop Clock - Update the time entry
				$stmt = $mysqli->prepare("
									UPDATE
										timeentry
									SET
										endTime = ?
									WHERE
										entryId = ?
				");
				$stmt->bind_param('ss',
									$endTime,
									$entryId
				);
				$stmt->execute();
				$stmt->close();
			}
		} else {
			// Record Does Not Exist
			// Start Clock - Create a timeclock Record
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = '1';
			$entryDate = $endTime = date("Y-m-d");
			$startTime = date("Y-m-d H:i:s");

			$sqlstmt = $mysqli->prepare("
								INSERT INTO
									timeclock(
										empId,
										weekNo,
										clockYear,
										running
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$sqlstmt->bind_param('ssss',
									$eid,
									$weekNo,
									$clockYear,
									$running
			);
			$sqlstmt->execute();
			$sqlstmt->close();

			// Get the new Tracking ID
			$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE empId = ".$eid." AND weekNo = '".$weekNo."' AND clockYear = ".$clockYear);
			$id = mysqli_fetch_assoc($track_id);
			$clockId = $id['clockId'];

			// Start Clock - Add a new time entry
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeentry(
										clockId,
										empId,
										entryDate,
										startTime
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$clockId,
								$eid,
								$entryDate,
								$startTime
			);
			$stmt->execute();
			$stmt->close();
		}
	}

	// Add Leave
	if (isset($_POST['submit']) && $_POST['submit'] == 'addLeave') {
		// Validations
		if($_POST['addHours'] == '') {
			$msgBox = alertBox($addLeaveHoursReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['weekNo'] == '') {
			$msgBox = alertBox($weekNumberReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['payYear'] == '') {
			$msgBox = alertBox($yearReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$addHours = $mysqli->real_escape_string($_POST['addHours']);
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$payYear = $mysqli->real_escape_string($_POST['payYear']);
			$dateEntered = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									leaveearned(
										empId,
										weekNo,
										clockYear,
										leaveHours,
										dateEntered
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssss',
								$eid,
								$weekNo,
								$payYear,
								$addHours,
								$dateEntered
			);
			$stmt->execute();
			$msgBox = alertBox($addHoursSavedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Subtract Leave
	if (isset($_POST['submit']) && $_POST['submit'] == 'takeLeave') {
		// Validations
		if($_POST['subHours'] == '') {
			$msgBox = alertBox($hoursTakenReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['payYear'] == '') {
			$msgBox = alertBox($yearReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$subHours = $mysqli->real_escape_string($_POST['subHours']);
			$payYear = $mysqli->real_escape_string($_POST['payYear']);
			$dateEntered = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									leavetaken(
										empId,
										clockYear,
										hoursTaken,
										dateEntered
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$eid,
								$payYear,
								$subHours,
								$dateEntered
			);
			$stmt->execute();
			$msgBox = alertBox($leaveTakenSavedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				empId, isAdmin, isMgr,
				empEmail, password,
				empFirst, IFNULL(empMiddleInt,'') AS empMiddleInt, empLast,
				DATE_FORMAT(empDob,'%Y-%m-%d') AS empDob,
				DATE_FORMAT(empDob,'%M %e, %Y') AS birthDate,
				empSsn, empAvatar,
				empPhone1, empPhone2, empPhone3,
				empAddress1, empAddress2,
				empPosition, empPayGrade,
				empStartSalery, empStartHourly, empCurrSalery,
				empCurrHourly, empSalaryTerm, leaveHours,
				DATE_FORMAT(empHireDate,'%Y-%m-%d') AS empHireDate,
				DATE_FORMAT(empHireDate,'%M %d, %Y') AS hireDate,
				isActive, empLastVisited,
				DATE_FORMAT(empLastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited,
				empTerminationDate,
				DATE_FORMAT(empTerminationDate,'%Y-%m-%d') AS empTermDate,
				DATE_FORMAT(empTerminationDate,'%M %e, %Y') AS terminationDate,
				terminationReason
			FROM
				employees
			WHERE empId = ".$eid;
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($set['enablePii'] == '1' && $isAdmin == '1') {
		if ($row['empSsn'] != '') {
			$empSsn = decryptIt($row['empSsn']);
		} else {
			$empSsn = '';
		}
		if ($row['empDob'] != '0000-00-00') {
			$empDob = $row['empDob'];
			$birthDate = $row['birthDate'];
		} else {
			$empDob = '';
			$birthDate = '';
		}
	}
	if ($row['empSsn'] != '') { $empSsn = decryptIt($row['empSsn']); } else { $empSsn = '';  }
	if ($row['empPhone1'] != '') { $empPhone1 = decryptIt($row['empPhone1']); } else { $empPhone1 = '';  }
	if ($row['empPhone2'] != '') { $empPhone2 = decryptIt($row['empPhone2']); } else { $empPhone2 = '';  }
	if ($row['empPhone3'] != '') { $empPhone3 = decryptIt($row['empPhone3']); } else { $empPhone3 = '';  }
	if ($row['empAddress1'] != '') { $empAddress1 = decryptIt($row['empAddress1']); } else { $empAddress1 = '';  }
	if ($row['empAddress2'] != '') { $empAddress2 = decryptIt($row['empAddress2']); } else { $empAddress2 = '';  }

	// Set some variables
	$empFullName = clean($row['empFirst']).' '.clean($row['empMiddleInt']).' '.clean($row['empLast']);
	if ($row['isAdmin'] == '1') {
		$role = $administratorText;
	} else if ($row['isMgr'] == '1') {
		$role = $managerText;
	} else {
		$role = $employeeText;
	}
	if ($row['isAdmin'] == '1') { $selected1 = 'selected'; } else { $selected1 = ''; }
	if ($row['isMgr'] == '1') { $selected2 = 'selected'; } else { $selected2 = ''; }
	if ($row['isActive'] == '0') { $selected3 = 'selected'; } else { $selected3 = ''; }

	if ($row['empTerminationDate'] != '0000-00-00 00:00:00') {
		$isTermed = '1';
		$empTerminationDate = $row['empTermDate'];
	} else {
		$isTermed = '0';
		$empTerminationDate = '';
	}

	// Employee's Current Status & Time Clock
	// Check for an Existing Record
	$check = $mysqli->query("SELECT 'X' FROM timeclock WHERE empId = ".$eid." AND weekNo = '".$weekNum."'");
	if ($check->num_rows) {
		$checked = "SELECT
						clockId,
						empId,
						weekNo,
						clockYear,
						running
					FROM
						timeclock
					WHERE
						empId = ".$eid." AND weekNo = '".$weekNum."'";
		$checkres = mysqli_query($mysqli, $checked) or die('-3'.mysqli_error());
		$col = mysqli_fetch_assoc($checkres);
		$clockId = $col['clockId'];
		$running = $col['running'];

		$sel = "SELECT
					clockId,
					entryId
				FROM
					timeentry
				WHERE
					clockId = ".$clockId." AND
					empId = ".$eid." AND
					endTime = '0000-00-00'";
		$selresult = mysqli_query($mysqli, $sel) or die('-4'.mysqli_error());
		$rows = mysqli_fetch_assoc($selresult);
		$entryId = (is_null($rows['entryId'])) ? '' : $rows['entryId'];
		$isRecord = '1';

		// Get Total Time Worked for the Current Week
		$qry1 = "SELECT
					TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
				FROM
					timeclock
					LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
				WHERE
					timeclock.empId = ".$eid." AND
					timeclock.weekNo = '".$weekNum."' AND
					timeclock.clockYear = '".$currentYear."' AND
					timeentry.endTime != '0000-00-00 00:00:00'";
		$results = mysqli_query($mysqli, $qry1) or die('-5'.mysqli_error());
		$times = array();
		while ($u = mysqli_fetch_assoc($results)) {
			$times[] = $u['diff'];
		}
		$totalTime = sumHours($times);
	} else {
		$clockId = '';
		$entryId = '';
		$running = $isRecord = '0';
		$totalTime = '00:00:00';
	}

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$avail = new paginator($pagPages,'p');

	// Get the number of total records
	$availrows = $mysqli->query("SELECT * FROM leaveearned WHERE empId = ".$eid." AND clockYear = ".$currentYear);
	$availtotal = mysqli_num_rows($availrows);

	// Pass the number of total records
	$avail->set_total($availtotal);

	// Get Leave Earned
    $qry1 = "SELECT
				earnedId,
				empId,
				weekNo,
				clockYear,
				leaveHours,
				DATE_FORMAT(dateEntered,'%M %d, %Y') AS dateEntered
			FROM
				leaveearned
			WHERE empId = ".$eid." AND clockYear = ".$currentYear."
			ORDER BY weekNo ".$avail->get_limit();
    $qry1res = mysqli_query($mysqli, $qry1) or die('-6' . mysqli_error());

	// Create new object & pass in the number of pages and an identifier
	$taken = new paginator($pagPages,'q');

	// Get the number of total records
	$takenrows = $mysqli->query("SELECT * FROM leavetaken WHERE empId = ".$eid." AND clockYear = ".$currentYear);
	$takentotal = mysqli_num_rows($takenrows);

	// Pass the number of total records
	$taken->set_total($takentotal);

	// Get Leave Taken
	$qry2 = "SELECT
				takenId,
				empId,
				clockYear,
				hoursTaken,
				DATE_FORMAT(dateEntered,'%M %d, %Y') AS dateEntered
			FROM
				leavetaken
			WHERE empId = ".$eid." AND clockYear = ".$currentYear."
			ORDER BY takenId ".$taken->get_limit();
    $qry2res = mysqli_query($mysqli, $qry2) or die('-7' . mysqli_error());

	// Get Leave Balances
	$earnedbal = "SELECT SUM(leaveHours) AS curBalance FROM leaveearned WHERE empId = ".$eid." AND clockYear = ".$currentYear;
	$earnedres = mysqli_query($mysqli, $earnedbal) or die('-8' . mysqli_error());
	$earned = mysqli_fetch_assoc($earnedres);

	$takenbal = "SELECT SUM(hoursTaken) AS takenBalance FROM leavetaken WHERE empId = ".$eid." AND clockYear = ".$currentYear;
	$takenres = mysqli_query($mysqli, $takenbal) or die('-9' . mysqli_error());
	$amttaken = mysqli_fetch_assoc($takenres);
	if ($amttaken['takenBalance'] != '') { $takenBalance = $amttaken['takenBalance']; } else { $takenBalance = '0'; }

	$availableBalance = $earned['curBalance'] - $amttaken['takenBalance'];
	if ($availableBalance < 0) { $isNeg = 'class="text-danger"'; } else { $isNeg = ''; }

	include 'includes/navigation.php';

	if (($row['isAdmin'] != '1') || ($isAdmin == '1')) {
		if (($isAdmin != '1') && ($isMgr != '1')) {
?>
		<div class="content">
			<h3><?php echo $accessErrorHeader; ?></h3>
			<div class="alertMsg danger">
				<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
			</div>
		</div>
	<?php } else { ?>
		<div class="contentAlt">
			<div class="row">
				<div class="col-md-4">
					<div class="content text-center no-margin profileHgt">
						<img src="<?php echo $avatarDir.$row['empAvatar']; ?>" alt="<?php echo $empFullName; ?>" class="empAvatar viewEmp" />
						<p class="lead mt20"><?php echo $empFullName; ?></p>
						<p class="mb0">
							<?php echo clean($row['empEmail']); ?><br />
							<?php echo $empPhone1; ?>
						</p>
						<div class="text-center mt20">
							<a data-toggle="modal" href="#deleteAvatar" class="btn btn-default">
								<i class="fa fa-picture-o" data-toggle="tooltip" data-placement="top" title="<?php echo $remAvatarTooltip; ?>"></i>
							</a>
							<a data-toggle="modal" href="#updateAccount" class="btn btn-default">
								<i class="fa fa-user" data-toggle="tooltip" data-placement="top" title="<?php echo $updtEmpDataTooltip; ?>"></i>
							</a>
							<a data-toggle="modal" href="#updateEmail" class="btn btn-default">
								<i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="<?php echo $updtEmpEmailTooltip; ?>"></i>
							</a>
							<a data-toggle="modal" href="#changePassword" class="btn btn-default">
								<i class="fa fa-lock" data-toggle="tooltip" data-placement="top" title="<?php echo $changeEmpPassTooltip; ?>"></i>
							</a>
							<?php if ($isAdmin == '1') { ?>
								<a data-toggle="modal" href="#position" class="btn btn-default">
									<i class="fa fa-money" data-toggle="tooltip" data-placement="top" title="<?php echo $updateEmpPosTooltip; ?>"></i>
								</a>
								<a data-toggle="modal" href="#terminate" class="btn btn-default">
									<i class="fa fa-ban" data-toggle="tooltip" data-placement="top" title="<?php echo $termEmpTooltip; ?>"></i>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>

				<div id="deleteAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form action="" method="post">
								<div class="modal-body">
									<p class="lead"><?php echo $deleteEmpAvatarConf; ?></p>
								</div>
								<div class="modal-footer">
									<button type="input" name="submit" value="deleteAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div id="updateAccount" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $updtEmpDataTooltip; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="row">
										<div class="col-md-5">
											<div class="form-group">
												<label for="empFirst"><?php echo $firstNameField; ?> <sup><?php echo $reqField; ?></sup></label>
												<input type="text" class="form-control" required="" name="empFirst" value="<?php echo clean($row['empFirst']); ?>" />
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="empMiddleInt"><?php echo $miField; ?></label>
												<input type="text" class="form-control" name="empMiddleInt" value="<?php echo clean($row['empMiddleInt']); ?>" />
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label for="empLast"><?php echo $lastNameField; ?> <sup><?php echo $reqField; ?></sup></label>
												<input type="text" class="form-control" required="" name="empLast" value="<?php echo clean($row['empLast']); ?>" />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="empPhone1"><?php echo $primPhoneField; ?> <sup><?php echo $reqField; ?></sup></label>
												<input type="text" class="form-control" required="" name="empPhone1" value="<?php echo $empPhone1; ?>" />
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="empPhone2"><?php echo $altPhone1; ?></label>
												<input type="text" class="form-control" name="empPhone2" value="<?php echo $empPhone2; ?>" />
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="empPhone3"><?php echo $altPhone1; ?></label>
												<input type="text" class="form-control" name="empPhone3" value="<?php echo $empPhone3; ?>" />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="empAddress1"><?php echo $mailingAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
												<textarea class="form-control" name="empAddress1" required="" rows="3"><?php echo $empAddress1; ?></textarea>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="empAddress2"><?php echo $altAddyField; ?></label>
												<textarea class="form-control" name="empAddress2" rows="3"><?php echo $empAddress2; ?></textarea>
											</div>
										</div>
									</div>
									<?php if ($set['enablePii'] == '1' && $isAdmin == '1') { ?>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="empDob"><?php echo $dobField; ?></label>
													<input type="text" class="form-control" name="empDob" id="empDob" value="<?php echo $empDob; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="empSsn"><?php echo $ssnField; ?></label>
													<input type="text" class="form-control" name="empSsn" value="<?php echo $empSsn; ?>" />
												</div>
											</div>
										</div>
									<?php } ?>

								</div>
								<div class="modal-footer">
									<button type="input" name="submit" value="updateAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updtEmpDataTooltip; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div id="updateEmail" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $updEmpEmailModal; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="empEmail"><?php echo $emailAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="empEmail" required="" value="<?php echo clean($row['empEmail']); ?>" />
										<span class="help-block"><?php echo $empEmailAddFieldHelp; ?></span>
									</div>
								</div>
								<div class="modal-footer">
									<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updtEmpEmailTooltip; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div id="changePassword" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $changeEmpPassModal; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="password"><?php echo $newPasswordField; ?> <sup><?php echo $reqField; ?></sup></label>
												<input type="text" class="form-control" name="password" required="" value="" />
												<span class="help-block"><?php echo $typeNewEmpPassHelp; ?></span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="password_r">Confirm <?php echo $newPasswordField; ?> <sup><?php echo $reqField; ?></sup></label>
												<input type="text" class="form-control" name="password_r" required="" value="" />
												<span class="help-block"><?php echo $confNewPasswordFieldHelp; ?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>" />
									<button type="input" name="submit" value="changePassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $changeEmpPassTooltip; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<?php if ($isAdmin == '1') { ?>
					<div id="position" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $updateEmpPosTooltip; ?></h4>
								</div>
								<form action="" method="post">
									<div class="modal-body">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="empPosition"><?php echo $posTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="empPosition" value="<?php echo clean($row['empPosition']); ?>" />
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="empHireDate"><?php echo $hireDateField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="empHireDate" id="empHireDate" value="<?php echo clean($row['empHireDate']); ?>" />
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="empPayGrade"><?php echo $payGradeField; ?></label>
													<input type="text" class="form-control" name="empPayGrade" value="<?php echo clean($row['empPayGrade']); ?>" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="empStartSalery"><?php echo $startSalaryField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="empStartSalery" value="<?php echo clean($row['empStartSalery']); ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="empCurrSalery"><?php echo $currSalaryField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="empCurrSalery" value="<?php echo clean($row['empCurrSalery']); ?>" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="empSalaryTerm"><?php echo $salaryTermField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="empSalaryTerm" value="<?php echo clean($row['empSalaryTerm']); ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="leaveHours"><?php echo $leavePerWeekField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" required="" name="leaveHours" value="<?php echo $row['leaveHours']; ?>" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="isMgr"><?php echo $mngrAccField; ?></label>
													<select class="form-control" name="isMgr">
														<option value="0"><?php echo $noBtn; ?></option>
														<option value="1" <?php echo $selected2; ?>><?php echo $yesBtn; ?></option>
													</select>
													<span class="help-block"><?php echo $mngrAccFieldHelp; ?></span>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="isAdmin"><?php echo $adminAccField; ?></label>
													<select class="form-control" name="isAdmin">
														<option value="0"><?php echo $noBtn; ?></option>
														<option value="1" <?php echo $selected1; ?>><?php echo $yesBtn; ?></option>
													</select>
													<span class="help-block"><?php echo $adminAccFieldHelp; ?></span>
												</div>
											</div>
										</div>

									</div>
									<div class="modal-footer">
										<button type="input" name="submit" value="position" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateEmpPosTooltip; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>

					<div id="terminate" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $termEmpTooltip; ?></h4>
								</div>
								<form action="" method="post">
									<div class="modal-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="setTermenated"><?php echo $termEmpTooltip; ?>?</label>
													<select class="form-control" name="setTermenated" id="setTermenated">
														<option value="0"><?php echo $noBtn; ?></option>
														<option value="1" <?php echo $selected3; ?>><?php echo $yesBtn; ?></option>
													</select>
													<span class="help-block"><?php echo $termEmpFieldHelp; ?></span>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="empTerminationDate"><?php echo $termDateField; ?> <sup><?php echo $reqField; ?></sup></label>
													<input type="text" class="form-control" name="empTerminationDate" id="empTerminationDate" value="<?php echo $empTerminationDate; ?>" />
													<span class="help-block"><?php echo $termDateFieldHelp; ?></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="terminationReason"><?php echo $termReasonField; ?> <sup><?php echo $reqField; ?></sup></label>
											<input type="text" class="form-control" name="terminationReason" id="terminationReason" value="<?php echo clean($row['terminationReason']); ?>" />
											<span class="help-block"><?php echo $termReasonFieldHelp; ?></span>
										</div>
									</div>
									<div class="modal-footer">
										<button type="input" name="submit" value="terminate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updTermStatusBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="col-md-8">
					<div class="content no-margin profileHgt">
						<?php if ($msgBox) { echo $msgBox; } ?>

						<div class="row">
							<div class="col-md-6">
								<table class="infoTable no-margin">
									<tr><td class="infoVal"><strong><?php echo $hireDateField; ?>:</strong> <?php echo $row['hireDate']; ?></td></tr>
									<tr><td class="infoVal"><strong><?php echo $posTitleField; ?>:</strong> <?php echo clean($row['empPosition']); ?></td></tr>
									<tr><td class="infoVal"><strong><?php echo $startSalaryField; ?>:</strong> <?php echo $curSym.clean($row['empStartSalery']).' / '.clean($row['empSalaryTerm']); ?></td></tr>
								</table>

								<table class="infoTable mt20">
									<tr><td class="infoVal"><strong><?php echo $altPhone1; ?>:</strong> <?php echo $empPhone2; ?></td></tr>
									<tr><td class="infoVal profileAddress"><strong><?php echo $mailingAddyField; ?>:</strong><br /> <?php echo nl2br($empAddress1); ?></td></tr>
								</table>
							</div>
							<div class="col-md-6">
								<table class="infoTable no-margin">
									<tr><td class="infoVal"><strong><?php echo $lastLoginField; ?>:</strong> <?php echo $row['lastVisited']; ?></td></tr>
									<tr><td class="infoVal"><strong><?php echo $payGradeField; ?>:</strong> <?php echo clean($row['empPayGrade']); ?></td></tr>
									<tr><td class="infoVal"><strong><?php echo $currSalaryField; ?>:</strong> <?php echo $curSym.clean($row['empCurrSalery']).' / '.clean($row['empSalaryTerm']); ?></td></tr>
								</table>

								<table class="infoTable mt20">
									<tr><td class="infoVal"><strong><?php echo $altPhone1; ?>:</strong> <?php echo $empPhone3; ?></td></tr>
									<tr><td class="infoVal profileAddress"><strong><?php echo $altAddyField; ?>:</strong><br /><?php echo nl2br($empAddress2); ?></td></tr>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<?php if ($set['enablePii'] == '1' && $isAdmin == '1') { ?>
									<table class="infoTable mt20">
										<tr><td class="infoVal"><strong><?php echo $dobField; ?>:</strong> <?php echo $birthDate; ?></td></tr>
									</table>
								<?php } ?>
							</div>
							<div class="col-md-6">
								<?php if ($set['enablePii'] == '1' && $isAdmin == '1') { ?>
									<table class="infoTable mt20">
										<tr><td class="infoVal"><strong><?php echo $ssnField; ?>:</strong> <?php echo $empSsn; ?></td></tr>
									</table>
								<?php } ?>
							</div>
						</div>

						<?php if ($isTermed == '1') { ?>
							<div class="row">
								<div class="col-md-6">
									<table class="infoTable mt20">
										<tr><td class="infoVal"><strong><?php echo $termDateField; ?>:</strong> <?php echo $row['terminationDate']; ?></td></tr>
									</table>
								</div>
								<div class="col-md-6">
									<table class="infoTable mt20">
										<tr><td class="infoVal"><strong><?php echo $termReasonField; ?>:</strong> <?php echo clean(nl2br($row['terminationReason'])); ?></td></tr>
									</table>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="contentAlt">
			<div class="row">
				<div class="col-md-4 text-center">
					<div class="content no-margin timeHgt">
						<?php echo clean($row['empFirst'])." ".clean($row['empLast']).' '.$hasWorkedText; ?><br />
						<span class="timeWorked" data-toggle="tooltip" data-placement="top" title="<?php echo $hoursMinsSecsTooltip; ?>"><?php echo $totalTime; ?></span><br />
						this week.
					</div>
				</div>
				<div class="col-md-5 text-center">
					<div class="content no-margin timeHgt">
						<?php if ($row['isActive'] == '1') { ?>
							<p><?php echo clean($row['empFirst']).' '.clean($row['empLast']).' '.$isCurrentlyText; ?> <strong><span class="workStatus"></span></strong>.<br />
								<small><?php echo $manuallyClockOutText; ?></small>
							</p>
							<form action="" method="post" class="empInfoClockBtn">
								<input type="hidden" name="empFullName" id="empFullName" value="<?php echo clean($row['empFirst'])." ".clean($row['empLast']); ?>" />
								<input type="hidden" name="clockId" value="<?php echo $clockId; ?>" />
								<input type="hidden" name="entryId" value="<?php echo $entryId; ?>" />
								<input type="hidden" name="weekNo" value="<?php echo $weekNum; ?>" />
								<input type="hidden" name="clockYear" value="<?php echo $currentYear; ?>" />
								<input type="hidden" name="running" id="running" value="<?php echo $running; ?>" />
								<input type="hidden" name="isRecord" id="isRecord" value="<?php echo $isRecord; ?>" />
								<button type="input" name="submit" id="timetrack" value="toggleTime" class="btn btn-icon" value="toggleTime"><i class=""></i> <span></span></button>
							</form>
						<?php } else { ?>
							<p class="lead text-danger inactiveEmp"><br /><strong><?php echo $inactEmpText; ?></strong></p>
						<?php } ?>
					</div>
				</div>
				<div class="col-md-3 text-center">
					<div class="content no-margin timeHgt">
						<?php echo $viewTimeCardsText; ?><br />
						<?php echo clean($row['empFirst'])." ".clean($row['empLast']); ?><br />
						<a href="index.php?page=viewTimecards&eid=<?php echo $row['empId']; ?>" class="btn btn-primary btn-icon empInfoClockBtn"><i class="fa fa-copy"></i> <?php echo $timeCardsNav; ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="contentAlt">
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<div class="row">
									<div class="col-md-4">
										<?php echo $leaveEarnedTitle; ?>
									</div>
									<div class="col-md-8">
										<div class="pull-right"><?php echo $availText; ?>: <strong <?php echo $isNeg; ?>><?php echo $availableBalance.' '.$hoursText; ?></strong></strong></div>
									</div>
								</div>
							</h4>
						</div>
						<div class="panel-wrapper">
							<div class="panel-body leaveHgt">
								<?php
									if ($row['isActive'] == '1') {
										if ($isAdmin == '1') {
								?>
											<a data-toggle="modal" href="#addLeave" class="btn btn-primary btn-xs btn-icon pull-right">
												<i class="fa fa-plus-square"></i> <?php echo $addLeaveBtn; ?>
											</a>
											<div class="clearfix"></div>
								<?php
										}
									}
									if(mysqli_num_rows($qry1res) < 1) {
								?>
										<div class="alertMsg default no-margin mt10">
											<i class="fa fa-minus-square"></i> <?php echo clean($row['empFirst'])." ".clean($row['empLast']).' '.$doesNotHaveLeaveMsg; ?>
										</div>
								<?php } else { ?>
									<table class="rwd-table mt10">
										<tbody>
											<tr>
												<th><?php echo $weekNoField; ?></th>
												<th><?php echo $yearField; ?></th>
												<th><?php echo $dateEnteredField; ?></th>
												<th><?php echo $hoursEarnedField; ?></th>
											</tr>
											<?php while ($rows = mysqli_fetch_assoc($qry1res)) { ?>
												<tr>
													<td data-th="<?php echo $weekNoField; ?>"><?php echo $rows['weekNo']; ?></td>
													<td data-th="<?php echo $yearField; ?>"><?php echo $rows['clockYear']; ?></td>
													<td data-th="<?php echo $dateEnteredField; ?>"><?php echo $rows['dateEntered']; ?></td>
													<td data-th="<?php echo $hoursEarnedField; ?>"><?php echo $rows['leaveHours']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php
										if ($availtotal > $pagPages) {
											echo $avail->page_links();
										}
									}
								?>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h4 class="panel-title">
								<div class="row">
									<div class="col-md-4">
										<?php echo $leaveUsedText; ?>
									</div>
									<div class="col-md-8">
										<div class="pull-right"><?php echo $totalText; ?>: <strong><?php echo $takenBalance.' '.$hoursUsedText; ?></strong></div>
									</div>
								</div>
							</h4>
						</div>
						<div class="panel-wrapper">
							<div class="panel-body leaveHgt">
								<?php
									if ($row['isActive'] == '1') {
										if ($isAdmin == '1') {
								?>
											<a data-toggle="modal" href="#takeLeave" class="btn btn-info btn-xs btn-icon pull-right">
												<i class="fa fa-minus-square"></i> <?php echo $subLeaveBtn; ?>
											</a>
											<div class="clearfix"></div>
								<?php
										}
									}
									if(mysqli_num_rows($qry2res) < 1) {
								?>
									<div class="alertMsg default no-margin mt10">
										<i class="fa fa-minus-square"></i> <?php echo clean($row['empFirst'])." ".clean($row['empLast']).' '.$noLeaveTakenMsg; ?>
									</div>
								<?php } else { ?>
									<table class="rwd-table mt10">
										<tbody>
											<tr>
												<th><?php echo $yearField; ?></th>
												<th><?php echo $dateEnteredField; ?></th>
												<th><?php echo $hoursTakenField; ?></th>
											</tr>
											<?php while ($rows = mysqli_fetch_assoc($qry2res)) { ?>
												<tr>
													<td data-th="<?php echo $yearField; ?>"><?php echo $rows['clockYear']; ?></td>
													<td data-th="<?php echo $dateEnteredField; ?>"><?php echo $rows['dateEntered']; ?></td>
													<td data-th="<?php echo $hoursTakenField; ?>"><?php echo $rows['hoursTaken']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php
										if ($takentotal > $pagPages) {
											echo $taken->page_links();
										}
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="content">
			<h4><?php echo $empPersInfoTitle; ?></h4>
			<p>
				<?php
					echo $empPersInfoQuip1;
					if ($isAdmin == '1') {
						echo $empPersInfoQuip2;
					}
				?>
			</p>
		</div>

		<?php if ($row['isActive'] == '1') { ?>
			<div id="addLeave" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addLeave" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
							<h4 class="modal-title"><?php echo $addLeaveModal; ?></h4>
						</div>

						<form action="" method="post">
							<div class="modal-body">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="addHours"><?php echo $addHoursField; ?></label>
											<input type="text" class="form-control" name="addHours" value="" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="weekNo"><?php echo $weekNumberField; ?></label>
											<input type="text" class="form-control" name="weekNo" value="" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="payYear"><?php echo $yearField; ?></label>
											<input type="text" class="form-control" name="payYear" value="<?php echo $currentYear; ?>" />
										</div>
									</div>
								</div>
							</div>

							<div class="modal-footer">
								<button type="input" name="submit" value="addLeave" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
								<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>

					</div>
				</div>
			</div>
		<?php } ?>

		<div id="takeLeave" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="takeLeave" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $subLeaveModal; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="subHours"><?php echo $hoursText; ?></label>
										<input type="text" class="form-control" name="subHours" value="" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="payYear"><?php echo $yearField; ?></label>
										<input type="text" class="form-control" name="payYear" value="<?php echo $currentYear; ?>" />
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="takeLeave" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } ?>