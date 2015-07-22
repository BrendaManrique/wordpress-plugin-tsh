<?php
	// Get Settings Data
	$setSql  = "
		SELECT
			allowRegistrations,
			localization,
			enableTimeEdits,
			enablePii
		FROM
			wp_tsh_sitesettings
	";
	$setRes = mysqli_query($mysqli, $setSql) or die('-99' . mysqli_error());
?>