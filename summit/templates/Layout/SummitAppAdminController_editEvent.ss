<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/published">Published Events</a></li>
            <li class="active">Working Group Session: Community App Catalog</li>
        </ol>

        <form>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" value="{$Event.Title}">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" class="form-control">{$Event.Description}</textarea>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>

                    <div class="btn-group">
                        <button id="location" type="button" class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Location <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Room#1 @ Venue #1</a></li>
                            <li><a href="#">Room#2 @ Venue #1</a></li>
                            <li><a href="#">Room#3 @ Venue #1</a></li>
                        </ul>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="left-inner-addon">
                                                    <span class="glyphicon glyphicon glyphicon-calendar"
                                                          aria-hidden="true"></span>
                                <input type="text" class="form-control" placeholder="Start Date"
                                       id="start-date"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="left-inner-addon ">
                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                <input type="text" class="form-control" placeholder="Start Time"
                                       id="start-time"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="left-inner-addon">
                                                    <span class="glyphicon glyphicon glyphicon-calendar"
                                                          aria-hidden="true"></span>
                                <input type="text" class="form-control" placeholder="End Date"
                                       id="end-date"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="left-inner-addon ">
                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                <input type="text" class="form-control" placeholder="End Time"
                                       id="end-time"/>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
    </div>
    <!-- /#page-content-wrapper -->

</div>