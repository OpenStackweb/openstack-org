
<% require themedCSS(filter) %>

<h1>OpenStack Foundation: Speakers Bureau</h1>

<form id="search_form" action="/community/speakers/results" method="get" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <label class="left" for="search_form_input">Search Speaker</label>
        <div class="middleColumn">
            <input id="search_form_input" class="text form-control acInput" name="search_query" placeholder="first name, last name, country, expertise or company, language spoken" />
        </div>

        <input type="submit" class="action" value="Go" />
    </fieldset>
</form>


<p class="linkLetters">
    <% loop LettersWithSpeakers %>
        <a href="{$Top.Link}?letter=$Letter">$Letter</a>
    <% end_loop %>
</p>

<% loop SpeakerList.GroupedBy(LastNameFirstLetter) %>
	<div class="filter">
    <h3 class="groupHeading" id="$LastNameFirstLetter">$LastNameFirstLetter</h3>
    <ul>
        <% loop Children %>
            <li><strong><a href="{$Top.Link}profile/{$ID}">$FirstName $LastName</strong></a><% if CurrentOrgName %> ($CurrentOrgName)<% end_if %></li>
        <% end_loop %>
    </ul>
	</div>
<% end_loop %>
