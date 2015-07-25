<?php
/**
* Plugin Name: WP-TimeSheet
* Plugin URI: http://brendamanrique.com/
* Description: Timesheet plugin for Trung <3
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

    add_menu_page('page_timesheet', 'Time Sheet', 'manage_options', 'timesheet_dashboard','timesheet_menu_dashboard', plugins_url( 'wp-timesheet/images/bullet.png' ), 6 ); 

    $dashboard_style =add_submenu_page('timesheet_dashboard', 'page_timesheet_dashboard', 'TSH Dashboard', 'manage_options', 'timesheet_dashboard' );

    add_submenu_page('timesheet_dashboard', 'page_timesheet_mytime', 'My Time', 'manage_options', 'timesheet_mytime',  'timesheet_menu_mytime');

    add_submenu_page('timesheet_dashboard', 'page_timesheet_newemployee', 'New Employee', 'manage_options', 'timesheet_newemployee',  'timesheet_menu_newEmployee');  
 	add_submenu_page('timesheet_dashboard', 'page_timesheet_listemployees', 'List Employees', 'manage_options', 'timesheet_listemployees',  'timesheet_menu_listEmployees');  

// Load the JS conditionally
        add_action( 'load-' . $dashboard_style, 'load_admin_js' );
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

function timesheet_menu_dashboard(){
	include ('language/en.php');	
	// wp_enqueue_script('datetimepicker');
  //  wp_enqueue_style('datetimepicker',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/custom.css');
 //wp_enqueue_script('datetimepicker1');
//    wp_enqueue_style('datetimepicker1',get_option('siteurl') . '/wp-content/plugins/wp-timesheet/css/bootstrap.css');

?>
<div class="wrap">


<?php


include('pages/dashboard.php');
?>
</div>
<?php
}

function timesheet_menu_mytime(){
	include ('language/en.php');	
?>
<div class="wrap">
<h4> A hello world111</h4>
<?php
include('pages/viewTime.php');
?>
</div>
<?php
}

function timesheet_menu_newEmployee(){
	include ('language/en.php');	
?>
<div class="wrap">
<h2> New Employee</h2>
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
<h2> List Employee</h2>
<?php
include('pages/listEmployees.php');
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
