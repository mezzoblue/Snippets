<?

	// all database configuration settings here
	$config = array(

		// host defines database host name, plus username and password
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

?>