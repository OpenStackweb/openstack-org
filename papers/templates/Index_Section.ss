<div class="row section" id="{$Slug}">
    <div class="col-lg-12">
        <h3 class="text-center title">{$_T($Paper.I18nContext, $Title)}</h3>
        <div id="accordion" class="panel-group">
            <% loop Items %>
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#{$Slug}" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion">$_T($Top.Paper.I18nContext, $Title)</a>
                    </h4>
                </div>
                <div id="{$Slug}" class="panel-collapse collapse in">
                    <div class="panel-body">
                        $_T($Top.Paper.I18nContext, $Content)
                    </div>
                </div>
            </div>
            <% end_loop %>
        </div>
    </div>
</div>