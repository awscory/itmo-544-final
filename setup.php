<!DOCTYPE html>
<html>
<body>
<?php
//$rds = new Aws\Rds\RdsClient([
  //  'version' => 'latest',
    //'region'  => 'us-east-1'
/*]);$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'itmo-544-SN-db',
]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";*/

$endpoint = $argv[1];

echo "Try Connecting the DB"; 
$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB",3306) or die("Error " . mysqli_error()); 

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

print($sql);


if ($link->query($sql) === TRUE) {
    echo "Table items created successfully";
} else {
    echo "Error creating table: " . $link->error;
}

$link->close();


echo "done";

?>

</body>
</html>
