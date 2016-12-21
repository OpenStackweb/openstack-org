<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='speakers_merge' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li>Speakers</li>
            <li class="active">Merge</li>
        </ol>

        <input type="hidden" id="summit_id" value="$Summit.ID" />

        <div class="row">
            <div class="col-md-6">
                <div class="input-group search_box" style="width: 100%;">
                    <label for="speaker-search-1">Select Speaker 1</label>
                    <input id="speaker-search-1" class="form-control" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group search_box" style="width: 100%;">
                    <label for="speaker-search-2">Select Speaker 2</label>
                    <input id="speaker-search-2" class="form-control" />
                </div>
            </div>
            <br><br><hr>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" id="speaker-id-1" />
                    </div>
                    <div class="col-md-6">
                        <input type="hidden" id="speaker-id-2" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="title-1">Title</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="title-1" data-field="Title" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="title-2" data-field="Title" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="first_name-1">First Name</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="first_name-1" data-field="FirstName" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="first_name-2" data-field="FirstName" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="last_name-1">Last Name</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="last_name-1" data-field="LastName" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="last_name-2" data-field="LastName" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="email-1">Reg. Email</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="email-1" data-field="Email" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="email-2" data-field="Email" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="member-1">Member</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="member-1" data-field="MemberID" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="member-2" data-field="MemberID" data-speaker="2" ></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="presentations-1">Presentations</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="presentations-1" data-field="Presentations" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="presentations-2" data-field="Presentations" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="twitter-1">Twitter</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="twitter-1" data-field="TwitterName" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="twitter-2" data-field="TwitterName" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="irc-1">IRC</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <input id="irc-1" data-field="IRCHandle" data-speaker="1" disabled />
                    </div>
                    <div class="col-md-6 selectable">
                        <input id="irc-2" data-field="IRCHandle" data-speaker="2" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="bio-1">Bio</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <textarea id="bio-1" data-field="Bio" data-speaker="1" disabled></textarea>
                    </div>
                    <div class="col-md-6 selectable">
                        <textarea id="bio-2" data-field="Bio" data-speaker="2" disabled></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="picture-1">Picture</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="picture-1" data-field="PhotoID" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="picture-2" data-field="PhotoID" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="expertise-1">Expertise</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="expertise-1" data-field="AreasOfExpertise" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="expertise-2" data-field="AreasOfExpertise" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="otherpres-1">Other Pres.</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="otherpres-1" data-field="OtherPresentationLinks" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="otherpres-2" data-field="OtherPresentationLinks" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="travel-1">Travel Pref.</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="travel-1" data-field="TravelPreferences" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="travel-2" data-field="TravelPreferences" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="languages-1">Languages</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="languages-1" data-field="Languages" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="languages-2" data-field="Languages" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="promocode-1">PromoCode</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="promocode-1" data-field="PromoCode" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="promocode-2" data-field="PromoCode" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="assistance-1">Assistance</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="assistance-1" data-field="SummitAssistances" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="assistance-2" data-field="SummitAssistances" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="roles-1">Org. Roles</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="roles-1" data-field="OrganizationalRoles" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="roles-2" data-field="OrganizationalRoles" data-speaker="2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="involvements-1">Involvements</label>
                    </div>
                    <div class="col-md-5 selectable">
                        <div id="involvements-1" data-field="ActiveInvolvements" data-speaker="1"></div>
                    </div>
                    <div class="col-md-6 selectable">
                        <div id="involvements-2" data-field="ActiveInvolvements" data-speaker="2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row merge_div">
            <div class="col-md-12">
                <button id="merge_button" class="btn btn-primary"> Merge </button>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<script type="application/javascript">
    var admin_link = "$Top.Link";
</script>





