<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=$Tab %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/schedule">Events</a></li>
            <li class="active"><% if $Event %> $Event.Title <% else %> New Event <% end_if %></li>
        </ol>
        <form id="edit-event-form" enctype="multipart/form-data">
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
                <label for="abstract">Short Description/Abstract</label>
                <textarea id="abstract" name="abstract" class="form-control html_text">{$Event.Abstract}</textarea>
            </div>
            <div class="form-group">
                <label for="social_summary">Social Summary (100 chars)</label>
                <textarea id="social_summary" name="social_summary" class="form-control">{$Event.SocialSummary}</textarea>
            </div>
            <div class="form-group" id="expect_learn_container" style="display: none;">
                    <label for="expect_learn">What can attendees expect to learn?</label>
                    <textarea id="expect_learn" name="expect_learn" class="form-control html_text">{$Event.AttendeesExpectedLearnt}</textarea>
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
                    <div class="col-md-4">
                        <label for="event_type">Event Type</label>
                        <select class="form-control" id="event_type" name="event_type">
                            <option value="">-- Select a Type --</option>
                            <% loop Summit.EventTypes() %>
                                    <option data-use-sponsors="$UseSponsors"
                                            data-sponsors-mandatory="$AreSponsorsMandatory"
                                            data-type-taxonomy="$Top.getTypeTaxonomy($Type, $AllowsAttachment)"
                                            <% if $Top.IsPresentationEventType($Type) %>
                                            data-use-speakers="$UseSpeakers"
                                            data-speakers-mandatory="$AreSpeakersMandatory"
                                            data-use-moderator="$UseModerator"
                                            data-moderator-mandatory="$IsModeratorMandatory"
                                            data-moderator-label="<%if $ModeratorLabel != '' %>$ModeratorLabel<% else %>Moderator<% end_if %>"
                                            <% end_if %>
                                            value="$ID" >$Type</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-4 track_container">
                        <label for="track">Track</label>
                        <select class="form-control" id="track" name="track">
                            <option value="">-- Select a Track --</option>
                            <% loop Summit.getCategories %>
                                <option value="$ID" <% if $Top.Event.CategoryID == $ID %> selected <% end_if %>>$Title</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-4 level_container" style="display:none;">
                        <label for="level">Level</label>
                        <select class="form-control" id="level" name="level">
                            <option value="">-- Select a Level --</option>
                            <option value="N/A" <% if $Top.Event.isPresentation() && $Top.Event.Level == N/A %> selected <% end_if %>>N/A</option>
                            <option value="Beginner" <% if $Top.Event.isPresentation() && $Top.Event.Level == Beginner %> selected <% end_if %>>Beginner</option>
                            <option value="Intermediate" <% if $Top.Event.isPresentation() && $Top.Event.Level == Intermediate %> selected <% end_if %>>Intermediate</option>
                            <option value="Advanced" <% if $Top.Event.isPresentation() && $Top.Event.Level == Advanced %> selected <% end_if %>>Advanced</option>
                        </select>
                    </div>
                </div>
            </div>
            <% if $Top.Event.ExtraAnswers() %>
            <hr>
            <div class="form-group">
                <label>Track Questions</label>
                <% loop $Top.Event.ExtraAnswers() %>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            $Question().Label
                            $getQuestionField(true)
                        </div>
                    </div>
                <% end_loop %>
            </div>
            <hr>
            <% end_if %>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Feedback</label><br>
                        <div class="checkbox">
                            <input type="checkbox" id="allow_feedback" name="allow_feedback" <% if $Event.AllowFeedBack %> checked <% end_if %>>
                            <label for="allow_feedback">
                                Allow feedback ?
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 to_record_container" style="display: none;">
                        <label>Record</label><br>
                        <div class="checkbox">
                            <input type="checkbox" id="to_record" name="to_record" <% if $Event.ToRecord %> checked <% end_if %>>
                            <label for="to_record">
                                To record ?
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Attending Media</label><br>
                        <div class="checkbox">
                            <input type="checkbox" id="attending_media" name="attending_media" <% if $Event.AttendingMedia %> checked <% end_if %>>
                            <label for="attending_media">
                                Allow attending media ?
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group tag-container">
                <div class="row">
                    <div class="col-md-12">
                        <label for="tags">Tags</label><br>
                        <input id="tags" name="tags"/>
                    </div>
                </div>
            </div>
            <div class="form-group sponsors-container" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <label for="sponsors">Sponsors</label><br>
                        <input id="sponsors" name="sponsors"/>
                    </div>
                </div>
            </div>
            <div class="form-group groups_container" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <label for="groups">Groups</label><br>
                        <input id="groups" name="groups"/>
                    </div>
                </div>
            </div>
            <div class="form-group attachment_container" style="display:none;">
                <div class="row">
                    <div class="col-md-6">
                        <label for="event_attachment">Attachment</label><br>
                        <% if $Event.Attachment %>
                            <div class="attachment-container">$Event.Attachment.CMSThumbnail&nbsp;$Event.Attachment.Name</div>
                        <% end_if %>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    Change… <input type="file" id="event-attachment" name="event-attachment">
                                </span>
                            </span>
                            <input id="attachment-filename" type="text" class="form-control" readonly="">
                            <input id="attachment-id" name="attachment-id" type="hidden" value="{$Event.AttachmentID}">
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var speakers          = [];
                var moderator         = {};
                var summit_begin_date = '{$Summit.getSummitBeginDate("Y-m-d")}';
                var summit_end_date   = '{$Summit.getSummitEndDate("Y-m-d")}';
                var summit_start_time = '{$Summit.getSummitBeginDate("H:i:s")}';
            </script>

            <div class="form-group speakers-container" style="display:none;">
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
            <div class="form-group moderator-container" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <label for="moderator" class="moderator-label">
                            Moderator
                        </label><br>
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
                <a href="{$Event.getLink(show)}" class="btn btn-default" target="_blank">View Event</a>
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

        var groups = [];
        <% if $Event.Groups() %>
            <% loop $Event.Groups() %>
                groups.push({id : "{$ID}", name : "{$Title.JS}"});
            <% end_loop %>
        <% end_if %>

        $(document).ready(function(){
            <% if $Top.Event %>
                // update the event types availables on combo filtering
                // by the current event type

                var event_type_id = $Top.Event.Type.ID;
                // set current
                $('#event_type').val(event_type_id).change();
                // and get type to filter by
                var taxonomy           = $Top.getTypeTaxonomy($Top.Event.Type.Type, $Top.Event.Type.AllowsAttachment);
                $("#event_type").find("option").each(function(){
                    var item_taxonomy = $(this).data('type-taxonomy');
                    if(typeof (item_taxonomy) == 'undefined') return;
                    if(item_taxonomy == taxonomy) {
                        $(this).removeAttr('disabled');
                    }
                    else{
                        $(this).attr('disabled', 'disabled');
                    }
                });


            <% end_if %>
        });
    </script>


</div>