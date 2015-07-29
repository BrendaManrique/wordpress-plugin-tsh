<?php
//wp_enqueue_style( 'prfx_meta_box_styles', plugin_dir_url( __FILE__ ) . 'meta-box-styles.css' );
global $wpdb;

$table_name_timeclock = $wpdb->base_prefix . 'tsh_timeclock';
$table_name_timeentry = $wpdb->base_prefix . 'tsh_timeentry';
$table_name_privatemessages = $wpdb->base_prefix . 'tsh_privatemessages';
$table_name_notices = $wpdb->base_prefix . 'tsh_notices';
$table_name_employees = $wpdb->base_prefix . 'tsh_employees';
$table_name_emptasks = $wpdb->base_prefix . 'tsh_emptasks';


global $current_user;
get_currentuserinfo();
$user_id = $current_user->ID;
$weekNo = getWeekNo(date("Y-m-d"));
$clockYear = date("Y");
$startTime = $endTime = date("Y-m-d H:i:s");


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
			$entryDate = date("Y-m-d");
			$startTime = date("Y-m-d H:i:s");
			$endTime = date("Y-m-d H:i:s");

			if ($running == '0') {
				// Start Clock - Update the timeclock Record
				$default_sitesettings_localization = 'en';	
				$wpdb->update($table_name_timeclock, array( 'running' => 1),array( 'clockId' =>$clockId));
				

				// Start Clock - Add a new time entry
				$wpdb->insert( 	$table_name_timeentry,array( 
								'clockId' => $clockId,
								'user_id' => $user_id,
								'entryDate' => $entryDate,
								'startTime' => $startTime	) 
							);
			} else {
				// Stop Clock - Update the timeclock Record
				$wpdb->update($table_name_timeclock, array( 'running' => 0),array( 'clockId' =>$clockId));

				// Stop Clock - Update the time entry
				
				$result = $wpdb->update($table_name_timeentry, array( 'endTime' => $endTime),array( 'entryId' =>$entryId));
                
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
			$track_id = $wpdb->get_row($query="SELECT clockId FROM $table_name_timeclock  WHERE user_id = $user_id AND weekNo = $weekNo AND clockYear = $clockYear", ARRAY_A);
			//$id = $track_id;// mysqli_fetch_assoc($track_id);
			$clockId = $track_id['clockId'];
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
	if ($wpdb->num_rows) {
		$col =  $wpdb->get_row($query="SELECT clockId,user_id,weekNo,clockYear,running FROM $table_name_timeclock  WHERE user_id = $user_id AND weekNo = $weekNo", ARRAY_A);
		$clockId = $col['clockId'];
		$running = $col['running'];
		$rows = $wpdb->get_row($query="SELECT entryId,clockId,user_id FROM $table_name_timeentry  WHERE clockId = $clockId AND user_id = $user_id AND endTime = '0000-00-00 00:00:00'", ARRAY_A);
		$entryId = (is_null($rows['entryId'])) ? '' : $rows['entryId'];
		$isRecord = '1';

		// Get Total Time Worked for the Current Week
		$u = $wpdb->get_results(
			$query="SELECT TIMEDIFF($table_name_timeentry.endTime,$table_name_timeentry.startTime) AS diff
			 FROM $table_name_timeclock
					LEFT JOIN $table_name_timeentry ON $table_name_timeclock.clockId = $table_name_timeentry.clockId
			WHERE  $table_name_timeclock.user_id = $user_id AND
					$table_name_timeclock.weekNo = $weekNo AND
					$table_name_timeclock.clockYear = $clockYear AND
					$table_name_timeentry.endTime != '0000-00-00 00:00:00'");
		$times = array();
		//while ($u as $urow) {
		//	$times[] = $u['diff'];
		//}
		$totalTime = sumHours($u);
	} else {
		$clockId = '';
		$entryId = '';
		$running = $isRecord = '0';
		$totalTime = '00:00:00';
	}



	// Get Unread Message Count
	$wpdb-> get_results( $query = "SELECT * FROM $table_name_privatemessages WHERE toId = $user_id AND toRead = 0");
	$unread = $wpdb->num_rows;

	// Get Notice Data
    $smtRes  = $wpdb->get_results(
			$query="SELECT
					$table_name_notices.createdBy,
					$table_name_notices.isActive,
					$table_name_notices.noticeTitle,
					$table_name_notices.noticeText,
					DATE_FORMAT($table_name_notices.noticeDate,'%M %d, %Y') AS noticeDate,
					UNIX_TIMESTAMP($table_name_notices.noticeDate) AS orderDate,
					$table_name_notices.noticeStart,
					$table_name_notices.noticeExpires,
					$table_name_employees.user_id AS postedBy
				FROM
					$table_name_notices
					LEFT JOIN $table_name_employees ON $table_name_notices.createdBy = $table_name_employees.user_id
				WHERE
					$table_name_notices.noticeStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					$table_name_notices.noticeExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					$table_name_notices.isActive = 1
				ORDER BY
					orderDate",ARRAY_A);
    $smtRes_numrows = $wpdb->num_rows;

    $res  = $wpdb->get_results(
			$query="SELECT
				$table_name_emptasks.empTaskId,
				$table_name_emptasks.createdBy,
				$table_name_emptasks.taskTitle,
				$table_name_emptasks.taskDesc,
				$table_name_emptasks.taskPriority,
				DATE_FORMAT($table_name_emptasks.taskStart,'%b %d %Y') AS taskStart,
				DATE_FORMAT($table_name_emptasks.taskDue,'%b %d %Y') AS taskDue,
				UNIX_TIMESTAMP($table_name_emptasks.taskDue) AS orderDate,
				$table_name_employees.user_id AS postedBy
			FROM
				$table_name_emptasks
				LEFT JOIN $table_name_employees ON $table_name_emptasks.createdBy = $table_name_employees.empId
			WHERE
				$table_name_emptasks.assignedTo = $user_id AND	$table_name_emptasks.isClosed = 0
			ORDER BY
				orderDate
			LIMIT 3",ARRAY_A);
    $res_numrows = $wpdb->num_rows;
	
	$result  = $wpdb->get_results(
			$query="SELECT
				$table_name_privatemessages.messageId,
				$table_name_privatemessages.fromId,
				$table_name_privatemessages.messageTitle,
				$table_name_privatemessages.messageText,
				DATE_FORMAT($table_name_privatemessages.messageDate,'%b %d %Y') AS messageDate,
				UNIX_TIMESTAMP($table_name_privatemessages.messageDate) AS orderDate,
				$table_name_employees.user_id AS sentBy
			FROM
				$table_name_privatemessages
				LEFT JOIN $table_name_employees ON $table_name_privatemessages.fromId = $table_name_employees.user_id
			WHERE
				$table_name_privatemessages.toId = $user_id AND
				$table_name_privatemessages.toArchived = 0 AND
				$table_name_privatemessages.toDeleted = 0
			ORDER BY
				orderDate DESC
			LIMIT 3",ARRAY_A);
	$result_numrows = $wpdb->num_rows;
	
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
						<input type="hidden" name="clockYear" value="<?php echo $clockYear; ?>" />
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
					if($res_numrows > 0) {
						while ($task = $res) {
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
					if($result_numrows > 0) {
						while ($msg = $result) {
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

<?php if($smtRes_numrows > 0) { ?>
	<div class="contentAlt">
		<?php while ($note = $smtRes) { ?>
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
