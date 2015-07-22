<?php
	// Get Settings Data
	$table_name = $wpdb->base_prefix . 'tsh_sitesettings';
	$query  = "
		SELECT
			allowRegistrations,
			localization,
			enableTimeEdits,
			enablePii
		FROM
			$table_name
	";
	$records = $wpdb->get_results($query);
?>