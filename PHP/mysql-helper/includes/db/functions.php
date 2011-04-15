<?

	//
	//	DATABASE MANIPULATION
	//

	// create a new database
	// (assumes open connection)
 	function createDB($connection, $db) {
		$sql = "CREATE DATABASE $db";
		if (!mysql_query($sql, $connection)) {
		    return 'Error creating database: ' . mysql_error();
		}
 	}

	// drop existing database 
	// (assumes open connection)
 	function deleteDB($connection, $db) {
 		$sql = "DROP DATABASE $db";
 		if (!mysql_query($sql, $connection)) {
		    return 'Error dropping database: ' . mysql_error();
		}
	}
	
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
	

	// list all databases on the host
	// (assumes open connection)
	function listDBs($connection) {
	
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
	}
	




	//
	//	TABLE MANIPULATION
	//

	// create a table within currently-selected database
	// (assumes open connection and selected db)
	function addTable($connection, $table, $keys) {
		$sql = "CREATE TABLE $table (";

		// create list of keys to add, as well as data types
		foreach ($keys as $key => $type) {
			$sql .= $key . " " . $type;
		}
		
		// add primary key, we're assuming it's id
		$sql .= "PRIMARY KEY (id)";
		$sql .= ") TYPE=innodb";
		
 		if (!mysql_query($sql, $connection)) {
 			return 'Error creating table: ' . mysql_error();
		}
	}


	// remove a table within currently-selected database
	// (assumes open connection and selected db)
	function deleteTable($connection, $table) {
		$sql = "DROP TABLE $table";
 		if (!mysql_query($sql, $connection)) {
 			return 'Error deleting table: ' . mysql_error();
		}
	}


	// get a list of all tables within currently-selected database
	// (assumes open connection and selected db)
	function getTableList($connection, $db) {
		$sql = "SHOW TABLES FROM " . $db;
		$shown = mysql_query($sql);
		if (!$shown) {
		    return null;
		} else {
			$return = array();
			while ($table = mysql_fetch_row($shown)) {
				array_push($return, $table[0]);
			}
			return $return;
		}
		
	}


	// populate a table within currently-selected database
	// (assumes open connection and selected db)
	function populateTable($connection, $table, $items) {
		foreach ($items as $id => $data) {
			
			// generate insert
			$sql = "INSERT INTO $table (";
			foreach ($data as $key => $value) {
				$sql .= $key . ", ";
			}
			$sql = trimTrailingComma($sql) . ") VALUES (";
			foreach ($data as $key => $value) {
				$sql .= "'" . addslashes($value) . "', ";
			}
			$sql = trimTrailingComma($sql) . "); ";

			// run insert
			$result = mysql_query($sql);
			if (!$result) {
				return "Error inserting data: " . mysql_error();
			}
		}
		
	}
	

	// print a table within currently-selected database to screen, mainly useful for diagnostics
	// (assumes open connection and selected db)
	function printTable($connection, $table) {
	    echo "Table: " . $table . "\n";

	    $sql = "SELECT * FROM " . $table;
			$cols = mysql_query($sql);
			if (!$cols) {
			    echo 'Error listing tables: ' . mysql_error();
			} else {
				while($row = mysql_fetch_array($cols, MYSQL_NUM)) {
					print_r($row);
		    }
			}
	}



	//
	//	HELPER FUNCTIONS
	//

	// trim trailing comma and remove white space from a string
	// (needed to clean up SQL generated within loops)
	function trimTrailingComma($str) {
		// kill white space
		$str = ltrim(rtrim($str));
		// remove trailing comma
		return (substr($str, 0, strlen($str) - 1));
	}


?>