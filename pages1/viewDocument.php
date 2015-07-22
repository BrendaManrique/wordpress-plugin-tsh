<?php
	$docId = $_GET['docId'];

	// Get the Documents Folder from the Site Settings
	$uploadsDir = $set['businessDocs'];

	// Edit Document Description
    if (isset($_POST['submit']) && $_POST['submit'] == 'editDoc') {
        // Validation
		if($_POST['docDesc'] == "") {
            $msgBox = alertBox($docDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$docDesc = $_POST['docDesc'];
            $stmt = $mysqli->prepare("UPDATE documents SET docDesc = ? WHERE docId = ?");
			$stmt->bind_param('ss', $docDesc, $docId);
			$stmt->execute();
			$msgBox = alertBox($docDescUpdt, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Document Data
    $query  = "SELECT
				documents.docId,
				documents.empId,
				documents.docName,
				documents.docDesc,
				documents.docUrl,
				DATE_FORMAT(documents.docDate,'%M %d, %Y') AS docDate,
				UNIX_TIMESTAMP(documents.docDate) AS orderDate,
				CONCAT(employees.empFirst,' ',employees.empLast) AS UploadedBy
			FROM
				documents
				LEFT JOIN employees ON documents.empId = employees.empId
			WHERE documents.docId = ".$docId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

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
			<li class="pull-right"><a href="index.php?page=newDocument" class="bg-success"><i class="fa fa-upload"></i> <?php echo $uplNewDocNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<div class="list-group mb20">
					<li class="list-group-item"><strong><?php echo $docTitleField; ?>:</strong> <?php echo clean($row['docName']); ?></li>
					<li class="list-group-item"><strong><?php echo $dateUplField; ?>:</strong> <?php echo $row['docDate']; ?></li>
					<li class="list-group-item"><strong><?php echo $uploadedByField; ?>:</strong> <?php echo clean($row['UploadedBy']); ?></li>
					<li class="list-group-item"><?php echo nl2br(clean($row['docDesc'])); ?></li>
				</div>

				<a data-toggle="modal" data-target="#editDoc" class="btn btn-success btn-icon"><i class="fa fa-edit"></i> <?php echo $editDocDescBtn; ?></a>

				<hr />

				<?php
					//Get Template Extension
					$ext = substr(strrchr($row['docUrl'],'.'), 1);
					$imgExts = array('gif','GIF','jpg','JPG','jpeg','JPEG','png','PNG','tiff','TIFF','tif','TIF','bmp','BMP');

					if (in_array($ext, $imgExts)) {
						echo '<p class="no-margin mt20"><img alt="'.clean($row['docName']).'" src="'.$uploadsDir.$row['docUrl'].'" class="img-responsive" /></p>';
					} else {
						echo '
								<div class="alertMsg default no-margin mb10"><i class="fa fa-info-circle"></i> '.$noPrevAvail.' '.clean($row['docName']).'</div>
								<p>
									<a href="'.$uploadsDir.$row['docUrl'].'" class="btn btn-info btn-icon" target="_blank">
									<i class="fa fa-download"></i> '.$downloadText.' '.$row['docName'].'</a>
								</p>
							';
					}
				?>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editDoc" tabindex="-1" role="dialog" aria-labelledby="editDoc" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $editDocDescModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="docDesc"><?php echo $docDescField; ?> <sup><?php echo $reqField; ?></sup></label>
							<textarea class="form-control" name="docDesc" required="" rows="6"><?php echo clean($row['docDesc']); ?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="editDoc" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>