#!/bin/bash

#Step 1 : create instances and run

aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --associate-public-ip-address --user-data file://EnvSetUp/install-env.sh --debug
for i in 0..150
do 
	echo -ne '.'
	sleep 1
done

#Step 2 : Decribe instances 
aws ec2 describe-instanes  > ./itmo-544-final-launch-log.txt

#Step 3: Create load Balancer
aws elb create-load-balancer --load-balancer-name $7 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-group-ids $5





