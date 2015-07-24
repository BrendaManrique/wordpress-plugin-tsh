<?php
/*************
* Prepare interface
*WP_List_Table snippet
**************/
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


//Prepare Table of elements
$wp_list_table = new Links_List_Table();
$wp_list_table->prepare_items();

//Table of elements
$wp_list_table->display();	

class Links_List_Table extends WP_List_Table {

   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    function __construct() {
       parent::__construct( array(
      'singular'=> 'wp_list_text_link', //Singular label
      'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
    }

	/**
 * Add extra markup in the toolbars before or after the list
 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
 */
function extra_tablenav( $which ) {
   if ( $which == "top" ){
      //The code that goes before the table is here
      echo"Hello, I'm before the table";
   }
   if ( $which == "bottom" ){
      //The code that goes after the table is there
      echo"Hi, I'm after the table";
   }
}

/**
 * Define the columns that are going to be used in the table
 * @return array $columns, the array of columns to use with the table
 */
function get_columns() {
   return $columns= array(
      'col_link_id'=>__('ID'),
      'col_link_name'=>__('Name'),
      'col_link_url'=>__('Url'),
      'col_link_description'=>__('Description'),
      'col_link_visible'=>__('Visible')
   );
}

/**
 * Decide which columns to activate the sorting functionality on
 * @return array $sortable, the array of columns that can be sorted by the user
 */
public function get_sortable_columns() {
   return $sortable = array(
     /* 'col_link_id'=>'link_id',
      'col_link_name'=>'link_name',
      'col_link_visible'=>'link_visible'*/
'col_link_id' => array('link_id',true),
'col_link_name' => array('link_name',true),
'col_link_visible' => array('link_visible',true)
   );
}


/**
 * Prepare the table with different parameters, pagination, columns and table elements
 */
function prepare_items() {
   global $wpdb, $_wp_column_headers;
   $screen = get_current_screen();

   /* -- Preparing your query -- */
        $query = "SELECT * FROM $wpdb->links";

   /* -- Ordering parameters -- */
       //Parameters that are going to be used to order the result
       $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
       $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
       if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

   /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 5;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
       if(!empty($paged) && !empty($perpage)){
          $offset=($paged-1)*$perpage;
         $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
       }

   /* -- Register the pagination -- */
      $this->set_pagination_args( array(
         "total_items" => $totalitems,
         "total_pages" => $totalpages,
         "per_page" => $perpage,
      ) );
      //The pagination links are automatically built according to those parameters

   /* -- Register the Columns -- 
      $columns = $this->get_columns();
      $_wp_column_headers[$screen->id]=$columns;*/

	/* — Register the Columns — */
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = $this->get_sortable_columns();
	$this->_column_headers = array($columns, $hidden, $sortable);

   /* -- Fetch the items -- */
      $this->items = $wpdb->get_results($query);
}


/**
 * Display the rows of records in the table
 * @return string, echo the markup of the rows
 */
function display_rows() {

   //Get the records registered in the prepare_items method
   $records = $this->items;

   //Get the columns registered in the get_columns and get_sortable_columns methods
   list( $columns, $hidden ) = $this->get_column_info();

   //Loop for each record
   if(!empty($records)){foreach($records as $rec){

      //Open the line
        echo '< tr id="record_'.$rec->link_id.'">';
      foreach ( $columns as $column_name => $column_display_name ) {

         //Style attributes for each col
         $class = "class='$column_name column-$column_name'";
         $style = "";
         if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
         $attributes = $class . $style;

         //edit link
         $editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->link_id;

         //Display the cell
         switch ( $column_name ) {
            case "col_link_id":  echo '< td '.$attributes.'>'.stripslashes($rec->link_id).'< /td>';   break;
            case "col_link_name": echo '< td '.$attributes.'>'.stripslashes($rec->link_name).'5< /td>'; break;
            case "col_link_url": echo '< td '.$attributes.'>'.stripslashes($rec->link_url).'< /td>'; break;
            case "col_link_description": echo '< td '.$attributes.'>'.$rec->link_description.'< /td>'; break;
            case "col_link_visible": echo '< td '.$attributes.'>'.$rec->link_visible.'< /td>'; break;
         }
      }

      //Close the line
      echo'< /tr>';
   }}
}




}




















/*$entryId = $_GET['entryId'];
	$datePicker = 'true';
	$jsFile = 'viewTime';

	// Edit Time Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'editRecord') {
        // Validation
		if($_POST['editReason'] == "") {
            $msgBox = alertBox($editReasonReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateIn'] == "") {
            $msgBox = alertBox($dateInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeIn'] == "") {
            $msgBox = alertBox($timeInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateOut'] == "") {
            $msgBox = alertBox($dateOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeOut'] == "") {
            $msgBox = alertBox($timeOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dateIn = $mysqli->real_escape_string($_POST['dateIn']);
			$timeIn = $mysqli->real_escape_string($_POST['timeIn']);
			$dateOut = $mysqli->real_escape_string($_POST['dateOut']);
			$timeOut = $mysqli->real_escape_string($_POST['timeOut']);
			$startTime = $dateIn.' '.$timeIn.':00';
			$endTime = $dateOut.' '.$timeOut.':00';
			$entryType = '3';

			// Edit the Record
            $stmt = $mysqli->prepare("UPDATE
										timeentry
									SET
										startTime = ?,
										endTime = ?,
										entryType = ?
									WHERE
										entryId = ?"
			);
			$stmt->bind_param('ssss',
									$startTime,
									$endTime,
									$entryType,
									$entryId
			);
			$stmt->execute();
			$stmt->close();

			$editReason = $mysqli->real_escape_string($_POST['editReason']);
			$editedDate = date("Y-m-d H:i:s");
			$origStartTime = $mysqli->real_escape_string($_POST['origStartTime']);
			$origEndTime = $mysqli->real_escape_string($_POST['origEndTime']);

			// Add a record of the Edit
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeedits(
										entryId,
										editedBy,
										editedDate,
										origStartTime,
										origEndTime,
										editedStartTime,
										editedEndTime,
										editReason
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssssss',
								$entryId,
								$empId,
								$editedDate,
								$origStartTime,
								$origEndTime,
								$startTime,
								$endTime,
								$editReason
			);
			$stmt->execute();
			$msgBox = alertBox($timeRecUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['editReason'] = '';
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				timeentry.entryId,
				timeentry.clockId,
				timeentry.empId,
				DATE_FORMAT(timeentry.entryDate,'%M %d, %Y') AS entryDate,
				timeentry.startTime,
				DATE_FORMAT(timeentry.startTime,'%Y-%m-%d') AS startDate,
				DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
				DATE_FORMAT(timeentry.startTime,'%H:%i') AS hourIn,
				timeentry.endTime,
				DATE_FORMAT(timeentry.endTime,'%Y-%m-%d') AS endDate,
				DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
				DATE_FORMAT(timeentry.endTime,'%H:%i') AS hourOut,
				timeentry.entryType,
				timeclock.weekNo,
				timeclock.clockYear,
				timeclock.running,
				CONCAT(employees.empFirst,' ',employees.empLast) AS theEmp
			FROM
				timeentry
				LEFT JOIN timeclock ON timeentry.clockId = timeclock.clockId
				LEFT JOIN employees ON timeentry.empId = employees.empId
			WHERE
				timeentry.entryId = ".$entryId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['entryType'] == '1') { $entryType = $entryType1; } else if ($row['entryType'] == '2') { $entryType = $entryType2; } else if ($row['entryType'] == '3') { $entryType = $entryType3; }
	if ($row['running'] == '1') { $isRunning = $yesBtn; } else { $isRunning = $noBtn; }

	// Get any Previous Edit data
	$sqlStmt = "SELECT
					timeedits.editedBy,
					DATE_FORMAT(timeedits.editedDate,'%M %d, %Y at %h:%i %p') AS editedDate,
					timeedits.editReason,
					CONCAT(employees.empFirst,' ',employees.empLast) AS editedBy
				FROM
					timeedits
					LEFT JOIN employees ON timeedits.editedBy = employees.empId
				WHERE timeedits.entryId = ".$entryId;
	$results = mysqli_query($mysqli, $sqlStmt) or die('-2' . mysqli_error());

	// Get the Total Time Worked
	$qry = "SELECT
				TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
			FROM
				timeclock
				LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
			WHERE
				timeentry.entryId = ".$entryId;
	$result = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());
	$times = array();
	while ($u = mysqli_fetch_assoc($result)) {
		$times[] = $u['diff'];
	}
	if ($row['endTime'] != '0000-00-00 00:00:00') {
		$totalTime = sumHours($times);
		$dateEnded = $row['dateEnded'];
		$hourEnded = $row['hourEnded'];
	} else {
		$totalTime = $dateEnded = $hourEnded = '';
	}

	include 'includes/navigation.php';

	if (($row['empId'] != $empId) && ($isAdmin != '1')) {
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

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><?php echo $recordDateField; ?>:</td>
						<td class="infoVal"><?php echo $row['entryDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $clockYearField; ?>:</td>
						<td class="infoVal"><?php echo $row['clockYear']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $dateInField; ?>:</td>
						<td class="infoVal"><?php echo $row['dateStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $dateOutField; ?>:</td>
						<td class="infoVal"><?php echo $dateEnded; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $entryTypeField; ?>:</td>
						<td class="infoVal"><?php echo $entryType; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><?php echo $clockRunningField; ?>:</td>
						<td class="infoVal"><?php echo $isRunning; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $weekNoField; ?>:</td>
						<td class="infoVal"><?php echo $row['weekNo']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $timeInField; ?>:</td>
						<td class="infoVal"><?php echo $row['hourStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $timeOutField; ?>:</td>
						<td class="infoVal"><?php echo $hourEnded; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><?php echo $totalHoursField; ?>:</td>
						<td class="infoVal"><strong><?php echo $totalTime; ?></strong></td>
					</tr>
				</table>
			</div>
		</div>
		<?php if ($row['endTime'] != '0000-00-00 00:00:00') { ?>
			<a data-toggle="modal" data-target="#editEntry" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editTimeRecBtn; ?></a>
		<?php } else { ?>
			<div class="alertMsg info no-margin mt10">
				<i class="fa fa-info-circle"></i> <?php echo $editTimeRecQuip; ?>
			</div>
		<?php } ?>
	</div>

	<div class="content last">
		<h4><?php echo $prevTimeEditsTitle; ?></h4>
		<?php if(mysqli_num_rows($results) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noTimeEditsFoundMsg; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $updateDateField; ?></th>
						<th><?php echo $updatedByField; ?></th>
						<th><?php echo $reasonForEditField; ?></th>
					</tr>
					<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
						<tr>
							<td data-th="<?php echo $updateDateField; ?>"><?php echo $rows['editedDate']; ?></td>
							<td data-th="<?php echo $updatedByField; ?>"><?php echo clean($rows['editedBy']); ?></td>
							<td data-th="<?php echo $reasonForEditField; ?>"><?php echo clean($rows['editReason']); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>

	<?php if ($row['endTime'] != '0000-00-00 00:00:00') { ?>
		<div id="editEntry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $updateTimeRecModal; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="editReason"><?php echo $reasonForEditField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="editReason" value="" />
								<span class="help-block"><?php echo $reasonForEditFieldHelp; ?></span>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateIn"><?php echo $dateInField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="<?php echo $row['startDate']; ?>" />
										<span class="help-block"><?php echo $dateFormatHelp; ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeIn"><?php echo $timeInField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="timeIn" id="timeIn" required="" value="<?php echo $row['hourIn']; ?>" />
										<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateOut"><?php echo $dateOutField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="dateOut" id="dateOut" required="" value="<?php echo $row['endDate']; ?>" />
										<span class="help-block"><?php echo $dateFormatHelp; ?></span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeOut"><?php echo $timeOutField; ?> <sup><?php echo $reqField; ?></sup></label>
										<input type="text" class="form-control" name="timeOut" id="timeOut" required="" value="<?php echo $row['hourOut']; ?>" />
										<span class="help-block"><?php echo $timeFormatHelp1; ?></span>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<input type="hidden" name="origStartTime" value="<?php echo $row['startTime']; ?>" />
							<input type="hidden" name="origEndTime" value="<?php echo $row['endTime']; ?>" />
							<button type="input" name="submit" value="editRecord" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
<?php
		}
	}*
?>
