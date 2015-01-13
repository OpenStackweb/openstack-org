<% with DupeAccount %>
    <p>
        Your About to delete your Account <b>$getEmail</b>. This is not a reversible action.
    </p>
    <% if isGerritUser %>
        <p>
            Also we detected that this duplicate account has set a valid <b>Gerrit user ID</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
        </p>
    <% else_if isCanditate %>
        <p>
            Also we detected that this duplicate account has set a valid <b>OpenStack Candidate</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
        </p>
    <% else_if isSpeaker %>
        <p>
            Also we detected that this duplicate account has set a valid <b>OpenStack Speaker</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else_if isMarketPlaceAdmin %>
        <p>
            Also we detected that this duplicate account has set a valid <b>MarketPlace Admin</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else_if isCompanyAdmin %>
        <p>
            Also we detected that this duplicate account has set a valid <b>Company Admin</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else_if isTrainingAdmin %>
        <p>
            Also we detected that this duplicate account has set a valid <b>Training Admin</b>. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else_if hasDeploymentSurveys %>
        <p>
            Also we detected that this duplicate account has some <b>Deployment Surveys</b> Filled Out. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else_if hasAppDevSurveys %>
        <p>
            Also we detected that this duplicate account has some <b>Application Development Surveys</b> Filled Out. Our recommendation is to merge with your current account (<% with Top.CurrentAccount %> <b>$getEmail</b> <% end_with %>).
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-merge"  data-token="$Top.ConfirmationToken" href="#">No, I want to merge it.</a>
            <a class="roundedButton" id="btn-keep"   data-token="$Top.ConfirmationToken" href="#">No, I want to keep it.</a>
    <% else %>
        <p>
            Are you sure?
        </p>
        <p>
            <a class="roundedButton" id="btn-delete" data-token="$Top.ConfirmationToken" href="#">Yes, I want to delete it.</a>
            <a class="roundedButton" id="btn-keep"  data-token="$Top.ConfirmationToken"  href="#">No, I want to keep it.</a>
        </p>
    <% end_if %>
<% end_with %>