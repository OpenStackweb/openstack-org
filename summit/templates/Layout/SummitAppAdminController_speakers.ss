<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='speakers' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Speakers</li>
        </ol>

        <script type="application/javascript">
            var summit_id = $Summit.ID;
            var speakers = [];
                <% loop $Summit.Speakers(0).Limit(20) %>
                speakers.push(
                        {
                            id: $ID,
                            member_id : {$Member.ID},
                            name: "$getName",
                            email : "{$getEmail}",
                            onsite_phone : "{$getOnSitePhoneFor($Top.Summit.ID)}",
                            presentation_count: "{$AllPresentations($Top.Summit.ID).Count()}",
                            registration_code: "{$getSummitPromoCode($Top.Summit.ID).Code}"
                        });
                <% end_loop %>

            var total_speakers = {$Summit.Speakers(0).count};
            var page_data = {page: 1, limit: 20, total_items: total_speakers}
        </script>
        <div class="row" style="padding-bottom: 5px;">
            <div class="col-md-2">
                <button title="add new speaker" type="button" id="add-speaker" class="btn btn-success" data-toggle="modal" data-target="#addSpeakerModal">Add Speaker</button>
            </div>
            <div class="col-md-10">&nbsp;</div>
        </div>
        <speakers-list edit_link="{$Top.Link}/{$Summit.ID}/speakers" speakers="{ speakers }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></speakers-list>
    </div>
</div>

<!-- Modal Add Speaker -->
<div class="modal fade bs-example-modal-lg" id="addSpeakerModal" tabindex="-1" role="dialog" aria-labelledby="addSpeakerModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addSpeakerModalLabel">Add Speaker</h4>
            </div>
            <div class="modal-body">
                <form id="add-speaker-form" name="add-speaker-form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 member_container">
                                <label>Email</label>
                                <input id="email" name="email" class="form-control" style="width: 98%"/>
                            </div>
                            <div class="col-md-6">
                                <label for="member">Member</label><br>
                                <input id="member_id" name="member_id" style="width: 98%"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Title</label>
                                <input id="title" name="title" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label>First Name</label>
                                <input id="first_name" name="first_name" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label>Last Name</label>
                                <input id="last_name" name="last_name" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label>Summit On Site Phone</label>
                                <input id="onsite_phone" name="onsite_phone" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Twitter Name</label>
                                <input id="twitter_name" name="twitter_name" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>IRC Name</label>
                                <input id="irc_name" name="irc_name" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="save-speaker" type="button" class="btn btn-primary">Add Speaker!</button>
            </div>
        </div>
    </div>
</div>

$ModuleJS('speakers-admin-view')
