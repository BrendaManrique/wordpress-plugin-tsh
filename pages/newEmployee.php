<?php

//require_once(get_option('siteurl') . '/wp-load.php');
/*
	$datePicker = 'true';
	$jsFile = 'newEmployee';
	global $wpdb;


	// Add New Employee Account
	if (isset($_POST['submit']) ) {
        // Validation
      $isActive = '1';

			$setUser = $_POST['setUser'];
			$empHireDate = $_POST['empHireDate'].' 00:00:00';
			$setAdmin = $_POST['setAdmin'];
			$setManager = $_POST['setManager'];
		

			$table_name = $wpdb->base_prefix . 'tsh_employees';
			$wpdb->insert( 
				$table_name, 
				array( 
					'user_id' => $setUser,
					'empHireDate' => $empHireDate,
					'isAdmin' => $setAdmin,
					'isMgr' => $setManager,
				) 
			);
									
			//$msgBox = alertBox($empAcctCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the form of Values
			$_POST['empFirst']  = $_POST['empHireDate'] = '';
			
		}
		
	


	/*if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { */ 
/*	?>
	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<li><a href="admin.php?page=timesheet_listemployees"><i class="fa fa-group"></i> <?php echo $listEmpNav; ?></a></li>
			<li class="pull-right"><a href="admin.php?page=timesheet_newemployee" data-toggle="tab" class="bg-success"><i class="fa fa-plus-square"></i> <?php echo $newEmpPage; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<form action="" method="post">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="empFirst"><?php echo $firstNameField; ?> <sup><?php echo $reqField; ?></sup></label>
								<?php wp_dropdown_users(array('name' => 'setUser')); ?>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="empHireDate"><?php echo $hireDateField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="empHireDate" id="empHireDate" value="<?php echo isset($_POST['empHireDate']) ? $_POST['empHireDate'] : ''; ?>" />
								<span class="help-block"><?php echo $hireDateFieldHelp; ?></span>
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
<?php //}

*/




/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
//function employee_list_table_employees_form_page_handler()
//{
    global $wpdb;
    $table_name = $wpdb->base_prefix . 'tsh_employees'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'empId' => 0,
        'user_id' => 0,
        'isAdmin' => '',
        'isMgr' => '',
        'empHireDate' => null,
    );

    // here we are verifying does this request is post back and have correct nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
    	$isPost = 1;
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = employee_list_table_validate_employee($item);
        if ($item_valid === true) {
            if ($item['empId'] == 0) {
            	$item['user_id'] = $_POST['setUser'];
				$item['empHireDate'] = $_POST['empHireDate'].' 00:00:00';
				$item['isAdmin'] = $_POST['setAdmin'];
				$item['isMgr'] = $_POST['setManager'];
            	$result = $wpdb->insert($table_name, $item);
               // $item['empId'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'employee_list_table');
                } else {
                    $notice = __('There was an error while saving item', 'employee_list_table');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('empId' => $item['empId']));
                if ($result) {
                    $message = __('Item was successfully updated', 'employee_list_table');
                } else {
                    $notice = __('There was an error while updating item', 'employee_list_table');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $isPost = 0;
        $item = $default;
        if (isset($_REQUEST['empId'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE empId = %d", $_REQUEST['empId']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'employee_list_table');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('employees_form_meta_box', 'Employee data', 'employee_list_table_employees_form_meta_box_handler', 'employee', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Employee', 'employee_list_table')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=timesheet_listemployees');?>"><?php _e('back to list', 'employee_list_table')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="empId" value="<?php echo $item['empId'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('employee', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'employee_list_table')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
//}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function employee_list_table_employees_form_meta_box_handler($item)
{global $wpdb;
	$table_name = $wpdb->base_prefix . 'tsh_employees';
	//$query  = "SELECT GROUP_CONCAT(id) AS emp FROM 'wp_users' WHERE id NOT IN (SELECT user_id FROM $table_name)";
    $query  = "SELECT GROUP_CONCAT(user_id) AS emp FROM $table_name";
  
	$record = $wpdb->get_results($query);

	if(!empty($record[0])){
		// Set Localization
		$local = $record[0]->emp;
	}else{
		$local =  '';
	}

    ?>


<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'employee_list_table')?></label>
        </th>
        <td>

              <?php
              if ($isPost == 1){
              		wp_dropdown_users(array('name' => 'setUser', 'selected' => $item['user_id'])); 
               } else{
               		wp_dropdown_users(array('name' => 'setUser', 'exclude' => $local)); 
               	}?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="empHireDate"><?php _e('Data of Hire', 'employee_list_table')?></label>
        </th>
        <td>
           <!-- <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['isAdmin'])?>"
                   size="50" class="code" placeholder="<?php _e('Your E-Mail', 'employee_list_table')?>" required>-->
                    <input id="empHireDate" name="empHireDate" type="text" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('YYYY-MM-DD', 'employee_list_table')?>" required>
                
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Administrator Account?', 'employee_list_table')?></label>
        </th>
        <td>
        <select class="form-control" name="setAdmin">
									<option value="0" selected>No</option>
									<option value="1">Yes</option>
								</select>
           <!-- <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('Your age', 'employee_list_table')?>" required> -->
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Account Type', 'employee_list_table')?></label>
        </th>
        <td>
        <select class="form-control" name="setMgr">
									<option value="0" selected>Employee</option>
									<option value="1">Manager</option>
								</select>
           <!-- <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('Your age', 'employee_list_table')?>" required> -->
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function employee_list_table_validate_employee($item)
{
    $messages = array();

    if (empty($item['empHireDate'])) $messages[] = __('Hire date is required', 'employee_list_table');
  //  if (!empty($item['isAdmin']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'employee_list_table');
 //   if (!ctype_digit($item['empHireDate'])) $messages[] = __('Age in wrong format', 'employee_list_table');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}









 ?>