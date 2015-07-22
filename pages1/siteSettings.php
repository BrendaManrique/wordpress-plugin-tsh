<?php
	// Update Global Site Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateSettings') {
        // Validation
		if($_POST['installUrl'] == "") {
            $msgBox = alertBox($installUrlReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['siteName'] == "") {
            $msgBox = alertBox($siteNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessName'] == "") {
            $msgBox = alertBox($busNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessAddress'] == "") {
            $msgBox = alertBox($busAddyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessEmail'] == "") {
            $msgBox = alertBox($busEmailReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessDocs'] == "") {
            $msgBox = alertBox($busDocsFoldReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileTypesAllowed'] == "") {
            $msgBox = alertBox($uploadTypesReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarFolder'] == "") {
            $msgBox = alertBox($avatarFoldReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarTypes'] == "") {
            $msgBox = alertBox($avatarTypesReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$installUrl = $mysqli->real_escape_string($_POST['installUrl']);
			$businessDocs = $mysqli->real_escape_string($_POST['businessDocs']);
			$avatarFolder = $mysqli->real_escape_string($_POST['avatarFolder']);
			if(substr($installUrl, -1) != '/') { $install = $installUrl.'/'; } else { $install = $installUrl; }
			if(substr($businessDocs, -1) != '/') { $busDocs = $businessDocs.'/'; } else { $busDocs = $businessDocs; }
			if(substr($avatarFolder, -1) != '/') { $avatarDir = $avatarFolder.'/'; } else { $avatarDir = $avatarFolder; }

			$localization = $mysqli->real_escape_string($_POST['localization']);
			$siteName = $mysqli->real_escape_string($_POST['siteName']);
			$businessName = $mysqli->real_escape_string($_POST['businessName']);
			$businessAddress = htmlentities($_POST['businessAddress']);
			$businessEmail = htmlspecialchars($_POST['businessEmail']);
			$businessPhone1 = $mysqli->real_escape_string($_POST['businessPhone1']);
			$businessPhone2 = $mysqli->real_escape_string($_POST['businessPhone2']);
			$fileTypesAllowed = $mysqli->real_escape_string($_POST['fileTypesAllowed']);
			$avatarTypes = $mysqli->real_escape_string($_POST['avatarTypes']);
			$enableTimeEdits = $mysqli->real_escape_string($_POST['enableTimeEdits']);
			$enablePii = $mysqli->real_escape_string($_POST['enablePii']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									installUrl = ?,
									localization = ?,
									siteName = ?,
									businessName = ?,
									businessAddress = ?,
									businessEmail = ?,
									businessPhone1 = ?,
									businessPhone2 = ?,
									businessDocs = ?,
									fileTypesAllowed = ?,
									avatarFolder = ?,
									avatarTypes = ?,
									enableTimeEdits = ?,
									enablePii = ?
			");
            $stmt->bind_param('ssssssssssssss',
								   $install,
								   $localization,
								   $siteName,
								   $businessName,
								   $businessAddress,
								   $businessEmail,
								   $businessPhone1,
								   $businessPhone2,
								   $busDocs,
								   $fileTypesAllowed,
								   $avatarDir,
								   $avatarTypes,
								   $enableTimeEdits,
								   $enablePii
			);
            $stmt->execute();
			$msgBox = alertBox($settingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
            $stmt->close();
		}
	}

	// Get Data
	$sqlStmt = "SELECT
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
					sitesettings";
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['localization'] == 'ar') { $ar = 'selected'; } else { $ar = ''; }
	if ($row['localization'] == 'bg') { $bg = 'selected'; } else { $bg = ''; }
	if ($row['localization'] == 'ce') { $ce = 'selected'; } else { $ce = ''; }
	if ($row['localization'] == 'cs') { $cs = 'selected'; } else { $cs = ''; }
	if ($row['localization'] == 'da') { $da = 'selected'; } else { $da = ''; }
	if ($row['localization'] == 'en') { $en = 'selected'; } else { $en = ''; }
	if ($row['localization'] == 'en-ca') { $en_ca = 'selected'; } else { $en_ca = ''; }
	if ($row['localization'] == 'en-gb') { $en_gb = 'selected'; } else { $en_gb = ''; }
	if ($row['localization'] == 'es') { $es = 'selected'; } else { $es = ''; }
	if ($row['localization'] == 'fr') { $fr = 'selected'; } else { $fr = ''; }
	if ($row['localization'] == 'ge') { $ge = 'selected'; } else { $ge = ''; }
	if ($row['localization'] == 'hr') { $hr = 'selected'; } else { $hr = ''; }
	if ($row['localization'] == 'hu') { $hu = 'selected'; } else { $hu = ''; }
	if ($row['localization'] == 'hy') { $hy = 'selected'; } else { $hy = ''; }
	if ($row['localization'] == 'id') { $id = 'selected'; } else { $id = ''; }
	if ($row['localization'] == 'it') { $it = 'selected'; } else { $it = ''; }
	if ($row['localization'] == 'ja') { $ja = 'selected'; } else { $ja = ''; }
	if ($row['localization'] == 'ko') { $ko = 'selected'; } else { $ko = ''; }
	if ($row['localization'] == 'nl') { $nl = 'selected'; } else { $nl = ''; }
	if ($row['localization'] == 'pt') { $pt = 'selected'; } else { $pt = ''; }
	if ($row['localization'] == 'ro') { $ro = 'selected'; } else { $ro = ''; }
	if ($row['localization'] == 'sv') { $sv = 'selected'; } else { $sv = ''; }
	if ($row['localization'] == 'th') { $th = 'selected'; } else { $th = ''; }
	if ($row['localization'] == 'vi') { $vi = 'selected'; } else { $vi = ''; }
	if ($row['localization'] == 'yue') { $yue = 'selected'; } else { $yue = ''; }

	if ($row['enableTimeEdits'] == '1') { $allowEdits = 'selected'; } else { $allowEdits = ''; }
	if ($row['enablePii'] == '1') { $showPii = 'selected'; } else { $showPii = ''; }

	include 'includes/navigation.php';

	if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger no-margin">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-cogs"></i> <?php echo $globalSiteSetNavLink; ?></a></li>
			<li class="pull-right"><a href="index.php?page=importData"><i class="fa fa-hdd-o"></i> <?php echo $importDataNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<form action="" method="post">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="installUrl"><?php echo $installUrlField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="installUrl" value="<?php echo $row['installUrl']; ?>" />
								<span class="help-block"><?php echo $installUrlFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="localization"><?php echo $localField; ?></label>
								<select class="form-control" name="localization">
									<option value="ar" <?php echo $ar; ?>><?php echo $optionArabic; ?> &mdash; ar.php</option>
									<option value="bg" <?php echo $bg; ?>><?php echo $optionBulgarian; ?> &mdash; bg.php</option>
									<option value="ce" <?php echo $ce; ?>><?php echo $optionChechen; ?> &mdash; ce.php</option>
									<option value="cs" <?php echo $cs; ?>><?php echo $optionCzech; ?> &mdash; cs.php</option>
									<option value="da" <?php echo $da; ?>><?php echo $optionDanish; ?> &mdash; da.php</option>
									<option value="en" <?php echo $en; ?>><?php echo $optionEnglish; ?> &mdash; en.php</option>
									<option value="en-ca" <?php echo $en_ca; ?>><?php echo $optionCanadianEnglish; ?> &mdash; en-ca.php</option>
									<option value="en-gb" <?php echo $en_gb; ?>><?php echo $optionBritishEnglish; ?> &mdash; en-gb.php</option>
									<option value="es" <?php echo $es; ?>><?php echo $optionEspanol; ?> &mdash; es.php</option>
									<option value="fr" <?php echo $fr; ?>><?php echo $optionFrench; ?> &mdash; fr.php</option>
									<option value="ge" <?php echo $ge; ?>><?php echo $optionGerman; ?> &mdash; ge.php</option>
									<option value="hr" <?php echo $hr; ?>><?php echo $optionCroatian; ?> &mdash; hr.php</option>
									<option value="hu" <?php echo $hu; ?>><?php echo $optionHungarian; ?> &mdash; hu.php</option>
									<option value="hy" <?php echo $hy; ?>><?php echo $optionArmenian; ?> &mdash; hy.php</option>
									<option value="id" <?php echo $id; ?>><?php echo $optionIndonesian; ?> &mdash; id.php</option>
									<option value="it" <?php echo $it; ?>><?php echo $optionItalian; ?> &mdash; it.php</option>
									<option value="ja" <?php echo $ja; ?>><?php echo $optionJapanese; ?> &mdash; ja.php</option>
									<option value="ko" <?php echo $ko; ?>><?php echo $optionKorean; ?> &mdash; ko.php</option>
									<option value="nl" <?php echo $nl; ?>><?php echo $optionDutch; ?> &mdash; nl.php</option>
									<option value="pt" <?php echo $pt; ?>><?php echo $optionPortuguese; ?> &mdash; pt.php</option>
									<option value="ro" <?php echo $ro; ?>><?php echo $optionRomanian; ?> &mdash; ro.php</option>
									<option value="sv" <?php echo $sv; ?>><?php echo $optionSwedish; ?> &mdash; sv.php</option>
									<option value="th" <?php echo $th; ?>><?php echo $optionThai; ?> &mdash; th.php</option>
									<option value="vi" <?php echo $vi; ?>><?php echo $optionVietnamese; ?> &mdash; vi.php</option>
									<option value="yue" <?php echo $yue; ?>><?php echo $optionCantonese; ?> &mdash; yue.php</option>
								</select>
								<span class="help-block"><?php echo $localFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="siteName"><?php echo $siteNameField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="siteName" value="<?php echo clean($row['siteName']); ?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="businessName"><?php echo $busNameField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="businessName" value="<?php echo clean($row['businessName']); ?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="businessEmail"><?php echo $busEmailField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="businessEmail" value="<?php echo clean($row['businessEmail']); ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessPhone1"><?php echo $busPhoneField1; ?></label>
								<input type="text" class="form-control" name="businessPhone1" value="<?php echo clean($row['businessPhone1']); ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessPhone2"><?php echo $busPhoneField2; ?></label>
								<input type="text" class="form-control" name="businessPhone2" value="<?php echo clean($row['businessPhone2']); ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="businessAddress"><?php echo $busAddyField; ?> <sup><?php echo $reqField; ?></sup></label>
						<textarea class="form-control" required="" name="businessAddress" rows="2"><?php echo clean($row['businessAddress']); ?></textarea>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="enableTimeEdits"><?php echo $allowTimeEditsField; ?></label>
								<select class="form-control" name="enableTimeEdits">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1" <?php echo $allowEdits; ?>><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $allowTimeEditsFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="enablePii"><?php echo $enablePiiField; ?></label>
								<select class="form-control" name="enablePii">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1" <?php echo $showPii; ?>><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $enablePiiFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessDocs"><?php echo $busDocsFoldField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="businessDocs" value="<?php echo clean($row['businessDocs']); ?>" />
								<span class="help-block"><?php echo $busDocsFoldFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fileTypesAllowed"><?php echo $uplFileTypeField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="fileTypesAllowed" value="<?php echo clean($row['fileTypesAllowed']); ?>" />
								<span class="help-block"><?php echo $uplFileTypeFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="avatarFolder"><?php echo $avatarFoldField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="avatarFolder" value="<?php echo clean($row['avatarFolder']); ?>" />
								<span class="help-block"><?php echo $avatarFoldFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="avatarTypes"><?php echo $avatarFileTypesField; ?> <sup><?php echo $reqField; ?></sup></label>
								<input type="text" class="form-control" required="" name="avatarTypes" value="<?php echo clean($row['avatarTypes']); ?>" />
								<span class="help-block"><?php echo $avatarFileTypesFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<button type="input" name="submit" value="updateSettings" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updSettingsBtn; ?></button>
				</form>
			</div>
		</div>
	</div>
<?php } ?>