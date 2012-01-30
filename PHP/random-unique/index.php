<?

	//
	// return an array of unique random integers
	//
	function getUniqueRandoms($returnNum = 10, $rangeMax = 10) {
		$return = array();
		while(count($return) < $returnNum) {
			$newRandom = mt_rand(0, $rangeMax - 1);
			if (!in_array($newRandom, $return)) {
				array_push($return, $newRandom);
			}
		}
		return($return);
	}


?>