<?

	//
	// Information about the DB server
	//
	// list current databases
	$db_list = mysql_list_dbs($connection);
	while ($row = mysql_fetch_object($db_list)) {

		echo "<br>Database name: " . $row->Database . "<br>";

		// list all rows from current database
		mysql_select_db($row->Database);
		$sql = "SHOW TABLES FROM " . $row->Database;
		$result = mysql_query($sql);
		if ($result) {
			while ($row = mysql_fetch_row($result)) {
				echo "Table: {$row[0]}<br>";
			}
		}

	}


?>