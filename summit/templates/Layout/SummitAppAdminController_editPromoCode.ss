<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=7 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-attendee-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/promocodes/">Promo Codes</a></li>
            <li class="active"><% if $PromoCode.Code %> $PromoCode.Code <% else %> new <% end_if %></li>
        </ol>

        <form id="edit-promocode-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="promocode_id" value="$PromoCode.ID" />

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label for="code_type">Type</label><br>
                        <select id="code_type" name="code_type" class="form-control">
                            <% loop $PromoCodeTypes %>
                            <option value="$Type" <% if $Top.PromoCode.Type == $Type %> selected <% end_if %>>$Type</option>
                            <% end_loop %>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="code">Code</label><br>
                        <input id="code" name="code" class="form-control"></input>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group">
                <div class="row member_container">
                    <div class="col-md-6">
                        <label for="member_id">Member</label><br>
                        <input id="member_id" name="member_id" class="form-control" />
                    </div>
                </div>
                <div class="row speaker_container">
                    <div class="col-md-6">
                        <label for="speaker_id">Speaker</label><br>
                        <input id="speaker_id" name="speaker_id" class="form-control" />
                    </div>
                </div>
                <div class="row company_container">
                    <div class="or_div"> OR </div>
                    <div class="col-md-6">
                        <label for="company_id">Company</label><br>
                        <input id="company_id" name="company_id" class="form-control" />
                    </div>
                </div>
                <div class="row addressee_container">
                    <div class="or_div"> OR </div>
                    <div class="col-md-3">
                        <label for="first_name">First Name</label><br>
                        <input id="first_name" name="first_name" class="form-control" value="$PromoCode.FirstName"/>
                    </div>
                    <div class="col-md-3">
                        <label for="last_name">Last Name</label><br>
                        <input id="last_name" name="last_name" class="form-control" value="$PromoCode.LastName"/>
                    </div>
                    <div class="col-md-3">
                        <label for="email">Email</label><br>
                        <input id="email" name="email" class="form-control" value="$PromoCode.Email"/>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top:30px">
                <div class="row">
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" value="1" id="email_sent" name="email_sent" disabled <% if $PromoCode.EmailSent == 1 %> checked <% end_if %> />
                            <label for="email_sent">Been Emailed</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" value="1" id="redeemed" name="redeemed" <% if $PromoCode.Redeemed == 1 %> checked <% end_if %> />
                            <label for="redeemed">Redeemed</label>
                        </div>
                    </div>
                    <% if $PromoCode.EmailSent == 0 && $PromoCode.Exists %>
                    <div class="col-md-3">
                        <button type="button" id="send_email" class="btn btn-default">Send Email</button>
                    </div>
                    <% end_if %>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

    <script type="text/javascript">
        var this_url = "{$Link}/{$Summit.ID}/promocodes/";
        var owner = {};
        var speaker = {};
        var company = {};
        var code = "{$PromoCode.Code.JS}";

        <% if $PromoCode.Speaker %>
            speaker = {speaker_id : "{$PromoCode.Speaker.ID}", name : "{$PromoCode.Speaker.FirstName.JS} {$PromoCode.Speaker.LastName.JS} ({$PromoCode.Speaker.Member.Email})"};
        <% end_if %>
        <% if $PromoCode.Owner %>
            owner = {id : "{$PromoCode.Owner.ID}", name : "{$PromoCode.Owner.FirstName.JS} {$PromoCode.Owner.LastName.JS} ({$PromoCode.Owner.Email})"};
        <% end_if %>
        <% if $PromoCode.Sponsor %>
            company = {id : "{$PromoCode.Sponsor.ID}", name : "{$PromoCode.Sponsor.Name.JS}"};
        <% end_if %>
    </script>

</div>