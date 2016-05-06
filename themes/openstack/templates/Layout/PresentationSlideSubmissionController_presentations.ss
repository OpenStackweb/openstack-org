<div class="container">
    <% require themedCSS(presentationupload) %>

    <p class="message">Thanks for the help, $Speaker.name! Please click on a presentation below to get your slides
        uploaded.</p>

    <h1>Your Presentations</h1>

    <% loop $Presentations %>
        <% if $MaterialType('PresentationSlide') %>
            <div class="row">
                <div class="col-lg-9">
                    <h3>$Title</h3>
                    <p><strong>$SpeakersCSV</strong></p>
                    <div class="presentation">
                        <a href="{$Top.link}/presentation/{$ID}/upload" class="presentation-title">$Name</a>
                        <% with $MaterialType('PresentationSlide') %>
                            <% if $IsUpload %>
                                <br/>Currently attached file: <strong>{$Slide.Name}</strong>
                            <% else_if $IsLink %>
                                <br/>Current presentation link: <strong><a href="{$Link}">{$Link}</a></strong>
                            <% end_if %>
                        <% end_with %>
                    </div>
                </div>
                <div class="col-lg-3">
                    <a href="{$Top.link}/presentation/{$ID}/upload" class="roundedButton add-slides">Change...</a>
                </div>
            </div>

        <% else %>
            <div class="row">
                <div class="col-lg-9">
                    <h3>$Title</h3>
                    <p><strong>$SpeakersCSV</strong></p>
                    <div class="presentation no-media">
                        <a href="{$Top.link}/presentation/{$ID}/upload" class="presentation-title">$Name</a>
                        <br/>No slides have been provided yet. Please <a href="{$Top.link}/presentation/{$ID}/upload">upload</a> your
                        slides.</strong>
                    </div>
                </div>
                <div class="col-lg-3">
                    <a href="{$Top.link}/presentation/{$ID}/upload" class="roundedButton add-slides">Add Slides</a>
                </div>
            </div>

        <% end_if %>
    <% end_loop %>

    <p></p>
    <hr/>
    <p>If you have any problems with this form, please contact <a href="mailto:speakersupport@openstack.org">speakersupport@openstack.org</a>
        and we'll work to help you out. Thanks so much for uploading your presentation.</p>
</div>
