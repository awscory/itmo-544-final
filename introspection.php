<?php
session_start();
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

$table_name = "items";
   $backup_file  = '/var/backups/items'.date("Y-m-d-H-i-s").'.sql';
   $sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";
   
   mysql_select_db('itmo544SNDB');
   $retval = mysql_query( $sql, $conn );
   
   if(! $retval )
   {
      die('Could not take data backup: ' . mysql_error());
   }
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
