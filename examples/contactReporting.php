<h2>Contact Reporting Data Example</h2>
<?php
session_start();
include_once('../ConstantContact.php');
$username = 'USERNAME';
$apiKey = 'APIKEY';
$consumerSecret = 'CONSUMERSECRET';

$Datastore = new CTCTDataStore();
$DatastoreUser = $Datastore->lookupUser($username);

if($DatastoreUser){
	$emailAddress = (isset($_GET['email_address'])) ? $_GET['email_address'] : '';
    $ConstantContact = new ConstantContact('oauth', $apiKey, $DatastoreUser['username'], $consumerSecret);
    // Fail if the email does not exist
    ?>
        <html>
        <head><title>Contact Email History</title></head>
        <body>
        <!--- Basic form for contact event search submission --->
        <form method="get" action="">
            Email Address: <input type="text" value="<?php echo $emailAddress; ?>" name="email_address"><br />
            Sends <input type="radio" name="event_type" value="sends"><br />
            Clicks <input type="radio" name="event_type" value="clicks"><br />
            Opens <input type="radio" name="event_type" value="opens"><br />
            Forwards <input type="radio" name="event_type" value="forwards"><br />
            OptOuts <input type="radio" name="event_type" value="optouts"><br />
            Bounces <input type="radio" name="event_type" value="bounces"><br />
            <br /><input type="submit" value="submit" name="submit">
        </form>
        </body>
        </html>
    <?php
    if($_GET['email_address']){
        $contactSearch = $ConstantContact->searchContactsByEmail($_GET['email_address']);
        if(!$contactSearch){exit('Email not found');}

            // Find the appropriate event type for the verified contact
            $page = (isset($_GET['nextLink'])) ? $_GET['nextLink'] : null;
            switch ($_GET['event_type']) {
                case 'sends':
                    $event = $ConstantContact->getContactSends($contactSearch[0], $page);
                    break;
                case 'clicks':
                    $event = $ConstantContact->getContactClicks($contactSearch[0], $page);
                    break;
                case 'opens':
                    $event = $ConstantContact->getContactOpens($contactSearch[0], $page);
                    break;
                case 'forwards':
                    $event = $ConstantContact->getContactForwards($contactSearch[0], $page);
                    break;
                case 'optouts':
                    $event = $ConstantContact->getContactOptOuts($contactSearch[0], $page);
                    break;
                case 'bounces':
                    $event = $ConstantContact->getContactBounces($contactSearch[0], $page);
                    break;
            }

            // Display the results of the search
            if(count($event)>0){
                echo "<h3>There are ".count($event['events'])." ".$_GET['event_type']." for ".$_GET['email_address']."</h3>";
                if($event['nextLink']){
                    echo '<a href="contactReporting.php?nextLink='.$event['nextLink'].'&email_address='.$_GET['email_address'].'&event_type='.$_GET['event_type'].'"> Next Page</a>';
                }
                echo "<table>";
                foreach ($event['events'] as $e){
                    foreach ($e as $key=>$value){
                        echo "<tr><td style='font-weight: bold;'>".$key."</td><td>".$value."</td></tr>";
                    }
                    echo "<tr><td><br /></td><td></td></tr>";
                }
                echo "</table>";
            }
    }
} else {echo ' Click <a href="example_verification.php?apiKey='.$apiKey.'&secret='.$consumerSecret.'&return='.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">here</a> to authorize';}