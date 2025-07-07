<div class="loggedInBox">
    <div class="row">

        <div class="col-md-6" style="height: 35px;line-height: 35px;"><span style="display:inline-block; vertical-align:middle;">You are logged in as: <strong>$CurrentMember.Name</strong></span></div>
        <div class="col-md-6  text-right">
            <% if $CurrentMember.isFoundationMember || $CurrentMember.isCommunityMember %>
                <a class="roundedButton" href="{$RenewLink}">Renew Membership</a>
            <% end_if %>
            <% if $CurrentMember.isIndividualMember %>
                <a class="roundedButton downgrade-2-community-member" href="$Top.Link(downgrade2communitymember)">Change to Community Member</a>
            <% end_if %>
            <a class="roundedButton" href="{$ResignLink}">Resign Membership</a>
        </div>
    </div>
    <div class="row">
        <% if $CurrentMember.isFoundationMember %>
            <div class="col-md-12" style="height: 35px;line-height: 35px;">
                <span style="display:inline-block; vertical-align:middle;">Current Member Level: <strong>Foundation Member</strong>&nbsp;<a href="{$RenewLink}">(Renew your Membership)</a></span>
            </div>
        <% else_if $CurrentMember.isSpeaker %>
            <div class="col-md-12" style="height: 35px;line-height: 35px;">
                <span style="display:inline-block; vertical-align:middle;">Current Member Level: <strong>Speaker</strong></span>
            </div>
        <% else_if $CurrentMember.isCommunityMember %>
            <div class="col-md-12" style="height: 35px;line-height: 35px;">
                <span style="display:inline-block; vertical-align:middle;">Current Member Level: <strong>Community Member</strong>&nbsp;<a href="{$RenewLink}">(Renew your Membership)</a></span>
            </div>
         <% else_if $CurrentMember.isIndividualMember %>
            <div class="col-md-12" style="height: 35px;line-height: 35px;">
                 <span style="display:inline-block; vertical-align:middle;">Current Member Level: <strong>OIF Individual Member</strong></span>
            </div>
        <% end_if %>
        </div>
</div>