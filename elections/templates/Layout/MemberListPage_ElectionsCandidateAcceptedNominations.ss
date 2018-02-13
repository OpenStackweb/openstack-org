<% if Candidate %>
    <% if Candidate.HasAcceptedNomination %>

        <h3>$Top.SelectedMember.FirstName is a candidate in the <% with CurrentElection %> $Title <% end_with %>.</h3>
        <hr/>

        <% if CurrentElection.NominationsAreOpen %>
            <% if Candidate.MoreThanTen %>
                <p>$Top.SelectedMember.FirstName has been nominated enough times to appear on the election ballot. You can read the answers $Top.SelectedMember.FirstName gave to the election questions below.</p>
            <% else %>
                <p>Read the Q&A below and see if you want to <a href="/community/members/confirmNomination/{$Top.SelectedMember.ID}">Nominate $Top.SelectedMember.FirstName</a> in this election.</p>
            <% end_if %>
            <hr/>

        <% end_if %>

        <% loop Candidate %>

            <div class="election-question span-10 last">
                <div class="span-1">Q</div>
                <div class="question span-9 last">
                    <h4>$Top.CurrentElectionPage.CandidateApplicationFormRelationshipToOpenStackLabel</h4>
                </div>
                <div class="span-1">A</div>
                <div class="answer span-9 last">
                    $RelationshipToOpenStack
                </div>
            </div>

            <div class="election-question span-10 last">
                <div class="span-1">Q</div>
                <div class="question span-9 last">
                    <h4>$Top.CurrentElectionPage.CandidateApplicationFormExperienceLabel</h4>
                </div>
                <div class="span-1">A</div>
                <div class="answer span-9 last">
                    $Experience
                </div>
            </div>

            <div class="election-question span-10 last">
                <div class="span-10 last">
                    <div class="span-1">Q</div>
                    <div class="question span-9 last">
                        <h4>$Top.CurrentElectionPage.CandidateApplicationFormBoardsRoleLabel</h4>
                    </div>
                </div>
                <div class="span-10 last">
                    <div class="span-1">A</div>
                    <div class="answer span-9 last">
                        $BoardsRole
                    </div>
                </div>
            </div>

            <div class="election-question span-10 last">
                <div class="span-1">Q</div>
                <div class="question span-9 last">
                    <h4>$Top.CurrentElectionPage.CandidateApplicationFormTopPriorityLabel</h4>
                </div>
                <div class="span-1">A</div>
                <div class="answer span-9 last">
                    $TopPriority
                </div>
            </div>


        <% end_loop %>


        <hr/>
        <p><% if Candidate.Nominations %>$Top.SelectedMember.FirstName has already been nominated by:<% end_if %></p>
        <% loop Candidate %>
            <ul>
                <% loop Nominations %>
                    <li>$Member.Name</li>
                <% end_loop %>
            </ul>
        <% end_loop %>


    <% end_if %>
<% end_if %>