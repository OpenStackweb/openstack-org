<% require themedCSS(member-list) %>

<h1>Individual Speaker Profile</h1>
<div class="candidate span-14">
    <% with Profile %>
        <div class="span-4">
            <img src="$ProfilePhoto()" />
        </div>
        <div class="details span-10 last">
            <div class="last name-and-title">
                <h3>
                    $FirstName $LastName
                    <a class="back" href="{$BaseHref}community/speakers">Back to Speakers Bureau</a>
                </h3>

            </div>
            <hr>
            <div class="col-md-6">
                <div class="span-4">
                    <strong>Date Joined: </strong> $Created.Month $Created.format(d), $Created.Year
                </div>
                <div class="span-4">
                    <strong>Country: </strong> $Country
                </div>
                <div class="span-4">
                    <strong>Registered for Upcoming Summit: </strong> <% if RegisteredForSummit %> $Summit.Name <% else %> No <% end_if %>
                </div>
                <% if Expertise %>
                    <strong>Expertise: </strong> $Expertise
                <% end_if %>
                <% if Member.OrderedAffiliations %>
                    <div class="span-4">
                        <strong>Affiliations</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                        <% loop Member.OrderedAffiliations %>
                            <li>$Organization.Name - <i> $Duration </i> </li>
                        <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
                <div class="span-4"><strong>Statement of Interest </strong></div>
                <div class="span-6 last">
                    <p>$Member.StatementOfInterest</p>
                </div>
                <% if AreasOfExpertise %>
                    <div class="span-4">
                        <strong>Areas Of Expertise</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                        <% loop AreasOfExpertise %>
                            <li>$Expertise</li>
                        <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
                <% if TwitterName || LinkedInProfile || IRCHandle || Bio %>
                    <hr>
                <% end_if %>
                <% if TwitterHandle %>
                    <div class="span-4"><strong>Twitter</strong></div>
                    <div class="span-6 last"><a href="https://twitter.com/{$TwitterHandle}">@{$TwitterHandle}</a><br><p>&nbsp;</p></div>
                <% end_if %>
                <% if IRCHandle %>
                    <div class="span-4"><strong>IRC</strong></div>
                    <div class="span-6 last">$IRCHandle<br><p>&nbsp;</p></div>
                <% end_if %>
                <% if Bio %>
                    <div class="span-4"><strong>Bio</strong></div>
                    <div class="span-6 last">$Bio</div>
                <% end_if %>
                <% if Projects %>
                    <hr><div class="span-4"><strong>Projects</strong></div>
                    <div class="span-6 last">
                        <p>I'm involved in the following OpenStack projects: $Projects</p>
                    </div>
                <% end_if %>
                <% if Presentations %>
                    <div class="span-4">
                        <strong>Presentations</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                        <% loop MixedPresentationLinks(5) %>
                            <li><a href="$Link"><% if $Source = 'summit' %>$Title<% else %>$Link<% end_if %></a></li>
                        <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
                <hr>
                <div class="span-4">
                    <strong>Funded Travel: </strong> <% if FundedTravel %> Yes <% else %> No <% end_if %>
                </div>
                <div class="span-4">
                    <strong>Willing to Travel: </strong> <% if WillingToTravel %> Yes <% else %> No <% end_if %>
                </div>
                <% if WillingToTravel %>
                    <div class="span-4">
                        <strong>Travel Preference</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                        <% loop TravelPreferences %>
                            <li>$Country</li>
                        <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
                <% if Languages %>
                    <div class="span-4">
                        <strong>Fluent in</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                        <% loop Languages %>
                            <li>$Language</li>
                        <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
            </div>
            <div class="col-md-6">
                <h4>Contact $FirstName</h4>
                $Top.ContactForm
            </div>
        </div>
    <% end_with %>
</div>
