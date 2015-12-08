<div id="content" class="typography">
	
	<h2 class="result_title">
	    $Title
	    <a class="back" href="{$BaseHref}community/speakers">Back to Speakers Bureau</a>
	</h2>

	<div class="search_criteria">
	    Spoken language: <b><% if $getSearchQuery('spoken_language') = '' %> Any <% else %> $getSearchQuery('spoken_language') <% end_if %> </b>-
	    Country of Origin: <b><% if $getSearchQuery('country_origin') = '' %> Any <% else %> $getSearchQuery('country_origin') <% end_if %> </b>-
	    Travel Preference: <b><% if $getSearchQuery('travel_preference') = '' %> Any <% else %> $getSearchQuery('travel_preference') <% end_if %> </b>
	    <% if $getSearchQuery('search_query') %>
	    - Search: <b>$getSearchQuery('search_query')</b>
	    <% end_if %>
	</div>
	
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

	<h2>Search again</h2>
	<form id="search_form" action="/community/speakers/results" method="get" enctype="application/x-www-form-urlencoded">
        <fieldset class="search_box">
            <label class="left" for="search_form_input">Search Speaker <span>Search box will auto-populate when you start typing</span></label>
            <div class="search_input">
                <input id="search_form_input" class="text form-control acInput" name="search_query" placeholder="first name, last name, expertise, or company" />
            </div>

            <input type="submit" class="btn btn-default" value="Go" />
        </fieldset>
    </form>


</div>