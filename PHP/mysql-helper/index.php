<?

	// MySQL Helper
	//
	// A set of simple scripts to allow database creation and schema definition through PHP
	//
	// Built to work with MAMP default settings, should work on any server with proper configuration in db/config.php
	//
	// Running this script will cycle between creating a database and populating, showing, 
	// exporting, deleting, and importing it. Or simply deleting it if the database already 
	// exists. Not particularly useful, but it's only meant to demonstrate how the various 
	// functions work.
	

	// for the sake of this demo
	ob_start();


	// database server configuration
	include ("includes/db/config.php");
	// database schema
	include ("includes/db/schema.php");
	// some example data
	include ("includes/db/data.php");
	// common functions
	include ("includes/db/functions.php");

	// strict error reporting while debugging
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');



	// connect to db server	
	$connection = @mysql_connect ($config["host"]["name"], $config["host"]["user"], $config["host"]["password"]) or die ('Couldn\'t connect: ' . mysql_error());


		
	// create a new database
	if (createDB($connection, $config["db"]["name"]) == "") {

		// select this database
		if (mysql_select_db($config["db"]["name"])) {


			//
			// initial setup
			//

			// get some information about the database environment
			echo "<h2>Displaying databases</h2>";
			listDBTables($connection);

			// create tables
			foreach ($db_schema as $table => $keys) {
				$tableResult = addTable($connection, $table, $keys);
				// if there's an error, show it
				if ($tableResult) echo $tableResult;
			}

			// populate tables
			foreach ($db_data as $table => $items) {
				$tableResult = populateTable($connection, $table, $items);
				// if there's an error, show it
				if ($tableResult) echo $tableResult;
			}

			// display the tables on screen so we can see they were created
			echo "<h2>Showing script-created tables</h2>";
			if ($tables = getTableList($connection, $config["db"]["name"])) {
				foreach ($tables as $id => $table) {
					printTable($connection, $table);
				}
			}


			//
			// export and kill database
			//

			// dump this database to a file
			exportDB($connection, $config["db"]["name"], $config["db"]["backupFile"]);

			// remove the tables we just created
			foreach ($db_schema as $table => $keys) {
				$tableResult = deleteTable($connection, $table);
				// if there's an error, show it
				if ($tableResult) echo $tableResult;
			}

			// display the tables on screen so we can see they were dumped
			echo "<h2>Showing tables after database export and table dump</h2>";
			if ($tables = getTableList($connection, $config["db"]["name"])) {
				foreach ($tables as $id => $table) {
					printTable($connection, $table);
				}
			} else {
				echo "<br><br>No tables found.<br><br>";
			}



			//
			// import database from file and show it on screen
			//

			echo "<h2>Showing tables after file import</h2>";
			$importResult = importDB($connection, $config["db"]["name"], $config["db"]["backupFile"]);
			// if there's an error, show it
			if ($importResult) echo $importResult;

			// display the tables on screen so we can see they were dumped
			if ($tables = getTableList($connection, $config["db"]["name"])) {
				foreach ($tables as $id => $table) {
					printTable($connection, $table);
				}
			} else {
				echo "<br><br>No tables found.<br><br>";
			}



		} else {
			echo "Error selecting database: " . mysql_error();
		}


	} else {

		// drop the database
		deleteDB($connection, $config["db"]["name"]);

	}


	// disconnect from db server	
	mysql_close($connection);


	// dump the output buffer
	$buffer = ob_get_contents();
	ob_end_clean();

?>


	<h1>MySQL Helper</h1>
	<p>This script is intended as a package of basic MySQL database functions. When you load this page, it will cycle through and show one of these two alternating states:</p>
	<ul>
		<li>Database roundtrip: Show the existing server environment, create a new database from the contents of <code>db/schema.php</code>, populate it with example data from <code>db/data.php</code>, export it to a flat file, delete all tables, then re-import from the generated flat file.</li>
		<li>Database dump: throwing out the previous database and showing nothing.</li>
	</ul>
	<p>Not particularly useful, but these functions are meant to serve more as a starting point for future code.</p>

	<p>Below this line, you'll see the results:</p>
	<hr>

<?php

	echo $buffer;

?>