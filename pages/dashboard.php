<?php
//wp_enqueue_style( 'prfx_meta_box_styles', plugin_dir_url( __FILE__ ) . 'meta-box-styles.css' );
global $wpdb;

$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
global $current_user;
get_currentuserinfo();
$user_id = $current_user->ID;	

	$jsFile = 'dashboard';

	// Start/Stop the Time Clock
	if (isset($_POST['submit']) ) {
		$isRecord = $_POST['isRecord'];

		if ($isRecord != '0') {
			// Record Already Exists
			$clockId = sanitize_text_field($_POST['clockId']);
			$entryId = sanitize_text_field($_POST['entryId']);
			$weekNo = sanitize_text_field($_POST['weekNo']);
			$clockYear = sanitize_text_field($_POST['clockYear']);
			$running = sanitize_text_field($_POST['running']);
			$entryDate = $endTime = date("Y-m-d");
			$startTime = $endTime = date("Y-m-d H:i:s");

			if ($running == '0') {
				// Start Clock - Update the timeclock Record
				$default_sitesettings_localization = 'en';	
				$wpdb->update($table_name_timeclock, array( 'running' => 1),"WHERE clockId =".$clockId);
				

				// Start Clock - Add a new time entry
				$wpdb->insert( 	$table_name_timeentry,array( 
								'clockId' => $clockId,
								'user_id' => $user_id,
								'entryDate' => $entryDate,
								'startTime' => $startTime	) 
							);
			} else {
				// Stop Clock - Update the timeclock Record
				$wpdb->update($table_name_timeclock, array( 'running' => 0),"WHERE clockId =".$clockId);

				// Stop Clock - Update the time entry
				$wpdb->update($table_name_timeentry, array( 'endTime' => $endTime),"WHERE entryId =".$entryId);
				
			}
		} else {
			// Record Does Not Exist
			// Start Clock - Create a timeclock Record
			$weekNo = sanitize_text_field($_POST['weekNo']);
			$clockYear = sanitize_text_field($_POST['clockYear']);
			$running = '1';
			$startTime = date("Y-m-d H:i:s");

			$wpdb->insert( $table_name_timeclock, array( 
					'user_id' => $user_id,
					'weekNo' => $weekNo,
					'clockYear' => $clockYear,
					'running' => $running,
				) 
			);
        	
			// Get the new Tracking ID
			$track_id = $wpdb->get_results($query("SELECT clockId FROM $table_name_timeclock  WHERE user_id = ".$user_id." AND weekNo = ".$weekNo." AND clockYear = ".$currentYear), ARRAY_A);
			$id = $track_id;// mysqli_fetch_assoc($track_id);
			$clockId = $id['clockId'];
			$entryDate = $endTime = date("Y-m-d");

			// Start Clock - Add a new time entry
			$wpdb->insert( $table_name_timeentry, array( 
					'clockId' => $clockId,
					'user_id' => $user_id,
					'entryDate' => $entryDate,
					'startTime' => $startTime,
				) 
			);
		}
	}

	// Check for an Existing Record
	$wpdb-> get_results( $query = "SELECT * FROM $table_name_timeclock WHERE user_id = $user_id AND weekNo = $weekNo;");
	echo 'hhhhhhhhhhhhhhhhhh'.$wpdb->num_rows;
	if ($wpdb->num_rows) {

		$col =  $wpdb->get_results($query("SELECT clockId,user_id,weekNo,clockYear,running FROM $table_name_timeclock  WHERE user_id = ".$user_id." AND weekNo = ".$weekNo), ARRAY_A);
		$clockId = $col['clockId'];
		$running = $col['running'];

		$rows = $wpdb->get_results($query("SELECT clockId,user_id FROM $table_name_timeentry  WHERE clockId = ".$clockId." AND user_id = ".$user_id." endTime = '0000-00-00'"));
		$entryId = (is_null($rows['entryId'])) ? '' : $rows['entryId'];
		$isRecord = '1';

		// Get Total Time Worked for the Current Week
		$u = $wpdb->get_results(
			$query("SELECT TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
			 FROM $table_name_timeclock
					LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
			WHERE  $table_name_timeclock.user_id = ".$user_id." AND
					$table_name_timeclock.weekNo = ".$weekNo." AND
					$table_name_timeclock.clockYear = '".$currentYear."' AND
					$table_name_timeclock.endTime != '0000-00-00 00:00:00'"));
		$times = array();
		while ($u) {
			$times[] = $u['diff'];
		}
		$totalTime = sumHours($times);
	} else {
		$clockId = '';
		$entryId = '';
		$running = $isRecord = '0';
		$totalTime = '00:00:00';
	}

	// Get Unread Message Count
	/*$unreadsql = "SELECT 'X' FROM privatemessages WHERE toId = ".$empId." AND toRead = 0";
	$unreadtotal = mysql_query($mysqli, $unreadsql) or die('-4'.mysql_error());
	$unread = mysql_num_rows($unreadtotal);

	// Get Notice Data
    $sqlSmt  = "SELECT
					notices.createdBy,
					notices.isActive,
					notices.noticeTitle,
					notices.noticeText,
					DATE_FORMAT(notices.noticeDate,'%M %d, %Y') AS noticeDate,
					UNIX_TIMESTAMP(notices.noticeDate) AS orderDate,
					notices.noticeStart,
					notices.noticeExpires,
					CONCAT(employees.empFirst,' ',employees.empLast) AS postedBy
				FROM
					notices
					LEFT JOIN employees ON notices.createdBy = employees.empId
				WHERE
					notices.noticeStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					notices.noticeExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					notices.isActive = 1
				ORDER BY
					orderDate";
    $smtRes = mysqli_query($mysqli, $sqlSmt) or die('-5' . mysqli_error());

	$qry = "SELECT
				emptasks.empTaskId,
				emptasks.createdBy,
				emptasks.taskTitle,
				emptasks.taskDesc,
				emptasks.taskPriority,
				DATE_FORMAT(emptasks.taskStart,'%b %d %Y') AS taskStart,
				DATE_FORMAT(emptasks.taskDue,'%b %d %Y') AS taskDue,
				UNIX_TIMESTAMP(emptasks.taskDue) AS orderDate,
				CONCAT(employees.empFirst,' ',employees.empLast) AS postedBy
			FROM
				emptasks
				LEFT JOIN employees ON emptasks.createdBy = employees.empId
			WHERE
				emptasks.assignedTo = ".$empId." AND
				emptasks.isClosed = 0
			ORDER BY
				orderDate
			LIMIT 3";
	$res = mysqli_query($mysqli, $qry) or die('-6'.mysqli_error());

	$stmt = "SELECT
				privatemessages.messageId,
				privatemessages.fromId,
				privatemessages.messageTitle,
				privatemessages.messageText,
				DATE_FORMAT(privatemessages.messageDate,'%b %d %Y') AS messageDate,
				UNIX_TIMESTAMP(privatemessages.messageDate) AS orderDate,
				CONCAT(employees.empFirst,' ',employees.empLast) AS sentBy
			FROM
				privatemessages
				LEFT JOIN employees ON privatemessages.fromId = employees.empId
			WHERE
				privatemessages.toId = ".$empId." AND
				privatemessages.toArchived = 0 AND
				privatemessages.toDeleted = 0
			ORDER BY
				orderDate DESC
			LIMIT 3";
	$result = mysqli_query($mysqli, $stmt) or die('-7'.mysqli_error());

	*/
?>
<div class="contentAlt">
<h2>TimeSheet Dashboard</h2></br>
	<div class="row">
		<div class="col-md-4">
			<div class="dashBlk">
				<div class="iconBlk primary">
					<i class="fa fa-envelope-o"></i>
				</div>
				<div class="contentBlk">
					<?php echo $messagesBox1; ?><br />
					<span class="msgCount" data-toggle="tooltip" data-placement="top" title="<?php echo $viewMessagesTooltip; ?>">
						<?php
							if ($unread > 0) {
								echo '<a href="index.php?page=inbox">'.$unread.'</a>';
							} else {
								echo '<a href="index.php?page=inbox">0</a>';
							}
						?>
					</span><br />
					<?php if ($unread == 1) { echo $messagesBox2; } else { echo $messagesBox3; }; ?>
				</div>
			</div>
		</div>

		<div class="col-md-4 col-dashBlk">
			<div class="dashBlk">
				<div class="iconBlk info">
					<i class="fa fa-calendar"></i>
				</div>
				<div class="contentBlk">
					<?php echo $timeBox1; ?><br />
					<span class="timeWorked" data-toggle="tooltip" data-placement="top" title="<?php echo $hoursMinsSecsTooltip; ?>"><?php echo $totalTime; ?></span><br />
					<?php echo $timeBox2; ?>
				</div>
			</div>
		</div>

		<div class="col-md-4 col-dashBlk">
			<div class="dashBlk">
				<div class="iconBlk success">
					<i class="fa fa-clock-o"></i>
				</div>
				<div class="contentBlk">
					<?php echo $clockBox; ?><br />
					<span class="clockstatus workStatus"></span>
					<form action="" method="post" class="clockBtn">
						<input type="hidden" name="clockId" value="<?php echo $clockId; ?>" />
						<input type="hidden" name="entryId" value="<?php echo $entryId; ?>" />
						<input type="hidden" name="weekNo" value="<?php echo $weekNo; ?>" />
						<input type="hidden" name="clockYear" value="<?php echo $currentYear; ?>" />
						<input type="hidden" name="running" id="running" value="<?php echo $running; ?>" />
						<input type="hidden" name="isRecord" id="isRecord" value="<?php echo $isRecord; ?>" />
						<button type="input" name="submit" id="timetrack" value="toggleTime" class="btn btn-lg btn-icon" value="toggleTime"><i class=""></i> <span></span></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="contentAlt">
	<div class="row">
		<div class="col-md-6">
			<div class="content setHeight no-margin">
			<h4><?php echo $recentTasksTitle; ?></h4>
				<?php
					if(mysqli_num_rows($res) > 0) {
						while ($task = mysqli_fetch_assoc($res)) {
				?>
							<div class="task-item">
								<h4>
									<a href="index.php?page=viewTask&taskId=<?php echo $task['empTaskId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewTaskTooltip; ?>">
										<?php echo clean($task['taskTitle']); ?>
									</a>
									<span class="pull-right"><?php echo clean($task['taskPriority']); ?></span>
								</h4>
								<p class="infoLabels">
									<?php echo $createdByField.': '.clean($task['postedBy']); ?>
									<span class="pull-right"><?php echo $dateDueField.': '.$task['taskDue']; ?></span>
								</p>
								<p><?php echo ellipsis($task['taskDesc'],140); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default">
						<i class="fa fa-minus-square-o"></i> <?php echo $noRecentTasksFound; ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<div class="col-md-6">
			<div class="content setHeight no-margin">
			<h4><?php echo $recentMsgsTitle; ?></h4>
			<?php
					if(mysqli_num_rows($result) > 0) {
						while ($msg = mysqli_fetch_assoc($result)) {
				?>
							<div class="task-item">
								<h4>
									<a href="index.php?page=viewMessage&messageId=<?php echo $msg['messageId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewMsgTooltip; ?>">
										<?php echo clean($msg['messageTitle']); ?>
									</a>
								</h4>
								<p class="infoLabels">
									<?php echo $rcvdFromField.': '.clean($msg['sentBy']); ?>
									<span class="pull-right"><?php echo $dateRcvdField.': '.$msg['messageDate']; ?></span>
								</p>
								<p><?php echo ellipsis($msg['messageText'],140); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default">
						<i class="fa fa-minus-square-o"></i> <?php echo $noRecentMsgFound; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php if(mysqli_num_rows($smtRes) > 0) { ?>
	<div class="contentAlt">
		<?php while ($note = mysqli_fetch_assoc($smtRes)) { ?>
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> <?php echo clean($note['noticeTitle']); ?>
						<span class="pull-right"><?php echo $note['noticeDate']; ?></span>
					</h3>
				</div>
				<div class="panel-body notices">
					<p class="infoLabels"><?php echo $postedByField.': '.clean($note['postedBy']); ?></p>
					<p><?php echo clean($note['noticeText']); ?></p>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>
