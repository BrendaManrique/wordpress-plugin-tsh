<?php
	// Get Settings Data
	$setSql  = "
		SELECT
			installUrl,
			localization,
			siteName,
			businessName,
			businessAddress,
			businessEmail,
			businessPhone1,
			businessPhone2,
			uploadPath,
			businessDocs,
			fileTypesAllowed,
			avatarFolder,
			avatarTypes,
			enableTimeEdits,
			enablePii
		FROM
			sitesettings
	";
	$setRes = mysqli_query($mysqli, $setSql) or die('-99' . mysqli_error());
?>