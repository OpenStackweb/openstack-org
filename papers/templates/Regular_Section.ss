<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title)}</h3>
        <% if $Subtitle %>
            <h4 class="subtitle">{$_T($Paper.I18nContext, $Subtitle)}</h4>
        <% end_if %>
        <% loop $OrderedContents %>
            <% if $Type == P %>
                <p>$_T($Top.Paper.I18nContext, $Content)</p>
            <% end_if %>
            <% if $Type == IMG %>
                <p class="text-center">$_T($Top.Paper.I18nContext, $Content)</p>
            <% end_if %>
            <% if $Type == H4 %>
                <h4 class="subtitle">$_T($Top.Paper.I18nContext, $Content)</h4>
            <% end_if %>
            <% if $Type == H5 %>
                <h5 class="highlight">$_T($Top.Paper.I18nContext, $Content)</h5>
            <% end_if %>
            <% if $Type == LIST %>
                <% if $SubType == UL %>
                    <% include UnorderedList Paper=$Top.Paper,Items=$OrderedItems %>
                <% end_if %>
                <% if $SubType == OL %>
                    <% include OrderedList Paper=$Top.Paper,Items=$OrderedItems %>
                <% end_if %>
            <% end_if %>
        <% end_loop %>
    </div>
</div>
