# CloudHackathon
VZCloudDay



AutomaticAMIPolling
Job1:
######################################################################


#!/bin/sh
if [ -s user.properties ]; then
  rm -f user.properties
fi

Stackname=MyStack$BUILD_NUMBER
aws s3 cp s3://templatebucket99/test.py test.py

aws s3 cp s3://templatebucket99/latestami latestami
OLDAMI=`cat latestami`
NEWAMIID=`python test.py -o $OS`

#if [ "$OLDAMI" == "$NEWAMIID" ] ; then
 # echo "AMI IDs are not updated so build is not triggering"
  #exit 1
#else
 echo "New AMI detected for the Os Type $OS and new ami id is $NEWAMIID. Notifying the users." | mail -s 

"New AMI detected for the $OS" pradeep.thadaka@gmail.com krishna.mangineni@gmail.com 

venkatesh4ganta@gmail.com
 echo "Detected a new ami so triggering the whole pipeline"
 echo -e "$NEWAMIID" > latestami
 aws s3 cp latestami s3://templatebucket99/latestami
#fi




#AMIID=`aws ec2 describe-images --owners 950547851261 --filters "Name=tag:OsType,Values=$OS" | grep -e 

\"ImageId\" -e \"CreationDate\" | awk '{print $2}' | head -n1 | sed 's/"//g' | sed 's/,//g'`


echo "AMIID=$NEWAMIID" >> user.properties
echo "OS=$OS" >> user.properties
echo "Stackname=EC2Stack${BUILD_ID}" >> user.properties
echo "ASGSTACKNAME=$ASGStackName" >> user.properties





######################################################################
EC2APPDeploy:
Job2:
######################################################################


PublicIP=`aws cloudformation describe-stacks --stack-name $Stackname --region us-east-1 | grep -A 1 

PublicIP | grep -v PublicIP | awk -F: '{print $2}' | tr -d '\"'`
echo "PublicIP=$PublicIP" >> user.properties




######################################################################
GenerateAMI:
Job3:
######################################################################

#!/bin/bash

echo "stack name from the previous job is :${Stackname}"

export InstanceID=`aws ec2 describe-instances | grep -B 50 -w "${Stackname}" | grep -w "InstanceId" | cut 

-d ":" -f2 | awk -F'"' '{print $2}'`

export AMI_ID=`aws ec2 create-image --instance-id "${InstanceID}" --name "${Stackname}" --description "An 

AMI for my server ${Stackname}" |  grep ami | cut -d ":" -f2 | awk -F'"' '{print $2}'`
echo "Generated AMI id is $AMI_ID for the instance id $InstanceID"

State=`aws ec2 describe-images --region us-east-1 --image-ids "${AMI_ID}" | grep -i state | cut -d ":" -f2 

| awk -F'"' '{print $2}'`
echo "Image status is not in available state waiting for it to be in available mode"
until [ "${State}" == "available" ]; do
sleep 5
State=`aws ec2 describe-images --region us-east-1 --image-ids "${AMI_ID}" | grep -i state | cut -d ":" -f2 

| awk -F'"' '{print $2}'`
done
echo "`date` ${AMI_ID} AMI has been created successfully and it is in available state"

echo "AMI_ID=$AMI_ID" >> user.properties





#############################################################################################
ASGBLueGreenDeploy:
Job4:
#############################################################################################

#!/bin/bash

# Parameters required to trigger the AWS CloudFormation Stack
export ASGStackName="$ASGSTACKNAME"
export InstanceType="m1.small"
export NEWAMI="$AMI_ID"
export OperatorEMail="venkatesh4ganta@gmail.com"
export KeyName="veganta"
export Templatefile="file://ASGTemplate.json"

## Command to sync the latest cloudformation template

aws s3 cp s3://templatebucket99/ASGTemplate.json .

### AWS CLI Command to trigger the CloudFormation and run the Instances undera Dynamic Load Balancer

aws cloudformation describe-stacks --stack-name ${ASGStackName}

if [ $? -eq 0 ]; then

Updatestate=aws cloudformation update-stack --stack-name "${ASGStackName}" --template-body "${Templatefile}" --parameters ParameterKey=InstanceType,ParameterValue="${InstanceType}" ParameterKey=NEWAMI,ParameterValue="${NEWAMI}" ParameterKey=OperatorEMail,ParameterValue="${OperatorEMail}" ParameterKey=KeyName,ParameterValue="${KeyName}"
if [ $? -eq 0 ]; then
echo " Cloud Formation executed Succesfully"
else
echo " Failed to execute the CloudFormation stack"
exit 1
fi

else
aws cloudformation create-stack --stack-name "${ASGStackName}" --template-body "${Templatefile}" --parameters ParameterKey=InstanceType,ParameterValue="${InstanceType}" ParameterKey=NEWAMI,ParameterValue="${NEWAMI}" ParameterKey=OperatorEMail,ParameterValue="${OperatorEMail}" ParameterKey=KeyName,ParameterValue="${KeyName}"

if [ $? -eq 0 ]; then
echo " Cloud Formation executed Succesfully"
else
echo " Failed to execute the CloudFormation stack"
exit 1
fi
fi
