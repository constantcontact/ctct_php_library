<?php
	session_start ();
	require_once 'ConstantContact.php';
	require_once 'config.php';
	
	
	// if you have a username stored in the Datastore (this is done inside config.php)
	if ($DatastoreUser) {
		$ConstantContact = new ConstantContact ( "oauth2", $apikey, $username, $accessToken );
		// This is to see if a post was made
		if($_POST)
		{	
			// Search Constant Contact for a given Contact
			$search = $ConstantContact->searchContactsByEmail($_POST["searchEmailAddress"]);
		}
		
		?>
	<H1>Search for a contact</H1>
	<form action="index.php" method="post">
		<input type="text" name="searchEmailAddress" maxlength="50"> <input
			type="submit" name="submit" value="Submit" />
	</form>
	
	<?php
	// False would represent the contact not existing
		if($search != false)
		{
			$details = $ConstantContact->getContactDetails($search[0]);
			echo $details->emailAddress . " Exists";
		}
		// Prints all details of that contact
		print_r($details);
	?>
	

	
	
	<?php 
		
	
	} else {
		
		?>
	<html>
	<body>
		<h1>OAuth 2.0 Example</h1>
	
		<h3>Click the link below to authorize your application to access your Constant Contact account resources through this PHP Wrapper Library, and obtain an access token that will be used to authenticate other API Requests made within the current session. The Wrapper Library will store the Authentication data in the PHP $_SESSION array by default.</h3>
		
		<h3>Developers should store the authentication data securely on their server and include code in application scripts to restore this to the $_SESSION array when the base application script is executed, to avoid the need to re-authenticate when applications scripts are executed at the start of each new session.</h3>
		
		<?php
		// you must encode your redirect URL and the link contains the variables from config.php
		$theRequest = urlencode ( $verificationURL );
		echo "<a href='https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize?response_type=code&client_id=" 
		. $apikey . "&redirect_uri=" 
		. $theRequest 
		. "'>Authorize here</a>";
		?>
	
	<?php
	
	}

?>
</body>
</html>