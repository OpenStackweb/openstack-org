<a href="{$Summit.RegistrationLink}/?aff={$Summit.Name}{$Position}" target="_blank" class="btn register-btn-lrg eventbrite-register-link {$ExtraClass}">
    <% if $RegisterLabel %>
        {$RegisterLabel}
    <% else %>
        Register Now
    <% end_if %>
    <i class="fa fa-arrow-right"></i>
</a>