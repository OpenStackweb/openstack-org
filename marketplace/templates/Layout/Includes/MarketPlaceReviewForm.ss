<% if IncludeFormTag %>
    <form $FormAttributes>
<% end_if %>
<% if Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>

    <fieldset>
        <p> Write a review of this product to share your opinions with others. </p>
        <% if Legend %>
            <legend>$Legend</legend>
        <% end_if %>

        <div class="field text" id="rating">
            <label for="$FormName_rating" class="left">Rating</label>

            <div class="middleColumn">
                $Fields.dataFieldByName(rating)
            </div>
            <p> Select 1 to 5 stars to rate this product </p>
        </div>
        <div class="field text" id="title">
            <label for="$FormName_title" class="left">Title</label>

            <div class="middleColumn">
                $Fields.dataFieldByName(title)
            </div>
        </div>
        <div class="field text" id="comment">
            <label for="$FormName_comment" class="left">Comment</label>

            <div class="middleColumn">
                $Fields.dataFieldByName(comment)
            </div>
            <p> Char limit 2000 </p>
        </div>
        $Fields.dataFieldByName(product)
        $Fields.dataFieldByName(logged_in)
        <div class="honey field text" id="field_98438688">
            <label for="$FormName_field_98438688">Don't enter anything here</label>
            $Fields.dataFieldByName(field_98438688)
        </div>
        $Fields.dataFieldByName(SecurityID)
        <div class="clear"><!-- --></div>

        <% if Actions %>
            <div class="Actions">
                <% loop Actions %>
                    $Field
                <% end_loop %>
            </div>
        <% end_if %>
    </fieldset>

<% if IncludeFormTag %>
    </form>
<% end_if %>

