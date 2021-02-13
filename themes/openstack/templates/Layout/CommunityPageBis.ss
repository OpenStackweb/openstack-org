</div>
<div class="banner">
    <div class="container">
        <div class="row">
            <div class="col-md-12 banner-text-wrapper text-center">
                <h2 class="text-center">Welcome to the OpenStack Community</h2>
                <p>
                    Welcome to the OpenStack Contributor Community! We're glad you're here. If you know how you want to contribute, get started below. If you need some advice, we're <a href="https://www.openstack.org/community#help">here to help</a>!
                </p>
            </div>
        </div>
    </div>
</div>
<div class="contribute-panel">
    <div class="container">
        <div class="row contribute-title">
            Select the way you want to contribute...
        </div>
        <div class="row nav">
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#code" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="{$Top.CloudUrl("images/community/pencil.png")}" />
                    </div>
                    <div>Code & Documentation</div>
                </a>
            </div>
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#events" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="{$Top.CloudUrl("images/community/globe.png")}" />
                    </div>
                    <div>Events</div>
                </a>
            </div>
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a href="https://www.meetup.com/pro/openinfradev" class="nav-button" target="_blank">
                    <div class="nav-button-icon">
                        <img src="{$Top.CloudUrl("images/community/pin.png")}" />
                    </div>
                    <div>User Groups</div>
                </a>
            </div>
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#users" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>Users</div>
                </a>
            </div>
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#sponsorship" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="{$Top.CloudUrl("images/community/book.png")}">
                    </div>
                    <div>Operators</div>
                </a>
            </div>
            <div class="col-md-5ths col-xs-6 nav-button-box">
                <a href="https://docs.openstack.org/contributors/common/mentoring.html" class="nav-button" target="_blank">
                    <div class="nav-button-icon">
                        <img src="{$Top.CloudUrl("images/community/bulb.png")}" />
                    </div>
                    <div>Mentoring</div>
                </a>
            </div>
        </div>
        <div id="accordion-parent">
            <div id="code" class="collapse">
                <% include CommunityPageBisCode %>
            </div>
            <div id="documentation" class="collapse">
                <% include CommunityPageBisDocumentation %>
            </div>
            <div id="events" class="collapse">
                <% include CommunityPageBisEvents %>
            </div>
            <div id="meetup" class="collapse">
                <% include CommunityPageBisMeetup %>
            </div>
            <div id="users" class="collapse">
                <% include CommunityPageBisUsers %>
            </div>
            <div id="sponsorship" class="collapse">
                <% include CommunityPageBisSponsorship %>
            </div>
        </div>
    </div>
</div>
<div class="quick-links">
    <div class="container">
        <div class="row">
            <div class="col-md-12 quick-links-header">
                <div class="pre-title">
                    <span>THE QUICK LINKS</span><a name="quicklinks"></a>
                </div>
                <h1>Contributor Resources</h1>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Who are we?
                </div>
                <div>
                    <a href="https://governance.openstack.org/tc/reference/opens.html" target="_blank">The Four Opens</a><br>
                    <a href="https://governance.openstack.org/tc/reference/principles.html" target="_blank">Our guiding principles</a><br>
                    <a href="https://governance.openstack.org/sigs/" target="_blank">Special Interest Groups (SIGs)</a><br>
                    <a href="https://governance.openstack.org/tc/reference/projects/index.html" target="_blank">Project Teams</a><br>
                    <a href="https://governance.openstack.org/uc/#teams" target="_blank">User committee working groups</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Getting started
                </div>
                <div>
                    <a href="https://releases.openstack.org/" target="_blank">Releases and release schedule</a><br>
                    <a href="https://docs.openstack.org" target="_blank">OpenStack documentation</a><br>
                    <a href="{$Top.CloudUrl('assets/marketing/OpenStack-101-Modular-Deck-1.pptx')}" target="_blank">OpenStack 101</a><br>
                    <a href="https://wiki.openstack.org/wiki/First_Contact_SIG" target="_blank">First Contact SIG</a><br>
                    <a href="https://governance.openstack.org/tc/reference/help-most-needed.html" target="_blank">Areas where help is most needed</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Developer resources
                </div>
                <div>
                    <a href="https://opendev.org/" target="_blank">Git repositories</a><br>
                    <a href="https://docs.openstack.org/project-team-guide/" target="_blank">Project team guide</a><br>
                    <a href="https://governance.openstack.org/tc/goals/" target="_blank">Community-wide goals</a><br>
                    <a href="http://specs.openstack.org/" target="_blank">Specs</a> and <a href="https://review.openstack.org/" target="_blank">Code reviews</a><br>
                    <a href="https://codesearch.opendev.org" target="_blank">Search all OpenStack code</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    User resources
                </div>
                <div>
                    Bug reports on <a href="https://storyboard.openstack.org/" target="_blank">StoryBoard</a> or <a href="https://launchpad.net/openstack/" target="_blank">Launchpad</a><br>
                    <a href="https://security.openstack.org/" target="_blank">Security advisories</a><br>
                    <a href="https://translate.openstack.org/" target="_blank">Contribute translations</a><br>
                    <a href="https://refstack.openstack.org/" target="_blank">Interoperability testing</a><br>
                    <a href="https://developer.openstack.org/" target="_blank">App developer resources</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Governance
                </div>
                <div>
                    <a href="/foundation/board-of-directors/">Board of Directors</a><br>
                    <a href="https://governance.openstack.org/tc/">Technical Committee</a><br>
                    <a href="https://governance.openstack.org/election/">Community elections</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Marketing &amp; Branding
                </div>
                <div>
                    <a href="/marketing">Marketing Portal</a><br>
                    <a href="/brand/">Commercial Logos</a><br>
                    <a href="/project-mascots/">Project Mascots</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Career
                </div>
                <div>
                    <a href="/marketplace/training/">Training</a><br>
                    <a href="/coa">Certified administrator program</a><br>
                    <a href="/jobs">OpenStack Jobs board</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Diversity
                </div>
                <div>
                    <a href="/legal/community-code-of-conduct/">Code of Conduct</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/openstack-discuss" target="_blank">Use the [diversity] tag on the OpenStack Discuss ML</a><br>
                    <a href="https://www.outreachy.org/" target="_blank">Outreachy</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="where-to-start">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <h2>How can I get more involved?</h2>
                <p>
                    OpenStack is a large community, and it can seem overwhelming when you don't know where to start.
                    The best way to approach it is to get involved with a specific project, working group or local meetup.
                </p>
            </div>
            <div class="col-md-5 col-sm-6 start-options">
                <a href="https://governance.openstack.org/tc/reference/upstream-investment-opportunities/" class="start-option btn">
                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    <span>Areas where help is most needed</span>
                </a><br>
                <a href="https://docs.openstack.org/upstream-training/" class="start-option btn">
                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    <span>Tutorials & Upstream Institute</span>
                </a><br>
                <a href="https://www.meetup.com/pro/openinfradev" class="start-option btn">
                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    <span>Find a local user group</span>
                </a><br>
                <a href="https://www.openstack.org/events/community-events" class="start-option btn">
                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    <span>Attend an event</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="help" id="help">
    <div class="container">
        <div class="row">
            <div class="col-md-12 help-header">
                <div class="help-title">Don't worry! We're here to help.</div>
                <p>
                    We want you to have a great experience. Open Infrastructure Foundation staff and volunteer ambassadors around
                    the world are here to help you get plugged in and make an impact.
                </p>
            </div>
            <div class="col-md-12 help-managers">
                <h4>Open Infrastructure Foundation Community Managers</h4>
                <div class="row">
                <% loop $CommunityManagers.Sort(Order) %>
                    <div class=" col-sm-5ths col-xs-6 community-manager">
                        <div class="profile-pic">
                            $ProfilePhoto(160, true)
                        </div>
                        <p><strong>$FullName</strong><br/>
                        $City, $Top.CountryName($Country)</p>
                        <p>
                        <% if TwitterName %>
                        <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}"></a>
                        <% end_if %>
                        <% if LinkedInProfile %>
                        <a class="staff-linkedin" href="{$LinkedInProfile}"></a>
                        <% end_if %>
                        <a class="staff-openstack" href="/community/members{$Link}{$ID}"></a></p>
                    </div>
                <% end_loop %>
                </div>
            </div>
            <div class="col-md-12 help-ambassadors">
                <h4>Global OpenStack Ambassadors</h4>
                <div class="row">
                <% loop $Ambassadors.Sort(Order) %>
                    <div class="col-md-2 col-sm-3 col-xs-6 ambassador">
                        <div class="profile-pic">
                            $ProfilePhoto(130, true)
                        </div>
                        <p><strong>$FullName</strong><br/>
                        $City, $Top.CountryName($Country)</p>
                        <div class="ambassador-twitter-veil">
                            <i class="fa fa-twitter" aria-hidden="true"></i><br>
                            <span> @$TwitterName </span>
                        </div>
                    </div>
                <% end_loop %>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="quick-links-banner">
    <img src="{$Top.CloudUrl("images/community/quicklinks.png")}" />
</div>
<div class="communicate">
    <div class="container">
        <div class="row">
            <div class="col-md-12 communicate-header">
                <div class="communicate-title">
                    <img class="communicate-icon" src="{$Top.CloudUrl("images/community/message.png")}">
                    How to communicate
                </div>
                <p>
                    Thousands of OpenStack community members around the world collaborate on a daily basis via mailing lists and IRC channels.
                    Once you get involved in a specific project or working group, there are often specialized meetings and communication channels.
                    Subscribe to the OpenStack social media channels and sign up to receive email communications from the Foundation to get plugged
                    into the largest information streams.
                </p>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    IRC channels (on Freenode)
                </div>
                <div>
                    <a href="https://docs.openstack.org/contributors/common/irc.html" target="_blank">How to set up IRC</a><br>
                    <a href="https://webchat.freenode.net/?channels=openstack" target="_blank">#openstack</a> (usage questions)<br>
                    <a href="https://webchat.freenode.net/?channels=openstack-dev" target="_blank">#openstack-dev</a> (development questions)<br>
                    <a href="https://webchat.freenode.net/?channels=openstack-infra" target="_blank">#openstack-infra</a> (project infrastructure)<br>
                    <a href="http://eavesdrop.openstack.org/">List of IRC meetings and channel logs</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Mailing lists
                </div>
                <div>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/openstack-discuss" target="_blank">OpenStack Discussion list</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo" target="_blank">List of all available mailing-lists</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Other tools
                </div>
                <div>
                    <a href="https://wiki.openstack.org/" target="_blank">Wiki</a><br>
                    <a href="https://etherpad.openstack.org/" target="_blank">Etherpad</a><br>
                    <a href="https://ethercalc.openstack.org/" target="_blank">Ethercalc</a><br>
                    <a href="https://wiki.openstack.org/wiki/Infrastructure/Conferencing" target="_blank">Conference call bridge</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Info, News, and Blogs
                </div>
                <div>
                    <a href="https://www.openstack.org/blog/" target="_blank">OpenStack Blog</a><br>
                    <a href="http://planet.openstack.org/" target="_blank">Planet OpenStack (blog aggregator)</a><br>
                    <a href="http://superuser.openstack.org/" target="_blank">Superuser Magazine</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Social Channels
                </div>
                <div>
                    <a href="https://twitter.com/openstack" target="_blank">Twitter</a><br>
                    <a href="https://www.facebook.com/openinfradev" target="_blank">Facebook</a><br>
                    <a href="https://www.linkedin.com/groups/3239106" target="_blank">LinkedIn</a><br>
                    <a href="https://www.youtube.com/user/OpenStackFoundation" target="_blank">YouTube Channel</a>
                </div>
            </div>
        </div>
    </div>
</div>



