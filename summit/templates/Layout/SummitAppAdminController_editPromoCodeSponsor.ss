<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=8 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-attendee-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/promocodes/">Promo Codes</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/promocodes/sponsors">Sponsors</a></li>
            <li class="active"><% if $Sponsor.Name %> $Sponsor.Name <% else %> new <% end_if %></li>
        </ol>

        <form id="edit-promocode-sponsor-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="sponsor_id" value="$Sponsor.ID" />
            <input type="hidden" id="code_type" name="code_type" value="SPONSOR">

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="company_id">Sponsor</label><br>
                        <input id="company_id" name="company_id" class="form-control" <% if $Sponsor.Exists %> disabled <% end_if %> />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="code">Add Codes</label><br>
                        <input id="code" name="code" class="form-control"></input>
                    </div>
                </div>
            </div>
            <% if $PromoCodes.Count %>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="codes">Codes</label><br>
                        <% loop $PromoCodes %>
                            <a href="{$Top.Link}/{$Top.Summit.ID}/promocodes/$Code">
                                $Code <% if $OwnerID %>- $Owner.FirstName $Owner.Surname <% else_if $Email %>- $Email <% end_if %>
                            </a><br>
                        <% end_loop %>
                    </div>
                </div>
            </div>
            <% end_if %>
            <hr>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

    <script type="text/javascript">
        var this_url = "{$Link}/{$Summit.ID}/promocodes/sponsors/";
        var sponsor = {};
        <% if $Sponsor %>
            sponsor = {id : "{$Sponsor.ID}", name : "{$Sponsor.Name.JS}"};
        <% end_if %>
    </script>

</div>