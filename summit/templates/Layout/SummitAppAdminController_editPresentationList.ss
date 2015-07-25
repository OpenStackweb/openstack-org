<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div>
    <!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">

        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/presentation-lists/">Presentation Lists</a></li>
            <li class="active">1</li>
        </ol>

        <div class="well well-sm"><strong>Cloudfunding: Startups and Capital</strong>&nbsp;
            <small>:&nbsp;List Name One #1</small>
        </div>


        <table class="table">
            <thead>
            <tr>
                <td># Order</td>
                <td>Presentation Name</td>
                <td>Speakers</td>
                <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>Openstack API support metric, documentation and testing</td>
                <td>John Due</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Crowbar for OpenStack Deployments</td>
                <td>John Due</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>OpenStack Service Day: Beach Cleanup</td>
                <td>John Due</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Entropy (or lack thereof) in OpenStack Instances</td>
                <td>John Due</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        Publish
                    </button>
                </td>
            </tr>
            </tbody>
        </table>


        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"> Openstack API support metric, documentation and
                            testing</h4>
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
    </div>
</div>