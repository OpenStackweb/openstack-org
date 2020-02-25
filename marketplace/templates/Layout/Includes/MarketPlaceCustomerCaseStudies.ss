<% if CustomerCaseStudies %>
    <hr>
    <h4 style="color: #{$Company.CompanyColor} !important;">Customer Case Studies</h4>
    <div class="pullquote info-area">
        <ul>
        <% loop CustomerCaseStudies %>
            <li><a href="$Uri">$Name</a></li>
        <% end_loop %>
        </ul>
    </div>
<% end_if %>
