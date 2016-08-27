<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=7 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="attendees-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Promo Codes</li>
        </ol>

        <script type="application/javascript">
            var summit_id = $Summit.ID;
            var promo_codes = [];

            <% loop $Summit.SummitRegistrationPromoCodes().Limit(20) %>
                promo_codes.push(
                    {
                        id: {$ID},
                        code : "{$Code}",
                        <% if $ClassName == 'SpeakerSummitRegistrationPromoCode' %>
                        owner: "{$Speaker().FirstName} {$Speaker().LastName}",
                        owner_email: "{$Speaker().Member().Email}",
                        <% else_if $Owner().Exists() %>
                        owner: "{$Owner().FirstName} {$Owner().Surname}",
                        owner_email: "{$Owner().Email}",
                        <% else_if $ClassName != 'SummitRegistrationPromoCode' %>
                        owner: "{$FirstName} {$LastName}",
                        owner_email: "{$Email}",
                        <% end_if %>
                        email_sent: {$EmailSent},
                        redeemed : {$Redeemed},
                        source : <% if $Creator().Exists %> "{$Creator().FullName.JS}" <% else %> "{$Source}" <% end_if %>,
                        type: "{$Type}",
                    });
            <% end_loop %>

            var promocode_types = [];
            <% loop $CodeTypes %>
                promocode_types.push('{$Type}');
            <% end_loop %>

            var total_promocodes = {$Summit.SummitRegistrationPromoCodes().count};
            var page_data = {page: 1, limit: 20, total_items: total_promocodes}
        </script>
        <div class="row" style="padding-bottom: 5px;">
            <div class="col-md-2">
                <a href="{$Top.Link}/{$Summit.ID}/promocodes/new" title="add new promo code" type="button" id="add-promocode" class="btn btn-success" >Add Promo Codes</a>
            </div>
            <div class="col-md-10">&nbsp;</div>
        </div>
        <promocode-list edit_link="{$Top.Link}/{$Summit.ID}/promocodes" promocode_types="{ promocode_types }" promo_codes="{ promo_codes }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></promocode-list>
    </div>
</div>

<script src="summit/javascript/schedule/admin/promocode-admin-view.bundle.js" type="application/javascript"></script>
