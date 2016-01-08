<div id="content" class="typography">
    <form id="search_form" action="/community/speakers/results" method="get" enctype="application/x-www-form-urlencoded">
        <fieldset class="search_box">
            <label class="left" for="search_form_input">Search Speaker <span>Search box will auto-populate when you start typing</span></label>

            <div class="search_input">
                <input id="search_form_input" class="text form-control acInput" name="search_query"
                       placeholder="first name, last name, expertise, or company" value="{$getSearchQuery('search_query')}"/>
            </div>

            <label class="left" for="filters" style="margin: 20px 0 10px 0;">Or Filter</label>
            (search results will match at least one of the options selected for every filter)

            <div class="filters">
                Spoken Language:
                <select id="spoken_language" name="spoken_language[]" multiple="multiple">
                    <% loop AvailableLanguages %>
                        <option value="$Language" $Top.optionSelected('spoken_language',$Language) >$Language</option>
                    <% end_loop %>
                </select>

                Country of Origin:
                <select id="country_origin" name="country_origin[]" multiple="multiple">
                    <% loop AvailableCountries %>
                        <option value="$Country" $Top.optionSelected('country_origin',$Country) >$Country</option>
                    <% end_loop %>
                </select>

                Travel Preference:
                <select id="travel_preference" name="travel_preference[]" multiple="multiple">
                    <% loop AvailableTravelCountries %>
                        <option value="$Country" $Top.optionSelected('travel_preference',$Country) >$Country</option>
                    <% end_loop %>
                </select>
            </div>

            <input type="submit" class="btn btn-default" value="Go"/>
        </fieldset>
    </form>

    <h2 class="result_title">
        Results
        <a class="back" href="{$BaseHref}community/speakers">Back to Speakers Bureau</a>
    </h2>

    <% if Results.Count %>
        <ul id="results">
            <% loop Results %>
                <% if AvailableForBureau = 1 %>
                    <li>
                        <p><a href="{$Top.Link}profile/{$ID}">$FirstName $LastName</a></p>
                    </li>
                <% end_if %>
            <% end_loop %>
        </ul>
    <% else %>
        <p>Sorry, your search query did not return any results</p>
    <% end_if %>

</div>