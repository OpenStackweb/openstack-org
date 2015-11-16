<div class="title_box">
    <div class="container">
        <div class="title">$Event.Title</div>
        <div class="actions">
            <event-favourite></event-favourite>
            <event-check></event-check>
        </div>
        <div class="subtitle">
            <% loop $Event.Tags %>
                <% if First %>
                    $Tag
                <% else_if Last %>
                    & $Tag
                <% else %>
                    , $Tag
                <% end_if %>
            <% end_loop %>
        </div>
    </div>
</div>
<div class="section1">
    <div class="container">
        <div class="short_description col1">
            $Event.ShortDescription()
        </div>
        <div class="info col2">
            <div class="info_item">
                <div class="info_item_icon"><img src="/summit/images/summitapp/Time.png" /></div>
                <div class="info_item_text">$Event.DateNice()</div>
            </div>
            <div class="info_item">
                <div class="info_item_icon"><img src="/summit/images/summitapp/map_pin.png" /></div>
                <div class="info_item_text">$Event.LocationNameNice()</div>
            </div>

            <% if Event.isAllowedSummitType("Design Summit + Main Conference") == 1 %>
            <div class="info_item">
                <div class="info_item_icon"><img style="height:15px" src="/summit/images/summitapp/credential.png" /></div>
                <div class="info_item_text">Design Summit Credential</div>
            </div>
            <% end_if %>

        </div>
        <div class="share">
            <share-event></share-event>
        </div>
    </div>
</div>
<div class="container section2">
    <div class="description col1">
        $Event.Description()
    </div>
    <div class="logo"></div>
</div>

<% if Event.getSpeakers().toArray() %>
    <div class="speaker_box">
        <div class="container">
            <% loop Event.getSpeakers() %>
            <div class="row speaker_profile col1">
                <div class="speaker_pic img-circle"> <img src="$ProfilePhoto(100)" width="100" class="img-circle" /> </div>
                <div class="speaker_info">
                    <div class="speaker_name"> $FirstName $LastName </div>
                    <div class="speaker_job_title"> $Member.getCurrentPosition()</div>
                    <div class="speaker_bio"> $getShortBio(200) <a href="">FULL BIO</a></div>
                </div>
            </div>
            <% end_loop %>
        </div>
    </div>
<% end_if %>

<div class="container">
    <div class="col1 comment_section">
        <div class="comment_title"> Comment </div>
        <% loop Event.getFeedback().Limit(5) %>
            <div class="comment <% if Last %>last<% end_if %>">
                <div class="comment_info">
                    <div class="comment_pic"> $Owner.ProfilePhoto(50) </div>
                    <div class="comment_name"> $Owner.getFullName() </div>
                    <div class="comment_date">
                        <b> Posted: </b>
                        <span> $getDateNice() </span>
                    </div>
                </div>
                <div class="comment_text"> $getNote() </div>
                <div class="comment_actions">
                    <div class=""></div>
                </div>
            </div>
            <% if Pos = 2 %> <a href="" > More Comments </a> <% end_if %>
        <% end_loop %>

    </div>
</div>
