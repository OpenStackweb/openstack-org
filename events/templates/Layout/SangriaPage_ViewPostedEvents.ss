<h2 style="float:left;margin-top:10px;">Posted Event List ($PostedEventsCount)</h2>
<div style="float:right;">
    <a href="#" class="add-live-event roundedButton addDeploymentBtn">Create Event</a>
</div>
<% if PostedEvents %>
    <table id="event-registration-requests-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Url</th>
                <th>Category</th>
                <th>Location</th>
                <th>Sponsor</th>
                <th>Is Summit</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <% loop PostedEvents %>
            <tr>
                <td class="title"><a id="evt{$ID}" href="#"></a>$Title</td>
                <td class="start-date">$EventStartDate</td>
                <td class="start-date">$EventEndDate</td>
                <td class="url"><a href="$EventLink">Link</a></td>
                <td class="category">$EventCategory</td>
                <td class="location">$EventLocation</td>
                <td class="sponsor">$EventSponsor</td>
                <td class="summit"><input class="summit_check" event_id="{$ID}" type="checkbox" <% if IsSummit == 1 %> checked <% end_if %> /></td>
                <td width="17%">
                    <a href="#" data-event-id="{$ID}" class="edit-live-event roundedButton addDeploymentBtn">Edit</a>
                    &nbsp;
                    <a href="#" data-event-id="{$ID}" class="delete-live-event roundedButton addDeploymentBtn">Delete</a>
                </td>
            </tr>
        <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any Events.</p>
<% end_if %>
<div id="edit_live_dialog" title="Edit Event" style="display: none;">
    $EventForm
</div>

<div id="dialog-delete-post" title="Delete Post ?" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to delete this event?</p>
</div>