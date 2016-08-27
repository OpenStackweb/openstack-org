<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=6 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-attendee-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/speakers/">Speakers</a></li>
            <li class="active">$Speaker.ID</li>
        </ol>

        <form id="edit-speaker-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="speaker_id" value="$Speaker.ID" />

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 member_container">
                        <label for="member_id">Member</label><br>
                        <input id="member_id" name="member_id" />
                    </div>
                    <div class="col-md-4">
                        <label for="email">Email</label><br>
                        <input id="email" name="email" value="{$Speaker.getEmail()}"/>
                    </div>
                    <div class="col-md-4">
                        <label for="reg_code">Summit Registration Code</label><br>
                        <input id="reg_code" name="reg_code" value="{$Speaker.getSummitPromoCode($Top.Summit.ID).Code}"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label>Title</label>
                        <input id="title" name="title" value="$Speaker.Title" />
                    </div>
                    <div class="col-md-3">
                        <label>First Name</label>
                        <input id="first_name" name="first_name" value="$Speaker.FirstName" />
                    </div>
                    <div class="col-md-3">
                        <label>Last Name</label>
                        <input id="last_name" name="last_name" value="$Speaker.LastName" />
                    </div>
                    <div class="col-md-3">
                        <label>Summit On Site Phone</label>
                        <input id="onsite_phone" name="onsite_phone" value="$Speaker.getOnSitePhoneFor($Top.Summit.ID)" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Twitter Name</label>
                        <input id="twitter_name" name="twitter_name" value="$Speaker.TwitterName" />
                    </div>
                    <div class="col-md-6">
                        <label>IRC Name</label>
                        <input id="irc_name" name="irc_name" value="$Speaker.IRCHandle" />
                    </div>

                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Bio</label><br>
                        <textarea id="bio" name="bio" >
                            $Speaker.Bio
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>Profile Pic</label><br>
                        <img class="profile_pic" src="{$Speaker.ProfilePhoto}" />
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    Change… <input type="file" id="profile-pic" name="profile-pic">
                                </span>
                            </span>
                            <input id="image-filename" type="text" class="form-control" readonly="">
                            <input id="picture_id" name="picture_id" type="hidden" value="{$Speaker.PhotoID}">
                        </div>
                    </div>
                </div>
            </div>
            <% if $Speaker.AllPresentations($Top.Summit.ID) %>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Presentations</h2>
                     <ul>
                         <% loop $Speaker.AllPresentations($Top.Summit.ID) %>
                             <li><a href="{$Top.Link}/{$Top.Summit.ID}/events/{$ID}">$Title</a></li>
                         <% end_loop %>
                     </ul>
                    </div>
                </div>
            </div>
            <% end_if %>
            <hr>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

    <script type="text/javascript">
        var member = {};
        var registration_code = {};
       <% if $Speaker.Member.Exists %>
            member = {id : "{$Speaker.MemberID}", name : "{$Speaker.Member.FirstName.JS} {$Speaker.Member.Surname.JS} ({$Speaker.Member.Email})"};
       <% end_if %>
       <% if $Speaker.getSummitPromoCode($Top.Summit.ID) %>
       registration_code = { code : "$Speaker.getSummitPromoCode($Top.Summit.ID).Code", name: "{$Speaker.getSummitPromoCode($Top.Summit.ID).Code} ({$Speaker.getSummitPromoCode($Top.Summit.ID).Type})"};
       <% end_if %>
    </script>

</div>