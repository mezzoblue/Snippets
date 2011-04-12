<?

	//
	// return a random integer between $min and $max but make sure it's a different number than the previous result
	//
	// (useful for image rotators etc. where you don't want to repeat the previous image)
	//
	function smoothRandomInt($min, $max) {

		// hold on to the previous value
		static $prev;

		// pick a new random number
		$num = rand($min, $max);
		
		// if the new one matches the previous, we need to change it by adding one
		// if the resulting number is larger than $max, set it to $min
		if ($num == $prev) {
			$num == $max ? ($num = $min) : ($num++);
		}

		// set the previous value and return the new one
		$prev = $num;
		return($num);
	}


?>