<?php
session_start();
require 'vendor/autoload.php';
//create topic
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);

echo "No error as of now";

print_r($result);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    print "============\n". $endpoint . "================";
echo "endpoint is available";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($link));

print_r($link);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$sql1 = "SELECT topicarn,topicname FROM topic ";
$result = mysqli_query($link, $sql1);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
	echo "topicarn:".$row["topicarn"]."<br>";
	if ($row["topicname"] == 'Mp2-Topic1')
	{
	echo "topic already exist";
	}
	else
	{
	$sns= new Aws\Sns\SnsClient([
	    'version' => 'latest',
	    'region'  => 'us-east-1'
	]);
	$topicName = 'Mp2-Topic1';
	$result = $sns->createTopic([
	    'Name' => $topicName, // REQUIRED
	]);

	$topicarn = $result['TopicArn'];
	echo "topic arn value is ----------- $topicarn";
	//set topic attributes
	$result = $sns->setTopicAttributes([
	    'AttributeName' => 'DisplayName', // REQUIRED
	    'AttributeValue' => 'TestMP2',
	    'TopicArn' => $topicarn, // REQUIRED
	]);
	    $sql_insert = "INSERT INTO topic (topicarn,topicname) VALUES (?,?)";
	if (!($stmt = $link->prepare($sql_insert))) {
	    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
	}
	else
	{
	echo "statement topic was success";
	}

	$stmt->bind_param("ss",$topicarn,$topicName);
	if (!$stmt->execute()) {
	    print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	printf("%d Row inserted.\n", $stmt->affected_rows);

	$stmt->close();
	}
    }
} 
else {
	$sns= new Aws\Sns\SnsClient([
	    'version' => 'latest',
	    'region'  => 'us-east-1'
	]);
	$topicName = 'Mp2-Topic1';
	$result = $sns->createTopic([
	    'Name' => $topicName, // REQUIRED
	]);

	$topicarn = $result['TopicArn'];
	echo "topic arn value is ----------- $topicarn";
	//set topic attributes
	$result = $sns->setTopicAttributes([
	    'AttributeName' => 'DisplayName', // REQUIRED
	    'AttributeValue' => 'TestMP2',
	    'TopicArn' => $topicarn, // REQUIRED
	]);
	    $sql_insert = "INSERT INTO topic (topicarn,topicname) VALUES (?,?)";
	if (!($stmt = $link->prepare($sql_insert))) {
	    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
	}
	else
	{
	echo "statement topic was success";
	}

	$stmt->bind_param("ss",$topicarn,$topicName);
	if (!$stmt->execute()) {
	    print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	printf("%d Row inserted.\n", $stmt->affected_rows);

	$stmt->close();
}

$link->close();



?>
<!DOCTYPE html>
<meta charset="UTF-8"> 
<html>
<body>
<form enctype="multipart/form-data" action="submit.php" method="post">
User Name: <input type="text" name="username" value="sukanya"><br>
E-mail: <input type="text" name="email" value="****@***.iit.edu"><br>
Phone : <input type="text" id="phone" name="phone" value="312-000-0000"><br>
<input type="hidden" name="MAX_FILE_SIZE" value="3000000"><br>
Your File : <input type="file" name="userfile">
<input type="submit" value="Upload">
</form><br><br>
<form enctype="multipart/form-data" action="subscribe.php" method="post">
Phone to Subscribe : <input type="text" name="phoneNo"/><br>
Would you like to subscribe to receive message on Upload ?  <input type="submit" value="Subscribe">
</form>

</body>
</html> 




    
