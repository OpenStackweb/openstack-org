
<div class="container">
    <% with Speaker %>
    <div class="row speaker_profile col1">
        <div class="speaker_pic img-circle"> <img src="$ProfilePhoto(150)" width="150" class="img-circle" /> </div>
        <div class="speaker_info">
            <div class="speaker_name"> $FirstName $LastName </div>
            <div class="speaker_job_title"> $Member.getCurrentPosition()</div>
            <div class="speaker_bio"> $Bio</div>
        </div>
    </div>
    <div class="row sessions col1">
        <h2> Sessions </h2>
        <ul>
        <% loop PublishedPresentations($Top.Summit.ID) %>
            <li>
            <a href="{$Top.Link}events/{$ID}">$Title</a><br>
            $DateNice() <br>
            $LocationNameNice()
            </li>
        <% end_loop %>
        </ul>
    </div>

    <% end_with %>
</div>

