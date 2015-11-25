<?php
session_start();
require 'vendor/autoload.php';
//Creating a Backup of the RDS database
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);


$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB") or die("Error " . mysqli_error($link));

$dbname = 'itmo544SNDB';
$dbuser = 'SukanyaN';
$dbpass = 'SukanyaNDB';
$backupFile = $dbname . date("Y-m-d-H-i-s") . '.gz';
$command = "mysqldump --opt -h $endpoint -u $dbuser -p $dbpass $dbname | gzip > $backupFile";
system($command);

$link->close();
echo "Database Backup was successful";
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
