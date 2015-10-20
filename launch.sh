#!/bin/bash
declare -a InstArr
declare -a InstWaitArr

#Step 1 : create instances and run 

aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6  --associate-public-ip-address --user-data file://EnvSetUp/install-env.sh  

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
aws elb create-load-balancer --load-balancer-name $7 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5

echo "load balancer name is $7"

#Step 3a: Configure health check policy for load balancer

aws elb configure-health-check --load-balancer-name $7 --health-check Target=HTTP:80/index.php,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

#step 3b: Register Instances to load balancer

aws elb register-instances-with-load-balancer --load-balancer-name $7 --instances ${InstArr[@]} 

#Step 3c: create cookie-stickiness policy 

aws elb create-lb-cookie-stickiness-policy --load-balancer-name $7 --policy-name my-duration-cookie-policy --cookie-expiration-period 60


#Step Create Auto scaling group

aws autoscaling create-launch-configuration --launch-configuration-name itmo544-launch-config --image-id $1 --key-name $4  --security-groups $5 --instance-type $3 --user-data file://EnvSetUp/install-env.sh  --iam-instance-profile SukanyaNagarajanDeveloper

aws autoscaling create-auto-scaling-group --auto-scaling-group-name itmo-544-extended-auto-scaling-group-2 --launch-configuration-name itmo544-launch-config --load-balancer-names $2  --health-check-type ELB --min-size 1 --max-size 3 --desired-capacity 2 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier $6 


#Step Creating RDS db-subnet-group

aws rds create-db-subnet-group --db-subnet-group-name dbsgnameSN --db-subnet-group-description DBSubnet-groupname-sukanyaN --subnet-ids ---

echo "ALL DONE"





