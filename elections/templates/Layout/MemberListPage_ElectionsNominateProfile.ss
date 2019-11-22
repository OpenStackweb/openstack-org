<% if CurrentElection.NominationsAreOpen %>
    <h2>Election</h2>
    <% with CurrentElectionPage %>
        <p>Nominations are open for the <a href="$Link">$Title.</a></p>
    <% end_with %>
    <% if CurrentMember %>
        <% if CurrentMember.isFoundationMember %>
            <p><a href="/community/members/confirmNomination/{$Top.SelectedMember.ID}" class="roundedButton">Nominate $Top.SelectedMember.FullName</a></p>
        <% else %>
            <hr/>
            <p><strong>Your account credentials do not allow you to nominate candidates.</strong></p>
            <p>If you have more than one account on this site, please log out and log back in with the credentials associated with your Foundation Membership</p>
            <p>Have additional questions? Email <a href=“mailto:secretary@openstack.org”>secretary@openstack.org</a></p>
        <% end_if %>
    <% else %>
        <p><a href="/Security/login/?BackURL=%2Fcommunity%2Fmembers%2Fprofile%2F{$Top.SelectedMember.ID}" class="roundedButton">Log In To Nominate</a></p>

    <% end_if %>
<% end_if %>