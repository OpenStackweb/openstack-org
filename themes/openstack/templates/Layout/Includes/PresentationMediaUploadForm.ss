<form $FormAttributes>

    <% if $Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
    <% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none;"></p>
    <% end_if %>

    <fieldset>

        $Fields.dataFieldByName('Slide').FieldHolder
        $Fields.dataFieldByName('SecurityID')

    </fieldset>

    <% if $Actions %>
        <div class="Actions">
            <% loop $Actions %>
                $Field
            <% end_loop %>
        </div>
    <% end_if %>

</form>

