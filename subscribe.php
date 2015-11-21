<?php
session_start();
require 'vendor/autoload.php';
if(!empty($_POST)){
echo $_POST['phoneNo'];
$phone = $_POST['phoneNo'];
}
else
{
echo "Please enter Phone number in the format 1-200-000-0000";
}
$sns= new Aws\Sns\SnsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
//subscribe to the topic using the sms protocol
$result = $sns->subscribe([
    'Endpoint' => $phone,
    'Protocol' => 'sms', // REQUIRED
    'TopicArn' => $topicarn, // REQUIRED
]);

echo "You will receive a Message to the mobile number provided.";
echo "Reply back to be subscribed to receive message ";

$subarn= $result['SubscriptionArn'];
echo "subscription arn is $subarn";

?>
<!DOCTYPE html>
 <meta charset="UTF-8"> 
<html>
<body>
<br>
<br>
<a href="index.php"> Main page</a>
</body>
</html> 



    
