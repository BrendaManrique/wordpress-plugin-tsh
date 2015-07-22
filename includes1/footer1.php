	</div>
	
	<section id="footer-default">
		<div class="container">
			<div class="footer-nav">
				<a href="index.php"><?php echo $dashboardNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a href="index.php?page=calendar"><?php echo $calendarNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a href="index.php?page=tasks"><?php echo $tasksNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a href="index.php?page=inbox"><?php echo $messagesNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a href="index.php?page=time"><?php echo $myTimeNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a href="index.php?page=myProfile"><?php echo $myProfileNav; ?></a>
					<i class="fa fa-circle-thin"></i>
				<a data-toggle="modal" href="#signOut"><?php echo $signOutNav; ?></a>
			</div>
			<div class="row">
				<div class="col-md-12 mb20">
					<a href=""><img src="images/logo.png"></a>
				</div>
				<div class="col-md-12">
					<p>
						<?php echo $footerText1; ?> <a href="http://codecanyon.net/item/timezone-employee-management-time-clock/6682629?ref=Luminary"><?php echo $set['siteName']; ?></a>
						<?php echo $footerText2; ?> <i class="fa fa-circle-thin"></i>
						<?php echo $footerText3; ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<?php if (isset($fullcalendar)) { echo '<script type="text/javascript" src="js/fullcalendar.js"></script>'; } ?>
	<?php if (isset($datePicker)) { echo '<script type="text/javascript" src="js/datetimepicker.js"></script>'; } ?>
	<?php if (isset($jsFile)) { echo '<script type="text/javascript" src="js/includes/'.$jsFile.'.js"></script>'; } ?>
	<?php if (isset($calinclude)) { include 'includes/calendar.php'; } ?>
	<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>