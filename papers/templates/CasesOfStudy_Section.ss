<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title)}</h3>
        <% loop OrderedContents %>
            $_T($Top.Paper.I18nContext, $Content)
        <% end_loop %>
        <h6>Select a Case Study</h6>
        <ul class="nav nav-tabs">
            <% loop CasesOfStudy %>
                <li class="<% if $First %>active<% end_if %> col-lg-3 col-sm-6 col-xs-12">
                    <a data-toggle="tab" href="#{$Slug}">
                        <img class="logo" src="{$LogoUrl}" alt="{$_T($Top.Paper.I18nContext, $Title)}">
                    </a>
                </li>
            <% end_loop %>
        </ul>
        <div class="tab-content">
            <% loop CasesOfStudy %>
                <div id="$Slug" class="tab-pane fade<% if $First %> in active<% end_if %>">
                <h4 class="subtitle">$_T($Top.Paper.I18nContext, $Title)</h4>
                <% loop OrderedContents %>
                    $_T($Top.Paper.I18nContext, $Content)
                <% end_loop %>
            </div>
            <% end_loop %>
        </div>
        <div class="studies">
            <a class="btn btn-primary" href="#" id="case-studies-btn" data-section="{$Slug}" title="{$_T($Paper.I18nContext, "Go to top")}">
                <span>{$_T($Paper.I18nContext, "Select Another Case Study")}</span>
                <i class="fa fa-caret-up"></i>
            </a>
        </div>
    </div>
</div>