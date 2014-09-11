<div>
    <h1>Add New Press Release</h1>
    <p></p>
</div>

<% if Saved %>
    <div class="siteMessage" id="SuccessMessage" style="padding: 10px;">
        <p style="float:left;">Your Event has been saved!</p>
        <input type="button" title="Add New Event" value="Add New Event" data-url="{$Top.Link}" name="add-new-event" id="add-new-event" class="action">
    </div>
<% else %>
<div>
    $NewsRegistrationRequestForm
</div>
<% end_if %>