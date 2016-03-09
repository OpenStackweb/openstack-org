<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=3 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <h2>Summit Events Bulk Edition</h2>
        <% if Events %>
        <ul class="list-unstyled">
            <li>
                <div class="row row-space" data-event-id="{$ID}">
                    <div class="col-md-1"><strong>ID</strong></div>
                    <div class="col-md-4">
                        <strong>Event Name</strong>
                    </div>
                    <div class="col-md-2">
                        <strong>Location</strong>
                    </div>
                    <div class="col-md-5">  <div class="row">
                        <div class="col-md-3">
                            <strong>Start Date</strong>
                        </div>
                        <div class="col-md-3">
                            <strong>Start Time</strong>
                        </div>
                        <div class="col-md-3">
                            <strong>End Date</strong>
                        </div>
                        <div class="col-md-3">
                            <strong>End Time</strong>
                        </div>
                    </div>
                </div>
            </li>
            <% loop Events %>
                <li>
                    <div class="row row-space" data-event-id="{$ID}">
                        <div class="col-md-1">$ID</div>
                        <div class="col-md-4">$Title</div>
                        <div class="col-md-2">
                            <select class="form-control" id="location_{$ID}" name="location_{$ID}">
                                <option value=''>-- Select A Location --</option>
                                <option value="0">TBA</option>
                                <% loop Summit.getTopVenues() %>
                                    <option value="$ID"
                                        <% if $ClassName == 'SummitVenue' %>
                                            class="location-venue" title="Venue"
                                        <% else_if $ClassName == 'SummitHotel' %>
                                            class="location-hotel" title="Hotel"
                                        <% else_if $ClassName == 'SummitExternalLocation' %>
                                            class="location-external" title="External Location"
                                        <% end_if %>
                                    >$Name</option>
                                    <% if $ClassName == 'SummitVenue' %>
                                        <% loop Rooms().sort('Name', 'ASC') %>
                                            <option value="$ID" title="Venue Room" class="location-venue-room">$Name</option>
                                        <% end_loop %>
                                    <% end_if %>
                                <% end_loop %>
                            </select>
                            <script>
                                $('#location_{$ID}').val($LocationID);
                                $('#location_{$ID}').attr('data-original-location-id', $LocationID);
                            </script>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <input disabled="disabled" type="text" id="start_date_{$ID}" name="start_date_{$ID}"class="form-control" value="{$BeginDateYMD}">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group clockpicker">
                                        <input disabled="disabled" type="text" id="start_time_{$ID}" name="start_time_{$ID}" class="form-control" value="{$StartTimeHMS}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <input disabled="disabled" type="text" id="end_date_{$ID}" name="end_date_{$ID}"class="form-control" value="{$EndDateYMD}">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group clockpicker">
                                        <input disabled="disabled" type="text" id="end_time_{$ID}" name="end_time_{$ID}" class="form-control" value="{$EndTimeHMS}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $('.clockpicker').clockpicker({
                                    placement: 'bottom',
                                    align: 'left',
                                    autoclose: true,
                                    'default': 'now',
                                    twelvehour:  true,
                                });
                                $('.input-group.date').datepicker({
                                    autoclose: true,
                                    format: "yyyy-mm-dd",
                                    startDate: "{$Summit.BeginDateYMD}",
                                    endDate: "{$Summit.EndDateYMD}",
                                });
                            </script>
                        </div>
                     </div>
                </li>
            <% end_loop %>
        </ul>
        <% if UnpublishedEvents %>
        <button id="apply_changes" name="apply_changes" type="button" class="btn btn-primary btn-lg">Apply Changes</button>
        <% end_if %>
        <button id="apply_changes_publish" name="apply_changes_publish" type="button" class="btn btn-success btn-lg">Apply Changes & Publish!</button>
        <% end_if %>
    </div>
    <script src="summit/javascript/schedule/admin/schedule-admin-view.bundle.js"  type="application/javascript"></script>
</div>