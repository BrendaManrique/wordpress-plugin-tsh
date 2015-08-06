<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header('Location: install/install.php');
	} else {
		session_start();
		if (!isset($_SESSION['empId'])) {
			header ('Location: login.php');
			exit;  
		}  

		// Logout
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			if ($action == 'logout') {
				session_destroy();
				header('Location: login.php');
			}
		}

		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
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

		// Include Functions
		include('includes/functions.php');

		// Keep some User data available
		$empId 			= $_SESSION['empId'];
		$isAdmin 		= $_SESSION['isAdmin'];
		$isMgr 			= $_SESSION['isMgr'];
		$empEmail 		= $_SESSION['empEmail'];
		$empName 		= $_SESSION['empName'];
		$empPosition	= $_SESSION['empPosition'];
		
		// Link to the Page
		if (isset($_GET['page']) && $_GET['page'] == 'myProfile') {							// All
			$page = 'myProfile';
			$pageName = $myProfilePage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'calendar') {					// All
			$page = 'calendar';
			$pageName = $myCalendarPage;
			$addCss = '
				<link rel="stylesheet" type="text/css" href="css/fullcalendar.css" />
				<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />
			';
		} else if (isset($_GET['page']) && $_GET['page'] == 'tasks') {						// All
			$page = 'tasks';
			$pageName = $myTasksPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'closedTasks') {				// All
			$page = 'closedTasks';
			$pageName = $closedTasksPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'newTask') {					// All
			$page = 'newTask';
			$pageName = $newTaskPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewTask') {					// All
			$page = 'viewTask';
			$pageName = $viewTaskPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'inbox') {						// All
			$page = 'inbox';
			$pageName = $inboxPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'sent') {						// All
			$page = 'sent';
			$pageName = $sentPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'archived') {					// All
			$page = 'archived';
			$pageName = $archivePage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'compose') {					// All
			$page = 'compose';
			$pageName = $composePage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewMessage') {				// All
			$page = 'viewMessage';
			$pageName = $viewMsgPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'time') {						// All
			$page = 'time';
			$pageName = $myTimePage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewTime') {					// All
			$page = 'viewTime';
			$pageName = $viewTimePage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'updateTime') {					// Managers & Admins
			$page = 'updateTime';
			$pageName = $editTimeTooltip;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'activeEmployees') {			// Managers & Admins
			$page = 'activeEmployees';
			$pageName = $activeEmpPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'inactiveEmployees') {			// Managers & Admins
			$page = 'inactiveEmployees';
			$pageName = $inactiveEmpPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'newEmployee') {				// Managers & Admins
			$page = 'newEmployee';
			$pageName = $newEmpPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewEmployee') {				// Managers & Admins
			$page = 'viewEmployee';
			$pageName = $viewEmpPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewTimecards') {				// Managers & Admins
			$page = 'viewTimecards';
			$pageName = $viewTimeCardsPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'reports') {					// Managers & Admins
			$page = 'reports';
			$pageName = $reportsPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'empReport') {					// Managers & Admins
			$page = 'empReport';
			$pageName = $empReportPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'employeeExport') {				// Managers & Admins
			$page = 'employeeExport';
		} else if (isset($_GET['page']) && $_GET['page'] == 'empTimeReport') {				// Managers & Admins
			$page = 'empTimeReport';
			$pageName = $empTimeReportPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'empTimeExport') {				// Managers & Admins
			$page = 'empTimeExport';
		} else if (isset($_GET['page']) && $_GET['page'] == 'notices') {					// Managers & Admins
			$page = 'notices';
			$pageName = $siteNotPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'newNotice') {					// Managers & Admins
			$page = 'newNotice';
			$pageName = $newNoticePage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'timeCards') {					// Managers & Admins
			$page = 'timeCards';
			$pageName = $empTimeCardsPage;
			$addCss = '<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />';
		} else if (isset($_GET['page']) && $_GET['page'] == 'searchResults') {				// Managers & Admins
			$page = 'searchResults';
			$pageName = $searchResPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'businessDocs') {				// Managers & Admins
			$page = 'businessDocs';
			$pageName = $documentsPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'newDocument') {				// Managers & Admins
			$page = 'newDocument';
			$pageName = $newDocPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewDocument') {				// Managers & Admins
			$page = 'viewDocument';
			$pageName = $viewDocPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'siteSettings') {				// Admins Only
			$page = 'siteSettings';
			$pageName = $settingsPage;
		} else if (isset($_GET['page']) && $_GET['page'] == 'importData') {					// Primary Admin Only
			$page = 'importData';
			$pageName = $importPage;
		} else {
			$page = 'dashboard';															// All
			$pageName = $dashboardPage;
		}
		
		if (($page != "employeeExport") && ($page != "empTimeExport")) {
			include('includes/header.php');
		}

		if (file_exists('pages/'.$page.'.php')) {
			// Load the Page
			include('pages/'.$page.'.php');
		} else {
			include 'includes/navigation.php';
			// Else Display an Error
			echo '
					<div class="content">
						<h3>'.$pageError1.' &mdash; '.$pageName.' '.$pageError2.'</h3>
						<div class="alertMsg default">
							<i class="fa fa-warning"></i> '.$pageError3.'
						</div>
					</div>
				';
		}

		if (($page != "employeeExport") && ($page != "empTimeExport")) {
			include('includes/footer.php');
		}
	}
?>
