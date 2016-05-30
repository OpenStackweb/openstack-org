<h1>Openstack Days Events</h1>

$FeaturedEventForm

<% if FeaturedEvents %>
    <br><br>
    <h2>Featured Events</h2>
    <table id="featured-event-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Image</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <% loop FeaturedEvents %>
            <tr>
                <td class="title"><a id="evt{$Event.ID}" href="#"></a>
                    $Event.Title
                </td>
                <td class="start-date">$Event.EventStartDate</td>
                <td class="start-date">$Event.EventEndDate</td>
                <td class="sponsor">$Picture.SetSize(100, 100)</td>
                <td width="17%">
                    <a href="#" data-event-id="{$ID}" class="delete-featured-event roundedButton addDeploymentBtn">Remove</a>
                </td>
            </tr>
        <% end_loop %>
        </tbody>
    </table>
<% end_if %>

<div id="dialog-delete-featured-post" title="Delete Featured Event ?" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to remove this event from featured?</p>
</div>