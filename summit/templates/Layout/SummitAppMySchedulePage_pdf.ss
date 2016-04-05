
<h1 style="text-align:left;">My Schedule</h1>
<hr/>

<% loop $Schedule %>
    <div class="panel-heading">{$Day}</div>
    <table class="table" cellspacing="0">
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
            <tr nobr="true">
                <td style="width:20%">{$getStartTime} - {$getEndTime}</td>
                <td style="width:45%">{$Title}</td>
                <td style="width:15%"><% if $Summit.ShouldShowVenues %> {$getLocationNameNice} <% else %> TBD <% end_if %></td>
                <td style="width:12%">{$Attendees.Count()}/{$LocationCapacity}</td>
                <td style="width:8%"><% if $RSVPLink %> Yes <% else %> No <% end_if %></td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
    <br><br>
<% end_loop %>
