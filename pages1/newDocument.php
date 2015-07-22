<?php
	// Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get the Documents Folder from the Site Settings
	$uploadsDir = $set['businessDocs'];

	// Get the File Types allowed
	$fileExt = $set['fileTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );

	// Upload a New Document
    if (isset($_POST['submit']) && $_POST['submit'] == 'uploadDoc') {
		// Validation
        if($_POST['docName'] == "") {
            $msgBox = alertBox($docTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['docDesc'] == "") {
            $msgBox = alertBox($docDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(empty($_FILES['file']['name'])) {
            $msgBox = alertBox($docFileReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Check file type
            $ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
            if (!in_array($ext, $ftypes_data)) {
                $msgBox = alertBox($invalidDocTypeMsg, "<i class='fa fa-times-circle'></i>", "danger");
            } else {
				$docName = $mysqli->real_escape_string($_POST['docName']);
				$docDesc = htmlentities($_POST['docDesc']);
				$docDate = date("Y-m-d H:i:s");

				// Replace any spaces with an underscore
				// And set to all lower-case
				$newName = str_replace(' ', '_', $docName);
				$fileNewName = strtolower($newName);

				// Set the upload path
				$uploadTo = $uploadsDir;
				$fileUrl = basename($_FILES['file']['name']);

				// Get the files original Ext
				$extension = end(explode(".", $fileUrl));

				// Generate a random string to append to the file's name
				$randomString=md5(uniqid(rand()));
				$appendName=substr($randomString, 0, 8);

				// Set the files name to the name set in the form
				// And add the original Ext
				$newfilename = $fileNewName.'-'.$appendName.'.'.$extension;
				$movePath = $uploadTo.'/'.$newfilename;

				$stmt = $mysqli->prepare("
                                    INSERT INTO
                                        documents(
                                            empId,
                                            docName,
                                            docDesc,
                                            docUrl,
                                            docDate
                                        ) VALUES (
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?
                                        )");
                $stmt->bind_param('sssss',
                    $empId,
                    $docName,
                    $docDesc,
                    $newfilename,
                    $docDate
                );

                if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
                    $stmt->execute();
					$msgBox = alertBox($docUplMsg, "<i class='fa fa-check-square'></i>", "success");
					// Clear the Form of values
					$_POST['docName'] = $_POST['docDesc'] = '';
					$stmt->close();
				}
			}
		}
	}

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($isMgr != '1')) {
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
			<li><a href="index.php?page=businessDocs"><i class="fa fa-files-o"></i> <?php echo $busDocsNavLink; ?></a></li>
			<li class="pull-right"><a href="#home" data-toggle="tab" class="bg-success"><i class="fa fa-upload"></i> <?php echo $uplNewDocNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<p>
					<strong><?php echo $maxUploadText; ?></strong> <?php echo $maxUpload.' '.$mbText; ?><br />
					<strong><?php echo $allowedFileTypesText; ?></strong> <?php echo $allowed; ?>
				</p>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="docName"><?php echo $docTitleField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="text" class="form-control" name="docName" required="" value="<?php echo isset($_POST['docName']) ? $_POST['docName'] : ''; ?>">
					</div>
					<div class="form-group">
						<label for="docDesc"><?php echo $docDescField; ?> <sup><?php echo $reqField; ?></sup></label>
						<textarea class="form-control" name="docDesc" required="" rows="4"><?php echo isset($_POST['docDesc']) ? $_POST['docDesc'] : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="file"><?php echo $selectFileField; ?> <sup><?php echo $reqField; ?></sup></label>
						<input type="file" id="file" name="file" required="">
					</div>
					<button type="input" name="submit" value="uploadDoc" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadDocBtn; ?></button>
				</form>
			</div>
		</div>
	</div>
<?php } ?>