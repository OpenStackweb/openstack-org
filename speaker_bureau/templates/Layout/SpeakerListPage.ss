<% require themedCSS(filter) %>

<h1>Open Infrastructure Foundation: Speakers Bureau</h1>

<form id="search_form" action="/community/speakers/results" method="get" enctype="application/x-www-form-urlencoded">
    <fieldset class="search_box">
        <label class="left" for="search_form_input">Search Speaker <span>Search box will auto-populate when you start typing</span></label>

        <div class="search_input">
            <input id="search_form_input" class="text form-control acInput" name="search_query"
                   placeholder="first name, last name, expertise, or company"/>
        </div>

        <label class="left" for="filters" style="margin: 20px 0 10px 0;">Or Filter</label>
        (search results will match at least one of the options selected for every filter)

        <div class="filters row">
            <div class="col-md-3">
                Spoken Language:
                <select id="spoken_language" name="spoken_language[]" multiple="multiple">
                    <% loop AvailableLanguages %>
                        <option value="$Language">$Language</option>
                    <% end_loop %>
                </select>
            </div>
            <div class="col-md-3">
                Country of Origin:
                <select id="country_origin" name="country_origin[]" multiple="multiple">
                    <% loop AvailableCountries %>
                        <option value="$Country">$Country</option>
                    <% end_loop %>
                </select>
            </div>
            <div class="col-md-3">
                City:
                <input class="form-control input-sm" id="city" name="city">
            </div>
            <div class="col-md-3">
                Zip Code:
                <input class="form-control input-sm" id="zipcode" name="zipcode">
            </div>

        </div>

        <input type="submit" class="btn btn-default" value="Go"/>
    </fieldset>
</form>

<div class="linkLetters">
    <% loop LettersWithSpeakers %>
        <a href="{$Top.Link}?letter=$Letter">$Letter</a>
    <% end_loop %>
</div>


<% loop SpeakerList.GroupedBy(LastNameFirstLetter) %>
    <div class="filter">
        <h3 class="groupHeading" id="$LastNameFirstLetter">$LastNameFirstLetter</h3>
        <ul>
            <% loop Children %>
                <li><strong><a href="{$Top.Link}profile/{$ID}/{$NameSlug}">$FirstName $LastName</strong></a><% if CurrentOrgName %>
                    ($CurrentOrgName)<% end_if %></li>
            <% end_loop %>
        </ul>
    </div>
<% end_loop %>
