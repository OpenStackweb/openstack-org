<div class="container">
	$SetCurrentTab(2)
	<% require themedCSS(profile-section) %>
		<h1>$Title</h1>
        <% if CurrentMember.isFoundationMember %>
                <% include CurrentUserInfoBox LogOutLink=$Top.LogoutUrl, ResignLink=$Top.ResignUrl %>

				<% include ProfileNav %>

				<% if CurrentElection  && CurrentElectionPage %>
					<% with CurrentElection %>
						<form>
							<fieldset>
								<div class="currentElection">
									<strong>The current election is the <a href="$Top.CurrentElectionPage.Link">$Top.CurrentElectionPage.Title</a>.</strong> &nbsp; <a href="$Top.CurrentElectionPage.Link" class="roundedButton">Election Details</a>
								</div>
								<hr/>
                                <h2>Your Status As A Candidate</h2>

								<% if not NominationsAreOpen %>

                                    <p>Nominations are currently closed. Elections start {$ElectionsOpen.Day}, {$ElectionsOpen.Month}, {$ElectionsOpen.DayOfMonth}, {$ElectionsOpen.Year}. See the <a href="$Top.CurrentElectionPage.Link">Election Details</a>.</p>

									<% if IamGoldCandidate %>
                                        <p>If you are a Gold Member Candidate, you can still edit your <a class="roundedButton" href="{$Top.Link}CandidateApplication/">Candidate Profile</a>. </p>
									<% end_if %>

								<% else_if NominationsForCurrentMember %>

									<% if CurrentMemberHasAccepted %>
                                        <p>You have been nominated <strong>$NominationsForCurrentMember.TotalItems</strong> <% if PluralNominations %>times<% else %>time<% end_if %>, and agreed to accept the nomination. You will be listed as a candidate on the ballot when you receive 10 nominations.</p>
                                        <p><a href="{$Top.Link}CandidateApplication/" class="roundedButton">Edit Candidate Application</a></p>
									<% else %>
                                        <p>You have been nominated <strong>$NominationsForCurrentMember.TotalItems</strong> <% if PluralNominations %>times<% else %>time<% end_if %>, but you have not accepted the nomination. You must accept the nomination and complete a Candidate Profile to be officially listed as a candidate for this election.</p>
                                        <p><a href="{$Top.Link}CandidateApplication/" class="roundedButton">Accept Nomination</a></p>
									<% end_if %>

								<% else %>

									<% if CurrentMemberHasAccepted %>
                                        <p>You have completed your Candidate Application, but have not been nominated yet. You will be listed as a candidate on the ballot when you receive 10 nominations.</p>
                                        <p><a href="{$Top.Link}CandidateApplication/" class="roundedButton">Edit Candidate Application</a></p>
									<% else %>
                                        <p><strong>You don't have any nominations for this election.</strong> To get started, complete your Candidate Application and encourage people to nominate you. Once you have completed the application and have at least 10 nominations, you will be listed on the election ballot.</p>
                                        <p><a href="{$Top.Link}CandidateApplication/" class="roundedButton">Fill Out Application</a></p>
									<% end_if %>

								<% end_if %>

								<% include CandidateNominations %>


                            <p><a href="{$Top.CurrentElectionPage.Link}/CandidateList/" class="roundedButton">See The Current Nominations</a> &nbsp;
								<% if NominationsAreOpen %><a href="/community/members/" class="roundedButton">Nominate A Member</a></p><% end_if %>

							</fieldset>
						</form>
					<% end_with %>
				<% else %>

					<form><fieldset>
						<p> There is no current Election. When an election is active, this page allows you to nominate candidates and edit your own candidate information.</p>
					</fieldset></form>
				<% end_if %>
		<% else %>
			<p>In order to edit your community profile, you will first need to <a href="/Security/login/?BackURL=%2Fprofile%2F">login as a member</a>. Don't have an account? <a href="/join/">Join The Foundation</a></p>
			<p><a class="roundedButton" href="/Security/login/?BackURL=%2Fprofile%2Felection%2F">Login</a> <a href="/join/" class="roundedButton">Join The Foundation</a></p>
		<% end_if %>
    </div>
</div>
