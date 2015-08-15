<?php
global $wpdb;
$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
$table_name_compiled = $wpdb->base_prefix . 'tsh_compiled'; 
$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
$currentYear= current_time("Y");
$weekNum = getWeekNo(current_time("Y-m-d"));
$isAdmin = '1';


	$datePicker = 'true';
	$jsFile = 'timeCards';

	// Compile Leave
    if (isset($_POST['submit']) && $_POST['submit'] == 'compileLeave') {
		$isCompiled = '';
		$compileWeek = $mysqli->real_escape_string($_POST['compileWeek']);
		$compileYear = $mysqli->real_escape_string($_POST['compileYear']);
		$dateComplied = date("Y-m-d H:i:s");

		// Check if the week has all ready been compiled
		$check = $mysqli->query("SELECT 'X' FROM compiled WHERE weekNo = '".$compileWeek."' AND clockYear = '".$compileYear."'");
		if ($check->num_rows) {
			$isCompiled = 'true';
		}

		// If week has all ready been compiled
		if ($isCompiled != '') {
			$msgBox = alertBox($leaveAllReadyCompiledMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$eid = $mysqli->query("SELECT user_id FROM $table_name_compiled WHERE isActive = 1");
		
			// $empIds = "SELECT empId FROM employees WHERE isActive = 1";
			// $idRes = mysqli_query($mysqli, $empIds) or die('-0' . mysqli_error());
			// // Set each into an array
			// $eid = array();
			// while($e = mysqli_fetch_assoc($idRes)) {
			// 	$eid[] = $e['empId'];
			// }

			// Add the hours to the DB for each active Employee
			if (!empty($eid)) {
				$sqlStmt = sprintf("
								INSERT INTO leaveearned (
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
								)"
				);

				foreach($eid as $key => $value) {
					$empHrs = "SELECT leaveHours FROM employees WHERE empId = ".$value;
					$hrsRes = mysqli_query($mysqli, $empHrs) or die('-1' . mysqli_error());
					$h = mysqli_fetch_assoc($hrsRes);
					$amtofleave = $h['leaveHours'];
					
					$compileWeek = $mysqli->real_escape_string($_POST['compileWeek']);
					$compileYear = $mysqli->real_escape_string($_POST['compileYear']);
					$dateEntered = date("Y-m-d H:i:s");

					if($stmt = $mysqli->prepare($sqlStmt)) {
						$stmt->bind_param('sssss',
											$value,
											$compileWeek,
											$compileYear,
											$amtofleave,
											$dateEntered
						);
						$stmt->execute();
						$stmt->close();
					}
				}

				// Add the compiled week to the database to prevent duplicates
				$stmt = $mysqli->prepare("
									INSERT INTO
										compiled(
											compliedBy,
											weekNo,
											clockYear,
											dateComplied
										) VALUES (
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssss',
									$empId,
									$compileWeek,
									$compileYear,
									$dateComplied
				);
				$stmt->execute();
				$msgBox = alertBox($leaveCompiledMsg, "<i class='icon-check-sign'></i>", "success");
				$stmt->close();
			}
		}
	}

	$years = $wpdb->get_results( 
		$query=" SELECT clockYear FROM $table_name_timeclock  GROUP BY clockYear;",ARRAY_A);

	if (($isAdmin != '1') && ($isMgr != '1')) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else {
 ?>
	<div class="content">
		<!--<h3><?php echo $pageName."se supone qe es nombre de pagina"; ?></h3>-->
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<?php
				foreach ($years as $tab) {
					if ($tab['clockYear'] == $currentYear) { $setActive = 'class="active"'; } else { $setActive = ''; }
			?>
					<li <?php echo $setActive; ?>><a href="#year<?php echo $tab['clockYear']; ?>" data-toggle="tab"><?php echo $tab['clockYear']; ?></a></li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<?php
				foreach ($years as $pane) {
					$paneyear=$pane['clockYear'];
					if ($pane['clockYear'] == $currentYear) { $isActive = 'in active'; } else { $isActive = ''; }
					$res = $wpdb->get_results( $query=" SELECT weekNo, clockYear 
						FROM $table_name_timeclock 
						WHERE  	 clockYear = $paneyear
						GROUP BY weekNo
							ORDER BY clockYear DESC , weekNo DESC",ARRAY_A);
				
				?>
				
				<div class="tab-pane <?php echo $isActive; ?>" id="year<?php echo $pane; ?>">
					<?php
						echo '<dl class="accordion no-margin">';
						foreach ( $res as $row) {
							$weekNo = $row['weekNo'];
							$clockYear = $row['clockYear'];

							// Get Total Time Worked for the Current Week
							$times = $wpdb->get_results( 
									$query=" SELECT
										TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
									FROM
										$table_name_timeclock
										LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
									WHERE
										$table_name_timeclock.weekNo = $weekNo AND
										$table_name_timeclock.clockYear = $clockYear AND
										$table_name_timeentry.endTime != '0000-00-00 00:00:00' ");
							
							$totalTime = sumHours($times);

							if ($weekNo == $weekNum) { $setActive = 'disabled'; } else { $setActive = ''; }
							if (empty($times)) {
								echo '
										<div class="alertMsg default no-margin">
											<i class="fa fa-warning"></i> '.$noTimeEntriesMsg.'
										</div>
									';
							} else {
					?>
								<dt><a> <?php echo $weekLink.': '.$weekNo; ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
								<?php 
									// Check if the week has all ready been compiled
									$wpdb->get_results($query = "SELECT * FROM compiled WHERE weekNo = $weekNo AND clockYear = $clockYear");
									$compres = $wpdb->num_rows; 

									//$comp = "SELECT 'X' FROM compiled WHERE weekNo = '".$weekNo."' AND clockYear = '".$clockYear."'";
									//$compres = mysqli_query($mysqli, $comp) or die('-5' . mysqli_error());
								?>
									<div class="row">
										<div class="col-lg-8">
										<?php if($setActive =='disabled') { ?>
												<p><?php echo $noEditMsg; ?></p>
										<?php } ?>
										</div>
										<div class="col-lg-4">
										<?php 
											if($compres < 1) {
												echo '<a data-toggle="modal" href="#compile'.$weekNo.$clockYear.'" class="btn btn-info btn-sm btn-icon pull-right" '.$setActive.'><i class="fa fa-cogs"></i> '.$compileText1.' '.$weekNo.' '.$compileText2.'</a>';
											} else {
												echo '<span class="btn btn-success btn-sm btn-icon pull-right"><i class="fa fa-check-square"></i>'.$weekLink.' '.$weekNo.' '.$compileText3.'</span>';
											}
										
										?>
										</div>
									</div>
									<div class="clearfix"></div>
									<table class="rwd-table mt10">
										<tbody>
											<tr>
												<th></th>
												<?php for ($day = 0; $day <= 6; $day++) { ?>
													<th><?php echo date('D. M d, Y', strtotime($clockYear.'W'.$weekNo.$day)); ?></th>
												<?php } ?>
												<th><?php echo $totalHoursField; ?></th>
											</tr>
										<?php

							
											$emps = $wpdb->get_results($query="SELECT user_id FROM $table_name_employees WHERE isActive = 1",ARRAY_A);
											

											foreach ($emps as $v) {
												// Get Total Time Worked for the Current Week per user
												$vuser = $v['user_id'];
												$times = $wpdb->get_results($query="SELECT
															TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
														FROM
															$table_name_timeclock 
															LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
														WHERE
															$table_name_timeclock.user_id = $vuser AND
															$table_name_timeclock.weekNo = $weekNo AND
															$table_name_timeclock.clockYear = $clockYear AND
															$table_name_timeentry.endTime != '0000-00-00 00:00:00'");
									
												$totalTime = sumHours($times);
												
												// Get Data
												$sqlres = $wpdb->get_results($query= "SELECT
															$table_name_employees.user_id,
															CONCAT($table_name_employees.user_id,'Emp name 238') AS empName
														FROM
															$table_name_timeclock
															LEFT JOIN $table_name_employees ON $table_name_timeclock.user_id = $table_name_employees.user_id
														WHERE
															$table_name_timeclock.user_id = $vuser  AND
															$table_name_timeclock.weekNo = $weekNo AND
															$table_name_timeclock.clockYear = $clockYear", ARRAY_A);
												foreach ($sqlres as $a) {
										?>
													<tr>
														<td><a href="admin.php?page=updatetime&eid=<?php echo $a['user_id']; ?>"><?php echo $a['empName']; ?></a></td>
														<?php
															for ($day = 0; $day <= 6; $day++) {
																$theDay = date('Y-m-d', strtotime($clockYear.'W'.$weekNo.$day));
																// Get the Total Hours per day
																
																$dayTotals  = $wpdb->get_results($query= "SELECT
																			TIMEDIFF(endTime,startTime) AS diff
																		FROM
																			$table_name_timeentry
																		WHERE
																			user_id = $vuser AND
																			entryDate = '$theDay' AND
																			endTime != '0000-00-00 00:00:00'");
															
																$totalHours = sumHours($dayTotals);
																
																$id  = $wpdb->get_row($query= "SELECT
																		entryId,
																		$table_name_timeentry.entryDate,
																		DATE_FORMAT($table_name_timeentry.entryDate,'%Y-%m-%d') AS theDate,
																		$table_name_timeentry.startTime,
																		DATE_FORMAT($table_name_timeentry.startTime,'%Y-%m-%d') AS startDate,
																		DATE_FORMAT($table_name_timeentry.startTime,'%H:%i') AS timeStart,
																		$table_name_timeentry.endTime,
																		DATE_FORMAT($table_name_timeentry.endTime,'%Y-%m-%d') AS endDate,
																		DATE_FORMAT($table_name_timeentry.endTime,'%H:%i') AS timeEnd,
																		$table_name_employees.user_id,'Emp name 238' AS theEmp
																	FROM
																		$table_name_timeentry
																		LEFT JOIN $table_name_employees ON $table_name_timeentry.user_id = $table_name_employees.user_id
																	WHERE $table_name_timeentry.user_id = $vuser AND $table_name_timeentry.entryDate = '$theDay'", ARRAY_A);
																

																if (($id['entryId'] != '') && ($id['endTime'] != '0000-00-00 00:00:00')) {
																	$editable = '<a href="admin.php?page=updatetime&date='.$id['entryDate'].'&user_id='.$vuser.'"><i class="fa fa-edit" data-toggle="tooltip" data-placement="top" title="'.$editTimeTooltip.'"></i></a>';
																} else {
																	$editable = '';
																}
														?>
																<td><?php echo $totalHours.' '.$editable; ?></td>

																<div id="compile<?php echo $weekNo.$clockYear; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
																	
																	<div class="modal-dialog">
																		<div class="modal-content">

																			<div class="modal-header modal-primary">
																				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
																				<h4 class="modal-title"><?php echo $compileModal.' '.$weekNo.', '.$clockYear; ?></h4>
																			</div>

																			<form action="" method="post">
																				<div class="modal-body">
																					<p class="lead"><?php echo $compileTimeQuip; ?></p>
																				</div>

																				<div class="modal-footer">
																					<input name="compileWeek" type="hidden" value="<?php echo $weekNo; ?>" />
																					<input name="compileYear" type="hidden" value="<?php echo $clockYear; ?>" />
																					<button type="input" name="submit" value="compileLeave" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $compileModal; ?></button>
																					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
																				</div>
																			</form>

																		</div>
																	</div>
																</div>
														<?php } ?>
														<td><strong><?php echo $totalTime; ?></strong></td>
													</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
								</dd>
						<?php
							}
						}
						echo '</dl><div class="clearfix"></div>';
					?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php }
 ?>