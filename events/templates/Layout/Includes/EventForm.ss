<% if IncludeFormTag %>
    <form $FormAttributes role="form">
<% end_if %>
<% if Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>

    <fieldset>
        <% if Legend %>
            <legend>$Legend</legend>
        <% end_if %>

        <div class="section_container">
            <h2>Event Information</h2>

            <div class="field text " id="title">
                <label for="$FormName_title" class="left">Title</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(title)
                </div>
            </div>
            <div class="field text " id="url">
                <label for="$FormName_url" class="left">Url</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(url)
                </div>
            </div>
            <div class="field text " id="logo_url">
                <label for="$FormName_logo_url" class="left">Logo Url</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(logo_url)
                </div>
            </div>
            <div class="field text " id="category">
                <label for="$FormName_event_category" class="left">Category</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(event_category)
                </div>
            </div>
        </div>

        <div class="form-group">
            <h2>Event Location</h2>

            <div class="field text " id="location">
                <label for="$FormName_location" class="left">Location</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(location)
                </div>
            </div>

            <div class="field text " id="continent">
                <label for="$FormName_continent" class="left">Continent</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(continent)
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h2>Event Duration</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2" id="start_date">
                <label for="$FormName_start_date" class="left">Start Date</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(start_date)
                </div>
            </div>
            <div class="col-md-2" id="end_date">
                <label for="$FormName_end_date" class="left">End Date</label>

                <div class="middleColumn">
                    $Fields.dataFieldByName(end_date)
                </div>
            </div>
        </div>

        <div style="position: absolute; left: -9999px;">
            <label for="$FormName_username">Don't enter anything here</label>
            $Fields.dataFieldByName(user_name)
        </div>
        $Fields.dataFieldByName(SecurityID)
        <div class="clear"><!-- --></div>
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