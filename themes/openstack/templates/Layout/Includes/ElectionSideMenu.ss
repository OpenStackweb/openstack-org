<% require themedCSS(election-page) %>

<div class="col-sm-4 col-sm-pull-8">
	<div class="newSubNav">
		<ul class="overviewNav">
			<li><a href="$Link">Election Details <i class="fa fa-chevron-right"></i></a></li>
			<li><a href="{$Link}CandidateList">See The Candidates <i class="fa fa-chevron-right"></i></a></li>
			<% if NominationsAreOpen %>
			    <li><a href="/community/members/">Nominate A Member <i class="fa fa-chevron-right"></i></a></li>
			<% end_if %>
			<% if ElectionIsActive %>
			    <li><a href="/profile/election/">Be A Candidate <i class="fa fa-chevron-right"></i></a></li>
			<% end_if %>
			<li><a href="{$Link}CandidateListGold">Gold Member Election Candidates <i class="fa fa-chevron-right"></i></a></li>
			<li><a href="/legal/community-code-of-conduct/">Code of Conduct <i class="fa fa-chevron-right"></i></a></li>
		</ul>
	</div>
</div>
