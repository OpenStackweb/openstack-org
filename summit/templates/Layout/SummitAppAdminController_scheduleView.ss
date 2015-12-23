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
            <li class="active">Schedule</li>
        </ol>
        <div class="row">
            <div class="col-md-6">
                <table id="day_schedule">
                    <tbody>
                    <tr>
                        <td class="times-col">
                            <div class="time-slot">07:00 AM</div>
                            <div class="time-slot">07:15 AM</div>
                            <div class="time-slot">07:30 AM</div>
                            <div class="time-slot">07:45 AM</div>
                            <div class="time-slot">08:00 AM</div>
                            <div class="time-slot">08:15 AM</div>
                            <div class="time-slot">08:30 AM</div>
                            <div class="time-slot">08:45 AM</div>
                            <div class="time-slot">09:00 AM</div>
                            <div class="time-slot">09:15 AM</div>
                            <div class="time-slot">09:30 AM</div>
                            <div class="time-slot">09:45 AM</div>
                            <div class="time-slot">10:00 AM</div>
                            <div class="time-slot">10:15 AM</div>
                            <div class="time-slot">10:30 AM</div>
                            <div class="time-slot">10:45 AM</div>
                            <div class="time-slot">11:00 AM</div>
                            <div class="time-slot">11:15 AM</div>
                            <div class="time-slot">11:30 AM</div>
                            <div class="time-slot">11:45 AM</div>
                            <div class="time-slot">12:00 PM</div>
                            <div class="time-slot">12:15 PM</div>
                            <div class="time-slot">12:30 PM</div>
                            <div class="time-slot">12:45 PM</div>
                            <div class="time-slot">01:00 PM</div>
                            <div class="time-slot">01:15 PM</div>
                            <div class="time-slot">01:30 PM</div>
                            <div class="time-slot">01:45 PM</div>
                            <div class="time-slot">02:00 PM</div>
                        </td>
                        <td class="events-col">
                            <div class="time-slot-container" data-time="07:00"></div>
                            <div class="time-slot-container" data-time="07:15"></div>
                            <div class="time-slot-container" data-time="07:30"></div>
                            <div class="time-slot-container" data-time="07:45"></div>
                            <div class="time-slot-container" data-time="08:00"></div>
                            <div class="time-slot-container" data-time="08:15"></div>
                            <div class="time-slot-container" data-time="08:30"></div>
                            <div class="time-slot-container" data-time="08:45"></div>
                            <div class="time-slot-container" data-time="09:00"></div>
                            <div class="time-slot-container" data-time="09:15"></div>
                            <div class="time-slot-container" data-time="09:30"></div>
                            <div class="time-slot-container" data-time="09:45"></div>
                            <div class="time-slot-container" data-time="10:00"></div>
                            <div class="time-slot-container" data-time="10:15"></div>
                            <div class="time-slot-container" data-time="10:30"></div>
                            <div class="time-slot-container" data-time="10:45"></div>
                            <div class="time-slot-container" data-time="11:00"></div>
                            <div class="time-slot-container" data-time="11:15"></div>
                            <div class="time-slot-container" data-time="11:30"></div>
                            <div class="time-slot-container" data-time="11:45"></div>
                            <div class="time-slot-container" data-time="12:00"></div>
                            <div class="time-slot-container" data-time="12:15"></div>
                            <div class="time-slot-container" data-time="12:30"></div>
                            <div class="time-slot-container" data-time="12:45"></div>
                            <div class="time-slot-container" data-time="13:00"></div>
                            <div class="time-slot-container" data-time="13:15"></div>
                            <div class="time-slot-container" data-time="13:30"></div>
                            <div class="time-slot-container" data-time="13:45"></div>
                            <div class="time-slot-container" data-time="14:00"></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
            </div>
        </div>
        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>

    </div>
    <!-- /#page-content-wrapper -->
</div>