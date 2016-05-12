<html>
 <head>
	<meta charset="UTF-8">
</head> 
<body>

<p>Hello, $Speaker.FirstName!</p>
 
<p>It was great seeing you at the OpenStack Summit in $Summit.Title!</p>
<p>We would love for you to share any slides you have for the following presentations:</p>
<ul>
	<% loop $Presentations %>
		<li><em>$Title</em></li>
	<% end_loop %>
</ul>

<p>Please use the direct link below to upload your final presentation slides. Your slides will be made viewable on the OpenStack website alongside the video recording of your presentation.</p>

<p>Ready? Click this link to go to the upload area: <a href="$AbsoluteBaseURL/submit-slides/presentations/?key={$Speaker.SpeakerHash}">$AbsoluteBaseURL/submit-slides/presentations/?key={$Speaker.SpeakerHash}</a></p>

<p>Thank you speaking at the OpenStack Summit. We look forward to seeing you in the future!</p>
<p>The OpenStack Events Team</p>

</body>
