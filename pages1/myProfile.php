<?php
	$jsFile = 'myProfile';
	$pagPages = '10';

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		// Get the Employee's avatar url
		$sql = "SELECT empAvatar FROM employees WHERE empId = ".$empId;
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
							   $empId);
			$stmt->execute();
			$msgBox = alertBox($avatarDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($avatarDeleteError, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Upload Avatar Image
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAvatar') {
		// Get the File Types allowed
		$fileExt = $set['avatarTypes'];
		$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space (, )
		$ftypes = array($fileExt);
		$ftypes_data = explode( ',', $fileExt );

		// Check file type
		$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
		if (!in_array($ext, $ftypes_data)) {
			$msgBox = alertBox($invalidAvatarMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			$avatarName = htmlentities($_POST['avatarName']);

			// Replace any spaces with an underscore
			// And set to all lowercase
			$newName = str_replace(' ', '_', $avatarName);

			// Generate a RANDOM Hash
			$randHash = uniqid(rand());

			// Take the first 6 digits
			$imgHash = substr($randHash, 0, 6);

			// Rename the Employee's Avatar
			$fileName = strtolower($newName).'_'.$imgHash;
			$fullName = $fileName;

			// set the upload path
			$avatarUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = end(explode(".", $avatarUrl));

			// Set the files name to the name set in the form
			// And add the original Ext
			$newAvatarName = $fullName.'.'.$extension;
			$movePath = $avatarDir.$newAvatarName;

			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									empAvatar = ?
								WHERE
									empId = ?");
			$stmt->bind_param('ss',
							   $newAvatarName,
							   $empId);

			if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
				$stmt->execute();
				$msgBox = alertBox($avatarUplMsg, "<i class='fa fa-check-square'></i>", "success");
				$completed = 'true';
				$stmt->close();
			} else {
				$msgBox = alertBox($avatarUplError, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
	}

	// Update Account
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
										empAddress2 = ?
									WHERE
										empId = ?"
			);
			$stmt->bind_param('sssssssss',
									$empFirst,
									$empMiddleInt,
									$empLast,
									$empPhone1,
									$empPhone2,
									$empPhone3,
									$empAddress1,
									$empAddress2,
									$empId
			);
			$stmt->execute();
			$msgBox = alertBox($accountInfoUpdMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Email
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
			$stmt->bind_param('ss', $empEmail, $empId);
			$stmt->execute();
			$msgBox = alertBox($acctEmailUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'changePassword') {
		$currentPass = encryptIt($_POST['currentpass']);
		// Validation
		if($_POST['currentpass'] == '') {
			$msgBox = alertBox($currentPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($currentPass != $_POST['passwordOld']) {
			$msgBox = alertBox($currPassIncorectMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] == '') {
			$msgBox = alertBox($newPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($retypePassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($passNotMatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
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
			$stmt->bind_param('ss', $password, $empId);
			$stmt->execute();
			$msgBox = alertBox($accountPassChangedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Get Data
	$query = "SELECT
				empId,
				isAdmin,
				isMgr,
				empEmail,
				password,
				empFirst,
				IFNULL(empMiddleInt,'') AS empMiddleInt,
				empLast,
				empAvatar,
				empPhone1,
				empPhone2,
				empPhone3,
				empAddress1,
				empAddress2,
				empPosition,
				empPayGrade,
				empCurrSalery,
				empCurrHourly,
				empSalaryTerm,
				DATE_FORMAT(empHireDate,'%M %d, %Y') AS empHireDate,
				isActive,
				DATE_FORMAT(empLastVisited,'%M %e, %Y at %l:%i %p') AS empLastVisited
			FROM
				employees
			WHERE empId = ".$empId;
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
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

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$avail = new paginator($pagPages,'p');

	// Get the number of total records
	$availrows = $mysqli->query("SELECT * FROM leaveearned WHERE empId = ".$empId." AND clockYear = ".$currentYear);
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
			WHERE empId = ".$empId." AND clockYear = ".$currentYear."
			ORDER BY weekNo ".$avail->get_limit();
    $qry1res = mysqli_query($mysqli, $qry1) or die('-3' . mysqli_error());

	// Create new object & pass in the number of pages and an identifier
	$taken = new paginator($pagPages,'q');

	// Get the number of total records
	$takenrows = $mysqli->query("SELECT * FROM leavetaken WHERE empId = ".$empId." AND clockYear = ".$currentYear);
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
			WHERE empId = ".$empId." AND clockYear = ".$currentYear."
			ORDER BY takenId ".$taken->get_limit();
    $qry2res = mysqli_query($mysqli, $qry2) or die('-4' . mysqli_error());

	// Get Leave Balances
	$earnedbal = "SELECT SUM(leaveHours) AS curBalance FROM leaveearned WHERE empId = ".$empId." AND clockYear = ".$currentYear;
	$earnedres = mysqli_query($mysqli, $earnedbal) or die('-5' . mysqli_error());
	$earned = mysqli_fetch_assoc($earnedres);

	$takenbal = "SELECT SUM(hoursTaken) AS takenBalance FROM leavetaken WHERE empId = ".$empId." AND clockYear = ".$currentYear;
	$takenres = mysqli_query($mysqli, $takenbal) or die('-6' . mysqli_error());
	$amttaken = mysqli_fetch_assoc($takenres);
	if ($amttaken['takenBalance'] != '') { $takenBalance = $amttaken['takenBalance']; } else { $takenBalance = '0'; }

	$availableBalance = $earned['curBalance'] - $amttaken['takenBalance'];
	if ($availableBalance < 0) { $isNeg = '<strong class="text-danger">'.$availableBalance.'</strong>'; } else { $isNeg = $availableBalance; }

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<div class="row">
		<div class="col-md-4">
			<div class="content text-center no-margin profHeight">
				<img src="<?php echo $avatarDir.$row['empAvatar']; ?>" alt="<?php echo $empFullName; ?>" class="empAvatar" />
				<p class="lead mt20 mb10"><?php echo $empFullName; ?></p>
				<p class="mb0">
					<?php echo $empPhone1; ?><br />
					<?php echo clean($row['empPosition']); ?> <span class="text-muted">[<?php echo $role; ?>]</span><br />
					<?php echo $hireDateText.': '.$row['empHireDate']; ?>
				</p>
			</div>
		</div>
		<div class="col-md-8">
			<div class="content no-margin profHeight">
				<div class="profileInfo">
					<?php if ($msgBox) { echo $msgBox; } ?>

					<p class="lead text-center">
						<?php echo nl2br(clean($empAddress1)); ?><br />
						<?php echo clean($row['empEmail']); ?>
					</p>
					<p class="lead text-center mt10">
						<?php echo $empPhone2; ?><br />
						<?php echo $empPhone3; ?>
					</p>
					<p class="lead text-center mt20 text-muted"><?php echo $lastLoginField.': '.$row['empLastVisited']; ?></p>
				</div>
				<div class="text-center no-margin mt30">
					<a data-toggle="modal" href="#profileAvatar" class="btn btn-default btn-icon"><i class="fa fa-picture-o"></i> <?php echo $changeAvatarBtn; ?></a>
					<a data-toggle="modal" href="#updateAccount" class="btn btn-default btn-icon"><i class="fa fa-user"></i> <?php echo $persInfoBtn; ?></a>
					<a data-toggle="modal" href="#updateEmail" class="btn btn-default btn-icon"><i class="fa fa-envelope"></i> <?php echo $updtEmailBtn; ?></a>
					<a data-toggle="modal" href="#changePassword" class="btn btn-default btn-icon"><i class="fa fa-lock"></i><?php echo $changePasswordBtn; ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="contentAlt">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary setHeight">
				<div class="panel-heading">
					<h4 class="panel-title">
						<div class="row">
							<div class="col-md-4">
								<?php echo $leaveEarnedTitle; ?>
							</div>
							<div class="col-md-8">
								<div class="pull-right"><?php echo $availText.': '.$isNeg,' '.$hoursText; ?></div>
							</div>
						</div>
					</h4>
				</div>

				<div class="panel-wrapper collapse in">
					<div class="panel-body">
						<?php if(mysqli_num_rows($qry1res) < 1) { ?>
							<div class="alertMsg default no-margin">
								<i class="fa fa-minus-square-o"></i> <?php echo $noLeaveEarnedMsg; ?>
							</div>
						<?php } else { ?>
							<table class="rwd-table">
								<tbody>
									<tr class="primary">
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
			<div class="panel panel-info setHeight">
				<div class="panel-heading">
					<h4 class="panel-title">
						<div class="row">
							<div class="col-md-4">
								<?php echo $leaveUsedText; ?>
							</div>
							<div class="col-md-8">
								<div class="pull-right"><?php echo $totalText.' '.$takenBalance.' '.$hoursUsedText; ?></div>
							</div>
						</div>
					</h4>
				</div>

				<div class="panel-wrapper collapse in">
					<div class="panel-body">
						<?php if(mysqli_num_rows($qry2res) < 1) { ?>
							<div class="alertMsg default no-margin">
								<i class="fa fa-minus-square-o"></i> <?php echo $noLeaveTakedMsg; ?>
							</div>
						<?php } else { ?>
							<table class="rwd-table">
								<tbody>
									<tr class="primary">
										<th><?php echo $yearField; ?></th>
										<th><?php echo $dateEnteredField; ?></th>
										<th><?php echo $hoursTakenField; ?></th>
									</tr>
									<?php while ($cols = mysqli_fetch_assoc($qry2res)) { ?>
										<tr>
											<td data-th="<?php echo $yearField; ?>"><?php echo $cols['clockYear']; ?></td>
											<td data-th="<?php echo $dateEnteredField; ?>"><?php echo $cols['dateEntered']; ?></td>
											<td data-th="<?php echo $hoursTakenField; ?>"><?php echo $cols['hoursTaken']; ?></td>
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
	<h4><?php echo $personalInfoTitle; ?></h4>
	<p><?php echo $personalInfoQuip; ?></p>
</div>

<div id="profileAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $profileAvatarModal; ?></h4>
			</div>
			<?php if ($row['empAvatar'] != 'empAvatar.png') { ?>
				<div class="modal-body">
					<img alt="" src="<?php echo $avatarDir.$row['empAvatar']; ?>" class="modalAvatar" />
					<p class="lead"><?php echo $profileAvatarQuip1; ?></p>
					<p><?php echo $profileAvatarQuip2; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> <?php echo $removeAvatarBtn; ?></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } ?>

			<?php if ($row['empAvatar'] == 'empAvatar.png') { ?>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $uplNewAvatarField; ?></p>
						<p><?php echo $allowedFileTypesText.' '.$avatarTypesAllowed; ?></p>

						<div class="form-group">
							<label for="file"><?php echo $selectNewAvatarField; ?> <sup><?php echo $reqField; ?></sup></label>
							<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarName" value="<?php echo $row['empFirst'].'_'.$row['empLast']; ?>" />
						<button type="input" name="submit" value="updateAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uplAvatarBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>
</div>

<div id="deleteAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $deleteAvatarConf; ?></p>
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
				<h4 class="modal-title"><?php echo $updatePersInfoModal; ?></h4>
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
					<div class="form-group">
						<label for="empAddress1"><?php echo $mailingAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
						<textarea class="form-control" name="empAddress1" required="" rows="3"><?php echo $empAddress1; ?></textarea>
					</div>
					<div class="form-group">
						<label for="empAddress2"><?php echo $altAddyField; ?></label>
						<textarea class="form-control" name="empAddress2" rows="3"><?php echo $empAddress2; ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateInfoBtn; ?></button>
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
				<h4 class="modal-title"><?php echo $updateEmailModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="empEmail"><?php echo $emailAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" name="empEmail" required="" value="<?php echo clean($row['empEmail']); ?>" />
						<span class="help-block"><?php echo $emailFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updtEmailBtn; ?></button>
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
				<h4 class="modal-title"><?php echo $changePasswordModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="currentpass"><?php echo $currPasswordField; ?> <sup><?php echo $reqField; ?></sup></label>
                        <input type="text" class="form-control" name="currentpass" required="" value="" />
						<span class="help-block"><?php echo $currPasswordFieldHelp; ?></span>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password"><?php echo $newPasswordField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="password" required="" value="" />
								<span class="help-block"><?php echo $newPasswordFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_r"><?php echo $confNewPasswordField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" name="password_r" required="" value="" />
								<span class="help-block"><?php echo $confNewPasswordFieldHelp; ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>" />
					<button type="input" name="submit" value="changePassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $changePasswordBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>