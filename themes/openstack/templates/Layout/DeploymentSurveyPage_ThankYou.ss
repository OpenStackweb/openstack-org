<h1>OpenStack User Survey</h1>

<div class="span-15">
<ul class="survey-steps">
	<li><a href="{$Link}OrgInfo">About You</a></li>
	<li><a href="{$Link}AppDevSurvey">Your OpenStack Usage</a></li>
	<li><a href="{$Link}Deployments">Your OpenStack Deployments</a></li>
	<li><a href="{$Link}ThankYou" class="current">Thank You!</a></li>
</ul>
</div>
<div class="span-9 last current-user">
	Logged in as <strong>$CurrentMember.FirstName</strong>. &nbsp; <a href="{$link}logout" class="roundedButton">Log Out</a>
</div>

<div class="span-24 last">
<h2>Thank You!</h2>

<p>We aggregate data from the survey every six months before the Summit. A video
presentation by the User Committee and slides from November 2013 are available to
<a href="http://www.slideshare.net/openstack/openstack-user-survey-october-2013">view now</a>.</p>

<p>If you'd like to get involved in working with other OpenStack users,
find out more about the <a href="/foundation/user-committee">User Committee</a>.</p>

<% if CurrentMember.Password %>
<% else %>
	<h2>Create A Password For Return Visits</h2>
	<p>If you'd like to be able to return and update this information in the future, you
	may choose a password below.</p>
	$SavePasswordForm
<% end_if %>


</div>

