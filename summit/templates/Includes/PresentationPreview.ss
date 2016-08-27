
<% with $Presentation %>

    <div class="main-panel-section confirm-block">
        <label>Title</label>
        <div class="confirm-item">$Title</div>
        <label>Abstract</label>
        <div>$ShortDescription</div>
    </div>
    <div class="main-panel-section confirm-block">
        <label>Level</label>
        <div class="confirm-item">$Level</div>
        <label>Summit Category</label>
        <div class="confirm-item">$Category.getCategoryGroups().First().Name</div>
        <label>Track</label>
        <div class="confirm-item">$Category.Title</div>
    </div>

    <% if $Moderator %>
        <div class="main-panel-section confirm-block">
            <label>Moderator</label>
            <% with $Moderator %>
            <div class="row">
                <div class="col-lg-2">
                    <p class="user-img" style="background-image: url($ProfilePhoto);"></p>
                </div>
                <div class="col-lg-10">
                    <label>Speaker</label>
                    <div class="confirm-item">$FirstName $LastName <br> $Title</div>
                    <label>Bio</label>
                    <div class="confirm-item">$Bio</div>
                </div>
            </div>
            <% end_with %>
        </div>
    <% end_if %>

    <% if $Speakers %>
        <div class="main-panel-section confirm-block">
            <label>Speakers</label>
            <% loop $Speakers %>
            <div class="row">
                <div class="col-lg-2">
                    <p class="user-img" style="background-image: url($ProfilePhoto);"></p>
                </div>
                <div class="col-lg-10">
                    <label>Speaker</label>
                    <div class="confirm-item">$FirstName $LastName <br> $Title</div>
                    <label>Bio</label>
                    <div class="confirm-item">$Bio</div>
                </div>
            </div>
            <% if $Last %><% else %><hr><% end_if %>
            <% end_loop %>
        </div>
    <% end_if %>
<% end_with %>
