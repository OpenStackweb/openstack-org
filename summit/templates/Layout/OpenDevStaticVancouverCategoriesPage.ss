<div class="all-sessions-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>$HeaderTitle</h1>
            </div>
        </div>
        <% loop $Summit.CategoryGroups() %>
            <div class="row sessions-landing-intro">
                <div class="col-sm-12">
                    <h3>$Name</h3>
                    <p>$Description</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <% loop $Categories() %>
                        <% if $VotingVisible && $ChairVisible %>
                            <p>
                                <strong> $Title </strong><br>
                                $Description
                            </p>
                        <% end_if %>
                    <% end_loop %>
                </div>
            </div>
        <% end_loop %>
    </div>
</div>
