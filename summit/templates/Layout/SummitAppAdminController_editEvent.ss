<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-event-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/schedule">Events</a></li>
            <li class="active">$Event.Title</li>
        </ol>

        <form id="edit-event-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="event_id" value="$Event.ID" />

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" value="{$Event.Title}">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" class="form-control">{$Event.Description}</textarea>
            </div>
            <div class="form-group">
                <label for="short_description">Short Description</label>
                <textarea id="short_description" class="form-control">{$Event.ShortDescription}</textarea>
            </div>

            <div class="form-group">
                <label>Location</label><br>
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" id="location">
                            <option hidden value="">Select a Venue</option>
                            <% loop Summit.Locations() %>
                                <option value="$ID" <% if $Top.Event.LocationID = $ID %> selected <% end_if %>>$Name</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="left-inner-addon">
                            <span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true"></span>
                            <input type="text" class="form-control" placeholder="Start Date" id="start_date" value="$Event.StartDate"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="left-inner-addon">
                            <span class="glyphicon glyphicon glyphicon-calendar" aria-hidden="true"></span>
                            <input type="text" class="form-control" placeholder="End Date" id="end_date" value="$Event.EndDate"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="event_type">Event Type</label>
                        <select class="form-control" id="event_type">
                            <option hidden value="">Select a Type</option>
                            <% loop Summit.EventTypes() %>
                                <option value="$ID" <% if $Top.Event.TypeID = $ID %> selected <% end_if %>>$Type</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="summit_type">Summit Type</label>
                        <select class="form-control" id="summit_type" multiple>
                            <option hidden value="">Select a Summit Type</option>
                            <% loop Summit.Types() %>
                                <option value="$ID" <% if $Top.Event.isAllowedSummitType($Title) %> selected <% end_if %>>$Title</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Feedback</label>
                        <div class="checkbox">
                            <label> <input id="allow_feedback" type="checkbox" <% if $Event.AllowFeedBack %> checked <% end_if %>> Allow Feedback </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="tags">Tags</label><br>
                        <input id="tags" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="sponsors">Sponsors</label><br>
                        <input id="sponsors" />
                    </div>
                </div>
            </div>
            <script>
                var speakers = [];
            </script>
            <% if $Event.isPresentation() %>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="speakers">Speakers</label><br>
                        <input id="speakers" />
                    </div>
                </div>
            </div>
            <script>
                <% loop $Event.Speakers() %>
                speakers.push({id : "{$MemberID}", name : "{$FirstName.JS} {$LastName.JS}  ({$MemberID})"});
                <% end_loop %>
            </script>
            <% end_if %>

            <button type="submit" class="btn btn-primary">Save</button>
            <% if $Event.isPublished() %>
            <a href="{$Top.Link()}/{$Summit.ID}/events/schedule/#day={$Event.StartDate.Format('Y-m-d')}&venue={$Event.LocationID}&event={$Event.ID}" class="btn btn-default">Go to Calendar</a>
            <% end_if %>
        </form>
    </div>
    <!-- /#page-content-wrapper -->

    <script>
        var tags = [];
        <% loop $Event.Tags() %>
        tags.push({id : "{$ID}", name : "{$Tag.JS}"});
        <% end_loop %>

        var sponsors = [];
        <% loop $Event.Sponsors() %>
        sponsors.push({id : "{$ID}", name : "{$Name.JS}"});
        <% end_loop %>
    </script>


</div>