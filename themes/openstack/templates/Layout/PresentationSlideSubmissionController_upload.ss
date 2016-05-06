<div class="container">
    <% require themedCSS(presentationupload) %>
    <% if $HasError %>
        <h2>Error</h2>
        <p>There seems to be an error uploading this presentation.</p>

    <% else %>    
        <% if $Presentation.MaterialType('PresentationSlide') %>
            <h2>Replace Presentation File For "$Presentation.Title"</h2>
            <% with $Presentation.MaterialType('PresentationSlide') %>
	            <% if $IsUpload %>
	                <p>The presentation <strong>{$Top.Presentation.Title}</strong> currently has the file <strong><a
	                        href="{$Slide.URL}">{$Slide.Name}</a></strong>
	                    uploaded. If you proceed, your new file or URL will replace the current one.</p>
	            <% else %>
	                <p>The presentation <strong>{$Top.Presentation.Title}</strong> currently has slides set to be available at <a
	                        href="{$Slide.Link}">{$Slide.Link}</a>. If you proceed, your
	                    new file or URL will replace the current one.</p>
	            <% end_if %>
            <% end_with %>

        <% else %>

            <h2>Upload Your Slides For "$Presentation.Title"</h2>
            <p>Please upload a file with your slides or provide a link to where your slides are hosted online. Thank you
                for the help!</p>
        <% end_if %>

        <h2 class="upload-tabs">
                <a href="$Link('upload')" class="active">Upload a file</a>
                <a href="$Link('linkto')">Link to an online presentation</a>
        </h2>

        $Form
    <% end_if %>

    <p></p>
    <hr/>
    <p>If you have any problems with this form, please contact <a href="mailto:speakersupport@openstack.org">speakersupport@openstack.org</a>
        and we'll work to help you out. Thanks so much for uploading your presentation.</p>
</div>