<div id="content" class="typography">
	
	<h2>
	    $Title
	    <a class="back" href="{$BaseHref}community/speakers">Back to Speakers Bureau</a>
	</h2>
	
	<% if SearchQuery %>
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
	<% end_if %>	

	<h2>Search again</h2>
	<form id="search_form" action="/community/speakers/results" method="get" enctype="application/x-www-form-urlencoded">
        <fieldset>
            <label class="left" for="search_form_input">Search Speaker</label>
            <div class="middleColumn">
                <input id="search_form_input" class="text form-control acInput" name="search_query" placeholder="first name, last name, country, expertise" />
            </div>

            <input type="submit" class="action" value="Go" />
        </fieldset>
    </form>


</div>