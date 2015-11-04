<?php
session_start();
require 'vendor/autoload.php';

#create RDSclient using the us-west-2 
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

#fetch the DB instance
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'itmo-544-sukanya']);


#get the end point to the instance
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    print "============\n". $endpoint . "================";
echo "endpoint is available";

$link = mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","itmo544SNDB");

?>
