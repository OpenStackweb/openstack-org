<div class="container">
<% require themedCSS(presentationupload) %>


<h1>Success!</h1>


<div class="success-box">
<% if $Material.IsUpload %>
<p>Your file <strong>{$Material.Slide.Filename}</strong> for the presentation <strong>{$Presentation.Title}</strong> was uploaded successfully!</p>
<% else %>
<p>The URL for the presentation <strong>{$Presentation.Title}</strong> was successfully set to <a href="$Material.Link">$Material.Link</a></p>
<% end_if %>
</div>

<a class="roundedButton" href="submit-slides/presentations/">Back To Your Presentations</a>
</div>