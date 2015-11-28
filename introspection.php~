<?php
require 'vendor/autoload.php';
//Creating a Backup of the RDS database
//http://www.tutorialspoint.com/php/perform_mysql_backup_php.htm
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

mkdir("/tmp/Backup");

$Bkpspath = '/tmp/Backup/';
$bname = uniqid("DBBackUp", false);
$append = $bname . '.' . sql;
$Path = $Bkpspath . $append;
$sql="mysqldump --user=$dbuser --password=$dbpass --host=$endpoint $dbname > $Path";
exec($sql);
$bucketname = uniqid("dbbackup", false);

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucketname,
]);
# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucketname,
   'Key' => $append,
'SourceFile' => $Path,
]);
$objectrule = $s3->putBucketLifecycleConfiguration([
    'Bucket' => $bucketname,
    'LifecycleConfiguration' => [
        'Rules' => [ 
            [
                'Expiration' => [
                    'Days' => 1,
                ],
                              
                'Prefix' => ' ',
                'Status' => 'Enabled',
                
            ],
            
        ],
    ],
]);
mysql_close($link);
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
