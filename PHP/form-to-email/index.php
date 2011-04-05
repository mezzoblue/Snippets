<?php
/*

	---------------------------------------------------------------------------------------
	form-to-email

	Generic reusable form that sends an email containing submitted form fields
	Originally designed for a Contact Form, can be re-purposed for any general type of form
	---------------------------------------------------------------------------------------
	
*/


	//
	// up front form configuration variables
	//
	// information about the form owner
	$formOwner = array(
		"name" => "Name of this site",
		"email" => "user@url-of-this-site.com",
		"url" => "url-of-this-site.com",
	);
	// generated email defaults
	$emailDefaults = array(
		"toemail" => $formOwner["email"],
		"fromemail" => "contact-form@" . $formOwner["url"],
		"subject" => "From " . $formOwner["name"] . " contact form",
	);

		

	// fields - simple contact form
	// (can add or remove as many as needed below)
	// currently only supports:
	//		input type=text
	//		textarea
	// shouldn't be hard to repurpose to include any type of form element
	// just modify includes/fields.php
	$formFields = array(
		"name" => array(
			"type" => "input",
			"label" => "Your name:",
			"class" => "",
			"defaultValue" => "",
			"required" => true,
		),
		"email" => array(
			"type" => "input",
			"label" => "Your email address:",
			"class" => "",
			"defaultValue" => "",
			"required" => true,
		),
		"subject" => array(
			"type" => "input",
			"label" => "Subject:",
			"class" => "",
			"defaultValue" => $emailDefaults["subject"],
			"required" => false,
		),
		"message" => array(
			"type" => "textarea",
			"label" => "Message:",
			"class" => "",
			"defaultValue" => "",
			"required" => true,
		),
	);




	// are we getting POST data?
	if ($_POST["sendmail"]) {

		// response handler
		include("includes/handler.php");
	}
	

	//
	// either way, display the page
	//

?>

<?php
	if ($messageSent) {
?>

		<h2>Success Message!</h2>

<?
	} else {

		if ($errors) {
?>		

		<h2>Error Message!</h2>

<?php
		} else {
?>		

		<h2>Here's a Form</h2>

<?php
		}
?>		
		<p class="form-required"><span class="required">*</span> - required fields</p>

			<form action="index.php" method="post">
				<fieldset>
					<div>
						<input type="hidden" name="sendmail" value="true">
					</div>
<?php
	include("includes/fields.php");
?>
					<button class="button" type="submit">Send Message</button
				</fieldset>
			</form>

<?php
	}
?>