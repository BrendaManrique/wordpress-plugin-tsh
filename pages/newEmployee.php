<?php


//Al guardar la modificacion, nombre se guarda como 0;

function my_scripts_method() {
	wp_enqueue_script(
		'datetimepicker',
		get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/datetimepicker.js',
		array( 'jquery' )
	);
		wp_enqueue_script(
		'datetimepicker',
		get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/jquery.js',
		array( 'jquery' )
	);
		wp_register_script( 'my-plugin-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/jquery.js');
		wp_enqueue_script( 'my-plugin-script' );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
//function employee_list_table_employees_form_page_handler()
//{
    global $wpdb;
    global $current_user;
    get_currentuserinfo();
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
        'empPosition' => null,
        'lastUpdateTime' => current_time("Y-m-d H:i:s"),
        'lastUpdateUser' => $current_user->ID
    );

    // here we are verifying does this request is post back and have correct nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);

                
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = employee_list_table_validate_employee($item);
        if ($item_valid === true) {
            if ($item['empId'] == 0) {
            	$item['user_id'] = $_POST['user_id'];
				$item['empHireDate'] = $_POST['empHireDate'].' 00:00:00';
				$item['isAdmin'] = $_POST['setAdmin'];
				$item['isMgr'] = $_POST['setManager'];
				$item['empPosition'] = $_POST['empPosition'];
                $item['lastUpdateTime'] = $_POST['lastUpdateTime'];
                $item['lastUpdateUser'] = $_POST['lastUpdateUser'];
            	$result = $wpdb->insert($table_name, $item);
               // $item['empId'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'employee_list_table');
                    $item = $default;
                } else {
                    $notice = __('There was an error while saving item', 'employee_list_table');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('empId' => $item['empId']));
                if ($result) {
                    $message = __('Item was successfully updated', 'employee_list_table');
                } else {
                    //exit( var_dump( $wpdb->last_query ) );
                    $notice = __('Information already exists', 'employee_list_table');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
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
    <h2><?php _e('New Employee', 'employee_list_table')?> <a class="add-new-h2"
        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=listemployees');?>"><?php _e('back to list', 'employee_list_table')?></a>
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
        <input type="hidden" name="lastUpdateTime" id="lastUpdateTime" value="<?php echo  $item['lastUpdateTime']  ?>" />
        <input type="hidden" name="lastUpdateUser" id="lastUpdateUser" value="<?php echo $item['lastUpdateUser']  ?>"  />

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
{	global $wpdb;
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

 $user_info = get_userdata( $item['user_id']);
    ?>

   


<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'employee_list_table')?><sup>*</sup></label>
        </th>
        <td>


              <?php
              if ($_REQUEST['empId'] != 0){
                    ?>
                  <select class="form-control" name="user_id" id="user_id">
                                    <option value="<?php echo esc_attr($item['user_id'])?>" <?php echo 'selected="selected"';?>><?php echo $user_info->user_login?></option>
                                </select>
                <?php 
               // wp_dropdown_users(array(
                 //   'name' => 'user_id',  
                   // 'include' => isset($_REQUEST['empId']) ? $user_id : null , 
                    //'exclude' => !isset($_REQUEST['empId']) ? $local : $null ,
                    //'selected' => $item['user_id'] 
                    //)); 
           
               } else{ 
                   if (wp_dropdown_users(array('name' => 'user_id', 'exclude' => $local))){
                     //   wp_dropdown_users(array('name' => 'user_id', 'exclude' => $local));
                    }else{
                        echo 'All users assigned';
                    }

                }
               
               ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="empHireDate"><?php _e('Date of Hire', 'employee_list_table')?><sup>*</sup></label>
        </th>
        <td>
           <!-- <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['isAdmin'])?>"
                   size="50" class="code" placeholder="<?php _e('Your E-Mail', 'employee_list_table')?>" required>-->
                    <input id="empHireDate" name="empHireDate"class="form-control"  type="text" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('YYYY-MM-DD', 'employee_list_table')?>" required>
                
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="empPosition"><?php _e('Position', 'employee_list_table')?></label>
        </th>
        <td>
           <!-- <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['isAdmin'])?>"
                   size="50" class="code" placeholder="<?php _e('Your E-Mail', 'employee_list_table')?>" required>-->
                    <input id="empPosition" name="empPosition"class="form-control"  type="text" style="width: 95%" value="<?php echo esc_attr($item['empPosition'])?>"
                   size="50" class="code" placeholder="<?php _e('Project Manager', 'employee_list_table')?>" >
                
        </td>
    </tr>
    <!--<tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Administrator Account?', 'employee_list_table')?><sup>*</sup></label>
        </th>
        <td>
        <select class="form-control" name="setAdmin">
									<option value="0" selected>No</option>
									<option value="1">Yes</option>
								</select>
           <!-- <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('Your age', 'employee_list_table')?>" required> -->
    <!--    </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Account Type', 'employee_list_table')?><sup>*</sup></label>
        </th>
        <td>
        <select class="form-control" name="setMgr">

      
			<!--	<option value="0" selected>Employee</option>
				<option value="1">Manager</option>-->
	<!--			<option value="0" <?php if ( $isPost == 1) selected( $item['isMgr'], '- select -' ); ?>>Employee</option>';
        		<option value="1" <?php if ( $isPost == 1) selected( $item['isMgr'], '- select -' ); ?>>Manager</option>';
        		
								</select>
           <!-- <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['empHireDate'])?>"
                   size="50" class="code" placeholder="<?php _e('Your age', 'employee_list_table')?>" required> -->
       <!-- </td>
    </tr>-->
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