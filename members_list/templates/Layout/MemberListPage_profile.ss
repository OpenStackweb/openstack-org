<% require themedCSS(member-list) %>

<h1>Individual Member Profile</h1>
<div class="candidate span-14">
    <% cached 'member_profile_page', $Profile.ID, $Profile.LastEdited %>
    <% with Profile %>
        <div class="span-4">
            $ProfilePhoto()
        </div>
        <a name="profile-$ID"></a>
        <div class="details span-10 last">
            <div class="last name-and-title">
                <h3>
                    $FirstName $Surname
                    <% if hasAvailableCertifications %>
                        <img height="65px" src="{$Top.CloudUrl("images/coa/coa-badge.jpg")}" title="COA Certified" alt="COA Certified">
                    <% end_if %>
                    <% if isUpstreamStudent %>
                        <img width="65px" src="{$Top.CloudUrl("images/oui-logo.jpg")}" title="OpenStack Upstream Institute" alt="OpenStack Upstream Institute">
                    <% end_if %>
                    <% if getCommunityAwards %>
                        <img width="65px" src="{$Top.CloudUrl("images/cca-logo.ppg")}" title="Community Constributor Award" alt="Community Constributor Award">
                    <% end_if %>
                </h3>
            </div>
            <hr>
            <div class="span-4">
                <strong>Date Joined</strong>
            </div>
            <div class="span-6 last">$Created.Month $Created.format(d), $Created.Year <br><br></div>
            <% if TwitterName || LinkedInProfile || IRCHandle || Bio %>
                <hr>
            <% end_if %>
            <% if TwitterName %>
                <div class="span-4"><strong>Twitter</strong></div>
                <div class="span-6 last"><a href="https://twitter.com/{$TwitterName}">@{$TwitterName}</a></div>
            <% end_if %>
            <% if LinkedInProfile %>
                <div class="span-4"><strong>LinkedIn </strong></div>
                <div class="span-6 last"><a href="{$LinkedInProfile}">{$LinkedInProfile}</a></div>
            <% end_if %>
            <% if IRCHandle %>
                <div class="span-4"><strong>IRC</strong></div>
                <div class="span-6 last">$IRCHandle<br><p>&nbsp;</p></div>
            <% end_if %>
            <div class="span-4"><strong>Statement of Interest </strong></div>
            <div class="span-6 last">
                <p>$StatementOfInterest</p>
            </div>
            <% if Bio %>
                <div class="span-4"><strong>Bio</strong></div>
                <div class="span-6 last">$Bio</div>
            <% end_if %>
            <% if OrderedAffiliations %>
                <div class="span-4">
                    <strong>Affiliations</strong>
                </div>
                <div class="span-6 last">
                    <ul>
                        <% loop OrderedAffiliations %>
                            <li>
                                $Organization.Name - $Duration
                            </li>
                        <% end_loop %>
                    </ul>
                </div>
            <% end_if %>
            <% if getCommunityAwards %>
                <div class="span-4">
                    <strong>Community Contributor Awards</strong>
                </div>
                <div class="span-6 last">
                    <ul>
                        <% loop getCommunityAwards %>
                            <li>
                                $Name - $Summit.Title $Summit.getSummitYear
                            </li>
                        <% end_loop %>
                    </ul>
                </div>
            <% end_if %>
            <% if Projects %>
                <hr><div class="span-4"><strong>Projects</strong></div>
                <div class="span-6 last">
                    <p>I'm involved in the following OpenStack projects: $Projects</p>
                </div>
            <% end_if %>
            <% if $Speaker().Exists() %>
                <% if $Speaker().PastAcceptedOrPublishedPresentations().Count() %>
                    <hr><div class="span-4"><strong> OpenStack Summit Presentations</strong></div>
                    <div class="span-6 last">
                        <% loop $Speaker().PastAcceptedOrPublishedPresentations(25).GroupedBy(SummitTitle) %>
                            $SummitTitle
                            <ul>
                            <% loop $Children %>
                                <li>
                                    <% if $hasVideos %>
                                        <a href="$getVideoLink"><% if $Title != '' %>$Title<% else %>$getVideoLink<% end_if %></a>
                                    <% else_if $Summit.isCurrent() %>
                                        <a href="$getLink(show)"><% if $Title != '' %>$Title<% else %>$getLink(show)<% end_if %></a>
                                    <% else %>
                                        $Title
                                    <% end_if %>
                                </li>
                            <% end_loop %>
                            </ul>
                        <% end_loop %>
                    </div>
                <% end_if %>
                <% if $Speaker.AvailableForBureau %>
                    <hr><div class="span-4"><strong> Speaker Profile: </strong></div>
                    <div class="span-6 last">
                        <a href="$Speaker.getProfileLink()">$Speaker.getName()</a>
                    </div>
                <% end_if %>
            <% end_if %>
            <p>&nbsp;</p>
        <% end_with %>
    <% end_cached %>
    $ProfileExtensions
</div>
</div>
<div class="span-6 prepend-1 last">
    $ProfileExtensionsFooter
	<% if OwnProfile %>
		<hr/>
		<h2>Your Profile</h2>
		<p><a href="/profile/" class="roundedButton">Edit Your Profile</a></p>
	<% end_if %>
</div>
