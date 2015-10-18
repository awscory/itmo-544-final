#!/bin/bash

#Step 1 : create instances and run

aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --associate-public-ip-address --user-data file://EnvSetUp/install-env.sh  

for i in 0..150
do 
	echo -ne '.'
	sleep 1
done

#Step 2 : Decribe instances 
declare -a ARRAY
ARRAY=(aws ec2 describe-instances --filter Name=instance-state-code,Values=16 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 

read -a ARRAY
for i in "${ARRAY[@]}" 
do 
	echo $i 
done 

#Step 3: Create load Balancer
aws elb create-load-balancer --load-balancer-name $7 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5







