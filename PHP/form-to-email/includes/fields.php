<?php
	foreach ($formFields as $field => $data) {

		// created 'required' flag
		$required = $data["required"] ? " <strong class=\"required\">*</strong>" : "";
		// only display error if there are errors, the field is required, and doesn't have a value
		$error = ($errors && $data["required"] && !$_POST[$field]) ? " errors" : "";

		echo '					<div class="form-row' . ($data["class"] ? ($class = ' ' . $data["class"]) : "") . $error . "\">\n";
		echo '						<label for="form-' . $field . '">' . $data["label"] . $required . "</label>\n";
		

		// text inputs
		if (($data["type"] == "input") || ($data["type"] == "text")) {
			echo '						<input type="text" id="form-' . $field . '" name="' . $field . '" value="' . ($_POST[$field] ? $_POST[$field] : $data["value"]) . "\">\n";
		}


		// text areas
		if ($data["type"] == "textarea") {
			echo '						<textarea id="form-' . $field . '" name="' . $field . '" >' . ($_POST[$field] ? $_POST[$field] : $data["value"]) . "</textarea>\n";
		}


		// selects
		if (($data["type"] == "select") || ($data["type"] == "dropdown")) {
			echo '						<select id="form-' . $field . '" name="' . $field . '">\n';
			if (isset($data["options"])) {
				foreach ($data["options"] as $optionId => $optionValue) {
					if (isset($data["defaultValue"]) && ($data["defaultValue"] == $optionId)) {
						$selected = " selected=\"selected\"";
					} else {
						$selected = "";
					}
					echo "\t\t\t\t\t\t<option value=\"$optionId\"$selected>$optionValue</option>\n";
				}
			}
			echo '						</select>';
		}

		
		echo '					</div>' . "\n\n";
	}
?>