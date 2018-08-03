<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title, 1)}</h3>
        <% loop OrderedContents %>
            <% if $Type == P %>
                <p>$_T($Top.Paper.I18nContext, $Content, 1)</p>
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
                    <% include UnorderedList Paper=$Top.Paper,Items=$OrderedItems %>
                <% end_if %>
                <% if $SubType == OL %>
                    <% include OrderedList Paper=$Top.Paper,Items=$OrderedItems %>
                <% end_if %>
            <% end_if %>
        <% end_loop %>
        <h6>Select a Case Study</h6>
        <ul class="nav nav-tabs">
            <% loop OrderedCasesOfStudy %>
                <li class="<% if $First %>active<% end_if %> col-lg-3 col-sm-6 col-xs-12">
                    <a data-toggle="tab" href="#{$Slug}">
                        <img class="logo" src="{$LogoUrl}" alt="{$_T($Top.Paper.I18nContext, $Title, 1)}">
                    </a>
                </li>
            <% end_loop %>
        </ul>
        <div class="tab-content">
            <% loop OrderedCasesOfStudy %>
                <div id="$Slug" class="tab-pane fade<% if $First %> in active<% end_if %>">
                <h4 class="subtitle">$_T($Top.Paper.I18nContext, $Title, 1)</h4>
                    <% loop $OrderedContents %>
                        <% if $Type == P %>
                            <p>$_T($Top.Paper.I18nContext, $Content, 1)</p>
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
                                <% include UnorderedList Paper=$Top.Paper,Items=$OrderedItems %>
                            <% end_if %>
                            <% if $SubType == OL %>
                                <% include OrderedList Paper=$Top.Paper,Items=$OrderedItems %>
                            <% end_if %>
                        <% end_if %>
                    <% end_loop %>
            </div>
            <% end_loop %>
        </div>
        <div class="studies">
            <a class="btn btn-primary" href="#" id="case-studies-btn" data-section="{$Slug}" title="{$_T($Paper.I18nContext, "Go to top")}">
                <span>{$_T("papers_ui", "Select Another Case Study")}</span>
                <i class="fa fa-caret-up"></i>
            </a>
        </div>
    </div>
</div>