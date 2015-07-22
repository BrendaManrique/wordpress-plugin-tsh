<?php

/*************
* Main Menu
**************/
add_action('admin_menu', 'register_timesheet_menu');
function register_timesheet_menu(){
    add_menu_page('page_timesheet', 'Time Sheet', 'manage_options', 'timesheet_menu','timesheet_menu_dashboard', plugins_url( 'timesheet/images/bullet.png' ), 6 ); 
    add_submenu_page('timesheet_menu', 'page_timesheet_dashboard', 'Time Sheet Dashboard', 'manage_options', 'timesheet_menu' );
    add_submenu_page('timesheet_menu', 'page_timesheet_mytime', 'My Time', 'manage_options', 'timesheet_mytime_slug',  'timesheet_menu_mytime'); 

}


function timesheet_menu_dashboard(){

?>
<div class="wrap">
<h4> A hello worldddd</h4>
</div>
<?php
//include('includes/navigation.php');
}

function timesheet_menu_mytime(){
?>
<div class="wrap">
<h4> A hello world111</h4>
</div>
<?php
}


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
</div></div>

<?php
}


?>
