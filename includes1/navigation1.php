<?php
	// Get Employees Avatar
	/*$avatar = "SELECT DATE_FORMAT(empHireDate,'%M %d, %Y') AS empHireDate, empAvatar FROM employees WHERE empId = ".$empId;
	$avatarRes = mysqli_query($mysqli, $avatar) or die('-97'.mysqli_error());
	$av = mysqli_fetch_assoc($avatarRes);
	$avatarName = $av['empAvatar'];
	$empHireDate = $av['empHireDate'];*/
?>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only"><?php echo $toggleNavQuip; ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="index.php"><?php echo $dashboardNav; ?></a></li>
					<li><a href="index.php?page=calendar"><?php echo $calendarNav; ?></a></li>
					<li><a href="index.php?page=time"><?php echo $myTimeNav; ?></a></li>
					<li><a href="index.php?page=tasks"><?php echo $tasksNav; ?></a></li>
					<li><a href="index.php?page=inbox"><?php echo $messagesNav; ?></a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<?php if (($isAdmin == '1') || ($isMgr == '1')) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
							<div class="dropdown-menu dropdown-form">
								<form action="index.php?page=searchResults" method="post">
									<div class="input-group custom-search-form">
										<input type="text" class="form-control" required="" name="searchTerm" placeholder="<?php echo $searchPlaceholder; ?>">
										<span class="input-group-btn">
											<button type="input" name="submit" value="search" class="btn btn-search"><span class="fa fa-search"></span></button>
										</span>
									</div>
								</form>
							</div>
						</li>
					
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $employeesNav; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?page=activeEmployees"><?php echo $activeEmpNav; ?></a></li>
								<li><a href="index.php?page=inactiveEmployees"><?php echo $inactiveEmpNav; ?></a></li>
								<?php if ($isAdmin == '1') { ?>
									<li><a href="index.php?page=newEmployee"><?php echo $newEmpNav; ?></a></li>
								<?php } ?>
							</ul>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $manageNav; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?page=notices"><?php echo $siteNotNav; ?></a></li>
								<li><a href="index.php?page=businessDocs"><?php echo $busDocsNav; ?></a></li>
								<li><a href="index.php?page=reports"><?php echo $reportsNav; ?></a></li>
								<?php if ($isAdmin == '1') { ?>
									<li><a href="index.php?page=timeCards"><?php echo $timeCardsNav; ?></a></li>
									<li><a href="index.php?page=siteSettings"><?php echo $siteSettingsNav; ?></a></li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					
					<li class="dropdown user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span><?php echo $empName; ?></span>
						</a>
						<ul class="dropdown-menu">
							<li class="user-header">
								<img src="<?php echo $avatarDir.$avatarName; ?>" alt="Avatar" />
								<p>
									<?php echo $empName; ?><br />
									<small><?php echo $empPosition; ?></small>
									<small><?php echo $hireDateText.': '.$empHireDate; ?></small>
								</p>
							</li>
							<li class="user-footer">
								<div class="pull-left">
									<a href="index.php?page=myProfile" class="btn btn-default"><i class="fa fa-user"></i> <?php echo $myProfileNav; ?></a>
								</div>
								<div class="pull-right">
									<a data-toggle="modal" href="#signOut" class="btn btn-default"><i class="fa fa-sign-out"></i> <?php echo $signOutNav; ?></a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-labelledby="signOut" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<p class="lead"><?php echo $empName.' '.$signOutConf; ?></p>
				</div>
				<div class="modal-footer">
					<a href="index.php?action=logout" class="btn btn-success btn-icon-alt"><?php echo $signOutNav; ?> <i class="fa fa-sign-out"></i></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
