<div>
    <h1>Add New Press Release</h1>
    <p></p>
</div>

<% if Saved %>
    <div class="siteMessage" id="SuccessMessage" style="padding: 10px;">
        <p style="float:left;">Your news article has been saved!</p>
        <input type="button" title="Add New Article" value="Add New Article" data-url="/news-add/" name="add-new-article" id="add-new-article" class="action">
    </div>
<% else %>
<div>
    $NewsRequestForm
</div>
<% end_if %>