<?

	// MySQL Restore Tool

	// all database configuration settings here
	$config = array(

		// host defines database host, plus username and password
		"host" => array(
			"name" => "localhost",
			"user" => "root",
			"password" => "root",
		),

		// db defines name of the database you'd like to work with, plus an optional filename for importing/exporting
		"db" => array(
			"name" => "testing",
			"backupFile" => "db-backup-" . date("Y-m-d-(H-i-s)") . ".sql",
		),
	);


	// import a flat file MySQL dump
	// (assumes open connection)
	function importDB($connection, $name, $file) {

		// initialize
		$returnValue = "";
		$sql = "";
		mysql_select_db($name, $connection);

		// start reading in the file, if it exists
		$lines = file($file);
		foreach($lines as $line) {
			if (strlen($line) > 1) { // to avoid blank lines
				$sql .= ltrim(rtrim($line));
				// if we've found a semi-colon it's time to execute
				if (strpos($sql, ";")) {
					if (!mysql_query($sql, $connection)) {
				    $returnValue .= mysql_error();
					}
			 		$sql = "";   
				}
			}
		}
		// return any errors encountered
		if ($returnValue) {
			return 'Error importing database: ' . $returnValue;
		}

	}


	// create list of all databases on the host
	// (assumes open connection)
	function listDBs($connection) {

		$dbs = Array();
	
		$db_list = mysql_list_dbs($connection);
		while ($row = mysql_fetch_object($db_list)) {
			array_push($dbs, $row->Database);
		}
		if (count($dbs)) {
			return $dbs;
		}
	}



	// strict error reporting while debugging
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');



	// connect to db server	
	$connection = @mysql_connect ($config["host"]["name"], $config["host"]["user"], $config["host"]["password"]) or die ('Couldn\'t connect: ' . mysql_error());


	$dbs = listDBs($connection);
	$restoreResult = false;

	// only bother if there's a file coming with the submit
	if (isset($_POST["import"])) {

		print_r($_FILES);
		if (isset($_FILES["importFile"])) {
			$file = $_FILES["importFile"]["tmp_name"];
			echo $file;
		}

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

		if (isset($file) && isset($dbName)) {
			if (!($restoreResult = importDB($connection, $dbName, $file))) {
				$restoreResult = true;
			}
		} else {
			$restoreResult = "Error processing file.";
		}			

	}


	// disconnect from db server	
	mysql_close($connection);



?>



<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>MySQL Restore</title>
	<meta name="robots" content="all">

	<link rel="stylesheet" href="../../common-ui/css/default.css" media="screen">
	<script src="../../common-ui/script/custom-forms.js"></script>
</head>
<body>


<div class="dialog">

	<header>
		<h1>MySQL Restore</h1>
	</header>

<?php
	if (!$restoreResult) {
?>
	<p>This script will import a previously backed-up MySQL database file to the server.</p>

	<form method="post" action="./restore.php">
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


</div>

</body>
</html>