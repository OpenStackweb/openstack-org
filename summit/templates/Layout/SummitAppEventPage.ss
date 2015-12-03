<div class="title_box">
    <div class="container">
        <div class="title">$Event.Title</div>
        <div class="actions">
            <event-favourite></event-favourite>
            <event-check></event-check>
        </div>
        <div class="subtitle">
            <% loop $Event.Tags %>
                <% if First %><a href="$Top.Link(global-search)?t={$Tag}">$Tag</a><% else_if Last %>
                &nbsp;&&nbsp;<a href="$Top.Link(global-search)?t={$Tag}">$Tag</a>
                <% else %>
                &nbsp;,&nbsp;<a href="$Top.Link(global-search)?t={$Tag}">$Tag</a>
                <% end_if %>
            <% end_loop %>
        </div>
    </div>
</div>
<div class="section1">
    <div class="container">
        <div class="description col1">
            $Event.Description()

            <div class="share">
                <script type="application/javascript">
                        var share_info =
                        {
                            url: "{$AbsoluteLink}event/{$Event.ID}",
                            title : "{$Event.Title.JS}",
                            description: "{$Event.ShortDescription().JS}",
                            image: "",
                            fb_app_id : "227356147446887",
                        };
                </script>
                <share-buttons share_info="{ share_info }"></share-buttons>
            </div>
        </div>
        <div class="info col2">
            <div class="info_item">
                <div class="info_item_icon"><i class="fa fa-2x fa-clock-o icon-clock"></i></div>
                <div class="info_item_text">$Event.DateNice()</div>
            </div>
            <div class="info_item">
                <div class="info_item_icon"><i class="fa fa-2x fa-map-marker icon-map"></i></div>
                <div class="info_item_text">$Event.LocationNameNice()</div>
            </div>

            <% if Event.isAllowedSummitType("DESIGN") == 1 %>
            <div class="info_item">
                <div class="info_item_icon"><img style="height:15px" src="/summit/images/summitapp/credential.png" /></div>
                <div class="info_item_text">Design Summit Credential</div>
            </div>
            <% end_if %>
            <div class="logo">
                <% loop Event.Sponsors %>
                    <% if TotalItems = 1 %>
                        $LargeLogoPreview()
                    <% else %>
                        $SidebarLogoPreview(100)
                    <% end_if %>
                <% end_loop %>
            </div>
        </div>
    </div>
</div>
<%--
<div class="container section2">
</div>
--%>

<% if Event.getSpeakers().toArray() %>
    <div class="speaker_box">
        <div class="container">
            <% loop Event.getSpeakers() %>
            <div class="row speaker_profile">
                <div class="speaker_pic img-circle"> <img src="$ProfilePhoto(100)" width="100" class="img-circle" /> </div>
                <div class="speaker_info">
                    <div class="speaker_name"> $FirstName $LastName </div>
                    <div class="speaker_job_title"> $Member.getCurrentPosition()</div>
                    <div class="speaker_bio"> $getShortBio(400) <a href="{$Top.AbsoluteLink}speakers/{$ID}"> FULL PROFILE</a></div>
                </div>
            </div>
            <% end_loop %>
        </div>
    </div>
<% end_if %>

<div class="container">
    <div class="col1 comment_section">
        <div class="comment_title"> Comment </div>

        <script type="application/javascript">
                var comments = [];

                <% loop $Event.getFeedback() %>
                    comments.push(
                    {
                        profile_pic : "{$Owner.ProfilePhotoUrl(50).JS}",
                        full_name : "{$Owner.getFullName.JS}",
                        date : "{$getDateNice.JS}",
                        note : "{$getNote.JS}",
                    });
                <% end_loop %>
        </script>

        <event-comments comments="{ comments }" limit="5"></event-comments>
    </div>
</div>
<div id="fb-root"></div>

<script src="summit/javascript/schedule/event-detail.bundle.js" type="application/javascript"></script>
<script src="summit/javascript/schedule/share-buttons.bundle.js" type="application/javascript"></script>
