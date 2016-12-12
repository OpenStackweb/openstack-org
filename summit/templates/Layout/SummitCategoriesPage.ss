            <div class="sessions-landing-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
                <h1>$HeaderTitle</h1>
                $HeaderText
            </div>
        </div>
    </div>
</div>
<div class="all-sessions-wrapper">
    <div class="container">
            <!-- Start Categories -->
            <% loop $Summit.getPublicCategoryGroups().Sort(Name) %>
                <% if $Odd %> <div class="row session-list-row"> <% end_if %>
                <div class="col-sm-6">
                    <div class="session-wrapper" id="containers">
                        <h3>
                            <a href="{$Top.Summit.getScheduleLink()}#track_groups={$ID}">
                                <span class="dot" style="background:#{$Color};"></span> $Name
                            </a>
                        </h3>
                        <div class="session-list-tracks">
                            <ul>
                                <% loop Categories() %>
                                <li class="tracks-tooltip" title="{$Description}">
                                    $Title <% if isPrivate() %> * <% end_if %>
                                </li>
                                <% end_loop %>
                            </ul>
                        </div>
                        <div class="session-list-wrapper">
                            <div class="session-tracks-title">Description</div>
                            <div class="session-list-description">
                                <div class="session-list-text">
                                    <p> $Description </p>
                                </div>
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
        </div>
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
