#!/bin/bash
#
# Downloads the TXT summary file from the meetbot records
# at OpenStack for specific meetings

MEETINGS="operators_ops_tools_monitoring ops_tags _operator_tags large_deployments_team_august_2015_meeting"
MEETINGS="$MEETINGS large_deployment_team large_deployments_team large_deployment_team_january_2015_meeting"
MEETINGS="$MEETINGS large_deployments_team_december_2015_meeting large_deployments_team_february_2016_meeting"
MEETINGS="$MEETINGS large_deployments_team_january_2016_meeting large_deployments_team_monthly_meeting"
MEETINGS="$MEETINGS large_deployments_team_october_2015_meeting large_deployments_team_september_2015_meeting"
MEETINGS="$MEETINGS log_wg openstack_operators"
MEETINGS="$MEETINGS product_team product_work_group product_working_group"
MEETINGS="$MEETINGS telcowg uc user_committee"

for meeting in $MEETINGS
do
  wget --no-parent --recursive --accept "*.txt" --reject="*.log.txt" http://eavesdrop.openstack.org/meetings/$meeting/
done
