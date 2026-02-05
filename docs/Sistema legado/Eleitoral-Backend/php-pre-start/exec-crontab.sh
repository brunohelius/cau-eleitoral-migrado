#!/bin/bash
if [ -f ./deploy/openshift/crontab.sh ]; then
	echo "--> Executing deploy/openshift/crontab.sh"
	. ./deploy/openshift/crontab.sh &
fi
