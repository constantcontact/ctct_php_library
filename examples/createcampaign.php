<?php
session_start ();
require_once('../ConstantContact.php');
require_once('../config.php');

# This example is intended to be used with the OAuth 2.0 example_verification.php script that is distributed in the root folder of the Constant Contact PHP Wrapper Library.
# The full url for the example_verification.php script should be used as the Redirect URI in the settings for your API Key, which can be accessed and updated here: http://community.constantcontact.com/t5/Documentation/API-Keys/ba-p/25015.
# The example_verification.php url must also be used for the $verificationURL value in config.php file for the wrapper libary (the API Key Redirect URI, and the script use to carry out the OAuth 2.0 flow must be the same, per OAuth 2.0 standards). 
# The code below checks to see if the wrapper library has a user in the datastore that has been authorized during the current session.
# If so, a form is loaded, and a request to create an email campaign is executed when the form is submitted.
# An object created from the XML in the API response is then echoed below the form, or an HTTP status code/error will be displayed.
# If the authorization is not current for the current session, an OAuth exception is throw, and a link to begin the OAuth 2.0 flow is provided.  
# As authorization/authentication credentials are stored in the session by default, navigating away from your server, of failing to include session_start(); at the top of any intermediary scripts executed after authorization will cause credentials to be lost, so we recommend altering the functions in the CTCTDataStore() class or otherwise adding code of your own to save the OAuth access token securely on your server by whatever means is best suited to your needs.

?>

<h2>Simple Add/Create Campaign Example</h2>

<?php
if($username){
    $ConstantContact = new ConstantContact('oauth2', $apiKey, $username, $accessToken);

	//gets first 50 contact lists, if more exist, a nextlink is returned in the $ContactLists object 
	$ContactLists = $ConstantContact->getLists();
	
	//gets verified account email addresses which can be used as from and rely-to addresses
	$VerifiedAddresses = $ConstantContact->getVerifiedAddresses();
	
	$testCampaign = new Campaign();

?>

<form name="addCampaign" action="" method="post">
    Campaign Name: <input type="text" size="50" name="campaign_name" /><br />
    Subject Line: <input type="text" size="50" name="subject_line" /><br />
	From Name: <input type="text" size="50" name="from_name" /><br />
	<h4>From Email Address</h4>
    <div style="overflow: auto; width: 400px;">
    <?php 
    foreach($VerifiedAddresses['addresses'] as $key=>$addr){
        echo '<input type="radio" name="from" value="'.$key.'"> '.$addr->email.'<br />';
    }
    ?>
    </div>
	<h4>Reply-to Email Address</h4>
    <div style="overflow: auto; width: 400px;">
    <?php 
    foreach($VerifiedAddresses['addresses'] as $key=>$addr){
        echo '<input type="radio" name="reply_to" value="'.$key.'"> '.$addr->email.'<br />';
    }
    ?>
    </div><br />
	Include View as Webpage link: <input type="checkbox" name="webpage_version" value="YES" /><br />
	View as Webpage Text: <input type="text" size="50" name="webpage_version_text" /> 
	link text: <input type="text" size="50" name="webpage_link_text" /><br />
	Include Permission Reminder: <input type="checkbox" name="permission_reminder" value="YES" /><br />
	Permission Reminder Text: <br /><textarea cols="80" rows="2" name="permission_reminder_text"></textarea><br />
	Include Forward to a Friend Link: <input type="checkbox" name="forward_link" value="YES" /><br />
	Forward Link Text: <br /><input size="50" name="forward_link_text" /><br />
	Include Subscribe link in Forwarded emails: <input type="checkbox" name="subscribe_link" value="YES" /><br />
	Subscribe Link Text: <br /><input size="50" name="subscribe_link_text" /><br />
	Email Type: <input type="radio" name="email_type" value="HTML" />HTML <input type="radio" name="email_type" value="XHTML" />XHTML<br />
    Email Content (HTML Code): <br /><textarea cols="80" rows="10" type="text" name="email_content"></textarea><br />
	Text Version Content: <br /><textarea cols="80" rows="10" type="text" name="text_version"></textarea><br />
	Style Sheet (XHTML ONLY): <br /><textarea cols="80" rows="10" type="text" name="style_sheet"></textarea><br />
	<h4>Contact Lists (first 50)</h4>
    <div style="border: 1px #000 solid; overflow: auto; width: 400px; height: 400px;">
    <?php
    foreach($ContactLists['lists'] as $list){
        echo '<input type="checkbox" name="lists[]" value="'.$list->id.'"> '.$list->name.'<br />';
    }
    ?>
    </div>
    <input type="submit" name="submit" value="Create Campaign" /><br />
</form>

<?php

    if(isset($_POST['campaign_name'])){
        $Campaign = new Campaign();
        $Campaign->name = $_POST['campaign_name'];
        $Campaign->subject = $_POST['subject_line'];
	$Campaign->fromName = $_POST['from_name'];
	$Campaign->vawp = $_POST['webpage_version'];
	$Campaign->vawpText = $_POST['webpage_version_text'];
	$Campaign->vawpLinkText = $_POST['webpage_link_text'];
	$Campaign->permissionReminder = $_POST['permission_reminder'];
	$Campaign->permissionReminderText = $_POST['permission_reminder_text'];
	$Campaign->incForwardEmail = $_POST['forward_link'];
	$Campaign->forwardEmailLinkText = $_POST['forward_link_text'];
	$Campaign->incSubscribeLink = $_POST['subscribe_link'];
	$Campaign->subscribeLinkText = $_POST['subscribe_link_text'];
        $Campaign->emailContentType = $_POST['email_type'];
        $Campaign->emailContent = $_POST['email_content'];
        $Campaign->textVersionContent = $_POST['text_version'];
	$Campaign->styleSheet = $_POST['style_sheet'];
	$Campaign->lists = $_POST['lists'];
		
		// add campaign to Constant Contact account
        $NewCampaign = $ConstantContact->addCampaign($Campaign, $VerifiedAddresses['addresses'][$_POST['from']], $VerifiedAddresses['addresses'][$_POST['reply_to']]);
        if($NewCampaign){
            echo "Campaign Added. This is your newly created campaign's raw information<br /><pre>";
            print_r($NewCampaign);
            echo "</pre>";

        }
    }
} 

else {echo ' Click <a href="../example_verification.php?apiKey='.$apiKey.'&secret='.$consumerSecret.'&return='.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">here</a> to authorize';}
?>