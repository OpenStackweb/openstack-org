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
            <li class="active">Unpublished Events</li>
        </ol>

        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>

        <label for="filter">Event Type</label>
        <div class="btn-group">
            <button id="filter" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                All<span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#">All</a></li>
                <li><a href="#">Sponsor Party</a></li>
                <li><a href="#">Break Out Session</a></li>
                <li><a href="#">Expo</a></li>
                <li><a href="#">Presentations</a></li>
            </ul>
        </div>
        <button type="button" class="btn btn-primary active btn-sm pull-right" data-toggle="modal" data-target="#modal-add-event">
            + Add Event
        </button>
        <table class="table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Type</th>
                <th>Location</th>
                <th>Attendance</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    1
                </td>
                <td>
                    <a href="$Top.Link/{$Summit.ID}/events/2" >Working Group Session: Community App Catalog</a>
                </td>
                <td>
                    May 20 - 11:50am
                </td>
                <td>
                    May 20 - 12:30am
                </td>
                <td>
                    Presentation
                </td>
                <td>
                    Room#1 @ Venue #1
                </td>
                <td>
                    <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>0
                </td>
                <td>
                    <a href="$Top.Link/{$Summit.ID}/events/1" class="btn btn-default btn-sm active" role="button">Edit</a>
                </td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-danger active btn-sm">Delete</button>
                </td>
            </tr>
            <tr>
                <td>
                    2
                </td>
                <td>
                    <a href="$Top.Link/{$Summit.ID}/events/2" >Working Group Session: Community App Catalog</a>
                </td>
                <td>
                    May 20 - 11:50am
                </td>
                <td>
                    May 20 - 12:30am
                </td>
                <td>
                    Presentation
                </td>
                <td>
                    Room#2 @ Venue #1
                </td>
                <td>
                    <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>0
                </td>
                <td>
                    <a href="$Top.Link/{$Summit.ID}/events/2" class="btn btn-default btn-sm active" role="button">Edit</a>
                </td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-danger active btn-sm">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <li class="disabled">
                    <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li  class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li>
                    <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Modal Publish -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Working Group Session: Community App Catalog</h4>
                    </div>
                    <div class="modal-body">
                        <form>
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

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Publish</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Add -->
        <div class="modal fade" id="modal-add-event" tabindex="-1" role="dialog" aria-labelledby="modal-add-event-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modal-add-event-label">Add New Event</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="event-type">Event Type</label>
                                <div class="btn-group" id="event-type">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--Event Type --<span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Summit Party</a></li>
                                        <li><a href="#">Sponsored Demo</a></li>
                                        <li><a href="#">Expo</a></li>
                                        <li><a href="#">Presentation</a></li>
                                        <li><a href="#">Lunch</a></li>
                                        <li><a href="#">Break</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="sponsors">Sponsors</label>
                                <select id="sponsors" multiple="" style="width:350px;" data-placeholder="Sponsors">
                                    <option value="1">Solinea</option>
                                    <option value="2">Rackspace</option>
                                    <option value="3">HP</option>
                                    <option value="3">IBM</option>
                                    <option value="3">Kio Networks</option>
                                </select>
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

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>