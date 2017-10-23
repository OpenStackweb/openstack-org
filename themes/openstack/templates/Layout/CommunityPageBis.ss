</div>
<div class="banner">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-xs-12 banner-img-wrapper pull-right">
                <img src="themes/openstack/images/community/rocket.png" />
            </div>
            <div class="col-md-8 col-xs-12 banner-text-wrapper pull-right">
                <h2>New to the community?</h2>
                <p>
                    Then you are in the right place! This is the best place to start when you're interested in contributing to OpenStack.
                    There are lots of benefits to participation, including oportinities to influence the future of the project.
                    Find out how to get involved.
                </p>
                <a href="https://docs.openstack.org/contributors" target="_blank" class="banner-button btn btn-primary">
                    Get Started with our Contributor Guide <i class="fa fa-chevron-circle-right fa-inverse" aria-hidden="true"></i>
                </a>
                <hr />
                <p>
                    Already a contributor and looking for resources? <a href="/community/#quicklinks" class="bannerquicklinks">Skip to Quick Links</a>
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
            <div class="col-md-5ths col-sm-6 col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#code" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="themes/openstack/images/community/pencil.png" />
                    </div>
                    <div>Code & Documentation</div>
                </a>
            </div>
            <div class="col-md-5ths col-sm-6 col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#events" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="themes/openstack/images/community/globe.png" />
                    </div>
                    <div>Events</div>
                </a>
            </div>
            <div class="col-md-5ths col-sm-6 col-xs-6 nav-button-box">
                <a href="https://groups.openstack.org/" class="nav-button" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="themes/openstack/images/community/pin.png" />
                    </div>
                    <div>User Groups</div>
                </a>
            </div>
            <div class="col-md-5ths col-sm-6 col-xs-6 nav-button-box">
                <a class="nav-button" data-toggle="collapse" data-target="#users" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="themes/openstack/images/community/bulb.png" />
                    </div>
                    <div>Users</div>
                </a>
            </div>
            <div class="col-md-5ths col-sm-6 col-xs-6 nav-button-box">
                <a href="https://www.openstack.org/join/#sponsor" class="nav-button" data-parent="#accordion-parent">
                    <div class="nav-button-icon">
                        <img src="themes/openstack/images/community/wallet.png" />
                    </div>
                    <div>Sponsorship</div>
                </a>
            </div>
        </div>
        <div id="accordion-parent">
            <div id="code" class="collapse">
                <% include CommunityPageBis_Code %>
            </div>
            <div id="documentation" class="collapse">
                <% include CommunityPageBis_Documentation %>
            </div>
            <div id="events" class="collapse">
                <% include CommunityPageBis_Events %>
            </div>
            <div id="meetup" class="collapse">
                <% include CommunityPageBis_Meetup %>
            </div>
            <div id="users" class="collapse">
                <% include CommunityPageBis_Users %>
            </div>
            <div id="sponsorship" class="collapse">
                <% include CommunityPageBis_Sponsorship %>
            </div>
        </div>
    </div>
</div>
<div class="where-to-start">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <h2>Where should I start?</h2>
                <p>
                    OpenStack is a large community, and it can seem overwhelming when you don't know where to start.
                    The best way to approach it is to get involved with a specific project, working group or local meetup.
                </p>
            </div>
            <div class="col-md-5 col-sm-6 start-options">
                <a href="https://governance.openstack.org/tc/reference/top-5-help-wanted.html" class="start-option btn">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    <span>Top 5 projects that need contributors</span>
                </a><br>
                <a href="https://docs.openstack.org/upstream-training/" class="start-option btn">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    <span>Tutorials & Upstream Institute</span>
                </a><br>
                <a href="https://groups.openstack.org/" class="start-option btn">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    <span>Find a local user group or attend an event</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="help">
    <div class="container">
        <div class="row">
            <div class="col-md-12 help-header">
                <div class="help-title">Don't worry! We're here to help.</div>
                <p>
                    We want you to have a great experience. OpenStack Foundation staff and volunteer ambassadors around
                    the world are here to help you get plugged in and make an impact.
                </p>
            </div>
            <div class="col-md-12 help-managers">
                <h4>OpenStack Foundation Community Managers</h4>
                <div class="row">
                <% loop $CommunityManagers.Sort(Order) %>
                    <div class=" col-sm-5ths col-xs-6 community-manager">
                        <div class="profile-pic">
                            $ProfilePhoto(160, true)
                        </div>
                        <p><strong>$FullName</strong><br/>
                        <% if CurrentJobTitle %>
                            $CurrentJobTitle<br/>
                        <% end_if %>
                        $City, $Top.CountryName($Country)</p>
                        <div class="ambassador-twitter-veil">
                            <i class="fa fa-twitter" aria-hidden="true"></i><br>
                            <span> @$TwitterName </span>
                        </div>
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
<div class="communicate">
    <div class="container">
        <div class="row">
            <div class="col-md-12 communicate-header">
                <div class="communicate-title">
                    <img class="communicate-icon" src="themes/openstack/images/community/message.png" />
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
                    OpenStack IRC channels (chat.freenode.net)
                </div>
                <div>
                    #openstack<br/>
                    #openstack-101<br/>
                    #openstack-dev<br/>
                    #openstack-infra<br/>
                    #openstack-meeting<br/>
                    #openstack-upstream-institute

                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Mailing lists
                </div>
                <div>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/openstack">OpenStack General</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/openstack-dev">OpenStack-dev</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/openstack-docs">OpenStack-docs</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/women-of-openstack">Women-of-OpenStack</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Info & Forums
                </div>
                <div>
                    <a href="http://superuser.openstack.org/" target="_blank">Superuser Magazine</a><br>
                    <a href="https://www.openstack.org/blog/" target="_blank">Developer Digest</a><br>
                    <a href="https://ask.openstack.org/" target="_blank">Q&A Forum</a>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 communicate-item">
                <div class="communicate-item-title">
                    Social Channels
                </div>
                <div>
                    <a href="https://twitter.com/openstack" target="_blank">Twitter</a><br>
                    <a href="https://www.facebook.com/openstack" target="_blank">Facebook</a><br>
                    <a href="https://www.linkedin.com/groups/3239106" target="_blank">LinkedIn</a><br/>
                    <a href="https://www.youtube.com/user/OpenStackFoundation" target="_blank">YouTube Channel</a>
                </div>
            </div>
            <div class="col-md-8 col-sm-12 communicate-item">
                <div class="communicate-item-title">
                    Sign up to hear from the Foundation
                </div>
                <div class="form-inline">
                    <input class="form-control sign-up-input" />
                    <a href="" class="sign-up-submit btn"> SUBMIT </a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="quick-links">
    <div class="quick-links-banner">
        <img src="themes/openstack/images/community/quicklinks.png" />
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 quick-links-header">
                <div class="pre-title"><span>THE QUICK LINKS</span><a name="quicklinks"></a></div>
                <h1>Contributor Resources</h1>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Who are we?
                </div>
                <div>
                    <a href="https://governance.openstack.org/tc/reference/opens.html" target="_blank">Four opens</a><br>
                    <a href="https://governance.openstack.org/tc/reference/principles.html" target="_blank">Our guiding principles</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Resources for projects
                </div>
                <div>
                    <a href="https://governance.openstack.org/tc/reference/tags/index.html" target="_blank">Tags</a><br>
                    <a href="https://governance.openstack.org/tc/goals/" target="_blank">Community wide goals</a><br>
                    <a href="https://releases.openstack.org/" target="_blank">Release schedule</a><br>
                    <a href="">Onboarding</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    App developers
                </div>
                <div>
                    <a href="https://developer.openstack.org/" target="_blank">SDKs</a><br>
                    <a href="https://wiki.openstack.org/wiki/Category:Working_Groups" target="_blank">Working groups & SIGs</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Getting started
                </div>
                <div>
                    <a href="https://governance.openstack.org/tc/reference/top-5-help-wanted.html" target="_blank">Top 5 areas for support</a><br>
                    <a href="https://www.youtube.com/user/OpenStackFoundation" target="_blank">Tutorials</a><br>
                    <a href="https://www.openstack.org/assets/marketing/OpenStack-101-Modular-Deck-1.pptx" target="_blank">OpenStack 101</a><br>
                    <a href="https://storyboard.openstack.org/" target="_blank">Report a bug</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Diversity
                </div>
                <div>
                    <a href="/legal/community-code-of-conduct/">Code of Conduct</a><br>
                    <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/women-of-openstack" target="_blank">Women of OpenStack mailing list</a><br>
                    <a href="https://www.outreachy.org/" target="_blank">Outreachy</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Foundation
                </div>
                <div>
                    <a href="/join">Join</a><br>
                    <a href="/foundation/tech-committee/">Technical Committee</a><br>
                    <a href="/foundation/user-committee/">User Committee</a><br>
                    <a href="/foundation/board-of-directors/">Board of Directors</a><br>
                    <a href="/foundation/staff">Staff</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Marketing & Branding
                </div>
                <div>
                    <a href="/marketing">Marketing Portal</a><br>
                    <a href="/brand/">Commercial Logos</a><br>
                    <a href="/project-mascots/">Project Mascots</a><br>
                    <a href="/store">Store</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 quick-links-item">
                <div class="quick-links-item-title">
                    Career
                </div>
                <div>
                    <a href="/marketplace/training/">Training</a><br>
                    <a href="/coa">COA</a><br>
                    <a href="/jobs">Jobs</a>
                </div>
            </div>
        </div>
    </div>
</div>
