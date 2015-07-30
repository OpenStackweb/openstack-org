<div class="link_button">
    <a href="/news" id="back_to_news">Back to News</a>
</div>
<% if CurrentMember.isNewsManager %>
    <div class="link_button">
        <a href="#" id="go_to_recent">Go to Recently Submitted</a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="newsSlider">
                            <h3>Banner (max 5)</h3>
                            <table>
                                <thead>
                                <tr>
                                    <th style="padding-left:5px;">Release</th>
                                    <th>Title</th>
                                    <th style="text-align:center;">Image</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="slider_sortable" class="connected">
                                    $getArticles(slider)
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="newsFeatured">
                            <h3>Featured</h3>
                            <table>
                                <thead>
                                <tr>
                                    <th style="padding-left:5px;">Release</th>
                                    <th>Title</th>
                                    <th style="text-align:center;">Image</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="featured_sortable" class="connected">
                                    $getArticles(featured)
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="newsRecent">
                            <h3>Recent News</h3>
                            <table>
                                <thead>
                                <tr>
                                    <th style="padding-left:5px;">Release</th>
                                    <th>Title</th>
                                    <th style="text-align:center;">Image</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="recent_sortable" class="connected">
                                    $getArticles(recent)
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="newsStandBy">
                    <h3>Recently Submitted</h3>
                    <table>
                        <thead>
                        <tr>
                            <th style="padding-left:5px;">Release</th>
                            <th>Title</th>
                            <th style="text-align:center;">Image</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="standby_sortable" class="connected">
                            $getArticles(standby)
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<% else %>
    <% if CurrentMember %>
        <p>In order to edit the news page, you need to be a news manager.</p>
    <% else %>
        <p>In order to edit your community profile, you will first need to <a href="/Security/login/?BackURL=%2Fprofile%2F">login as a member</a>.
        Don't have an account? <a href="/join/">Join The Foundation</a></p>
        <p><a class="roundedButton" href="/Security/login/?BackURL=%2Fnews-manage%2F">Login</a> <a href="/join/" class="roundedButton">Join The Foundation</a></p>
    <% end_if %>
<% end_if %>