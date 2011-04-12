//
// return a random integer between min and max but make sure it's a different number than the previous result
//
// (useful for image rotators etc. where you don't want to repeat the previous image)
// (see ../PHP/smooth-random-int for the PHP equivalent)
//
function smoothRandomInt(min, max) {

    // hold on to previous value
    // initialize prev if it doesn't exist yet
    if (typeof smoothRandomInt.prev == 'undefined') {
        smoothRandomInt.prev = 0;
    }

	// pick a new random number
	var diff = max - min;
	var num = Math.floor(Math.random() * diff + 1) + min;
	
	// if the new one matches the previous, we need to change it
	// if it's already the max value, loop around to min, otherwise, just add one.
	if (num == smoothRandomInt.prev) {
		num == max ? (num = min) : (num++);
	}

	// set the previous value and return the new one
	smoothRandomInt.prev = num;
	return(num);
}