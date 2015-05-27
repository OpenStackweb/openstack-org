<div class="row">
    <div class="col-sm-12 survey-top" style="padding-left: 0 !important;padding-right: 0 !important;">
        Logged in as <strong>$CurrentMember.FirstName</strong>. &nbsp; <a href="$Top.Link(logout)" class="survey-logout">Log Out</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="padding-left: 0 !important;padding-right: 0 !important;">
        <ul class="survey-steps">
            <% loop Survey.getSteps %>
                <li><a id="$Template.Name" href="/surveys/current/{$Template.Name}" class="survey-step<% if $Template.Name == $Top.Survey.CurrentStep.Template.Name %> current<% end_if %>">$Template.FriendlyName</a></li>
            <% end_loop %>
        </ul>
    </div>
</div>