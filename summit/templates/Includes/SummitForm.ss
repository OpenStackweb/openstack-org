<% if IncludeFormTag %>
    <form $FormAttributes role="form">
<% end_if %>
<% if Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>

<input type="hidden" id="summitid" value="$Summit.ID">

<div class="row form-inline">
    <div class="col-md-4">
        <label for="{$FormName}_Name" class="left">Summit</label>
        $Fields.dataFieldByName(Name)
    </div>
    <div class="col-md-4">
        <label for="{$FormName}_SummitBeginDate" class="left">Begin Date</label>
        $Fields.dataFieldByName(SummitBeginDate)
    </div>
    <div class="col-md-4">
        <label for="{$FormName}_SummitEndDate" class="left">End Date</label>
        $Fields.dataFieldByName(SummitEndDate)
    </div>
</div>
<hr>
<div class="row form-inline">
    <div class="col-md-12">
        <label for="{$FormName}_EventTypes" class="left">Event Types</label>
        $Fields.dataFieldByName(EventTypes)
    </div>
</div>
<br>
<label>Summit Types</label>
<table id="summittype_table" class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Audience</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <% loop Summit.getTypes %>
        <tr data-summittypeid="$getIdentifier()">
            <td class="title">$getTitle()</td>
            <td class="description">$getDescription()</td>
            <td class="audience">$getAudience()</td>
            <td class="start_date">$getStartDate()</td>
            <td class="end_date">$getEndDate()</td>
            <td class="center_text buttons">
                <span class="edit glyphicon glyphicon-pencil"></span>
                <span class="delete glyphicon glyphicon-trash"></span>
                <span style="display:none;" class="update glyphicon glyphicon-ok"></span>
            </td>
        </tr>
        <% end_loop %>
        <tr>
            <td><input id="new_title"></input></td>
            <td><input id="new_description"></input></td>
            <td><input id="new_audience"></input></td>
            <td><input id="new_start_date"></input></td>
            <td><input id="new_end_date"></input></td>
            <td class="center_text">
                <span class="save new glyphicon glyphicon-ok"></span>
            </td>
        </tr>
    </tbody>
</table>



<br><br>
<fieldset>


</fieldset>



<% if Actions %>
        <div class="Actions">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
<% end_if %>
<% if IncludeFormTag %>
    </form>
<% end_if %>