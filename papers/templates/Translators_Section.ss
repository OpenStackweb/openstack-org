<div class="row section" id="translators">
    <div class="col-lg-12">
        <h4 class="subtitle">{$_T("papers_ui", "Translators")}</h4>
        <ul>
            <% loop $Translators %>
                <li><p>$DisplayName</p></li>
            <% end_loop %>
        </ul>
    </div>
</div>
