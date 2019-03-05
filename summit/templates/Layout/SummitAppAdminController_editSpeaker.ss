<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=6 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container" >
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
                        <input id="email" name="email" disabled class="form-control" value="{$Speaker.getEmail()}"/>
                    </div>
                    <div class="col-md-4">
                        <label for="reg_code">Summit Registration Code</label><br>
                        <input id="reg_code" name="reg_code" class="form-control" value="{$Speaker.getSummitPromoCode($Top.Summit.ID).Code}"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <input id="registered" name="registered" type="checkbox" <% if $Speaker.getAssistanceFor($Top.Summit.ID).RegisteredForSummit %> checked <% end_if %>>
                            <label for="registered">Registered</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <input id="checked_in" name="checked_in" type="checkbox" <% if $Speaker.getAssistanceFor($Top.Summit.ID).CheckedIn %> checked <% end_if %>>
                            <label for="checked_in">Checked-In</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <input id="confirmed" name="confirmed" type="checkbox" <% if $Speaker.getAssistanceFor($Top.Summit.ID).IsConfirmed %> checked <% end_if %>>
                            <label for="confirmed">Confirmed</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Title</label>
                        <input id="title" name="title" class="form-control" value="$Speaker.Title" />
                    </div>
                    <div class="col-md-4">
                        <label>First Name</label>
                        <input id="first_name" name="first_name" class="form-control" value="$Speaker.FirstName" />
                    </div>
                    <div class="col-md-4">
                        <label>Last Name</label>
                        <input id="last_name" name="last_name" class="form-control" value="$Speaker.LastName" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label>Summit On Site Phone</label>
                        <input id="onsite_phone" name="onsite_phone" class="form-control" value="$Speaker.getOnSitePhoneFor($Top.Summit.ID)" />
                    </div>
                    <div class="col-md-4">
                        <label>Twitter Name</label>
                        <input id="twitter_name" name="twitter_name" class="form-control" value="$Speaker.TwitterName" />
                    </div>
                    <div class="col-md-4">
                        <label>IRC Name</label>
                        <input id="irc_name" name="irc_name" class="form-control" value="$Speaker.IRCHandle" />
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
                             <li>
                                <a href="{$Top.Link}/{$Top.Summit.ID}/events/{$ID}">$Title</a>
                                 - <% if isModeratorByID($Top.Speaker.ID) %> Moderator <% else %> Speaker <% end_if %>
                                 - $getStatusNice()
                            </li>
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