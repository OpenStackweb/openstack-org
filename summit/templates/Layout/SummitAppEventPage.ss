<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-6 col-xs-12 col-main-title">
                <h1 style="text-align:left;">Event Details</h1>
                <% if $BackURL %>
                    <div class="go-back">
                        <a href="{$BackURL}"><< Go back </a>
                    </div>
                <% end_if %>
            </div>
            <div class="col-sm-6 col-xs-12">
                <schedule-global-filter search_url="{$Top.Link(global-search)}"></schedule-global-filter>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="title">$Event.Title</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <% if $Event.Category %>
                    <div class="track">
                        <a href="$Top.Link(global-search)?t={$Event.Category.Title}">$Event.Category.Title</a>
                    </div>
                <% end_if %>

                $Event.Abstract

                <% if $Event.isPresentation &&  $Event.AttendeesExpectedLearnt %>
                    <br>
                    <div class="expected-learnt">
                        <div>What can I expect to learn?</div>
                        $Event.AttendeesExpectedLearnt
                    </div>
                <% end_if %>
            </div>
            <div class="col-md-6 col-xs-12 info">
                    <script type="application/javascript">
                            <% if CurrentMember %>
                            <% with $CurrentMember %>
                            var current_user = {
                                id: {$ID},
                                first_name: '{$FirstName.JS}',
                                last_name: '{$Surname.JS}',
                                is_attendee: <% if CurrentMember.isAttendee($Top.Event.Summit.ID) %>true<% else %>false<% end_if %>,
                                has_feedback :  <% if Event.getCurrentMemberFeedback() %>true<% else %>false<% end_if %>
                            };
                            <% end_with %>
                            <% else %>
                            var current_user = null;
                            <% end_if %>
                            var event = {
                                id              : {$Event.ID},
                                summit_id       : {$Event.SummitID},
                                rsvp_link       : "{$Event.getRSVPURL().JS}",
                                has_rsvp        : <%if $Event.hasRSVP() %>true<% else %>false<% end_if %>,
                                rsvp_external   : <%if $Event.isExternalRSVP() %>true<% else %>false<% end_if %>,
                                rsvp_seat_type  : "{$Event.CurrentRSVPSubmissionSeatType}",
                                avg_rate        : "{$Event.getAvgRate()}",
                                allow_feedback  : <% if Event.AllowFeedBack %>true<% else %>false<% end_if %>,
                                has_ended       : <% if Event.hasEnded %>true<% else %>false<% end_if %>,
                                <% if $Event.ClassName == 'Presentation' %>
                                    moderator_id: {$Event.ModeratorID},
                                    speakers_id : [<% loop $Event.Speakers %>{$ID},<% end_loop %>],
                                    level       : '{$Event.Level}',
                                    to_record   : {$Event.ToRecord},
                                <% end_if %>

                                <% if $CurrentMember && $CurrentMember.isOnMySchedule($Top.Event.ID) %>
                                    going      : true,
                                <% else %>
                                    going      : false,
                                <% end_if %>
                                <% if $CurrentMember && $CurrentMember.isOnFavorites($Top.Event.ID) %>
                                    favorite : true,
                                <% else %>
                                    favorite : false,
                                <% end_if %>
                               comments : [],
                           };

                           <% loop $Event.getFeedback() %>
                                event.comments.push(
                                    {
                                        rate:  "{$getRate.JS}",
                                        date : "{$getDateNice.JS}",
                                        note : "{$getNote.JS}",
                                    });
                            <% end_loop %>
                    </script>
                    <event-action-buttons event="{ event }" current_user="{ current_user }"></event-action-buttons>
                <div class="row info_item">
                    <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-clock-o icon-clock"></i></div>
                    <div class="col-md-10 col-xs-10 info_item_text">$Event.DateNice()</div>
                </div>
                <% if Event.Summit.ShouldShowVenues %>
                    <div class="row info_item">
                        <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-map-marker icon-map"></i></div>
                        <div class="col-md-10 col-xs-10 info_item_text">
                            <a href="{$Event.Summit.Link}venues/#venue={$Event.Location.Venue().ID}" > $Event.LocationNameNice() </a>
                        </div>
                    </div>
                <% end_if %>
                <% if Event.ClassName == 'SummitEventWithFile' && $Event.Attachment().Exists() %>
                    <div class="row info_item">
                        <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-download icon-attachment"></i></div>
                        <div class="col-md-10 col-xs-10 info_item_text">
                            <a href="{$Event.Attachment().getUrl()}" target="_blank"> Attachment </a>
                        </div>
                    </div>
                <% end_if %>
                <% if $Event.isPresentation %>
                    <% if $Event.ToRecord || $Event.getVideoLink() %>
                        <div class="row info_item">
                            <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-2x fa-video-camera icon-record"></i></div>
                            <% if $Event.getVideoLink() %>
                                <div class="col-md-10 col-xs-10 info_item_text">
                                    <a href="{$Event.getVideoLink()}" target="_blank">View video</a>
                                </div>
                            <% else %>
                                <div class="col-md-10 col-xs-10 info_item_text">Will be recorded</div>
                            <% end_if %>
                        </div>
                    <% end_if %>
                    <div class="row info_item">
                        <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-2x fa-signal icon-level"></i></div>
                        <div class="col-md-10 col-xs-10 info_item_text">Level: $Event.Level</div>
                    </div>
                <% end_if %>

                <% if $Event.Tags %>
                    <div class="row info_item">
                        <div class="col-md-2 col-xs-2 info_item_icon"><i class="fa fa-tags"></i></div>
                        <div class="col-md-10 col-xs-10 info_item_text">
                            Tags:
                            <% loop $Event.Tags %>
                                <a href="$Top.Link(global-search)?t={$Tag}">$Tag</a>
                            <% end_loop %>
                        </div>
                    </div>
                <% end_if %>
                <div class="clearfix"></div>

                <% if Event.Sponsors %>
                    <div class="logo">
                        <% loop Event.Sponsors %>
                            <% if TotalItems = 1 %>
                                $LargeLogoPreview()
                            <% else %>
                                $SidebarLogoPreview(100)
                            <% end_if %>
                        <% end_loop %>
                    </div>
                <% end_if %>
                <div class="share">
                    <script type="application/javascript">
                        var share_info =
                                {
                                    fb_app_name: "OpenStack",
                                    fb_app_id : "{$SiteConfig.getOGApplicationID()}",
                                    token: "{$Token}",
                                    event_id: {$Event.ID},
                                    event_title: "{$Event.Title}",
                                    event_url: "{$Event.getLink(show)}",
                                    event_description: "{$Event.Description.JS}",
                                    social_summary : "{$Event.SocialSummary.JS}",
                                    event_pic_url: "{$Event.getOGImage}",
                                    tweet: '<%t Summit.TweetText %>'
                                };
                    </script>
                    <share-buttons share_info="{ share_info }"></share-buttons>
                </div>
            </div>
        </div>
    </div>
</div>


<% if Event.allowSpeakers %>
    <div class="speaker_box">
        <div class="container">
            <% loop Event.getSpeakersAndModerators() %>
                <div class="row speaker_profile">
                    <div class="speaker-photo-left">
                        <a class="profile-pic-wrapper" href="{$Top.AbsoluteLink}speakers/{$ID}" target="_blank" style="background-image: url('$ProfilePhoto(100)')"></a>
                    </div>
                    <div class="speaker_info">
                        <div class="speaker_name">
                            <a href="{$Top.AbsoluteLink}speakers/{$ID}" title="$FirstName $LastName" target="_blank">$FirstName $LastName</a>
                            <% if $Top.Event.isModeratorByID($ID) %>&nbsp;<span class="label label-info">Moderator</span><% end_if %>
                        </div>
                        <div class="speaker_job_title"> $getTitleNice() </div>
                        <div class="speaker_bio"> $getShortBio(400) <a href="{$Top.AbsoluteLink}speakers/{$ID}"> FULL PROFILE</a></div>
                    </div>
                </div>
            <% end_loop %>
        </div>
    </div>
<% end_if %>

<feedback-form-comments current_user="{ current_user }" event="{ event }" limit="5"></feedback-form-comments>

$ModuleJS('event-detail')
<div id="fb-root"></div>

