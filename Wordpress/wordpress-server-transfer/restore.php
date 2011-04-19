<?

	// MySQL Restore Tool

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

	$restoreResult = false;
	// only bother if there's a file coming with the submit
	if (isset($_POST["import"])) {

		if (isset($_FILES["importFile"]["tmp_name"]) && (strlen($_FILES["importFile"]["tmp_name"]) > 0)) {

			$file = $_FILES["importFile"]["tmp_name"];

			// set name based on whether existing or new values have been defined.
			// favour new to avoid nuking existing dbs where possible
			if (isset($_POST["db-existing"])) {
				$dbName = $_POST["db-existing"];
			}
			if (isset($_POST["db-new"])) {
				if (strlen($_POST["db-new"])) {
					$dbName = $_POST["db-new"];
				}
			}
	
			// if we've got a file and a database name, import it
			if (isset($file) && isset($dbName)) {
				if (!($restoreResult = importDB($connection, $dbName, $file, $dbs))) {
					$restoreResult = true;
				}
			} else {
				$restoreResult = "Can't use specified filename/database.";
			}			

		}
	}


	// disconnect from db server	
	mysql_close($connection);

?>



<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Wordpress DB Restore</title>
	<meta name="robots" content="all">

	<link rel="stylesheet" href="common/css/default.css" media="screen">
	<script src="common/script/custom-forms.js"></script>
</head>
<body>


<div class="dialog">

	<header>
		<h1>Wordpress DB Restore</h1>
	</header>

<?php
	if (!$restoreResult) {
?>
	<p>This script will import a previously backed-up Wordpress MySQL database file to the server.</p>

	<form method="post" action="./restore.php" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="import" value="true">
			<label>Select a file:</label>
			<input type="file" name="importFile">
		</div>
		<div>
			<label>Replace Existing:</label>
			<select name="db-existing" size="5">
			<?php
				foreach ($dbs as $key => $name) {
					echo "<option value=\"$name\">$name</option>";
				}
			?>
			</select>
		</div>
		<div class="extra">
			- or -
		</div>
		<div>
			<label>Create New:</label>
			<input name="db-new" value="" type="text">
		</div>
		<div class="buttons">
			<button type="submit" name="restore">Restore</button>
		</div>
	</form>

<?php
	} else if (strlen($restoreResult) > 1) {
?>

	<p>Something went wrong:</p>
	<p><?php echo $restoreResult; ?></p>

<?	
	} else {

?>
	<p>Imported!</p>

<?php
	}
?>


	<p class="warning"><strong>Warning:</strong> this script is dangerous to leave unprotected on a server, as it allows access to your database server to anyone who knows where to look. Protecting this script with <a href="http://php.net/manual/en/features.http-auth.php">HTTP Authentication</a> should be considered mandatory, but even higher security is recommended if possible.</p>

</div>

</body>
</html>