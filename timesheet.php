<?php
/**
* Plugin Name: WP-TimeSheet
* Plugin URI: http://brendamanrique.com/
* Description: Timesheet plugin for Borough Construction
* Version: 1.0
* Author: Brenda Manrique
* Author URI: http://brendamanrique.com/
* License:  GPL2
*/

/*************
* Plugin variables
**************/
define('WPLC_BASIC_PLUGIN_DIR', dirname(__FILE__));
define('WPLC_BASIC_PLUGIN_URL', plugins_url() . "/wp-timesheet/");
global $wplc_basic_plugin_url;
$wplc_basic_plugin_url = get_option('siteurl') . "/wp-content/plugins/wp-timesheet/";
global $wpdb;






add_action('admin_menu','wphidenag');

	function wphidenag() {

	remove_action( 'admin_notices', 'update_nag', 3 );

	}
/*************
* css
**************
add_action('wp_enqueue_scripts','load_css');
function load_css(){
	wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__ ));
}
function pd101_load_styles() {
	wp_enqueue_style( 'pd101-styles', plugins_url( 'pd101-styles.css', __FILE__ ) );
	wp_enqueue_script( 'pd101-scripts', plugins_url( 'pd101-scripts.js', __FILE__ ), array( 'jquery' ), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'pd101_load_styles' );*/


/*************
* PHP includes
**************/
include('install/install.php'); 

// Get Settings Data
include ('includes/settings.php');
$records = $wpdb->get_results($query);
if(!empty($records[0])){
	// Set Localization
	$local = $records[0]->localization;
	switch ($local) {
		case 'ar':		include ('language/ar.php');		break;
		case 'bg':		include ('language/bg.php');		break;
		case 'ce':		include ('language/ce.php');		break;
		case 'cs':		include ('language/cs.php');		break;
		case 'da':		include ('language/da.php');		break;
		case 'en':		include ('language/en.php');		break;
		case 'en-ca':	include ('language/en-ca.php');		break;
		case 'en-gb':	include ('language/en-gb.php');		break;
		case 'es':		include ('language/es.php');		break;
		case 'fr':		include ('language/fr.php');		break;
		case 'ge':		include ('language/ge.php');		break;
		case 'hr':		include ('language/hr.php');		break;
		case 'hu':		include ('language/hu.php');		break;
		case 'hy':		include ('language/hy.php');		break;
		case 'id':		include ('language/id.php');		break;
		case 'it':		include ('language/it.php');		break;
		case 'ja':		include ('language/ja.php');		break;
		case 'ko':		include ('language/ko.php');		break;
		case 'nl':		include ('language/nl.php');		break;
		case 'pt':		include ('language/pt.php');		break;
		case 'ro':		include ('language/ro.php');		break;
		case 'sv':		include ('language/sv.php');		break;
		case 'th':		include ('language/th.php');		break;
		case 'vi':		include ('language/vi.php');		break;
		case 'yue':		include ('language/yue.php');		break;
	}
}else
{
	include ('language/en.php');	
}


// Include Functions
//include('includes/functions.php');



/*************
* Main Menu
**************/

add_action('admin_menu', 'register_timesheet_menu');
function register_timesheet_menu(){


    add_menu_page('page_timesheet', 'Time Sheet', 'upload_files', 'timesheet_dashboard','timesheet_menu_dashboard', plugins_url( 'wp-timesheet/images/bullet.png' ), 6 ); 

    $dashboard_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_dashboard', 'TSH Dashboard', 'upload_files', 'timesheet_dashboard' );

    $mytime_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_mytime', 'My Time', 'upload_files', 'mytime',  'timesheet_menu_mytime');
    $tasks_style =add_submenu_page('', 'page_timesheet_tasks', 'Tasks', 'upload_files', 'tasks',  'timesheet_menu_tasks');
     $newtask_style =add_submenu_page('', 'page_timesheet_newtask', 'New Task', 'upload_files', 'newtask',  'timesheet_menu_newtask');

    $newemployee_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_newemployee', 'New Employee', 'manage_options', 'newemployee',  'timesheet_menu_newEmployee');  
 	$listemployees_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_listemployees', 'List Employees', 'manage_options', 'listemployees',  'timesheet_menu_listEmployees');  

add_submenu_page('timesheet_dashboard', 'page_timesheet_separator', '- - - - - - - ', 'manage_options', '',  '');  
    $timecards_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_timecards', 'Manage Time Cards', 'manage_options', 'timecards',  'timesheet_menu_timecards');
 $updatetime_style =add_submenu_page('', 'page_timesheet_updatetime', 'Update Time', 'manage_options', 'updatetime',  'timesheet_menu_updatetime');
 $viewtime_style =add_submenu_page('', 'page_timesheet_viewtime', 'View Time', 'manage_options', 'viewtime',  'timesheet_menu_viewtime');
 


 add_menu_page('Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');

add_submenu_page( 'my-top-level-slug', 'My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug1');

add_submenu_page( 'my-top-level-slug1', 'My Custom Submenu Page', 'My Custom Submenu Page', 'manage_options', 'my-secondary-slug');


// Load the JS conditionally
        add_action( 'load-' . $dashboard_style, 'load_admin_js' );
        add_action( 'load-' . $mytime_style, 'load_mytime_js' );
        add_action( 'load-' . $tasks_style, 'load_mytime_js' );
        add_action( 'load-' . $newtask_style, 'load_mytime_js' );
        add_action( 'load-' . $manage_style, 'load_mytime_js' );
        add_action( 'load-' . $timecards_style, 'load_mytime_js' );
        add_action( 'load-' . $updatetime_style, 'load_mytime_js' );
        add_action( 'load-' . $viewtime_style, 'load_mytime_js' );
        add_action( 'load-' . $newemployee_style, 'load_mytime_js' );
        add_action( 'load-' . $listemployees_style, 'load_mytime_js' );
}

function load_admin_js(){
        // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
        add_action( 'admin_enqueue_scripts', 'enqueue_admin_js' );
    }
function enqueue_admin_js(){
// Isn't it nice to use dependencies and the already registered core js files?
	wp_enqueue_script( 'custom-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/custom.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	wp_enqueue_style('custom',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/custom.css');
	wp_enqueue_style('bootstrap',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/bootstrap.css');
	wp_enqueue_script( 'dashboard-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/includes/dashboard.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	wp_enqueue_style('font-awesome',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/font-awesome.css');
	wp_enqueue_style('timezone',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/timezone.css');
	
}
function load_mytime_js(){
        // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
        add_action( 'admin_enqueue_scripts', 'enqueue_mytime_js' );
    }
function enqueue_mytime_js(){
// Isn't it nice to use dependencies and the already registered core js files?
	wp_enqueue_script( 'viewTime-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/includes/viewTime.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );	
	wp_enqueue_script( 'timeCards-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/includes/timeCards.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	wp_enqueue_script( 'bootstrap-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/bootstrap.min.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	
	wp_enqueue_script( 'custom-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/custom.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	wp_enqueue_style('custom',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/custom.css');
	wp_enqueue_style('bootstrap',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/bootstrap.css');
	//wp_enqueue_script( 'dashboard-script', get_option('siteurl') . '/wp-content/plugins/wp-timesheet/js/includes/dashboard.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ) );
	wp_enqueue_style('font-awesome',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/font-awesome.css');
	wp_enqueue_style('timezone',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/timezone.css');
	
}

function timesheet_menu_dashboard(){ 
	include ('language/en.php');	
	// wp_enqueue_script('datetimepicker');
  //  wp_enqueue_style('datetimepicker',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/custom.css');
 //wp_enqueue_script('datetimepicker1');
//    wp_enqueue_style('datetimepicker1',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/bootstrap.css');



global $current_user;
//get_currentuserinfo();
$user_id = $current_user->ID;

?>
<div class="wrap">


<?php

include('includes/functions.php');
include('pages/dashboard.php');
?>
</div>
<?php
}

function timesheet_menu_mytime(){
	include ('language/en.php');	
?>
<div class="wrap">
<h2>My Time</h2>
<?php
include('includes/functions.php');
include('pages/time.php');
?>
</div>
<?php
}

function timesheet_menu_tasks(){
	include ('language/en.php');	
	global $wpdb;
	global $current_user;
$user_id = $current_user->ID;
?>
<div class="wrap">
<h2>Tasks</h2> 
<?php
//include('includes/functions.php');
include('pages/tasks.php');
?>
</div>
<?php
}

function timesheet_menu_newtask(){
	include ('language/en.php');	
	global $wpdb;
	global $current_user;
$user_id = $current_user->ID;
?>
<div class="wrap">
<h2>New Task</h2>
<?php
//include('includes/functions.php');
include('pages/newtask.php');
?>
</div>
<?php
}

function timesheet_menu_newEmployee(){
	include ('language/en.php');	
?>
<div class="wrap">
<!--<h2> New Employee</h2>-->
<?php
include('pages/newEmployee.php');
?>
</div>
<?php
}

function timesheet_menu_listEmployees(){
	include ('language/en.php');	
?>
<div class="wrap">
<!--<h2> List Employees</h2>-->
<?php
include('pages/listEmployees.php');
?>
</div>
<?php
}

function timesheet_menu_timecards(){
	include ('language/en.php');	
	global $wpdb;
	global $current_user;
$user_id = $current_user->ID;
?>
<div class="wrap">
<h2>Time Cards</h2> 
<?php
include('includes/functions.php');
include('pages/timeCards.php');
?>
</div>
<?php
}

function timesheet_menu_updatetime(){
	include ('language/en.php');	
	global $wpdb;
	global $current_user;
$user_id = $current_user->ID;
?>
<div class="wrap">
<h2>Update Time</h2> 
<?php
include('includes/functions.php');
include('pages/updateTime.php');
?>
</div>
<?php
}
function timesheet_menu_viewtime(){
	include ('language/en.php');	
	global $wpdb;
	global $current_user;
$user_id = $current_user->ID;
?>
<div class="wrap">
<h2>View Time</h2> 
<?php
include('includes/functions.php');
include('pages/viewTime.php');
?>
</div>
<?php
}


/*************
* SETTINGS
**************/
// Add page to SETTINGS
add_action('admin_menu', 'plugin_admin_actions');

function plugin_admin_actions(){
	add_options_page('TimesheetPlugin','TimesheetPlugin','manage_options',__FILE__,'timesheetplugin_admin');//Page title, title submenu, who view submenu, menu slug, function that displays
}
function timesheetplugin_admin(){
?>
<div class="wrap">
<h4> A hello world</h4>

<div class="content">
	<h4>Holalaaa<?php echo $personalInfoTitle; ?></h4>
	<p><?php echo $personalInfoQuip; ?></p>
</div>


<?php
//include('pages/config.php');
?>
</div>
<?php
}



?>
