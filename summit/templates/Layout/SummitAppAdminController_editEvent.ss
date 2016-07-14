<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=$Tab %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-event-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/schedule">Events</a></li>
            <li class="active"><% if $Event %> $Event.Title <% else %> New Event <% end_if %></li>
        </ol>
        <form id="edit-event-form">
            <input type="hidden" id="summit_id" value="{$Summit.ID}" />
            <input type="hidden" id="event_id"  value="{$Event.ID}" />
            <input type="hidden" id="published" value="{$Event.IsPublished}" />
            <% if $Event %>
            <div class="form-group">
                <span><% if $Event.IsPublished %>Published<% else %>Not Published<% end_if %></span>
            </div>
            <% end_if %>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{$Event.Title}">
            </div>
            <div class="form-group">
                <label for="short_description">Short Description/Abstract</label>
                <textarea id="short_description" name="short_description" class="form-control">{$Event.ShortDescription}</textarea>
            </div>
            <div class="form-group" id="expect_learn_container" style="display: none;">
                    <label for="expect_learn">What can attendees expect to learn?</label>
                    <textarea id="expect_learn" name="expect_learn" class="form-control">{$Event.AttendeesExpectedLearnt}</textarea>
            </div>
            <div class="form-group">
                <label for="headcount">Head Count</label>
                <input type="number" class="form-control" id="headcount" name="headcount" value="{$Event.HeadCount}">
            </div>
            <div class="form-group">
                <label for="rsvp_link">RSVP Link</label>
                <input type="text" class="form-control" id="rsvp_link" name="rsvp_link" value="{$Event.RSVPLink}">
            </div>
            <div class="form-group">
                <label>Location</label><br>
                <div class="row">
                    <div class="col-md-6">
                        <select hidden class="form-control" id="location" name="location">
                            <option value=''>-- Select A Venue --</option>
                            <option value="0" <% if $Top.Event.LocationID = 0 %> selected <% end_if %>>TBA</option>
                            <% loop Summit.getTopVenues() %>
                                <option value="$ID"
                                    <% if $ClassName == 'SummitVenue' %>
                                        class="location-venue" title="Venue"
                                     <% else_if $ClassName == 'SummitHotel' %>
                                        class="location-hotel" title="Hotel"
                                     <% else_if $ClassName == 'SummitExternalLocation' %>
                                        class="location-external" title="External Location"
                                    <% end_if %>
                                    <% if $Top.Event.LocationID = $ID %> selected <% end_if %>
                                >$Name</option>
                                <% if $ClassName == 'SummitVenue' %>
                                    <% loop Rooms().sort('Name', 'ASC') %>
                                        <option value="$ID" title="Venue Room" class="location-venue-room"<% if $Top.Event.LocationID = $ID %> selected <% end_if %>>$Name</option>
                                    <% end_loop %>
                                <% end_if %>
                            <% end_loop %>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="left-inner-addon">
                            <span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true"></span>
                            <input type="text" class="form-control" placeholder="Start Date" autocomplete="off" readonly="readonly" id="start_date" name="start_date" value="$Event.StartDate"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="left-inner-addon">
                            <span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true"></span>
                            <input type="text" class="form-control" placeholder="End Date" autocomplete="off" readonly="readonly" id="end_date" name="end_date" value="$Event.EndDate"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="event_type">Event Type</label>
                        <select class="form-control" id="event_type" name="event_type">
                            <option value="">-- Select a Type --</option>
                            <% loop Summit.EventTypes() %>
                                <% if $Top.Event %>
                                    <% if $Top.Event.isPresentation() %>
                                        <% if $Top.IsPresentationEventType($Type) %>
                                            <option value="$ID" <% if $Top.Event.Type.ID == $ID %>selected<% end_if %> >$Type</option>
                                        <% end_if %>
                                    <% else %>
                                        <% if  $Top.IsSummitEventType($Type) %>
                                            <option value="$ID" <% if $Top.Event.Type.ID == $ID %>selected<% end_if %> >$Type</option>
                                        <% end_if %>
                                    <% end_if %>
                                <% else %>
                                    <option value="$ID" <% if $Top.Event.Type.ID == $ID %>selected<% end_if %> >$Type</option>
                                <% end_if %>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="summit_type">Summit Type</label>
                        <select class="form-control" id="summit_type" name="summit_type" multiple>
                            <% loop Summit.Types() %>
                                <option value="$ID" <% if $Top.Event.isAllowedSummitType($ID) %> selected <% end_if %>>$Title</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-3 track_container" style="display:none">
                        <label for="track">Track</label>
                        <select class="form-control" id="track" name="track">
                            <option value="">-- Select a Track --</option>
                            <% loop Summit.Categories() %>
                                <option value="$ID" <% if $Top.Event.isPresentation() && $Top.Event.CategoryID == $ID %> selected <% end_if %>>$Title</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-3 level_container" style="display:none;">
                        <label for="level">Level</label>
                        <select class="form-control" id="level" name="level">
                            <option value="">-- Select a Level --</option>
                            <option value="Beginner" <% if $Top.Event.isPresentation() && $Top.Event.Level == Beginner %> selected <% end_if %>>Beginner</option>
                            <option value="Intermediate" <% if $Top.Event.isPresentation() && $Top.Event.Level == Intermediate %> selected <% end_if %>>Intermediate</option>
                            <option value="Advanced" <% if $Top.Event.isPresentation() && $Top.Event.Level == Advanced %> selected <% end_if %>>Advanced</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div classs="row">
                    <div class="col-md-12">
                        <label>Feedback</label>
                        <div class="checkbox">
                            <label> <input id="allow_feedback" name="allow_feedback" type="checkbox" <% if $Event.AllowFeedBack %> checked <% end_if %>> Allow Feedback </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="tags">Tags</label><br>
                        <input id="tags" name="tags"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="sponsors">Sponsors</label><br>
                        <input id="sponsors" name="sponsors"/>
                    </div>
                </div>
            </div>
            <script>
                var speakers          = [];
                var moderator         = {};
                var summit_begin_date = '{$Summit.BeginDateYMD}';
                var summit_end_date   = '{$Summit.EndDateYMD}';
                var summit_start_time = '{$Summit.BeginTime}';
            </script>

            <div class="form-group speakers_container" style="display:none;">
                <div class="row">
                    <div class="col-md-10">
                        <label for="speakers">Speakers</label><br>
                        <input id="speakers" name="speakers"/>
                    </div>
                    <div class="col-md-2">
                        <a href="mailto:" id="email-speakers" class="btn btn-default" target="_blank">Contact</a>
                    </div>
                </div>
            </div>
            <div class="form-group moderator_container" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <label for="moderator">Moderator</label><br>
                        <input id="moderator" name="moderator"/>
                    </div>
                </div>
            </div>
            <script>
                <% if $Event && $Event.isPresentation && $Event.Speakers() %>
                    <% loop $Event.Speakers() %>
                    speakers.push({
                        unique_id : "{$MemberID}_{$ID}",
                        name : "{$FirstName.JS} {$LastName.JS}  ({$getEmail})",
                        speaker_id : $ID,
                        member_id: $MemberID,
                        email:"$getEmail"
                    });
                    <% end_loop %>
                <% end_if %>
                <% if $Event && $Event.isPresentation && $Event.Moderator().Exists %>
                moderator = {
                                unique_id : "{$Event.Moderator.MemberID}_{$Event.Moderator.ID}",
                                name : "{$Event.Moderator.FirstName.JS} {$Event.Moderator.LastName.JS}  ({$Event.Moderator.getEmail})",
                                speaker_id : $Event.Moderator.ID,
                                member_id:  $Event.Moderator.MemberID,
                                email: "$Event.Moderator.getEmail"
                            };
                <% end_if %>
            </script>
            <% if $Event%>
                <% if $Event.isPublished %>
                <button id="btn_publish" class="btn btn-primary btn-success">Save & Publish</button>
                <% else %>
                <button id="btn_save" class="btn btn-primary">Save</button>
                <button id="btn_publish" class="btn btn-primary btn-success">Save & Publish</button>
                <% end_if %>
            <% else %>
                <button id="btn_save" class="btn btn-primary">Save</button>
                <button id="btn_publish" class="btn btn-primary btn-success">Save & Publish</button>
            <% end_if %>
            <% if $Event.isPublished() %>
                <button id="btn_unpublish" class="btn btn-primary btn-danger">UnPublish</button>
                <a href="{$Top.Link()}/{$Summit.ID}/events/schedule/#day={$Event.StartDate.Format('Y-m-d')}&venue={$Event.LocationID}&event={$Event.ID}" class="btn btn-default">Go to Calendar</a>
                <a href="{$Event.getLink()}" class="btn btn-default" target="_blank">View Event</a>
            <% end_if %>
        </form>
    </div>
    <!-- /#page-content-wrapper -->

    <script>

        var tags = [];
        <% if $Event.Tags() %>
            <% loop $Event.Tags() %>
            tags.push({id : "{$ID}", name : "{$Tag.JS}"});
            <% end_loop %>
        <% end_if %>

        var sponsors = [];
        <% if $Event.Sponsors() %>
            <% loop $Event.Sponsors() %>
            sponsors.push({id : "{$ID}", name : "{$Name.JS}"});
            <% end_loop %>
        <% end_if %>

        $(document).ready(function(){
            $("#location").chosen();
            $("#event_type").chosen();
            <% if $Top.Event %>
                <% if $Top.Event.Type.Type == 'Presentation' || $Top.Event.Type.Type == 'Keynotes' %>
                    $('.speakers_container').show();
                    $('.track_container').show();
                    $('.level_container').show();
                    $('#expect_learn_container').show();
                <% end_if %>
                <% if $Top.Event.Type.Type == 'Keynotes' %>
                    $('.moderator_container').show();
                <% end_if %>
            <% end_if %>
        });
    </script>


</div>