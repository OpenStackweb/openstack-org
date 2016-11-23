<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='promocodes_sponsors' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="attendees-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/promocodes">Promo Codes</a></li>
            <li class="active">Sponsors</li>
        </ol>

        <script type="application/javascript">
            var summit_id = $Summit.ID;
            var promo_codes = [];

            <% loop $PromoCodes %>
                promo_codes.push(
                    {
                        id: {$ID},
                        codes : "{$Codes}",
                        sponsor: "{$Name}",
                    });
            <% end_loop %>

            var total_promocodes = {$PromoCodes.count};
            var page_data = {page: 1, limit: 20, total_items: total_promocodes}
        </script>
        <div class="row" style="padding-bottom: 5px;">
            <div class="col-md-2">
                <a href="{$Top.Link}/{$Summit.ID}/promocodes/sponsors/new" title="add new promo code" type="button" id="add-promocode" class="btn btn-success" >Add Sponsor</a>
            </div>
            <div class="col-md-10">&nbsp;</div>
        </div>
        <promocode-sponsor-list edit_link="{$Top.Link}/{$Summit.ID}/promocodes/sponsors" promo_codes="{ promo_codes }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></promocode-sponsor-list>
    </div>
</div>

$ModuleJS('promocode-admin-view')
