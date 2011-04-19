<?
	
	// Simple MySQL database creation tool
	// Handy for creating that initial empty Wordpress database without mucking around in PHPMyAdmin

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

	$result = "";
	// you really don't want to run this without user confirmation first
	if (isset($_POST["create"]) && isset($_POST["filename"])) {
		// create a new database
		if (createDB($connection, $_POST["filename"]) == "") {
			$result = "Created database.";
		} else {
			$result = "Couldn't connect, or database already exists.";
		}
	}


	// disconnect from db server	
	mysql_close($connection);


?>



<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Wordpress DB Creation</title>
	<meta name="robots" content="all">

	<link rel="stylesheet" href="common/css/default.css" media="screen">
	<script src="common/script/custom-forms.js"></script>
</head>
<body>


<div class="dialog">

	<header>
		<h1>Wordpress DB Creation</h1>
	</header>

<?php
	if (!$result) {
?>
	<p>This script will create a new MySQL database on the server. Configure host settings in <code>config.php</code> first.</p>

<?php
	if (count($dbs)) {
?>
	<p>Existing databases that you may not want to overwrite:</p>
	<ul>
<?php
	foreach ($dbs as $key => $name) {
		echo "<li>$name</li>\n";
	}
?>
	</ul>
<?php
	}
?>

	<form method="post" action="./create.php" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="create" value="true">
			<label>New database name:</label>
			<input type="text" name="filename" value="<?php echo $config["db"]["name"]; ?>">
		</div>
		<div class="buttons">
			<button type="submit" name="restore">Make it so</button>
		</div>
	</form>

<?php
	} else if (strlen($result) > 1) {
?>

	<p>Something went wrong:</p>
	<p><?php echo $result; ?></p>

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