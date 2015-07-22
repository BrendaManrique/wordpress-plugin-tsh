<?php
	$delimiter = ',';
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importEmployees') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									employees(
										empId,
										empEmail,
										password,
										empFirst,
										empMiddleInt,
										empLast,
										empDob,
										empSsn,
										empPhone1,
										empPhone2,
										empPhone3,
										empAddress1,
										empAddress2,
										isMgr,
										isAdmin,
										empPosition,
										empPayGrade,
										empStartSalery,
										empCurrSalery,
										empSalaryTerm,
										leaveHours,
										empHireDate,
										isActive,
										empLastVisited
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]',
										'$data[6]',
										'$data[7]',
										'$data[8]',
										'$data[9]',
										'$data[10]',
										'$data[11]',
										'$data[12]',
										'$data[13]',
										'$data[14]',
										'$data[15]',
										'$data[16]',
										'$data[17]',
										'$data[18]',
										'$data[19]',
										'$data[20]',
										'$data[21]',
										'$data[22]',
										'$data[23]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($empDataUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	if (isset($_POST['submit']) && $_POST['submit'] == 'importCompiled') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									compiled(
										compileId,
										compliedBy,
										weekNo,
										clockYear,
										dateComplied
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($compiledLeaveUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importEarned') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									leaveearned(
										earnedId,
										empId,
										weekNo,
										clockYear,
										leaveHours,
										dateEntered
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($leaveEarnedUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importTaken') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									leavetaken(
										takenId,
										empId,
										clockYear,
										hoursTaken,
										dateEntered
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($leaveTakenUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importClocks') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									timeclock(
										clockId,
										empId,
										weekNo,
										clockYear,
										running
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($timeClockDataUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importEntries') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									timeentry(
										entryId,
										clockId,
										empId,
										entryDate,
										startTime,
										endTime
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($timeEntriesUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importEdits') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									timeedits(
										editId,
										entryId,
										editedBy,
										editedDate,
										editReason
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($timeEditsUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'importNotices') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									notices(
										noticeId,
										createdBy,
										isActive,
										noticeTitle,
										noticeText,
										noticeDate,
										noticeStart,
										noticeExpires
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]',
										'$data[6]',
										'$data[7]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($siteNoticesUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	// Check for Records
	$employeesCk = $mysqli->query("SELECT 'X' FROM employees");
	$totalemployees = mysqli_num_rows($employeesCk);
	
	$earnedCk = $mysqli->query("SELECT 'X' FROM leaveearned");
	$totalearned = mysqli_num_rows($earnedCk);
	
	$takenCk = $mysqli->query("SELECT 'X' FROM leavetaken");
	$totaltaken = mysqli_num_rows($takenCk);
	
	$compiledCk = $mysqli->query("SELECT 'X' FROM compiled");
	$totalcompiled = mysqli_num_rows($compiledCk);
	
	$clockCk = $mysqli->query("SELECT 'X' FROM timeclock");
	$totalclock = mysqli_num_rows($clockCk);
	
	$entriesCk = $mysqli->query("SELECT 'X' FROM timeentry");
	$totalentries = mysqli_num_rows($entriesCk);
	
	$editsCk = $mysqli->query("SELECT 'X' FROM timeedits");
	$totaledits = mysqli_num_rows($editsCk);
	
	$noticesCk = $mysqli->query("SELECT 'X' FROM notices");
	$totalnotices = mysqli_num_rows($noticesCk);

	include 'includes/navigation.php';

	if ($empId != '1') {
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
			<li><a href="index.php?page=siteSettings"><i class="fa fa-cogs"></i> <?php echo $globalSiteSetNavLink; ?></a></li>
			<li class="active pull-right"><a href="#home" data-toggle="tab"><i class="fa fa-hdd-o"></i> <?php echo $importDataNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">				
				<h3><?php echo $importDataTitle; ?></h3>
				<p><?php echo $importDataQuip1; ?></p>
				<p><?php echo $importDataQuip2; ?></p>
				<p><?php echo $importDataQuip3; ?></p>
				
				<div class="row mt20">
					<div class="col-md-4">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo $employeeDataTitle; ?></h3>
							</div>
							<div class="panel-body setHeight">
								<h4><?php echo $employeesNav; ?></h4>
								<?php if ($totalemployees == 1) { ?>
									<form action="" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<label for="file"><?php echo $selectFileField; ?></label>
											<input type="file" id="importfile" name="importfile" required="">
										</div>
										<button type="input" name="submit" value="importEmployees" class="btn btn-primary btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $impEmpBtn; ?></button>
									</form>
								<?php } else { ?>
									<?php echo $recordsExistsMsg; ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="panel panel-info">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo $leaveDataTitle; ?></h3>
							</div>
							<div class="panel-body setHeight">
								<div class="row">
									<div class="col-md-4">
										<h4><?php echo $leaveEarnedTitle; ?></h4>
										<?php if ($totalearned == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importEarned" class="btn btn-info btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $leaveEarnedBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
									<div class="col-md-4">
										<h4><?php echo $leaveTakenTitle; ?></h4>
										<?php if ($totaltaken == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importTaken" class="btn btn-info btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $leaveTakenBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
									<div class="col-md-4">
										<h4><?php echo $compiledLeaveTitle; ?></h4>
										<?php if ($totalcompiled == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importCompiled" class="btn btn-info btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $compiledLeaveBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row mt20">
					<div class="col-md-8">
						<div class="panel panel-success">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo $timeClockTitle; ?></h3>
							</div>
							<div class="panel-body setHeight">
								<div class="row">
									<div class="col-md-4">
										<h4><?php echo $timeClocksTitle; ?></h4>
										<?php if ($totalclock == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importClocks" class="btn btn-success btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $timeClocksBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
									<div class="col-md-4">
										<h4><?php echo $timeEntriesTitle; ?></h4>
										<?php if ($totalentries == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importEntries" class="btn btn-success btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $timeEntriesBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
									<div class="col-md-4">
										<h4><?php echo $timeEditsTitle; ?></h4>
										<?php if ($totaledits == 0) { ?>
											<form action="" method="post" enctype="multipart/form-data">
												<div class="form-group">
													<label for="file"><?php echo $selectFileField; ?></label>
													<input type="file" id="importfile" name="importfile" required="">
												</div>
												<button type="input" name="submit" value="importEdits" class="btn btn-success btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $timeEditsBtn; ?></button>
											</form>
										<?php } else { ?>
											<?php echo $recordsExistsMsg; ?>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="panel panel-warning">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo $siteNoticesTitle; ?></h3>
							</div>
							<div class="panel-body setHeight">
								<h4><?php echo $siteNoticesText; ?></h4>
								<?php if ($totalnotices == 0) { ?>
									<form action="" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<label for="file"><?php echo $selectFileField; ?></label>
											<input type="file" id="importfile" name="importfile" required="">
										</div>
										<button type="input" name="submit" value="importNotices" class="btn btn-warning btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $siteNoticesBtn; ?></button>
									</form>
								<?php } else { ?>
									<?php echo $recordsExistsMsg; ?>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
<?php } ?>