<% if $Top.isMultiRegister %>
    <div class="dropdown register-dropdown">
        <button class="btn btn-secondary dropdown-toggle register-btn-lrg eventbrite-register-link {$ExtraClass}" type="button" id="dropdownRegister" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Register Now <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a role="dropdown-item" href="{$Summit.RegistrationLink}/?aff={$Summit.Name}{$Position}" target="_blank">
                <% if $RegisterLabel %> {$RegisterLabel} <% else %> {$Top.RegisterButtonLabel} <% end_if %>
            </a>
            <a role="dropdown-item" href="{$Summit.SecondaryRegistrationLink}/?aff={$Summit.Name}{$Position}" target="_blank">
                {$Summit.SecondaryRegistrationBtnText}
            </a>
        </div>
    </div>
<% else %>
    <a href="{$Summit.RegistrationLink}/?aff={$Summit.Name}{$Position}" target="_blank" class="btn register-btn-lrg eventbrite-register-link {$ExtraClass}">
        <% if $RegisterLabel %> {$RegisterLabel} <% else %> {$Top.RegisterButtonLabel} <% end_if %>
        <i class="fa fa-arrow-right"></i>
    </a>
<% end_if %>

