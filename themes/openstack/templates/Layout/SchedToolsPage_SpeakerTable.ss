<div class="container">
<table>
<tr>
	<th>Name</th>
	<th>Email</th>
	<th>Upload Link</th>
</tr>

<% loop ShowSchedSpeakers %>
	<tr>
		<td>$name</td>
		<td>$email</td>
		<td><a href="{$Top.Link}Presentations/?key={$SpeakerHash}">{$Top.Link}Presentations/?key={$SpeakerHash}</a></td>
	</tr>
<% end_loop %>

</table>
</div>
