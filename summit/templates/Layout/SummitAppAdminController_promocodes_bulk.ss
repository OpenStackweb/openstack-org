<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=9 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-attendee-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/promocodes/">Promo Codes</a></li>
            <li class="active">Bulk</li>
        </ol>

        <form id="bulk-promocode-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="code_type">Code Type</label><br>
                        <select id="code_type" name="code_type" class="form-control">
                            <% loop CodeTypes %>
                                <option value="{$Type}"> $Type </option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="code_qty">Quantity</label><br>
                        <input id="code_qty" type="number" name="code_qty" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label for="code_prefix">Prefix</label><br>
                        <input id="code_prefix" name="code_prefix" class="form-control" />
                    </div>
                </div>
            </div>
            <hr>
            <label>Matching Codes:</label><br>
            <span id="set_qty">Please set the quantity.</span>
            <div class="row" id="matching_codes" style="display:none;"></div>
            <hr>
            <div class="form-group">
                <label>Assign: </label>
                <i> Fill this in if you want to assign codes. </i>
                <div class="row">
                    <div class="col-md-3">
                        <div class="radio">
                            <input type="radio" name="bulk_action" id="bulk_action_1" class="form-control"></input>
                            <label for="bulk_action_1">Use matching codes</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="radio">
                            <input type="radio" name="bulk_action" id="bulk_action_2" class="form-control"></input>
                            <label for="bulk_action_2">Create new codes</label>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <label for="members">Members</label><br>
                        <input id="members" name="members" class="form-control"></input>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script type="text/javascript">
        var this_url = "{$Link}/{$Summit.ID}/promocodes/bulk/";

    </script>

</div>