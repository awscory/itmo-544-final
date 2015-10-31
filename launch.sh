#!/bin/bash

./itmo-544-final/cleanup.sh

declare -a InstArr
declare -a InstWaitArr
declare -a VPCSubnetArr

#Step 1 : create instances and run 
echo "creating instances"
aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --iam-instance-profile Name=$8 --associate-public-ip-address --user-data file://EnvSetUp/install-env.sh  

for var1 in {0..60}
do
echo -ne "."
	sleep 1
done

#Step 1a: Check if instances are running.
mapfile -t InstWaitArr < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=0 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 

	echo "the output of wait array is ${InstWaitArr[@]}" 

if [ ${#InstWaitArr[@]} -eq 0 ];then
for var1 in {0..60}
do
echo -ne "."
	sleep 1
done
fi
echo "waiting for instances to be available"
aws ec2 wait --region us-west-2b instance-running --instance-ids ${InstWaitArr[@]}

#Step 2 : Decribe instances 
mapfile -t InstArr < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=16 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 

	echo "the output is ${InstArr[@]}" 
#create VPC
mapfile -t VpcId < <(aws ec2 create-vpc --cidr-block 10.0.0.0/16 --output table |grep VpcId |sed "s/|//g" | tr -d ' ' | sed "s/VpcId//g")

aws ec2 modify-vpc-attribute --vpc-id $VpcId --enable-dns-support "{\"Value\":true}"

aws ec2 modify-vpc-attribute --vpc-id $VpcId --enable-dns-hostnames "{\"Value\":true}"

echo "VPC created $VpcId"

#create subnet

mapfile -t SubnetId < <(aws ec2 create-subnet --vpc-id $VpcId --cidr-block 10.0.0.0/24 --output table |grep SubnetId |sed "s/|//g" | tr -d ' ' | sed "s/SubnetId//g")

echo "subnet created $SubnetId"

#create Internet gateway
mapfile -t IntGate < <(aws ec2 create-internet-gateway --output table |grep InternetGatewayId |sed "s/|//g" | tr -d ' ' | sed "s/InternetGatewayId//g")

echo "Internet gateway created $IntGate"

#Attach gateway to vpc
aws ec2 attach-internet-gateway --internet-gateway-id $IntGate --vpc-id $VpcId

# describe security group id for this vpc
mapfile -t SgId < <(aws ec2 describe-security-groups --filter "Name=vpc-id,Values=$VpcId" --output table |grep GroupId |sed "s/|//g" | tr -d ' ' | sed "s/GroupId//g")

echo "Security group created $SgId"

#Changing the in-bound rules of security group
#for SSH
aws ec2 authorize-security-group-ingress --group-id $SgId --protocol tcp --port 22 --cidr 0.0.0.0/0 
#For HTTP
aws ec2 authorize-security-group-ingress --group-id $SgId --protocol tcp --port 80 --cidr 0.0.0.0/0
#For MYSQL
aws ec2 authorize-security-group-ingress --group-id $SgId --protocol tcp --port 3306 --cidr 0.0.0.0/0

#Step 3: Create load Balancer
echo "creating load balancer"
aws elb create-load-balancer --load-balancer-name $7 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5

echo "load balancer name is $7"

#Step 3a: Configure health check policy for load balancer
echo "configuring health check for load balancer"
aws elb configure-health-check --load-balancer-name $7 --health-check Target=HTTP:80/index.php,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

#step 3b: Register Instances to load balancer
echo "registering instances to the load balancer"
aws elb register-instances-with-load-balancer --load-balancer-name $7 --instances ${InstArr[@]} 

#Step 3c: create cookie-stickiness policy 
echo "creating load balancer cookie stickiness policy"
aws elb create-lb-cookie-stickiness-policy --load-balancer-name $7 --policy-name my-duration-cookie-policy --cookie-expiration-period 60


#Step Create Auto scaling group
echo "creating launch configuration"
#aws autoscaling create-launch-configuration --launch-configuration-name itmo544-launch-config --image-id $1 --key-name $4  --security-groups $5 --instance-type $3 --user-data file://EnvSetUp/install-env.sh --iam-instance-profile $8

echo "creating auto scaling group"
#aws autoscaling create-auto-scaling-group --auto-scaling-group-name itmo-544-Sukanya-auto-scaling-group-2 --launch-configuration-name itmo544-launch-config --load-balancer-names $7  --health-check-type ELB --min-size 1 --max-size 3 --desired-capacity 2 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier $6 

#Step Creating RDS db-subnet-group
echo "creating DB-subnet-group"

mapfile -t VPCSubnetArr< <(aws ec2 describe-subnets --filters "Name=vpc-id,Values=vpc-5fbfe03a" --output table |grep SubnetId | sed "s/|//g" | tr -d ' ' | sed "s/SubnetId//g")

aws rds create-db-subnet-group --db-subnet-group-name dbsgnameSN --db-subnet-group-description DBSubnet-groupname-sukanyaN --subnet-ids ${VPCSubnetArr[@]}


#Step Create DB instance
echo "Creating DB instance"
aws rds create-db-instance --db-name itmo544SukanyaMySql --db-instance-identifier itmo-544-SN-db --allocated-storage 20 --db-instance-class db.t1.micro --engine MYSQL --master-username SukanyaN --master-user-password SukanyaNDB --vpc-security-group-ids $5 --availability-zone us-west-2b  --db-subnet-group-name dbsgnameSN

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


php ./itmo-544-final/setup.php

echo "ALL DONE"





