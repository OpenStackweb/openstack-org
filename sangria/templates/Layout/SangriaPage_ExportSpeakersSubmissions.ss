<h2>Speakers Submissions</h2>

<div id="speakersExportAlert" class="alert alert-danger hidden" role="alert">
    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
    <span class="sr-only">Error:</span>
    Select at least one summit
</div>

<form id="speakersSubmissionsExport" method="POST" action="$Link(ExportSpeakersSubmissions)">
    <div>
        <label>Summits</label>
        <div class="row">
        <% loop $Summits %>
            <div class="col-md-3">
                <div class="checkbox">
                    <label>
                        <input name="summit[]" class="submit-filter" type="checkbox" value="$ID" <% if $Active == 1 %> checked <% end_if %>/>$Title
                    </label>
                </div>
            </div>
        <% end_loop %>
        </div>
    </div>
    <hr>
    <label>Status</label>
    <div class="row">
        <div class="col-md-3">
            <div class="checkbox">
                <label>
                    <input name="statusSubmitted" value="1" type="checkbox" <% if $statusSubmitted %> checked <% end_if %>/>Submitted, Not Accepted
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="checkbox">
                <label>
                    <input name="statusPrimary" value="1" type="checkbox" <% if $statusPrimary %> checked <% end_if %>/>Accepted Primary
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="checkbox">
                <label>
                    <input name="statusAlternate" value="1" type="checkbox" <% if $statusAlternate %> checked <% end_if %>/>Accepted Alternate
                </label>
            </div>
        </div>
    </div>
    <hr>
    <input id="ext" name="ext" type="hidden"/>
    <button data-ext="xls">Export Speakers Users (XLS)</button>
    <button data-ext="csv">Export Speakers Users (CSV)</button>

</form>