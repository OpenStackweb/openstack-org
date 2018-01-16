<div class="sessions-landing-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
                <h1>$HeaderTitle</h1>
                <h3>The Summit</h3>
                <p>The Summit is a collection of presentations, panels and workshops organized by Track. Among the presentations and panels, there are case studies, architecture / operations sessions, upstream development, 101 and demos.</p>
            </div>
        </div>
    </div>
</div>
<div class="all-sessions-wrapper">
    <div class="container">
            <!-- Start Categories -->
            <!-- <% loop $Summit.getPublicCategoryGroups().Sort(Name) %>
                <% if $Odd %> <div class="row session-list-row"> <% end_if %>
                <div class="col-sm-6">
                    <div class="session-wrapper" id="containers">
                        <h3>
                            <a href="{$Top.Summit.getScheduleLink()}#track_groups={$ID}">
                                <span class="dot" style="background:#{$Color};"></span> $Name
                            </a>
                        </h3>
                        <div class="session-list-description">
                            <div class="session-list-text" style="margin-bottom:10px;">
                                <p> $Description </p>
                            </div>
                        </div>
                        <div class="session-list-wrapper">
                            <div class="session-tracks-title">Tracks</div>
                            <div class="session-list-tracks">
                                <ul>
                                    <% loop Categories() %>
                                    <li class="tracks-tooltip" title="{$Description}">
                                        $Title <% if not $VotingVisible %> * <% end_if %>
                                    </li>
                                    <% end_loop %>
                                </ul>
                            </div>
                        </div>
                        <% if $Top.Summit.isCallForSpeakersOpen() %>
                            <a href="{$Top.Summit.getCallForPresentationsLink()}" class="all-sessions-btn">
                                Submit a Presentation <i class="fa fa-chevron-right"></i>
                            </a>
                        <% else_if $Top.Summit.isScheduleDisplayed() %>
                            <a href="{$Top.Summit.getScheduleLink()}#track_groups={$ID}" class="all-sessions-btn">
                                View On Summit Schedule <i class="fa fa-chevron-right"></i>
                            </a>
                        <% end_if %>
                    </div>
                </div>
                <% if $Even || $Last %> </div> <% end_if %>
            <% end_loop %>
            <!-- End Categories -->

        <div class="row">
            <div class="col-sm-12">
                <p>* Note: these are not selected though the Call for Presentations process</p>
            </div>
        </div> -->
        <!-- Full track definitions -->
        <div class="row">
            <div class="col-sm-12">
                <hr>
                <h1 id="all-tracks">All Summit Tracks</h1>
                <% loop $Summit.getPublicCategories() %>
                <p>
                    <strong> $Title </strong><br>
                    $Description
                </p>
                <% end_loop %>
            </div>
        </div>
    </div>
</div>
<div class="sessions-landing-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
                
                <h3>The Forum</h3>
                <p>OpenStack users and developers gather at the Forum to brainstorm the requirements for the next release, gather feedback on the past version and have strategic discussions that go beyond just one release cycle. Sessions are submitted outside of the Summit Call for Presentations and are more collaborative, discussion-oriented.</p>
            </div>
        </div>
    </div>
</div>
