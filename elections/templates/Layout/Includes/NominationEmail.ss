
<p>Hello $Candidate.Member.FirstName --</p>

<p>You were just nominated as an Open Infrastructure Foundation Individual Board Member Candidate in the {$Election.Title}.</p>

<% loop Candidate %>
<% if HasAcceptedNomination %>
	<p>You have already accepted your nomination. You can edit your candidate information at: http://www.openstack.org/profile/election/ </p>

	<% if MoreThanTen %>
		<p>You have $countNominations nomination<% if countNominations = 1 %><% else %>s<% end_if %>. Congratulations! Since you now have enough nominations, you will be listed on the ballot.</p>
	<% else %>
		<p>You have $countNominations nomination<% if countNominations = 1 %><% else %>s<% end_if %>. You will need at least 10 nominations to be listed on the ballot. Encourage people to nominate you with your profile link: http://www.openstack.org/community/members/profile/{$MemberID}</p>
	<% end_if %>

<% else %>
	<p>You have not yet accepted the nomination. If you would like to accept your nomination and run in the OpenStack Individual Board Member election, you can do that here: http://www.openstack.org/profile/election/ </p>
	<p>Once you accept the nomination and complete the application, you'll need a total of 10 nominations from members before you appear on the ballot. You currently have $countNominations nomination<% if countNominations = 1 %><% else %>s<% end_if %>.</p>
	<p>If you wish to run in the {$Election.Title}, you must accept your nomination by {$Election.NominationAppDeadline.Month} {$Election.NominationAppDeadline.DayOfMonth}, {$Election.NominationAppDeadline.Year} and <a href="http://www.openstack.org/profile/election/">submit your candidate application</a>.</p>
<% end_if %>

<% end_loop %>

<p>To nominate other members, go to http://www.openstack.org/community/members/</p>

<p>Sincerely<br/>
The Open Infrastructure Foundation</p> 





