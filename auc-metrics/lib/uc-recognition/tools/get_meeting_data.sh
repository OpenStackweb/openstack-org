#!/bin/bash
#
# Downloads the TXT summary file from the meetbot records
# at OpenStack for specific meetings

MEETINGS="operators_ops_tools_monitoring ops_tags _operator_tags"
MEETINGS="$MEETINGS large_deployment_team large_deployments_team"
MEETINGS="$MEETINGS large_deployments_team_monthly_meeting"
MEETINGS="$MEETINGS log_wg openstack_operators ops_meetups_team ops_meetup_team"
MEETINGS="$MEETINGS product_team product_work_group product_working_group"
MEETINGS="$MEETINGS scientific_wg telcowg uc user_committee wos_mentoring"
MEETINGS="$MEETINGS massively_distributed_clouds operators_telco_nfv"

for meeting in $MEETINGS
do
  wget --no-parent --no-clobber --recursive --accept "*.txt" --reject="*.log.txt" http://eavesdrop.openstack.org/meetings/$meeting/ -P $1
done
