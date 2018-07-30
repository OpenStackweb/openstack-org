<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title, 1)}</h3>
        <% if $Contents %>
            <% loop $Contents %>
                <% if $Type == P %>
                    <p>$_T($Top.Paper.I18nContext, $Conten, 1t)</p>
                <% end_if %>
                <% if $Type == IMG %>
                    <p class="text-center">$_T($Top.Paper.I18nContext, $Content, 1)</p>
                <% end_if %>
                <% if $Type == H4 %>
                    <h4 class="subtitle">$_T($Top.Paper.I18nContext, $Content, 1)</h4>
                <% end_if %>
                <% if $Type == H5 %>
                    <h5 class="highlight">$_T($Top.Paper.I18nContext, $Content, 1)</h5>
                <% end_if %>
                <% if $Type == LIST %>
                    <% if $SubType == UL %>
                        <% include UnorderedList Paper=$Top.Paper,Items=$Items %>
                    <% end_if %>
                    <% if $SubType == OL %>
                        <% include OrderedList Paper=$Top.Paper,Items=$Items %>
                    <% end_if %>
                <% end_if %>
            <% end_loop %>
        <% end_if %>
        <div id="accordion" class="panel-group">
            <% loop Items %>
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#{$Slug}" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion">
                            $_T($Top.Paper.I18nContext, $Title, 1)
                        </a>
                    </h4>
                </div>
                <div id="{$Slug}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <% if $Link %>
                        <a class="index-link" title="visit it" href="$Link"><i class="fa fa-external-link"></i></a>
                        <% end_if %>
                        $_T($Top.Paper.I18nContext, $Content, 1)
                    </div>
                </div>
            </div>
            <% end_loop %>
        </div>
    </div>
</div>