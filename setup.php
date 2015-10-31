<!DOCTYPE html>
<html>
<body>
<?php
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'itmo-544-SN-db',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";

echo "Try Connecting the DB"; 
$link = mysqli_connect("itmo-544-SN-db","SukanyaN","SukanyaNDB","items") or die("Error " . mysqli_error($link)); 

echo "Here is the result: " . $link;


$sql = "CREATE TABLE items 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
UName VARCHAR(20),
Email Varchar(20),
Phone Varchar(20),
RawS3Url  Varchar(256),
FinalS3Url  Varchar(256),
JpgFileName    Varchar(256),
Status    TinyInt(3),
Issubscribed TinyInt(3),
CreationTime  Timestamp
)";

$con->query($sql);

echo "done";

?>

</body>
</html>
