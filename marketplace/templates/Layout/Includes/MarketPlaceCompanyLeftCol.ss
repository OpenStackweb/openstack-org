$Company.SmallLogoPreview(150)
<h4 style="color: #{$Company.CompanyColor} !important;">About $Company.Name</h4>
<p>$Company.Overview</p>
<hr>
<div class="pullquote">
    <h4 style="color: #{$Company.CompanyColor} !important;">$Company.Name Commitment</h4>
    <div <% if Company.CommitmentAuthor %>class="commitment"<% end_if %>>$Company.Commitment</div>
    <% if Company.CommitmentAuthor %>
        <p class="author">&mdash;$Company.CommitmentAuthor, $Company.Name</p>
    <% end_if %>
</div>
