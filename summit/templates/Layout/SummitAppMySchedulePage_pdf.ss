<div class="container-fluid">
    <div class="container">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-6 col-main-title">
                <h1 style="text-align:left;">My Schedule</h1>
            </div>
        </div>
        <hr/>

        <% loop $Schedule %>
        <div class="panel">
            <div class="panel-heading">{$Day}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Room</th>
                        <th>Vacancy</th>
                        <th>RSVP</th>
                    </tr>
                </thead>
                <tbody>
                    <% loop $Events %>
                    <tr>
                        <td style="width:100px">{$getStartTime} - {$getEndTime}</td>
                        <td style="width:260px">{$Title}</td>
                        <td style="width:100px"><% if $Summit.ShouldShowVenues %> {$getLocationNameNice} <% else %> TBD <% end_if %></td>
                        <td style="width:60px">{$Attendees.Count()}/{$LocationCapacity}</td>
                        <td style="width:20px"><% if $RSVPLink %> Yes <% else %> No <% end_if %></td>
                    </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
        <% end_loop %>
    </div>
</div>