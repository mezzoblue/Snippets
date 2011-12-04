<?php
		// formValidate
		// Purpose: Ensure no harmful characters are present
		// Expects: slash-stripped string
		// Returns: value, if approved
		function formValidate($value) {
			if (eregi("\r", $value) || eregi("\n", $value) || eregi("MIME-Version: ", $value)) {
				die("no");
			} else {
				return $value;
			}
		}	
		
		// formCheckPost
		// Purpose: Check if there's post data for the argument specified, return it if so
		// Expects: String (post argument name)
		// Returns: nothing, or post value
		function formCheckPost($postName) {
			if ($_POST[$postName]) {
				return $_POST[$postName];
			}
		}	
		
		// formCheckChecked
		// Purpose: Check if there's a checked value for post data for the argument specified, return it if so
		// Expects: String (post argument name)
		// Returns: nothing, or HTML string
		function formCheckChecked($postName) {
			if ($_POST[$postName]) {
				return 'checked="checked"';
			}
		}	
		// formCheckSelected
		// Purpose: Check if there's a selected value for post data for the argument specified, return it if so
		// Expects: String (post argument name)
		// Returns: nothing, or HTML string
		function formCheckSelected($postName, $valueName) {
			if ($_POST[$postName]) {
				foreach($_POST[$postName] as $value) {
					if ($value == $valueName) {
						return 'selected="selected"';
					}
				}
			}
		}


		// data verification to reduce form misuse and cut down on spam
		foreach($_POST as $key => $value) {
			formValidate(stripslashes($value));
		}


		$errors = false;

		// check if we're missing required fields
		foreach($formFields as $field => $data) {
			if ($data["required"]) {
				if (!$_POST[$field]) {
					$errors = "Required fields are missing.";
				}
			}
		}


		if (!$errors) {
			

			// set sender email address
			if ($_POST["email"]) {
				$fromemail = "From: " . $_POST["email"];
			} else {
				$fromemail = "From: " . $emailDefaults["fromemail"];
			}
			// set mail subject
			if ($_POST["subject"]) {
				$subject = $_POST["subject"];
			} else {
				$subject = $emailDefaults["subject"];
			}
			
	
			// build the email body
			$message = "";
			foreach($_POST as $key => $value) {
				// remove form handling fields
				if (($key != "sendmail") && ($key != "subject")) {
					$message .= str_replace("-", " ", ucwords($key)) . ": " . $value . "\n\n";
				}
			}
			

			// send the email
			mail($emailDefaults["toemail"], $subject, $message, $fromemail);
			
			// flag it was sent so form doesn't show up
			$messageSent = true;
		}


?>