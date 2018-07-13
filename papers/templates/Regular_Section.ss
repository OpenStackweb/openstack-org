<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title)}</h3>
        <% if $Subtitle %>
            <h4 class="subtitle">{$_T($Paper.I18nContext, $Subtitle)}</h4>
        <% end_if %>
        <% loop $OrderedContents %>
            $_T($Top.Paper.I18nContext, $Content)
        <% end_loop %>
    </div>
</div>
