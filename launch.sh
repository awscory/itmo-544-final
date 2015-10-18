#!/bin/bash
declare -a InstArr
declare -a InstWaitArr

#Step 1 : create instances and run

aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --iam-instance-profile Name=$7 --associate-public-ip-address --user-data file://EnvSetUp/install-env.sh  

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

aws ec2 wait --region us-west-2b instance-running --instance-ids ${InstWaitArr[@]}

#Step 2 : Decribe instances 

mapfile -t InstArr < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=16 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 

	echo "the output is ${InstArr[@]}" 


#Step 3: Create load Balancer
aws elb create-load-balancer --load-balancer-name $8 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5

echo "load balancer name is $8"

#Step 3a: Configure health check policy for load balancer

aws elb configure-health-check --load-balancer-name $8 --health-check Target=HTTP:80/png,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3






