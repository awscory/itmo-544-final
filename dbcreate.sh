#!/bin/bash

#Step Create DB instance
echo "Creating DB instance"
mapfile -t result < <(aws rds describe-db-instances --db-instance-identifier itmo-544-SN-db --output table | grep Address | sed "s/|//g" | tr -d ' ' | sed "s/Address//g")

# wait for the DB instance to be available
echo "waiting for the Db instance to be available"
#aws rds wait db-instance-available --db-instance-identifier itmo-544-SN-db 
 
echo "DB instance wait over. It should be Available "
#Create Read replica of the Db instance in the same region
echo "creating read replica"
#aws rds create-db-instance-read-replica --db-instance-identifier itmo-544-SN-dbreplica --source-db-instance-identifier itmo-544-SN-db --db-instance-cass db.t1.micro --availability-zone us-west-2a

# wait for read replica to be available
echo "waiting for read replica to be available"
#aws rds wait db-instance-available --db-instance-identifier itmo-544-SN-dbreplica

echo "============\n". $result . "================";

echo "begin database";
link= mysqli_connect($result,"SukanyaN","SukanyaNDB","items") or die("Error ". mysqli_error($link));

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

