<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='schedule' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <h2>Summit Events Bulk Edition</h2>
        <% if Events %>
        <form id="events-form" name="events-form">
            <input type="hidden" name="summit_id" id="summit_id" value="{$Summit.ID}"/>
            <ul class="list-unstyled">
                <li>
                <div class="row row-space" data-event-id="{$ID}">
                    <div class="col-md-1">
                        <div class="row">
                            <div class="col-md-12" style="min-height: 50px">
                                &nbsp;
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>ID</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12" style="min-height: 50px">
                                &nbsp;
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Event Name</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-12" style="min-height: 50px">
                                <select class="form-control" id="location_all" name="location_all">
                                    <option value=''>Select A Location For All</option>
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Location</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12" style="min-height: 50px">
                                    <div class="input-group date">
                                        <input data-rule-date="true"  type="text" id="start_date_all" name="start_date_all" class="form-control" value="">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Start Date</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12" style="min-height: 50px">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Start Time</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12" style="min-height: 50px">
                                    <div class="input-group date">
                                        <input data-rule-date="true"  type="text" id="end_date_all" name="end_date_all" class="form-control" value="">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>End Date</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12" style="min-height: 50px">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>End Time</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <% loop Events %>
                <li>
                    <div class="row row-space row-event" data-event-id="{$ID}">
                        <div class="col-md-1"><a href="summit-admin/6/events/$ID" title="edit event">$ID</a></div>
                        <div class="col-md-4">$Title</div>
                        <div class="col-md-2">
                            <select data-rule-required="true"  class="form-control location" id="location_{$ID}" name="location_{$ID}">
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
                                        <input data-rule-datetimeGreaterThan="start_time_{$ID},end_date_{$ID},end_time_{$ID}" data-rule-date="true"  data-rule-required="true" type="text" id="start_date_{$ID}" name="start_date_{$ID}"class="form-control start-date" value="{$BeginDateYMD}">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group clockpicker">
                                        <input data-rule-time12="true" data-rule-required="true" type="text" id="start_time_{$ID}" name="start_time_{$ID}" class="form-control start-time" value="{$StartTimeHMS}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <input data-rule-date="true" data-rule-required="true" type="text" id="end_date_{$ID}" name="end_date_{$ID}" class="form-control end-date" value="{$EndDateYMD}">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group clockpicker">
                                        <input data-rule-time12="true" data-rule-required="true" type="text" id="end_time_{$ID}" name="end_time_{$ID}" class="form-control end-time" value="{$EndTimeHMS}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                </li>
            <% end_loop %>
        </ul>
        </form>
        <ul id="actions_to_resolve" display="none">
        </ul>
        <% if UnpublishedEvents %>
        <button id="apply_changes" name="apply_changes" type="button" class="btn-apply-bulk-action btn btn-primary btn-lg">Apply Changes</button>
        <% end_if %>
        <button id="apply_changes_publish" name="apply_changes_publish" type="button" class="btn-apply-bulk-action btn btn-success btn-lg">Apply Changes & Publish!</button>
        <% end_if %>
    </div>
    <script>
        $('.input-group.date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            startDate: "{$Summit.BeginDateYMD}",
            endDate: "{$Summit.EndDateYMD}",
        });
    </script>
    <script src="summit/javascript/schedule/admin/schedule-admin-view.bundle.js"  type="application/javascript"></script>
</div>