#!/bin/python

import os
import subprocess
import re
import argparse

parser = argparse.ArgumentParser()
parser.add_argument('-o', '--OsType', action='store', dest='OsType', help='Enter OsType')

par = parser.parse_args()
rostype = par.OsType

result = []
#result = subprocess.check_output("aws ec2 describe-images --owners 950547851261 --filters \"Name=tag:OsType,Values=%s\" | grep -e \"ImageId\" -e \"CreationDate\" | awk '{print $2}'", shell=True)

cmd = str("aws ec2 describe-images --owners 950547851261 --filters \"Name=tag:OsType,Values=%s\" | grep -e \"ImageId\" -e \"CreationDate\" | awk '{print $2}'" %(rostype))

result = subprocess.check_output(cmd, shell=True)


lista = result.split(",")

listami = lista[0:][::2]
listdate = lista[1:][::2]

#print result


latestdate = listdate.index(max(listdate))
print listami[latestdate].replace('"', '')