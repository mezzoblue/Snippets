<?

	// MySQL Backup Tool

	// database server configuration
	include ("config.php");
	// common functions
	include ("common/functions.php");

	// strict error reporting while debugging
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');



	// connect to db server	
	$connection = @mysql_connect ($config["host"]["name"], $config["host"]["user"], $config["host"]["password"]) or die ('Couldn\'t connect: ' . mysql_error());

	// get the database list
	$dbs = listDBs($connection);

	// run the backup
	$backupResult = false;
	if (isset($_POST["export"])) {
		// dump this database to a file
		exportDB($connection, $_POST["db"], $_POST["filename"], $_POST["local-host-url"], $_POST["remote-host-url"]);
		$backupResult = true;
	}


	// if this is a file download, we have to send via headers before showing the page
	if (isset($_POST["download"])) {

		// security check: does the file exist? Is it something other than a .php file? Then okay.
		// otherwise: DIE.
		// adapted from http://safalra.com/programming/php/prevent-hotlinking/
		if (
			(!$file = realpath($_POST["filename"])) || 
			(substr($file, -4) == '.php')
			) {
				header('HTTP/1.0 404 Not Found');
				exit();
		}

		// if the temporary file is there, send it then delete it from the server after download
		if (file_exists($_POST["filename"])) {
			$size = filesize($_POST["filename"]);
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $_POST["filename"]);
			header('Content-Length: ' . $size);
			readfile($_POST["filename"]);
			unlink($_POST["filename"]);
		} else {
			$backupResult = "Error saving file to disk.";
		}


	}

	// disconnect from db server	
	mysql_close($connection);

?>



<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Wordpress DB Backup</title>
	<meta name="robots" content="all">

	<link rel="stylesheet" href="common/css/default.css" media="screen">
	<script src="common/script/custom-forms.js"></script>
</head>
<body>


<div class="dialog">

	<header>
		<h1>Wordpress DB Backup</h1>
	</header>

<?php
	if (!$backupResult) {
?>
	<p>This script will backup the Wordpress MySQL database of your choice to a flat file.</p>
	<form method="post" action="./backup.php">
		<div>
			<input type="hidden" name="export" value="true">
			<label>Select a database:</label>
			<div class="form-styled">
				<select name="db" class="styled">
			<?php
				foreach ($dbs as $key => $name) {
					echo "<option value=\"$name\">$name</option>";
				}
			?>
				</select>
			</div>
		</div>
		<div>
			<label>Filename:</label>
			<input name="filename" value="<?php echo $config["db"]["backupFile"]; ?>" type="text">
		</div>
		<div>
			<p class="extra"> &#8211; The values below are configurable in <code>config.php</code> &#8211;</p>
		</div>
		<div>
			<label>Local Host URL:</label>
			<input name="local-host-url" value="<?php echo $config["host"]["local-host-url"]; ?>" type="text">
		</div>
		<div>
			<label>Remote Host URL:</label>
			<input name="remote-host-url" value="<?php echo $config["host"]["remote-host-url"]; ?>" type="text">
		</div>
		<div class="buttons">
			<button type="submit" name="save">Save on Server</button>
			<button type="submit" name="download">Download</button>
		</div>
	</form>

<?php
	} else if (strlen($backupResult) > 1) {
?>

	<p>Something went wrong:</p>
	<p><?php echo $backupResult; ?></p>

<?	
	} else {

?>
	<p>Database saved.</p>

<?php
	}
?>

	<p class="warning"><strong>Warning:</strong> this script is dangerous to leave unprotected on a server, as it allows access to your database server to anyone who knows where to look. Protecting this script with <a href="http://php.net/manual/en/features.http-auth.php">HTTP Authentication</a> should be considered mandatory, but even higher security is recommended if possible.</p>

</div>

</body>
</html>