<?

	// MySQL Backup Tool

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

	// backup an entire db, or just a table, to a flat file
	// (assumes open connection)
	// (more or less copied wholesale from http://davidwalsh.name/backup-mysql-database-php)
	function exportDB($connection, $name, $file, $tables = '*') {

		// initialize
	  $return = $row2 = $result = "";
		mysql_query('SET NAMES utf8');
		mysql_query('SET CHARACTER SET utf8');
	  mysql_select_db($name, $connection);
	  
	  // get all of the tables
	  if($tables == '*') {
	    $tables = array();
	    $result = mysql_query('SHOW TABLES');
	    while($row = mysql_fetch_row($result)) {
	      $tables[] = $row[0];
	    }
	  }
	  else {
	    $tables = is_array($tables) ? $tables : explode(',', $tables);
	  }
	  
	  // cycle through
	  foreach($tables as $table) {
	    $result = mysql_query('SELECT * FROM ' . $table);
	    $num_fields = mysql_num_fields($result);
	    
	    $return .= 'DROP TABLE ' . $table . ';';
	    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
	    $return .= "\n\n" . $row2[1] . ";\n\n";
	    
	    for ($i = 0; $i < $num_fields; $i++) {
	      while($row = mysql_fetch_row($result)) {
	        $return .= 'INSERT INTO ' . $table . ' VALUES(';
	        for($j = 0; $j < $num_fields; $j++) {
	          $row[$j] = addslashes($row[$j]);
	          $row[$j] = ereg_replace("\n", "\\n", $row[$j]);
	          if (isset($row[$j])) {
	          	$return .= '"' . $row[$j].'"';
	          } else {
	          	$return .= '""';
	          }
	          if ($j < ($num_fields - 1)) {
	          	$return .= ',';
	          }
	        }
	        $return .= ");\n";
	      }
	    }
	    $return .= "\n\n\n";
	  }
	  
	  // save file
	  $handle = fopen($file , 'w+');
	  fwrite($handle, $return);
	  fclose($handle);

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

	$backupResult = false;
	if (isset($_POST["export"])) {
		// dump this database to a file
		exportDB($connection, $_POST["db"], $_POST["filename"]);
		$backupResult = true;
	}


	if (isset($_POST["download"])) {

		// security check: does the file exist? Is it something other than a .php file? Then okay.
		// otherwise: DIE.
		// adapted from http://safalra.com/programming/php/prevent-hotlinking/
		if (
			(!$file = realpath($_POST["filename"])) || 
			(substr($file, -4) == '.php')
			) {
				//header('HTTP/1.0 404 Not Found');
				echo "no dice";
				exit();
		}
		
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
	<title>MySQL Backup</title>
	<meta name="robots" content="all">

	<link rel="stylesheet" href="../../common-ui/css/default.css" media="screen">
	<script src="../../common-ui/script/custom-forms.js"></script>
</head>
<body>


<div class="dialog">

	<header>
		<h1>MySQL Backup</h1>
	</header>

<?php
	if (!$backupResult) {
?>
	<p>This script will backup the MySQL database of your choice to a flat file.</p>

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
			<button type="submit" name="save">Save to Disk</button>
			<button type="submit" name="download">Download</button>
		</div>
	</form>

<?php
	} else if (strlen($backupResult) > 1) {
?>

	<p>Something went wrong.</p>
	<p><?php echo $backupResult; ?></p>

<?	
	} else {

?>
	<p>Exported!</p>

<?php
	}
?>


</div>

</body>
</html>