<?php
	$pagPages = '10';

	// Get the Documents Folder from the Site Settings
	$uploadsDir = $set['businessDocs'];

	// Delete Document
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteDoc') {
		$docId = $mysqli->real_escape_string($_POST['docId']);
		$docUrl = $mysqli->real_escape_string($_POST['docUrl']);

		// Delete the file from the server
		$filePath = $uploadsDir.'/'.$docUrl;

		if (file_exists($filePath)) {
			// Delete the File
			unlink($filePath);

			// Delete the Record
			$stmt = $mysqli->prepare("DELETE FROM documents WHERE docId = ?");
			$stmt->bind_param('s', $docId);
			$stmt->execute();
			$stmt->close();

			$msgBox = alertBox($docDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox($deleteErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM documents");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get File Data
    $sql  = "SELECT
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
			ORDER BY orderDate, documents.docId ".$pages->get_limit();
    $res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());

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
			<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-files-o"></i> <?php echo $busDocsNavLink; ?></a></li>
			<li class="pull-right"><a href="index.php?page=newDocument" class="bg-success"><i class="fa fa-upload"></i> <?php echo $uplNewDocNavLink; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane in active" id="home">
				<?php if(mysqli_num_rows($res) < 1) { ?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noUploadsFound; ?>
					</div>
				<?php } else { ?>
					<table class="rwd-table">
						<tbody>
							<tr class="primary">
								<th><?php echo $DocNameField; ?></th>
								<th><?php echo $descField; ?></th>
								<th><?php echo $dateUplField; ?></th>
								<th><?php echo $uploadedByField; ?></th>
								<th></th>
							</tr>
							<?php while ($row = mysqli_fetch_assoc($res)) { ?>
								<tr>
									<td data-th="<?php echo $DocNameField; ?>">
										<a href="index.php?page=viewDocument&docId=<?php echo $row['docId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewDocTooltip; ?>">
											<?php echo clean($row['docName']); ?>
										</a>
									</td>
									<td data-th="<?php echo $descField; ?>"><?php echo ellipsis($row['docDesc'],75); ?></td>
									<td data-th="<?php echo $dateUplField; ?>"><?php echo $row['docDate']; ?></td>
									<td data-th="<?php echo $uploadedByField; ?>"><?php echo clean($row['UploadedBy']); ?></td>
									<td data-th="<?php echo $actionText; ?>">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewDocTooltip; ?>">
											<a href="index.php?page=viewDocument&docId=<?php echo $row['docId']; ?>"><i class="fa fa-file-text edit"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteDocTooltip; ?>">
											<a href="#deleteDoc<?php echo $row['docId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
										</span>
									</td>
								</tr>

								<div class="modal fade" id="deleteDoc<?php echo $row['docId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deleteDocConf.' '.clean($row['docName']); ?>?</p>
												</div>
												<div class="modal-footer">
													<input name="docId" type="hidden" value="<?php echo $row['docId']; ?>" />
													<input name="docUrl" type="hidden" value="<?php echo $row['docUrl']; ?>" />
													<button type="input" name="submit" value="deleteDoc" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $deleteDocTooltip; ?></button>
													<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
												</div>
											</form>
										</div>
									</div>
								</div>
							<?php } ?>
						</tbody>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>
<?php } ?>