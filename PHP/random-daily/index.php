<?

	//
	// return a different random integer once per day
	//
	// (useful for featured product etc. where you want a different item each day)
	//
	function getDailyRandom($count) {
		srand(mktime(0, 0, 0));
		return rand(0, $count);
	}


?>