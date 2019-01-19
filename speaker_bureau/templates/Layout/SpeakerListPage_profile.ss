<% require themedCSS(member-list) %>

<h1>Individual Speaker Profile</h1>
<div class="candidate span-14">

    <% with Profile %>
        <% cached "speaker_profile_page", $ID, $qLastEdited %>
        <div class="span-4">
            <img class="profile_pic" src="$ProfilePhoto()"/>
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
                    <strong>Country: </strong> $CountryName
                </div>
                <% if $Member.Exists %>
                <div class="span-4">
                    <strong>Home Town: </strong> $Member.City
                </div>
                <div class="span-4">
                    <strong>Zip Code: </strong> $Member.Postcode
                </div>
                <% end_if %>
                <div class="span-4">
                    <strong>Registered for Upcoming
                        Summit: </strong> <% if RegisteredForSummit %> $Summit.Name <% else %> No <% end_if %>
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
                                <li>$Organization.Name - <i> $Duration </i></li>
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
                <% if TwitterName || IRCHandle || Bio %>
                    <hr>
                <% end_if %>
                <% if TwitterName %>
                    <div class="span-4"><strong>Twitter</strong></div>
                    <div class="span-6 last"><a href="https://twitter.com/{$TwitterName}">@{$TwitterName}</a><br>

                        <p>&nbsp;</p></div>
                <% end_if %>
                <% if IRCHandle %>
                    <div class="span-4"><strong>IRC</strong></div>
                    <div class="span-6 last">$IRCHandle<br>

                        <p>&nbsp;</p></div>
                <% end_if %>
                <% if Bio %>
                    <div class="span-4"><strong>Bio</strong></div>
                    <div class="span-6 last">$Bio</div>
                <% end_if %>
                <% if Projects %>
                    <hr>
                    <div class="span-4"><strong>Projects</strong></div>
                    <div class="span-6 last">
                        <p>I'm involved in the following OpenStack projects: $Projects</p>
                    </div>
                <% end_if %>
                <% if PastAcceptedOrPublishedPresentations() %>
                    <hr>
                    <div class="span-4">
                        <strong>Presentations from previous OpenStack Summits:</strong>
                    </div>
                    <div class="span-6 last">
                        <% loop PastAcceptedOrPublishedPresentations(25).GroupedBy(SummitTitle) %>
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
                <% if OtherPresentationLinks() %>
                    <div class="span-4">
                        <strong>Additional presentations:</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                            <% loop OtherPresentationLinks.Limit(5) %>
                                <li>
                                    <a href="$LinkUrl"><% if $Title != '' %>$Title<% else %>$LinkUrl<% end_if %></a>
                                    <% if $YoutubeID %>
                                        <br>
                                        <iframe frameborder="0" width="200" height="120" allowfullscreen=""
                                                src="//www.youtube.com/embed/{$YoutubeID}?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;controls=2">
                                        </iframe>
                                    <% end_if %>
                                </li>
                            <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>
                <hr>
                <div class="span-4 checkbox_item">
                    <strong>Willing to present via video conference: </strong><% if WillingToPresentVideo %> Yes <% else %> No <% end_if %>
                </div>
                <% if $WillingToTravel %>
                    <div class="span-4 checkbox_item">
                        <strong>Willing to travel to any country:</strong> Yes
                    </div>
                <% else_if TravelPreferences.Count %>
                    <div class="span-4">
                        <strong>I'm willing to travel to:</strong>
                    </div>
                    <div class="span-6 last">
                        <ul>
                            <% loop TravelPreferences %>
                                <li>$getCountryName()</li>
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
                                <li>$Name</li>
                            <% end_loop %>
                        </ul>
                    </div>
                <% end_if %>

                <% if Notes %>
                    <div class="span-4"><strong>Notes</strong></div>
                    <div class="span-6 last"> $Notes </div>
                <% end_if %>
            </div>
            <% end_cached %>
            <div class="col-md-6 contact_form_div">
                <h4>Contact $FirstName</h4>
                $Top.ContactForm
            </div>
        </div>
    <% end_with %>

</div>
