<?

	// all database configuration settings here
	// (note: an assumption is made in functions.php that "id" is what the primary key of each table is called.
	//	you can change it if you like, just make sure to change the reference there too.)
	$db_schema = array(
		"posts" => array(
			"id" => "MEDIUMINT(3) UNSIGNED NOT NULL AUTO_INCREMENT, ",
			"photo_id" => "MEDIUMINT(3) UNSIGNED, ",
			"title" => "VARCHAR(255), ",
			"date" => "DATETIME, ",
			"url" => "TEXT, ",
			"text" => "MEDIUMTEXT, ",
			"comments" => "SMALLINT UNSIGNED, ",
		),
		"photos" => array(
			"id" => "MEDIUMINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,",
			"src" => "TEXT,",
			"width" => "SMALLINT UNSIGNED,",
			"height" => "SMALLINT UNSIGNED,",
			"alt" => "VARCHAR(255), ",
		),
	);

?>