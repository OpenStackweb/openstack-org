		<div class="candidate">
			<div class="span-2">
				<% loop Member %>
					<a href="/community/members/profile/{$ID}">$ProfilePhoto(50)</a> <p>&nbsp</p>
				<% end_loop %>
			</div>
			<div class="span-14 last">
				<h4><a href="/community/members/profile/{$Member.ID}">$Member.Name</a></h4>
				<p class="nominations">
					<strong>Nominated by: </strong><% loop Nominations %><span>$Member.Name</span> <% end_loop %>
				</p>
<% if Member.Bio %>
<div class="bio">
<strong>About $Member.FirstName $Member.LastName</strong><br/>
$Member.Bio</div>
<% end_if %>
<a href="/community/members/profile/{$Member.ID}">View $Member.Name's full candidate profile and Q&A >></a><br/>
<br/>
			</div>
			<hr/>
		</div>
