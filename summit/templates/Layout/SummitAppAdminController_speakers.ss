<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=6 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="attendees-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Speakers</li>
        </ol>

        <script type="application/javascript">
            var speakers = [];
                <% loop $Summit.Speakers(0).Limit(20) %>
                speakers.push(
                        {
                            id: $ID,
                            member_id : {$Member.ID},
                            name: "$getName",
                            email : "{$getEmail}",
                            onsite_phone : "{$getOnSitePhoneFor($Top.Summit.ID)}",
                        });
                <% end_loop %>

            var total_speakers = {$Summit.Speakers(0).count};
            var page_data = {page: 1, limit: 20, total_items: total_speakers}
        </script>
        <speakers-list edit_link="{$Top.Link}/{$Summit.ID}/speakers" speakers="{ speakers }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></speakers-list>
    </div>
</div>

<script src="summit/javascript/schedule/admin/speakers-admin-view.bundle.js" type="application/javascript"></script>
