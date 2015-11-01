#!/bin/bash

#Step Create DB instance
echo "Creating DB instance"
result= aws rds create-db-instance --db-name itmo544SukanyaMySql --db-instance-identifier itmo-544-SN-db --allocated-storage 20 --db-instance-class db.t1.micro --engine MYSQL --master-username SukanyaN --master-user-password SukanyaNDB --vpc-security-group-ids $1 --availability-zone us-west-2b  --db-subnet-group-name dbsgnameSN

# wait for the DB instance to be available
echo "waiting for the Db instance to be available"
aws rds wait db-instance-available --db-instance-identifier itmo-544-SN-db 
 
echo "DB instance wait over. It should be Available "
#Create Read replica of the Db instance in the same region
echo "creating read replica"
#aws rds create-db-instance-read-replica --db-instance-identifier itmo-544-SN-dbreplica --source-db-instance-identifier itmo-544-SN-db --db-instance-cass db.t1.micro --availability-zone us-west-2a

# wait for read replica to be available
echo "waiting for read replica to be available"
#aws rds wait db-instance-available --db-instance-identifier itmo-544-SN-dbreplica

echo "result here $result"

#get DB endpoint
endpoint= $result ['DBInstances']['Endpoint']['Address']

echo "============\n". $endpoint . "================";

echo "begin database";
link= mysqli_connect($endpoint,"SukanyaN","SukanyaNDB","items") or die("Error ". mysqli_error($link));

echo "LInk is $link" 

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

echo " table done";


#sudo apt-get install php5-cli

#php ./itmo-544-final/setup.php

echo "ALL DONE"

