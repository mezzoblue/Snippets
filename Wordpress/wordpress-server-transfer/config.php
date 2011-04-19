<?
	$config = array(
		"host" => array(


			// name: your local server hostname
			//
			// user: your MySQL username for that server
			//
			// password: your MySQL password
			//
			"name" => "localhost",
			"user" => "root",
			"password" => "root",



			// The fields below are URLs that need to be swapped during database import/export
			//
			// Wordpress references the absolute path to your server in multiple spots, 
			// which causes the database to break when transferring between servers.
			//
			// The backup script has a provision to swap these out automatically when
			// saving the flat file, you just need to make sure they're configured properly
			// here. Ensure each points to the root of your Wordpress site, without a trailing
			// slash on the end.
			//
			// These are optional, but highly recommended.
			//
			"local-host-url" => "http://localhost:8888/project",
			"remote-host-url" => "http://example.com/projects/root",

		),
		"db" => array(


			// name: the database you'd like to work with, optional. 
			//   If you set a name here, the specified database will be automatically filled out in each script
			//
			// backupFile: the filename of the backed up database file, optional.
			//
			"name" => "test-db",
			"backupFile" => "test-db-backup.sql",


		),
	);
?>