<html>
<body>

<p>$Recipient.FirstName $Recipient.Surname --</p>

<p>Your <% if MultipleTalks %>presentations are<% else %>presentation is<% end_if %> now available to the OpenStack community for online voting! Please encourage your friends and colleagues in the OpenStack community to vote for your <% if MultipleTalks %>presentations<% else %>presentation<% end_if %>.</p>

<% if AdminTalks %>

<ul>
	<% loop AdminTalks %>
		<li>$PresentationTitle <% if Speakers %> ( <% if SpeakerCount=1 %>Speaker:<% else %>Speakers:<% end_if %> <% loop Speakers %><% if Last %>$FirstName $Surname<% else %>{$FirstName} {$Surname}, <% end_if %> <% end_loop %>) <% end_if %> <br/>
			<a href="https://www.openstack.org/vote-vancouver/Presentation/{$URLSegment}">https://www.openstack.org/vote-vancouver/Presentation/{$URLSegment}</a>
	<% end_loop %>
</ul>

<% end_if %>

<% if SpeakerTalks %>

<p>Here <% if MultipleTalks %>are the presentations<% else %>is the presentation<% end_if %> someone else submitted with you as a speaker:</p>

<ul>
	<% loop SpeakerTalks %>
		<li>$PresentationTitle <% if Speakers %> submitted by $Owner.FirstName $Owner.Surname ( <% if SpeakerCount=1 %>Speaker:<% else %>Speakers:<% end_if %> <% loop Speakers %><% if Last %>$FirstName $Surname<% else %>{$FirstName} {$Surname}, <% end_if %> <% end_loop %>) <% end_if %><br/>
			<a href="https://www.openstack.org/vote-vancouver/Presentation/{$URLSegment}">https://www.openstack.org/vote-vancouver/Presentation/{$URLSegment}</a>
	<% end_loop %>
</ul>

<% end_if %>

<p>To review and edit presentations, please log in with your email address and password here:
<a href="https://www.openstack.org/summit/vancouver-2015/call-for-speakers/">https://www.openstack.org/summit/vancouver-2015/call-for-speakers/</a></p>

<p>If you don't remember your password, you can have it reset:<br/>
<a href="https://www.openstack.org/Security/lostpassword">https://www.openstack.org/Security/lostpassword</a></p>

<p><strong>Community voting ends Monday, February 23 at 5pm CT</strong>. After the voting concludes, the OpenStack Summit Track Chairs will review all the submissions and make final selections. Then, we’ll send you an email with your status (whether you were selected to present, chosen as an alternate, or not included in this Summit).</p>

<p>Continue to check <a href="http://www.openstack.org/summit/">http://www.openstack.org/summit/</a> for updates.</p>

<p>Good luck with your speaking submissions! If you have any questions along the way, please don't hesitate to ask. You can reach us at <a href="mailto:summit@openstack.org">summit@openstack.org</a>.</p>

<p>The OpenStack Summit Team</p>

</body>
</html>